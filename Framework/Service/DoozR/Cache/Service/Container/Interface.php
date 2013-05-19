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

/**
 * DoozR Service Cache
 *
 * Interface for caching-container
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
interface DoozR_Cache_Service_Container_Interface
{
    // CRUD on cache-container

    /**
     * creates a cache-entry
     *
     * This method is intend to create a cache-entry.
     *
     * @param string  $id       The dataset Id
     * @param string  $data     The data to write to cache
     * @param integer $expires  Date/Time on which the cache-entry expires
     * @param string  $group    The dataset group
     *
     * @return  boolean TRUE if entry was created succesful, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function create($id, $data, $expires, $group);

    /**
     * reads a cache-entry
     *
     * This method is intend to read a cache-entry.
     *
     * @param string $id    The dataset Id
     * @param string $group The dataset group
     *
     * @return  mixed The data from cache if succesful, otherwise NULL
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function read($id, $group);

    /**
     * updates a cache-entry
     *
     * This method is intend to update a cache-entry.
     *
     * @param string  $id       The dataset Id
     * @param string  $data     The data to write to cache
     * @param integer $expires  Date/Time on which the cache-entry expires
     * @param string  $group    The dataset group
     * @param string  $userdata The custom userdata to add
     *
     * @return  boolean TRUE if entry was created succesful, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function update($id, $data, $expires, $group, $userdata);

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
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function delete($id, $group);

    /**
     * cleanup cache
     *
     * This method is intend to cleanup the cache-entries.
     *
     * @param integer $maxlifetime Maximum lifetime in seconds of an no longer used/touched entry
     *
     * @return  boolean TRUE if entry was deleted succesful, otherwise FALSE
     * @access  protected
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function garbageCollection($maxlifetime);
}

?>
