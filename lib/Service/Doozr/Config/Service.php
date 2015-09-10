<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Config - Service
 *
 * Service.php - Config Service
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
 * @package    Doozr_Service
 * @subpackage Doozr_Service_Config
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Service/Multiple.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Service/Interface.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Config/Interface.php';
require_once DOOZR_DOCUMENT_ROOT . 'Service/Doozr/Config/Service/Exception.php';

use Doozr\Loader\Serviceloader\Annotation\Inject;

/**
 * Doozr - Config - Service
 *
 * Config Service
 *
 * @category   Doozr
 * @package    Doozr_Service
 * @subpackage Doozr_Service_Config
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 * @Inject(
 *     link   = "doozr.registry",
 *     type   = "constructor",
 *     target = "getInstance"
 * )
 */
class Doozr_Configuration_Service extends Doozr_Base_Service_Multiple
    implements
    Doozr_Base_Service_Interface,
    Doozr_Configuration_Interface
{
    /**
     * contains an instance of the class/object decorated
     *
     * @var object
     * @access protected
     */
    private $decoratedObject;

    /**
     * contains an instance of the class/object decorated
     * for static access
     *
     * @var object
     * @access protected
     * @static
     */
    private static $_staticDecoratedObject;

    /**
     * Contains instance of Doozr_Path
     *
     * @var Doozr_Path_Interface
     * @access private
     */
    private $path;

    /**
     * Contains instance of Doozr_Logging
     *
     * @var Doozr_Logging_Interface
     * @access private
     */
    private $logger;

    /**
     * Contains the un-decorated properties
     *
     * @var array
     * @access private
     * @static
     */
    private static $_ownProperties = array(
        'decoratedObject',
        'path',
        'logger'
    );


    /*------------------------------------------------------------------------------------------------------------------
    | TEARUP
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend as replacement for __construct
     * PLEASE DO NOT USE __construct() - make always use of __tearup()!
     *
     * @param string $type          The type of config container (Ini, Json, ...)
     * @param bool   $enableCaching TRUE to enable caching, FALSE to disable it
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __tearup($type, $enableCaching = false)
    {
        // Store path manager
        $this->path = $this->registry->path;

        // Store logger
        $this->logger = $this->registry->logger;

        // Create instance through factory and set as object to decorate!
        $this->setDecoratedObject(
            $this->factory(
                'Doozr_Config_Service_Container_'.ucfirst(strtolower($type)),
                DOOZR_DOCUMENT_ROOT,
                array(
                    $this->path,
                    $this->logger,
                    $enableCaching
                )
            )
        );

        self::$_staticDecoratedObject = $this->getDecoratedObject();
    }

    /*------------------------------------------------------------------------------------------------------------------
    | BEGIN PUBLIC INTERFACES
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to act as setter for $decoratedObject.
     *
     * @param object $instance An instance of a class to decorate
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setDecoratedObject($instance)
    {
        $this->decoratedObject = $instance;
    }

    /**
     * This method is intend to act as getter for $decoratedObject.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object An instance of a class
     * @access protected
     */
    protected function getDecoratedObject()
    {
        return $this->decoratedObject;
    }

    /**
     * This method is intend to act as generic facade - for all non-implemented methods
     *
     * @param string $signature The signature (name of the method) originally called
     * @param mixed  $arguments The arguments used for call (can be either an ARRAY of values or NULL)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Result of called method if exists, otherwise NULL
     * @access public
     */
    public function __call($signature, $arguments)
    {
        if ($arguments) {
            $result = call_user_func_array(
                array($this->decoratedObject, $signature),
                $arguments
            );
        } else {
            $result = call_user_func(
                array($this->decoratedObject, $signature)
            );
        }

        //
        return $result;
    }

    /**
     * This method is intend to act as generic facade - for all non-implemented static methods
     *
     * @param string $signature The signature (name of the method) originally called
     * @param mixed  $arguments The arguments used for call (can be either an ARRAY of values or NULL)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Result of called method if exists, otherwise NULL
     * @access public
     * @static
     */
    public static function __callStatic($signature, $arguments)
    {
        $targetClassname = get_class(self::$_staticDecoratedObject);

        if ($arguments) {
            $result = call_user_func_array(
                $targetClassname.'::'.$signature,
                $arguments
            );
        } else {
            $result = call_user_func(
                array($targetClassname, $signature)
            );
        }

        //
        return $result;
    }

    /**
     * generic getter for dispatching to decorated object
     *
     * This method is intend to act as generic getter for dispatching to decorated object.
     *
     * @param string $property The property to return
     *
     * @throws Doozr_Config_Service_Exception
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The data from property
     * @access public
     */
    public function __get($property)
    {
        if ($property != 'decoratedObject') {

            if (isset($this->decoratedObject->{$property}) === false) {
                throw new Doozr_Config_Service_Exception('Error reading property!');
            }

            return $this->decoratedObject->{$property};
        }
    }

    /**
     * generic isset for dispatching to decorated object
     *
     * This method is intend to act as generic isset for dispatching to decorated object.
     *
     * @param string $property The property to check if set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if set, otherwise FALSE
     * @access public
     */
    public function __isset($property)
    {
        if ($property != 'decoratedObject') {
            return isset($this->decoratedObject->{$property});
        }
    }

    /**
     * generic setter for dispatching to decorated object
     *
     * This method is intend to act as generic setter for dispatching to decorated object.
     *
     * @param string $property The property to set
     * @param mixed  $value    The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The data from property
     * @access public
     */
    public function __set($property, $value)
    {
        if (in_array($property, self::$_ownProperties) === false) {
            return $this->decoratedObject->{$property} = $value;
        }
    }

    /*------------------------------------------------------------------------------------------------------------------
    | BEGIN TOOLS + HELPER
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to act as factory for creating an instance of a config container.
     *
     * @param string $class     The classname of container
     * @param string $path      The base path to Framework
     * @param mixed  $arguments Arguments to pass to instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object The fresh created instance
     * @access protected
     */
    protected function factory($class, $path, $arguments = null)
    {
        // get required file
        include_once $path.'Service'.DIRECTORY_SEPARATOR.str_replace('_', DIRECTORY_SEPARATOR, $class).'.php';

        // create and return a fresh instance
        if ($arguments) {
            return $this->instanciate($class, $arguments);
        } else {
            return new $class();
        }
    }

    /**
     * Updates a configuration node.
     *
     * @param string $node The configuration node
     * @param string $value The data to write
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if entry was created successful, otherwise FALSE
     * @access public
     */
    public function update($node, $value)
    {
        // TODO: Implement update() method.
    }

    /**
     * Setter for key => value pairs of config.
     *
     * @param string $node The key used for entry
     * @param mixed $value The value (every type allow) be sure to check if it is supported by your chosen config type
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function set($node, $value)
    {
        // TODO: Implement set() method.
    }

    /**
     * Getter for value of passed key.
     *
     * @param string $node    The key used for value lookup.
     * @param mixed  $default The default value to return, NULL = default
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed|null The value if set, otherwise NULL
     * @access public
     */
    public function get($node, $default = null)
    {
        return $this->decoratedObject->get($node, $default);
    }

    /**
     * Creates a configuration node.
     *
     * @param string $node The node to create
     * @param string $data The data to write to config
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if entry was created successful, otherwise FALSE
     * @access public
     */
    public function create($node, $data)
    {
        // TODO: Implement create() method.
    }

    /**
     * Reads and return a configuration file.
     *
     * @param string $filename The filename to read/parse
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The data from cache if successful, otherwise NULL
     * @access public
     */
    public function read($filename)
    {
        return $this->decoratedObject->read($filename);
    }

    /**
     * Deletes a node.
     *
     * @param string $node The node to delete
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if entry was deleted successful, otherwise FALSE
     * @access public
     */
    public function delete($node)
    {
        // TODO: Implement delete() method.
    }
}
