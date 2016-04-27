<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Cache - Service.
 *
 * Cache.php - Caching Service for caching operations with support for
 * different container like "Filesystem", "Memcache" ...
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
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Service/Multiple.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Crud/Interface.php';

use Doozr\Loader\Serviceloader\Annotation\Inject;
use Gpupo\Cache\CacheItem;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Doozr - Cache - Service.
 *
 * Caching Service for caching operations with support for
 * different container like "Filesystem", "Memcache" ...
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
 *
 * @throws Doozr_Cache_Service_Exception
 * @Inject(
 *     link   = "doozr.registry",
 *     type   = "constructor",
 *     target = "getInstance"
 * )
 */
class Doozr_Cache_Service extends Doozr_Base_Service_Multiple
    implements
    Doozr_Base_Service_Interface,
    Doozr_Base_Crud_Interface,
    CacheItemPoolInterface
{
    /**
     * Deferred items to save on __teardown.
     *
     * @var array
     */
    protected $deferredItems = [];

    /**
     * Active namespace.
     *
     * @var string
     */
    protected $scope;

    /**
     * Active container.
     *
     * @var Doozr_Cache_Service_Container_Interface
     */
    protected $container;

    /**
     * Whether OS is unixoid.
     *
     * @var bool
     */
    protected $unix;

    /**
     * Whether caching is disabled, or not.
     *
     * @var bool
     */
    protected $enabled;

    /**
     * Garbage collector maximum lifetime of an element per default.
     *
     * This values controls at what time difference an element in the cache
     * is referred to as stale. Stale elements get collected and cleaned by
     * the garbage collecting (gc) process. Default is one day,
     * 60 * 60 * 24 = 86400 seconds.
     *
     * @var int
     */
    protected $gcMaximumLifetime = 86400;

    /**
     * Garbage collection: Probability in percent.
     * 0 = never / 100 always.
     *
     * @var int
     */
    protected $gcProbability = 50;

    /**
     * Garbage collection:
     * probability in seconds.
     *
     * 60 sec. * 60 min. = 3600
     *
     * If set to a value above 0 a garbage collection will flush all cache entries older than the specified number
     * of seconds.
     *
     * @var int
     */
    protected $gcProbabilityTime = 3600;

    /**
     * Time of the last run.
     *
     * @var int
     * @static
     */
    protected static $gcLastRunTimestamp;

    /**
     * Container filesystem (default).
     *
     * @var string
     */
    const CONTAINER_FILESYSTEM = 'filesystem';

    /**
     * Container memcache (higher performance).
     *
     * @var string
     */
    const CONTAINER_MEMCACHE = 'memcache';

    /**
     * The default scope for cache elements.
     *
     * @var string
     */
    const SCOPE_DEFAULT = 'doozr.cache';

    /**
     * Service entry point.
     *
     * @param string $container        Container to use for caching or a collection with priority to try
     * @param string $scope            Scope to group saved/cached elements under
     * @param array  $containerOptions Configuration/options for the container instance
     * @param bool   $unix             TRUE if current OS is unix-type, FALSE if not
     * @param bool   $enabled          Default state is enabled, FALSE to signalize that caching is disabled
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __tearup(
                $container,
                $scope = self::SCOPE_DEFAULT,
        array $containerOptions = [],
                $unix = true,
                $enabled = true
    ) {
        $this
            ->scope($scope)
            ->unix($unix)
            ->container($container, $containerOptions)
            ->enabled($enabled);
    }

    /**
     * Creates an cache entry.
     *
     * @param string $key      The key of the entry
     * @param mixed  $value    The value of the entry
     * @param int    $lifetime The time to expire
     * @param string $scope    The dataset scope
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if dataset could be written, otherwise FALSE
     *
     * @throws Doozr_Cache_Service_Exception
     */
    public function create(
        $key,
        $value,
        $lifetime = null,
        $scope = null
    ) {
        // Get scope if not passed
        if ($scope === null) {
            $scope = $this->getScope();
        }

        // Retrieve expiration date
        if ($lifetime === null) {
            $lifetime = $this->getGcMaximumLifetime();
        }

        // Try to create entry
        if ($this->createExtended($key, $value, $lifetime, $scope) === false) {
            throw new Doozr_Cache_Service_Exception(
                sprintf('Error while creating entry with key: "%s" in scope: "%s"!', $key, $scope)
            );
        }

        return true;
    }

    /**
     * Returns the requested dataset it if exists and is not expired.
     *
     * This method is intend to return the requested dataset it if exists and is not expired.
     *
     * @param string $key      Key to read
     * @param string $scope    Scope to read from
     * @param bool   $metaData Whether to add meta-data
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed|null Data from cache, otherwise NULL
     *
     * @throws Doozr_Cache_Service_Exception
     */
    public function read(
        $key,
        $scope = null,
        $metaData = false
    ) {
        // Assume we will have a false result
        $result = null;

        // Get scope if not passed
        if (null === $scope) {
            $scope = $this->getScope();
        }

        // Check if content exists
        if (true !== $this->exists($key, $scope)) {
            throw new Doozr_Cache_Service_Exception(
                sprintf(
                    'Requested entry with key: "%s" in scope: "%s" could not be found in cache!', $key, $scope
                )
            );
        }

        // Check that element is not expired and return ...
        if (false === $this->expired($key, $scope)) {
            // Then return the content;
            $result = $this->getContainer()->read($key, $scope);
        }

        // Return only data not meta overhead (key = 2)
        return (true === $metaData) ? $result : $result[2];
    }

    /**
     * Updates an entry.
     *
     * @param string $key      The dataset Id
     * @param string $value    The data to write to cache
     * @param int    $lifetime Date/Time on which the cache-entry expires
     * @param string $scope    The dataset group
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if dataset could be written, otherwise FALSE
     *
     * @throws Doozr_Cache_Service_Exception
     */
    public function update(
        $key,
        $value,
        $lifetime = null,
        $scope = null
    ) {
        if ($scope === null) {
            $scope = $this->getScope();
        }

        return $this->create($key, $value, $lifetime, $scope);
    }

    /**
     * Deletes a dataset from cache.
     *
     * @param string $key   The key to delete data of
     * @param string $scope The scope to look for key
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if entry was deleted successful, otherwise FALSE
     */
    public function delete($key, $scope = null)
    {
        if ($scope === null) {
            $scope = $this->getScope();
        }

        return $this->getContainer()->delete($key, $scope);
    }

    /**
     * Checks if an cached object exists and return result.
     *
     * This method is intend to check if an cached object exists and return result.
     *
     * @param string $key   The id of the object to check
     * @param string $scope The scope of the object to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if exists, otherwise FALSE
     */
    public function exists($key, $scope = null)
    {
        if ($scope === null) {
            $scope = $this->getScope();
        }

        return $this->getContainer()->exists($key, $scope);
    }

    /**
     * Checks whether an entry is expired.
     *
     * @param string $key      The key of the element to check
     * @param string $scope    The cache scope
     * @param int    $lifetime The maximum age for the cached data in seconds - 0 for endless. If the cached
     *                         data is older but the given lifetime it will be removed from the cache. You don't
     *                         have to provide this argument if you call expired(). Every dataset knows it's
     *                         expire date and will be removed automatically. Use this only if you know what
     *                         you're doing...
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if entry is expired, otherwise FALSE
     *
     * @throws Doozr_Cache_Service_Exception
     */
    public function expired($key, $scope = null, $lifetime = null)
    {
        if ($scope === null) {
            $scope = $this->getScope();
        }

        if ($lifetime === null) {
            $lifetime = $this->getGcMaximumLifetime();
        }

        return $this->getContainer()->expired($key, $scope, $lifetime);
    }

    /**
     * Calls the garbage-collector of the cache-container.
     *
     *  - is forced ($force = true)
     *  - there was a run before and this run is older = gcProbabilityTime
     *
     * @param string $scope           The scope to look for elements in
     * @param int    $maximumLifetime The maximum lifetime of an element
     * @param bool   $force           TRUE to force a garbage collection run, otherwise FALSE (default) to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int The number of elements collected in gc run
     */
    public function garbageCollection($scope = null, $maximumLifetime = null, $force = false)
    {
        // Default result
        $result = 0;

        // Get our active scope if no special one passed
        if ($scope === null) {
            $scope = $this->getScope();
        }

        // The maximum lifetime for the entry in seconds
        if ($maximumLifetime === null) {
            $maximumLifetime = $this->getGcMaximumLifetime();
        }

        // Start the randomizer ...
        srand(microtime(true));

        // Force || Time and probability based
        if (
            ($force) ||                                                                // Forced run
            (
                self::$gcLastRunTimestamp !== null &&                                  // Must be run and ...
                self::$gcLastRunTimestamp < (time() + $this->getGcProbabilityTime())   // ... more than 30 min. before
            ) ||
            (rand(1, 100) <= $this->gcProbability)                                     // or randomizer hit?
        ) {
            $result                   = $this->getContainer()->garbageCollection($scope, $maximumLifetime);
            self::$gcLastRunTimestamp = time();
        }

        return $result;
    }

    /**
     * Removes all scope datasets from cache.
     *
     * @param string $scope The scope of the cache item
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int Number of removed entries
     */
    public function purge($scope = null)
    {
        // Get scope if not passed
        if (null === $scope) {
            $scope = $this->getScope();
        }

        // Return result of purge
        return $this->getContainer()->purge($scope);
    }

    /**
     * Checks existence of a container and returns result.
     *
     * This method is intend to check existence of a container and returns result.
     *
     * @param string $container The container name to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if $container exists, otherwise FALSE
     */
    public function containerExists($container)
    {
        // Correct format filename
        $container = ucfirst(strtolower($container));

        // The filename to include
        $filename = $this->retrievePathToCurrentClass().
                    'Service'.DIRECTORY_SEPARATOR.'Container'.DIRECTORY_SEPARATOR.$container.'.php';

        return file_exists($filename) ? (include_once $filename) : false;
    }

    /**
     * Setter for gcProbabilityTime.
     *
     * @param int $gcProbabilityTime The gcProbabilityTime to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setGcProbabilityTime($gcProbabilityTime)
    {
        $this->gcProbabilityTime = $gcProbabilityTime;
    }

    /**
     * Setter for gcProbabilityTime.
     *
     * @param int $gcProbabilityTime The gcProbabilityTime to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function gcProbabilityTime($gcProbabilityTime)
    {
        $this->setGcProbabilityTime($gcProbabilityTime);

        return $this;
    }

    /**
     * Getter for gcProbabilityTime.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|bool GarbageCollectorMaxLifetime if set, otherwise NULL
     */
    protected function getGcProbabilityTime()
    {
        return $this->gcProbabilityTime;
    }

    /**
     * Setter for gcMaximumLifetime.
     *
     * @param int $gcMaximumLifetime The gcMaximumLifetime to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setGcMaximumLifetime($gcMaximumLifetime)
    {
        $this->gcMaximumLifetime = $gcMaximumLifetime;
    }

    /**
     * Fluent setter for gcMaximumLifetime.
     *
     * @param int $gcMaximumLifetime The gcMaximumLifetime to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance of this class for chaining (fluent interface pattern)
     */
    public function gcMaximumLifetime($gcMaximumLifetime)
    {
        $this->gcMaximumLifetime = $gcMaximumLifetime;

        return $this;
    }

    /**
     * Getter for gcMaximumLifetime.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|bool GarbageCollectorMaxLifetime if set, otherwise NULL
     */
    public function getGcMaximumLifetime()
    {
        return $this->gcMaximumLifetime;
    }

    /**
     * Cleanup.
     *
     * This method is intend to cleanup on class destruct.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @throws Psr\Cache\InvalidArgumentException
     */
    public function __teardown()
    {
        // Don't cleanup if cache is disabled! Disabled = meaning NEVER take action!
        if (true === $this->isEnabled()) {
            $this->garbageCollection(
                $this->getScope(),             // Retrieve scope from this service instance
                $this->getGcMaximumLifetime(), // The lifetime used to check entries
                false                          // At tear-down don't force
            );

            // Save deferred items
            $this->commit();
        }
    }

    /**
     * Setter for container.
     *
     * @param string $container        The container to use
     * @param array  $containerOptions The options to pass to container
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     *
     * @throws Doozr_Cache_Service_Exception
     */
    protected function setContainer(
                $container,
        array $containerOptions = []
    ) {
        // check if container-type exists
        if ($this->containerExists($container) === false) {
            throw new Doozr_Cache_Service_Exception(
                sprintf('Error! Container: "%s" does not exist! Please choose an existing container.', $container)
            );
        }

        // Get container with new options
        return
            $this->container = $this->containerFactory(
                $container,
                array_merge(
                    $this->getDefaultOptions(),
                    $containerOptions
                )
            )
        ;
    }

    /**
     * Setter for container.
     *
     * @param string $container        The container to use
     * @param array  $containerOptions The options to pass to container
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     *
     * @throws Doozr_Cache_Service_Exception
     */
    protected function container(
                $container,
        array $containerOptions = null
    ) {
        $this->setContainer($container, $containerOptions);

        return $this;
    }

    /**
     * Getter for container.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|Doozr_Cache_Service_Container_Interface $container The container
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * Setter for deferredItems.
     *
     * @param bool $deferredItems The deferredItems to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setDeferredItems($deferredItems)
    {
        $this->deferredItems = $deferredItems;
    }

    /**
     * Fluent setter for deferredItems.
     *
     * @param bool $deferredItems The deferredItems to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance of this class for chaining (fluent interface pattern)
     */
    protected function deferredItems($deferredItems)
    {
        $this->deferredItems = $deferredItems;

        return $this;
    }

    /**
     * Getter for deferredItems.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|bool DeferredItems if set, otherwise NULL
     */
    protected function getDeferredItems()
    {
        return $this->deferredItems;
    }

    /**
     * Setter for unix.
     *
     * @param bool $unix The unix to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setUnix($unix)
    {
        $this->unix = $unix;
    }

    /**
     * Fluent setter for unix.
     *
     * @param bool $unix The unix to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance of this class for chaining (fluent interface pattern)
     */
    protected function unix($unix)
    {
        $this->unix = $unix;

        return $this;
    }

    /**
     * Getter for unix.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|bool Unix if set, otherwise NULL
     */
    protected function getUnix()
    {
        return $this->unix;
    }

    /**
     * Setter for enabled.
     *
     * @param bool $enabled The enabled to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * Fluent: Setter for enabled.
     *
     * @param bool $enabled The enabled to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function enabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Getter for enabled.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|bool Enabled if set, otherwise NULL
     */
    protected function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Boolean isser for enabled.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if enabled, otherwise FALSE
     */
    protected function isEnabled()
    {
        return true === $this->enabled;
    }

    /**
     * Setter for scope.
     *
     * This method is intend to generate and set the scope.
     *
     * @param string $scope The dataset scope to use
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setScope($scope)
    {
        $this->scope = $scope;
    }

    /**
     * Setter for scope.
     *
     * This method is intend to generate and set the scope.
     *
     * @param string $scope The dataset scope to use
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function scope($scope)
    {
        $this->setScope($scope);

        return $this;
    }

    /**
     * Getter for scope.
     *
     * This method returns the scope of the current dataset
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The scope
     */
    protected function getScope()
    {
        return $this->scope;
    }

    /**
     * Stores a dataset with additional user-defined data.
     *
     * @param string $key      The key of the entry
     * @param mixed  $value    The value of the entry
     * @param int    $lifetime The time to expire
     * @param string $scope    The dataset scope
     * @param string $userdata The userdata to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if dataset could be written, otherwise FALSE
     *
     * @throws Doozr_Cache_Service_Exception
     */
    protected function createExtended(
        $key,
        $value,
        $lifetime,
        $scope,
        $userdata = ''
    ) {
        try {
            $result = $this->getContainer()->create(
                $key,
                $value,
                $lifetime,
                $scope,
                $userdata
            );
        } catch (Exception $e) {
            throw new Doozr_Cache_Service_Exception(
                sprintf('Error creating cache entry for with key: "%s".', $key)
            );
        }

        return $result !== false;
    }

    /**
     * Factory for container.
     *
     * @param string $container        The container to create
     * @param array  $containerOptions The configuration/options for the container
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Cache_Service_Container_Interface Instance of the container
     *
     * @throws Doozr_Cache_Service_Exception
     */
    protected function containerFactory($container, array $containerOptions = [])
    {
        $container = ucfirst(strtolower($container));
        $class     = __CLASS__.'_Container_'.$container;
        $file      = DOOZR_DOCUMENT_ROOT.'Service'.DIRECTORY_SEPARATOR.
                        str_replace('_', DIRECTORY_SEPARATOR, $class).'.php';

        include_once $file;

        return new $class($containerOptions);
    }

    /**
     * Returns the default options for container.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The default options
     */
    protected function getDefaultOptions()
    {
        return [
            'unix'  => $this->unix,
            'scope' => $this->getScope(),
        ];
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Fulfill: Psr\Cache\CacheItemPoolInterface
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns a Cache Item representing the specified key.
     *
     * This method must always return an ItemInterface object, even in case of a cache miss. It MUST NOT return null.
     *
     * @param string $key The key for which to return the corresponding Cache Item.
     *
     * @throws InvalidArgumentException If the $key string is not a legal value an exception MUST be thrown.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return CacheItemInterface The corresponding Cache Item.
     */
    public function getItem($key)
    {
        // Return an item in all cases
        $item = new CacheItem($key);

        try {
            $data = $this->read($key, null, true);
            $item->set($data[2], $data[0] - time());

        } catch (Doozr_Cache_Service_Exception $exception) {

        }

        return $item;
    }

    /**
     * Returns a traversable set of cache items.
     *
     * @param array $keys An indexed array of keys of items to retrieve.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array|\Traversable A traversable collection of Cache Items keyed by the cache keys of each item. A Cache
     *                            item will be returned for each key, even if that key is not found. However, if no keys
     *                            are specified then an empty traversable MUST be returned instead.
     */
    public function getItems(array $keys = [])
    {
        $collection = [];

        foreach ($keys as $key) {
            $collection[$key] = $this->getItem($key);
        }

        return $collection;
    }

    /**
     * Deletes all items in the pool.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool True if the pool was successfully cleared. False if there was an error.
     */
    public function clear()
    {
        return (false !== $this->purge());
    }

    /**
     * Removes multiple items from the pool.
     *
     * @param array $keys An array of keys that should be removed from the pool.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this The invoked object.
     */
    public function deleteItems(array $keys)
    {
        foreach ($keys as $key) {
            try {
                $this->delete($key);

            } catch (Exception $e) {}
        }

        return $this;
    }

    /**
     * Persists a cache item immediately.
     *
     * @param CacheItemInterface $item The cache item to save.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this The invoked object.
     */
    public function save(CacheItemInterface $item)
    {
        $this->create($item->getKey(), $item->get(), $item->getExpiration());

        return $this;
    }

    /**
     * Sets a cache item to be persisted later.
     *
     * @param CacheItemInterface $item The cache item to save.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return static The invoked object.
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        $this->deferredItems[] = $item;

        return $this;
    }

    /**
     * Persists any deferred cache items.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if all not-yet-saved items were successfully saved. FALSE otherwise.
     */
    public function commit()
    {
        // Save deferred items
        $deferredItems = $this->getDeferredItems();

        foreach ($deferredItems as $deferredItem) {
            $this->save($deferredItem);
        }
    }
}
