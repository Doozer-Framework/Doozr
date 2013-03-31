<?php
/***********************************************************************************************************************
 *
* DEMONSTRATION
* Module: I18n
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
 * get I18n Module of DoozR -> for translations, converting and formatting values ...
 */
$i18n = DoozR_Loader_Moduleloader::load('i18n', array('de', $registry->config));
$translator = $i18n->getTranslator();
$translator->setNamespace('default');
$translated = $translator->_('x_books_in_my_y_shelves', array(921, 8));
pre($translated);


/**
 * demonstrate some formatter stuff:
 * available formatter: datetime, currency, measure, number, string
 *
 * Demonstrate - formatter: Datetime
 */
$formatterDatetime = $i18n->getFormatter('datetime');
$time = $formatterDatetime->shortTime(time());
$day = $formatterDatetime->dayName(time());
pre($day);


/**
 * Demonstrate - formatter: Currency
 */
$formatterCurrency = $i18n->getFormatter('currency');
$currencyCode = $formatterCurrency->getCurrencyCode();
pre($formatterCurrency->format(127, 'symbol', 'gb'));

?>
