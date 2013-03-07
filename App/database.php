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
pred(gethostbyaddr('62.143.164.17'));
$hostname = 'ip-62-143-164-17.unitymediagroup.de';


// check if form submitted and valid
if ($form->submitted('register') && $form->valid()) {
    // submitted
    pred('redirect');

} else {
    /*
    if ($form->submitted('contact') && !$form->valid()) {
        //echo 'Error: ',$form->getError('form');
    }
    */

    // create the form
    $form->create('register')                                             // create a new form with name/id = register
         ->method('post')                                                 // set method POST | GET
         ->action($_SERVER['PHP_SELF'])                                   // set action (processing script)
         ->onInvalidToken(DoozR_Form_Module::TOKEN_BEHAVIOR_IGNORE)       // what's to do on invalid token
         ->setAttribute(                                                  // demo for custom attribute
             'onsubmit',
             'javascript:alert(\'about to submit\'); return true;'
         )
         ->i18n($i18n)
         ->setFieldsetBegin('fieldset1', 'Kostenlos registrieren', 'myclass')           // set a new block begin

             // add a label element
             ->add('label')
             ->html('Bla bla bla')
             //->text('Bla bla bla')
             ->done()

             // radio 1 (gender)
             ->add('radio', true, 'leftHalf')
             ->name('gender')
             ->required(true)
             ->preselected(true)
             ->id('gender-1')
             ->value('mrs')
             ->text('Frau')
             ->done()

             // radio 2 (gender)
             ->add('radio', true, 'rightHalf')
             ->name('gender')
             ->required(true)
             ->id('gender-2')
             ->value('mr')
             ->text('Herr')
             ->done()

             // text 1 (firstname)
             ->add('text', true)
             ->name('firstname')
             ->required(true)
             ->id('firstname')
             ->value('z. B. Helene')
             ->label('Vorname')
             ->validation('string')
             ->done()

             // text 2 (lastname)
             ->add('text', true)
             ->name('lastname')
             ->required(true)
             ->id('lastname')
             ->value('z. B. Mustermann')
             ->label('Nachname')
             ->validation('string')
             ->done()

             // text 3 (email)
             ->add('text', true)
             ->name('email')
             ->required(true)
             ->id('email')
             ->value('Enter email address')
             ->label('Email')
             ->validation('emailauth')
             ->validation('link', 'email-confirm')
             ->done()

             // text 4 (email-confirm)
             ->add('text', true)
             ->name('email-confirm')
             ->required(true)
             ->id('email-confirm')
             ->value('Confirm email address')
             ->label('Confirm Email')
             ->validation('email')
             ->validation('link', 'email')
             ->done()

             ->add('submit', true)
             ->name('submit-register')
             ->id('submit-register')
             ->value('Kostenlos registrieren')
             ->done()

         ->setFieldsetEnd();                                              // set a new block end
}

?>
<html>
<head>
<title>DoozR Module Form - Form: register</title>
<style>
    /* general form n1ce pimp ups */
    label {
        cursor: pointer !important;
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

    #container_gender {
        width: 100px;
    }

    #container_gender input {
        width: 70%;
        float: right;
    }

    #container_gender label {
        width: 10%;
        float: left;
    }

    .leftHalf {
        float: left;
    }

    .rightHalf {
        float: right;
    }

    #container_gender input {
        cursor: pointer;
    }

</style>
</head>
<body>
<?php echo $form; ?>
</body>
</html>

