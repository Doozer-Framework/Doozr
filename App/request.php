<?php
/***********************************************************************************************************************
 *
 * DEMONSTRATION
 * Request(s) in DoozR
 *
 **********************************************************************************************************************/

/**
 * bootstrap
 */
require_once '../Framework/DoozR/Bootstrap.php';


/**
 * Instanciate DoozR
 */
$DoozR = DoozR_Core::getInstance();


/**
 * Get registry containing DoozR's base object instances
 */
$registry = DoozR_Registry::getInstance();

/**
 * get access to Front-Controller
 * @var $front DoozR_Controller_Front
 */
$front = $registry->front;


// get request from front-controller
// -> DoozR_Request_Web when served via Apache/IIS
// -> DoozR_Request_Httpd when served via PHP-integrated-webserver
$request = $front->getRequest();

pred($request->getArguments()->A());
