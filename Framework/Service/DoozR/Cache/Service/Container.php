<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Cache - Service - Container
 *
 * Container.php - Base class of all cache storage container.
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
 */

/**
 * DoozR - Cache - Service - Container
 *
 * Base class of all cache storage container.
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Cache
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @throws     Service_DoozR_Cache_Service_Exception
 * @service    Multiple
 */
abstract class DoozR_Cache_Service_Container
{
    /**
     * Flag indicating wheter to preload datasets or not.
     *
     * @var boolean
     * @access private
     */
    private $_preload = true;

    /**
     * Cache group of a preloaded dataset
     *
     * @var string
     * @access private
     */
    private $_group = '';

    /**
     * Expiration timestamp of a preloaded dataset.
     * 0 means never, endless
     *
     * @var integer
     * @access private
     */
    private $_expires = 0;

    /**
     * Value of a preloaded dataset.
     *
     * @var string
     * @access private
     */
    private $_data = '';

    /**
     * userdata field for preloaded datasets
     *
     * @var string
     * @access private
     */
    private $_userdata = '';

    /**
     * Flag indicating that the dataset requested for preloading is unknown.
     *
     * @var boolean
     * @access private
     */
    private $_unknown = true;

    /**
     * Encoding mode for cache data: base64 or addslashes() (slash).
     * base64 or slash
     *
     * @var string
     * @access protected
     */
    protected $encodingMode = 'base64';

    /**
     * Highwater mark - maximum space required by all cache entries.
     *
     * Whenever the garbage collection runs it checks the amount of space
     * required by all cache entries. If it's more than n (highwater) bytes
     * the garbage collection deletes as many entries as necessary to reach the
     * lowwater mark.
     *
     * @var integer
     * @see lowwater
     * @access protected
     */
    protected $highwater = 2048000;

    /**
     * Lowwater mark
     *
     * @var integer
     * @see highwater
     * @access protected
     */
    protected $lowwater = 1536000;

    /**
     * ID of a preloaded dataset
     *
     * @var string
     * @access private
     */
    private $_id = '';

    /**
     * Options that can be set in every derived class using it's constructor.
     *
     * @var array
     * @access protected
     */
    protected $allContainerAllowedOptions = array(
        'encodingMode',
        'highwater',
        'lowwater',
        'unix'
    );


    /**
     * This method is intend to act as constructor.
     *
     * @param array $options The options passed to this instance at runtime
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object Instance of this class
     * @access public
     */
    public function __construct(array $options = array())
    {
        // configure
        $this->setOptions(
            $options,
            array_merge(
                $this->allContainerAllowedOptions,
                $this->thisContainerAllowedOptions
            )
        );
    }

    /**
     * This method is intend to load a dataset from cache.
     *
     * @param string $id    The dataset Id
     * @param string $group The dataset group
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The dataset value, NULL on failure
     * @access public
     */
    public function read($id, $group)
    {
        // preloading activated?
        if ($this->_preload) {
            // do a checked preload
            $this->_checkedPreload($id, $group);
            return $this->_data;
        }

        $result = $this->read($id, $group);

        list( , $data, ) = $result;

        return $data;
    }

    /**
     * This method is intend to write data to cache. It's just a facade to create() cause
     * update isn't fully implemented yet.
     *
     * @param string  $id       The dataset Id
     * @param string  $data     The data to write to cache
     * @param integer $expires  Date/Time on which the cache-entry expires
     * @param string  $group    The dataset group
     * @param string  $userdata The custom userdata to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success
     * @access public
     * @throws DoozR_Cache_Service_Exception
     */
    public function update($id, $data, $expires, $group, $userdata = null)
    {
        $this->create($id, $data, $expires, $group, $userdata);
    }

    /**
     * This method returns the userdata from preloaded dataset.
     *
     * @param string $id    The dataset id
     * @param string $group The dataset group
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function getUserdata($id, $group)
    {
        // preloading activated?
        if ($this->_preload) {
            // do a checked preload
            $this->_checkedPreload($id, $group);

            return $this->_userdata;
        }

        $ret = $this->read($id, $group);

        list( , , $userdata) = $ret;
        return $userdata;
    }

    /**
     * This method sets the id.
     *
     * @param string $id The id to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * This method returns the id.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The current id
     * @access public
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * This method is intend to check if a dataset is cached.
     *
     * @param string $id    The dataset Id
     * @param string $group The dataset group
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if cached, otherwise FALSE
     * @access public
     */
    public function isCached($id, $group)
    {
        // is preloading activated?
        if ($this->_preload) {
            // do a checked preload
            $this->_checkedPreload($id, $group);

            return !($this->_unknown);
        }

        return $this->idExists($id, $group);
    }

    /**
     * This method is intend to check if a dataset is expired.
     *
     * @param string  $id     The dataset Id
     * @param string  $group  The dataset group
     * @param integer $maxAge Maximum age timestamp
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The dataset value, NULL on failure
     * @access public
     */
    public function isExpired($id, $group, $maxAge)
    {
        // is preloading enabled?
        if ($this->_preload) {
            // do a checked preload
            $this->_checkedPreload($id, $group);

            if ($this->_unknown) {
                return false;
            }
        } else {
            // check if at all it is cached
            if (!$this->isCached($id, $group)) {
                return false;
            }
            // I'm lazy...
            $ret = $this->read($id, $group);

            list($this->_expires, , ) = $ret;
        }

        // endless
        if (0 == $this->_expires) {
            return false;
        }

        $expired  = ($this->_expires <= time()) || ($maxAge && ($this->_expires <= $maxAge));

        // you feel fine, Ulf?
        if ($expired) {
            // call remove in container
            $this->delete($id, $group);
            $this->flushPreload();
        }

        return $expired;
    }

    /**
     * This method is intend to preload a dataset.
     *
     * @param string $id    The dataset Id
     * @param string $group The dataset group
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success
     * @access public
     */
    private function _preload($id, $group)
    {
        // whatever happens, remember the preloaded ID
        $this->_id = $id;
        $this->_group = $group;

        // try to read result by id-group
        $result = $this->read($id, $group);

        //list($this->_expires, $this->_data, $this->_userdata) = $result;

        if ($this->_expires === null) {
            // Uuups, unknown ID
            $this->flushPreload();
            return false;
        }

        //
        $this->_unknown = false;


        return true;
    }

    /**
     * This method is intend to do a checked preload. This means that this
     * method first checks if the current request was already loaded before.
     *
     * @param string $id    The dataset id
     * @param string $group The dataset group
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    private function _checkedPreload($id, $group)
    {
        // if the active set id or group is different from last preloading
        if ($this->_id != $id || $this->_group != $group) {
            $this->_preload($id, $group);
        }
    }

    /**
     * This method is intend to import the requested datafields as object variables if allowed.
     *
     * @param array $requested The values which should be imported as variable into class-namespace
     * @param array $allowed   The allowed keys (variable-names) - allowed to import
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setOptions(array $requested = array(), array $allowed = array())
    {
        foreach ($allowed as $key => $value) {
            if (isset($requested[$value])) {
                $this->{$value} = $requested[$value];
            }
        }
    }

    /**
     * This method is intend to flush the internal preload buffer.
     * create(), delete() and flush() must call this method to preevent differences between the preloaded values and
     * the real cache contents.
     *
     * @param string $id    The dataset ID, if left out the preloaded values will be flushed. If given the preloaded
     *                      values will only be flushed if they are equal to the given id and group
     * @param string $group The dataset group
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function flushPreload($id = '', $group = 'Default')
    {
        if (!$id || ($this->_id == $id && $this->_group == $group)) {
            // clear the internal preload values
            $this->_id       = '';
            $this->_group    = '';
            $this->_data     = '';
            $this->_userdata = '';
            $this->_expires  = -1;
            $this->_unknown  = true;
        }
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
        if ($this->encodingMode == 'base64') {
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
        pre($data);

        if ($this->encodingMode == 'base64') {
            return unserialize(base64_decode($data));
        } else {
            return unserialize($data);
        }
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
            return 0;
        }

        // for api-compatibility, one has not to provide a "+", if integer is < 946681200 (= Jan 01 2000 00:00:00)
        if ($expires[0] == '+' || $expires < 946681200) {
            return(time() + $expires);
        } elseif ($expires < 100000000000) {
            //if integer is < 100000000000 (= in 3140 years),
            // it must be an absolut unixtime
            // (since the "human readable" definition asks for a higher number)
            return $expires;
        } else {
            // else it's "human readable";
            $year = substr($expires, 0, 4);
            $month = substr($expires, 4, 2);
            $day = substr($expires, 6, 2);
            $hour = substr($expires, 8, 2);
            $minute = substr($expires, 10, 2);
            $second = substr($expires, 12, 2);
            return mktime($hour, $minute, $second, $month, $day, $year);
        }
    }

    /**
     * This method is intend to start the garbageCollection in child container(s). Please override this
     * method in your container and call parent::garbageCollection($maxlifetime) or $this->flushPreload() on
     * every call first.
     *
     * @param integer $maxlifetime Maximum lifetime in seconds of an no longer used/touched entry
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function garbageCollection($maxlifetime)
    {
        // flush the internal preload buffer on every call
        $this->flushPreload();
    }

    /**
     * This method is intend to check if a dataset exists.
     *
     * @param string $id    The id of the dataset
     * @param string $group The group of the dataset
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if Id exist, otherwise FALSE
     * @access protected
     * @abstract
     */
    abstract protected function idExists($id, $group);

    /**
     * This method is intend to remove an dataset finally from container.
     *
     * @param string $id    The id of the dataset
     * @param string $group The group of the dataset
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access protected
     * @abstract
     */
    //abstract protected function delete($id, $group);

    /**
     * This method is intend to flush the cache. It removes all caches datasets from the cache.
     *
     * @param string $group The dataset group to flush
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Number of removed datasets on success, otherwise FALSE
     * @access public
     * @abstract
     */
    abstract public function flush($group);
}
