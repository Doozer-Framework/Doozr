<?php

// include DoozR core
require_once 'Controller/Core/Core.php';

// instanciate DoozR core
$DoozR = DoozR_Core::getInstance();

//trigger_error('ABER HALLO!');

$DoozR->coreError('use thise method on critical catched errors', true);

?>
