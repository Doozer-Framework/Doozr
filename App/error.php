<?php
/***********************************************************************************************************************
 *
* DEMONSTRATION
* Core: Error- and Exception-Handling
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
 * Show error handling example error is triggered but exception
 * is thrown and get catched afterwards
 */
try {
    // trigger an error manually
    trigger_error('Aloha @ '.microtime(), E_USER_ERROR);

} catch (Exception $e) {
    pred($e->getTrace());

}
