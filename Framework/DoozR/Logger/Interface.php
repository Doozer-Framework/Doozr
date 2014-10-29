<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Logger - Interface
 *
 * Interface.php - Logger-Interface for all Logger compliant to requirements of
 * DoozR
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
 * @package    DoozR_Logger
 * @subpackage DoozR_Logger_Interface
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

/**
 * DoozR - Logger - Interface
 *
 * Logger-Interface for all Logger compliant to requirements of
 * DoozR
 *
 * @category   DoozR
 * @package    DoozR_Logger
 * @subpackage DoozR_Logger_Interface
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
interface DoozR_Logger_Interface
{
    /**
     * Sets the name of the logger or an other identifier.
     *
     * @param string $name The name of the logger.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setName($name);

    /**
     * Returns the name of the logger or an other identifier.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The name of the logger.
     * @access public
     */
    public function getName();

    /**
     * Sets the level of the logger.
     *
     * @param int $level The log level.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setLevel($level);

    /**
     * Returns the log level of the logger.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return int The log level
     * @access public
     */
    public function getLevel();

    /**
     * Should return the Version of the Logger
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The version of the logger-class that use this interface
     * @access public
     */
    public function getVersion();

    /**
     * Should log to a channel of choice.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function log($level, $message, array $context = array());

    /**
     * Should return the current collection.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array|string The collection as string or as array if $asArray is set to true
     * @access public
     */
    public function getCollection($asArray = false);

    /**
     * Should return the current collection in its raw format.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|array The collection in its raw format
     * @access public
     */
    public function getCollectionRaw();

    /**
     * Should return the current content.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array|string The content as string or as array if $asArray is set to true
     * @access public
     */
    public function getContent($asArray = false);

    /**
     * Should return the current content in its raw format.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|string The content in its raw format
     * @access public
     */
    public function getContentRaw();

    /**
     * Should clear the collection.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function clearCollection();

    /**
     * Should clear the content.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function clearContent();

    /**
     * Should clear the collection AND the content.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function clear();
}
