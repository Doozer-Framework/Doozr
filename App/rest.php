<?php
/***********************************************************************************************************************
 *
 * DEMONSTRATION
 * Service: Api (Demo of a Webservice)
 *
 **********************************************************************************************************************/

/**
 * Bootstrap
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
 * Get module API from Serviceloader
 */
$api = DoozR_Loader_Serviceloader::load('rest');


/**
 * @todo Implement a rerouting here via DoozR_Route
 *       The goal is to be able to reroute while already
 *       running a route.
 */
//pred($api);


/*
WE MUST IMPLEMENT A WAY WHICH MAKES IT POSSIBLE TO ROUTE REQUESTS
TO IMPLEMENTED WEBSERVER THROUGH OUR CENTRAL ROUTING CLASS SO WE
CAN SAY THAT THIS IS OUT CENTRAL POINT OF ROUTING NODES NO MATTER
WHICH MODE PHP IS RUNNING IN (CLI|WEB|HTTPD)
// run route init
DoozR_Route::init(
    (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '/',
    $config->route(),
    $registry,
    $config->base->pattern->autorun()
);

    if (preg_match('/\.(?:png|jpg|jpeg|gif)$/', $_SERVER["REQUEST_URI"]))
        return false; // Liefere die angefragte Ressource direkt aus
*/
