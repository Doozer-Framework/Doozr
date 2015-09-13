<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Demonstration - Model
 *
 * Index.php - Index-Model demonstration of Doozr's Model implementation.
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
 * @subpackage Doozr_App_Model
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

/**
 * Demonstration Model for 'Hello World!'
 */
final class Model_Index extends Doozr_Base_Model
{
    /**
     * Generic data retrieval.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success, otherwise FALSE (we need!!! this as signal from userland back to backend!)
     * @access protected
     */
    protected function __data()
    {
        // Dummy array for iterated output
        $people   = [];
        $people[] = 'John Do';
        $people[] = 'Jane Do';
        $people[] = 'Foo Bar';
        $people[] = 'Bar Baz';

        // Data used as key => value pairs for template engine
        $data = array(
            'title'  => 'Doozr\'s bootstrap environment',
            'year'   => date('Y'),
            'people' => $people,
        );

        // Just store and trigger dispatch to view -> render by observer pattern
        $this->setData($data);
    }

    /**
     * Constructor replacement.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function __tearup()
    {
        // Intentionally left empty.
        // Here it's up to your imagination.
    }

    /**
     * Shutdown event.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function __teardown()
    {
        // Intentionally left empty.
        // Here it's up to your imagination.
    }
}
