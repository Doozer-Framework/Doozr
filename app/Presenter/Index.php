<?php


use DoozR\Route\Annotation\Route;


/**
 * Index Presenter
 * from Bootstrap Package
 *
 * @Route(
 *     route="/index/index/popel/123/234/test/super/duper",
 *     method="GET"
 *     presenter="index",
 *     action="index"
 * )
 */
final class Presenter_Index extends DoozR_Base_Presenter
{
    public function indexAction()
    {
        // get data from model
        $buffer = $this->model->getData();

        // set data to trigger events in view (and maybe also model)
        $this->setData($buffer);
    }
}
