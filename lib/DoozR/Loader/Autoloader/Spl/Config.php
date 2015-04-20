<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Loader - Autoloader - Spl - Config
 *
 * Config.php - Config-Class for DoozR's SPL-Autoloader-Facade
 *
 * PHP versions 5.4
 *
 * LICENSE:
 * DoozR - The lightweight PHP-Framework for high-performance websites
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
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Loader/Autoloader/Spl/Config/Interface.php';

/**
 * DoozR - Loader - Autoloader - Spl - Config
 *
 * Config-Class for DoozR's SPL-Autoloader-Facade
 *
 * @category   DoozR
 * @package    DoozR_Loader
 * @subpackage DoozR_Loader_Autoloader
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Loader_Autoloader_Spl_Config implements DoozR_Autoload_Spl_Config_Interface
{
    /**
     * The unique-id of the Autoloader
     * (used to identify the Autoloader later outside the SPL-Facade)
     *
     * @var string
     * @access protected
     */
    protected $uid;

    /**
     * Holds the name of the Autoloader
     *
     * @var string
     * @access protected
     */
    protected $namespace;

    /**
     * Holds the priority of the Autolaoder
     * (used to [re]order the SPL-List of Autoloaders)
     *
     * @var int
     * @access protected
     */
    protected $priority;

    /**
     * Holds the description of the Autoloader
     *
     * @var string
     * @access protected
     */
    protected $description;

    /**
     * Holds the file-extensions
     * (used by SPL-Facade to setup the list of file-extension indexed by SPL)
     *
     * @var array
     * @access protected
     */
    protected $extension = array();

    /**
     * Holds the (optional) classname containing the Autoloader-Method (Function)
     *
     * @var string
     * @access protected
     */
    protected $class;

    /**
     * Holds the name of the Autoloader-Method (Function)
     *
     * @var string
     * @access protected
     */
    protected $method = 'load';

    /**
     * Holds a list of paths used by Autoloader for file-lookup (class-files)
     *
     * @var array
     * @access protected
     */
    protected $path = array();

    /**
     * Holds the information if Autoloader-Method is standalone (procedural Function) or part of a class
     *
     * @var bool
     * @access protected
     */
    protected $isClass = false;

    /**
     * Holds the information if Autoloader-Method is standalone (procedural Function) or part of a class
     *
     * @var bool
     * @access protected
     */
    protected $isLoader = true;

    /**
     * The namespace for the Autoloader
     *
     * @var string
     * @access protected
     */
    protected $namespaceSeparator;

    /**
     * The directory separator for current OS
     *
     * @var string The directory separator
     * @access protected
     */
    protected $separator = DIRECTORY_SEPARATOR;


    /**
     * Setter for $namespaceSeparator
     *
     * This method is intend as Setter for $namespaceSeparator.
     *
     * @param string $namespace The namespace of the loader
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Loader_Autoloader_Spl_Config
     * @access public
     */
    public function setNamespaceSeparator($namespace)
    {
        $this->namespaceSeparator = $namespace;
        return $this;
    }

    /**
     * Getter for $namespaceSeparator
     *
     * This method is intend as Getter for $uid.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The previous setted unique-Id of the Autoloader
     * @access public
     */
    public function getNamespaceSeparator()
    {
        return $this->namespaceSeparator;
    }

    /**
     * Setter for $uid
     *
     * This method is intend as Setter for $uid.
     *
     * @param string $uId An unique-Id to identify the Autoloader later from outside the SPL-Facade.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Loader_Autoloader_Spl_Config Current instance for chaining
     * @access public
     */
    public function setUid($uId)
    {
        $this->uid = $uId;
        return $this;
    }

    /**
     * Getter for $uid
     *
     * This method is intend as Getter for $uid.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The previous setted unique-Id of the Autoloader
     * @access public
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Setter for $namespace
     *
     * This method is intend as Setter for $namespace.
     *
     * @param string $namespace A namespace for the Autoloader.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Loader_Autoloader_Spl_Config Current instance for chaining
     * @access public
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        $this->setUid(md5($namespace.microtime()));

        return $this;
    }

    /**
     * Getter for $namespace
     *
     * This method is intend as Getter for $namespace.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The previous setted name of the Autoloader
     * @access public
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Setter for $priority
     *
     * This method is intend as Setter for $priority.
     *
     * @param int $priority A priority for the Autoloader. An integer between 0 and X (0 = highest priority).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Loader_Autoloader_Spl_Config Current instance for chaining
     * @access public
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Getter for $priority
     *
     * This method is intend as Getter for $priority.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The previous setted priority of the Autoloader
     * @access public
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Setter for $description
     *
     * This method is intend as Setter for $description.
     *
     * @param string $description A description for the Autoloader.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Loader_Autoloader_Spl_Config Current instance for chaining
     * @access public
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Getter for $description
     *
     * This method is intend as Getter for $description.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The previous setted description of the Autoloader
     * @access public
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Setter for a single file-extension (added to $extension)
     *
     * This method is intend as Setter for a single file-extension (added to $extension).
     *
     * @param string $extension A file-extension used by the Autoloader.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Loader_Autoloader_Spl_Config Current instance for chaining
     * @access public
     */
    public function addExtension($extension)
    {
        $extension = trim($extension);

        (!preg_match('/^\./', $extension)) ? $extension = '.'.$extension : '';

        if ($this->extension) {
            if (!in_array($extension, $this->extension)) {
                $this->extension[] = $extension;
            }
        } else {
            $this->extension[] = $extension;
        }

        // for chaining
        return $this;
    }

    /**
     * Setter for a list of (array) file-extensions (added to $extension)
     *
     * This method is intend as Setter for a list of (array) file-extensions (added to $extension).
     *
     * @param array $extensions A list of file-extensions used by the Autoloader.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function addExtensions(array $extensions)
    {
        // assume success
        $result = true;

        foreach ($extensions as $extension) {
            $result = $result && $this->addExtension($extension);
        }

        // for chaining
        return $this;
    }

    /**
     * Getter for $extension
     *
     * This method is intend as Getter for $extension.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The previous setted extension(s) used by the Autoloader
     * @access public
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Setter for $class
     *
     * This method is intend as Setter for a class containing the Autoloader-Method (Function).
     *
     * @param string $class A name of a class containing the Autoloader-Method used by the Autoloader.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Loader_Autoloader_Spl_Config Current instance for chaining
     * @access public
     */
    public function setClass($class)
    {
        // is this autoloader class based or simple method
        $this->isClass  = true;
        $this->isLoader = false;

        // store classname
        $this->class = $class;

        // for chaining
        return $this;
    }

    /**
     * Getter for $class
     *
     * This method is intend as Getter for $class.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The previous setted class used by the Autoloader
     * @access public
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * returns is-class status of the Autoloader-Config
     *
     * This method is intend to return the is-class status of the Autoloader-Config.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean true is a Class containing the Autoloader-Method (Function), otherwise false (proced. Function)
     * @access public
     */
    public function isClass()
    {
        return (!is_null($this->class));
    }

    /**
     * Setter for $method
     *
     * This method is intend as Setter for a method (function) used as "loader" by the Autoloader.
     *
     * @param string $method A method-name used by the Autoloader.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Loader_Autoloader_Spl_Config Current instance for chaining
     * @access public
     */
    public function setMethod($method)
    {
        // is this autoloader class based or simple method
        $this->isClass = ($this->class);

        // store method
        $this->method = $method;

        // for chaining
        return $this;
    }

    /**
     * Getter for $method
     *
     * This method is intend as Getter for $method.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The previous setted method used by the Autoloader
     * @access public
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Setter for $method (alias for setMethod())
     *
     * This method is intend as Setter for a method (function) used as "loader" by the Autoloader.
     *
     * @param string $function A function-name used by the Autoloader.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Loader_Autoloader_Spl_Config Current instance for chaining
     * @access public
     */
    public function setFunction($function)
    {
        $this->setMethod($function);
        return $this;
    }

    /**
     * Getter for $method (alias for getMethod())
     *
     * This method is intend as Getter for $method.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The previous setted method used by the Autoloader
     * @access public
     */
    public function getFunction()
    {
        return $this->getMethod();
    }

    /**
     * Setter for $path
     *
     * This method is intend as Setter for a path or a list of (array) paths used for lookup by the Autoloader.
     *
     * @param mixed $path A single path (string) or a list of paths (array) used by the Autoloader for lookup for files.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Loader_Autoloader_Spl_Config Current instance for chaining
     * @access public
     */
    public function setPath($path)
    {
        // check given type
        if (is_array($path)) {
            $result = ($this->path = $path);
        } else {
            $result = ($this->path = array($path));
        }

        // for chaining
        return $this;
    }

    /**
     * Setter for $path
     *
     * This method is intend as Setter for a path. It adds a single path or a list of (array) paths to the already
     * exiting path(s).
     *
     * @param mixed $path A single path (string) or a list of paths (array) used by the Autoloader for lookup for files.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function addPath($path)
    {
        // Check given type
        if (is_array($path)) {
            array_unique(array_merge($this->path, $path));
        } else {
            // check if not already exist
            if (!in_array($path, $this->path)) {
                $this->path[] = $path;
            }
        }

        return $this;
    }

    /**
     * Getter for $path
     *
     * This method is intend as Getter for $path.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The previous setted path(s) used by the Autoloader
     * @access public
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * This method returns TRUE if the current instance is a loader,
     * otherwise FALSE.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if loader, otherwise FALSE
     * @access public
     */
    public function isLoader()
    {
        return $this->isLoader;
    }

    /**
     * This method is the loader mechanism for this loader config
     *
     * @param string $classname The name of the class to load
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function load($classname)
    {
        // get namespace of current instance
        $currentNamespace = $this->namespace . $this->namespaceSeparator;

        // get namespace from passed classname
        $namespace = substr($classname, 0, strlen($currentNamespace));

        // check if requested class must be loaded by this instance of loader
        // this is a good example use case for namespacing which makes sense
        if (
            $this->namespace === null ||
            $currentNamespace === $namespace
        ) {
            $filename = '';
            $lastNamespaceSeparatorPosition = strripos($classname, $this->namespaceSeparator);

            if ($lastNamespaceSeparatorPosition !== false) {
                $namespace = substr($classname, 0, $lastNamespaceSeparatorPosition);
                $classname = substr($classname, $lastNamespaceSeparatorPosition + 1);
                $filename  = str_replace(
                    $this->namespaceSeparator,
                    $this->separator,
                    $namespace
                ).$this->separator;
            }

            $filename .= str_replace('_', $this->separator, $classname).'.php';
            $path      = $this->getPath();

            if ($path !== null) {
                foreach ($path as $singlePath) {
                    $filename = $singlePath.$this->separator.$filename;
                    if (file_exists($filename)) {
                        include_once $filename;
                        return true;
                    }
                }
            }
        }

        // Nothing done
        return false;
    }
}
