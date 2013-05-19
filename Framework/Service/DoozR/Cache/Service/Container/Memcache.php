<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Cache - Service - Container - Memcache
 *
 * Memcache.php - Memcache-Container of the Caching Service.
 *
 * PHP versions 5
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
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Cache
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Cache/Service/Container.php';
require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Cache/Service/Container/Interface.php';

/**
 * DoozR - Cache - Service - Container - Memcache
 *
 * Memcache-Container of the Caching Service.
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Cache
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 * @DoozRType  Multiple
 */
class DoozR_Cache_Service_Container_Memcache extends DoozR_Cache_Service_Container
implements DoozR_Cache_Service_Container_Interface
{
    /**
     * contains the hostname for the connection
     *
     * @var string
     * @access protected
     */
    protected $hostname = '127.0.0.1';

    /**
     * contains the port for the connection
     *
     * @var string
     * @access protected
     */
    protected $port = '11211';

    /**
     * contains the memcache instance (connection)
     *
     * @var object
     * @access private
     */
    private $_connection;

    /**
     * TRUE  to compress content with zlib from memcache
     * FALSE to store content uncompressed
     *
     * @var boolean
     * @access private
     */
    private $_compress = true;

    /**
     * the allowed options specific for this container
     *
     * @var array
     * @access protected
     */
    protected $thisContainerAllowedOptions = array(
        'hostname',
        'port'
    );

    /**
     * the allowed options specific for this container
     *
     * @var array
     * @access protected
     */
    const UNIQUE_IDENTIFIER = __CLASS__;


    /**
     * constructor
     *
     * This method is intend to act as constructor.
     * If you use custom configuration options -> ensure that they are enabled via $thisContainerAllowedOptions!
     *
     * @param array $options Custom configuration options
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object instance of this class
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

        // init a connection to server
        /* @var $this->_connection Memcache */
        $this->_connection = $this->_connect($this->hostname, $this->port);

        // if highwater = max -> retrieve configuration from server to define highwater
        if ($this->highwater === 'max') {
            $serverConfiguration = $this->_connection->getExtendedStats();
            $this->highwater     = $serverConfiguration[$this->hostname.':'.$this->port]['limit_maxbytes'];
        }
    }

    /**
     * Stores a dataset
     *
     * This method is intend to write data to cache.
     * WARNING: If you supply userdata it must not contain any linebreaks, otherwise it will break the filestructure.
     *
     * @param string  $id      The dataset Id
     * @param string  $value   The data to write to cache
     * @param integer $expires Date/Time on which the cache-entry expires
     * @param string  $group   The dataset group
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success
     * @access public
     * @throws DoozR_Cache_Service_Exception
     */
    public function create($id, $value, $expires, $group)
    {
        // unique key
        $key = $this->_uniquifyId($id, $group);

        // flush
        $this->flushPreload($id, $group);

        // prepare
        $flags  = ($this->_compress === true) ? MEMCACHE_COMPRESSED : 0;

        if (
            $this->_connection->set(
                $key,
                $value,
                $flags,
                $expires    //$this->getExpiresAbsolute($expires)
            ) !== true
        ) {
            throw new DoozR_Cache_Service_Exception(
                'Error while creating dataset!'
            );
        }

        return true;
    }

    /**
     * This method is intend to read data from cache.
     *
     * @param string $id    The dataset Id
     * @param string $group The dataset group
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Array containing data from cache on success, otherwise FALSE
     * @access public
     */
    public function read($id, $group)
    {
        // construct uid
        $key = $this->_uniquifyId($id, $group);

        // try to read from cache (server) by uid
        $result = $this->_connection->get(
            $key
        );

        return $result;
        //return ($result !== false) ? $this->decode($result) : $result;
    }

    /**
     * updates a dataset
     *
     * This method is intend to write data to cache. This is a real implementation
     * Memcached DB supports updates by calling replace().
     *
     * @param string  $id       The dataset Id
     * @param string  $value    The data to write to cache
     * @param integer $expires  Date/Time on which the cache-entry expires
     * @param string  $group    The dataset group
     * @param string  $userdata The custom userdata to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success
     * @access public
     */
    public function update($id, $value, $expires, $group, $userdata)
    {
        return $this->_connection->replace(
            md5(self::UNIQUE_IDENTIFIER.$group.$id),
            array(
                $this->getExpiresAbsolute($expires),
                $userdata,
                $this->encode($value)
            ),
            0,
            $expires
        );
    }

    /**
     * removes a dataset finally from container
     *
     * This method is intend to remove an dataset finally from container.
     *
     * @param string $id    The id of the dataset
     * @param string $group The group of the dataset
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access protected
     * @throws DoozR_Cache_Service_Exception
     */
    public function delete($id, $group)
    {
        // IMPORTANT: flush preload
        $this->flushPreload($id, $group);

        // build identifier
        $key = md5(self::UNIQUE_IDENTIFIER.$group.$id);

        if ($this->_connection->delete($key, 0)) {
            return true;
        } else {
            throw new DoozR_Cache_Service_Exception(
                'Error while deleting key: "'.$key.'" of group: "'.$group.'"!'
            );
        }
    }

    /**
     * returns the current status of the memcache server
     *
     * This method is intend to return the current status of the memcache server.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Status of server as ARRAY, otherwise FALSE
     * @access public
     */
    public function getStatus()
    {
        return $this->_connection->getExtendedStats();
    }

    /**
     * returns the caching status of a given id and group
     *
     * This method is intend to return the caching status of a given id and group.
     *
     * @param string $id    The Id for lookup
     * @param string $group The group for lookup
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed BOOLEAN false if not found, otherwise the result from cache
     * @access public
     */
    public function isCached($id, $group)
    {
        return $this->read($id, $group);
    }

    /**
     * deletes all entries which exceeds highwater level
     *
     * This method is intend to delete all entries which exceed the highwater marker. For
     * expiration we do not care cause memcache has its own garbage collector
     *
     * @param integer $maxlifetime Maximum lifetime in seconds of an no longer used/touched entry
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean The result of the operation
     * @access public
     */
    public function garbageCollection($maxlifetime)
    {
        pred('GARBAGECOLLECTOR!');

        // do the flush
        parent::garbageCollection($maxlifetime);

        //
        $datasets = array();

        // get all entries from server
        $entries = $this->_getEntries();

        // build identifier
        $identifier = md5(self::UNIQUE_IDENTIFIER).$group;

        // caluclate identifier length
        $length = strlen($identifier);

        foreach ($entries as $key => $entry) {
            if (substr($key, 0, $length) == $identifier) {
                $datasets[] = $entry;
                pred($datasets);
            }
        }

        /*
        // check the space used by the cache entries
        if ($this->_totalSize > $this->highwater) {
            krsort($this->_entries);
            reset($this->_entries);

            while ($this->_totalSize > $this->lowwater && list($lastmod, $entry) = each($this->_entries)) {
                if (@unlink($entry['file'])) {
                    $this->_totalSize -= $entry['size'];
                } else {
                    throw new DoozR_Cache_Service_Exception(
                        'Can\'t delete '.$entry['file'].'. Check the permissions.'
                    );
                }
            }
        }

        $this->_entries = array();
        $this->_totalSize = 0;
        */

        // return the result of the operation
        return $result;
    }

    /**
     * checks if a dataset exists
     *
     * This method is intend to check if a dataset exists.
     *
     * @param string $id    The id of the dataset
     * @param string $group The group of the dataset
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if file exist, otherwise FALSE
     * @access protected
     */
    protected function idExists($id, $group)
    {
        // build identifier
        $key = md5(self::UNIQUE_IDENTIFIER.$group.$id);

        return ($this->_connection->get($key) === false) ? false : true;
    }

    /**
     * flushes the cache
     *
     * This method is intend to flush the cache. It removes all caches datasets from the cache.
     *
     * @param string $group The dataset group to flush
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Number of removed datasets on success, otherwise FALSE
     * @access public
     */
    public function flush($group)
    {
        // flush
        $this->flushPreload();

        // delete entries and retrieve count
        $removedEntries = $this->_removeEntries($group);

        // return count of removed entries
        return $removedEntries;
    }

    /**
     * Returns an unique id for passed id and group
     *
     * @param string $id    The id to generate id for
     * @param string $group The group to generate id for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The unique identifier
     * @access private
     */
    private function _uniquifyId($id, $group)
    {
        return md5(self::UNIQUE_IDENTIFIER.serialize($id));
    }

    /**
     * deletes a directory and all files in it
     *
     * This method is intend to delete a directory and all files in it.
     *
     * @param string $group The group of entries to remove
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Number of removed entries on success, otherwise FALSE
     * @access private
     * @throws DoozR_Cache_Service_Exception
     */
    private function _removeEntries($group)
    {
        // count of entries removed
        $entriesRemoved = 0;

        // get all entries from server
        $entries = $this->_getEntries();

        // build identifier
        $identifier = md5(self::UNIQUE_IDENTIFIER).$group;

        // caluclate identifier length
        $length = strlen($identifier);

        foreach ($entries as $key => $entry) {
            if (substr($key, 0, $length) == $identifier) {
                if ($this->_connection->delete($key, 0)) {
                    ++$entriesRemoved;
                } else {
                    throw new DoozR_Cache_Service_Exception(
                        'Error while removing key: "'.$key.'" of group: "'.$group.'" from memcache!'
                    );
                }
            }
        }

        // return the count of removed entries
        return $entriesRemoved;
    }

    /**
     * returns all entries from memcache server
     *
     * This method is intend to return all entries from memcache server.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array All entries form memcache server
     * @access private
     * @throws DoozR_Cache_Service_Exception
     */
    private function _getEntries()
    {
        $list     = array();
        $allSlabs = $this->_connection->getExtendedStats('slabs');
        $items    = $this->_connection->getExtendedStats('items');

        foreach ($allSlabs as $server => $slabs) {
            foreach ($slabs as $slabId => $slabMeta) {
                $cdump = $this->_connection->getExtendedStats('cachedump', (int)$slabId);
                foreach ($cdump as $server => $entries) {
                    if ($entries) {
                        foreach ($entries as $eName => $eData) {
                            $list[$eName] = array(
                                 'key' => $eName,
                                 'server' => $server,
                                 'slabId' => $slabId,
                                 'detail' => $eData,
                                 'age' => $items[$server]['items'][$slabId]['age'],
                            );
                        }
                    }
                }
            }
        }

        ksort($list);
        return $list;
    }

    /**
     * connects to a given server and port
     *
     * This method is intend to connect to a given server and port.
     *
     * @param string $hostname The hostname to connect to
     * @param string $port     The port to connect to
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Memcache The created instance of memcache
     * @access private
     * @throws DoozR_Cache_Service_Exception
     */
    private function _connect($hostname, $port)
    {
        $memcache = new Memcache();

        // API requires to add server first
        $memcache->addServer($hostname, $port);

        // then we check if its up
        if ($memcache->getServerStatus($hostname, $port) == 0) {
            throw new DoozR_Cache_Service_Exception(
                'Server seems to be down. Could not connect to hostname: "'.$hostname.'" on Port: "'.$port.'".'
            );
        }

        // and finally we try to connect
        if (!@$memcache->connect($hostname, $port)) {
            throw new DoozR_Cache_Service_Exception(
                'Error while connecting to host: "'.$hostname.'" on Port: "'.$port.'". Connection failed.'
            );
        }

        // return instance on success
        return $memcache;
    }
}

?>
