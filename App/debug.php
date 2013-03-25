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


// trigger an error manually
trigger_error('Aloha @ '.microtime(), E_USER_ERROR);


//$registry->logger->log('some test');

?>
