<?php

/**
 * include DoozR bootstrapper
 */
require_once '../Framework/Core/DoozR.bootstrap.php';

/**
 * instanciate DoozR
 */
$DoozR = DoozR_Core::getInstance();


/**
 * get access to module I18n
 */
$i18n_1 = DoozR_Core::module('i18n', 'de');


$translator = $i18n_1->getTranslator();

$translator->setNamespace(
    array(
        'header'
    )
);

pre(mb_internal_encoding());
pre($translator->__(utf8_encode('Zählen'), array(20, 2)));

die();


/*
// get number formatter for default locale with it's default namespace
$formatString = $i18n_1->getFormatString();

$badWords = $formatString->removeBadWords('Eine Ficken fotze pimmel hure ist auch nicht besser als Arsch!');

$specialWords = $formatString->highlightSpecialWords('Das WWW ist das tollste!');

pre($badWords);
pre($specialWords);
*/


$formatCurrency = $i18n_1->getFormatCurrency();



//time()
pred($formatCurrency->getCurrencyCode());




die();


$i18n_2 = DoozR_Core::module('i18n', 'de');

pre(
    $i18n_1->config->get('TRANSLATOR')
);



if ($i18n_1 !== $i18n_2) {
    pre('not same same');
} else {
    pre('same same');
}
















die();

/**
 * get first instance of locale manager with detected locale => should be "de-de"
 */
$i18n_automatic = $i18n->getManagerLocale(
    array(
        'namespace_demo',
        'namespace_home'
    )
);


$i18n_manager_format_automatic = $i18n_automatic->getManagerFormat();
$i18n_date_formatter = $i18n_manager_format_automatic->getDateFormatter();
$test = $i18n_date_formatter->shortDate(time());
pre($test);
die();



$i18n_automatic_translator = $i18n_automatic->getManagerTranslation();


pred($i18n_automatic_translator->translate('x_books_in_my_y_shelves', array(4545, 42)));

/**
 * demo getting current namespace
 */
pre($i18n_automatic->getNamespace());


/**
 * get 1. format-manager (the base) and 2. the currency-formatter
 */
$i18n_manager_format_automatic = $i18n_automatic->getManagerFormat();
$i18n_currency_formatter = $i18n_manager_format_automatic->getCurrencyFormatter();

pred($i18n_currency_formatter->getCurrencyCode());


/**
 * test formatting currency for current locale
 */
pre($i18n_currency_formatter->format(0.90, 'symbol', 'ES', 'html'));


die();

pred($i18n_automatic->translate('x_books_in_my_y_shelves', array(4545, 42)));




/**
 * translate a text with placeholder(s) with default translator
 */
$result_automatic = $i18n_automatic->translate('x_books_in_my_y_shelves', array(4545, 42));
pre($result_automatic);

die();

/**
 * get 1. format-manager (the base) and 2. the currency-formatter
 */
$i18n_manager_format_automatic = $i18n_automatic->getManagerFormat();
$i18n_currency_formatter = $i18n_manager_format_automatic->getCurrencyFormatter();


/**
 * test formatting currency for current locale
 */
pre($i18n_currency_formatter->format('4154654654354', true, false, true));


/**
 * get number-formatter
 */
$i18n_number_formatter = $i18n_manager_format_automatic->getNumberFormatter();


/**
 * format percentage value
 */
pre($i18n_number_formatter->percent('99', true));


/**
 * format number
 */
pre($i18n_number_formatter->number('9387654765434565443655464342'));


/**
 * get string-formatter
 */
$i18n_string_formatter = $i18n_manager_format_automatic->getStringFormatter();


/**
 * remove bad-words
 */
$result = $i18n_string_formatter->removeBadWords('Ficken Pimmel Fotze Arsch Nutte hallo wie gehts?');
pre($result);


/**
 * highlight special words
 */
$result = $i18n_string_formatter->highlightSpecialWords('Buffy ist viel im WWW und schrieb ein Kapitel f&uuml;r das N.T.');
pre($result);


/**
 * change namespace to "namespace_demo"
 */
//$i18n_automatic->setNamespace('namespace_demo');


/**
 * demo echo current active namespace
 */
pre($i18n_automatic->getNamespace());


/**
 * now echo the same sentence as above but from another namespace!
 */
$result_automatic = $i18n_automatic->translate('x_books_in_my_y_shelves', array(4545, 42));
pre($result_automatic);


/**
 * get locale-manager "en"
 */
$i18n_en = $i18n->getManagerLocale('en');


/**
 * demonstrate a translation "automatic"
 */
$result_en = $i18n_en->_('x_books_in_my_y_shelves', array(783, 40));


/**
 * get locale-manager "es"
 */
$i18n_es = $i18n->getManagerLocale('es');


/**
 * demonstrate a translation "es"
 */
$result_es = $i18n_es->_('x_books_in_my_y_shelves', array(8374, 324));


/**
 * get locale-manager "ru"
 */
$i18n_ru = $i18n->getManagerLocale('ru');


/**
 * demonstrate a translation "ru"
 */
$result_ru = $i18n_ru->_('x_books_in_my_y_shelves', array(12523, 65));



pre($result_automatic);
pre($result_en);
pre($result_es);
pre($result_ru);
die();



/**
 * echo out the locale of the first (main) locale-manager
 */
pre($i18n_automatic->getLocale());


/**
 * now change to "ar" (+ save in session!)
 */
$i18n_automatic->changeLocale('ar');


/**
 * echo out the locale of the first (main) locale-manager - AFTER changing it to "ar"
 */
pre($i18n_automatic->getLocale());


/**
 * get second instance with custom defined locale "en"
 */
$i18n_second = $i18n->getManagerLocale('en');


/**
 * echo out the locale of the second locale-manager
 */
pre($i18n_second->getLocale());

?>
