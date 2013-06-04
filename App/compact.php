<?php
/***********************************************************************************************************************
 *
 * DEMONSTRATION
 * Service: Compact (Demo of compacting resources)
 *
 **********************************************************************************************************************/

/**
 * Bootstrap
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
 * Get module API from Serviceloader
 */
$compact = DoozR_Loader_Serviceloader::load('compact');


pred($compact);

?>
