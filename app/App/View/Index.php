<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace App\View;

/**
 * Doozr - Demonstration - View.
 *
 * Index.php - Index-View demonstration of Doozr's View implementation.
 *
 * PHP versions 5.5
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
 *
 * Copyright (c) 2005 - 2016, Benjamin Carl - All rights reserved.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
final class Index extends \Doozr_View_Web
{
    /**
     * Renderer for Index.
     *
     * This demo implementation shows how the render method can be used to intercept the render process (hook)
     * and transform the input so the data is bound to a fingerprint (Should be unique for your case // UUID).
     * In this demonstration we make use of our really strong user bound session identifier to cache data for
     * a specific user.
     *
     * @param array $data The data to render
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool
     */
    protected function __renderIndex(array $data)
    {
        /* @var $session \Doozr_Session_Service */
        $session = \Doozr_Loader_Serviceloader::load('session');

        /*
         * A user specific view would pass a user specific value in here for group pages a group id and so on ...
         * Use this as fingerprint only if you also use an unique session identifier for each user (Doozr default).
         */
        $fingerprint = $session->getIdentifier();

        // Get I18n service instance
        $i18nService = \Doozr_Loader_Serviceloader::load('i18n', $this->getRegistry()->getConfiguration());

        // Render data from model
        return $this->render($data, $fingerprint, $i18nService);
    }
}
