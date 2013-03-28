<?php
/***********************************************************************************************************************
 *
* DEMONSTRATION
* Core: Registry
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
 * Get registry containing DoozR's base object instances
 */
$registry = DoozR_Registry::getInstance();


/**
 *  now we can grab the already stored instances from DoozR core
 */
$logger = $registry->logger;
$config = $registry->config;

/**
 * add one or more new ones
 */
$foo    = new stdClass();
$foo->bar = 'Hello World!';
$registry->foo = $foo;

/**
 * grab and use it direct from registry anywhere else in your followup code ...
 */
pre($registry->foo->bar);

?>
