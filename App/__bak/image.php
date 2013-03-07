<?php

// require the bootstrapper for DoozR
require_once '../Framework/Core/DoozR.bootstrap.php';

// get an instance of DoozR
$DoozR = DoozR_Core::getInstance();

// get a handle on captcha
$moduleimage = DoozR_Core::module('image');

$image = DoozR_Image::load('D://desktop_wallpaper.bmp');

$resized = $image->resize(100);

//$resized->output('jpg', 30);
$resized->saveToFile('D://small.jpg', 30);

?>
