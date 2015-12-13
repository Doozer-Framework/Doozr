<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Cache - Service
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
 * @package    Doozr_Service
 * @subpackage Doozr_Service_Cache
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Service/Multiple.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Psr/Cache/Interface.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Crud/Interface.php';

use Psr\Cache\Doozr_Psr_Cache_Interface;
use Doozr\Loader\Serviceloader\Annotation\Inject;

/**
 * Doozr - Cache - Service
 *
 * Caching Service for caching operations with support for
 * different container like "Filesystem", "Memcache" ...
 *
 * @category   Doozr
 * @package    Doozr_Service
 * @subpackage Doozr_Service_Cache
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 * @throws     Doozr_Cache_Service_Exception
 * @Inject(
 *     link   = "doozr.registry",
 *     type   = "constructor",
 *     target = "getInstance"
 * )
 */
class Doozr_Cache_Service extends Doozr_Base_Service_Multiple
    implements
    Doozr_Base_Service_Interface,
    Doozr_Psr_Cache_Interface,
    Doozr_Base_Crud_Interface
{
    /**
     * Active namespace
     *
     * @var string
     * @access protected
     */
    protected $namespace;

    /**
     * Active container
     *
     * @var Doozr_Cache_Service_Container_Interface
     * @access protected
     */
    protected $container;

    /**
     * Whether OS is unixoid
     *
     * @var bool
     * @access protected
     */
    protected $unix;

    /**
     * Whether caching is disabled, or not.
     *
     * @var bool
     * @access protected
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
     * @access protected
     */
    protected $gcMaximumLifetime = 86400;

    /**
     * Garbage collection: Probability in percent.
     * 0 = never / 100 always
     *
     * @var int
     * @access protected
     */
    protected $gcProbability = 50;

    /**
     * Garbage collection:
     * probability in seconds
     *
     * 60 sec. * 60 min. = 3600
     *
     * If set to a value above 0 a garbage collection will flush all cache entries older than the specified number
     * of seconds.
     *
     * @var int
     * @access public
     */
    protected $gcProbabilityTime = 3600;

    /**
     * Time of the last run
     *
     * @var int
     * @access protected
     * @static
     */
    protected static $gcLastRunTimestamp;

    /**
     * Container filesystem (default)
     *
     * @var string
     * @access public
     */
    const CONTAINER_FILESYSTEM = 'filesystem';

    /**
     * Container memcache (higher performance)
     *
     * @var string
     * @access public
     */
    const CONTAINER_MEMCACHE = 'memcache';

    /**
     * The default namespace for cache elements.
     *
     * @var string
     * @access public
     */
    const NAMESPACE_DEFAULT = 'doozr.cache';

    /**
     * Constructor.
     *
     * @param string $container        Container to use for caching or a collection with priority to try
     * @param string $namespace        Namespace to group saved/cached elements under
     * @param array  $containerOptions Configuration/options for the container instance
     * @param bool   $unix             TRUE if current OS is unix-type, FALSE if not
     * @param bool   $enabled          Default state is enabled, FALSE to signalize that caching is disabled
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __tearup(
              $container,
              $namespace        = self::NAMESPACE_DEFAULT,
        array $containerOptions = [],
              $unix             = true,
              $enabled          = true
    ) {
        $this
            ->namespace_($namespace)
            ->unix($unix)
            ->container($container, $containerOptions)
            ->enabled($enabled);
    }

    /**
     * Creates an cache entry.
     *
     * @param string  $key       The key of the entry
     * @param mixed   $value     The value of the entry
     * @param int     $lifetime  The time to expire
     * @param string  $namespace The dataset namespace
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if dataset could be written, otherwise FALSE
     * @access public
     * @throws Doozr_Cache_Service_Exception
     */
    public function create(
        $key,
        $value,
        $lifetime  = null,
        $namespace = null
    ) {
        // Get namespace if not passed
        if ($namespace === null) {
            $namespace = $this->getNamespace();
        }

        // Retrieve expiration date
        if ($lifetime === null) {
            $lifetime = $this->getGcMaximumLifetime();
        }

        // Try to create entry
        if ($this->createExtended($key, $value, $lifetime, $namespace) === false) {
            throw new Doozr_Cache_Service_Exception(
                sprintf('Error while creating entry with key: "%s" in namespace: "%s"!', $key, $namespace)
            );
        }

        return true;
    }

    /**
     * Returns the requested dataset it if exists and is not expired.
     *
     * This method is intend to return the requested dataset it if exists and is not expired.
     *
     * @param string $key       The key to read
     * @param string $namespace The namespace to read from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed|null Data from cache, otherwise NULL
     * @access public
     * @throws Doozr_Cache_Service_Exception
     */
    public function read(
        $key,
        $namespace = null
    ) {
        // Assume we will have a false result
        $result = null;

        // Get namespace if not passed
        if ($namespace === null) {
            $namespace = $this->getNamespace();
        }

        // Check if content exists
        if ($this->exists($key, $namespace) !== true) {
            throw new Doozr_Cache_Service_Exception(
                sprintf(
                    'Requested entry with key: "%s" in namespace: "%s" could not be found in cache!', $key, $namespace
                )
            );
        }

        // Check that element is not expired and return ...
        if ($this->expired($key, $namespace) === false) {
            // Then return the content;
            $result = $this->getContainer()->read($key, $namespace);
        }

        // Return only data not meta overhead (key = 2)
        return $result[2];
    }

    /**
     * Updates an entry.
     *
     * @param string $key       The dataset Id
     * @param string $value     The data to write to cache
     * @param int    $lifetime  Date/Time on which the cache-entry expires
     * @param string $namespace The dataset group
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if dataset could be written, otherwise FALSE
     * @access public
     * @throws Doozr_Cache_Service_Exception
     */
    public function update(
        $key,
        $value,
        $lifetime  = null,
        $namespace = null
    ) {
        if ($namespace === null) {
            $namespace = $this->getNamespace();
        }

        return $this->create($key, $value, $lifetime, $namespace);
    }

    /**
     * Deletes a dataset from cache
     *
     * @param string $key       The key to delete data of
     * @param string $namespace The namespace to look for key
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if entry was deleted successful, otherwise FALSE
     * @access public
     */
    public function delete($key, $namespace = null)
    {
        if ($namespace === null) {
            $namespace = $this->getNamespace();
        }

        return $this->getContainer()->delete($key, $namespace);
    }

    /**
     * Checks if an cached object exists and return result.
     *
     * This method is intend to check if an cached object exists and return result.
     *
     * @param string $key       The id of the object to check
     * @param string $namespace The namespace of the object to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if exists, otherwise FALSE
     * @access public
     */
    public function exists($key, $namespace = null)
    {
        if ($namespace === null) {
            $namespace = $this->getNamespace();
        }

        return $this->getContainer()->exists($key, $namespace);
    }

    /**
     * Checks whether an entry is expired.
     *
     * @param string $key             The key of the element to check
     * @param string $namespace       The cache namespace
     * @param int    $lifetime The maximum age for the cached data in seconds - 0 for endless. If the cached
     *                                data is older but the given lifetime it will be removed from the cache. You don't
     *                                have to provide this argument if you call expired(). Every dataset knows it's
     *                                expire date and will be removed automatically. Use this only if you know what
     *                                you're doing...
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if entry is expired, otherwise FALSE
     * @access public
     * @throws Doozr_Cache_Service_Exception
     */
    public function expired($key, $namespace = null, $lifetime = null)
    {
        if ($namespace === null) {
            $namespace = $this->getNamespace();
        }

        if ($lifetime === null) {
            $lifetime = $this->getGcMaximumLifetime();
        }

        return $this->getContainer()->expired($key, $namespace, $lifetime);
    }

    /**
     * Calls the garbage-collector of the cache-container.
     *
     *  - is forced ($force = true)
     *  - there was a run before and this run is older = gcProbabilityTime
     *
     * @param string $namespace       The namespace to look for elements in
     * @param int    $maximumLifetime The maximum lifetime of an element
     * @param bool   $force           TRUE to force a garbage collection run, otherwise FALSE (default) to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return int The number of elements collected in gc run
     * @access public
     */
    public function garbageCollection($namespace = null, $maximumLifetime = null, $force = false)
    {
        // Default result
        $result = 0;

        // Get our active namespace if no special one passed
        if ($namespace === null) {
            $namespace = $this->getNamespace();
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
            $result = $this->getContainer()->garbageCollection($namespace, $maximumLifetime);
            self::$gcLastRunTimestamp = time();
        }

        return $result;
    }

    /**
     * Removes all namespace datasets from cache.
     *
     * @param string $namespace The namespace of the cache item
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return int Number of removed entries
     * @access public
     */
    public function purge($namespace = null)
    {
        // Get namespace if not passed
        if ($namespace === null) {
            $namespace = $this->getNamespace();
        }

        // Return result of purge
        return $this->getContainer()->purge($namespace);
    }

    /**
     * Checks existence of a container and returns result
     *
     * This method is intend to check existence of a container and returns result.
     *
     * @param string $container The container name to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if $container exists, otherwise FALSE
     * @access public
     */
    public function containerExists($container)
    {
        // Correct format filename
        $container = ucfirst(strtolower($container));

        // The filename to include
        $filename  = $this->getPathToClass() .
                     'Service' . DIRECTORY_SEPARATOR . 'Container' . DIRECTORY_SEPARATOR . $container . '.php';

        return (file_exists($filename) ? (include_once $filename) : false);
    }

    /**
     * Setter for gcProbabilityTime.
     *
     * @param int $gcProbabilityTime The gcProbabilityTime to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
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
     * @return $this Instance for chaining
     * @access public
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
     * @return null|bool GarbageCollectorMaxLifetime if set, otherwise NULL
     * @access public
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
     * @return void
     * @access public
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
     * @return $this Instance of this class for chaining (fluent interface pattern)
     * @access public
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
     * @return null|bool GarbageCollectorMaxLifetime if set, otherwise NULL
     * @access public
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
     * @return void
     * @access public
     */
    public function __teardown()
    {
        // Don't cleanup if cache is disabled! Disabled = meaning NEVER take action!
        if (true === $this->isEnabled()) {
            $this->garbageCollection(
                $this->getNamespace(),         // Retrieve namespace from this service instance
                $this->getGcMaximumLifetime(), // The lifetime used to check entries
                false                          // At tear-down don't force
            );
        }
    }

    /**
     * Setter for container.
     *
     * @param string $container       The container to use
     * @param array $containerOptions The options to pass to container
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success, otherwise FALSE
     * @access protected
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
        return (
            $this->container = $this->containerFactory(
                $container,
                array_merge(
                    $this->getDefaultOptions(),
                    $containerOptions
                )
            )
        );
    }

    /**
     * Setter for container.
     *
     * @param string $container       The container to use
     * @param array $containerOptions The options to pass to container
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
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
     * @return null|Doozr_Cache_Service_Container_Interface $container The container
     * @access protected
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * Setter for unix.
     *
     * @param bool $unix The unix to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
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
     * @return $this Instance of this class for chaining (fluent interface pattern)
     * @access protected
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
     * @return null|bool Unix if set, otherwise NULL
     * @access protected
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
     * @return void
     * @access protected
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
     * @return $this Instance for chaining
     * @access protected
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
     * @return null|bool Enabled if set, otherwise NULL
     * @access protected
     */
    protected function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Boolean isser for enabled.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if enabled, otherwise FALSE
     * @access protected
     */
    protected function isEnabled()
    {
        return (true === $this->enabled);
    }

    /**
     * Setter for namespace.
     *
     * This method is intend to generate and set the namespace.
     *
     * @param string $namespace The dataset namespace to use
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Setter for namespace.
     *
     * This method is intend to generate and set the namespace.
     *
     * @param string $namespace The dataset namespace to use
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function namespace_($namespace)
    {
        $this->setNamespace($namespace);
        return $this;
    }

    /**
     * Getter for namespace
     *
     * This method returns the namespace of the current dataset
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The namespace
     * @access protected
     */
    protected function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Stores a dataset with additional user-defined data.
     *
     * @param string  $key       The key of the entry
     * @param mixed   $value     The value of the entry
     * @param int     $lifetime  The time to expire
     * @param string  $namespace The dataset namespace
     * @param string  $userdata  The userdata to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if dataset could be written, otherwise FALSE
     * @access public
     * @throws Doozr_Cache_Service_Exception
     */
    protected function createExtended(
        $key,
        $value,
        $lifetime,
        $namespace,
        $userdata = ''
    ) {
        try {
            $result = $this->getContainer()->create(
                $key,
                $value,
                $lifetime,
                $namespace,
                $userdata
            );

        } catch (Exception $e) {
            throw new Doozr_Cache_Service_Exception(
                sprintf('Error creating cache entry for with key: "%s".', $key)
            );
        }

        return ($result !== false);
    }

    /**
     * Factory for container.
     *
     * @param string $container        The container to create
     * @param array  $containerOptions The configuration/options for the container
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Cache_Service_Container_Interface Instance of the container
     * @access protected
     * @throws Doozr_Cache_Service_Exception
     */
    protected function containerFactory($container, array $containerOptions = [])
    {
        $container = ucfirst(strtolower($container));
        $class     = __CLASS__ . '_Container_' . $container;
        $file      = DOOZR_DOCUMENT_ROOT . 'Service' . DIRECTORY_SEPARATOR .
                     str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';

        include_once $file;

        return new $class($containerOptions);
    }

    /**
     * Returns the default options for container.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The default options
     * @access protected
     */
    protected function getDefaultOptions()
    {
        return array(
            'unix'      => $this->unix,
            'namespace' => $this->getNamespace(),
        );
    }
}
