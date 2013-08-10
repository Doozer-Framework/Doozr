<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Logger for client based logging operations
 *
 * Client.php - This logger-implementation is intend to log to the client
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
 * @package    DoozR_Logger
 * @subpackage DoozR_Logger_Client
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Logger/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Logger/Interface.php';

/**
 * Logger for client based logging operations
 *
 * This logger-implementation is intend to log to the client
 *
 * @category   DoozR
 * @package    DoozR_Logger
 * @subpackage DoozR_Logger_Client
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
final class DoozR_Logger_Client extends DoozR_Logger_Abstract implements DoozR_Logger_Interface
{
    /**
     * Name of this logger
     *
     * @var string
     * @access private
     */
    protected $name = 'Client';

    /**
     * Version of this logger
     *
     * @var string
     * @access protected
     */
    protected $version = '$Rev$';


    /**
     * constructs the class
     *
     * This method is the constructor and responsible for building the instance.
     *
     * @param integer $level       The level to use for this logger
     * @param string  $fingerprint The fingerprint of the client
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function __construct($level, $fingerprint)
    {
        // call parents constructor
        parent::__construct($level, $fingerprint);

        // level
        $this->level = $level;

        // set line seperator
        $this->lineSeparator = '';

        // set line break
        $this->lineBreak = '<br />';
    }

    /**
     * Adds the defined line-separator to log-content
     *
     * This method is intend to add the defined line-separator to log-content.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function separate()
    {
        // do nothing to seperate in file logger
        return true;
    }

    /**
     * override for parent::output()
     *
     * we need to control what is send before the header is sent! if we have a critical output like "error"
     * or "warning" we send the content with no matter if headers was already send or not. simple content like
     * "log", "notice" and so on doesn't get delivered!
     *
     * @param string $color The color of the ouput as hexadecimal string reprensentation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The parent-call result
     * @access protected
     */
    protected function output($color = '#7CFC00')
    {
        $color = $this->typeToColor($this->contentType);
        return parent::output($color);
    }
}
