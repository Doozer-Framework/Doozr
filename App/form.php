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

// create a new form-container which combines the control-layer and the HTML parts
$formManager = new DoozR_Form_Service_FormManager(
    'register',                                                 // The namespace (used for session, I18n, ...)
    $i18n,                                                      // The I18n service instance for translation(s) [DI]
    new DoozR_Form_Service_Element_Input(                       // Input element <- for cloning [DI]
        '',
        $registry->front->getRequest()->getArguments()
    ),
    new DoozR_Form_Service_Element_Form('register'),            // The form element we operate on [DI]
    new DoozR_Form_Service_Store_Session($session),             // The session store [DI]
    new DoozR_Form_Service_Renderer_Native(),                   // A Renderer -> Native = HTML [DI]
    new DoozR_Form_Service_Validate_Validator(),                // A Validator to validate the elements [DI]
    new DoozR_Form_Service_Validate_Error(),                    // A Error object <- for cloning [DI]
    $registry->front->getRequest()->getArguments()              // The currents requests arguments
);


/*
if ($formManager->wasSubmitted()) {
    #$registry->front->getRequest()->FILES();
    #pred( $_FILES->file );
    $arguments = $registry->front->getRequest()->getArguments();
    #pre($arguments);
}
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
    $formManager->getForm()
        ->setMethod('post')
        ->setAction($_SERVER['PHP_SELF'])
        ->setNovalidate()
        ->enableUpload();

    // native implemented methods also chain support
    $formManager->setStep(1);
    $formManager->setSteps(3);
    $formManager->setInvalidTokenBehavior(DoozR_Form_Service_Constant::TOKEN_BEHAVIOR_DENY);
    $formManager->setI18n($i18n);



    if ($formManager->wasJumped() !== false) {
        #pre('we jumped right to step: '.$formManager->getStep());
    }


    // Create a label
    $label = new DoozR_Form_Service_Element_Label('Firstname:');

    // Create an input field
    $element = new DoozR_Form_Service_Element_Input(
        'firstname',
        $registry->front->getRequest()->getArguments(),
        $formManager->getRegistry()
    );

    $element->setId('firstname');
    $element->setType('text');
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
        $error = DoozR_Form_Service_Validate_Constant::ERROR_PREFIX.$formManager->getError(
                'firstname', null
            )[0]['error'];
    }

    $message = new DoozR_Form_Service_Element_Message(
        $formManager->translate(
            $error
        )
    );
    $message->setStyle('color:red;');


    // Create a group with: Label, Element, Message
    $group1 = new DoozR_Form_Service_Element_Group(
        $label,
        $element,
        $message
    );



    // Create a label
    $label1 = new DoozR_Form_Service_Element_Label('Male:');
    $label2 = new DoozR_Form_Service_Element_Label('Female:');

    // Create an input field
    $element1 = new DoozR_Form_Service_Element_Radio(
        'gender',
        $registry->front->getRequest()->getArguments(),
        $formManager->getRegistry()
    );
    $element1->setId('gender1');
    $element1->setValue(
        'male'
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
    $element2 = new DoozR_Form_Service_Element_Radio(
        'gender',
        $registry->front->getRequest()->getArguments(),
        $formManager->getRegistry()
    );

    $element2->setId('gender2');
    $element2->setValue(
        'female'
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

    $message = new DoozR_Form_Service_Element_Message(
        $formManager->translate(
            $error
        )
    );
    $message->setStyle('color:red;');

    $group2 = new DoozR_Form_Service_Element_Group(
        array($label1, $label2),
        array($element1, $element2),
        $message
    );


    // Create a label
    $label1 = new DoozR_Form_Service_Element_Label('Support:');
    $label2 = new DoozR_Form_Service_Element_Label('Question:');

    // Create an input field
    $element1 = new DoozR_Form_Service_Element_Checkbox(
        'interest[]',
        $registry->front->getRequest()->getArguments(),
        $formManager->getRegistry()
    );

    $element1->setId('interest1');

    $element1->setValue(
        'support'
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
    $element2 = new DoozR_Form_Service_Element_Checkbox(
        'interest[]',
        $registry->front->getRequest()->getArguments(),
        $formManager->getRegistry()
    );

    $element2->setId('interest2');

    $element2->setValue(
        'question'
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

    $message = new DoozR_Form_Service_Element_Message(
        $formManager->translate(
            $error
        )
    );
    $message->setStyle('color:red;');

    $group3 = new DoozR_Form_Service_Element_Group(
        array($label1, $label2),
        array($element1, $element2),
        $message
    );


    // define label
    $label1 = new DoozR_Form_Service_Element_Label('Datei zum upload wählen:');

    // Create an input field
    $element1 = new DoozR_Form_Service_Element_File(
        'file',
        $registry->front->getRequest()->getArguments(),
        $formManager->getRegistry()
    );

    $element1->setValue(
        $formManager->getValue('file')
    );

    $element1->setMaxFilesize(
        'auto'
    );

    $element1->setId('file');

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

    $element1->addValidation(
        DoozR_Form_Service_Validate_Constant::VALUE,
        'MeinAuto.txt'
    );

   // Create a message
    if ($formManager->getError('file') === null) {
        $error = null;
    } else {
        $error = DoozR_Form_Service_Validate_Constant::ERROR_PREFIX.$formManager->getError(
                'file', null
            )[0]['error'];
    }

    $message = new DoozR_Form_Service_Element_Message(
        $formManager->translate(
            $error,
            array(500, 800)     // min-size, max-size,
        )
    );
    $message->setStyle('color:red;');

    $group4 = new DoozR_Form_Service_Element_Group(
        $label1,
        $element1,
        $message
    );


    // Create an input element "button"
    $element = new DoozR_Form_Service_Element_Input(
        'submit',
        $registry->front->getRequest()->getArguments(),
        $formManager->getRegistry()
    );
    $element->setId('submit');
    $element->setType('submit');
    $element->setValue('Weiter zu Schritt 2/3');

    // Create a fieldset
    $fieldset = new DoozR_Form_Service_Element_Fieldset(
        new DoozR_Form_Service_Element_Legend('This is an example legend ...')
    );

    $fieldset->add($group1);
    $fieldset->add($group2);
    $fieldset->add($group3);
    $fieldset->add($group4);

    $fieldset->add($element);

    // Add the fieldset to the form -> looks weird ... why to put the form in first ...
    $formManager->getForm()->add($fieldset);

    // render the whole stuff
    #$html = $formManager->render();

} elseif ($formManager->getStep() === 2) {

    // magic sets on form setMethod('post') becomes ->setAttribute('method', 'post');
    $formManager->getForm()
        ->setMethod('post')
        ->setAction($_SERVER['PHP_SELF'])
        ->setNovalidate()
        ->enableUpload();

    // native implemented methods also chain support
    $formManager->setStep(2);
    $formManager->setSteps(3);
    $formManager->setInvalidTokenBehavior(DoozR_Form_Service_Constant::TOKEN_BEHAVIOR_DENY);
    $formManager->setI18n($i18n);


    // Create a label
    $label = new DoozR_Form_Service_Element_Label('Nummer:');

    // Create an input field
    $element = new DoozR_Form_Service_Element_Input(
        'number',
        $registry->front->getRequest()->getArguments(),
        $formManager->getRegistry()
    );

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

    $message = new DoozR_Form_Service_Element_Message(
        $formManager->translate(
            $error
        )
    );
    $message->setStyle('color:red;');


    // Create a group with: Label, Element, Message
    $group1 = new DoozR_Form_Service_Element_Group(
        $label,
        $element,
        $message
    );

    // Create a fieldset
    $fieldset = new DoozR_Form_Service_Element_Fieldset(
        new DoozR_Form_Service_Element_Legend('Please provide us some more information about you ...')
    );

    // Create an input field
    $element = new DoozR_Form_Service_Element_Html(
        'jump',
        $registry->front->getRequest()->getArguments(),
        $formManager->getRegistry()
    );

    $element->setTag('a');
    $element->setAttribute('href', $_SERVER['PHP_SELF'].'?DoozR_Form_Service_Jump=1');
    $element->setInnerHtml('Zurück zu Schritt 1/3');

    $group2 = new DoozR_Form_Service_Element_Group(
        null,
        $element,
        null
    );

    // Create an input element "button"
    $element = new DoozR_Form_Service_Element_Input(
        'submit',
        $registry->front->getRequest()->getArguments(),
        $formManager->getRegistry()
    );
    $element->setId('submit');
    $element->setType('submit');
    $element->setValue('Weiter zu Schritt 3/3');

    $fieldset->add($group1);
    $fieldset->add($group2);
    $fieldset->add($element);

    // Add the fieldset to the form -> looks weird ... why to put the form in first ...
    $formManager->getForm()->add($fieldset);

} elseif ($formManager->getStep() === 3) {


    // magic sets on form setMethod('post') becomes ->setAttribute('method', 'post');
    $formManager->getForm()
        ->setMethod('post')
        ->setAction($_SERVER['PHP_SELF'])
        ->setNovalidate();

    // native implemented methods also chain support
    $formManager->setStep(3);
    $formManager->setSteps(3);
    $formManager->setInvalidTokenBehavior(DoozR_Form_Service_Constant::TOKEN_BEHAVIOR_DENY);
    $formManager->setI18n($i18n);


    // Create an input field
    $element1 = new DoozR_Form_Service_Element_Html(
        'jump',
        $registry->front->getRequest()->getArguments(),
        $formManager->getRegistry()
    );

    $element1->setTag('a');
    $element1->setAttribute('href', $_SERVER['PHP_SELF'].'?DoozR_Form_Service_Jump=2');
    $element1->setInnerHtml('Zurück zu Schritt 2/3');

    // Create a fieldset
    $fieldset = new DoozR_Form_Service_Element_Fieldset(
        new DoozR_Form_Service_Element_Legend('Have a look at all collected Data ...')
    );


    $element3 = new DoozR_Form_Service_Element_Html(
        'details',
        $registry->front->getRequest()->getArguments(),
        $formManager->getRegistry()
    );

    $element3->setTag('div');
    $element3->setAttribute('id', 'wurstbrot');
    $element3->setInnerHtml(
        nl2br(var_export($formManager->getRegistry('data'), true))
    );

    // Create an input element "button"
    $element2 = new DoozR_Form_Service_Element_Input(
        'submit',
        $registry->front->getRequest()->getArguments(),
        $formManager->getRegistry()
    );
    $element2->setId('submit');
    $element2->setType('submit');
    $element2->setValue('abschliessen');

    $fieldset->add($element1);
    $fieldset->add($element3);
    $fieldset->add($element2);

    // Add the fieldset to the form -> looks weird ... why to put the form in first ...
    $formManager->getForm()->add($fieldset);
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
