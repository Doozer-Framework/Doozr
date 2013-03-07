<?php
/*
session_start();

var_dump($_SESSION['ABC']);

$_SESSION['ABC'] = 123;

?>

<a href="session2.php">session2</a>
*/


/**
 * include DoozR bootstrapper
 */
require_once '../Framework/Core/DoozR.bootstrap.php';

/**
 * instanciate DoozR
 */
$DoozR = DoozR_Core::getInstance();

/**
 * get module session
 */
$session = DoozR_Core::module('session');

$proof = $session->get('jhfsdjkgfnnsdnhf');

pre($proof);

$session->set('jhfsdjkgfnnsdnhf', 'VALUE_VALUE_VALUE');

?>
<a href="session2.php">session2</a>