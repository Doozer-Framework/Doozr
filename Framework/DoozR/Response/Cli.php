<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Response - Cli
 *
 * Cli.php - Response Cli - Response-Handler to pass responses to CLI
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
 * @package    DoozR_Response
 * @subpackage DoozR_Response_Cli
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Response.php';

/**
 * DoozR - Response - Cli
 *
 * Response Cli - Response-Handler to pass responses to CLI
 *
 * @category   DoozR
 * @package    DoozR_Response
 * @subpackage DoozR_Response_Cli
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Response_Cli extends DoozR_Base_Response
{
    /**
     * type of this response
     *
     * holds the type of this response
     *
     * @var string
     * @access private
     */
    const TYPE = 'cli';


    /**
     * Constructor
     *
     * @param DoozR_Config $config The config of DoozR
     * @param DoozR_Logger $logger The logger instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Response_Cli
     * @access public
     */
    public function __construct(DoozR_Config $config, DoozR_Logger $logger)
    {
        // map type
        self::$type = self::TYPE;

        // call parents constructor
        parent::__construct($config, $logger);
    }

    /**
     * close a connection correctly
     *
     * This method is intend to correctly close an open connection to client.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function closeConnection()
    {
        exit;
    }

    /**
     * returns a valid + supported encoding/charset
     *
     * This method is intend to return a valid + supported encoding/charset
     *
     * @param string $encoding The encoding to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string A valid encoding to use for content-delivery
     * @access private
     */
    private function _getCharset($encoding = null)
    {
        // check encoding
        if (!$encoding) {
            // get it from config
            $encoding = DoozR_Core::config()->get('ENCODING.CHARSET');
        } else {
            // to upper for switch
            $encoding = strtoupper($encoding);

            // check if compatible
            switch ($encoding) {
            case 'UTF-8':
                $encoding = 'UTF-8';
                break;
            case 'ISO-8859-1':
                $encoding = 'ISO-8859-1';
                break;
            default:
                $encoding = 'UTF-8';
                break;
            }
        }

        // return the correct encoding/charset
        return strtoupper($encoding);
    }

    /**
     * returns a valid encoded content for delivery
     *
     * This method is intend to return a valid encoded content for delivery
     *
     * @param mixed  $data    The data to encode to valid + supported target charset
     * @param string $charset The charset to use for encoding (switch)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Valid + supported encoded content
     * @access protected
     */
    protected function fixEncoding($data = null, $charset = 'UTF-8')
    {
        // get module encoding
        /*
        $encoding = DoozR_Core::module('encoding');

        // check for given target charset and convert
        switch ($charset) {
        case 'UTF-8':
            return $encoding->encodeUtf8($data);
            break;
        case 'ISO-8859-1':
            return $encoding->encodeIso88591($data);
            break;
        }
        */

        return $data;
    }
}
