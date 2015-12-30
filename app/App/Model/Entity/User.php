<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace App\Model\Entity;

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
 * @link       http://clickalicious.github.com/Doozr/
 */

/**
 * @Entity @Table(name="user")
 **/
final class User
{
    /** @Id @Column(type="string") **/
    protected $Host;

    /** @Column(type="string") **/
    protected $User;

    /** @Column(type="string") **/
    protected $Password;

    public function getUser()
    {
        return $this->User;
    }
}
