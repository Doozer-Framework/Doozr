<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace App;

/**
 * Doozr - Demonstration - View
 *
 * Test.php - Test-View demonstration of Doozr's View implementation.
 *
 * PHP versions 5.5
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
 *
 * Copyright (c) 2005 - 2015, Benjamin Carl - All rights reserved.
 *
 * @category   Doozr
 * @package    Doozr_App
 * @subpackage Doozr_App_View
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

/**
 * Demonstration View for 'Hello World!'
 */
final class View_Test extends \Doozr_View_Web
{
    /**
     * Renderer for Test
     *
     * This demo implementation shows how the render method can be used to intercept the render process (hook)
     * and transform the input so the data is bound to a fingerprint (Should be unique for your case // UUID).
     * In this demonstration we make use of our really strong user bound session identifier to cache data for
     * a specific user.
     *
     * @param array $data The data to render
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool
     * @access protected
     */
    protected function __renderTest(array $data)
    {
        // Render data from model
        return parent::render($data);
    }
}
