<?php

    /**
	 * RescueMe Package command line interface
	 * 
	 * @copyright Copyright 2013 {@link http://www.discoos.org DISCO OS Foundation}  
	 *
     * @since 19. June 2013
	 * 
	 * @author Kenneth Gulbrandsøy <kenneth@onevoice.no>
	 */

    // Only run this when executed on the CLI
    if(php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR'])) {

        // Configure build
        require __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

        // Get options
        $opts = parse_opts($argv, array('h'));

        // Get action
        $action = $opts[ACTION];
        
        // Perform sanity checks on php host system
        $halt = false;
        $status = system_checks($action);
        if($status !== true) {
            foreach($status as $error) {
                list($code, $message) = $error;
                error(implode(PHP_EOL.'--> ',$message));
                if($code === E_USER_ERROR) {
                    $halt = $error;
                }
            }
        }

        // Failure?
        if($halt !== false) {
            echo PHP_EOL;
            exit($halt);
        }

        // Get error message?
        $msg = (empty($action) ? "Show help: -h | help ACTION" : null);        
        
        // Print help now?
        if(empty($action) && isset($opts['h'])) print_help();

        // Print help with error message?
        if(isset($msg))  print_help(HELP, "Show help: -h");
        
        info("rescueme build script", BUILD_SUCCESS); echo PHP_EOL;
        
        execute(array($action => $opts));
        
    }
        
    else {

        fatal("Run 'cli' on php-cli only!");

    }// else
        
        
    /**
     * Execute actions
     * 
     * @param array $actions
     */
    function execute($actions)
    {
        foreach($actions as $action => $opts) 
        {            
            // Print help now?
            if(isset($opts['h'])) {
                print_help($action);
            }
                    
            // Perform action
            switch($action)
            {
                case STATUS:

                    // Get default paths
                    $root = get_safe_dir($opts, INSTALL_DIR, "src");
                    
                    // Create Status
                    require('classes/RescueMe/Status.php');

                    $status = new RescueMe\Status($root);

                    // Status unsuccessful?
                    if(($config = $status->execute()) === false) {
                       done($action, BUILD_ERROR);
                    }// if                        
                    
                    break;
                    
                case IMPORT:
                    
                    // Skip?
                    if(in_phar()) print_help();
                    
                    // Get default path install path
                    $root = get_safe_dir($opts, IMPORT_DIR, "src");
                    
                    // Get configuration parameters
                    $config = file_exists(realpath($root)."/config.php") ? get_config_params($root) : array();

                    // Get database parameters
                    $opts = get_db_params($opts, $config);

                    // Create Import
                    require('classes/RescueMe/Import.php');
                    // Include dependent resources
                    require("$root/classes/RescueMe/DB.php");
                    require("$root/classes/RescueMe/User.php");
                    require("$root/classes/RescueMe/Log/Logs.php");
                    require("$root/vendor/psr/log/Psr/Log/LogLevel.php");

                    $import = new RescueMe\Import(
                        $opts[PARAM_HOST],
                        $opts[PARAM_USERNAME],
                        $opts[PARAM_PASSWORD],
                        $opts[PARAM_DB],
                        $root);

                    // Import unsuccessful?
                    if($import->execute() !== true) {
                       done($action, BUILD_ERROR);
                    }// if

                    break;

                case EXPORT:

                    // Skip?
                    if(in_phar()) print_help();
                    
                    // Get default path paths
                    $src = get_safe_dir($opts, SRC_DIR, "src");
                    $root = get_safe_dir($opts, EXPORT_DIR, "src");
                    
                    // Get configuration parameters
                    $config = file_exists(realpath($root)."/config.php") ? get_config_params($root) : array();

                    // Get database parameters
                    $opts = get_db_params($opts, $config);

                    // Create Export
                    require('classes/RescueMe/Export.php');
                    require("$root/classes/RescueMe/DB.php");
                    require("$root/classes/RescueMe/User.php");
                    require("$root/classes/RescueMe/Log/Logs.php");
                    require("$root/vendor/psr/log/Psr/Log/LogLevel.php");

                    $import = new RescueMe\Export(
                        $opts[PARAM_HOST],
                        $opts[PARAM_USERNAME],
                        $opts[PARAM_PASSWORD],
                        $opts[PARAM_DB],
                        $root);

                    // Export unsuccessful?
                    if($import->execute() !== true) {
                       done($action, BUILD_ERROR);
                    }// if                        

                    break;

                case PACKAGE:

                    // Skip?
                    if(in_phar()) print_help();
                    
                    // Verify options
                    $msg = (isset($opts['v']) ? null : "VERSION is missing");

                    // Print help now?
                    if(!empty($msg)) print_help(PACKAGE, $msg);
                    
                    // Get default paths
                    $src = get_safe_dir($opts, SRC_DIR, "src");
                    $dist = get_safe_dir($opts, DIST_DIR, "dist");
                    $build = get_safe_dir($opts, BUILD_DIR, "build");
                    
                    // Export database?
                    if(stristr(isset_get($opts, EXPORT, 'true'), 'true') !== false)
                    {
                        execute(array(EXPORT => array(SRC_DIR => $src, EXPORT_DIR => $src)));
                    }

                    // Create package script
                    require('classes/RescueMe/Package.php');
                    $package = new RescueMe\Package($opts['v'], $build, $src, $dist);

                    // Package unsuccessful?
                    if($package->execute() !== true) {
                       done($action, BUILD_ERROR);
                    }// if
                    
                    break;
                    
                case EXTRACT:

                    // Skip?
                    if(in_phar() === false) print_help();
                    
                    // Get parameters
                    $src = get($opts, ARCHIVE, "src.zip", false);
                    $root = get($opts, EXTRACT_DIR, getcwd(), false);

                    require('classes/RescueMe/Extract.php');

                    // Create extract
                    $extract = new RescueMe\Extract($src, $root);

                    // Execute extraction
                    if($extract->execute() !== true) {
                        done($action, BUILD_ERROR);
                    }// if

                    break;
                    
                case INSTALL:
                case CONFIGURE:
                    
                    // Skip?
                    if(in_phar() && $action === CONFIGURE || in_phar() == false && $action === INSTALL) print_help();
                    
                    // Get paths
                    $root = get_safe_dir($opts, INSTALL_DIR, in_phar() ? getcwd() : "src");
                    
                    // Get default ini values
                    $ini = is_file("rescueme.ini") ? parse_ini_file("rescueme.ini") : array();

                    // Escape version
                    $ini['VERSION'] = str_escape(isset_get($ini,'VERSION',"source"));
                    
                    // Get host specific defaults
                    $locale = (extension_loaded("intl") ? \locale_get_default() : DEFAULT_LOCALE);
                    $codes = preg_split("#[_-]#", $locale);
                    $ini['COUNTRY_PREFIX'] = isset($codes[1]) ? $codes[1] : 'US';

                    // Get default configuration parameters
                    $config = get_config_params($root);
                    $ini = array_merge($ini, $config);
                    
                    // Get default minify configuration parameters
                    $config = get_config_minify_params($root);
                    $ini = array_merge($ini, $config);
                    
                    // Get flags
                    $silent = isset_get($opts,'silent',false);
                    $update = isset_get($opts,'update',false);
                    
                    // Prompt params from user?
                    if($silent === FALSE) {
                        
                        $ini['SALT']             = str_escape(in("Salt", get($ini, "SALT", str_rnd())));
                        $ini['TITLE']            = str_escape(in("Title", get($ini, "TITLE", "RescueMe")));
                        $ini['SMS_FROM']         = str_escape(in("Sender", get($ini, "SMS_FROM", "RescueMe")));
                        $ini['DB_HOST']          = str_escape(in("DB Host", get($ini, "DB_HOST", "localhost")));
                        $ini['DB_NAME']          = str_escape(in("DB Name", get($ini, "DB_NAME", "rescueme")));
                        $ini['DB_USERNAME']      = str_escape(in("DB Username", get($ini, "DB_USERNAME", "root")));
                        $ini['DB_PASSWORD']      = str_escape(in("DB Password", get($ini, "DB_PASSWORD", "''")));
                        $ini['COUNTRY_PREFIX']   = str_escape(strtoupper(in("Default Country Code (ISO2)", get($ini, "COUNTRY_PREFIX"))));
                        $ini['DEFAULT_LOCALE']   = str_escape(in("Default Language (locale, ISO2)", get($ini, "DEFAULT_LOCALE")));
                        $ini['TIMEZONE']         = str_escape(in_timezone($ini));
                        $ini['MINIFY_MAXAGE']    = in("Minify Cache Time", get($ini, "MINIFY_MAXAGE", 1800, false));

                        echo PHP_EOL;
                    } 
                    
                    // Install only?
                    if($action === INSTALL) {

                        // Uninstall?
                        if(file_exists(realpath($root)))
                        {
                            execute(array(UNINSTALL => array(INSTALL_DIR => $root)));
                        }

                        // Get source archive
                        $src = get($opts, ARCHIVE, "src.zip", false);                    

                        // Extract?
                        if(file_exists($src))
                        {
                            execute(array('extract' => array(ARCHIVE => $src, EXTRACT_DIR => $root)));
                        }
                        
                    }
                    
                    require('classes/RescueMe/Install.php');

                    define('APP_PATH', $root.DIRECTORY_SEPARATOR);
                    define('APP_PATH_LOCALE', APP_PATH.'locale'.DIRECTORY_SEPARATOR);
                    require("$root/inc/locale.inc.php");

                    set_system_locale(DOMAIN_ADMIN, $locale);

                    // Create install
                    $install = new RescueMe\Install($root, $ini, $silent, $update);

                    // Execute installation
                    if($install->execute() !== true) {
                        done($action, BUILD_ERROR); break;
                    }// if
                    
 
                    break;

                case UNINSTALL:
                    
                    // Skip?
                    if(in_phar() === false) print_help();
                    
                    // Import classes
                    require('classes/RescueMe/Uninstall.php');

                    // Get default path install path
                    $root = get($opts, INSTALL_DIR, getcwd(), false);

                    $uninstall = new RescueMe\Uninstall($root);

                    // Uninstall successful?
                    if($uninstall->execute() !== true) {
                       done($action, BUILD_ERROR);
                    }// if

                    break;
                    
                case HELP:

                    // Get action
                    $help = $opts[NAME];

                    // Verify help action
                    $msg = (isset($help) ? null : "ACTION is missing");

                    // Print help now
                    print_help($help, $msg);

                    break;

                default:

                    print_help();

            }// switch
        
        }// foreach

    }// execute
    
    
    /**
     * Print help. 
     * 
     * Peforms a forced exit.
     * 
     * @param string $action Action
     * @param string $msg Message
     * @param int $status Exit status
     */
    function print_help($action = HELP, $msg = null, $status = BUILD_ERROR)
    {
        switch($action)
        {
            case STATUS:

                info("RescueMe Status Script" . (isset($msg) ? " - " . $msg : ""));
                echo 'Usage: rescueme status [OPTIONS]' . PHP_EOL;
                echo "OPTIONS:" . PHP_EOL;
                echo "        --install-dir Installation directory [default: src]" . PHP_EOL;
                echo "        -h            Display this help" . PHP_EOL;                
                break;
            case IMPORT:

                // Skip?
                if(in_phar()) print_help();

                info("RescueMe Import Script" . (isset($msg) ? " - " . $msg : ""));
                echo 'Usage: rescueme import [OPTIONS]' . PHP_EOL;
                echo "OPTIONS:" . PHP_EOL;
                echo "        --host        Database host" . PHP_EOL;
                echo "        --db          Database name" . PHP_EOL;
                echo "        --username    Database username" . PHP_EOL;
                echo "        --password    Database password" . PHP_EOL;
                echo "        --import-dir  Import directory [default: src]" . PHP_EOL;
                echo "        -h            Display this help" . PHP_EOL;                
                break;
            case EXPORT:

                // Skip?
                if(in_phar()) print_help();

                info("RescueMe Export Script" . (isset($msg) ? " - " . $msg : ""));
                echo 'Usage: rescueme export [OPTIONS]' . PHP_EOL;
                echo "OPTIONS:" . PHP_EOL;
                echo "        --host        Database host" . PHP_EOL;
                echo "        --db          Database name" . PHP_EOL;
                echo "        --username    Database username" . PHP_EOL;
                echo "        --password    Database password" . PHP_EOL;
                echo "        --src-dir     Source directory [default: src]" . PHP_EOL;
                echo "        --export-dir  Export directory [default: src]" . PHP_EOL;
                echo "        -h            Display this help" . PHP_EOL;                
                break;
            case EXTRACT:

                // Skip?
                if(in_phar() === false) print_help();

                info("RescueMe Extraction Script" . (isset($msg) ? " - " . $msg : ""));
                echo 'Usage: rescueme extract [OPTIONS]' . PHP_EOL;
                echo "OPTIONS:" . PHP_EOL;
                echo "        --archive     Archive [default: src.zip]" . PHP_EOL;
                echo "        --extract-dir Extraction directory [default: ".  getcwd() ."]" . PHP_EOL;
                echo "        -h            Display this help" . PHP_EOL;                
                break;
            case PACKAGE:

                // Skip?
                if(in_phar()) print_help();

                info("RescueMe Package Script" . (isset($msg) ? " - " . $msg : ""));
                echo 'Usage: rescueme package -v VERSION [OPTIONS]' . PHP_EOL;
                echo "PARAMETERS:" . PHP_EOL;
                echo "        -v            Version" . PHP_EOL;
                echo "OPTIONS:" . PHP_EOL;
                echo "        --export      Export RescueMe database [default: true]" . PHP_EOL;
                echo "        --src-dir     Source directory [default: src]" . PHP_EOL;
                echo "        --dist-dir    Package distribution directory [default: dist]" . PHP_EOL;
                echo "        -h            Display this help" . PHP_EOL;                
                break;
            case INSTALL:
                
                // Skip?
                if(in_phar() === false) print_help();
                
                info("RescueMe Install Script" . (isset($msg) ? " - " . $msg : ""));
                echo 'Usage: rescueme install [OPTIONS]... ' . PHP_EOL;
                echo "OPTIONS:" . PHP_EOL;
                echo "        --silent      No user interaction [use defaults]" . PHP_EOL;
                echo "        --archive     RescueMe archive file [default: src.zip]" . PHP_EOL;
                echo "        --install-dir Install directory [default: ".getcwd()."]" . PHP_EOL;
                echo "        -h            Display this help" . PHP_EOL;                
                break;
            case CONFIGURE:
                
                // Skip?
                if(in_phar()) print_help();
                
                info("RescueMe Configure Script" . (isset($msg) ? " - " . $msg : ""));
                echo 'Usage: rescueme configure [OPTIONS]... ' . PHP_EOL;
                echo "OPTIONS:" . PHP_EOL;
                echo "        --silent      No user interaction [use defaults]" . PHP_EOL;
                echo "        --update      Update libraries if already installed [default: false]" . PHP_EOL;
                echo "        --install-dir Install directory [default: src]" . PHP_EOL;
                echo "        -h            Display this help" . PHP_EOL;                
                break;
            case UNINSTALL:
                
                // Skip?
                if(in_phar() === false) print_help();
                
                info("RescueMe Uninstall Script" . (isset($msg) ? " - " . $msg : ""));
                echo 'Usage: rescueme uninstall [OPTIONS]... ' . PHP_EOL;
                echo "OPTIONS:" . PHP_EOL;
                echo "        --install-dir Install directory [default: ".(in_phar() ? getcwd() : "src")."]" . PHP_EOL;
                echo "        -h            Display this help" . PHP_EOL;                
                break;
            case HELP:
            default:
                info("RescueMe Command Line Scrips" . (isset($msg) ? " - " . $msg : ""));
                echo 'Usage: rescueme ACTION... [OPTIONS]... ' . PHP_EOL;
                echo "OPTIONS:" . PHP_EOL;
                echo "        -h            Display this help" . PHP_EOL;                
                echo "ACTION:" . PHP_EOL;
                echo "        status        Show RescueMe status (parameters)" . PHP_EOL;
                if(in_phar()) {
                    echo "        extract       Extract RescueMe" . PHP_EOL;
                    echo "        install       Install RescueMe" . PHP_EOL;
                    echo "        uninstall     Uninstall RescueMe" . PHP_EOL;
                }
                else {
                    echo "        import        Import RescueMe database (sql->db)" . PHP_EOL;
                    echo "        export        Export RescueMe database (db->sql)" . PHP_EOL;
                    echo "        configure     Configure RescueMe source (dev)" . PHP_EOL;
                    echo "        package       Package RescueMe as executable phar-archive" . PHP_EOL;
                }
                echo "        help          Display help about an action" . PHP_EOL;
                
                break;
        }// switch
        
        // Finished
        echo PHP_EOL . PHP_EOL;

        exit($status);
        
    }// print_help
    
?>
    