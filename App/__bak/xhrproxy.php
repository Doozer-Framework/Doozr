<?php

// include DoozR bootstrapper
require_once '../Framework/Core/DoozR.bootstrap.php';

// instanciate DoozR
$DoozR = DoozR_Core::getInstance();

// get module filesystem
$xhrproxy = DoozR_Core::module('xhrproxy');

// parameter
$parameter = array(
    'hl'     => 'de',
    'source' => 'hp',
    'q'      => 'test',
    'aq'     => 'f',
    'aqi'    => 'g10',
    'fp'     => '60998b6bb3b59350'
);

// fetch result
//$result = $xhrproxy->fetch('http://campaignworld-3.localhost/main/sessioncheck/', 'GET', $parameter);
$result = $xhrproxy->fetch('http://www.xing.com/de/', 'GET', $parameter);

// show the result
echo $result;

?>
