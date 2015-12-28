<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace App\Model;

/**
 * Doozr - Demonstration - Model.
 *
 * Index.php - Index-Model demonstration of Doozr's Model implementation.
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
 * @link       http://clickalicious.github.com/DoozR/
 */
final class Index extends \Doozr_Base_Model
{
    /**
     * Magic & generic data delivery.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE (we need!!! this as signal from userland back to backend!)
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
        $data = [
            'title'  => 'Doozr\'s bootstrap environment',
            'year'   => date('Y'),
            'people' => $people,
        ];

        // Just store and trigger dispatch to view -> render by observer pattern
        $this->setData($data);

        return true;
    }

    /**
     * Constructor replacement.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool
     */
    protected function __tearup()
    {
        // Intentionally left empty.
        // Here it's up to your imagination.

        // Must return TRUE always!
        return true;
    }

    /**
     * Shutdown event.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function __teardown()
    {
        // Intentionally left empty.
        // Here it's up to your imagination.
    }
}
