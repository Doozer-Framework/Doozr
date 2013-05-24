<?php

class DoozR_User extends Doodi_Couchdb_Document
{
    // BEGIN
	protected static $type = 'user';

	protected $requiredProperties = array(
		'firstname',
		'lastname',
	    'email'
	);

	public function __construct(array $user = array())
	{
        $this->properties = array(
            'firstname' => new Doodi_Couchdb_Text_Validator(),
            'lastname'  => new Doodi_Couchdb_String_Validator(),
            'email'     => new Doodi_Couchdb_Email_Validator(),
            'address'   => new Doodi_Couchdb_Array_Validator(),
            'lastlogin' => new Doodi_Couchdb_Date_Validator()
        );

        parent::__construct();

		foreach ($user as $key => $value) {
		    //
		    if (substr($key, 0, 1) != '_' && $key != 'type') {
		        $this->{$key} = $value;
		    }
	    }
	}

	protected function generateId()
	{
		return $this->stringToId($this->storage->email);
	}

	protected function getType()
	{
		return self::$type;
	}

	// END

	// BEGIN

	protected $user;

	/*
    public function __get($property)
    {
        $value = null;

        if (isset($this->user[$property])) {
            $value = $this->user[$property];
        }

        return $value;
    }
	*/
}

?>
