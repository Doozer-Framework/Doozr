<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr Service Cache.
 *
 * Interface.php - Interface for caching-container
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

/**
 * Doozr Service Cache.
 *
 * Interface for caching-container
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
interface Doozr_Cache_Service_Container_Interface
{
    /**
     * Creates an entry.
     *
     * @param string $key      Dataset Id
     * @param string $value    Data to write to cache
     * @param int    $lifetime Timestamp on which the cache-entry expires (become stale)
     * @param string $scope    Scope of the entry
     * @param mixed  $userdata Additional userdata
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if entry was created successful, otherwise FALSE
     */
    public function create($key, $value, $lifetime, $scope, $userdata = null);

    /**
     * Reads an entry.
     *
     * @param string $key   Key to read
     * @param string $scope Scope to read from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed The data from cache if successful, otherwise NULL
     */
    public function read($key, $scope);

    /**
     * Updates an entry.
     *
     * @param string $key      Dataset Id
     * @param string $value    Data to write to cache
     * @param int    $lifetime Date/Time on which the cache-entry expires
     * @param string $scope    Dataset group
     * @param mixed  $userdata Additional userdata
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if entry was created successful, otherwise FALSE
     */
    public function update($key, $value, $scope, $lifetime, $userdata = null);

    /**
     * Deletes an entry.
     *
     * @param string $key   Dataset Id
     * @param string $scope Dataset group
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if entry was deleted successful, otherwise FALSE
     */
    public function delete($key, $scope);

    /**
     * Cleanup cache - only stale items!
     *
     * @param string $scope    Scope to look in
     * @param int    $lifetime Maximum age for an entry of the cache
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if entry was deleted successful, otherwise FALSE
     */
    public function garbageCollection($scope, $lifetime);

    /**
     * This method is intend to purge the cache. It removes all caches datasets from the cache.
     *
     * @param string $scope The dataset scope to purge
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed Number of removed datasets on success, otherwise FALSE
     */
    public function purge($scope);

    /**
     * Whether the cache entry exists or not.
     *
     * @param string $key   Key to check
     * @param string $scope Scope to look in
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if entry exists, otherwise FALSE
     */
    public function exists($key, $scope);

    /**
     * Whether the cache entry for key is expired.
     *
     * Throws Doozr_Cache_Service_Exception when checking a not existing
     * key or scope! Check via exists first!
     *
     * @see exists()
     *
     * @param string $key   Key to check
     * @param string $scope Scope to look in
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if entry expired, otherwise FALSE
     *
     * @throws Doozr_Cache_Service_Exception
     */
    public function expired($key, $scope);
}
