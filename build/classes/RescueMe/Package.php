<?php

    /**
     * File containing: Package class
     * 
     * @copyright Copyright 2013 {@link http://www.discoos.org DISCO OS Foundation} 
     *
     * @since 18. August 2013
     * 
     * @author Kenneth Gulbrandsøy <kenneth@discoos.org>
     */
    
    namespace RescueMe;
    
    /**
     * Package class
     * 
     * @package 
     */
    class Package
    {
        /**
         * RescueMe version
         * @var string
         */
        private $version;
        
        /**
         * Build directory
         * @var string
         */
        private $build;

        
        /**
         * Source directory
         * @var string
         */
        private $src;
        

        /**
         * Distribution directory
         * @var string
         */
        private $dist;
        

        /**
         * Constructor
         *
         * @param string $version RescueMe version
         * @param string $build RescueMe build directory
         * @param string $src RescueMe source directory
         * @param string $dist Package distribution directory
         * 
         * 
         * @since 18. August 2013
         *
         */
        public function __construct($version, $build, $src, $dist)
        {
            $this->version = $version;
            $this->src = $src;
            $this->dist = $dist;
            $this->build = $build;
            
        }// __construct
        
        
        /**
         * Execute package script
         * 
         * @return mixed TRUE if success, error message otherwise.
         * 
         */
        public function execute()
        {
            begin(PACKAGE);
            
            // Get package file without extension
            $package = "$this->dist".DIRECTORY_SEPARATOR."rescueme-" . $this->version;

            // Notify
            info("  Packaging [$this->src] into [$package]....", BUILD_INFO, NEWLINE_NONE);
            
            // Create folder if not exists
            if(!file_exists($this->dist)) {
                mkdir($this->dist);
            }

            // Delete if already exists
            if(file_exists($package)) {
                unlink("$package.phar");
            }

            // Could be done in php.ini
            ini_set("phar.readonly", "0");

            // Creating new Phar
            $oPhar = new \Phar("$package.phar");

            // Start buffering
            $oPhar->startBuffering();

            // Pointing main file which bootstrap all resources
            $oPhar->setDefaultStub('cli.php', 'cli.php');

            // Add build scripts source
            $oPhar->buildFromDirectory("$this->build");

            // Add dependencies
            $oPhar->addFile("$this->src/inc/locale.inc.php", "inc/locale.inc.php");

            // Add 5.4+ compatible class loader
            $oPhar->addFile("$this->src/vendor/composer/ClassLoader.php", "classes/ClassLoader.php");

            // Prepare ini values
            $ini = "VERSION = " . $this->version;

            // Add ini file
            $oPhar->addFromString("rescueme.ini", $ini);

            // Prepare default config file
            $config = file_get_contents(realpath("$this->src/config.tpl.php"));

            // Add configuration template
            $oPhar->addFromString("config.tpl.php", $config);

            // Prepare default minify config file
            $config_minify = file_get_contents(realpath("$this->src/config.minify.tpl.php"));

            // Add minify configuration template
            $oPhar->addFromString("config.minify.tpl.php", $config_minify);            
            
            // Package source files as zip file, exclude dev-local (ignored) files
            $zip = new \ZipArchive();
            $zip->open("src.zip", \ZipArchive::CREATE);
            add_folder_to_zip("$this->src/", $zip, "src/", "$this->src/config.php|$this->src/config.minify.php|.*min/cache");            
            $zip->close();

            // Add source to package
            $oPhar->addFile("src.zip");

            // Write changes to file
            $oPhar->stopBuffering();

            // Cleanup
            unlink("src.zip");

            info("DONE");
            
            done(PACKAGE);
            
            return true;            
            
        }// execute
        

    }// Package
