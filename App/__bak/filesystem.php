<?php

// include DoozR bootstrapper
require_once '../Framework/Core/DoozR.bootstrap.php';

// instanciate DoozR
$DoozR = DoozR_Core::getInstance();

// get module filesystem (REAL-MODE)
//$fs = DoozR_Core::module('filesystem');
//pre($fs);

// get module filesystem (VIRTUAL-MODE)
$fs = DoozR_Core::module('filesystem', 'virtual');
pre($fs);

// check writable
$result = $fs->writable("D:\\_temp\\");

// debug result
pre('Folder writable? ');
pre($result);

// write
$result = $fs->write("D:\\_temp\\virtualfilesystem.txt", "\0");

// debug result
pre('File written? ');
pre($result);

pred('done');

?>
