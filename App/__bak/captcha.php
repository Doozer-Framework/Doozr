<?php

// require the bootstrapper for DoozR
require_once '../Framework/Core/DoozR.bootstrap.php';

// get an instance of DoozR
$DoozR = DoozR_Core::getInstance();

// get a handle on captcha
$captcha = DoozR_Core::module('captcha');

// define fonts
$captchaFonts = array('VeraSeBd.ttf', 'elephant.ttf', 'VeraBd.ttf', 'VeraIt.ttf', 'Vera.ttf');

// create/setup captcha ...
$captcha->setup($captchaFonts, 160, 80);

// set branding text
//$captcha->setBrandingText('(c)2005 - 2013 Benjamin Carl');

// colorize the whole captcha?
$captcha->setColorizeCaptcha(false);

// ... retrieve it's binary data
$data = $captcha->create(); // default parameter = false [return binary]
// and deliver through Response-Class (web)
DoozR_Core::front()->getResponse()->sendJpeg($data, true);

/*
// output through captcha module
$captcha->create(true); // [output binary image data by captcha class]
*/

/*
// write captch to file
$captcha->create('D://captcha.jpg');
*/

?>
