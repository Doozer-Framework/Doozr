<?php

//require_once '../Framework/DoozR/Model/Lib/Couchdb/classes/'

class myBlogDocument extends phpillowDocument {

	protected static $type = 'blog_entry';

	protected $requiredProperties = array('title', 'text');


	public function __construct() {
		$this->properties = array(
			'title'    => new phpillowStringValidator(),
			'text'     => new phpillowTextValidator(),
			'comments' => new phpillowDocumentArrayValidator('myBlogComments'),
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

?>
