<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Base - Tools
 *
 * Tools.php - Toolkit collection useful for developers.
 *
 * PHP version 5
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
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Tools
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

/**
 * Doozr - Base - Tools
 *
 * Toolkit collection useful for developers.
 *
 * @category   Doozr
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Tools
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Base_Tools
{
    /**
     * The backtrace information so we don't need to call this expensive
     * function more than once.
     *
     * @var array
     * @access protected
     */
    protected $backtrace;

    /**
     * The path to the parent which extends this base
     *
     * @var array
     * @access protected
     */
    protected $paths = array(
        'realpath' => null,
        'symlink'  => null,
    );

    /**
     * The filename of the parent which extends this base
     *
     * @var string
     * @access protected
     */
    protected $file;

    /**
     * The path and the filename of the parent which extends this base
     *
     * @var string
     * @access protected
     */
    protected $pathAndFile;


    /**
     * This method is intend to return the path to the parent (extending class) of this class.
     * WARNING! This method returns the resolved path to the current file (the file where you
     * execute the method! It does not return symlinked paths'.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The path to parent class
     * @access public
     */
    public function getPathToClass($resolveSymlinks = false)
    {
        $mode = ($resolveSymlinks === false) ? 'symlink' : 'realpath';

        // @todo: seek and destroy: is_null() against === null
        if ($this->paths[$mode] === null) {
            if ($this->backtrace === null) {
                $this->backtrace = debug_backtrace();
            }
            $path = realpath_ext($this->backtrace[0]['file'], $resolveSymlinks);
            $this->paths[$mode] = dirname($path).DIRECTORY_SEPARATOR;
        }

        return $this->paths[$mode];
    }

    /**
     * Returns the filename of parents class.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The filename of the parent class
     * @access public
     */
    public function getFilename()
    {
        if (is_null($this->file)) {
            if (is_null($this->backtrace)) {
                $this->backtrace = debug_backtrace();
            }
            $this->file = filename($this->backtrace[0]['file']);
        }

        return $this->file;
    }

    /**
     * Returns the filename and path (combined) of the parent of this "Base"
     *
     * @param bool $resolveSymlinks TRUE to resolve symlinks to real path FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The filename and path of the parent class
     * @access public
     */
    public function getPathAndFile($resolveSymlinks = false)
    {
        if (is_null($this->pathAndFile)) {
            $this->pathAndFile = $this->getPathToClass($resolveSymlinks) . $this->getFilename();
        }

        return $this->pathAndFile;
    }

    /**
     * Returns TRUE if base class of current instance has method passed by argument. FALSE if not.
     *
     * @param string $method The name of the method to check for existence in base class of current instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool True if  method exist, otherwise false
     * @access public
     */
    public function hasMethod($method)
    {
        return method_exists(get_class($this), $method);
    }

    /**
     * This method is a generic instanciation method. It instanciates and returns any class requested.
     *
     * @param string $classname   The name of the class to load
     * @param array  $arguments   The arguments to pass to loaded class
     * @param string $constructor The constructor to use for instanciation
     * @param string $includeFile The file to include before instantiating the class
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object Instance of the class ($classname) requested
     * @access protected
     */
    protected static function instanciate($classname, $arguments = null, $constructor = null, $includeFile = null)
    {
        // include file given?
        if (!is_null($includeFile)) {
            include_once $includeFile;
        }

        // create reflection of class
        $reflectionOfClass = new ReflectionClass($classname);

        // check if class is instantiable
        $instantiable = $reflectionOfClass->isInstantiable();

        // get constructor if not instantiable (maybe singleton!)
        if (!$instantiable) {
            // if no constructor given for singleton instantiation try to detect
            // PERFORMANCE: slow
            if (is_null($constructor)) {
                // get name of class
                $classname = $reflectionOfClass->getName();

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
                    if (strpos($methodSourcecode, 'new self(') || strpos($methodSourcecode, 'new '.$classname.'(')) {
                        $constructor = $possibleConstructor->name;
                        break;
                    }
                }
            }

            // and finally instantiate
            if (is_null($arguments)) {
                return call_user_func($classname.'::'.$constructor);
            } else {
                // parameter!
                if (is_array($arguments)) {
                    return call_user_func_array($classname.'::'.$constructor, $arguments);
                } else {
                    return call_user_func($classname.'::'.$constructor, $arguments);
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
     * Generic call methods (generic dynamic calling methods). This method is a generic dynamic
     * calling method. It calls dynamic build methods (by name) in already existing instances
     * with or without params (default = null).
     *
     * @param object     $instance The instance of a class to call method in
     * @param string     $method   The name of the method to call
     * @param null|mixed $params   The parameters as anything/mixed
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the call
     * @access public
     * @static
     */
    public static function dynamicCall($instance, $method, $params = null)
    {
        // No params given
        if (!$params) {
            $returnValue = $instance->{$method}();

        } else {
            // Pass params to method
            $returnValue = $instance->{$method}($params);
        }

        // Return result from method-call
        return $returnValue;
    }
}
