<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Cache - Service - Container
 *
 * Container.php - Base class of all cache storage container.
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

require_once DOOZR_DOCUMENT_ROOT . 'Service/Doozr/Cache/Service/Container/Interface.php';

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

/**
 * Doozr - Cache - Service - Container
 *
 * Base class of all cache storage container.
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
abstract class Doozr_Cache_Service_Container
    implements
    Doozr_Cache_Service_Container_Interface
{
    /**
     * Whether the OS is unixoid.
     *
     * @var bool
     * @access protected
     */
    protected $unix = true;

    /**
     * Encoding runtimeEnvironment for cache data: base64 or addslashes() (slash).
     * base64 or slash
     *
     * @var string
     * @access protected
     */
    protected $encoding = 'base64';

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
     */
    protected $highwaterMarker = 536870912;

    /**
     * Lowwater mark
     *
     * @var int
     * @see highwaterMarker
     * @access protected
     */
    protected $lowwaterMarker = 178956971;

    /**
     * Total number of bytes required by all cache entries, used within a gc run.
     *
     * @var int
     * @access protected
     */
    protected $totalSize = 0;

    /**
     * List of cache entries, used within runGarbageCollection()
     *
     * @var array
     * @access protected
     */
    protected $entries = [];

    /**
     * Options that can be set in every derived class using it's constructor.
     *
     * @var array
     * @access protected
     */
    protected $allContainerAllowedOptions = array(
        'encoding',
        'highwaterMarker',
        'lowwaterMarker',
        'unix',
        'namespace'
    );

    /**
     * Allowed options specific for this container
     *
     * @var array
     * @access protected
     */
    protected $thisContainerAllowedOptions = [];

    /**
     * Dumb runtime cache implementation.
     *
     * @var array
     * @access protected
     */
    protected $runtimeCache = [];

    /**
     * The encoding base64
     *
     * @var string
     * @access public
     */
    const ENCODING_BASE64 = 'base64';

    /**
     * The namespace separator
     *
     * @example doozr.cache.front.request contains "." as separator
     *
     * @var string
     * @access public
     */
    const NAMESPACE_SEPARATOR = '.';

    /**
     * This method is intend to act as constructor.
     *
     * @param array $options The options passed to this instance at runtime
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Cache_Service_Container Instance of this class
     * @access public
     */
    public function __construct(array $options = [])
    {
        // Configure
        $this
            ->options(
                $options,
                array_merge(
                    $this->allContainerAllowedOptions,
                    $this->thisContainerAllowedOptions
                )
            );
    }

    /**
     * This method is intend to write data to cache. It's just a facade to create() cause
     * update isn't fully implemented yet.
     *
     * @param string  $key       The dataset Id
     * @param string  $value     The data to write to cache
     * @param int     $lifetime  Date/Time on which the cache-entry expires
     * @param string  $namespace The dataset namespace
     * @param string  $userdata  The custom userdata to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success
     * @access public
     */
    public function update($key, $value, $namespace, $lifetime = null, $userdata = null)
    {
        $this->create($key, $value, $namespace, $lifetime, $userdata);
    }

    /**
     * This method returns the userdata from preloaded dataset.
     *
     * @param string $key        The dataset id
     * @param string $namespace The dataset namespace
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function getUserdata($key, $namespace)
    {
        $ret = $this->read($key, $namespace);

        list( , , $userdata) = $ret;
        return $userdata;
    }

    /**
     * This method is intend to check if a dataset is cached.
     *
     * @param string $key        The dataset Id
     * @param string $namespace The dataset namespace
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if cached, otherwise FALSE
     * @access public
     */
    public function cached($key, $namespace)
    {
        return $this->exists($key, $namespace);
    }

    /**
     * Setter for encoding.
     *
     * @param string $encoding The encoding to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * Setter for encoding.
     *
     * @param string $encoding The encoding to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function encoding($encoding)
    {
        $this->setEncoding($encoding);
        return $this;
    }

    /**
     * Getter for encoding.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|string Encoding if set, otherwise NULL
     * @access protected
     */
    protected function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Setter for highwaterMarker.
     *
     * @param int $highwaterMarker The highwaterMarker to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setHighwaterMarker($highwaterMarker)
    {
        $this->highwaterMarker = $highwaterMarker;
    }

    /**
     * Setter for highwaterMarker.
     *
     * @param int $highwaterMarker The highwaterMarker to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function highwaterMarker($highwaterMarker)
    {
        $this->setHighwaterMarker($highwaterMarker);
        return $this;
    }

    /**
     * Getter for highwaterMarker.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return int highwaterMarker
     * @access protected
     */
    protected function getHighwaterMarker()
    {
        return $this->highwaterMarker;
    }

    /**
     * Setter for lowwaterMarker.
     *
     * @param int $lowwaterMarker The lowwaterMarker to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setLowwaterMarker($lowwaterMarker)
    {
        $this->lowwaterMarker = $lowwaterMarker;
    }

    /**
     * Setter for lowwaterMarker.
     *
     * @param int $lowwaterMarker The lowwaterMarker to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function lowwaterMarker($lowwaterMarker)
    {
        $this->setLowwaterMarker($lowwaterMarker);
        return $this;
    }

    /**
     * Getter for lowwaterMarker.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|bool GarbageCollectorMaxLifetime if set, otherwise NULL
     * @access protected
     */
    protected function getLowwaterMarker()
    {
        return $this->lowwaterMarker;
    }

    /**
     * Setter for totalSize.
     *
     * @param int $totalSize The totalSize to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setTotalSize($totalSize)
    {
        $this->totalSize = $totalSize;
    }

    /**
     * Setter for totalSize.
     *
     * @param int $totalSize The totalSize to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function totalSize($totalSize)
    {
        $this->setTotalSize($totalSize);
        return $this;
    }

    /**
     * Getter for totalSize.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|bool GarbageCollectorMaxLifetime if set, otherwise NULL
     * @access protected
     */
    protected function getTotalSize()
    {
        return $this->totalSize;
    }

    /**
     * Setter for entries.
     *
     * @param array $entries The entries to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setEntries(array $entries)
    {
        $this->entries = $entries;
    }

    /**
     * Setter for entries.
     *
     * @param array $entries The entries to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function entries(array $entries)
    {
        $this->setEntries($entries);
        return $this;
    }

    /**
     * Getter for entries.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|array The entries if set, otherwise NULL
     * @access protected
     */
    protected function getEntries()
    {
        return $this->entries;
    }

    /**
     * Adds an element (entry) to list of entries.
     *
     * @param mixed  $key   The key to use as index
     * @param array  $value The entry to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success, otherwise false
     * @access protected
     */
    protected function addEntry($key, array $value)
    {
        $this->entries[$key] = $value;
    }

    /**
     * Setter for runtime cache element.
     *
     * @param string $key       The key to store value under
     * @param array  $dataset   The value to store
     * @param string $namespace The namespace to use
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function addToRuntimeCache($key, array $dataset, $namespace)
    {
        if (!isset($this->runtimeCache[$namespace])) {
            $this->runtimeCache[$namespace] = [];
        }

        $this->runtimeCache[$namespace][$key] = $dataset;
    }

    /**
     * Getter for runtime cache element.
     *
     * @param string $key The key to read from cache
     * @param string $namespace The namespace to use for zhat
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed|null The value for passed key if exist, otherwise NULL
     * @access protected
     * @throws Doozr_Cache_Service_Exception
     */
    protected function getFromRuntimeCache($key, $namespace)
    {
        $result = false;

        if (isset($this->runtimeCache[$namespace][$key]) === true) {
            $result = $this->runtimeCache[$namespace][$key];
        }

        return $result;
    }

    /**
     * Returns the whole runtime cache.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The runtime cache
     * @access protected
     */
    protected function getRuntimeCache()
    {
        return $this->runtimeCache;
    }

    /**
     * Purges the runtime cache.
     *
     * @param string|null $key       The key to purge from runtime cache
     * @param string|null $namespace The namespace to purge, or NULL to purge all namespaces
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if runtime cache was purged, otherwise FALSE
     * @access protected
     * @throws Doozr_Cache_Service_Exception
     */
    protected function purgeRuntimeCache($key = null, $namespace = null)
    {
        // Assume will fail
        $result = false;

        if ($namespace !== null && !isset($this->runtimeCache[$namespace])) {
            // Nothing to do is a success when purging ...
            $result = true;

        } else {
            // Hard reset when both null ...
            if ($key === null && $namespace === null) {
                $this->runtimeCache = [];

            } else {
                if ($key === null) {
                    $this->runtimeCache[$namespace] = [];
                } else {
                    unset($this->runtimeCache[$namespace][$key]);
                }

            }
        }

        return $result;
    }

    /**
     * This method is intend to translate human-readable/relative times into UNIX-time
     *
     * @param mixed $expires This can be in the following formats:
     *                       human readable          : yyyymmddhhmm[ss]] eg: 20010308095100
     *                       relative in seconds (1) : +xx              eg: +10
     *                       relative in seconds (2) : x <  946681200   eg: 10
     *                       absolute unixtime       : x < 2147483648   eg: 2147483648
     *                       see comments in code for details
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer UNIX-Timestamp
     * @access protected
     */
    protected function getExpiresAbsolute($expires)
    {
        if (!$expires) {
            $result= 0;

        } else {
            // For API-compatibility, one has not to provide a "+", if integer is < 946681200 (= Jan 01 2000 00:00:00)
            if ($expires[0] == '+' || $expires < 946681200) {
                $result = (time() + $expires);

            } elseif ($expires < 100000000000) {
                //if integer is < 100000000000 (= in 3140 years),
                // it must be an absolut unixtime (since the "human readable" definition asks for a higher number)
                $result = $expires;

            } else {
                // else it's "human readable";
                $year   = substr($expires, 0, 4);
                $month  = substr($expires, 4, 2);
                $day    = substr($expires, 6, 2);
                $hour   = substr($expires, 8, 2);
                $minute = substr($expires, 10, 2);
                $second = substr($expires, 12, 2);
                $result = mktime($hour, $minute, $second, $month, $day, $year);
            }
        }

        return $result;
    }

    /**
     * This method is intend to import the requested datafields as object variables if allowed.
     *
     * @param array $requested The values which should be imported as variable into class-namespace
     * @param array $allowed   The allowed keys (variable-names) - allowed to import
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function options(array $requested = [], array $allowed = [])
    {
        foreach ($allowed as $key => $value) {
            if (isset($requested[$value]) === true) {
                $this->{$value} = $requested[$value];
            }
        }

        return $this;
    }

    /**
     * This method is intend to encode the data for the storage container.
     *
     * @param string $data The dataset to encode
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Encoded data input
     * @access protected
     */
    protected function encode($data)
    {
        if ($this->getEncoding() === self::ENCODING_BASE64) {
            return base64_encode(serialize($data));
        } else {
            return serialize($data);
        }
    }

    /**
     * This method is intend to decode the data for the storage container.
     *
     * @param string $data The dataset to encode
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Encoded data input
     * @access protected
     */
    protected function decode($data)
    {
        if ($this->getEncoding() === self::ENCODING_BASE64) {
            return unserialize(base64_decode($data));

        } else {
            return unserialize($data);

        }
    }

    /**
     * Calculates a UUID for a passed string.
     *
     * @param string $input The input to calculate the UUID for.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The UUID
     * @access protected
     */
    protected function calculateUuid($input)
    {
        try {
            // Generate a version 5 (name-based and hashed with SHA1) UUID object
            $uuid5 = Uuid::uuid5(Uuid::NAMESPACE_DNS, $input);
            $uuid = $uuid5->toString();

        } catch (UnsatisfiedDependencyException $e) {
            $uuid = sha1($input);
        }

        return $uuid;
    }
}
