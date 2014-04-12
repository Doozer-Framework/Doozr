<?php
/***********************************************************************************************************************
 *
 * DEMONSTRATION
 * Service: Form
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
 * Get the config instance easily from registry
 */
$config = $registry->config;


/**
 * Get the "I18n" module to demonstrate you the nice interaction between module "Form" and "I18n"
 */
$i18n = DoozR_Loader_Serviceloader::load('i18n', $registry->config, 'de');


/**
 * The store for the form transfer from one request to next
 */
$session = DoozR_Loader_Serviceloader::load('session');


/**
 * Get the "Form" module
 * @var $formservice DoozR_Form_Service
 */
$formservice = DoozR_Loader_Serviceloader::load('form');


/*
$parser = new DoozR_Form_Service_Parser_Form();

// Extract information
$parser->open(DOOZR_APP_ROOT . 'form1.html')->parse();

// Show template
pre(
    $parser->getTemplate()
);

// Show config
pre(
    $parser->getConfiguration()
);
die;
*/

/*
$component1 = new DoozR_Form_Service_Component_Html('p');
$component1->setAttribute('id', 'foo');
$component1->setInnerHtml('Hallo :) ');

$component2 = new DoozR_Form_Service_Component_Html('h1');
$component2->setInnerHtml('Alter wie geil!');
$component1->addChild($component2);

$component1['id'] = 'bar';

echo var_dump( $component1->render() );
*/

/*
$component2 = new DoozR_Form_Service_Component_Html('h1');
$component2->setInnerHtml('Alter wie geil!');

$component1->addChild($component2);

$component3 = new DoozR_Form_Service_Component_Input(
    'foo',
    $registry->front->getRequest()->getArguments(),
    $formManager->getRegistry()
);

$component3->setValue('Feed me');

var_Dump( $component3->isValid() );

$component1->addChild($component3);

var_dump( $component1->render() );
#echo $component1->render();

die;
*/


// create a new form-container which combines the control-layer and the HTML parts
$formManager = new DoozR_Form_Service_FormManager(
    'register',                                                 // The namespace (used for session, I18n, ...)
    $i18n,                                                      // The I18n service instance for translation(s) [DI]
    new DoozR_Form_Service_Component_Input(                     // Input element <- for cloning [DI]
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    ),
    new DoozR_Form_Service_Component_Form(                      // The form element we operate on [DI]
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic(),
        'register'
    ),
    new DoozR_Form_Service_Store_Session($session),             // The session store [DI]
    new DoozR_Form_Service_Renderer_Html(),                     // A Renderer -> Native = HTML [DI]
    new DoozR_Form_Service_Validate_Validator(),                // A Validator to validate the elements [DI]
    new DoozR_Form_Service_Validate_Error(),                    // A Error object <- for cloning [DI]
    $registry->front->getRequest()->getArguments()              // The currents requests arguments
);


/*
// Input
$foo = new DoozR_Form_Service_Component_Input(
    new DoozR_Form_Service_Renderer_Html(),
    new DoozR_Form_Service_Validator_Generic()
);
*/

/*
// Textfeld
$foo = new DoozR_Form_Service_Component_Text(
    new DoozR_Form_Service_Renderer_Html(),
    new DoozR_Form_Service_Validator_Generic()
);
*/

/*
// Radio
$foo = new DoozR_Form_Service_Component_Radio(
    new DoozR_Form_Service_Renderer_Html(),
    new DoozR_Form_Service_Validator_Generic()
);
*/

/*
// Label
$foo = new DoozR_Form_Service_Component_Label(
    new DoozR_Form_Service_Renderer_Html(),
    new DoozR_Form_Service_Validator_Generic()
);
$foo->setFor('foo');
$foo->setText('I am Label!');
*/

/*
// Checkbox
$foo = new DoozR_Form_Service_Component_Checkbox(
    new DoozR_Form_Service_Renderer_Html(),
    new DoozR_Form_Service_Validator_Generic()
);
*/

/*
// Div
$foo = new DoozR_Form_Service_Component_Div(
    new DoozR_Form_Service_Renderer_Html(),
    new DoozR_Form_Service_Validator_Generic()
);
$foo->setInnerHtml('Hallo');
*/

/*
// Legend
$foo = new DoozR_Form_Service_Component_Legend(
    new DoozR_Form_Service_Renderer_Html(),
    'Foo'
);

// Fieldset
$foo = new DoozR_Form_Service_Component_Fieldset(
    new DoozR_Form_Service_Renderer_Html(),
    $foo
);
*/

/*
// Form
$foo = new DoozR_Form_Service_Component_Form(
    new DoozR_Form_Service_Renderer_Html(),
    new DoozR_Form_Service_Validator_Generic()
);
*/

/*
// File
$foo = new DoozR_Form_Service_Component_File(
    new DoozR_Form_Service_Renderer_Html(),
    new DoozR_Form_Service_Validator_Generic()
);
*/

/*
// Hidden
$foo = new DoozR_Form_Service_Component_Hidden(
    new DoozR_Form_Service_Renderer_Html(),
    new DoozR_Form_Service_Validator_Generic()
);
*/

/*
// Message
$foo = new DoozR_Form_Service_Component_Message(
    new DoozR_Form_Service_Renderer_Html(),
    'This is the message'
);
*/

/*
// Textarea
$foo = new DoozR_Form_Service_Component_Textarea(
    new DoozR_Form_Service_Renderer_Html(),
    new DoozR_Form_Service_Validator_Generic()
);
$foo->setText('Hallo');
*/

/*
// Optgroup
$foo1 = new DoozR_Form_Service_Component_Optgroup(
    new DoozR_Form_Service_Renderer_Html(),
    new DoozR_Form_Service_Validator_Generic()
);
$foo1->setLabel('Foo');

// Option
$foo2 = new DoozR_Form_Service_Component_Option(
    new DoozR_Form_Service_Renderer_Html(),
    new DoozR_Form_Service_Validator_Generic()
);
$foo2->setKey('Foo');

$foo1->addOption($foo2);

// Select
$foo = new DoozR_Form_Service_Component_Select(
    new DoozR_Form_Service_Renderer_Html(),
    new DoozR_Form_Service_Validator_Generic()
);
$foo->addOption($foo1);
*/

/*
// Group
$foo = new DoozR_Form_Service_Component_Group(
    new DoozR_Form_Service_Renderer_Html(),
    new DoozR_Form_Service_Validator_Generic(),
    new DoozR_Form_Service_Component_Label(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    ),
    new DoozR_Form_Service_Component_Text(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    ),
    new DoozR_Form_Service_Component_Message(
        new DoozR_Form_Service_Renderer_Html(),
        'Geil Alter!'
    )
);

#$foo->setType('text');
$foo->setName('foo');

#echo $foo->render();
var_dump($foo->render());
die;
*/

/**
 * check first if form was submitted - if it is valid - and if it is the last step ...
 */
if ($formManager->wasSubmitted() && $formManager->isValid() && $formManager->isComplete()) {
    // required to clear session content as well as other stuff ro prevent resubmitting.
    $formManager->invalidateRegistry();

    pred('Form submitted, valid and complete!');
}

if ($formManager->getStep() === 1) {

    // magic sets on form setMethod('post') becomes ->setAttribute('method', 'post');
    $form = $formManager->getForm();
    $form->setMethod('post');
    $form->setAction($_SERVER['PHP_SELF']);
    $form->setNovalidate();
    $form->enableUpload();

    // native implemented methods also chain support
    $formManager->setStep(1);
    $formManager->setSteps(3);
    $formManager->setInvalidTokenBehavior(DoozR_Form_Service_Constant::TOKEN_BEHAVIOR_DENY);
    $formManager->setI18n($i18n);

    if ($formManager->wasJumped() !== false) {
        pre('we jumped right to step: '.$formManager->getStep());
    }

    // Create a label
    $label = new DoozR_Form_Service_Component_Label(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    );
    $label->setText('Firstname:');

    // Create an input field
    $element = new DoozR_Form_Service_Component_Text(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    );
    $element->setName('firstname');
    $element->setId('firstname');
    $element->setValue(
        $formManager->getValue('firstname', 'Vorname')
    );
    $element->addValidation(
        DoozR_Form_Service_Validate_Constant::REQUIRED
    );
    $element->addValidation(
        DoozR_Form_Service_Validate_Constant::VALUE,
        'Ben'
    );
    $element->addValidation(
        DoozR_Form_Service_Validate_Constant::VALUE,
        'Benjamin'
    );

    // Create a message
    if ($formManager->getError('firstname') === null) {
        $error = null;
    } else {
        $error = DoozR_Form_Service_Validate_Constant::ERROR_PREFIX . $formManager->getError(
                'firstname', null
            )[0]['error'];
    }

    $message = new DoozR_Form_Service_Component_Message(
        new DoozR_Form_Service_Renderer_Html(),
        $formManager->translate($error)
    );

    $message->setStyle('color:red;');


    // Create a group with: Label, Component, Message
    $group1 = new DoozR_Form_Service_Component_Group(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic(),
        $label,
        $element,
        $message
    );


    // Create a label
    $label1 = new DoozR_Form_Service_Component_Label(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    );
    $label1->setText('Male:');

    $label2 = new DoozR_Form_Service_Component_Label(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    );
    $label2->setText('Female:');

    // Create an input field
    $element1 = new DoozR_Form_Service_Component_Radio(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    );
    $element1->setName('gender');
    $element1->setId('gender1');
    $element1->setValue(
        'male', $formManager->getValue('gender')
    );
    $element1->addValidation(
        DoozR_Form_Service_Validate_Constant::REQUIRED
    );
    $element1->addValidation(
        DoozR_Form_Service_Validate_Constant::VALUE,
        'male'
    );
    $element1->addValidation(
        DoozR_Form_Service_Validate_Constant::VALUE,
        'female'
    );

    // Create an input field
    $element2 = new DoozR_Form_Service_Component_Radio(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    );
    $element2->setName('gender');
    $element2->setId('gender2');
    $element2->setValue(
        'female', $formManager->getValue('gender')
    );
    #$element2->setChecked();
    $element2->addValidation(
        DoozR_Form_Service_Validate_Constant::REQUIRED
    );
    $element2->addValidation(
        DoozR_Form_Service_Validate_Constant::VALUE,
        'male'
    );
    $element2->addValidation(
        DoozR_Form_Service_Validate_Constant::VALUE,
        'female'
    );

    // Create a message
    if ($formManager->getError('gender') === null) {
        $error = null;
    } else {
        $error = DoozR_Form_Service_Validate_Constant::ERROR_PREFIX.$formManager->getError(
                'gender', null
            )[0]['error'];
    }

    $message = new DoozR_Form_Service_Component_Message(
        new DoozR_Form_Service_Renderer_Html(),
        $formManager->translate($error)
    );
    $message->setStyle('color:red;');

    $group2 = new DoozR_Form_Service_Component_Group(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic(),
        array($label1, $label2),
        array($element1, $element2),
        $message
    );



    // Create a label
    $label1 = new DoozR_Form_Service_Component_Label(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    );
    $label1->setText('Support:');

    $label2 = new DoozR_Form_Service_Component_Label(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
     );
    $label2->setText('Question:');

    // Create an input field
    $element1 = new DoozR_Form_Service_Component_Checkbox(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    );
    $element1->setName('interest');
    $element1->setId('interest1');
    $element1->setValue(
        'support', $formManager->getValue('interest')
    );

    $element1->addValidation(
        DoozR_Form_Service_Validate_Constant::REQUIRED
    );
    $element1->addValidation(
        DoozR_Form_Service_Validate_Constant::VALUE,
        'support'
    );
    $element1->addValidation(
        DoozR_Form_Service_Validate_Constant::VALUE,
        'question'
    );

    // Create an input field
    $element2 = new DoozR_Form_Service_Component_Checkbox(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    );
    $element2->setName('interest');
    $element2->setId('interest2');
    $element2->setValue(
        'question', $formManager->getValue('interest')
    );

    // if an element must always be checked again like confirming AGB / Privacy ...
    // then uncheck the field on each call with argument force = true!
    #$element2->uncheck(true);

    $element2->addValidation(
        DoozR_Form_Service_Validate_Constant::REQUIRED
    );
    $element2->addValidation(
        DoozR_Form_Service_Validate_Constant::VALUE,
        'support'
    );
    $element2->addValidation(
        DoozR_Form_Service_Validate_Constant::VALUE,
        'question'
    );

    // Create a message
    if ($formManager->getError('interest') === null) {
        $error = null;
    } else {
        $error = DoozR_Form_Service_Validate_Constant::ERROR_PREFIX.$formManager->getError(
                'interest', null
            )[0]['error'];
    }

    $message = new DoozR_Form_Service_Component_Message(
        new DoozR_Form_Service_Renderer_Html(),
        $formManager->translate($error)
    );
    $message->setStyle('color:red;');

    $group3 = new DoozR_Form_Service_Component_Group(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic(),
        array($label1, $label2),
        array($element1, $element2),
        $message
    );

    // define label
    $label1 = new DoozR_Form_Service_Component_Label(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    );
    $label1->setText('Datei zum upload wählen:');

    // Create an input field
    $element1 = new DoozR_Form_Service_Component_File(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    );
    $element1->setName('file');
    $element1->setId('file');
    $element1->setValue(
        $formManager->getValue('file')
    );

    $element1->setMaxFilesize(
        'auto'
    );

    $element1->addValidation(
        DoozR_Form_Service_Validate_Constant::REQUIRED
    );

    $element1->addValidation(
        DoozR_Form_Service_Validate_Constant::FILETYPE,
        array('text/plain')
    );

    $element1->addValidation(
        DoozR_Form_Service_Validate_Constant::FILEEXTENSION,
        array('txt')
    );

    $element1->addValidation(
        DoozR_Form_Service_Validate_Constant::FILESIZEMIN,
        500
    );


    $fileInfo = new DoozR_Form_Service_Component_Div(
        new DoozR_Form_Service_Renderer_Html()
    );
    $pathInfo = pathinfo(($element1->getValue() !== null) ? $element1->getValue() : '');
    $fileInfo->setInnerHtml(
        $pathInfo['basename']
    );

    $groupFile = new DoozR_Form_Service_Component_Group(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic(),
        $fileInfo,
        $element1,
        null
    );


   // Create a message
    if ($formManager->getError('file') === null) {
        $error = null;
    } else {
        $error = DoozR_Form_Service_Validate_Constant::ERROR_PREFIX.$formManager->getError(
                'file', null
            )[0]['error'];
    }

    $message = new DoozR_Form_Service_Component_Message(
        new DoozR_Form_Service_Renderer_Html(),
        $formManager->translate(
            $error,
            array(500, 800)     // min-size, max-size,
        )
    );
    $message->setStyle('color:red;');

    $group4 = new DoozR_Form_Service_Component_Group(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic(),
        $label1,
        $groupFile,
        $message
    );

    // Create an input element "button"
    $element = new DoozR_Form_Service_Component_Input(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    );
    $element->setName('submit');
    $element->setId('submit');
    $element->setType('submit');
    $element->setValue('Weiter zu Schritt 2/3');


    // Create a fieldset
    $fieldset = new DoozR_Form_Service_Component_Fieldset(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Component_Legend(
            new DoozR_Form_Service_Renderer_Html(),
            'This is an example legend ...'
        )
    );

    $fieldset->addChild($group1);
    $fieldset->addChild($group2);
    $fieldset->addChild($group3);
    $fieldset->addChild($group4);
    $fieldset->addChild($element);

    // Add the fieldset to the form -> looks weird ... why to put the form in first ...
    $formManager->getForm()->addChild($fieldset);

    // render the whole stuff
    #$html = $formManager->render();

} elseif ($formManager->getStep() === 2) {

    // magic sets on form setMethod('post') becomes ->setAttribute('method', 'post');
    $form = $formManager->getForm();
    $form->setMethod('post');
    $form->setAction($_SERVER['PHP_SELF']);
    $form->setNovalidate();
    $form->enableUpload();

    // native implemented methods also chain support
    $formManager->setStep(2);
    $formManager->setSteps(3);
    $formManager->setInvalidTokenBehavior(DoozR_Form_Service_Constant::TOKEN_BEHAVIOR_DENY);
    $formManager->setI18n($i18n);


    // Create a label
    $label = new DoozR_Form_Service_Component_Label(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    );
    $label->setText('Type:');

    // Create an input field
    $element = new DoozR_Form_Service_Component_Select(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    );
    $element->setName('type');
    $element->setId('type');

    $element->addValidation(
        DoozR_Form_Service_Validate_Constant::REQUIRED
    );

    $element->addValidation(
        DoozR_Form_Service_Validate_Constant::VALUE,
        'value2'
    );

    /*
    $element->setValue(
        $formManager->getValue('type')
    );
    */

    $optgroup = new DoozR_Form_Service_Component_Optgroup(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    );
    $optgroup->setLabel('Optgroup');

    $option1 = new DoozR_Form_Service_Component_Option(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    );
    $option1->setKey('key1');
    $option1->setValue('value1', $formManager->getValue('type'));

    $option2 = new DoozR_Form_Service_Component_Option(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    );
    $option2->setKey('key2');
    $option2->setValue('value2', $formManager->getValue('type'));

    $optgroup->addOption($option1);
    $optgroup->addOption($option2);

    $element->addOption($optgroup);

    // Create a message
    if ($formManager->getError('type') === null) {
        $error = null;
    } else {
        $error = DoozR_Form_Service_Validate_Constant::ERROR_PREFIX.$formManager->getError(
                'type', null
            )[0]['error'];
    }

    $message = new DoozR_Form_Service_Component_Message(
        new DoozR_Form_Service_Renderer_Html(),
        $formManager->translate(
            $error
        )
    );
    $message->setStyle('color:red;');


    // Create a group with: Label, Component, Message
    $group0 = new DoozR_Form_Service_Component_Group(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic(),
        $label,
        $element,
        $message
    );


    /**
     * Begin TEXTAREA: sometext
     */
    $label = new DoozR_Form_Service_Component_Label(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    );
    $label->setText('Tell us about you:');

    $textarea = new DoozR_Form_Service_Component_Textarea(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    );
    $textarea->setName('sometext');

    $textarea->addValidation(
        DoozR_Form_Service_Validate_Constant::REQUIRED
    );

    $textarea->setValue(
        $formManager->getValue(
            'sometext', 'Enter some text here ....'
        )
    );

    // Create a message
    if ($formManager->getError('sometext') === null) {
        $error = null;
    } else {
        $error = DoozR_Form_Service_Validate_Constant::ERROR_PREFIX.$formManager->getError(
                'sometext', null
            )[0]['error'];
    }

    $message = new DoozR_Form_Service_Component_Message(
        new DoozR_Form_Service_Renderer_Html(),
        $formManager->translate(
            $error
        )
    );
    $message->setStyle('color:red;');

    // Create a group with: Label, Component, Message
    $group3 = new DoozR_Form_Service_Component_Group(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic(),
        $label,
        $textarea,
        $message
    );



    // Create a label
    $label = new DoozR_Form_Service_Component_Label(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    );
    $label->setText('Nummer:');

    // Create an input field
    $element = new DoozR_Form_Service_Component_Input(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    );
    $element->setName('number');
    $element->setId('number');
    $element->setType('text');
    $element->setValue(
        $formManager->getValue('number', '123456')
    );
    $element->addValidation(
        DoozR_Form_Service_Validate_Constant::REQUIRED
    );
    $element->addValidation(
        DoozR_Form_Service_Validate_Constant::VALUE,
        '123456789'
    );

    // Create a message
    if ($formManager->getError('number') === null) {
        $error = null;
    } else {
        $error = DoozR_Form_Service_Validate_Constant::ERROR_PREFIX.$formManager->getError(
                'number', null
            )[0]['error'];
    }

    $message = new DoozR_Form_Service_Component_Message(
        new DoozR_Form_Service_Renderer_Html(),
        $formManager->translate(
            $error
        )
    );
    $message->setStyle('color:red;');


    // Create a group with: Label, Component, Message
    $group1 = new DoozR_Form_Service_Component_Group(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic(),
        $label,
        $element,
        $message
    );

    // Create a fieldset
    $fieldset = new DoozR_Form_Service_Component_Fieldset(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Component_Legend(
            new DoozR_Form_Service_Renderer_Html(),
            'Please provide us some more information about you ...'
        )
    );

    // Create an input field
    $element = new DoozR_Form_Service_Component_Generic(
        'a',
        null,
        new DoozR_Form_Service_Renderer_Html()
    );

    #$element->setTag('a');
    $element->setAttribute('href', $_SERVER['PHP_SELF'].'?DoozR_Form_Service_Jump=1');
    $element->setInnerHtml('Zurück zu Schritt 1/3');

    $group2 = new DoozR_Form_Service_Component_Group(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic(),
        null,
        $element,
        null
    );

    // Create an input element "button"
    $element = new DoozR_Form_Service_Component_Input(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    );
    $element->setName('submit');
    $element->setId('submit');
    $element->setType('submit');
    $element->setValue('Weiter zu Schritt 3/3');

    $fieldset->addChild($group0);
    $fieldset->addChild($group3);
    $fieldset->addChild($group1);
    $fieldset->addChild($group2);
    $fieldset->addChild($element);

    // Add the fieldset to the form -> looks weird ... why to put the form in first ...
    $formManager->getForm()->addChild($fieldset);


} elseif ($formManager->getStep() === 3) {

    // magic sets on form setMethod('post') becomes ->setAttribute('method', 'post');
    $form = $formManager->getForm();
    $form->setMethod('post');
    $form->setAction($_SERVER['PHP_SELF']);
    $form->setNovalidate();
    $form->enableUpload();

    // native implemented methods also chain support
    $formManager->setStep(3);
    $formManager->setSteps(3);
    $formManager->setInvalidTokenBehavior(DoozR_Form_Service_Constant::TOKEN_BEHAVIOR_DENY);
    $formManager->setI18n($i18n);


    // Create an input field
    $element1 = new DoozR_Form_Service_Component_Generic(
        'a',
        null,
        new DoozR_Form_Service_Renderer_Html()
    );

    $element1->setAttribute('href', $_SERVER['PHP_SELF'].'?DoozR_Form_Service_Jump=2');
    $element1->setInnerHtml('Zurück zu Schritt 2/3');

    // Create a fieldset
    $fieldset = new DoozR_Form_Service_Component_Fieldset(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Component_Legend(
            new DoozR_Form_Service_Renderer_Html(),
            'Have a look at all collected Data ...'
        )
    );


    $element3 = new DoozR_Form_Service_Component_Generic(
        'div',
        null,
        new DoozR_Form_Service_Renderer_Html()
    );

    $element3->setAttribute('id', 'wurstbrot');
    $element3->setInnerHtml(
        nl2br(var_export($formManager->getRegistry('data'), true))
    );

    // Create an input element "button"
    $element2 = new DoozR_Form_Service_Component_Input(
        new DoozR_Form_Service_Renderer_Html(),
        new DoozR_Form_Service_Validator_Generic()
    );
    $element2->setName('submit');
    $element2->setId('submit');
    $element2->setType('submit');
    $element2->setValue('abschliessen');

    $fieldset->addChild($element1);
    $fieldset->addChild($element3);
    $fieldset->addChild($element2);

    // Add the fieldset to the form -> looks weird ... why to put the form in first ...
    $formManager->getForm()->addChild($fieldset);
}

?>
<html>
<head>
    <title>DoozR Service Form - Form: register</title>
    <style>

        body {
            font-family: Arial, Helvetica Neue, Helvetica;
        }

        /* general form n1ce pimp ups */
        label {
            cursor: pointer !important;
            display: block;
        }

        /* custom style */
        .invalid {
            color: #cc0000;
            /*margin-bottom: 12px;*/
        }

        .DoozR_Form_Service_Fieldset_Container {
            float: left;
            margin-bottom: 18px;
            width: 100%;
        }

        .DoozR_Form_Service_Fieldset_Container label,
        .DoozR_Form_Service_Fieldset_Container input {
            float: left;
            width: 100%;
        }

        .myclass {
            width: 320px;

        }

        #container_gender-1,
        #container_gender-2 {
            width: 100px;
        }

        #container_gender-1 input,
        #container_gender-2 input {
            width: 70%;
            float: right;
        }

        #container_gender-1 label,
        #container_gender-2 label {
            width: 10%;
            float: left;
        }

        .leftHalf {
            float: left;
        }

        .rightHalf {
            float: right;
        }

        #container_gender-1 input,
        #container_gender-2 input {
            cursor: pointer;
        }

        #container_agb label {
            width: 299px;
            float: left;
            font-size: 0.8em;
            margin-top: 2px;
        }

        #container_agb input {
            width: 17px;
            float: left;
            margin-left: 0;
            padding-left: 0;
            text-align: left;
        }

        #container_submit-register,
        #container_reset-register {
            width: 50%;
        }

        #container_reset-register input {
            text-align: center;
            background: none;
            border: 0;
            border-bottom: 1px solid blue;
            /*font-size: 0.75em;*/
            margin-top: 2px;
            width: 70px;
            margin-left: 50px;
            color: blue;
            cursor: pointer;
        }

        #container_gender-1 #error_gender {
            display: none;
        }

        #container_gender-2 #error_gender {
            display: inline;
            float: left;
            margin-left: -220px;
            margin-top: 22px;
            width: 200px;
        }

        .DoozR-Form-Uploaded {
            color: green;
            font-weight: bold;
            font-size: 1em;
        }
    </style>
</head>
<body>
<div class="invalid">
    <?php

    $formError = $formManager->getError('form', array());

    foreach ($formError as $error) {

        echo $formManager->translate(
                $error['error'],
                $error['context']
            ).'<br />';
    };

    ?>
</div>
<?php
echo $formManager;


$registry = $formManager->getRegistry();
pre($registry);
?>

</body>
</html>
