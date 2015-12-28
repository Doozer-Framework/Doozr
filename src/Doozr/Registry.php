<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Registry.
 *
 * Registry.php - Registry of the Doozr framework.
 *
 * PHP versions 5.5
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
 *
 * Copyright (c) 2005 - 2016, Benjamin Carl - All rights reserved.
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
 * must display the following acknowledgment: This product includes software
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
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Class/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Registry/Interface.php';

use Psr\Cache\CacheItemPoolInterface;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid;

/**
 * Doozr - Registry.
 *
 * Registry of the Doozr framework.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Registry extends Doozr_Base_Class_Singleton
    implements
    Doozr_Registry_Interface,
    ArrayAccess,
    Iterator,
    Countable
{
    /**
     * To be more flexible we use an array for storing properties
     * which are passed via __set and set()
     * key = property-name.
     *
     * @var array
     * @static
     */
    protected static $lookup = [];

    /**
     * To be more flexible for a reverse lookup
     * key = index (numeric).
     *
     * @var array
     * @static
     */
    protected static $reverseLookup = [];

    /**
     * Lookup matrix for implementation of ArrayAccess
     * Those lookup matrix is used to retrieve the relation
     * between an identifier and a numeric index.
     *
     * @var array
     * @static
     */
    protected static $references = [];

    /**
     * The position of the iterator for iterating
     * elements.
     *
     * @var int
     * @static
     */
    protected static $position = 0;

    /**
     * The count of elements peculated for countable interface.
     *
     * @var int
     * @static
     */
    protected static $count = 0;

    /**
     * Parameter bag for environment variables and so on.
     *
     * @var array
     * @static
     */
    protected static $parameters = [];

    /**
     * Constructor.
     *
     * @param array $parameters The parameters to store.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Registry The registry
     */
    /*
    public static function getInstance(array $parameters = [])
    {
        return parent::getInstance($parameters);
    }
    */

    protected function __construct(array $parameters = [])
    {
        self::setParameters($parameters);
    }

    /**
     * This method storages an element in the registry under the passed key.
     *
     * @param string $variable   The variable (class, object) to store
     * @param string $identifier The identifier for the stored object, class ...
     *                           If not passed a UUID is calculated and returned
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The identifier for reading the stored variable
     */
    public function set(&$variable, $identifier = null)
    {
        // Generate identifier if not passed
        if ($identifier === null) {
            $identifier = sha1(serialize($variable));
        }

        // store the variable as reference
        self::$references[] = $variable;
        $index = count(self::$references) - 1;
        self::$lookup[$identifier] = $index;
        self::$reverseLookup[$index] = $identifier;

        // store count of elements
        self::$count = $index + 1;

        // return identifier for outer use
        return $identifier;
    }

    /**
     * This method returns a previously stored element from the registry.
     *
     * @param string $identifier The identifier of the stored object, class ...
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed The stored variable if exist
     */
    public function get($identifier = null)
    {
        $result = null;

        if (null === $identifier) {
            $result = self::$lookup;
        } elseif ('doozr.registry' === $identifier) {
            $result = $this;
        } else {
            if (true === isset(self::$lookup[$identifier])) {
                $result = self::$references[self::$lookup[$identifier]];
            }
        }

        return $result;
    }

    /**
     * Static generic instance fetcher. Prototype !!!
     *
     * @todo Proof if useful and conform
     *
     * @param string $name      Name of the "method" called -> will be used as property name
     * @param array  $arguments Unused!
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed ...
     */
    public static function __callStatic($name, array $arguments)
    {
        return (true === isset(self::$lookup[$name])) ? self::$references[self::$lookup[$name]] : null;
    }

    /**
     * Adds an multi element like a multi instance service to registry by generating UUID for instances.
     *
     * @param Doozr_Base_Service_Interface $variable
     * @param string                       $identifier
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string
     */
    public function add(&$variable, $identifier = null)
    {
        if ($identifier === null) {
            $identifier = $this->calculateUuid();
        }

        return $this->set($variable, $identifier);
    }

    /**
     * Calculates a random UUID.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The UUID
     *
     * @throws Doozr_Registry_Exception
     */
    protected function calculateUuid()
    {
        try {
            // Generate a version 4 (random) UUID object
            $uuid4 = Uuid::uuid4();
            $uuid = $uuid4->toString();
        } catch (UnsatisfiedDependencyException $exception) {
            throw new Doozr_Registry_Exception($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $uuid;
    }

    /**
     * This method is a shortcut wrapper to set().
     *
     * @param string $identifier The identifier of the property
     * @param mixed  $value      The value to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The result of the operation
     */
    public function __set($identifier, $value)
    {
        return $this->set($value, $identifier);
    }

    /**
     * This method is a shortcut wrapper to get().
     *
     * @param string $identifier The identifier of the property
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed The value of the property if exist
     */
    public function __get($identifier)
    {
        return $this->get($identifier);
    }

    /**
     * Setter for parameter.
     *
     * @param string $key   The key or name of the parameter
     * @param mixed  $value The value to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setParameter($key, $value)
    {
        self::$parameters[$key] = $value;
    }

    /**
     * Fluent: Setter for parameter.
     *
     * @param string $key   The key or name of the parameter
     * @param mixed  $value The value to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function parameter($key, $value)
    {
        $this->setParameter($key, $value);

        return $this;
    }

    /**
     * Getter for parameter.
     *
     * @param string $key The key or name of the parameter to return value for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed Value for key if set, otherwise NULL
     */
    public function getParameter($key)
    {
        if (true === isset(self::$parameters[$key])) {
            $value = self::$parameters[$key];
        } else {
            throw new Doozr_Registry_Exception(
                sprintf('Key "%s" does not exist!', $key)
            );
        }

        return $value;
    }

    /**
     * Setter for parameters.
     *
     * @param array $parameters The parameters to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @static
     */
    public static function setParameters(array $parameters)
    {
        self::$parameters = $parameters;
    }

    /**
     * Getter for parameters.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The collection of arguments
     * @static
     */
    public static function getParameters()
    {
        return self::$parameters;
    }

    /**
     * Setter for Doozr DI Container.
     *
     * @param Doozr_Di_Container $container The DI container to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setContainer(Doozr_Di_Container $container)
    {
        $this->set($container, 'container');
    }

    /**
     * Setter for Doozr DI Container.
     *
     * @param Doozr_Di_Container $container The DI container to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function container(Doozr_Di_Container $container)
    {
        $this->setContainer($container);

        return $this;
    }

    /**
     * Getter for Doozr DI Container.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Container Container instance
     */
    public function getContainer()
    {
        return $this->get('container');
    }

    /**
     * Setter for request (state).
     *
     * @param Doozr_Request $request The request state to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setRequest(Doozr_Request $request)
    {
        $this->set($request, 'request');
    }

    /**
     * Fluent: Setter for request (state).
     *
     * @param Doozr_Request $request The request state to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function request(Doozr_Request $request)
    {
        $this->setRequest($request);

        return $this;
    }

    /**
     * Getter for request (state).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Request_Web The request state
     */
    public function getRequest()
    {
        return $this->get('request');
    }

    /**
     * Setter for response (state).
     *
     * @param Doozr_Response_Interface $response The response state
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setResponse(Doozr_Response_Interface $response)
    {
        $this->set($response, 'response');
    }

    /**
     * Fluent: Setter for response (state).
     *
     * @param Doozr_Response_Interface $response The response state
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function response(Doozr_Response_Interface $response)
    {
        $this->setResponse($response);

        return $this;
    }

    /**
     * Getter for response (state).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Response_Web The response state
     */
    public function getResponse()
    {
        return $this->get('response');
    }

    /**
     * Setter for map.
     *
     * @param Doozr_Di_Map $map The map to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setMap(Doozr_Di_Map $map)
    {
        $this->set($map, 'map');
    }

    /**
     * Fluent; Setter for map.
     *
     * @param Doozr_Di_Map $map The map to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function map(Doozr_Di_Map $map)
    {
        $this->setMap($map);

        return $this;
    }

    /**
     * Getter for map.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Map The Di Map instance
     */
    public function getMap()
    {
        return $this->get('map');
    }

    /**
     * Setter for logger.
     *
     * @param Doozr_Logging_Interface $logger The logger to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setLogger(Doozr_Logging_Interface $logger)
    {
        $this->set($logger, 'logger');
    }

    /**
     * Getter for logger.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Logging The logger instance
     */
    public function getLogger()
    {
        return $this->get('logger');
    }

    /**
     * Setter for filesystem.
     *
     * @param Doozr_Base_Service_Interface $filesystem The filesystem instance to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setFilesystem(Doozr_Filesystem_Service $filesystem)
    {
        $this->set($filesystem, 'filesystem');
    }

    /**
     * Getter for filesystem.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Filesystem_Service The filesystem instance
     */
    public function getFilesystem()
    {
        return $this->get('filesystem');
    }

    /**
     * Setter for configuration.
     *
     * @param Doozr_Configuration $configuration Instance of configuration
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setConfiguration(Doozr_Configuration $configuration)
    {
        $this->set($configuration, 'configuration');
    }

    /**
     * Getter for configuration.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Configuration The configuration instance
     */
    public function getConfiguration()
    {
        return $this->get('configuration');
    }

    /**
     * Setter for cache.
     *
     * @param CacheItemPoolInterface|Doozr_Base_Service_Interface $cache Instance of cache
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setCache($cache)
    {
        $this->set($cache, 'cache');
    }

    /**
     * Fluent: Setter for cache.
     *
     * @param CacheItemPoolInterface $cache Instance of cache
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function cache($cache)
    {
        $this->setCache($cache);

        return $this;
    }

    /**
     * Getter for cache.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return CacheItemPoolInterface The cache instance
     */
    public function getCache()
    {
        return $this->get('cache');
    }

    /**
     * Setter for path.
     *
     * @param Doozr_Path $path Instance of path
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setPath(Doozr_Path $path)
    {
        $this->set($path, 'path');
    }

    /**
     * Fluent: Setter for path.
     *
     * @param Doozr_Path $path Instance of path
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Registry
     */
    public function path(Doozr_Path $path)
    {
        $this->setPath($path);

        return $this;
    }

    /**
     * Getter for path.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Path The path instance
     */
    public function getPath()
    {
        return $this->get('path');
    }

    /**
     * Setter for encoding.
     *
     * @param Doozr_Encoding $encoding Instance of encoding
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setEncoding(Doozr_Encoding $encoding)
    {
        $this->set($encoding, 'encoding');
    }

    /**
     * Fluent: Setter for encoding.
     *
     * @param Doozr_Encoding $encoding Instance of encoding
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function encoding(Doozr_Encoding $encoding)
    {
        $this->setEncoding($encoding);

        return $this;
    }

    /**
     * Getter for encoding.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Encoding The instance of encoding
     */
    public function getEncoding()
    {
        return $this->get('encoding');
    }

    /**
     * Setter for locale.
     *
     * @param Doozr_Locale $locale Instance of locale
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setLocale(Doozr_Locale $locale)
    {
        $this->set($locale, 'locale');
    }

    /**
     * Getter for locale.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Locale Instance of locale
     */
    public function getLocale()
    {
        return $this->get('locale');
    }

    /**
     * Setter for debugging.
     *
     * @param Doozr_Debugging $debugging Instance of debugging
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setDebugging(Doozr_Debugging $debugging)
    {
        $this->set($debugging, 'debugging');
    }

    /**
     * Getter for debugging.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Debugging Instance of debugging
     */
    public function getDebugging()
    {
        return $this->get('debugging');
    }

    /**
     * Setter for security.
     *
     * @param Doozr_Security $security Instance of security
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setSecurity(Doozr_Security $security)
    {
        $this->set($security, 'security');
    }

    /**
     * Getter for security.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Security Instance of security
     */
    public function getSecurity()
    {
        return $this->get('security');
    }

    /**
     * Setter for model.
     *
     * @param Doozr_Model $model Instance of model
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setModel(Doozr_Model $model)
    {
        $this->set($model, 'model');
    }

    /**
     * Getter for model.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Model Instance of model
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
     */
    public function setDebugbar(DebugBar\StandardDebugBar $debugBar)
    {
        $this->set($debugBar, 'debugbar');
    }

    /**
     * Getter for debugbar.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return DebugBar\StandardDebugBar Instance of StandardDebugBar
     */
    public function getDebugbar()
    {
        return $this->get('debugbar');
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Fulfill ArrayAccess
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the TRUE if the passed offset exists otherwise FALSE.
     *
     * @param mixed $offset The offset to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool The result of the operation
     */
    public function offsetExists($offset)
    {
        if (!is_int($offset)) {
            $offset = array_search($offset, self::$reverseLookup);
        }

        return (isset(self::$references[$offset]));
    }

    /**
     * Returns the value for the passed offset.
     *
     * @param mixed $offset The offset to return value for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed The result of the operation
     */
    public function offsetGet($offset)
    {
        if (!is_int($offset)) {
            $offset = array_search($offset, self::$reverseLookup);
        }

        return self::$references[$offset];
    }

    /**
     * Sets the value for the passed offset.
     *
     * @param int   $offset The offset to set value for
     * @param mixed $value  The value to write
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed The result of the operation
     */
    public function offsetSet($offset, $value)
    {
        if (!is_int($offset) && $exist = array_search($offset, self::$reverseLookup)) {
            $offset = $exist;
        }

        self::$references[$offset] = $value;
    }

    /**
     * Unsets an offset.
     *
     * @param mixed $offset The offset to unset
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed The result of the operation
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
     * Rewinds the position to 0.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed The result of the operation
     */
    public function rewind()
    {
        self::$position = 0;
    }

    /**
     * Checks if current position is still valid.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool The result of the operation
     */
    public function valid()
    {
        return self::$position < count(self::$references);
    }

    /**
     * Returns the key for the current position.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int The result of the operation
     */
    public function key()
    {
        return self::$position;
    }

    /**
     * Returns the current element.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed The result of the operation
     */
    public function current()
    {
        return self::$references[self::$position];
    }

    /**
     * Goes to next element.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed The result of the operation
     */
    public function next()
    {
        ++self::$position;
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Fulfill Countable
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the count of elements in registry.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int The result of the operation
     */
    public function count()
    {
        return self::$count;
    }
}
