<?php
/*----------------------------------------------------------------------------------------------------------------------
| Demonstration: I18n-Service
+---------------------------------------------------------------------------------------------------------------------*/

// Bootstrap the Framework Environment
require_once '../Framework/DoozR/Bootstrap.php';

// Init (instanciate) DoozR
$DoozR = DoozR_Core::getInstance();

// Get registry containing DoozR's base object instances
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
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>DoozR | I18n Demonstration</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">

    <link rel="stylesheet" href="/www/view/assets/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 50px;
            padding-bottom: 20px;
        }
    </style>
    <link rel="stylesheet" href="/www/view/assets/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="/www/view/assets/css/main.css">

    <!--[if lt IE 9]>
    <script src="js/vendor/html5-3.6-respond-1.1.0.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">DoozR</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul id="main-menu" class="nav navbar-nav">
            </ul>
        </div><!--/.navbar-collapse -->
    </div>
</div>

<!-- Main jumbotron for a primary marketing message or call to action -->
<div class="jumbotron">
    <div class="container">
        <h1><b>I18n</b> Demonstration</h1>
        <p>This is the demonstration of the DoozR I18n service. It shows you some short examples on translation (translate from one language to another) and localization (e.g. convert currencies to correct local format). Enjoy!</p>
        <p>
            <!--button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
                Try REST-API /User/1234 &raquo;
            </button-->
        </p>
    </div>
</div>


<div class="panel panel-success">
    <div class="panel-heading">
        <h3 class="panel-title">Important notice!</h3>
    </div>
    <div class="panel-body">
        This is only a collection of demonstrations of the service. You will find more details in the service' Readme.md in service root folder.
    </div>
</div>

<div class="well">
    <div class="container">
        <h2>Load service</h2>
        <p>The following code will load the I18n-Service:</p>
        <dl class="dl-horizontal">
            <dt>Code</dt>
            <dd><code>$i18n = DoozR_Loader_Serviceloader::load('i18n', ...);</code></dd>
            <dt>Result</dt>
            <dd><code>Will return an instance of DoozR I18n-Service</code></dd>
        </dl>
    </div>
</div>

<?php
/*----------------------------------------------------------------------------------------------------------------------
| I18n please tell me which locale is prefered by the current client?
+---------------------------------------------------------------------------------------------------------------------*/
$locale = $i18n->getClientPreferedLocale();
#echo '<p>Clients detected prefered locale is: "'.$locale.'"</p>';
?>
<div class="container">
    <h2>Detect clients prefered locale</h2>
    <p>The following code will detect the clients prefered locale and return it as string:</p>
    <dl class="dl-horizontal">
        <dt>Code</dt>
        <dd><code>$locale = $i18n->getClientPreferedLocale();</code></dd>
        <dt>Result</dt>
        <dd><code><?=$locale;?></code></dd>
    </dl>
</div>

<div class="well">
    <?php
    /*----------------------------------------------------------------------------------------------------------------------
    | I18n please tell me which translations are available?
    +---------------------------------------------------------------------------------------------------------------------*/
    $locales = $i18n->getAvailableLocales();
    #echo '<p>Available locales: "'.var_export($translations, true).'"</p>';
    ?>
    <div class="container">
        <h2>Get available locales</h2>
        <p>The following code will return an array containing all configured locales:</p>
        <dl class="dl-horizontal">
            <dt>Code</dt>
            <dd><code>$locales = $i18n->getAvailableLocales();</code></dd>
            <dt>Result</dt>
            <dd><code><?=var_export($locales, true);?></code></dd>
        </dl>
    </div>
</div>

<?php
/*----------------------------------------------------------------------------------------------------------------------
| I18n please take this as collection of available translations!
+---------------------------------------------------------------------------------------------------------------------*/
$locales = $i18n->setAvailableLocales($locales);
#echo '<p>Set available locales: "'.var_export($locales, true).'"</p>';
?>
<div class="container">
    <h2>Set available locales</h2>
    <p>The following code will store the passed locales as available:</p>
    <dl class="dl-horizontal">
        <dt>Code</dt>
        <dd><code>$locales = $i18n->setAvailableLocales($locales);</code></dd>
        <dt>Result</dt>
        <dd><code><?=var_export($locales, true);?></code></dd>
    </dl>
</div>

<div class="well">
    <?php
    /*----------------------------------------------------------------------------------------------------------------------
    | I18n please tell me which locale is currently active?
    +---------------------------------------------------------------------------------------------------------------------*/
    $locale = $i18n->getActiveLocale();
    #echo '<p>Get active locale: "'.var_export($activeLocale, true).'"</p>';
    ?>
    <div class="container">
        <h2>Get active locale</h2>
        <p>The following code return the current active locale from Service:</p>
        <dl class="dl-horizontal">
            <dt>Code</dt>
            <dd><code>$locale = $i18n->getActiveLocale();</code></dd>
            <dt>Result</dt>
            <dd><code><?=var_export($locale, true);?></code></dd>
        </dl>
    </div>
</div>

<?php
/*----------------------------------------------------------------------------------------------------------------------
| I18n please take this as active locale!
+---------------------------------------------------------------------------------------------------------------------*/

$locale = $i18n->setActiveLocale($locale);
#echo '<p>Set active locale: "'.var_export($activeLocale, true).'"</p>';
?>
<div class="container">
    <h2>Set active locale</h2>
    <p>The following code sets the passed locale active:</p>
    <dl class="dl-horizontal">
        <dt>Code</dt>
        <dd><code>$locale = $i18n->setActiveLocale($locale);</code></dd>
        <dt>Result</dt>
        <dd><code><?=var_export($locale, true);?></code></dd>
    </dl>
</div>

<div class="well">
    <?php
    /*----------------------------------------------------------------------------------------------------------------------
    | I18n please tell me the active encoding?
    +---------------------------------------------------------------------------------------------------------------------*/
    $encoding = $i18n->getEncoding();
    #echo '<p>Get active encoding: "'.var_export($encoding, true).'"</p>';
    ?>
    <div class="container">
        <h2>Get encoding</h2>
        <p>The following code returns the encoding (e.g. "UTF-8") used:</p>
        <dl class="dl-horizontal">
            <dt>Code</dt>
            <dd><code>$encoding = $i18n->getEncoding();</code></dd>
            <dt>Result</dt>
            <dd><code><?=var_export($encoding, true);?></code></dd>
        </dl>
    </div>
</div>

<?php
/*----------------------------------------------------------------------------------------------------------------------
| I18n please take this as active encoding!
+---------------------------------------------------------------------------------------------------------------------*/
$encoding = $i18n->setEncoding($encoding);
#echo '<p>Set encoding -> result: "'.var_export($encoding, true).'"</p>';
?>
<div class="container">
    <h2>Set encoding</h2>
    <p>The following code sets the encoding (e.g. "UTF-8"):</p>
    <dl class="dl-horizontal">
        <dt>Code</dt>
        <dd><code>$encoding = $i18n->setEncoding($encoding);</code></dd>
        <dt>Result</dt>
        <dd><code><?=var_export($encoding, true);?></code></dd>
    </dl>
</div>

<div class="well">
    <?php
    /*----------------------------------------------------------------------------------------------------------------------
    | I18n give me the capability to translate to auto locale
    +---------------------------------------------------------------------------------------------------------------------*/
    $translator = $i18n->getTranslator();
    $translator->setNamespace('default');
    $translated = $translator->_('Yes');
    ?>
    <div class="container">
        <h2>Translate a string</h2>
        <p>The following code translates a string:</p>
        <dl class="dl-horizontal">
            <dt>Code</dt>
            <dd><code>$translated = $translator->_('Yes');</code></dd>
            <dt>Result</dt>
            <dd><code><?=var_export($translated, true);?></code></dd>
        </dl>
    </div>
</div>

<?php
/*----------------------------------------------------------------------------------------------------------------------
| I18n give me the capability to translate to auto locale
+---------------------------------------------------------------------------------------------------------------------*/
$translator = $i18n->getTranslator();
$translator->setNamespace('default');
$randomNumberBooks = rand(1, 100);
$randomNumberShelves = rand(1, 10);
$translated = $translator->_('x_books_in_my_y_shelves', array($randomNumberBooks, $randomNumberShelves));
#echo '<p>Translate x_books_in_my_y_shelves('.$randomNumberBooks.', '.$randomNumberShelves.') to "'.$activeLocale.'": "'.
#    $translator->_('x_books_in_my_y_shelves', array($randomNumberBooks, $randomNumberShelves)).'"</p>';
?>
<div class="container">
    <h2>Translate a string with arguments</h2>
    <p>The following code translates a string and passes arguments to translation:</p>
    <dl class="dl-horizontal">
        <dt>Code</dt>
        <dd><code>$translated = $translator->_('x_books_in_my_y_shelves', array(<?=$randomNumberBooks;?>, <?=$randomNumberShelves;?>));</code></dd>
        <dt>Result</dt>
        <dd><code><?=var_export($translated, true);?></code></dd>
    </dl>
</div>

<div class="well">
    <?php
    /*----------------------------------------------------------------------------------------------------------------------
    | I18n give me the capability to translate to auto locale
    +---------------------------------------------------------------------------------------------------------------------*/
    $locale = 'es';
    $translator = $i18n->getTranslator($locale);
    $translator->setNamespace('default');
    $translated = $translator->_('x_books_in_my_y_shelves', array($randomNumberBooks, $randomNumberShelves));

    /* @var $translator DoozR_I18n_Service_Translator */
    var_dump($translated);
    die;


    ?>
    <div class="container">
        <h2>Translate a string with arguments to locale "<?=$locale;?>"</h2>
        <p>The following code translates a string to "<?=$locale;?>" and passes arguments to translation:</p>
        <dl class="dl-horizontal">
            <dt>Code</dt>
            <dd><code>$translated = $translator->_('x_books_in_my_y_shelves', array(<?=$randomNumberBooks;?>, <?=$randomNumberShelves;?>));</code></dd>
            <dt>Result</dt>
            <dd><code><?=var_export($translated, true);?></code></dd>
        </dl>
    </div>
</div>

<?php
/*----------------------------------------------------------------------------------------------------------------------
| I18n give me a localizer
+---------------------------------------------------------------------------------------------------------------------*/
$datetime = $i18n->getLocalizer(DoozR_I18n_Service::FORMAT_DATETIME, $locale);
?>
<div class="container">
    <h2>Load a localizer for locale "<?=$locale;?>"</h2>
    <p>The following code loads a localizer which can localize currencies, numbers, date & time-values ...:</p>
    <dl class="dl-horizontal">
        <dt>Code</dt>
        <dd><code>$datetime = $i18n->getLocalizer(DoozR_I18n_Service::FORMAT_DATETIME, $locale);</code></dd>
        <dt>Result</dt>
        <dd><code>Will return an instance of DoozR_I18n_Datetime</code></dd>
    </dl>
</div>

<div class="well">
    <?php
    /*----------------------------------------------------------------------------------------------------------------------
    | I18n give me the capability to localize time and date values
    +---------------------------------------------------------------------------------------------------------------------*/
    #$datetime = $i18n->getLocalizer(DoozR_I18n_Service::FORMAT_DATETIME, $locale);
    $shorttime = $datetime->shortTime(time());
    ?>
    <div class="container">
        <h2>Localize a short time to locale "<?=$locale;?>"</h2>
        <p>The following code localizes a short-time to "<?=$locale;?>":</p>
        <dl class="dl-horizontal">
            <dt>Code</dt>
            <dd><code>$shorttime = $datetime->shortTime(time());</code></dd>
            <dt>Result</dt>
            <dd><code><?=var_export($shorttime, true);?></code></dd>
        </dl>
    </div>
</div>

<?php
/*----------------------------------------------------------------------------------------------------------------------
| I18n give me the capability to localize currency values
+---------------------------------------------------------------------------------------------------------------------*/
/* @var DoozR_I18n_Service_Localize_Currency $currency */
$currency = $i18n->getLocalizer(DoozR_I18n_Service::FORMAT_CURRENCY, 'en-gb');
// some dollar or euro
$value = 127000;
$amount = $currency->format($value, DoozR_I18n_Service_Localize_Currency::NOTATION_SYMBOL);
#echo '<p>Localizeted currency: "'.$value.'"</p>';
?>
<div class="container">
    <h2>Localize a value of "<?=$value;?>" to an amount (currency) with locale "en-gb"</h2>
    <p>The following code localizes a short-time to "<?=$locale;?>":</p>
    <dl class="dl-horizontal">
        <dt>Code</dt>
        <dd><code>$amount = $currency->format($amount, DoozR_I18n_Service_Localize_Currency::NOTATION_SYMBOL);</code></dd>
        <dt>Result</dt>
        <dd><code><?=var_export($amount, true);?></code></dd>
    </dl>
</div>

<div class="well">
    <?php
    /*----------------------------------------------------------------------------------------------------------------------
    | I18n give me the capability to filter bad words from string
    +---------------------------------------------------------------------------------------------------------------------*/
    /* @var DoozR_I18n_Service_Localize_String $string */
    $string = $i18n->getLocalizer(DoozR_I18n_Service::FORMAT_STRING, 'de');
    // some bad words in blog post or something like that:
    $post = 'Du bist ein Arsch!';
    $post = $string->removeBadWords($post);
    ?>
    <div class="container">
        <h2>Filter bad words out of Blog-Post "de"</h2>
        <p>The following code filters out bad words from a blog post for example and replace it with "*":</p>
        <dl class="dl-horizontal">
            <dt>Code</dt>
            <dd><code>$post = $string->removeBadWords('Du bist ein Arsch!');</code></dd>
            <dt>Result</dt>
            <dd><code><?=var_export($post, true);?></code></dd>
        </dl>
    </div>
</div>

<?php
/*----------------------------------------------------------------------------------------------------------------------
| I18n give me the capability to highlight special words
+---------------------------------------------------------------------------------------------------------------------*/
$string = $i18n->getLocalizer(DoozR_I18n_Service::FORMAT_STRING, 'de');
$post = 'The www is an amazing innovation!';
$post = $string->highlightSpecialWords($post);
?>
<div class="container">
    <h2>Highlight special words & add translation to abbr</h2>
    <p>The following code highlights predefined special words and adds a translated description:</p>
    <dl class="dl-horizontal">
        <dt>Code</dt>
        <dd><code>$post = $string->highlightSpecialWords('The www is an amazing innovation!');</code></dd>
        <dt>Result</dt>
        <dd><code><?=var_export($post, true);?></code></dd>
    </dl>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="/www/view/assets/js/vendor/jquery-1.10.1.min.js"><\/script>')</script>
<script src="/www/view/assets/js/vendor/bootstrap.min.js"></script>
<script src="/www/view/assets/js/main.js"></script>

</body>
</html>
