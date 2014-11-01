<?php

/**
 * Demo View
 */
final class View_Index extends DoozR_Base_View
{
    protected function __renderIndex(array $data)
    {
        /* @var $session DoozR_Session_Service */
        $session = DoozR_Loader_Serviceloader::load('session');

        /* Use this as fingerprint only if you also use an unique session identifier for each user (DoozR default) */
        // A user specific view would pass a user specific value in here // for group pages a group id ...
        $fingerprint = $session->getIdentifier();

        // Render data from model
        return parent::render($data, $fingerprint);
    }
}
