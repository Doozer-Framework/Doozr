<?php

/**
 * include DoozR bootstrapper
 */
require_once '../Framework/DoozR/Bootstrap.php';



/**
 * instanciate DoozR
 */
$DoozR = DoozR_Core::getInstance();



$registry = DoozR_Registry::getInstance();

/**
 * get access to module I18n
 */
$configreader = DoozR_Loader_Moduleloader::load('Configreader', array(
        $registry->path,
        $registry->logger,
    	'Ini'
    )
);

/* @var $i18n_1 DoozR_I18n_Module */
$i18n_1 = DoozR_Loader_Moduleloader::load('i18n', array(null, $configreader));

pre(
    $i18n_1->getActiveLocale()
);
//$i18n_1->setActiveLocale('de');


pred($i18n_1);



/**
 * get translator instance from i18n-Module
 */
$translator = $i18n_1->getTranslator();

/**
 * set namespace of source where we get the translations from
 */
$translator->setNamespace(
    array(
        'header'
    )
);


/**
 * DEMO 1
 *
 * I18n - preferred locale autodetect from client
 *
 * 1. manually get Detector-Instance from DoozR-I18n-Module
 * 2. call detect() to detect data
 * 3. show retrieved information about locale, language and country
 */
$detector = $i18n_1->getDetector();
$detector->detect();

// get list of all available locales (in weight order) from client
$locales = $detector->getLocales();

// get a collection of the 3 preferences
$localesPreferences = $detector->getLocalePreferences();

// get 1st locale in order by weight
$locale = $detector->getLocale();

// get weight of first locale
$weight = $detector->getWeight();

// get country of locale
$country = $detector->getCountry();

// get language
$language = $detector->getLanguage();

// show locales
pre($locales);

// show locale preferences
pre($localesPreferences);

// show preferred locale
pre($locale);

// show preferred locale's weight
pre($weight);

// show preferred locale's country
pre($country);

// show preferred locale's language
pre($language);

?>
