<?php

/* This is the Autoloader which takes a static file/class mapping matrix from ./Autoload/Matrix.php */

[[docblock.tpl]]

function Doodi_[[proxy-name]]_Autoload($class)
{
    // holds the classes map static in memory
    static $classes;

    // get statically (prebuild map of file <-> location)
    if ($classes === null) {
        $classes = include dirname(__FILE__).'/Autoload/Matrix.php';
    }

    // check if classname can be found in map or if it must be constructed from classname
    if (!isset($classes[$class])) {
        // try here to load file by name
        $folder = dirname(__FILE__).DIRECTORY_SEPARATOR.
                  str_replace('_', DIRECTORY_SEPARATOR, str_replace('Doodi_[[proxy-name]]_', '', $class)).DIRECTORY_SEPARATOR;
        $file = $folder.str_replace('_', '', $class).'.php';

        if (!file_exists($file)) {
            // return status => failed
            return false;
        }
    } else {
        // get filename from prebuild array (the fastest way!)
        $file = dirname(__FILE__).'/'.$classes[$class];
    }

    // now include the file
    include $file;

    // return status => success
    return true;
}

/**
 * register the autoloader
 */
spl_autoload_register('Doodi_[[proxy-name]]_Autoload');

?>
