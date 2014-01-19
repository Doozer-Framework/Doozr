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
 * Instanciate DoozR
 */
$DoozR = DoozR_Core::getInstance();


/**
 * Get registry containing DoozR's base object instances
 */
$registry = DoozR_Registry::getInstance();


/**
 * get I18n Service of DoozR -> for translations, converting and formatting values ...
 *
 * null = NO_LOCALE
 * true = USE_AUTOMAGIC_AUTOLOADING
 *
 * @var DoozR_I18n_Service $i18n
 */
$i18n = DoozR_Loader_Serviceloader::load('i18n', $registry->config);


/*----------------------------------------------------------------------------------------------------------------------
| I18n please tell me which locale is prefered by the current client?
+---------------------------------------------------------------------------------------------------------------------*/
$locale = $i18n->getClientPreferedLocale();                                         // i would like something like: i18n->client->preferedLocale()
echo '<p>Clients detected prefered locale is: "'.$locale.'"</p>';

/*----------------------------------------------------------------------------------------------------------------------
| I18n please tell me which translations are available?
+---------------------------------------------------------------------------------------------------------------------*/
$translations = $i18n->getAvailableLocales();
echo '<p>Available locales: "'.var_export($translations, true).'"</p>';

/*----------------------------------------------------------------------------------------------------------------------
| I18n please take this as collection of available translations!
+---------------------------------------------------------------------------------------------------------------------*/
$translations = $i18n->setAvailableLocales($translations);
echo '<p>Set available locales: "'.var_export($translations, true).'"</p>';

/*----------------------------------------------------------------------------------------------------------------------
| I18n please tell me which locale is currently active?
+---------------------------------------------------------------------------------------------------------------------*/
$activeLocale = $i18n->getActiveLocale();
echo '<p>Get active locale: "'.var_export($activeLocale, true).'"</p>';

/*----------------------------------------------------------------------------------------------------------------------
| I18n please take this as active locale!
+---------------------------------------------------------------------------------------------------------------------*/
$activeLocale = $i18n->setActiveLocale($activeLocale);
echo '<p>Set active locale: "'.var_export($activeLocale, true).'"</p>';

/*----------------------------------------------------------------------------------------------------------------------
| I18n please tell me the active encoding?
+---------------------------------------------------------------------------------------------------------------------*/
$encoding = $i18n->getEncoding();
echo '<p>Get active encoding: "'.var_export($encoding, true).'"</p>';

/*----------------------------------------------------------------------------------------------------------------------
| I18n please take this as active encoding!
+---------------------------------------------------------------------------------------------------------------------*/
$encoding = $i18n->setEncoding($encoding);
echo '<p>Set encoding -> result: "'.var_export($encoding, true).'"</p>';


die;

/*----------------------------------------------------------------------------------------------------------------------
| I18n give me the capability to translate to auto locale
+---------------------------------------------------------------------------------------------------------------------*/
$translator = $i18n->getTranslator();
$translator->setNamespace('default');

/**
 * Some random dummy data
 */
$randomNumberBooks   = rand(1, 100);
$randomNumberShelves = rand(1, 10);

echo '<p>Translate x_books_in_my_y_shelves('.$randomNumberBooks.', '.$randomNumberShelves.') to "'.$activeLocale.'": "'.
      $translator->_('x_books_in_my_y_shelves', array($randomNumberBooks, $randomNumberShelves)).'"</p>';

/*----------------------------------------------------------------------------------------------------------------------
| I18n give me the capability to translate to a custom (en-gb) locale
+---------------------------------------------------------------------------------------------------------------------*/
$translator = $i18n->getTranslator('en-gb');
$translator->setNamespace('default');

echo '<p>Translate x_books_in_my_y_shelves('.$randomNumberBooks.', '.$randomNumberShelves.') to "en-gb": "'.
    $translator->_('x_books_in_my_y_shelves', array($randomNumberBooks, $randomNumberShelves)).'"</p>';

/*----------------------------------------------------------------------------------------------------------------------
| I18n give me the capability to localize time and date values
+---------------------------------------------------------------------------------------------------------------------*/
/* @var DoozR_I18n_Service_Format_Currency $datetime */
$datetime = $i18n->getFormatter(DoozR_I18n_Service::FORMAT_DATETIME, 'en-gb');
$shorttime = $datetime->shortTime(time());
$dayname   = $datetime->dayname(time());

echo '<p>Formatted shorttime: "'.$shorttime.'" and dayname: "'.$dayname.'"</p>';

/*----------------------------------------------------------------------------------------------------------------------
| I18n give me the capability to localize currency values
+---------------------------------------------------------------------------------------------------------------------*/
/* @var DoozR_I18n_Service_Format_Currency $currency */
$currency = $i18n->getFormatter(DoozR_I18n_Service::FORMAT_CURRENCY, 'en-gb');

// some dollar or euro
$amount = 127000;
$amount = $currency->format($amount, DoozR_I18n_Service_Format_Currency::NOTATION_SYMBOL);

echo '<p>Formatted currency: "'.$amount.'"</p>';

/*----------------------------------------------------------------------------------------------------------------------
| I18n give me the capability to filter bad words from string
+---------------------------------------------------------------------------------------------------------------------*/
/* @var DoozR_I18n_Service_Format_String $string */
$string = $i18n->getFormatter(DoozR_I18n_Service::FORMAT_STRING, 'en-gb');

// some bad words in blog post or something like that:
$post = 'You fucking bastard fuck you foo!';
$post = $string->removeBadWords($post);

echo '<p>Post with filtered badwords: "'.$post.'"</p>';

/*----------------------------------------------------------------------------------------------------------------------
| I18n give me the capability to highlight special words
+---------------------------------------------------------------------------------------------------------------------*/
/* @var DoozR_I18n_Service_Format_String $string */
$string = $i18n->getFormatter(DoozR_I18n_Service::FORMAT_STRING, 'en-gb');

// some bad words in blog post or something like that:
$post = 'The www is an amazing innovation! But buffy does not have invented it. Do you know c.a.d.';
$post = $string->highlightSpecialWords($post);

echo '<p>Post with highlighted special words: "'.$post.'"</p>';


die;


/**
 * Demonstrate translate a string
 * Translate the string "x_books_in_my_y_shelves" to detected locale and echo out
 *
 * Demonstrate - translator
 */
$translator = $i18n->getTranslator('en-gb');
//$translator = $i18n->getTranslator();
$translator->setNamespace('default');
$translated = $translator->_('x_books_in_my_y_shelves', array(921, 8));
pre($translated);


/**
 * Demonstrate some formatter stuff:
 * available formatter: datetime, currency, measure, number, string
 *
 * Demonstrate - formatter: Datetime
 */
$formatterDatetime = $i18n->getFormatter('datetime');
$time = $formatterDatetime->shortTime(time());
pre($time);
$day = $formatterDatetime->dayName(time());
pre($day);


/**
 * Demonstrate - formatter: Currency
 */
$formatterCurrency = $i18n->getFormatter('currency');
$currencyCode = $formatterCurrency->getCurrencyCode();
pre($currencyCode);
pre($formatterCurrency->format(127, 'symbol', 'gb'));
