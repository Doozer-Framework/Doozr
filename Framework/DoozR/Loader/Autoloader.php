<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Loader - Autoloader
 *
 * Autoloader.php - Class-Autoloader of the DoozR Framework.
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
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Loader/Autoloader/Interface.php';

/**
 * DoozR - Loader - Autoloader
 *
 * Autoloader.php - Class-Autoloader of the DoozR Framework.
 *
 * @category   DoozR
 * @package    DoozR_Loader
 * @subpackage DoozR_Loader_Autoloader
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Loader_Autoloader implements DoozR_Loader_Autoloader_Interface
{
    /**
     * The file-extension for the files to be loaded by autoloader
     *
     * @var string The file-extension
     * @access private
     */
    private $_fileExtension = '.php';

    /**
     * The namespace for the files to be loaded by autoloader
     *
     * @var string The namespace
     * @access private
     */
    private $_namespace;

    /**
     * The include path used as base-path when loading files
     *
     * @var string The include path
     * @access private
     */
    private $_includePath;

    /**
     * The namespace separator used when autoloading files
     *
     * @var string The namespace separator
     * @access private
     */
    private $_namespaceSeparator = '\\';

    /**
     * The directory separator for current OS
     *
     * @var string The directory separator
     * @access private
     */
    private $_separator = DIRECTORY_SEPARATOR;

    /**
     * holds classes to include with fullpath
     *
     * @var array
     * @access private
     */
    private $_packages = array();

    /**
     * holds UID of the current instance
     *
     * @var string
     * @access private
     */
    private $_uid;

    /**
     * holds name of the current instance
     *
     * @var string
     * @access private
     */
    private $_name;

    /**
     * holds description of the current instance
     *
     * @var string
     * @access private
     */
    private $_description;

    /**
     * holds priority of the current instance
     *
     * @var string
     * @access private
     */
    private $_priority;


    /**
     * Constructor
     *
     * @param string $namespace   The namespace to use
     * @param string $includePath The include-path to use as base
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __construct($namespace = null, $includePath = null)
    {
        $this->_namespace = $namespace;
        $this->_includePath = $includePath;
    }

    /**
     * Registers this class loader on the SPL autoload stack
     *
     * This method is intend to register this class loader on the SPL autoload stack.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
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
        $namespace = substr($classname, 0, strlen($this->_namespace.$this->_namespaceSeparator));

        // check if requested class must be loaded by this instance of loader
        if ($this->_namespace === null || $this->_namespace.$this->_namespaceSeparator === $namespace) {
            $fileName  = '';
            $namespace = '';

            if (($lastNsPos = strripos($classname, $this->_namespaceSeparator)) !== false) {
                $namespace = substr($classname, 0, $lastNsPos);
                $classname = substr($classname, $lastNsPos + 1);
                $fileName  = str_replace($this->_namespaceSeparator, $this->_separator, $namespace).$this->_separator;
            }

            $fileName .= str_replace('_', $this->_separator, $classname).$this->_fileExtension;

            if ($this->_includePath !== null) {
                $fileName = $this->_includePath.$this->_separator.$fileName;
            }

            //pre(__CLASS__.' -> '.$fileName);

            // check first if file exists and load it
            //if (file_exists($fileName)) {
            @include_once $fileName;
            //}
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
        return $this->_uid;
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
        $uid = (!$uid) ? $uid = md5($this->_name.$this->_namespace.$this->_priority) : $uid;

        $this->_uid = $uid;

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
        return $this->_namespaceSeparator;
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
        $this->_namespaceSeparator = $separator;
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
        return $this->_name;
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
        $this->_name = $name;
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
        return $this->_description;
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
        $this->_description = $description;
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
        return $this->_priority;
    }

    /**
     * Sets the priority
     *
     * This method is intend to set the spl-autoloader stack priority of this instance.
     *
     * @param integer $priority The priority to set (0 = highest possible)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setPriority($priority)
    {
        $this->_priority = $priority;
    }
}

?>
