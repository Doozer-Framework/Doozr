<?php

// include DoozR core
require_once '../Controller/Core/Core.php';

// instanciate DoozR core
$DoozR = DoozR_Core::getInstance('');

$session = $DoozR->getModuleHandle('session');

$auth = $DoozR->getModuleHandle('auth');

$auth->logout('index.php');

?>