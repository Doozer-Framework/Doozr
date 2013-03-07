<?php

// include DoozR bootstrapper
require_once '../Framework/Core/DoozR.bootstrap.php';

// instanciate DoozR
$DoozR = DoozR_Core::getInstance();

// get module virtual-filesystem
$vfs = DoozR_Core::module('virtualfilesystem');

// setup test vfs
$vfs->setup('exampleDir');

/**
 * dummy class just for demonstration
 */
$example = new Example('id');

/**
 * check if folder id exist ...
 */
pre($vfs->vfsStreamWrapper->getRoot()->hasChild('id'));

/**
 * create new folder id ...
 */
$example->setDirectory($vfs->url('exampleDir'));

/**
 * check if folder id exist ...
 */
pre($vfs->vfsStreamWrapper->getRoot()->hasChild('id'));

/**********************************************************************************************************************/

class Example
{
    /**
     * id of the example
     *
     * @var  string
     */
    protected $id;
    /**
     * a directory where we do something..
     *
     * @var  string
     */
    protected $directory;

    /**
     * constructor
     *
     * @param  string  $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * sets the directory
     *
     * @param  string  $directory
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory . DIRECTORY_SEPARATOR . $this->id;
        if (file_exists($this->directory) === false) {
            mkdir($this->directory, 0700, true);
        }
    }

    // more source code here...
}

?>
