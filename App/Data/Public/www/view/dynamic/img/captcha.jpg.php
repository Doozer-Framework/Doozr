<?php

// include core
require_once '../../../Controller/Core/Core.php';

// instanciate DoozR core
$DoozR = DoozR_Core::getInstance();

// get a handle on captcha
$objCaptcha = $DoozR->getModuleHandle('captcha');

// create captcha and send to browser as jpg
$strTTFPath = ''; //CONF_PATH_BASE . 'Controller/libs/PhpCaptcha/ttf/';
$aFonts = array($strTTFPath.'VeraBd.ttf', $strTTFPath.'VeraIt.ttf', $strTTFPath.'Vera.ttf');
$objCaptcha->Setup($aFonts, 140, 50);
$objCaptcha->Create();

?>
