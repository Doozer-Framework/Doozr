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
 * get access to model
 */
$i18n = DoozR_Core::module('i18n');


/**
 * get first instance of locale manager with detected locale => should be "de-de"
 */
$i18n_automatic = $i18n->getManagerLocale();

pre($i18n_automatic->getNamespace());

$result_automatic = $i18n_automatic->translate('x_books_in_my_y_shelves', array(4545, 42));
pre($result_automatic);



$i18n_manager_format_automatic = $i18n_automatic->getManagerFormat();
$i18n_string_formatter = $i18n_manager_format_automatic->getStringFormatter();

$result = $i18n_string_formatter->removeBadWords('Ficken Pimmel Fotze Arsch Nutte hallo wie gehts?');

pre($result);

pred('<a href="i18n.php">zur&uuml;ck zu 1</a>');

?>
