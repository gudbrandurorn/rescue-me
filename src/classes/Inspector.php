<?php

    /**
     * File containing: Inspector class
     * 
     * @copyright Copyright 2013 {@link http://www.discoos.org DISCO OS Foundation} 
     *
     * @since 03. July 2013
     * 
     * @author Kenneth Gulbrandsøy <kenneth@discoos.org>
     */

    /**
     * Inspector class
     * 
     * @package 
     */
    class Inspector
    {
        
        public static function namespaceOf($className, $separator='\\') {
            $className = ltrim($className,$separator);
            if (false !== ($lastNsPos = strripos($className, $separator))) {
                return substr($className, 0, $lastNsPos);
            } 
            return $separator;
        }
        
        public static function subclassesOf($className, $abstract=false, $includePath=null, $separator='\\', $extension='.php') {

            $classes = array();
            
            $includePath = isset($includePath) ? $includePath : dirname(__FILE__); 
            
            $dir = new DirectoryIterator($includePath);
            
            $className = ltrim($className,$separator);
            
            $namespace = self::namespaceOf($className);
            
            foreach ($dir as $file) {
                if($file->isFile()) {
                    $basename = $file->getBasename($extension);
                    $subclassName = ltrim($namespace.$separator.$basename,$separator);
                    if($subclassName !== 'Inspector' && class_exists($subclassName)) {
                        $class = new ReflectionClass($namespace.$separator.$basename);
                        if($class->isSubclassOf($className) && $class->isAbstract() === $abstract) {
                            $classes[$subclassName] = $basename;
                        }
                    }
                } elseif($file->isDir() && !$file->isDot()) {
                    $classes += self::subclassesOf($className, $abstract, $file->getPathname(), $separator, $extension);
                }
            }
            return $classes;
        }
        
    }// Inspector
