<?php
/***********************************************************************************************************************
 *
 * DEMONSTRATION
 * Service: Database
 *
 **********************************************************************************************************************/

/**
 * Bootstrap
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
 * get the configuration
 */
$config = $registry->config;


/**
 * get the model
 * @var DoozR_Model $model
 */
$model = $registry->model;


/**
 * In DoozR's default configuration (.config) there is a generic database wrapper
 * configured for accessing all kinds of database frameworks in a generic way.
 * Each call is translated to , so there is one level of indirection! But it's a
 * "pregenerated-array"-lookup and this is really fast.
 *
 * The default configurarion is a CouchDB wrapper configuration.
 *
 * Hirarchy!
 *
 * This call:
 * $model                 ->connect('localhost', 5984, 'user', 'password');
 * (DoozR's Database OxM)---(Translation)(Arguments)
 *
 * will become:
 * phpillowConnection::createInstance('localhost', 5984, 'user', 'password');
 */


/**
 * we start by connecting so server on port x with user and password
 */
$connection = $model->connect(
    $config->database->host,
    $config->database->port,
    $config->database->user,
    $config->database->password
);


/**
 * now we open a connection to configured database
 */
$databaseHandle = $model->open($config->database->database);


/**
 * get request object
 */
$request = $registry->front->getRequest();


/**
 * do things
 * ... the following "inline" class definition and ... is just for demo purposes
 */


require_once DOOZR_APP_ROOT.'Class/DoozR/User.php';
require_once DOOZR_APP_ROOT.'Class/DoozR/User/View.php';


/**
 * create and save a blog document
 */
$user = new DoozR_User();
$user->fetchById('user-ben.c_gmx.de');
pred($user);


/**
 * query data by map/reduce through our myBlogView-View
 */
//$result = DoozR_User_View::entries(array('key' => $_GET->email));
//pre($result->rows);

/*
if (isset($result->rows[0])) {
    $user = new DoozR_User($result->rows[0]['value']);
    pre($user->firstname.' '.$user->lastname);
} else {
    echo 'Lookup (view) user by email: "'.$_GET->email.'" failed!';
}
*/


/**
 * close connection
 */
$model->close();


/**
 * close connection
 */
$model->disconnect();

?>
