<?php
/***********************************************************************************************************************
 *
 * DEMONSTRATION
 * Service: Oauth2
 *
 **********************************************************************************************************************/

// current context
// => this is the server (service-provider) which receives a request
// the place where the user will see a message and is required to either
// allow or reject access ...

/**
 * Bootstrap
 */
require_once '../Framework/DoozR/Bootstrap.php';

/**
 * Instantiate DoozR
 */
$DoozR = DoozR_Core::getInstance();

/**
 * Get registry containing DoozR's base object instances
 */
$registry = DoozR_Registry::getInstance();

require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Oauth2/Service.php';

// create a new object to generate a permissions set
/* @var oauth2 OAuth2\Server */
$oauth2 = DoozR_Loader_Serviceloader::load('oauth2', DoozR_Oauth2_Service::MODE_SERVER, 'PDO', array(
        'dsn'  => 'mysql:dbname=test;host=127.0.0.1',
        'user' => 'root',
        'pass' => ''
    )
);


$get = $_GET;
$post = $_POST->getArray();

$request = OAuth2\Request::createFromGlobals($get, $post);
$response = new OAuth2\Response();

// let the oauth2-server-php library do all the work!
$oauth2->handleTokenRequest($request, $response);

$response->send();
