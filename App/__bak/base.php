<?php

// include DoozR bootstrapper
require_once '../Framework/Core/DoozR.bootstrap.php';

$start = microtime(true);

// instanciate DoozR
$DoozR = DoozR_Core::getInstance();

$end = microtime(true);
pre($end - $start);

// demo for instanciating a user class
//$myClass = new MyClass();

?>
