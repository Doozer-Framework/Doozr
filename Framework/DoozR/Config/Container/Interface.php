<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Interface-Config-Manager
 *
 * Interface.php - Interface-Config-Manager of the DoozR Framework
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
 * @package    DoozR_Config
 * @subpackage DoozR_Config_Interface
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

/**
 * DoozR Interface-Config-Manager
 *
 * Interface-Config-Manager of the DoozR Framework
 *
 * @category   DoozR
 * @package    DoozR_Config
 * @subpackage DoozR_Config_Interface
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
interface DoozR_Config_Container_Interface
{
    /**
     * Creates a configuration node.
     *
     * @param string $node The node to create
     * @param string $data The data to write to config
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if entry was created successful, otherwise FALSE
     * @access public
     */
    public function create($node, $data);

    /**
     * Reads and return a configuration node.
     *
     * @param mixed $node The node to read/parse
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The data from cache if successful, otherwise NULL
     * @access public
     */
    public function read($node);

    /**
     * Updates a configuration node.
     *
     * @param string $node  The configuration node
     * @param string $value The data to write
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if entry was created successful, otherwise FALSE
     * @access public
     */
    public function update($node, $value);

    /**
     * Deletes a node.
     *
     * @param string $node The node to delete
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if entry was deleted successful, otherwise FALSE
     * @access public
     */
    public function delete($node);
}
