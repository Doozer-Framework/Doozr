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
 * Example call:
 *
 */
$parser = new DoozR_Form_Service_Parser_Form();

// Extract information
$parser->open('form1.html')->parse();

// Show template
pre(
    $parser->getTemplate()
);

// Show config
pre(
    $parser->getConfiguration()
);
