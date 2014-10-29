<?php
/***********************************************************************************************************************
 *
 * DEMONSTRATION
 * Service: I18n
 *
 **********************************************************************************************************************/

/**
 * bootstrap
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


/**
 * get I18n Service of DoozR -> for translations, converting and formatting values ...
 * @var DoozR_I18n_Service $i18n
 */
$i18n = DoozR_Loader_Serviceloader::load('i18n', $registry->config);


$translator = $i18n->getTranslator();
$translator->setNamespace('default');
pred($translator->_('This is "bar"'));
