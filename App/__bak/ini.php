<?php

// include DoozR bootstrapper
require_once '../Framework/Core/DoozR.bootstrap.php';

// instanciate DoozR
$DoozR = DoozR_Core::getInstance();

// get module filesystem
$ini = DoozR_Core::module('ini', 'C:\\Programme\\xampp\\htdocs\\DoozR\\Framework\\Data\\Private\\Config\\Config.ini.php');

pred($ini->get('BASE.BASE_MODULES_CLI'));

?>
