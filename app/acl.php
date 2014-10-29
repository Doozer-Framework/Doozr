<?php
/***********************************************************************************************************************
 *
 * DEMONSTRATION
 * Service: Acl (Demo of a Webservice)
 *
 **********************************************************************************************************************/

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


// the actions supported by object
/*
$actions = array(
    'create',
    'read',
    'update',
    'delete'
);
*/

// create a new object to generate a permissions set
$acl = DoozR_Loader_Serviceloader::load('acl');

// add/remove the permissions you want
$acl->addPermission('create');
$acl->addPermission('read');
$acl->addPermission('update');
$acl->removePermission('delete');

// and get an integer that correlates to the set of permissions you chose.
// this can be stored and associated with a user account.
$code = $acl->evaluate();

// create an object and pass it a permissions code to test against
$checkAcl = DoozR_Loader_Serviceloader::load('acl', $code);

// get an array of possible actions you can test for
$actions = $checkAcl->getActions();

// check which actions are allowed with the permissions code you passed in
foreach ($actions as $action) {
    if ($checkAcl->hasPermission($action)) {
        echo $action . ' is allowed <br>';
    } else {
        echo $action . ' is NOT allowed <br>';
    }
}
