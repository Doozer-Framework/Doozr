<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Service Cache
 *
 * Interface.php - Interface for caching-container
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2014, Benjamin Carl - All rights reserved.
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
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

/**
 * DoozR Service Cache
 *
 * Interface for caching-container
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Cache
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
interface DoozR_Cache_Service_Container_Interface
{
    /**
     * Creates an entry.
     *
     * @param string $key       The dataset Id
     * @param string $value     The data to write to cache
     * @param int    $lifetime  Timestamp on which the cache-entry expires (become stale)
     * @param string $namespace The namespace of the entry
     * @param mixed  $userdata  The additional userdata
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if entry was created successful, otherwise FALSE
     * @access public
     */
    public function create($key, $value, $lifetime, $namespace, $userdata = null);

    /**
     * Reads an entry.
     *
     * @param string $key       The key to read
     * @param string $namespace The namespace to read from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The data from cache if successful, otherwise NULL
     * @access public
     */
    public function read($key, $namespace);

    /**
     * Updates an entry.
     *
     * @param string $key       The dataset Id
     * @param string $value     The data to write to cache
     * @param int    $lifetime  Date/Time on which the cache-entry expires
     * @param string $namespace The dataset group
     * @param mixed  $userdata  The additional userdata
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if entry was created successful, otherwise FALSE
     * @access public
     */
    public function update($key, $value, $namespace, $lifetime, $userdata = null);

    /**
     * Deletes an entry.
     *
     * @param string $key       The dataset Id
     * @param string $namespace The dataset group
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if entry was deleted successful, otherwise FALSE
     * @access public
     */
    public function delete($key, $namespace);

    /**
     * Cleanup cache - only stale items!
     *
     * @param string $namespace The namespace to look in
     * @param int    $lifetime  The maximum age for an entry of the cache
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if entry was deleted successful, otherwise FALSE
     * @access protected
     */
    public function garbageCollection($namespace, $lifetime);

    /**
     * This method is intend to purge the cache. It removes all caches datasets from the cache.
     *
     * @param string $namespace The dataset namespace to purge
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Number of removed datasets on success, otherwise FALSE
     * @access public
     */
    public function purge($namespace);

    /**
     * Whether the cache entry exists or not.
     *
     * @param string $key       The key to check
     * @param string $namespace The namespace to look in
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if entry exists, otherwise FALSE
     * @access public
     */
    public function exists($key, $namespace);

    /**
     * Whether the cache entry for key is expired.
     *
     * Throws DoozR_Cache_Service_Exception when checking a not existing
     * key or namespace! Check via exists first!
     * @see exists()
     *
     * @param string $key       The key to check
     * @param string $namespace The namespace to look in
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if entry expired, otherwise FALSE
     * @access public
     * @throws DoozR_Cache_Service_Exception
     */
    public function expired($key, $namespace);
}
