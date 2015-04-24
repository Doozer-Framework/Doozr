<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Registry
 *
 * Registry.php - Registry of the DoozR framework.
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
 * this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 * - All advertising materials mentioning features or use of this software
 * must display the following acknowledgement: This product includes software
 * developed by Benjamin Carl and other contributors.
 * - Neither the name Benjamin Carl nor the names of other contributors
 * may be used to endorse or promote products derived from this
 * software without specific prior written permission.
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
 * @package    DoozR_Kernel
 * @subpackage DoozR_Kernel_Registry
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Class/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Registry/Interface.php';

use Rhumsaa\Uuid\Uuid;
use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;

/**
 * DoozR - Registry
 *
 * Registry of the DoozR framework.
 *
 * @category   DoozR
 * @package    DoozR_Kernel
 * @subpackage DoozR_Kernel_Registry
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Registry extends DoozR_Base_Class_Singleton implements
    DoozR_Registry_Interface,
    ArrayAccess,
    Iterator,
    Countable
{
    /**
     * To be more flexible we use an array for storing properties
     * which are passed via __set and set()
     * key = property-name
     *
     * @var array
     * @access protected
     * @static
     */
    protected static $lookup = array();

    /**
     * To be more flexible for a reverse lookup
     * key = index (numeric)
     *
     * @var array
     * @access protected
     * @static
     */
    protected static $reverseLookup = array();

    /**
     * Lookup matrix for implementation of ArrayAccess
     * Those lookup matrix is used to retrieve the relation
     * between an identifier and a numeric index
     *
     * @var array
     * @access protected
     * @static
     */
    protected static $references = array();

    /**
     * The position of the iterator for iterating
     * elements.
     *
     * @var int
     * @access protected
     * @static
     */
    protected static $position = 0;

    /**
     * The count of elements peculated for countable interface.
     *
     * @var int
     * @access protected
     * @static
     */
    protected static $count = 0;


    /**
     * This method stores an element in the registry under the
     * passed key.
     *
     * @param string $variable   The variable (class, object) to store
     * @param string $identifier The identifier for the stored object, class ...
     *                           If not passed a UUID is calculated and returned
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The identifier for reading the stored variable
     * @access public
     */
    public function set(&$variable, $identifier = null)
    {
        // Generate identifier if not passed
        if ($identifier === null) {
            $identifier = sha1(serialize($variable));
        }

        // store the variable as reference
        self::$references[]          = $variable;
        $index                        = count(self::$references)-1;
        self::$lookup[$identifier]   = $index;
        self::$reverseLookup[$index] = $identifier;

        // store count of elements
        self::$count = $index + 1;

        // return identifier for outer use
        return $identifier;
    }

    /**
     * This method returns a previously stored element from the registry
     *
     * @param string $identifier The identifier of the stored object, class ...
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The stored variable if exist
     * @access public
     */
    public function get($identifier = null)
    {
        $result = null;

        if ($identifier === null) {
            $result = self::$lookup;
        } else {
            if (isset(self::$lookup[$identifier])) {
                $result = self::$references[self::$lookup[$identifier]];
            }
        }

        return $result;
    }

    /**
     * Adds an multi element like a multi instance service to registry by generating UUID for instances.
     *
     * @param $variable
     * @param null $identifier
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string
     * @access public
     */
    public function add(&$variable, $identifier = null)
    {
        if ($identifier === null) {
            $identifier = $this->getUuid();
        }

        return $this->set($variable, $identifier);
    }

    /**
     * Returns an random UUID.
     *
     * @param string $salt Salt used for generating UUID
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string An random UUID
     * @access public
     */
    protected function getUuid($salt = null)
    {
        // Generate a version 4 (random) UUID object
        $uuid4 = Uuid::uuid4();

        if ($salt === null) {
            $salt = microtime(true);
        }

        return sha1($uuid4->toString() . $salt);
    }

    /**
     * This method is a shortcut wrapper to set()
     *
     * @param string $identifier The identifier of the property
     * @param mixed  $value      The value to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function __set($identifier, $value)
    {
        return $this->set($value, $identifier);
    }

    /**
     * This method is a shortcut wrapper to get()
     *
     * @param string $identifier The identifier of the property
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The value of the property if exist
     * @access public
     */
    public function __get($identifier)
    {
        return $this->get($identifier);
    }

    /**
     * ==========================================================
     */

    /**
     * Setter for DoozR DI Container.
     *
     * @param DoozR_Di_Container $container The DI container to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setContainer(DoozR_Di_Container $container)
    {
        $this->set($container, 'container');
    }

    /**
     * Getter for DoozR DI Container.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Di_Container Container instance
     * @access public
     */
    public function getContainer()
    {
        return $this->get('container');
    }

    /**
     * Setter for request (state).
     *
     * @param DoozR_Request_State $request The request state to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setRequest(DoozR_Request_State $request)
    {
        $this->set($request, 'request');
    }

    /**
     * Getter for request (state).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Request_State The request state
     * @access public
     */
    public function getRequest()
    {
        return $this->get('request');
    }

    /**
     * Setter for response (state).
     *
     * @param DoozR_Response_State $response The response state
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setResponse(DoozR_Response_State $response)
    {
        $this->set($response, 'response');
    }

    /**
     * Getter for response (state).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Response_State The response state
     * @access public
     */
    public function getResponse()
    {
        return $this->get('response');
    }

    /**
     * Setter for map.
     *
     * @param DoozR_Di_Map The map to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setMap(DoozR_Di_Map $map)
    {
        $this->set($map, 'map');
    }

    /**
     * Getter for map.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Di_Map The Di Map instance
     * @access public
     */
    public function getMap()
    {
        return $this->get('map');
    }

    /**
     * Setter for front.
     *
     * @param DoozR_Controller_Front $front Front controller instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setFront(DoozR_Controller_Front $front)
    {
        $this->set($front, 'front');
    }

    /**
     * Getter for front controller.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Controller_Front Front controller instance
     * @access public
     */
    public function getFront()
    {
        return $this->get('front');
    }

    /**
     * Setter for back.
     *
     * @param DoozR_Controller_Back $back The back controller.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setBack(DoozR_Controller_Back $back)
    {
        $this->set($back, 'back');
    }

    /**
     * Getter for back.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Controller_Back The back controller
     * @access public
     */
    public function getBack()
    {
        return $this->get('back');
    }

    /**
     * Setter for logger.
     *
     * @param DoozR_Logger_Interface $logger The logger to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setLogger(DoozR_Logger_Interface $logger)
    {
        $this->set($logger, 'logger');
    }

    /**
     * Getter for logger.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Logger The logger instance
     * @access public
     */
    public function getLogger()
    {
        return $this->get('logger');
    }

    /**
     * Setter for filesystem.
     *
     * @param DoozR_Filesystem_Service $filesystem The filesystem instance to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setFilesystem(DoozR_Filesystem_Service $filesystem)
    {
        $this->set($filesystem, 'filesystem');
    }

    /**
     * Getter for filesystem.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Filesystem_Service The filesystem instance
     * @access public
     */
    public function getFilesystem()
    {
        return $this->get('filesystem');
    }

    /**
     * Setter for config.
     *
     * @param DoozR_Config $config Instance of config
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setConfig(DoozR_Config $config)
    {
        $this->set($config, 'config');
    }

    /**
     * Getter for config.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Config The config instance
     * @access public
     */
    public function getConfig()
    {
        return $this->get('config');
    }

    /**
     * Setter for cache.
     *
     * @param DoozR_Psr_Cache_Interface $cache Instance of cache
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setCache(DoozR_Psr_Cache_Interface $cache)
    {
        $this->set($cache, 'cache');
    }

    /**
     * Getter for cache.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Psr_Cache_Interface The cache instance
     * @access public
     */
    public function getCache()
    {
        return $this->get('cache');
    }

    /**
     * Setter for path.
     *
     * @param DoozR_Path $path Instance of path
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setPath(DoozR_Path $path)
    {
        $this->set($path, 'path');
    }

    /**
     * Getter for path.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Path The path instance
     * @access public
     */
    public function getPath()
    {
        return $this->get('path');
    }

    /**
     * Setter for encoding.
     *
     * @param DoozR_Encoding $encoding Instance of encoding
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setEncoding(DoozR_Encoding $encoding)
    {
        $this->set($encoding, 'encoding');
    }

    /**
     * Getter for encoding.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Encoding The instance of encoding
     * @access public
     */
    public function getEncoding()
    {
        return $this->get('encoding');
    }

    /**
     * Setter for locale.
     *
     * @param DoozR_Locale $locale Instance of locale
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setLocale(DoozR_Locale $locale)
    {
        $this->set($locale, 'locale');
    }

    /**
     * Getter for locale.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Locale Instance of locale
     * @access public
     */
    public function getLocale()
    {
        return $this->get('locale');
    }

    /**
     * Setter for debug.
     *
     * @param DoozR_Debug $debug Instance of debug
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setDebug(DoozR_Debug $debug)
    {
        $this->set($debug, 'debug');
    }

    /**
     * Getter for debug.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Debug Instance of debug
     * @access public
     */
    public function getDebug()
    {
        return $this->get('debug');
    }

    /**
     * Setter for security.
     *
     * @param DoozR_Security $security Instance of security
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setSecurity(DoozR_Security $security)
    {
        $this->set($security, 'security');
    }

    /**
     * Getter for security.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Security Instance of security
     * @access public
     */
    public function getSecurity()
    {
        return $this->get('security');
    }

    /**
     * Setter for model.
     *
     * @param DoozR_Model $model Instance of model
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setModel(DoozR_Model $model)
    {
        $this->set($model, 'model');
    }

    /**
     * Getter for model.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Model Instance of model
     * @access public
     */
    public function getModel()
    {
        return $this->get('model');
    }

    /**
     * Setter for debugbar.
     *
     * @param DebugBar\StandardDebugBar $debugBar Instance of $debugbar
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setDebugbar(DebugBar\StandardDebugBar $debugBar)
    {
        $this->set($debugBar, 'debugbar');
    }

    /**
     * Getter for debugbar.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DebugBar\StandardDebugBar Instance of StandardDebugBar
     * @access public
     */
    public function getDebugbar()
    {
        return $this->get('debugbar');
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Fulfill ArrayAccess
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the TRUE if the passed offset exists otherwise FALSE
     *
     * @param mixed $offset The offset to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function offsetExists($offset)
    {
        if (!is_int($offset)) {
            $offset = array_search($offset, self::$reverseLookup);
        }

        return (isset(self::$references[$offset]));
    }

    /**
     * Returns the value for the passed offset
     *
     * @param mixed $offset The offset to return value for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function offsetGet($offset)
    {
        if (!is_int($offset)) {
            $offset = array_search($offset, self::$reverseLookup);
        }

        return self::$references[$offset];
    }

    /**
     * Sets the value for the passed offset
     *
     * @param int $offset The offset to set value for
     * @param mixed   $value  The value to write
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function offsetSet($offset, $value)
    {
        if (!is_int($offset) && $exist = array_search($offset, self::$reverseLookup)) {
            $offset = $exist;
        }

        self::$references[$offset] = $value;
    }

    /**
     * Unsets an offset
     *
     * @param mixed $offset The offset to unset
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function offsetUnset($offset)
    {
        $identifier = self::$reverseLookup[$offset];
        unset(self::$lookup[$identifier]);
        unset(self::$reverseLookup[$identifier]);
        unset(self::$references[$identifier]);
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Fulfill Iterator
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Rewinds the position to 0
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function rewind()
    {
        self::$position = 0;
    }

    /**
     * Checks if current position is still valid
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function valid()
    {
        return self::$position < count(self::$references);
    }

    /**
     * Returns the key for the current position
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function key()
    {
        return self::$position;
    }

    /**
     * Returns the current element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function current()
    {
        return self::$references[self::$position];
    }

    /**
     * Goes to next element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function next()
    {
        self::$position++;
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Fulfill Countable
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the count of elements in registry
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function count()
    {
        return self::$count;
    }
}
