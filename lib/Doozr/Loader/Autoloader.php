<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Loader - Autoloader
 *
 * Autoloader.php - Class-Autoloader of the Doozr Framework.
 *
 * PHP versions 5.5
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
 *
 * Copyright (c) 2005 - 2015, Benjamin Carl - All rights reserved.
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
 *   must display the following acknowledgment: This product includes software
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
 * @category   Doozr
 * @package    Doozr_Loader
 * @subpackage Doozr_Loader_Autoloader
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Loader/Autoloader/Interface.php';

/**
 * Doozr - Loader - Autoloader
 *
 * Autoloader.php - Class-Autoloader of the Doozr Framework.
 *
 * @category   Doozr
 * @package    Doozr_Loader
 * @subpackage Doozr_Loader_Autoloader
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Loader_Autoloader
    implements
    Doozr_Loader_Autoloader_Interface
{
    /**
     * The file-extension for the files to be loaded by autoloader
     *
     * @var string The file-extension
     * @access protected
     */
    protected $fileExtension = '.php';

    /**
     * The namespace for the files to be loaded by autoloader
     *
     * @var string The namespace
     * @access protected
     */
    protected $namespace;

    /**
     * The include path used as base-path when loading files
     *
     * @var string The include path
     * @access protected
     */
    protected $includePath;

    /**
     * The namespace separator used when autoloading files
     *
     * @var string The namespace separator
     * @access protected
     */
    protected $namespaceSeparator = '\\';

    /**
     * The directory separator for current OS
     *
     * @var string The directory separator
     * @access protected
     */
    protected $separator = DIRECTORY_SEPARATOR;

    /**
     * holds classes to include with fullpath
     *
     * @var array
     * @access protected
     */
    protected $packages = [];

    /**
     * holds UID of the current instance
     *
     * @var string
     * @access protected
     */
    protected $uid;

    /**
     * holds name of the current instance
     *
     * @var string
     * @access protected
     */
    protected $name;

    /**
     * holds description of the current instance
     *
     * @var string
     * @access protected
     */
    protected $description;

    /**
     * holds priority of the current instance
     *
     * @var string
     * @access protected
     */
    protected $priority;


    /**
     * Constructor.
     *
     * @param string $namespace   The namespace to use
     * @param string $includePath The include-path to use as base
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \Doozr_Loader_Autoloader
     * @access public
     */
    public function __construct($namespace = null, $includePath = null)
    {
        $this->namespace = $namespace;
        $this->includePath = $includePath;
    }

    /**
     * Registers this class loader on the SPL autoload stack
     *
     * This method is intend to register this class loader on the SPL autoload stack.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success, otherwise FALSE
     * @access public
     */
    public function register()
    {
        return spl_autoload_register(
            array($this, 'load')
        );
    }

    /**
     * Loads the given class or interface
     *
     * This method is intend to load the given class or interface.
     *
     * @param string $classname The name of the class to load.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function load($classname)
    {
        // get namespace from classname
        $namespace = substr($classname, 0, strlen($this->namespace.$this->namespaceSeparator));

        // check if requested class must be loaded by this instance of loader
        if ($this->namespace === null || $this->namespace.$this->namespaceSeparator === $namespace) {
            $fileName  = '';
            $namespace = '';

            if (($lastNsPos = strripos($classname, $this->namespaceSeparator)) !== false) {
                $namespace = substr($classname, 0, $lastNsPos);
                $classname = substr($classname, $lastNsPos + 1);
                $fileName  = str_replace($this->namespaceSeparator, $this->separator, $namespace).$this->separator;
            }

            $fileName .= str_replace('_', $this->separator, $classname).$this->fileExtension;

            if ($this->includePath !== null) {
                $fileName = $this->includePath.$this->separator.$fileName;
            }

            // check first if file exists and load it -> @... suppress is fasted solution ;)
            @include_once $fileName;
        }
    }

    /**
     * Returns the active UID
     *
     * This method is intend to return the active UID.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The UID
     * @access public
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Sets the unique-identifier of this autoloader
     *
     * This method is intend to set the unique-identifiert of this autoloader instance.
     *
     * @param mixed $uid The unique ID to use for this instance, if no UID given it creates and returns one
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The active UID
     * @access public
     */
    public function setUid($uid = false)
    {
        // generate UID by config if not given
        $uid = (!$uid) ? $uid = md5($this->name.$this->namespace.$this->priority) : $uid;

        $this->uid = $uid;

        return $uid;
    }

    /**
     * Returns the active namespace separator
     *
     * This method is intend to return the namespace separator used for loading classes.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The currently active namespace separator
     * @access public
     */
    public function getNamespaceSeparator()
    {
        return $this->namespaceSeparator;
    }

    /**
     * Sets the namespace separator
     *
     * This method is intend to set the namespace separator used for loading classes.
     *
     * @param string $separator The separator to use
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setNamespaceSeparator($separator)
    {
        $this->namespaceSeparator = $separator;
    }

    /**
     * Returns the active name
     *
     * This method is intend to return the active name.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The name
     * @access public
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     *
     * This method is intend to set the name of this instance
     *
     * @param string $name The name to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the active description
     *
     * This method is intend to return the active description.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The description
     * @access public
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * This method is intend to set the description of this instance.
     *
     * @param string $description The description to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Returns the active priority
     *
     * This method is intend to return the active priority of the autoloader instance
     * on the spl stack.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The currently active namespace separator
     * @access public
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Sets the priority
     *
     * This method is intend to set the spl-autoloader stack priority of this instance.
     *
     * @param int $priority The priority to set (0 = highest possible)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }
}
