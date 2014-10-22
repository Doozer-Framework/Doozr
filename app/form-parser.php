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
 * Get the "I18n" module to demonstrate you the nice interaction between module "Form" and "I18n"
 */
$i18n = DoozR_Loader_Serviceloader::load('i18n', $registry->config, 'de');


/**
 * The store for the form transfer from one request to next
 */
$session = DoozR_Loader_Serviceloader::load('session');


// create a new form-container which combines the control-layer and the HTML parts
$formManager = new DoozR_Form_Service_FormManager(
    'register',                                                 // The namespace (used for session, I18n, ...)
    $i18n,                                                      // The I18n service instance for translation(s) [DI]
    new DoozR_Form_Service_Component_Input(                     // Input element <- for cloning [DI]
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    ),
    new DoozR_Form_Service_Component_Form(                      // The form element we operate on [DI]
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic(),
        'register'
    ),
    new DoozR_Form_Service_Store_Session($session),             // The session store [DI]
    new DoozR_Form_Service_Renderer_Html(),                     // A Renderer -> Native = HTML [DI]
    new DoozR_Form_Service_Validate_Validator(),                // A Validator to validate the elements [DI]
    new DoozR_Form_Service_Validate_Error(),                    // A Error object <- for cloning [DI]
    $registry->front->getRequest()->getArguments()              // The currents requests arguments
);


// Get a HTML Parser!
$parser = new DoozR_Form_Service_Parser_Html(
    new DoozR_Form_Service_Configuration()
);

// Get some HTML input from a file
$html = file_get_contents(DOOZR_APP_ROOT . 'form1.html');

// Set the content we parse from
$parser->setInput($html);

// Extract information
$parser->parse();


// Get extracted configs
$configurations = $parser->getConfigurations();
$template       = $parser->getTemplate();

pred($configurations);
die;


/*
// Show template
pre(
    $parser->getTemplate()
);

// Show config
pre(
    $parser->getConfiguration()
);
*/
