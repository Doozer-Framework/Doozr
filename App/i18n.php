<?php

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
 * get I18n Module of DoozR -> for translations, converting
 * and formatting values ...
 */
$i18n = DoozR_Loader_Moduleloader::load('i18n', array('de', $registry->config));
$translator = $i18n->getTranslator();
$translator->setNamespace('demo');
$localized = $translator->_('x_books_in_my_y_shelves', array(5, 4));

pre($localized);

?>
