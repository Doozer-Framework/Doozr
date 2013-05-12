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

//require_once DOOZR_DOCUMENT_ROOT.'Model/Doodi/Couchdb/View/DoodiCouchdbView.class.php';
//require_once DOOZR_DOCUMENT_ROOT.'Model/Doodi/Couchdb/Document/DoodiCouchdbDocument.class.php';

/*
phpillowStringValidator
phpillowTextValidator
phpillowDocumentArrayValidator
*/

class myBlogView extends Doodi_Couchdb_View
{
    protected $viewDefinitions = array(
        'entries' => 'function(doc) {
             if (doc.type == "blog_entry") {
                 emit(doc.title, doc._id);
                 emit([doc._id, 0], doc._id);
                 if (doc.comments) {
                     for ( var i = 0; i < doc.comments.length; ++i ) {
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


class myBlogDocument extends Doodi_Couchdb_Document
{
	protected static $type = 'blog_entry';

	protected $requiredProperties = array(
		'title',
		'text'
	);

	public function __construct()
	{
        $this->properties = array(
            'title'     => new Doodi_Couchdb_String_Validator(),
            'text'      => new Doodi_Couchdb_Text_Validator(),
            'comments'  => new Doodi_Couchdb_Array_Validator(
                'myBlogComments'
            )
        );

        parent::__construct();
	}

	protected function generateId()
	{
		return $this->stringToId($this->storage->title);
	}

	protected function getType()
	{
		return self::$type;
	}
}

/**
 * create and save a blog document
 */
/*
$doc = new myBlogDocument();
$doc->title = 'New blog post';
$doc->text = 'Hello world.';
$doc->save();
*/


/**
 * query data by map/reduce through our myBlogView-View
 */
$result = myBlogView::entries(array('key' => 'New blog post'));


foreach ($result->rows as $row) {
    pre($row);
}


/**
 * close connection
 */
$model->close();


/**
 * close connection
 */
$model->disconnect();

?>
