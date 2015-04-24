<?php

/**
 * Demonstration View
 */
final class View_Index extends DoozR_Base_View
{
    /**
     * Index: Renderer for view.
     * This demo implementation shows how the render method can be used to intercept the render process (hook)
     * and transform the input so the data is bound to a fingerprint (Should be unique for your case // UUID).
     * In this demonstration we make use of our really strong user bound session identifier to cache data for
     * a specific user.
     *
     * @param array $data The data to render
     *
     * @return bool
     * @throws \DoozR_Base_View_Exception
     */
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
