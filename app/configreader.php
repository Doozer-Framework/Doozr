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
 * Instantiate DoozR
 */
$DoozR = DoozR_Core::getInstance();


/**
 * create new instance of service config, pass 'json' as argument to it -> this means we
 * want to use a json-container for CRUD-operations (currently available container: json, ini)
 */
$config = DoozR_Loader_Serviceloader::load('config', 'json', true);


/**
 * Read configuration from file ".config"
 * and create with data some runtimeconfiguration dummy which is merged into .config
 */
$filename = 'Data/Private/Config/.config';
$runtime  = array('transmission' => array('gzip' => array('enabled' => false)));


/**
 * read configuration from file in JSON-format and merge with some runtime parameter
 */
if ($config->read($filename)) {

    pre($config->transmission->gzip->enabled);

    $config->read($runtime);

    pre($config->transmission->gzip->enabled);
    exit;

} else {
    pre('Configuration could not be read ...');
}
