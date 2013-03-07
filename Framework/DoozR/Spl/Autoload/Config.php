<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR-Loader-Autoloader-Spl-Config
 *
 * DoozRLoaderAutoloaderSplConfig.php - Config-Class for DoozR's SPL-Autoloader-Facade.
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
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.
    'DoozR/Loader/Autoloader/Spl/Config/Interface/DoozRAutoloadSplConfigInterface.php';

/**
 * DoozR-Loader-Autoloader-Spl-Config
 *
 * Config-Class for DoozR's SPL-Autoloader-Facade.
 * Instances of this class are used by our Spl-Facade to register a new Autoloader.
 *
 * @category   DoozR
 * @package    DoozR_Loader
 * @subpackage DoozR_Loader_Autoloader
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2011 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Loader_Autoloader_Spl_Config implements DoozR_Autoload_Spl_Config_Interface
{
    /**
     * holds the unique-id of the Autoloader
     * (used to identify the Autoloader later outside the SPL-Facade)
     *
     * @var string
     * @access private
     */
    private $_uId;

    /**
     * holds the name of the Autoloader
     *
     * @var string
     * @access private
     */
    private $_name;

    /**
     * holds the priority of the Autolaoder
     * (used to [re]order the SPL-List of Autoloaders)
     *
     * @var integer
     * @access private
     */
    private $_priority = null;

    /**
     * holds the description of the Autoloader
     *
     * @var string
     * @access private
     */
    private $_description;

    /**
     * holds the file-extensions
     * (used by SPL-Facade to setup the list of file-extension indexed by SPL)
     *
     * @var array
     * @access private
     */
    private $_extension = array();

    /**
     * holds the (optional) classname containing the Autoloader-Method (Function)
     *
     * @var string
     * @access private
     */
    private $_class = null;

    /**
     * holds the name of the Autoloader-Method (Function)
     *
     * @var string
     * @access private
     */
    private $_method;

    /**
     * holds a list of paths used by Autoloader for file-lookup (class-files)
     *
     * @var array
     * @access private
     */
    private $_path = array();

    /**
     * holds the information if Autoloader-Method is standalone (procedural Function) or part of a class
     *
     * @var boolean
     * @access private
     */
    private $_isClass = false;


    /**
     * setter for $_uId
     *
     * This method is intend as setter for $_uId.
     *
     * @param string $uId An unique-Id to identify the Autoloader later from outside the SPL-Facade.
     *
     * @return  boolean true if setting was succesful, otherwise false
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function setUid($uId)
    {
        return ($this->_uId = $uId);
    }


    /**
     * getter for $_uId
     *
     * This method is intend as getter for $_uId.
     *
     * @return  string The previous setted unique-Id of the Autoloader
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getUid()
    {
        return $this->_uId;
    }


    /**
     * setter for $_name
     *
     * This method is intend as setter for $_name.
     *
     * @param string $name A name for the Autoloader.
     *
     * @return  boolean true if setting was succesful, otherwise false
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function setName($name)
    {
        return ($this->_name = $name);
    }


    /**
     * getter for $_name
     *
     * This method is intend as getter for $_name.
     *
     * @return  string The previous setted name of the Autoloader
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getName()
    {
        return $this->_name;
    }


    /**
     * setter for $_priority
     *
     * This method is intend as setter for $_priority.
     *
     * @param integer $priority A priority for the Autoloader. An integer between 0 and X (0 = highest priority).
     *
     * @return  boolean true if setting was succesful, otherwise false
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function setPriority($priority)
    {
        return ($this->_priority = $priority);
    }


    /**
     * getter for $_priority
     *
     * This method is intend as getter for $_priority.
     *
     * @return  integer The previous setted priority of the Autoloader
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getPriority()
    {
        return $this->_priority;
    }


    /**
     * setter for $_description
     *
     * This method is intend as setter for $_description.
     *
     * @param string $description A description for the Autoloader.
     *
     * @return  boolean true if setting was succesful, otherwise false
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function setDescription($description)
    {
        return ($this->_description = $description);
    }


    /**
     * getter for $_description
     *
     * This method is intend as getter for $_description.
     *
     * @return  string The previous setted description of the Autoloader
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getDescription()
    {
        return $this->_description;
    }


    /**
     * setter for a single file-extension (added to $_extension)
     *
     * This method is intend as setter for a single file-extension (added to $_extension).
     *
     * @param string $extension A file-extension used by the Autoloader.
     *
     * @return  boolean true if setting was succesful, otherwise false
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function addExtension($extension)
    {
        // assume success
        $result = true;

        $extension = trim($extension);

        (!preg_match('/^\./', $extension)) ? $extension = '.'.$extension : '';

        if ($this->_extension) {
            if (!in_array($extension, $this->_extension)) {
                $result = $result && ($this->_extension[] = $extension);
            }
        } else {
            $result = $result && ($this->_extension[] = $extension);
        }

        return $result;
    }


    /**
     * setter for a list of (array) file-extensions (added to $_extension)
     *
     * This method is intend as setter for a list of (array) file-extensions (added to $_extension).
     *
     * @param array $extensions A list of file-extensions used by the Autoloader.
     *
     * @return  boolean true if setting was succesful, otherwise false
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function addExtensions(array $extensions)
    {
        // assume success
        $result = true;

        foreach ($extensions as $extension) {
            $result = $result && $this->addExtension($extension);
        }

        return $result;
    }


    /**
     * getter for $_extension
     *
     * This method is intend as getter for $_extension.
     *
     * @return  array The previous setted extension(s) used by the Autoloader
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getExtension()
    {
        return $this->_extension;
    }


    /**
     * setter for $_class
     *
     * This method is intend as setter for a class containing the Autoloader-Method (Function).
     *
     * @param string $class A name of a class containing the Autoloader-Method used by the Autoloader.
     *
     * @return  boolean true if setting was succesful, otherwise false
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function setClass($class)
    {
        // is this autoloader class based or simple method
        $this->_isClass = true;

        // store classname
        return ($this->_class = $class);
    }


    /**
     * getter for $_class
     *
     * This method is intend as getter for $_class.
     *
     * @return  string The previous setted class used by the Autoloader
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getClass()
    {
        return $this->_class;
    }


    /**
     * returns is-class status of the Autoloader-Config
     *
     * This method is intend to return the is-class status of the Autoloader-Config.
     *
     * @return  boolean true is a Class containing the Autoloader-Method (Function), otherwise false (proced. Function)
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function isClass()
    {
        return (!is_null($this->_class));
    }


    /**
     * setter for $_method
     *
     * This method is intend as setter for a method (function) used as "loader" by the Autoloader.
     *
     * @param string $method A method-name used by the Autoloader.
     *
     * @return  boolean true if setting was succesful, otherwise false
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function setMethod($method)
    {
        // is this autoloader class based or simple method
        $this->_isClass = ($this->_class);

        // store method
        return ($this->_method = $method);
    }


    /**
     * getter for $_method
     *
     * This method is intend as getter for $_method.
     *
     * @return  string The previous setted method used by the Autoloader
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getMethod()
    {
        return $this->_method;
    }


    /**
     * setter for $_method (alias for setMethod())
     *
     * This method is intend as setter for a method (function) used as "loader" by the Autoloader.
     *
     * @param string $function A function-name used by the Autoloader.
     *
     * @return  boolean true if setting was succesful, otherwise false
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function setFunction($function)
    {
        return ($this->setMethod($function));
    }


    /**
     * getter for $_method (alias for getMethod())
     *
     * This method is intend as getter for $_method.
     *
     * @return  string The previous setted method used by the Autoloader
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getFunction()
    {
        return $this->getMethod();
    }


    /**
     * setter for $_path
     *
     * This method is intend as setter for a path or a list of (array) paths used for lookup by the Autoloader.
     *
     * @param mixed $path A single path (string) or a list of paths (array) used by the Autoloader for lookup for files.
     *
     * @return  boolean true if setting was succesful, otherwise false
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function setPath($path)
    {
        // check given type
        if (is_array($path)) {
            $result = ($this->_path = $path);
        } else {
            $result = ($this->_path = array($path));
        }

        // return result of operation
        return $result;
    }


    /**
     * setter for $_path
     *
     * This method is intend as setter for a path. It adds a single path or a list of (array) paths to the already
     * exiting path(s).
     *
     * @param mixed $path A single path (string) or a list of paths (array) used by the Autoloader for lookup for files.
     *
     * @return  boolean true if setting was succesful, otherwise false
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function addPath($path)
    {
        // assume operation fails
        $result = false;

        // check given type
        if (is_array($path)) {
            $result = array_unique(array_merge($this->_path, $path));
        } else {
            // check if not already exist
            if (!in_array($path, $this->_path)) {
                $result = ($this->_path[] = $path);
            }
        }

        // return status
        return $result;
    }


    /**
     * getter for $_path
     *
     * This method is intend as getter for $_path.
     *
     * @return  array The previous setted path(s) used by the Autoloader
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getPath()
    {
        return $this->_path;
    }
}

?>
