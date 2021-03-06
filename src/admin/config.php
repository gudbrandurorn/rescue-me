<?php

    require(implode(DIRECTORY_SEPARATOR, array(dirname(__DIR__),'config.php')));

    // RescueMe administration paths
    define('ADMIN_PATH', APP_PATH.'admin/');
    define('ADMIN_PATH_INC', ADMIN_PATH.'inc/');
    define('ADMIN_PATH_GUI', ADMIN_PATH.'gui/');
    define('ADMIN_PATH_CLASS', ADMIN_PATH.'classes/');

    foreach(array('common', 'gui') as $lib) {
        require(ADMIN_PATH_INC.$lib.'.inc.php');
    }
    
?>