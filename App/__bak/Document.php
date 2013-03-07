<?php

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Model/Lib/Couchdb/Document.php';


class App_Document extends DoozR_Model_Lib_Couchdb_Document
{
    protected static $type = 'user';

    protected $requiredProperties = array('title', 'text');


    public function __construct() {
        $this->properties = array(
        	'title'    => new phpillowStringValidator(),
        	'text'     => new phpillowTextValidator(),
			'comments' => new phpillowDocumentArrayValidator('myBlogComments')
        );

        parent::__construct();
    }


    protected function generateId() {
        return $this->stringToId($this->storage->title);
    }


    protected function getType() {
        return self::$type;
    }
}

?>
