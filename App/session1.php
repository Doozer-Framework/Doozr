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

$session->set('Hallo', 'Welt');

/*
session_start();

if (isset($_SESSION['HALLO'])) {
    echo 'already set: '.$_SESSION['HALLO'];
}

$_SESSION['HALLO'] = 'Welt';
*/

?>

<a href="session2.php">session transfer</a>