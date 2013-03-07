<?php

/**
 * bootstrap
 */
require_once '../Framework/DoozR/Bootstrap.php';

/**
 * Instanciate DoozR
 */
$DoozR = DoozR_Core::getInstance();

$session = DoozR_Loader_Moduleloader::load('session');

pre( $session->get('Hallo') );
pre($_SESSION);


/*
session_start();
var_dump($_SESSION);
*/

?>

<a href="session1.php">session transfer</a>