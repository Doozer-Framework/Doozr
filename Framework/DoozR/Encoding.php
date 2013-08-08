<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Encoding
 *
 * Encoding.php - Encoding bootstrap of the DoozR Framework
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
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Encoding
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Class/Singleton.php';

/**
 * DoozR - Encoding
 *
 * Encoding bootstrap of the DoozR Framework
 *
 * @category   DoozR
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Encoding
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Encoding extends DoozR_Base_Class_Singleton
{
    /**
     * holds instance of config
     *
     * @var object
     * @access private
     */
    private $_config;

    /**
     * holds instance of logger
     *
     * @var object
     * @access private
     */
    private $_logger;


    /**
     * This method is the constructor
     *
     * @param object $config The config instance
     * @param object $logger The logger instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function __construct($config, $logger)
    {
        $this->_config = $config;
        $this->_logger = $logger;

        // get encoding
        $encoding = $config->locale->encoding;
        $mimetype = $config->locale->mimetype;

        // begin configuration
        $this->_setInternalEncoding($encoding);
        $this->_setOutputEncoding($encoding);
        $this->_setLanguage($encoding);
        $this->_setRegexEncoding($encoding);
        $this->_setDefaultCharset($encoding);
        $this->_setDefaultMimeType($mimetype);
        //$this->_setOutputHandler();
    }

    /**
     * This method is intend to set the internal encoding
     *
     * @param string $encoding The encoding to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _setInternalEncoding($encoding = 'UTF-8')
    {
        // setup internal php
        mb_internal_encoding($encoding);
    }

    /**
     * This method is intend to set the output encoding
     *
     * @param string $encoding The encoding to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _setOutputEncoding($encoding = 'UTF-8')
    {
        // setup output
        mb_http_output($encoding);
    }

    /**
     * This method is intend to set the language
     *
     * @param string $encoding The encoding to set (is converted to language)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _setLanguage($encoding = 'UTF-8')
    {
        switch ($encoding) {
        case 'English':
        case 'en':
        case 'Japanese':
        case 'ja':
            mb_language($encoding);
            break;
        default:
            mb_language('uni');
            break;
        }
    }

    /**
     * This method is intend to set the regex-encoding
     *
     * @param string $encoding The encoding to set (is converted to language)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _setRegexEncoding($encoding = 'UTF-8')
    {
        // setup regex encoding
        mb_regex_encoding($encoding);
    }

    /**
     * This method is intend to set the default charset
     *
     * @param string $encoding The encoding to set (is converted to language)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _setDefaultCharset($encoding = 'UTF-8')
    {
        ini_set('default_charset', $encoding);
    }

    /**
     * This method is intend to set the default-mimetype (default_mimetype) for
     * PHP's output operations to the value configured
     *
     * @param string $mimetype The mimetype to define as PHP's default
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _setDefaultMimeType($mimetype = 'text/html')
    {
        ini_set('default_mimetype', $mimetype);
    }

    /**
     * This method is intend to set the output handler
     *
     * @param string $handler The handler to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _setOutputHandler($handler = 'mb_output_handler')
    {
        ob_start($handler);
    }
}
