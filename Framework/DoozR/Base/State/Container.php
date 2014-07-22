<?php


require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Class.php';


class DoozR_Base_State_Container extends DoozR_Base_Class
{
    // state object containing state data
    /**
     * @var DoozR_Base_State
     */
    protected $stateObject;


    // State access
    protected function setStateObject(DoozR_Base_State_Interface $stateObject)
    {
        $this->stateObject = $stateObject;
    }

    protected function stateObject(DoozR_Base_State_Interface $stateObject)
    {
        $this->setStateObject($stateObject);
        return $this;
    }

    protected function getStateObject()
    {
        return $this->stateObject;
    }




    public function extractState()
    {
        return $this->getStateObject();
    }

    public function injectState(DoozR_Base_State_Interface $stateObject)
    {
        $this->setStateObject($stateObject);
    }
}
