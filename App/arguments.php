<?php
/***********************************************************************************************************************
 *
 * DEMONSTRATION
 * Service: Form
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
 * Get the config instance easily from registry
 */
$config = $registry->config;


/**
 * Get DoozR_Request_Web
 */
$request = $registry->front->getRequest();


/**
 * Get DoozR_Request_Arguments
 */
$arguments = $request->getArguments();


/**
 * Iterate arguments just for demonstration
 */
/* @var DoozR_Request_Argument $argument */
foreach ($arguments as $argument => $value) {
    // the name of the argument
    pre($argument);

    // the value
    pre($value->getRaw());
    pre($value->getSanitized());
    pre($value->getImpact());

    // get as object?
    pre($arguments->{$argument});

    // Access like array
    pre($arguments[$argument]);
}
