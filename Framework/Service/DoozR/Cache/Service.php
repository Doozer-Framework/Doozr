<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Cache - Service
 *
 * Cache.php - Caching Service for caching operations with support for
 * different container like "Filesystem", "Memcache" ...
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

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Service/Multiple.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Psr/Cache/Interface.php';
require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Cache/Service/Exception.php';

/**
 * DoozR - Cache - Service
 *
 * Caching Service for caching operations with support for
 * different container like "Filesystem", "Memcache" ...
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
 * @throws     DoozR_Cache_Service_Exception
 * @DoozRType  Multiple
 */
class DoozR_Cache_Service extends DoozR_Base_Service_Multiple implements DoozR_Psr_Cache_Interface
{
    /**
     * Contains the current working status
     *
     * @var boolean
     * @access private
     */
    private $_enabled = true;

    /**
     * contains the currently active id
     *
     * @var string
     * @access private
     */
    private $_id;

    /**
     * contains the currently active group
     *
     * @var string
     * @access private
     */
    private $_group;

    /**
     * contains the current container
     *
     * @var object
     * @access private
     */
    private $_container;

    /**
     * contains the current container options
     *
     * @var array
     * @access private
     */
    private $_containerOptions = array();

    /**
     * is os unix
     *
     * @var boolean
     * @access private
     */
    private $_isUnix;

    /**
     * contains the content from a cache recording
     *
     * @var string
     * @access private
     */
    private $_content;

    /**
    * Garbage collection: Delete all entries not used for n seconds.
    * Default is one day, 60 * 60 * 24 = 86400 seconds.
    *
    * @var  integer
    * @access private
    */
    private $_gcMaxlifetime = 86400;

    /**
     * Garbage collection: probability in percent
     * 0 => never
     *
     * @var integer
     * @access private
     * @see $_gcTime, $_gcMaxlifetime
     */
    private $_gcProbability = 1;

    /**
    * Garbage collection: probability in seconds
    *
    * If set to a value above 0 a garbage collection will
    * flush all cache entries older than the specified number
    * of seconds.
    *
    * @see $_gcProbability, $_gcMaxlifetime
    * @var integer
    * @access public
    */
    private $_gcTime = 1;

    /**
     * contains the time of the last run
     *
     * @var integer
     * @access private
     * @static
     */
    private static $_gcLastRun = 0;


    /**
     * constructor
     *
     * This method is intend to act as constructor.
     *
     * @param boolean $isUnix           TRUE if current OS is unix-type, FALSE if not
     * @param string  $container        The container to use for caching
     * @param array   $containerOptions The configuration/options for the container instance
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function __tearup($isUnix = true, $container = null, array $containerOptions = array())
    {
        // store
        $this->_isUnix = $isUnix;

        // container-type given -> then init
        if ($container) {
            // get container
            $this->setContainer($container, $containerOptions);
        }
    }

    /**
     * Sets the options for cache container
     *
     * This method is intend to set the options for cache container.
     *
     * @param array $containerOptions The options to set
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function setContainerOptions(array $containerOptions = array())
    {
        $this->_containerOptions = $containerOptions;
    }

    /**
     * Returns the options for cache container
     *
     * This method is intend to return the options for cache container.
     *
     * @return  array The container options
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getContainerOptions()
    {
        return $this->_containerOptions;
    }

    /**
     * Checks existence of a container and returns result
     *
     * This method is intend to check existence of a container and returns result.
     *
     * @param string $container The container name to check
     *
     * @return  boolean TRUE if $container exists, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function containerExists($container)
    {
        // correct format filename
        $container = ucfirst(strtolower($container));

        return file_exists(
            $this->getPath().'Service'.DIRECTORY_SEPARATOR.'Container'.DIRECTORY_SEPARATOR.$container.'.php'
        );
    }

    /**
     * Sets the container used for CRUD cache objects
     *
     * This method is intend to set the container used for CRUD cache objects.
     *
     * @param string $container        The container to set
     * @param array  $containerOptions The options to use for container
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function setContainer($container, array $containerOptions = array())
    {
        // check if container-type exists
        if (!$this->containerExists($container)) {
            throw new DoozR_Cache_Service_Exception(
                'Error! Container: "'.$container.'" does not exist! Please choose an existing container.'
            );
        }

        // if options empty
        if (!count($containerOptions)) {
            $containerOptions = $this->getContainerOptions();
        }

        // inject the UNIX boolean into container arguments and store options
        $containerOptions = array_merge(
            $containerOptions,
            array('unix' => $this->_isUnix)
        );

        // get container with new options
        $this->_container = $this->_containerFactory($container, $containerOptions);
    }

    /**
     * cleanup
     *
     * This method is intend to cleanup on class destruct.
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function __teardown()
    {
        $this->_garbageCollection();
    }

    /**
     * stores the given data in the cache
     *
     * This method is intend to store data to cache.
     *
     * @param mixed   $data    The data to store
     * @param string  $id      The dataset Id
     * @param integer $expires The time to expire
     * @param string  $group   The dataset group
     *
     * @return  boolean TRUE if dataset could be written, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @throws  DoozR_Cache_Service_Exception
     */
    public function create($data, $id = null, $expires = null, $group = 'Default')
    {
        if (!$this->_enabled) {
            throw new DoozR_Cache_Service_Exception(
                'Error while trying to create content in cache! Please activate cache first.'
            );
        }

        // retrieve correct id
        $id = $this->_getId($id);

        // retrieve expiration date
        $expires = (!$expires) ? $this->_gcMaxlifetime : $expires;

        // try to create entry
        if (!$this->_createExt($id, $data, $expires, $group)) {
            // failed
            throw new DoozR_Cache_Service_Exception(
                'Error while trying to create content in cache!'
            );
        }

        // success
        return true;
    }

    /**
     * returns the requested dataset it if exists and is not expired
     *
     * This method is intend to return the requested dataset it if exists and is not expired.
     *
     * @param string $id    The dataset Id
     * @param string $group The dataset group
     *
     * @return  mixed Data from cache, NULL on failure
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @throws  DoozR_Cache_Service_Exception
     */
    public function read($id = null, $group = 'Default')
    {
        if (!$this->_enabled) {
            throw new DoozR_Cache_Service_Exception(
                'Error while trying to read() from cache! Please active cache first.'
            );
        }

        // retrieve correct id
        $id = $this->_getId($id);

        // check if content exists
        if ($this->isCached($id, $group) && !$this->isExpired($id, $group)) {
            // then return the content
            return $this->_container->read($id, $group);
        } else {
            // if not exist throw exception
            throw new DoozR_Cache_Service_Exception(
                'Requested dataset with Id: "'.$id.'" in group: "'.$group.'" could not be found in cache!'
            );
        }
    }

    /**
     * updates a dataset
     *
     * This method is intend to update a dataset.
     *
     * @param mixed   $data    The data to store
     * @param string  $id      The dataset Id
     * @param integer $expires The time to expire
     * @param string  $group   The dataset group
     *
     * @return  boolean TRUE if dataset could be written, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @throws  DoozR_Cache_Service_Exception
     */
    public function update($data, $id, $expires = 0, $group = 'Default')
    {
        if (!$this->_enabled) {
            throw new DoozR_Cache_Service_Exception(
                'Error while trying to update() cache! Please active cache first.'
            );
        }

        return $this->create($data, $id, $expires, $group);
    }

    /**
     * deletes a dataset from cache
     *
     * This method is intend to delete an entry from cache.
     *
     * @param string $id    The dataset Id
     * @param string $group The dataset group
     *
     * @return  boolean TRUE if entry was deleted succesful, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function delete($id, $group = 'Default')
    {
        if (!$this->_enabled) {
            throw new DoozR_Cache_Service_Exception(
                'Error while trying to delete content from cache! Please active cache first.'
            );
        }

        //return $this->create('', $id, 0.0001, $group);
        return $this->_container->delete($id, $group);
    }

    /**
     * Checks if a dataset is cached
     *
     * This method is intend to check if data was cached before.
     * Note: this does not say that the cached data is not expired!
     *
     * @param string $id    The dataset-Id
     * @param string $group The cache group
     *
     * @return  boolean TRUE if dataset is cached, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @throws  DoozR_Cache_Service_Exception
     */
    public function isCached($id = null, $group = 'Default')
    {
        if (!$this->_enabled) {
            throw new DoozR_Cache_Service_Exception(
                'Error while trying to check with isCached()! Please activate cache first.'
            );
        }

        if (!$id) {
            $id = $this->_id;
        }

        // retrieve correct id
        $id = $this->_getId($id);

        $result = $this->_container->isCached($id, $group);

        return $result;
    }

    /**
     * Checks if an cached object exists and return result
     *
     * This method is intend to check if an cached object exists and return result.
     *
     * @param string $id    The id of the object to check
     * @param string $group The group of the object to check
     *
     * @return  boolean TRUE if exists, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function exists($id = null, $group = 'Default')
    {
        return $this->isCached($id, $group);
    }

    /**
     * Checks if a dataset is expired
     *
     * This method is intend to check if a given dataset(-Id) is already expired.
     *
     * @param string $id     The dataset-Id
     * @param string $group  The cache group
     * @param intege $maxAge The maximum age for the cached data in seconds - 0 for endless
     *                       If the cached data is older but the given lifetime it will be removed from the cache.
     *                       You don't have to provide this argument if you call isExpired(). Every dataset knows
     *                       it's expire date and will be removed automatically. Use this only if you know what
     *                       you're doing...
     *
     * @return  boolean TRUE if dataset is expired, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @throws  DoozR_Cache_Service_Exception
     */
    public function isExpired($id, $group = 'Default', $maxAge = 0)
    {
        if (!$this->_enabled) {
            throw new DoozR_Cache_Service_Exception(
                'Error while trying to check with isExpired()! Please active cache first.'
            );
        }

        return $this->_container->isExpired($id, $group, $maxAge);
    }

    /**
     * setter for group
     *
     * used to generate and set the group
     *
     * @param string $group The dataset group to use
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function setGroup($group)
    {
        // store locally
        $this->_group = $group;
    }

    /**
     * getter for group
     *
     * returns the group of the current dataset
     *
     * @return  string The group
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getGroup()
    {
        return $this->_group;
    }

    /**
     * setter for Id
     *
     * used to generate and set the (unique-)ID for the current cache operation
     *
     * @param string $id A predefined ID to generate a unique ID for
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function setId($id)
    {
        // store locally
        $this->_id = $id;

        // and in container
        $this->_container->setId($id);
    }

    /**
     * getter for Id
     *
     * returns the (unique-)ID _id of the current cache operation
     *
     * @return  string The (unique-)ID _id of the current cache operation
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * getter for status
     *
     * returns the current status of the cache-module (enabled = TRUE|FALSE)
     *
     * @return  boolean TRUE if enabled, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getStatus()
    {
        return $this->_enabled;
    }

    /**
     * setter for lifetime
     *
     * This method sets the given Lifetime.
     *
     * @param integer $lifetime The Lifetime to set
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function setLifetime($lifetime)
    {
        $this->_gcMaxlifetime = $lifetime;
    }

    /**
     * getter for lifetime
     *
     * This method is intend to return the current active lifetime.
     *
     * @return  integer The lifetime
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getLifetime()
    {
        return $this->_gcMaxlifetime;
    }

    /**
     * generates a unique-Id for the given value
     *
     * This is a quick but dirty hack to get a "unique" ID for a any kind of variable.
     * ID clashes might occur from time to time although they are extreme unlikely!
     *
     * @param mixed   $value     Value to generate Id for
     * @param boolean $setActive TRUE to store Id directly, otherwise FALSE
     *
     * @return  string An unique-Id
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function generateId($value = false, $setActive = true)
    {
        // generate value if not given - serialize it if given
        $value = (!$value) ? microtime() : serialize($value);

        // get hash
        $id = md5($value);

        if ($setActive) {
            $this->setId($id);
        }

        return $id;
    }

    /**
     * enables cache
     *
     * This method is intend to enable the cache
     *
     * @return  boolean TRUE if cache was enabled, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @throws  DoozR_Cache_Service_Exception
     */
    public function enable()
    {
        $this->_enabled = $this->_reset();

        if (!$this->_enabled) {
            throw new DoozR_Cache_Service_Exception(
                'Failed to enable cache!'
            );
        }

        return $this->_enabled;
    }

    /**
     * disables cache
     *
     * This method is intend to disable the cache
     *
     * @return  boolean TRUE if cache was disabled, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @throws  DoozR_Cache_Service_Exception
     */
    public function disable()
    {
        $this->_enabled = $this->_reset(false);

        if ($this->_enabled) {
            throw new DoozR_Cache_Service_Exception(
                'Failed to disable cache!'
            );
        }

        return !$this->_enabled;
    }

    /**
     * switches the status - from enabled to disabled and vice versa
     *
     * This method is intend to switch the status - from enabled to disabled and vice versa.
     *
     * @return  boolean TRUE if cache was disabled, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @throws  DoozR_Cache_Service_Exception
     */
    public function switchStatus()
    {
        if ($this->_enabled) {
            return $this->disable();
        } else {
            return $this->enable();
        }
    }

    /**
     * records content for cache
     *
     * This method is intend to record content from PHP's output buffer for
     * cache.
     *
     * @param string $id    The id to use for this dataset when it is stored
     * @param string $group The dataset group
     *
     * @return  boolean TRUE on success, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @throws  DoozR_Cache_Service_Exception
     */
    public function recordStart($id = false, $group = 'Default')
    {
        // create id if not given
        $this->_id    = ($id) ? $id : $this->generateId();
        $this->_group = $group;

        // start buffering
        if (!ob_start()) {
            throw new DoozR_Cache_Service_Exception(
                'Cache-recording failed. ob_start() returned false.'
            );
        }

        return true;
    }

    /**
     * records content for cache
     *
     * This method is intend to record content from PHP's output buffer for
     * cache.
     *
     * @return  boolean TRUE on success, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @throws  DoozR_Cache_Service_Exception
     */
    public function recordStop()
    {
        // get content from buffer
        $this->_content = ob_get_contents();

        // stop buffering
        ob_end_clean();

        // store the data into the cache with max lifetime
        if ($this->create($this->_content, $this->_id, $this->_gcMaxlifetime, $this->_group)) {
            return $this->_id;
        }

        // failed
        return false;
    }

    /**
     * removes all group datasets from cache
     *
     * This method is intend to remove all group datasets from cache.
     *
     * @param string $group The group of the cache item
     *
     * @return  integer Number of removed entries
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function flush($group = 'Default')
    {
        return $this->_container->flush($group);
    }

    /**
     * calls the garbage-collector of the cache-container
     *
     * This method is intend to call the garbage-collector of the cache-container.
     *
     * @param boolean $force TRUE to force a garbage collection run, otherwise FALSE (default)
     *
     * @return  void
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    private function _garbageCollection($force = false)
    {
        // no exception here cause -> we just save the time for gc if disabled!
        if (!$this->_enabled) {
            return;
        }

        // time and probability based
        if (($force)
            || (self::$_gcLastRun && self::$_gcLastRun < (time() + $this->_gcTime))
            || (rand(1, 100) < $this->_gcProbability)
        ) {
            $this->_container->garbageCollection($this->_gcMaxlifetime);
            self::$_gcLastRun = time();
        }
    }

    /**
     * resets the cache-module
     *
     * This method is intend to reset this module.
     *
     * @param boolean $return TRUE if this method should return TRUE on success,
     *                        otherwise FALSE to return FALSE value
     *
     * @return  boolean TRUE|FALSE on success
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    private function _reset($return = true)
    {
        $this->_id      = null;
        $this->_group   = null;
        $this->_content = null;

        return $return;
    }

    /**
     * factory for container
     *
     * This method is intend to act as factory for container.
     *
     * @param string $container        The container to create
     * @param array  $containerOptions The configuration/options for the container
     *
     * @return  object Instance of the container
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    private function _containerFactory($container, array $containerOptions = array())
    {
        $container = ucfirst(strtolower($container));
        $class     = __CLASS__.'_Container_'.$container;
        $file      = $this->registry->path->get('module').str_replace('_', DIRECTORY_SEPARATOR, $class).'.php';

        // check if file exists
        if (!file_exists($file)) {
            throw new DoozR_Cache_Service_Exception(
                'Container-File: '.$file.' does not exist!'
            );
        }

        include_once $file;
        return new $class($containerOptions);
    }

    /**
     * returns the active id
     *
     * This method is intend to return the $id used for input or the internal
     * stored if given id is null.
     *
     * @param string $id The id to use for check
     *
     * @return  string The id
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @throws  DoozR_Cache_Service_Exception
     */
    private function _getId($id)
    {
        // if not a special id given - use current active
        $id = (!$id) ? $this->_id : $id;

        // id no valid id given
        if (!$id) {
            throw new DoozR_Cache_Service_Exception(
                'Error! Invalid Id: "'.$id.'" for operation.'
            );
        }

        // return the correct id
        return $id;
    }

    /**
     * stores a dataset with additional userdefined data
     *
     * This method is intend to store a dataset with additional userdefined data.
     *
     * @param string  $id       The dataset Id
     * @param mixed   $data     The data to cache
     * @param integer $expires  The time to expire
     * @param string  $group    The dataset group
     * @param string  $userdata The userdata to add
     *
     * @return  boolean TRUE if dataset could be written, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    private function _createExt($id, $data, $expires = null, $group = 'Default', $userdata = '')
    {
        try {
            $this->_id = $this->_container->create($id, $data, $expires, $group, $userdata);

        } catch (Exception $e) {
            throw new DoozR_Cache_Service_Exception('Error creating dataset!');
        }

        return ($this->_id !== false) ? true : false;
    }
}

?>
