<?php
/***********************************************************************************************************************
 *
 * DEMONSTRATION
 * Service: Http
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
 * Get the "Http" service
 * @var $http DoozR_Http_Service
 */
$http = DoozR_Loader_Serviceloader::load('http');


/**
 * Test Data
 */
$host     = 'ip.jsontest.com';
$port     = 80;
$protocol = DoozR_Http_Service::CONNECTION_PROTOCOL_HTTP;


/*
$http->setProtocol($protocol);
$http->setHost($host);
$http->setPort($port);

$http->get();
$http->post();
$http->put():
$http->patch();
$http->delete();
$http->head();
$http->options();
$http->connect();
$http->trace();
*/

$content = $http->protocol($protocol)->host($host)->port($port)->get(null, array('q' => 'DoozR'))->run();

pred($content);
