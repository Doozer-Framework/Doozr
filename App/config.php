<?php

/**
 * bootstrap
 */
require_once '../Framework/DoozR/Bootstrap.php';

/**
 * Instanciate DoozR
 */
$DoozR = DoozR_Core::getInstance();

/**
 * Get registry containing DoozR's base object instances
 */
$registry = DoozR_Registry::getInstance();


/**
 * create new instance of module configreader,
 * pass 'json' as argument to it -> this means we
 * want to use a json-container for CRUD-operations
 * (currently available container: json, ini)
 */
/*
$configreader = DoozR_Loader_Moduleloader::load('configreader', array('json'));


$filename = 'config.txt';
$data     = json_encode(array('A' => 1, 'B' => 2));


pre( $configreader->read($filename, $data) );
*/

?>
