<?php

// include DoozR core
require_once '../Controller/Core/Core.php';

// instanciate DoozR core
$DoozR = DoozR_Core::getInstance($configOverride);

$xmlhttpproxy = $DoozR->getModuleHandle('xmlhttpproxy');

$url = $_GET['url'];

$xmlhttpproxy->setType('GET');

$xmlhttpproxy->fetchURL($url);

echo $xmlhttpproxy->getResult();

?>
