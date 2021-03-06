<?php

require_once('../config.php');

use RescueMe\Missing;
use RescueMe\Properties;

set_system_locale();

$id = $_GET['id'];
$missing = Missing::get($id);

if($missing !== false) {
    $version = VERSION;
    $user_id = $missing->user_id;
    $type = Properties::get(Properties::LOCATION_APPCACHE, $user_id);
    if($type === 'settings')
    {
        header('Content-Type: text/cache-manifest');

        $options['age'] = Properties::get(Properties::LOCATION_MAX_AGE, $user_id);
        $options['wait'] = Properties::get(Properties::LOCATION_MAX_WAIT, $user_id);   
        $options['acc'] = Properties::get(Properties::LOCATION_DESIRED_ACC, $user_id);
        $options['locale'] = Properties::get(Properties::SYSTEM_LOCALE, $user_id);
        $version .= ' ' . md5(json_encode($options));
        
        echo "CACHE MANIFEST\n# $version\n../img/loading.gif\nNETWORK:\n*";
        
    } else {
        
        // Tell browser to remove appcache
        header("HTTP/1.0 404 Not Found");
        
    }
}

?>