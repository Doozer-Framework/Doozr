<?php


require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Class.php';


final class Doozr_Di_Injection_Matrix extends Doozr_Base_Class
{
    private $constructorInjections = [];

    private $methodInjections = [];

    private $propertyInjections = [];


    public function addConstructorInjection($position, $instance)
    {
        $this->constructorInjections[$position-1] = $instance;
    }

    public function addSetterInjection($method, $instance)
    {
        $this->methodInjections[$method] = $instance;
    }

    public function addPropertyInjection($property, $instance)
    {
        $this->propertyInjections[$property] = $instance;
    }
}


