<?php
/*
session_start();

var_dump($_SESSION['ABC']);

//unset($_SESSION['ABC']);

?>

<a href="session1.php">session1</a>
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


?>
<a href="session1.php">session1</a>