<?php

require_once 'Core/Controller/Core.php';

$DoozR = DoozR_Core::getInstance();

$browserdetect = $DoozR->getModuleHandle('browserdetect');

echo $browserdetect->getBrowser();

echo '<br />';

echo $browserdetect->getVersion();

?>
