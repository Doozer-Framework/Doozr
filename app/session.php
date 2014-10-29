<?php
/***********************************************************************************************************************
 *
 * DEMONSTRATION
 * Service: Session
 *
 **********************************************************************************************************************/

/**
 * Bootstrap
 */
require_once '../Framework/DoozR/Bootstrap.php';

/**
 * Instantiate DoozR
 */
$DoozR = DoozR_Core::getInstance();

/**
 * Get module session from Serviceloader
 */
$session = DoozR_Loader_Serviceloader::load('session');

/**
 * Check if session variable is set -> echo out if exist
 */
if ($session->issetVariable('Foo')) {
    pre($session->get('Foo'));

}

/**
 * Set a session variable
 */
$session->set('Foo', 'Bar');

?>

<a href="session.php">reload this file</a>
