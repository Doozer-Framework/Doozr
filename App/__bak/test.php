<?php

// require the bootstrapper for doozR
require_once '../Framework/Core/DoozR.bootstrap.php';

// get an instance of doozR
$DoozR = DoozR_Core::getInstance();

// get a handle on captcha
//$captcha = DoozR_Core::module('captcha');

pred(DoozR_Core::config()->get('DATABASE.GORMDB.DB.PRIMARY_DB.PORT'));

?>
