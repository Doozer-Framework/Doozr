<?php

/**
 * Demo Presenter
 */
final class Presenter_Index extends DoozR_Base_Presenter
{
    public function Index()
    {
        // get data from model
        $buffer = $this->model->getData();

        // set data to trigger events in view (and maybe also model)
        $this->setData($buffer);
    }
}
