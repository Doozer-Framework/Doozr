<?php

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
 * Get the model layer easily through registry - painless
 */
$model  = $registry->model;
$config = $registry->config;


/**
 * here we 1st retrieve request through front
 * over registry and 2nd perform transformation
 * array => object ...
 */
$request = $registry->front->getRequest();
$request->GET();
$key = (isset($_GET->key)) ? $_GET->key : '';

/*
 pre($request->isGet());
pre($request->isPost());
pre($request->getGet());
pre($request->getPost());
pre($request->getRequestHeader());
pre($request->getRequestAsString());
pre($request->getUrl());
pre($request->isSsl());
pre($request->getProtocol());
pre($request->getRequestMethod());
pre($request->getRequestOrder());

*/

//$request->transform($request->getRequestMethod());
// trigger transformation of $_GET
//$request->GET();
//pre( $_GET->a );


/**
 * connect server
 *
 * The argument order port:ip in the next call to connect() is a
 * demonstration of how the input defaults can be mapped to fit
 * the requirements of a used client library. The field used for
 * this mapping is "argumentMap". It maps arguments as coming
 * from default config of DoozR for example like this:
 * $model->connect($doozr->config->database->default->ip, $doozr->config->database->default->port]
 *
 * than we use:
 *     'argumentMap' => array(
 *         0 => 1,
 *         1 => 0
 *     )
 *
 * in translation array!
 *
 * And the target method will be executed with arguments in changed order (flipped).
 */
$connection = $model->connect('127.0.0.1', 5984);


/**
 * open database for r/w [always]
 */
$model->open(
    // config: { database: 'demo' }
    $config->database->database
);


/**
 * Create a new user
 */
require_once 'Data/Private/Model/App/User.php';

/*
$doc = new App_User();

$doc->salutation = 'Mr.';
$doc->firstname  = 'Benjamin';
$doc->lastname   = 'Carl';
$doc->email      = 'ben.c@gmx.de';

$id = $doc->save();
pre($id);
*/

/**
 * querying a view
 */
require_once 'Data/Private/Model/App/User.php';

$id = 'user-ben.c_gmx.de';
$doc = new App_User($id);



/*
$key = 'user-';

$doc = App_User_View::by_id($key);
pred($doc);
*/

/**
 * fetch a doc
 */
/*
$doc->fetchById('user-ben.c_gmx.de');
pre($doc->revisions[0]['salutation'].' '.$doc->revisions[0]['firstname'].' '.$doc->revisions[0]['lastname'].
' '.$doc->revisions[0]['email']
);

// remove
$doc->delete();
*/

/**
 * close database
 */
$model->close();


/**
 * disconnect from server
 */
$model->disconnect();



$password = DoozR_Loader_Moduleloader::load('password');


//$pwd = $password->generate(DoozR_Password_Module::PASSWORD_USERFRIENDLY_REMEMBER);
//pre($pwd);

$pwd   = '123456123456';
//$hash  = $password->hash($pwd);
$hash  = '$2a$08$MRCwKBSvMBW2KRK0KBOzKuQxUfoe08GLqnT1Id5f0wK/bX4IW.gm6';
$valid = $password->validateAgainstHash($pwd, $hash);


$i18n = DoozR_Loader_Moduleloader::load('i18n', array('de', $registry->config));
//$translator = $i18n->getTranslator();
//$translator->setNamespace('demo');
//$localized = $translator->_('x_books_in_my_y_shelves', array(5, 4));


$form = DoozR_Loader_Moduleloader::load('form', array($i18n));


/**
 * required for MX lookup
 */
$mx = array(
    'ip'       => '62.143.164.17',
    'hostname' => 'ip-62-143-164-17.unitymediagroup.de'
);


if ($form->submitted('register') && $form->valid() && $form->finished()) {

    pre('Form submitted, valid and finished!');

    $dataset = $form->getData(true);
    pred($dataset);
    //pre($dataset[1]['name']);
    //pre($dataset[2]['ustid']);
    exit;

} else {

    // create the form for step 1
    if ($form->getStep() === 1) {

        $form->create('register')
            ->method('post')
            ->action($_SERVER['PHP_SELF'])
            ->step()
            ->steps(2)
            ->onInvalidToken(DoozR_Form_Module::TOKEN_BEHAVIOR_DENY)
            ->i18n($i18n)
            ->setFieldsetBegin('fieldset1', 'Jetzt kostenlos registrieren:', 'myclass')
                // radio 1 (gender)
                ->add('radio', true, 'leftHalf')
                ->name('gender')
                ->required(true)
                ->preselected(true)
                ->id('gender-1')
                ->value('mrs')
                ->label('Frau')
                ->tabindex(1)
                ->validate('value', 'Frau, Herr')
                ->done()

                // radio 2 (gender)
                ->add('radio', true, 'rightHalf')
                ->name('gender')
                ->required(true)
                ->id('gender-2')
                ->value('mr')
                ->label('Herr')
                ->tabindex(2)
                ->validate('value', 'Frau, Herr')
                ->done()

                // name
                ->add('text', true)
                ->label('Name:')
                ->name('name')
                ->id('name')
                //->value('los gehts')
                ->required(true)
                ->tabindex(3)
                ->done()

                // checkbox 1 (agb + privacy)
                ->add('checkbox', true)
                ->name('agb')
                ->required(true)
                ->id('agb')
                ->value('true')
                ->label('Ich akzeptiere die <a href="#">AGB</a> und die <a href="#">Datenschutzbestimmungen</a>')
                ->validate('boolean')
                ->tabindex(7)
                ->done()

                // submit
                ->add('submit', true)
                ->name('submit-register')
                ->id('submit-register')
                ->value('Weiter zu Schritt 2')
                ->tabindex(8)
                ->done()

                // reset
                ->add('reset', true)
                ->name('reset-register')
                ->id('reset-register')
                ->value('abbrechen')
                ->tabindex(9)
                ->done()
            ->setFieldSetEnd();

    } elseif ($form->getStep() === 2) {

        $form->create('register')
            ->method('post')
            ->action($_SERVER['PHP_SELF'])
            ->step()
            ->steps(2)
            ->onInvalidToken(DoozR_Form_Module::TOKEN_BEHAVIOR_DENY)
            ->i18n($i18n)
            ->setFieldsetBegin('fieldset1', 'Nur noch vervollst&auml;ndigen:', 'myclass')
                ->add('text', true)
                ->label('UstId:')
                ->name('ustid')
                ->id('ustid')
                ->required(true)
                ->tabindex(1)
                ->done()

                ->add('label', true)
                ->name('jump')
                ->id('jump')
                ->html('<a href="form.php?DoozR_Form_Module_Step=1">jump jump</a>')
                ->done()

                ->add('submit', true)
                ->name('submit-register')
                ->id('submit-register')
                ->value('Registrierung abschlie&szlig;en')
                ->tabindex(2)
                ->done()
            ->setFieldSetEnd();
    }
}

?>
<html>
<head>
<title>DoozR Module Form - Form: register</title>
<style>
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

    .DoozR_Form_Module_Fieldset_Container {
        float: left;
        margin-bottom: 18px;
        width: 100%;
    }

    .DoozR_Form_Module_Fieldset_Container label,
    .DoozR_Form_Module_Fieldset_Container input {
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

</style>
</head>
<body>
<?php echo $form; ?>
</body>
</html>
