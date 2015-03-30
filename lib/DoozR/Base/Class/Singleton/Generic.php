<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Base - Class - Singleton - Generic
 *
 * Generic.php Generic singleton base class of the DoozR Framework
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2014, Benjamin Carl - All rights reserved.
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
 * @subpackage DoozR_Base_Class
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Tools.php';

/**
 * DoozR - Base - Class - Singleton - Generic
 *
 * Generic singleton base class of the DoozR Framework
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Class
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Base_Class_Singleton_Generic extends DoozR_Base_Tools
{
    /**
     * hThe singleton-instance(s) of this class
     *
     * @var array
     * @access protected
     */
    protected static $instances = array();

    /**
     * Status of using STRICT-SINGLETON runtimeEnvironment for this
     * class. In STRICT-SINGLETON-Mode the singleton includes the
     * parameter also.
     *
     * Must be set by the extending class
     *
     * @var bool
     * @access protected
     */
    protected static $strict = false;

    /**
     * The registry instance injected in all core classes
     * based on DoozR_Base_Class.
     *
     * @var DoozR_Registry
     * @access protected
     * @static
     */
    protected static $registry;


    /**
     * Instance getter for singleton instances
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object instance/object of this class
     * @access public
     */
    public static function getInstance()
    {
        // get arguments (all arguments optional)
        $arguments = func_get_args();
        $arguments = array_shift($arguments);

        // get name of calling class (child)
        $callerSignature = get_called_class();

        // check for strict runtimeEnvironment
        if (self::$strict === true) {
            // build identifier including parameter
            if (is_array($arguments)) {
                // array to hold all elements for crc but object(s)
                $crc = array();

                // iterate over arguments to filter objects out => too complex!
                // TODO: maybe serialize objects is a way to process them?!
                foreach ($arguments as $argument) {
                    // add if not object
                    if (!is_object($argument)) {
                        $crc[] = $argument;
                    }
                }
            } else {
                // if no arguments use direct as crc
                $crc = $arguments;
            }

            // build identifier including (strict) parameter
            $identifier = md5($callerSignature.serialize($crc));
        } else {
            // build identifier excluding (loose) parameter
            $identifier = md5($callerSignature);
        }

        // check for already assigned instance
        if (!isset(self::$instances[$identifier])) {
            // get instance
            $instance = self::genericInstantiate($callerSignature, $arguments);

            // store instance
            self::$instances[$identifier] = $instance;
        } else {
            // we have a match
            $instance = self::$instances[$identifier];
        }

        // return instance
        return $instance;
    }

    /**
     * Generic instance getter. This method is intend to return an instance of a requested class with no
     * matter how much parameters are passed to it. OK the maximum is 10 Parameter - but if you are running
     * in trouble using this class you should consider if you're doing things right.
     *
     * @param string     $className The name of the class to create an instance of
     * @param null|array $arguments The arguments to pass to instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object Instance/object of the requested class
     * @access protected
     * @throws Exception
     */
    protected static function genericInstantiate($className, $arguments = null)
    {
        // check for parameter
        if (is_null($arguments)) {
            // with no parameter -> just return the instance
            return new $className();

        } else {
            // not so nice but the fastest solution i've found
            switch (count($arguments)) {
            case 1:
                return new $className(
                    $arguments[0]
                );
                break;
            case 2:
                return new $className(
                    $arguments[0],
                    $arguments[1]
                );
                break;
            case 3:
                return new $className(
                    $arguments[0],
                    $arguments[1],
                    $arguments[2]
                );
                break;
            case 4:
                return new $className(
                    $arguments[0],
                    $arguments[1],
                    $arguments[2],
                    $arguments[3]
                );
                break;
            case 5:
                return new $className(
                    $arguments[0],
                    $arguments[1],
                    $arguments[2],
                    $arguments[3],
                    $arguments[4]
                );
                break;
            case 6:
                return new $className(
                    $arguments[0],
                    $arguments[1],
                    $arguments[2],
                    $arguments[3],
                    $arguments[4],
                    $arguments[5]
                );
                break;
            case 7:
                return new $className(
                    $arguments[0],
                    $arguments[1],
                    $arguments[2],
                    $arguments[3],
                    $arguments[4],
                    $arguments[5],
                    $arguments[6]
                );
                break;
            case 8:
                return new $className(
                    $arguments[0],
                    $arguments[1],
                    $arguments[2],
                    $arguments[3],
                    $arguments[4],
                    $arguments[5],
                    $arguments[6],
                    $arguments[7]
                );
                break;
            case 9:
                return new $className(
                    $arguments[0],
                    $arguments[1],
                    $arguments[2],
                    $arguments[3],
                    $arguments[4],
                    $arguments[5],
                    $arguments[6],
                    $arguments[7],
                    $arguments[8]
                );
                break;
            case 10:
                return new $className(
                    $arguments[0],
                    $arguments[1],
                    $arguments[2],
                    $arguments[3],
                    $arguments[4],
                    $arguments[5],
                    $arguments[6],
                    $arguments[7],
                    $arguments[8],
                    $arguments[9]
                );
                break;
            default:
                // Failover
                $reflection = new ReflectionClass($className);
                return $reflection->newInstanceArgs($arguments);
            }
        }
    }

    /**
     * Setter for registry
     *
     * @param DoozR_Registry $registry The registry of DoozR
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     * @static
     */
    protected static function setRegistry(DoozR_Registry &$registry)
    {
        self::$registry = $registry;
    }

    /**
     * Setter for registry
     *
     * @param DoozR_Registry $registry The registry of DoozR
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function registry(DoozR_Registry $registry)
    {
        self::setRegistry($registry);
        return $this;
    }

    /**
     * Getter for registry
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Registry The registry of DoozR
     * @access protected
     * @static
     */
    protected static function getRegistry()
    {
        if (self::$registry === null) {
            self::$registry = DoozR_Registry::getInstance();
        }

        return self::$registry;
    }

    /**
     * Prevent direct calls to __clone
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    protected function __clone()
    {
        // Intentionally left blank -> To prevent cloning
    }
}
