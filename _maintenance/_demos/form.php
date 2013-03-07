<?php

/*
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2013, Benjamin Carl - All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * - All advertising materials mentioning features or use of this software
 *   must display the following acknowledgement: This product includes software
 *   developed by Benjamin Carl and other contributors.
 * - Neither the name Benjamin Carl nor the names of other contributors
 *   may be used to endorse or promote products derived from this
 *   software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * Please feel free to contact us via e-mail: opensource@clickalicious.de
 */

// include DoozR core
require_once 'Controller/Core/Core.php';

// instanciate DoozR core
$DoozR = DoozR_Core::getInstance();

// get a handle on formhelper
$form = $DoozR->getModuleHandle('formhelper');


if ($form->getSubmitted()) {
	echo 'wurde submitted';

	// hier alle Felder iterieren (über formhandler -> Session -> Elements)
	// und dann hasImpact() ausgeben und wenn dann getImpact()
}

?>

<html>
<head>
<title>DoozR - Formhelper PoC</title>

<style>

label{
    display:inline;
}

.DoozR-Form{
    width:100%;
    font-family:Georgia,"Nimbus Roman No9 L",serif;
    font-size:14px;
}

.DoozR-Form input, textarea, select{
    font-family:Georgia,"Nimbus Roman No9 L",serif;
    font-size:14px;
}

#blockRadio div {
	/*float:left;*/
}

.DoozR-Form-surround .error {
    color:#cc0000;
    border:1px solid #cc0000;
}


.DoozR-Form-Surround {
    border:1px dashed #0C9;
    float:left;
    padding-bottom:22px;
}

.DoozR-Form-Surround label {
	width:200px;
	margin-top:4px;
    float:left;
}

#mytextarea, #myinputtext, #myselect, #myfile {
    width:400px;
    height:24px;
}

#mytextarea{
    height:120px;
}

.myclass{
	margin-bottom:40px;
	margin-top:40px;
}

.dFhBlock {
	width:100%;
}

</style>

</head>
<body>


<div style="width:100%;text-align:center">
<div style="width:800px;text-align:left;">

<?php
/**
 * create a form element with formhelper
 */
// setup form name
$form->setName('mytestform');
// set action
$form->setAction($_SERVER['PHP_SELF']);
// set method
$form->setMethod('post');
// set id
$form->setId('mytestform');
// upload
$form->setUpload(true, 999999999);
// prevent submit
//$form->setSubmittable(false);
// set custom attribute
$form->setAttribute('onsubmit', 'javascript:alert(\'about to submit\');return true;');


//
// INPUT TEXT
//
// set a new block begin
$form->setBlockBegin('block1', 'myclass');
// add the element
$element = $form->addElement('text');
// set its name
$element->setName('myinputtext');
// set label
$element->setLabel('Mein Text:', 'left');
// set its id
$element->setId('myinputtext');
// set it default value
$element->setValue(23235);
// set required
$element->setRequired(true);
// set type awaiting
//$element-setValidation('NUMBER', 'INT', 5, 5);
// set to readonly
//$element->setReadonly();
// set a custom attribute + value
//$element->setAttribute('onclick', 'javascript:alert(document.location);');
// set max length attrib
$element->setMaxlength(100);
if ($form->getSubmitted()) {
    echo 'myinputtext: ' . $element->getValue() . '<br />';;
}

/*
$element = $form->addElement('file');
$element->setName('videoupload');
$element->setId('myfile');
$element->setLabel('hier ein dateiupload');


// add a css hacked file-upload field
$element = $form->addElement('Swfupload');
$element->setName('videoupload2');
$element->setId('myfile2');
$element->setLabel('hier SWFUpload:');
//$error = $element->getError();
*/


//
// TEXTAREA
//
$element = $form->addElement('textarea');
// set its name
$element->setName('mytextarea');
// set label
$element->setLabel('Mein Text Teil2:', 'left');
// set its id
$element->setId('mytextarea');
// set it default value
$element->setValue('this is my textarea');
// check value
if ($form->getSubmitted()) {
    echo 'mytextarea: ' . $element->getValue() . '<br />';;
}

$form->setBlockEnd();


//
// SELECT
//
$element = $form->addElement('select');
// set its name
$element->setName('myselect');
// set its id
$element->setId('myselect');
// set its default value
$element->addOption('element 1', '1');
// set its default value
$element->addOption('element 2', '2');
// set active value
$element->setValue('2');
// set label
$element->setLabel('Mein Select:', 'left');
if ($form->getSubmitted()) {
    echo 'select: ' . $element->getValue() . '<br />';
}


// set a new block begin
$form->setBlockBegin('blockRadio', 'radioclass');
//
// Radio 1
//
$element = $form->addElement('radio');
// set its name
$element->setName('myradio');
// set its id
$element->setId('myradio1');
// set active value
$element->setValue('ja');
// set text
$element->setText('ja');
// set label
$element->setLabel('Mein Radio:', 'left');

//
// Radio 2
//
$element = $form->addElement('radio');
// set its name
$element->setName('myradio');
// set its id
$element->setId('myradio2');
// set active value
$element->setValue('nein');
// set text
$element->setText('nein');
if ($form->getSubmitted()) {
    echo 'myradio: ' . $element->getValue() . '<br />';
}

$form->setBlockEnd();

//
// Checkbox
//
$element = $form->addElement('checkbox');
// set its name
$element->setName('mycheckbox');
// set its id
$element->setId('mycheckbox');
// set active value
$element->setValue('agb');
// set label
$element->setLabel('Mein checkbox:', 'left');


//
// Checkbox 2
//
$element = $form->addElement('checkbox');
// set its name
$element->setName('mycheckbox');
// set its id
$element->setId('mycheckbox2');
// set active value
$element->setValue('agbnein');
// set label
$element->setLabel('Mein checkbox 2:', 'left');
if ($form->getSubmitted()) {
    echo 'checkbox: ' . $element->getValue() . '<br />';
}


//
// SUBMIT
//
$element = $form->addElement('submit');
// set its name
$element->setName('mysubmit');
// set its id
$element->setId('mysubmit');
// set its default value
$element->setValue('abschicken');
// set label
$element->setLabel('&nbsp;', 'left');

// render the form to a var
$formhtml = $form->render(false);

// echo out generated and fetched form html code
echo $formhtml;

?>
</div>
</div>

</body>
</html>
