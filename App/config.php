<?php
/***********************************************************************************************************************
 *
* DEMONSTRATION
* Service: Config
*
**********************************************************************************************************************/

/**
 * bootstrap
 */
require_once '../Framework/DoozR/Bootstrap.php';


/**
 * Instanciate DoozR
 */
$DoozR = DoozR_Core::getInstance();


/**
 * create new instance of service configreader, pass 'json' as argument to it -> this means we
 * want to use a json-container for CRUD-operations (currently available container: json, ini)
 */
$configreader = DoozR_Loader_Serviceloader::load('configreader', 'json', true);


/**
 * Read configuration from file ".config"
 * and create with data some runtimeconfiguration dummy which is merged into .config
 */
$filename = 'Data/Private/Config/.config';
$runtime  = array('transmission' => array('gzip' => array('enabled' => false)));


/**
 * read configuration from file in JSON-format and merge with some runtime parameter
 */
if ($configreader->read($filename)) {

    pre($configreader->transmission->gzip->enabled);

    $configreader->read($runtime);

    pre($configreader->transmission->gzip->enabled);
    exit;

} else {
    pre('Configuration could not be read ...');
}
