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

    // what's to do on invalid token
    $form->onInvalidToken('IGNORE');

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

        // direction
        $element->lang('en-en');

        // set it default value
        $element->value('my_username');

        // set required
        $element->required(true);

        // set type awaiting
        $element->validType('NUMBER_INTEGER_5_5');

        // set to readonly
        //$element->readonly();

        // set a custom attribute + value
        //$element->attribute('onclick', 'javascript:alert(document.location);');

        // set max length attrib
        $element->maxlength(100);


        // PASSWORD
        $element = $form->addElement('password', true);

        // set its name
        $element->name('myinputpassword');

        // set label
        $element->label('Password', 'left');

        // set its id
        $element->id('myinputpassword');

        // set it default value
        $element->value('my_password');

        // set to readonly
        $element->readonly();

        // set max length attrib
        $element->maxlength(12);


        // TEXTAREA
        $element = $form->addElement('textarea', true);

        // set its name
        $element->name('mytextarea');

        // set required
        $element->required(true);

        // set label
        $element->label('Feedback', 'left');

        // set its id
        $element->id('mytextarea');

        // set it default value
        $element->value('this is my textarea');

    // end of block
    $form->setFieldsetEnd();


    // set a new block begin
    $form->setFieldsetBegin('fieldset2', 'Choose your gender', 'myclass');

        // Radio 1
        $element = $form->addElement('radio', true);

        // set its name
        $element->name('gender');

        // set required
        $element->required(true);

        // set checked (preselect)
        $element->preselected(true);

        // set its id
        $element->id('myradio1');

        // set active value
        $element->value('male');

        // set label
        $element->label('male', 'left');

        // Radio 2
        $element = $form->add('radio', true);

        // set its name
        $element->name('gender');

        // set required
        $element->required(true);

        // set its id
        $element->id('myradio2');

        // set active value
        $element->value('female');

        // set its class
        $element->cssClass('radio2nd');

        // set text
        //$element->setText('nein');

        // set label
        $element->label('female', 'left');

    // end of block
    $form->setFieldsetEnd();


    // set a new block begin
    $form->setFieldsetBegin('fieldset4', 'Choose Country', 'myclass');

        // SELECT
        $element = $form->addElement('select');

        // set its name
        $element->name('myselect');

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

        // demonstration of how to remove a option
        $element->removeOption($reference);

        // set type to multiline
        $element->multiline(true, 2);

        // allow multiple input
        $element->multiple();

        // set label
        $element->label('Your country:', 'left');

    // end of block
    $form->setFieldsetEnd();


    // HTML
    $element = $form->addElement('html');

    $element->html('<h2>Form - Step 2 of 3</h2>');


    // set a new block begin
    $form->setFieldsetBegin('fieldset5', 'Select PDF-File to upload', 'myclass');

        // SELECT
        $element = $form->addElement('file', true);

        // set its name
        $element->name('myfile');

        // set label
        $element->label('Your CV:', 'left');

        // set mimetype accept
        $element->setAccept('application/pdf');


        // IMAGE
        $element = $form->addElement('image', true);

        // set its name
        $element->name('myimage');

        // set URL (image source)
        $element->src('/DoozR/www/view/static/img/submit-button.jpg');


        // Checkbox
        $element = $form->addElement('checkbox', true, 'classWrapMe');

        // set its name
        $element->name('mycheckbox');

        // set its id
        $element->id('mycheckbox');

        // set active value
        $element->value('agb');

        // required field
        $element->required();

        //$element->preselected();

        // set label
        $element->label('I accept the AGB', 'left');


        // Checkbox
        $element = $form->addElement('checkbox', true, 'classWrapMe2');

        // set its name
        $element->name('mycheckbox2');

        // set its id
        $element->id('mycheckbox2');

        // set active value
        $element->value('agbnot');

        // set label
        $element->label('not', 'left');


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
    <title>DoozR - Demonstration of module :: DoozR_Form</title>
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
    </body>
</html>