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
     * holds the backtrace information
     * so we don't need to call this expensive function more than
     * one time
     *
     * @var array
     * @access private
     */
    private $_backtrace = null;

    /**
     * holds the path to the parent which extends this base
     *
     * @var string
     * @access private
     */
    private $_path = null;

    /**
     * holds the filename of the parent which extends this base
     *
     * @var string
     * @access private
     */
    private $_file = null;

    /**
     * holds the path and the filename of the parent which extends this base
     *
     * @var string
     * @access private
     */
    private $_pathAndFile = null;


    /**
     * logs a given message to logger-facade-system of DoozR
     *
     * This method is intend to log a given message to logger-facade-system of DoozR.
     *
     * @param string $message The message to log
     * @param string $type    The type of the message to log (e.g. null = log, error, warning)
     *
     * @return  boolean True if logging was successful, otherwise false
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    protected function log($message, $type = null)
    {
        //$logger = DoozR_Logger::getInstance();
        //$logger->log($message, $type);
        return true;
    }

    /**
     * returns the path to the child of this "Base"
     *
     * This method is intend to return the path to the parent (extending class) of this class.
     *
     * @return  string The path to parent class
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
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
     * @return  string The filename of the parent class
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
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
     * @return  string The filename and path of the parent class
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
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
     * @return  boolean True if  method exist, otherwise false
     * @access  protected
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function hasMethod($method)
    {
        return method_exists(get_class($this), $method);
    }

    /**
     * generic loader method (generic class instanciation)
     *
     * This method is a generic instanciation method. It instanciates and
     * returns any class requested.
     *
     * @param string $className   The name of the class to load
     * @param array  $arguments   The arguments to pass to loaded class
     * @param string $constructor The constructor to use for instanciation
     * @param string $includeFile The file to include before instantiating the class
     *
     * @return  object Instance of the class ($className) requested
     * @access  protected
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
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

                // return instance with parameter
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
     * @return  mixed The result of the call
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
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
     * gets triggered when invoking inaccessible methods in an object context
     *
     * This method is intend to fetch calls to not-existent methods and throw an exception
     *
     * @param string $methodSignature The name of the not-existent method called
     * @param mixed  $arguments       The arguments (optional) as array or null
     *
     * @return  void
     * @throws  Exception
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    /*
    public function __call($methodSignature, $arguments)
    {
        // check if lambda function defined and requested
        if (isset($this->{$methodSignature}) === true) {
            $func = $this->$methodSignature;
            $func(implode(', ', $arguments));
        } else {
            // throw error
            trigger_error(
                'call to undefined method '.get_called_class().'::'.
                $methodSignature.'('.var_export($arguments, true).')',
                E_USER_ERROR
            );
        }
    }
    */

    /**
     * gets triggered when serialize() on this class is called
     *
     * This method is intend to fetch calls to not-existent methods and throw an exception.
     * serialize() checks if your class has a function with the magic name __sleep and calls it.
     *
     * @return  array An empty array
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
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
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
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
     * @return  void
     * @throws  Exception
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function __unset($property)
    {
        throw new Exception(
            __METHOD__ .'(): tried to unset an undefined property "'.$property.'"!'
        );
    }

    /**
     * gets triggered when isset() or empty() is invoked on inaccessible properties
     *
     * __isset() is invoked when isset() or empty() is used on inaccessible properties.
     *
     * @param mixed $property The accessed property
     *
     * @return  boolean false
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    /*
    public function __isset($property)
    {
        return false;
    }
    */

    /**
     * gets triggered when trying to read an not-existant property
     *
     * __get() is utilized for reading data from inaccessible properties.
     *
     * @param mixed $property The accessed property
     *
     * @return  void
     * @throws  Exception
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    /*
    public function __get($property)
    {
        throw new Exception(
            __METHOD__ .'(): tried to read an undefined property "'.$property.'"!'
        );
    }
    */

    /**
     * gets triggered when trying to set an not-existant property
     *
     * __set() is utilized when trying to read data from an inaccessible property.
     *
     * @param mixed $property The accessed property
     * @param mixed $value    The value to set
     *
     * @return  void
     * @throws  Exception
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function __set($property, $value)
    {
        /*
        $logger = DoozR_Logger::getInstance();
        $logger->log(
            __METHOD__ .'(): sets property an runtime: "'.$property.'='.var_export($value, true).'"!'
        );
        */

        // and set
        $this->{$property} = $value;
    }

    /**
     * gets triggered when this class (or it's childs) get printed out
     *
     * This method is intend to allow a class to decide how it will react when it is converted to a string.
     *
     * @return  array This class-instance as an array
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @todo    remove "_backtrace" content (set to null?) to prevent *RECURSION* message of php when executed
     */
    public function __toString()
    {
        //$this->_backtrace = null;
        //return print_r($this, true);
        return '';
    }
}

?>
