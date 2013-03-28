<?php
/***********************************************************************************************************************
 *
 * DEMONSTRATION
 * Module: Session
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
 * Get module session from Moduleloader
 */
$session = DoozR_Loader_Moduleloader::load('session');

/**
 * Check if session variable is set -> echo out if exist
 */
if ($session->issetVariable('Hallo')) {
    pre($session->get('Hallo'));

}

/**
 * Set a session variable
 */
$session->set('Hallo', 'Welt');

?>

<a href="session.php">reload this file</a>