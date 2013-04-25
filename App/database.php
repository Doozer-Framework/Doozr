<?php
/***********************************************************************************************************************
 *
 * DEMONSTRATION
 * Module: Database
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

$connection = $model->connect(
    $config->database->host,
    $config->database->port,
    $config->database->user,
    $config->database->password
);


/**
 * open
 */
$connection = $model->open($config->database->database);


/**
 * do things
 */

require_once DOOZR_DOCUMENT_ROOT.'Model/Doodi/Couchdb/View/DoodiCouchdbView.class.php';


class myBlogView extends Doodi_Couchdb_View
{
    protected $viewDefinitions = array(
        // Index blog entries by their title, and list all comments
        'entries' => 'function(doc)
        {
             if (doc.type == "blog_entry") {

                 emit(doc.title, doc._id);

                 emit([doc._id, 0], doc._id);

                 if (doc.comments) {
                     for ( var i = 0; i &lt; doc.comments.length; ++i ) {
                         emit([doc._id, 1], doc.comments[i]);
                     }
                 }
             }
        }',
    );

    protected function getViewName()
    {
        return 'blog_entries';
    }
}


$doc = myBlogView::entries( array( 'key' => 'New blog post' ) );


pred($connection);


?>
