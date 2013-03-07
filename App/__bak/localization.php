<?php

header("content-type: text/html;charset=UTF-8 \r\n");
mb_internal_encoding('UTF-8');
mb_language('uni');

/**
 * include DoozR bootstrapper
 */
require_once '../Framework/Core/DoozR.bootstrap.php';

/**
 * instanciate DoozR
 */
$DoozR = DoozR_Core::getInstance();

/**
 * get module localization (i18n)
 */
$language = DoozR_Core::module('localization', 'en');



function start_timer($event) {
    //printf("timer: %s<br>\n", $event);
    list($low, $high) = explode(' ', microtime());
    $t = $high + $low;
    flush();
    return $t;
}

function next_timer($start, $event) {
    list($low, $high) = explode(' ', microtime());
    $t    = $high + $low;
    $used = $t - $start;
    sumTimer($used, false);
    printf('timer (%s): %8.4f', $event, $used);
    //printf("Page generated in %s %8.4f seconds", $event, $used);
    flush();
    return $t;
}

function sumTimer($time = 0, $show = false) {
    static $sum = 0;
    if($show == true) {
        printf('timer (sum): %8.4f', $sum);
    } else {
        $sum += $time;
    }
}


$t = start_timer("start Befehl 1");


/**
 * get locale instance
 */
$locale = $language->getLocale('en');

/**
 * get translator instance
 */
$english_translator = $language->getTranslator('', $locale);


$i18n_user = $english_translator->getI18Nuser();


if (!is_null($_POST->locale())) {
    $i18n_user->setPrefLocale($_POST->locale());
} elseif (!is_null($_POST->timeformat())) {
    $i18n_user->setPrefTimeFormat($_POST->timeformat());
} elseif (!is_null($_POST->measure())) {
    $i18n_user->setPrefMeasureSystem($_POST->measure());
} // end if


//$translator = new I18Ntranslator();
$translator = $language->getTranslator();
//$measure = new I18Nmeasure('si');
$measure = $language->getMeasure('si');
//$format_date = new I18NformatDate();
$format_date = $language->getFormatDate();
//$format_number = new I18NformatNumber();
$format_number = $language->getFormatNumber();
//$format_string = new I18NformatString();
$format_string = $language->getFormatString();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <head>
        <title>i18n example-script</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <style type="text/css">
        <!--
        /*@import url(css.css);*/

body {
    background-color:#fff;
    font: 0.9em Verdana, Arial, Helvetica, sans-serif;
    padding: 10px;
}

h1, h2 {
    background-color: #339;
    color: #fff;
    margin: 0px;
    text-indent: 7px;
}

h1 {

    border: 3px solid #006;
    font-size: 1.6em;
    padding: 4px;
    letter-spacing: 0.2em;
    margin-bottom: 2px;
}

h2 {

    border: 2px solid #006;
    font-size: 1.1em;
    padding: 3px;
    letter-spacing: 0.1em;
    font-variant: small-caps;
    margin-top: 30px;
}

h3 {
    margin: 3px 0px 10px 10px;
    font-size: 1.05em;

}

h1 a {
    color: #fff;
}


.sample {
    background-color: orange;
    margin: 5px 40px;
    padding: 5px;
}

samp {
    font: 0.9em Verdana, Arial, Helvetica, sans-serif;
    font-style: italic;
    background-color: #fc6;
    padding: 1px 4px;
    border-bottom: 1px solid #fc9;
    border-right: 1px solid #fc9;
    border-top: 1px solid #c60;
    border-left: 1px solid #c60;
}

.timer, #sum {
    text-align: right;
    background-color: #eee;
    font-size: 0.9em;
    padding: 2px;
    color: #999;
    margin: 0px;
}

.timer {
    margin-top: 20px;
}

form, #sum, .timer {
    margin-left: 10px;
}

#sum {
    font-weight: bold;
    border-top: 1px solid #ccc;
}

dfn {
    color: red;
}
acronym {
    color: lightgreen;
}

#menu {
    list-style-type: none;
    padding: 0px;
    margin: 0px;
    display: inline;
    text-align: right;
}

#menu li {
    background-color: #006;
    display: inline;
    margin: 0px;
    padding: 5px;
    list-style-type: none;
    font-size: 0.6em;
}

#menu li a {
    color: #fff;
    margin: 0px;
    padding: 0px;
}

        -->
        </style>
    </head>
    <body>
        <h1><a href="https://sourceforge.net/projects/php-flp/">FLP &ndash; i18n</a> <small>(V2.3)</small></h1>
            <ul id="menu">
                <li><a href="#translator">Translator</a></li>
                <li><a href="#date">Date</a></li>
                <li><a href="#number">Number</a></li>
                <li><a href="#measure">Measure</a></li>
                <li><a href="#string">String</a></li>
            </ul>
            <p class="timer"><?php $t = next_timer($t, 'generating objects'); ?></p>
            <h2 id="translator">Translator <small>(<?php echo $translator->getI18NSetting('mode'); ?>)</small></h2>
                <h3>(current locale used: <?php echo $english_translator->_($translator->getTranslatorLocale()->getI18Nlocale()); ?>)</h3>
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>#translator" name="select_locale" id="select_locale">
                        <select name="locale" id="locale">
                        <?php
                        $possible_locales = array_keys($translator->getLocales());
                        $current_locale = $translator->getTranslatorLocale();
                        foreach ($possible_locales as $code) {
                            $translator->changeLocale(new I18Nlocale($code));
                            $selected = ($code == $current_locale->getI18Nlocale()) ? ' selected="selected"' : '';
                            echo '<option value="' , $code , '"' , $selected , '>' , $translator->_($code) , '</option>';
                            //echo '<option value="' , $code , '"' , $selected , '>' , mb_detect_encoding($translator->_($code)) , '</option>';

                        } // end foreach
                        $translator->changeLocale($current_locale);
                        ?>
                        </select>
                        <input type="submit" name="Submit" value="Change language" />
                    </form>
                    <p class="sample" id="translator_sample">
                        Translating &raquo;no_records_found&laquo;: <samp><?php echo $translator->_('no_records_found'); ?></samp>
                    </p>
                    <p class="timer"><?php $t = next_timer($t, 'translating'); ?></p>
            <h2 id="date">Date</h2>
                <h3>(current time format used: <?php echo $format_date->getTimeFormat(); ?>)</h3>
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>#date" name="select_timeformat" id="select_timeformat">
                        <select name="timeformat" id="timeformat">
                        <?php
                        foreach ($format_date->getPossibleTimeFormats() as $id => $name) {
                            $selected = ($format_date->getTimeFormat() == $name) ? ' selected="selected"' : '';
                            echo '<option value="' , $id , '"' , $selected , '>' , $name , '</option>';
                        } // end foreach
                        ?>
                        </select>
                        <input type="submit" name="Submit" value="Change time format" />
                    </form>
                    <?php
                    $date = '2004-12-31 15:30:00';
                    $timestamp = $format_date->ISOdatetimeToUnixtimestamp($date);
                    ?>
                    <p class="sample" id="formatdate_sample">
                        Formating &raquo;<?php echo $date; ?>&laquo; <small>(long)</small>: <samp><?php echo $format_date->longDateTime($timestamp); ?></samp>
                    </p>
                    <p class="sample" id="formatdate_sample">
                        Formating &raquo;<?php echo $date; ?>&laquo; <small>(middle)</small>: <samp><?php echo $format_date->middleDateTime($timestamp); ?></samp>
                    </p>
                    <p class="sample" id="formatdate_sample">
                        Formating &raquo;<?php echo $date; ?>&laquo; <small>(short)</small>: <samp><?php echo $format_date->shortDateTime($timestamp); ?></samp>
                    </p>
                    <p class="timer"><?php $t = next_timer($t, 'formating dates'); ?></p>
            <h2 id="number">Number</h2>
                <?php
                $number = 12345.678;
                $float = 0.567;
                $money = 0.56;
                ?>
                <p class="sample" id="formatnumber_sample">
                    Formating &raquo;<?php echo $number; ?>&laquo;: <samp><?php echo $format_number->number($number); ?></samp>
                </p>
                <p class="sample" id="formatnumber_sample">
                    Formating &raquo;<?php echo $float; ?>&laquo; <small>(percent)</small>: <samp><?php echo $format_number->percent($float); ?>%</samp>
                </p>
                <p class="sample" id="formatnumber_sample">
                    Formating &raquo;<?php echo $money; ?>&laquo; <small>(currency big)</small>: <samp><?php echo $format_number->currency($money, 'full', 'gb', TRUE); ?></samp>
                </p>
                <p class="sample" id="formatnumber_sample">
                    Formating &raquo;<?php echo $money; ?>&laquo; <small>(currency small)</small>: <samp><?php echo $format_number->currency($money, 'full', 'gb', FALSE); ?></samp>
                </p>
                <p class="sample" id="formatnumber_sample">
                    Formating &raquo;<?php echo $money; ?>&laquo; <small>(currency symbol left)</small>: <samp><?php echo $format_number->currency($money, 'symbol', 'gb', TRUE, 'before'); ?></samp>
                </p>
                <p class="sample" id="formatnumber_sample">
                    Formating &raquo;<?php echo $money; ?>&laquo; <small>(currency symbol right)</small>: <samp><?php echo $format_number->currency($money, 'symbol', 'gb', TRUE, 'after'); ?></samp>
                </p>
                <p class="timer"><?php $t = next_timer($t, 'formating numers'); ?></p>
            <h2 id="measure">Measure</h2>
                <h3>(current measure output format used: <?php echo $measure->getOutput(); ?>)</h3>
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>#measure" name="select_measure" id="select_measure">
                        <select name="measure" id="measure2">
                        <?php
                        $possible_formats = $measure->getFormats();
                        foreach ($possible_formats as $format) {
                            $translator->changeLocale(new I18Nlocale($code));
                            $selected = ($format == $measure->getOutput()) ? ' selected="selected"' : '';
                            echo '<option value="' , $format , '"' , $selected , '>' , $format , '</option>';
                        } // end foreach
                        ?>
                        </select>
                        <input type="submit" name="Submit" value="Change measure output format" />
                    </form>
                <?php
                $number = 30000;
                ?>
                <p class="sample" id="formatmeasure_sample">
                    Formating &raquo;<?php echo $number; ?> mm&laquo;: <samp><?php echo $measure->linear($number, 0, 0) , ' ' ,  $measure->Unit(2); ?></samp>
                </p>
                <p class="timer"><?php $t = next_timer($t, 'formating measure'); ?></p>
            <h2 id="string">String</h2>
            <?php
            $string_1 = 'I know a lot of Buffy fanpages on the WWW.';
            $string_2 = 'Most of them have Fanfiction stories you can download, but nearly all of them are bullshit.';
            ?>
            <p class="sample" id="formatstring_sample">
                Stripping bad words: <samp><?php echo $format_string->wordFilter($string_2, TRUE); ?></samp>
            </p>
            <p class="sample" id="formatstring_sample">
                Highlighting special words: <samp><?php echo $format_string->highlightSpecialWords($string_1); ?></samp>
            </p>
            <p class="timer"><?php $t = next_timer($t, 'formating strings'); ?></p>
            <p id="sum"><?php $t = sumTimer(0,true); ?></p>
    </body>
</html>
