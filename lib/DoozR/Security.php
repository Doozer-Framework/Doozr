<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Security
 *
 * Security.php - Access to private/public keys for security operations of
 * the DoozR Framework.
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
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Security
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Class/Singleton.php';

/**
 * DoozR Security
 *
 * Access to private/public keys for security operations of the DoozR Framework.
 *
 * @category   DoozR
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Security
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Security extends DoozR_Base_Class_Singleton
{
    /**
     * Instance of DoozR_Config
     *
     * @var DoozR_Config
     * @access protected
     */
    protected static $config;

    /**
     * Contains an instance of DoozR_Logger
     *
     * @var object
     * @access protected
     */
    protected static $logger;


    /**
     * Constructor.
     *
     * @param DoozR_Config $config Instance of DoozR_Config
     * @param DoozR_Logger $logger Instance of DoozR_Logger
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Security
     * @access protected
     */
    protected function __construct(DoozR_Config $config, DoozR_Logger $logger)
    {
        self::$config = $config;
        self::$logger = $logger;
    }

    /**
     * Returns private key of DoozR
     *
     * This method is intend to return the current private key.
     *
     * @param int $bit The Bit-Count for the private key (e.g. 256 / 512 / 1024)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The current private key
     * @access public
     * @throws DoozR_Exception
     */
    public static function getPrivateKey($bit = 256)
    {
        // max-len in bit = 1024
        if ($bit > 1024) {
            throw new DoozR_Exception('The largest size of a private-key is 1024! Please choose a lower bit count.');
        }

        // calculate bytes from bits
        $bytes = round($bit / 8);

        // get whole key
        $key = self::$config->crypt->keys->private;

        // return extracted key
        return substr($key, (strlen($key) - $bytes), $bytes);
    }
}