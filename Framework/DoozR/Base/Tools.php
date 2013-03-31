<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Tools.php - DoozR Base-Tools
 * A toolset which is useful while developing classes which give you features like
 * ...
 *
 * PHP version 5
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
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Tools
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

//require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Development.php';

/**
 * DoozR Base-Tools
 * A toolset which is useful while developing classes which give you features like
 * ...
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Tools
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @author     $LastChangedBy$ <doozr@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Base_Tools // extends DoozR_Base_Development
{
    /**
     * The backtrace information so we don't need to call this expensive
     * function more than once.
     *
     * @var array
     * @access private
     */
    private $_backtrace = null;

    /**
     * The path to the parent which extends this base
     *
     * @var string
     * @access private
     */
    private $_path = null;

    /**
     * The filename of the parent which extends this base
     *
     * @var string
     * @access private
     */
    private $_file = null;

    /**
     * The path and the filename of the parent which extends this base
     *
     * @var string
     * @access private
     */
    private $_pathAndFile = null;


    /**
     * This method is intend to return the path to the parent (extending class) of this class.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The path to parent class
     * @access public
     */
    public function getPath()
    {
        if (is_null($this->_path)) {
            if (is_null($this->_backtrace)) {
                $this->_backtrace = debug_backtrace();
            }
            $fileAndPath = $this->_backtrace[0]['file'];
            $this->_path = substr($fileAndPath, 0, strrpos($fileAndPath, DIRECTORY_SEPARATOR)+1);
        }
        return $this->_path;
    }

    /**
     * returns the filename of the parent of this "Base"
     *
     * This method is intend to return the filename of the parent (extending class) of this class.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The filename of the parent class
     * @access public
     */
    public function getFile()
    {
        if (is_null($this->_file)) {
            if (is_null($this->_backtrace)) {
                $this->_backtrace = debug_backtrace();
            }
            $fileAndPath = $this->_backtrace[0]['file'];
            $this->_file = substr(
                $fileAndPath,
                (strrpos($fileAndPath, DIRECTORY_SEPARATOR)+1),
                (strlen($fileAndPath) - strrpos($fileAndPath, DIRECTORY_SEPARATOR)+1)
            );
        }
        return $this->_file;
    }

    /**
     * returns the filename and path (combined) of the parent of this "Base"
     *
     * This method is intend to return the filename and path (combined) of the parent (extending class) of this class.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The filename and path of the parent class
     * @access public
     */
    public function getPathAndFile()
    {
        if (is_null($this->_pathAndFile)) {
            $this->_pathAndFile = $this->getPath().$this->getFile();
        }

        return $this->_pathAndFile;
    }

    /**
     * checks if given method in a any child of a class exist
     *
     * This method is intend to check if a given method in a child of BASE exist.
     * PARENT -> CHILD -> ... -> CHILD -> method()
     *
     * @param string $method The name of the method to check for existence
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean True if  method exist, otherwise false
     * @access protected
     */
    public function hasMethod($method)
    {
        return method_exists(get_class($this), $method);
    }

    /**
     * This method is a generic instanciation method. It instanciates and
     * returns any class requested.
     *
     * @param string $className   The name of the class to load
     * @param array  $arguments   The arguments to pass to loaded class
     * @param string $constructor The constructor to use for instanciation
     * @param string $includeFile The file to include before instantiating the class
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object Instance of the class ($className) requested
     * @access protected
     */
    protected static function instanciate($className, $arguments = null, $constructor = null, $includeFile = null)
    {
        // include file given?
        if (!is_null($includeFile)) {
            include_once $includeFile;
        }

        // create reflection of class
        $reflectionOfClass = new ReflectionClass($className);

        // check if class is instantiable
        $instantiable = $reflectionOfClass->isInstantiable();

        // get constructor if not instantiable (maybe singleton!)
        if (!$instantiable) {
            // if no constructor given for singleton instantiation try to detect
            // PERFORMANCE: slow
            if (is_null($constructor)) {
                // get name of class
                $className = $reflectionOfClass->getName();

                // get filename of class
                if (is_null($includeFile)) {
                    $includeFile = $reflectionOfClass->getFileName();
                }

                // read the file as array
                $sourcecode = file($includeFile);

                // lets find the "real" constructor -> the instance can only be created by a static method
                $possibleConstructors = $reflectionOfClass->getMethods(ReflectionMethod::IS_STATIC);

                // assume default singleton getter methodname
                $constructor = 'getInstance';

                // iterate over static methods and check for instantiation
                foreach ($possibleConstructors as $possibleConstructor) {
                    $start = $possibleConstructor->getStartLine()+1;
                    $end = $possibleConstructor->getEndline()-1;
                    $methodSourcecode = '';

                    // concat sourcecode lines
                    for ($i = $start; $i < $end; ++$i) {
                        $methodSourcecode .= $sourcecode[$i];
                    }

                    // check for instantiation
                    if (strpos($methodSourcecode, 'new self(') || strpos($methodSourcecode, 'new '.$className.'(')) {
                        $constructor = $possibleConstructor->name;
                        break;
                    }
                }
            }

            // and finally instantiate
            if (is_null($arguments)) {
                return call_user_func($className.'::'.$constructor);
            } else {
                // parameter!
                if (is_array($arguments)) {
                    return call_user_func_array($className.'::'.$constructor, $arguments);
                } else {
                    return call_user_func($className.'::'.$constructor, $arguments);
                }
            }
        } else {
            // check for given arguments
            if ($arguments) {
                // check if parameter is already of type array
                if (!is_array($arguments)) {
                    // if not make array
                    $arguments = array($arguments);
                }

                // return instance with parameter}
                return $reflectionOfClass->newInstanceArgs($arguments);
            } else {
                // return instance without parameter
                return $reflectionOfClass->newInstance();
            }
        }
    }

    /**
     * generic call methods (generic dynamic calling methods)
     *
     * This method is a generic dynamic calling method. It calls dynamic build methods (by name)
     * in allready existing instances with or without params (default = null).
     *
     * @param object $instance The instance of a class to call method in
     * @param string $method   The methodname to call as string
     * @param mixed  $params   The parameters as anything/mixed
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the call
     * @access public
     * @static
     */
    public static function dynamicCall($instance, $method, $params = null)
    {
        // no params given
        if (!$params) {
            $returnValue = $instance->{$method}();
        } else {
            // pass params to method
            $returnValue = $instance->{$method}($params);
        }

        // return result from method-call
        return $returnValue;
    }

    /**
     * gets triggered when serialize() on this class is called
     *
     * This method is intend to fetch calls to not-existent methods and throw an exception.
     * serialize() checks if your class has a function with the magic name __sleep and calls it.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array An empty array
     * @access public
     */
    public function __sleep()
    {
        //return array();
    }

    /**
     * gets triggered when unserialize() on this class is called
     *
     * Conversely, unserialize() checks for the presence of a function with the magic name __wakeup.
     * If present, this function can reconstruct any resources that the object may have.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __wakeup()
    {
        // nothing
    }

    /**
     * gets triggered when unset() is invoked on inaccessible properties
     *
     * __unset() is invoked when unset() is used on inaccessible properties.
     *
     * @param mixed $property The accessed property
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __unset($property)
    {
        throw new Exception(
            __METHOD__ .'(): tried to unset an undefined property "'.$property.'"!'
        );
    }

    /**
     * gets triggered when trying to set an not-existant property
     *
     * __set() is utilized when trying to read data from an inaccessible property.
     *
     * @param mixed $property The accessed property
     * @param mixed $value    The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __set($property, $value)
    {
        // and set
        $this->{$property} = $value;
    }

    /**
     * gets triggered when this class (or it's childs) get printed out
     *
     * This method is intend to allow a class to decide how it will react when it is converted to a string.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array This class-instance as an array
     * @access public
     * @todo   remove "_backtrace" content (set to null?) to prevent *RECURSION* message of php when executed
     */
    public function __toString()
    {
        //$this->_backtrace = null;
        //return print_r($this, true);
        return '';
    }
}

?>
