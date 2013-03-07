<?php

// include DoozR core
require_once 'Controller/Core/Core.php';

// instanciate DoozR core
$DoozR = DoozR_Core::getInstance();

$DoozR->dispatch('demo', true);

?>
