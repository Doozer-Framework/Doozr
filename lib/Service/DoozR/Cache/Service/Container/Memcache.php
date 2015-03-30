<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Cache - Service - Container - Memcache
 *
 * Memcache.php - Container Memcache: Serves I/O access to memcached.
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
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
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Cache
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Cache/Service/Container.php';

/**
 * DoozR - Cache - Service - Container - Memcache
 *
 * Container Memcache: Serves I/O access to memcached.
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Cache
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Cache_Service_Container_Memcache extends DoozR_Cache_Service_Container
{
    /**
     * The hostname used for connection.
     *
     * @var string
     * @access protected
     */
    protected $hostname = '127.0.0.1';

    /**
     * The port used for the connection.
     *
     * @var int
     * @access protected
     */
    protected $port = 11211;

    /**
     * contains the memcache instance (connection)
     *
     * @var Memcache
     * @access protected
     */
    protected $connection;

    /**
     * Whether to use compression on memcached storage.
     *
     * TRUE  to compress content with zlib from memcache
     * FALSE to store content uncompressed
     *
     * @var bool
     * @access protected
     */
    protected $compress = false;

    /**
     * Highwater mark - maximum space required by all cache entries.
     *
     * Whenever the garbage collection runs it checks the amount of space
     * required by all cache entries. If it's more than n (highwater) bytes
     * the garbage collection deletes as many entries as necessary to reach the
     * lowwater mark.
     *
     * @var int
     * @see lowwaterMarker
     * @access protected
     * @see parent::highwaterMarker
     */
    protected $highwaterMarker = -1;

    /**
     * Allowed options specific for this container
     *
     * @var array
     * @access protected
     */
    protected $thisContainerAllowedOptions = array(
        'hostname',
        'port'
    );

    /**
     * Type for slabs.
     *
     * @var string
     * @access public
     * @const
     */
    const MEMCACHE_TYPE_SLABS = 'slabs';

    /**
     * Type for items.
     *
     * @var string
     * @access public
     * @const
     */
    const MEMCACHE_TYPE_ITEMS = 'items';

    /**
     * Type for cachedump.
     *
     * @var string
     * @access public
     * @const
     */
    const MEMCACHE_TYPE_CACHEDUMP = 'cachedump';


    /**
     * Constructor.
     *
     * @param array $options Custom configuration options
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Cache_Service_Container_Memcache
     * @access public
     * @throws DoozR_Cache_Service_Exception
     */
    public function __construct(array $options = array())
    {
        // do the check and transfer of allowed options
        parent::__construct($options);

        // check requirements!
        if (!extension_loaded('memcache')) {
            throw new DoozR_Cache_Service_Exception(
                'In order to use memcache container for caching, the memcache extension must be loaded.'
            );
        }

        // Init a connection to server
        $this->setConnection(
            $this->connect($this->getHostname(), $this->getPort())
        );

        // Auto adjust max storage (highwater)
        if ($this->getHighwaterMarker() === -1) {
            $serverStatistics = $this->getConnection()->getExtendedStats();

            $server = $this->getHostname() . ':' . $this->getPort();

            if (isset($serverStatistics[$server]['limit_maxbytes']) === false) {
                throw new DoozR_Cache_Service_Exception(
                    sprintf('Could not retrieve "limit_maxbytes" for server "%s"', $server)
                );
            }

            $this->setHighwaterMarker(
                $serverStatistics[$this->getHostname().':'.$this->getPort()]['limit_maxbytes']
            );
        }
    }

    /**
     * Creates a new dataset from input and stores it in cache.
     *
     * WARNING: If you supply userdata it must not contain any linebreaks,
     * otherwise it will break the filestructure.
     *
     * @param string $key       The entry key
     * @param string $value     The entry value
     * @param int    $lifetime  Date/Time on which the cache-entry expires
     * @param string $namespace The dataset namespace
     * @param string $userdata  The custom userdata to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     * @throws DoozR_Cache_Service_Exception
     */
    public function create($key, $value, $lifetime, $namespace, $userdata = null)
    {
        // Get internal used key
        $key = $this->calculateUuid($key . $namespace);

        // On create we need to purge the old entry from runtime cache ...
        $this->purgeRuntimeCache($key, $namespace);

        // Build dataset from input
        $dataset = array(
            $this->getExpiresAbsolute($lifetime),
            $userdata,
            $this->encode($value),
            $namespace,
        );

        if (
            true !== $result = $this->connection->set(
                $key,
                $dataset,
                ($this->getCompress() === true) ? MEMCACHE_COMPRESSED : 0,
                $this->getExpiresAbsolute($lifetime)
            )
        ) {
            throw new DoozR_Cache_Service_Exception(
                'Error while creating dataset!'
            );
        }

        if ($result === true) {
            // On create we need to purge the old entry from runtime cache ...
            $this->addToRuntimeCache(
                $key,
                $dataset,
                $namespace
            );
        }

        return true;
    }

    /**
     * Reads an entry from cache.
     *
     * @param string $key       The key to read data from
     * @param string $namespace The namespace used for that
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool|array The data from cache if set, otherwise FALSE
     * @access public
     */
    public function read($key, $namespace)
    {
        // Get internal used key
        $key = $this->calculateUuid($key . $namespace);

        // Try to retrieve data from runtime cache ...
        $dataset = $this->getFromRuntimeCache($key, $namespace);

        // Check for result from runtime cache
        if ($dataset === false) {
            // Try to read from real cache ...
            $dataset = $this->getConnection()->get($key);

            // We need to put to runtime cache? dont?
            $this->addToRuntimeCache($key, $dataset, $namespace);
        }

        // Decode value!
        $dataset[2] = $this->decode($dataset[2]);

        return $dataset;
    }

    /**
     * Updates data in cache.
     *
     * @param string $key       The dataset Id
     * @param string $value     The data to write to cache
     * @param string $namespace The dataset namespace
     * @param int    $lifetime  Date/Time on which the cache-entry expires
     * @param string $userdata  The custom userdata to add
     *
     * @return bool TRUE on success, otherwise FALSE
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     * @throws DoozR_Cache_Service_Exception
     */
    public function update($key, $value, $namespace, $lifetime = null, $userdata = null)
    {
        // Get internal used key
        $key = $this->calculateUuid($key . $namespace);

        // Build dataset from input
        $dataset = array(
            $this->getExpiresAbsolute($lifetime),
            $userdata,
            $this->encode($value),
            $namespace,
        );

        if (
            true !== $result = $this->connection->replace(
                $key,
                $dataset,
                ($this->getCompress() === true) ? MEMCACHE_COMPRESSED : 0,
                $this->getExpiresAbsolute($lifetime)
            )
        ) {
            throw new DoozR_Cache_Service_Exception(
                'Error while updating dataset!'
            );
        }

        if ($result === true) {
            // On create we need to purge the old entry from runtime cache ...
            $this->addToRuntimeCache(
                $key,
                $dataset,
                $namespace
            );
        }

        return true;
    }

    /**
     * Deletes a dataset from cache.
     *
     * @param string $key The key of the cache entry
     * @param string $namespace The namespace of the dataset
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success, otherwise FALSE
     * @access protected
     * @throws DoozR_Cache_Service_Exception
     */
    public function delete($key, $namespace)
    {
        // Get internal used key
        $key = $this->calculateUuid($key . $namespace);

        // On create we need to purge the old entry from runtime cache ...
        $this->purgeRuntimeCache($key, $namespace);

        // Try to delete!
        $result = $this->connection->delete($key);

        if ($result === false) {
            throw new DoozR_Cache_Service_Exception(
                sprintf('Error while deleting key: "%s" of group: "%s"!', $key, $namespace)
            );
        }

        return $result;
    }

    /**
     * Checks if a dataset exists
     *
     * This method is intend to check if a dataset exists.
     *
     * @param string $key       The key of the dataset
     * @param string $namespace The namespace of the dataset
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if dataset exist, otherwise FALSE
     * @access public
     * @throws DoozR_Cache_Service_Exception
     */
    public function exists($key, $namespace)
    {
        // Get internal used key
        $key = $this->calculateUuid($key . $namespace);

        // Assume it does not exist
        $result = false;

        if ($this->getFromRuntimeCache($key, $namespace) !== false) {
            $result = true;

        } else {
            $value = $this->connection->get($key);

            if ($value !== false) {
                $this->addToRuntimeCache($key, $value, $namespace);
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Checks if an element for a passed key & namespace combination is already expired.
     *
     * @param string $key       The key to check
     * @param string $namespace The namespace to look in
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if element is expired, otherwise FALSE
     * @access public
     * @throws DoozR_Cache_Service_Exception
     */
    public function expired($key, $namespace)
    {
        // Get internal used key
        $key = $this->calculateUuid($key . $namespace);

        // Assume item expired
        $result = true;

        // Read content from file with exclusive locking
        $dataset = $this->getConnection()->get($key);

        // Check if lifetime of entry (is written within the entry) smaller current timestamp ( = not expired = valid)
        if ($dataset[0] > time()) {
            $this->addToRuntimeCache(
                $key,
                $dataset,
                $namespace
            );

            $result = false;
        }

        return $result;
    }

    /**
     * Flushes the cache
     *
     * This method is intend to purge the cache. It removes all caches datasets from the cache.
     *
     * @param string $namespace The dataset namespace to purge
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Number of removed datasets on success, otherwise FALSE
     * @access public
     */
    public function purge($namespace)
    {
        $this->purgeRuntimeCache();

        // delete entries and retrieve count
        $removedEntries = $this->removeEntries($namespace);

        // return count of removed entries
        return $removedEntries;
    }

    /**
     * deletes all expired files
     *
     * This method is intend to delete all expired files.
     * Garbage collection for files is a rather "expensive", "long time" operation. All files in the cache
     * directory have to be examined which means that they must be opened for reading, the expiration date has
     * to be read from them and if neccessary they have to be unlinked (removed). If you have a user comment
     * for a good default gc probability please add it to to the inline docs.
     *
     * @param string $namespace The namespace to delete items from
     * @param int    $lifetime  The maximum age for an entry of the cache
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean The result of the operation
     * @access public
     * @throws DoozR_Cache_Service_Exception
     */
    public function garbageCollection($namespace, $lifetime)
    {
        // Run successful?
        $result = true;

        if ($this->getConnection() !== null) {
            $result = $this->doGarbageCollection($namespace, $lifetime);
        }

        // Return the result of the operation
        return $result;
    }

    /**
     * Setter for port.
     *
     * @param int The port to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * Setter for port.
     *
     * @param int The port to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function port($port)
    {
        $this->setPort($port);
        return $this;
    }

    /**
     * Getter for port.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return int|null The port if set, otherwise NULL
     * @access protected
     */
    protected function getPort()
    {
        return $this->port;
    }

    /**
     * Setter for compress.
     *
     * @param bool $compress TRUE to use compression, otherwise FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setCompress($compress)
    {
        $this->compress = $compress;
    }

    /**
     * Setter for compress.
     *
     * @param bool $compress TRUE to use compression, otherwise FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function compress($compress)
    {
        $this->setCompress($compress);
        return $this;
    }

    /**
     * Getter for compress.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if compression enabled, otherwise FALSE
     * @access protected
     */
    protected function getCompress()
    {
        return $this->compress;
    }

    /**
     * Setter for hostname.
     *
     * @param string The hostname to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setHostname($hostname)
    {
        $this->hostname = $hostname;
    }

    /**
     * Setter for hostname.
     *
     * @param string The hostname to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function hostname($hostname)
    {
        $this->setHostname($hostname);
        return $this;
    }

    /**
     * Getter for hostname.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The hostname if set, otherwise NULL
     * @access protected
     */
    protected function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Setter for connection.
     *
     * @param Memcache $connection The connection to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setConnection(Memcache $connection = null)
    {
        $this->connection = $connection;
    }

    /**
     * Setter for connection.
     *
     * @param Memcache $connection The connection to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function connection(Memcache $connection)
    {
        $this->setConnection($connection);
        return $this;
    }

    /**
     * Getter for connection.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Memcache|null The memcache connection instance if connected, otherwise FALSE
     * @access protected
     */
    protected function getConnection()
    {
        return $this->connection;
    }

    /**
     * Connects to a server.
     *
     * @param string $hostname The hostname to connect to
     * @param string $port     The port to connect to
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Memcache The created instance of memcache
     * @access protected
     * @throws DoozR_Cache_Service_Exception
     */
    protected function connect($hostname, $port)
    {
        $memcache = new Memcache();

        // API requires to add server first
        $memcache->addServer($hostname, $port);

        // Finally we try to connect
        try {
            @$memcache->connect($hostname, $port);

        } catch (Exception $e) {
            throw new DoozR_Cache_Service_Exception(
                sprintf('Error while connecting to host: "%s" on Port: "%s". Connection failed.', $hostname, $port)
            );
        }

        // return instance on success
        return $memcache;
    }

    /**
     * Disconnects from a server.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null
     * @access protected
     * @throws DoozR_Cache_Service_Exception
     */
    protected function disconnect()
    {
        $this->getConnection()->close();
        return null;
    }

    /**
     * Removes entries from cache storage recursive.
     *
     * @param string $namespace The namespace to delete/remove/unlink
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Number of removed entries on success, otherwise FALSE
     * @access protected
     * @throws DoozR_Cache_Service_Exception
     */
    protected function removeEntries($namespace)
    {
        // count of entries removed
        $entriesRemoved = 0;

        // Get all entries
        $entries = $this->getAllEntries($namespace);

        // Iterate
        foreach ($entries as $key => $entry) {
            if ($this->getConnection()->delete($key) !== true) {
                throw new DoozR_Cache_Service_Exception(
                    sprintf('Can\'t remove key "%s". Check server status, health and permissions.', $key)
                );
            }
            ++$entriesRemoved;
        }

        // return the count of removed entries
        return $entriesRemoved;
    }

    /**
     * Returns all entries for a passed slab (hostname & port) by passed namespace or all.
     *
     * @param string|null $namespace The namespace to filter on as string, otherwise NULL to fetch all
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array List of entries indexed by key
     * @access protected
     */
    protected function getAllEntries($namespace = null)
    {
        // Assume empty result
        $list     = array();

        $allSlabs = $this->getConnection()->getExtendedStats(self::MEMCACHE_TYPE_SLABS);
        $items    = $this->getConnection()->getExtendedStats(self::MEMCACHE_TYPE_ITEMS);

        // Iterate slabs
        foreach($allSlabs as $server => $slabs) {
            // Iterate single slab
            foreach($slabs as $slabId => $slabMeta) {
                // There is also metadata within array (string index!)
                if (is_int($slabId) === true) {
                    $cachedump = $this->getConnection()->getExtendedStats(self::MEMCACHE_TYPE_CACHEDUMP, (int)$slabId);

                    // Iterate all server from slab
                    foreach ($cachedump as $server => $entries) {

                        if (is_array($entries) === true) {
                            // Check if at least one entry exist
                            if (count($entries) > 0) {

                                // Get data for eacht entry and store in list ...
                                foreach($entries as $key => $data) {
                                    $dataset = $this->getConnection()->get($key);

                                    if (
                                        is_array($dataset) === true &&
                                        isset($dataset[3]) === true &&  // first both checks to be sure we have an dataset
                                        (
                                            $namespace === null ||      // this one to either include all (null) or specific
                                            $namespace === $dataset[3]
                                        )
                                    ) {
                                        // Add to result ...
                                        $list[$key] = array(
                                            'value'     => $dataset,
                                            'namespace' => $dataset[3],
                                            'server'    => $server,
                                            'slabId'    => $slabId,
                                            'age'       => $items[$server]['items'][$slabId]['age'],
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $list;
    }

    /**
     * Does the recursive gc procedure.
     *
     * @param string $namespace The namespace do run gc on
     * @param int    $lifetime  Maximum lifetime in seconds of an no longer used/touched entry
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success, otherwise FALSE
     * @access protected
     * @throws DoozR_Cache_Service_Exception
     */
    protected function doGarbageCollection($namespace, $lifetime)
    {
        $entries = $this->getAllEntries($namespace);

        // Iterate entries ...
        foreach ($entries as $key => $entry) {

            // Checking last access is so much faster then reading from file so we try to exclude file read here!
            if ($entry['age'] > $lifetime) {
                if ($this->getConnection()->delete($key) === false) {
                    throw new DoozR_Cache_Service_Exception(
                        sprintf('Can\'t remove cache entry "%s", skipping. Check permissions.', $key)
                    );
                    continue;
                }
            } else {
                $expire = $entry['value'][0];

                // Remove if expired
                if ($expire <= time()) {
                    if ($this->getConnection()->delete($key) === false) {
                        throw new DoozR_Cache_Service_Exception(
                            sprintf('Can\'t remove cache entry "%s", skipping. Check permissions.', $key)
                        );
                        continue;
                    }
                } else {
                    $this->addEntry(
                        time(),
                        array(
                            'key'  => $key,
                            'size' => strlen($entry['value'][2])
                        )
                    );

                    $this->setTotalSize($this->getTotalSize() + strlen($entry['value'][2]));
                }
            }
        }

        // Check the space used by the cache entries
        if ($this->getTotalSize() > $this->getHighwaterMarker()) {

            $entries = $this->getEntries();
            krsort($entries);
            reset($entries);

            while (($this->getTotalSize() > $this->getLowwaterMarker()) && count($entries) > 0) {
                $entry = array_shift($entries);

                if ($this->getConnection()->delete($key) === true) {
                    $this->setTotalSize($this->getTotalSize() - $entry['size']);

                } else {
                    throw new DoozR_Cache_Service_Exception(
                        sprintf('Can\'t unlink cache file "%s". Check permissions and path.', $entry['file'])
                    );
                }
            }

            // Update
            $this->setEntries($entries);
        }
    }
}
