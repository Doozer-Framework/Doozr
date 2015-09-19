<?php

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Class.php';

class Doozr_Foo extends Doozr_Base_Class_Singleton
{
    public $reg;

    protected function __construct(Doozr_Registry $registry = null)
    {
        //
    }

    public function setReg(Doozr_Registry $registry = null)
    {
        //
    }

    public function getReg()
    {
        return $this->reg;
    }


}

