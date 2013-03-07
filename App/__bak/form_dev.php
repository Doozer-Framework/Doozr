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
 * get module form
 */
$form = DoozR_Core::module('form');


// temporary currently unused => but this is the way the form get generated in near future!
// give the user a good interface - one way is a meta-language (abstraction) like this array
// another way is the current used way of combined method calls step by step
$config = array(
    'form' => array(
        'name'    => 'contact',
        'id'      => 'contact',
        'method'  => 'post',
        'action'  => $_SERVER['PHP_SELF'],
        'success' => 'http://www.google.de/',
        'error'   => 'http://www.test.de',
        'elements' => array(
            // add elements here
        )
    )
);


// check if form submitted and valid
if ($form->submitted('contact') && $form->valid()) {
    pred('redirect');

} else {
    //$html = $form->render('contact', array('form' => $config));

    // create the form
    $form->create('contact'); //, 'Demo_Form_Contact'

    // set method POST | GET
    $form->method('post');

    // set action (processing script)
    $form->action($_SERVER['PHP_SELF']);

    /**
     * what's to do on invalid token
     * this can be either:
     * IGNORE       => DO NOTHING
     * DENY         => CANCEL REQUEST AND SEND 404
     * INVALIDATE   => MARK FORM AS INVALID!
     */
    $form->onInvalidToken(DoozR_Form::TOKEN_BEHAVIOR_IGNORE);

    // demo for custom attribute
    $form->setAttribute('onsubmit', 'javascript:alert(\'about to submit\'); return true;');

    // set a new block begin
    $form->setFieldsetBegin('fieldset1', 'Userdata', 'myclass');

        // TEXT
        $element = $form->addElement('text', true);

        // set its name
        $element->name('myinputtext');

        // set label
        $element->label('Username', 'left');

        // set its id
        $element->id('myinputtext');

        // set it default value
        $element->value('my_username');

        // set required
        $element->required(true);

        // add validation(s)
        //$element->addValidation('alphabetic');
        //$element->addValidation('notnull');
        //$element->addValidation('notempty');
        //$element->addValidation('minlength', 2);
        //$element->addValidation('ustid', 'DE');
        //$element->addValidation('empty');
        //$element->addValidation('boolean');
        $element->addValidation('double');
        //$element->addValidation('integer');

        // set max length attrib (this also set the validation to "maxlength" => n)
        //$element->maxlength(15);

        // CHECKBOX
        $element = $form->addElement('checkbox', true);

        // set its name
        $element->name('myinputcheckbox');

        // set label
        $element->label('Allow', 'left');

        // set its id
        $element->id('myinputcheckbox');

        // set it default value
        $element->value('allow');

        // set required
        $element->required(true);

    // end of block
    $form->setFieldsetEnd();


    // set a new block begin
    $form->setFieldsetBegin('radiofieldset', 'Choose your destiny');

        // SELECT
        $element = $form->addElement('select');

        // set its name
        $element->name('myselect');

        // set required
        $element->required(true);

        // set its id
        $element->id('myselect');

        // set its default value
        $element->addOption('Germany', '1');

        // set its default value
        $element->addOption('Austria', '2');

        // set its default value
        $reference = $element->addOption('Lichtenstein', '3');

        // set its default value
        $element->addOption('Switzerland', '4');

        // set active value
        $element->active('2');

        // set label
        $element->label('Your country:', 'left');

    // end of block
    $form->setFieldsetEnd();


    // set a new block begin
    $form->setFieldsetBegin('fieldset6');

        // SUBMIT
        $element = $form->addButton('submit');

        // set its name
        $element->name('mysubmit');

        // set its id
        $element->id('mysubmit');

        // set its class
        $element->cssClass('cssKlasse');

        // set its default value
        $element->value('submit');


        // RESET
        $element = $form->addButton('reset');

        // set its name
        $element->name('myreset');

        // set its id
        $element->id('myreset');

        // set its class
        $element->cssClass('cssKlasse');

        // set its default value
        $element->value('reset');

    // end of block
    $form->setFieldsetEnd();


    // render the form to a var
    $formhtml = $form->render('contact');
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <title>Wikipedia - Die freie Enzyklop&auml;die</title>
</head>

<style>

body {
    font-family: Helvetica;
    font-size: 14px;
    margin-top: 22px;
    margin-left: 22px;
    color: #999;
}

h1, h2, h3, h4, h5, h6 {
    font-family: Georgia, Palatino, Palatino Linotype, Times, Times New Roman, serif;
    margin: 0;
    padding: 0;
    margin-bottom: 22px;
}

h1 {
    font-size: 20px;
}

input[type=text] {
    border-top: 1px solid #999;
    border-right: 1px solid #999;
    border-bottom: 1px solid #666;
    border-left: 1px solid #666;
    height: 38px;
    width: 376px;
    line-height: 24px;
    font-size: 14px;
    color: #333;
    font-family: Helvetica;
    margin-left: 142px;
    -moz-border-radius: 15px;
    padding-left: 12px;
    padding-right: 12px;
}

input[type=password] {
    border-top: 1px solid #999;
    border-right: 1px solid #999;
    border-bottom: 1px solid #666;
    border-left: 1px solid #666;
    height: 38px;
    width: 376px;
    line-height: 24px;
    font-size: 14px;
    color: #333;
    font-family: Helvetica;
    margin-left: 142px;
    -moz-border-radius: 15px;
    padding-left: 12px;
    padding-right: 12px;
}

input[type=file] {
    width: 376px;
    font-size: 14px;
    color: #333;
    font-family: Helvetica;
    margin-left: 142px;
    padding-left: 12px;
    padding-right: 12px;
}

select {
    border-top: 1px solid #999;
    border-right: 1px solid #999;
    border-bottom: 1px solid #666;
    border-left: 1px solid #666;
    height: 38px;
    width: 376px;
    line-height: 14px;
    font-size: 14px;
    color: #333;
    font-family: Helvetica;
    margin-left: 142px;
    -moz-border-radius: 15px;
    padding-left: 12px;
    padding-right: 12px;
    padding-top: 6px;
    padding-bottom: 6px;
}

textarea {
    border-top: 1px solid #999;
    border-right: 1px solid #999;
    border-bottom: 1px solid #666;
    border-left: 1px solid #666;
    height: 108px;
    width: 450px;
    line-height: 24px;
    font-size: 14px;
    color: #333;
    font-family: Helvetica;
    margin-left: 142px;
    -moz-border-radius: 15px;
    padding-left: 16px;
    padding-right: 30px;
}

input[type=radio] {
    line-height: 24px;
    color: #333;
    font-family: Helvetica;
    padding-left: 16px;
    padding-right: 30px;
    margin-left: 142px;
}

input[type=submit], input[type=reset] {
    line-height: 24px;
    font-size: 14px;
    height: 38px;
    color: #333;
    font-family: Helvetica;
    padding-left: 30px;
    padding-right: 30px;
    border: 1px solid #666;
    background-color: #999;
    -moz-border-radius: 15px;
    cursor: pointer;
    text-align: center;
}

#myselect {
    margin-left: 142px;
    height: 100px;
    overflow: hidden;
}

fieldset {
    margin: 0;
    padding: 0;
    border: 0;
    min-height: 40px;
    width: 640px;
    padding: 22px;
}

fieldset div {
    float: left;
    margin-bottom: 12px;
}

#fieldset1, #fieldset2, #fieldset3, #fieldset4, #fieldset5 {
    border: 1px solid #999;
    margin-bottom: 22px;
}

#fieldset1 legend, #fieldset2 legend, #fieldset3 legend, #fieldset4 legend, #fieldset5 legend {
    margin-left: 22px;
}

#fieldset1 label, #fieldset2 label, #fieldset3 label, #fieldset4 label,  #fieldset5 label, #labelmytextarea {
    position: absolute;
    text-align: right;
    width: 130px;
    color: #333;
}

#DoozR_Form_Fieldset_0 {
    text-align: left;
}

.radio2nd {
    /*margin-left: 0;*/
}

.classWrapMe {
    /*width: 500px;*/
    clear: both;
    margin-left: 138px;
    margin-top: 12px;
}

.classWrapMe2 {
    margin-top: 12px;
    margin-left: 120px;
}

.classWrapMe2 label {
    text-align: left !important;
    margin-left: 24px;
}

.valid {
    color: #3B8F11 !important;
}

.invalid {
    color: #AF1515 !important;
}

#mysubmit {
    font-weight: bold;
    color: #000;
}

#myreset {
    font-weight: normal;
    color: #666;
}


</style>

<body>
    <h1>Form - Step 1 of 3</h1>
    <?php
        echo $formhtml;
    ?>
    <div style="color:#cc0000;font-weight:bold;">

        <?php
            if ($form->submitted('contact')) {
                $error = $form->getError('form');
                if ($error) {
                    echo 'error in form: => '.$error.'<br />';
                }
            /*
                echo '<br />all error:<br />';
                foreach ($form->getError() as $field => $error) {
                    echo 'error in field: '.$field.' => '.$error.'<br />';
                }

                echo '<br />form error:<br />';
                $error = $form->getError('form');
                if ($error) {
                    echo 'error in form: => '.$error.'<br />';
                }

                echo '<br />last error (string only):<br />';
                echo $form->getLastError(true).'<br />';

                echo '<br />last error (keyed array):<br />';
                $error = $form->getLastError();
                echo 'error in field: '.$error['element'].' => '.$error['error'].'<br />';

                echo '<br />form status:<br />';
                if ($form->valid()) {
                    echo '<span style="color:#28AF20;">VALID</span>';
                } else {
                    echo 'INVALID';
                }
                echo '<br />';

                echo '<br />form error (after valid-check):<br />';
                $error = $form->getError('form');
                if ($error) {
                    echo 'error in form: => '.$error.'<br />';
                }
            */
            }
        ?>
    </div>
    </body>
</html>