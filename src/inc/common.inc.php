<?php

    function assert_types($values)
    {
        foreach($values as $type => $value)
        {
            assert_type($type, $value);
        }
    }
    
    function assert_type($expected, $actual)
    {
        if(!call_user_func("is_$expected", $actual))
        {
            throw new Exception("[$actual] is not of type '$actual'.");
        }
    }

    function prepare_values($fields, $values) 
    {
        reset($fields);
        reset($values);
        $prepared = array();
        foreach($fields as $field) {
            $value = current($values);
            if($value === FALSE) break;
            $prepared[$field] = $value;
            next($values);
        }
        return $prepared;
    }    
    
    function isset_get($array, $key, $default=null) {
        return isset($array[$key]) ? $array[$key] : $default;
    }
    
    function array_pick($array, $key) {
        $values = array();
        foreach($array as $name => $value) {
            if($key === $name 
                || is_string($key) && strstr($name, $key) !== false
                || is_array($key) && in_array($name, $key)) {
                $values[$name] = $value;
            }
        }
        return $values;
    }
    
    /**
     * Exclude given key(s) from array
     * 
     * @param array $array
     * @param mixed $key
     * @return array
     */
    function array_exclude($array, $key) {
        $values = array();
        foreach($array as $name => $value) {
            if(!($key === $name 
                || is_string($key) && strstr($name, $key) !== false
                || is_array($key) && in_array($name, $key))) {
                $values[$name] = $value;
            }
        }
        return $values;
    }
    
    
    function modules_exists($module, $_ = null) {
        
        $missing = array();
        
        foreach(func_get_args() as $module) {
            if(!RescueMe\Module::exists($module))
            {
                $missing[] = $module;
            }
        }    
        
        if(defined('USE_SILEX') && USE_SILEX)
        	return empty($missing);
        
        if(!empty($missing)) {
            insert_errors(_("Missing modules").' ( <a href="'.ADMIN_URI.'setup">'. _("Configure"). "</a>): ", $missing);
        }
        
        return empty($missing);
    }    
    
    
    function is_function($value) {
        return preg_match("#([A-Za-z0-9_]+)[\(\)]+#", $value) !== 0;
    }
    
    /** 
     * Get random string of given length
     * 
     * @param integer $length String lengt
     * @return string
     */
    function str_rnd($length = 8)
    {
        $str = '';
        
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=!@#$%&*()_+,./<>?;:[]{}';
        
        $max = (strlen($chars) - 1);

        for($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, $max)];
        }

        return $str;
    }// str_rnd
    
    
    /**
     * Escape string with "'"
     * 
     * @param string $string
     * @param string $delimiter"
     */
    function str_escape($value) {
        return isset($value) ? ("'".trim($value,"'")."'") : "''";
    }// str_escape
    
    
    
?>
