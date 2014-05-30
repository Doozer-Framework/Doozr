<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR-Loader-Autoloader-Interface
 *
 * Interface.php - Interface for DoozR-Compatible Autoloaders.
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2013, Benjamin Carl - All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * - All advertising materials mentioning features or use of this software
 *   must display the following acknowledgement: This product includes software
 *   developed by Benjamin Carl and other contributors.
 * - Neither the name Benjamin Carl nor the names of other contributors
 *   may be used to endorse or promote products derived from this
 *   software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * Please feel free to contact us via e-mail: opensource@clickalicious.de
 *
 * @category   DoozR
 * @package    DoozR_Loader
 * @subpackage DoozR_Loader_Autoloader
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2011 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

/**
 * DoozR-Loader-Autoloader-Interface
 *
 * Interface for DoozR-Compatible Autoloaders. This interface is the blueprint for
 * Autoloader-Classes used by our SPL-Facade.
 *
 * @category   DoozR
 * @package    DoozR_Loader
 * @subpackage DoozR_Loader_Autoloader
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2011 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
interface DoozR_Loader_Autoloader_Interface
{
    /**
     * registers this class loader on the SPL autoload stack
     *
     * This method is intend to register this class loader on the SPL autoload stack.
     *
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function register();

    /**
     * loads the given class or interface
     *
     * This method is intend to load the given class or interface.
     *
     * @param string $classname The name of the class to load.
     *
     * @return void
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function load($classname);

    /**
     * returns the active UID
     *
     * This method is intend to return the active UID.
     *
     * @return string The UID
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function getUid();

    /**
     * sets the unique-identifier of this autoloader
     *
     * This method is intend to set the unique-identifiert of this autoloader instance.
     *
     * @param mixed $uid The unique ID to use for this instance, if no UID given it creates and returns one
     *
     * @return string The active UID
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setUid($uid = false);

    /**
     * returns the active namespace separator
     *
     * This method is intend to return the namespace separator used for loading classes.
     *
     * @return string The currently active namespace separator
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function getNamespaceSeparator();

    /**
     * sets the namespace separator
     *
     * This method is intend to set the namespace separator used for loading classes.
     *
     * @param string $separator The separator to use
     *
     * @return void
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setNamespaceSeparator($separator);

    /**
     * returns the active name
     *
     * This method is intend to return the active name.
     *
     * @return string The name
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function getName();

    /**
     * sets the name
     *
     * This method is intend to set the name of this instance
     *
     * @param string $name The name to set
     *
     * @return void
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setName($name);

    /**
     * returns the active description
     *
     * This method is intend to return the active description.
     *
     * @return string The description
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function getDescription();

    /**
     * sets the description
     *
     * This method is intend to set the description of this instance.
     *
     * @param string $description The description to set
     *
     * @return void
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setDescription($description);

    /**
     * returns the active priority
     *
     * This method is intend to return the active priority of the autoloader instance
     * on the spl stack.
     *
     * @return string The currently active namespace separator
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function getPriority();

    /**
     * sets the priority
     *
     * This method is intend to set the spl-autoloader stack priority of this instance.
     *
     * @param integer $priority The priority to set (0 = highest possible)
     *
     * @return void
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setPriority($priority);
}
