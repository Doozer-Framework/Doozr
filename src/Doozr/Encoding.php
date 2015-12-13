<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Encoding
 *
 * Encoding.php - Encoding bootstrap of the Doozr Framework
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
 * @package    Doozr_Kernel
 * @subpackage Doozr_Kernel_Encoding
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Class/Singleton.php';

// Use
use Psr\Log\LoggerInterface;

/**
 * Doozr - Encoding
 *
 * Encoding bootstrap of the Doozr Framework
 *
 * @category   Doozr
 * @package    Doozr_Kernel
 * @subpackage Doozr_Kernel_Encoding
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Encoding extends Doozr_Base_Class_Singleton
{
    /**
     * Instance of config
     *
     * @var Doozr_Configuration_Interface
     * @access protected
     */
    protected $config;

    /**
     * Logger
     *
     * @var Psr\Log\LoggerInterface
     * @access protected
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param Doozr_Configuration_Interface $config The config instance
     * @param LoggerInterface               $logger The logger instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access protected
     */
    protected function __construct($config, $logger)
    {
        $this->config = $config;
        $this->logger = $logger;

        // Get encoding
        $encoding = $config->kernel->localization->encoding;
        $mimetype = $config->kernel->localization->mimetype;

        // Begin configuration
        $this->setInternalEncoding($encoding);
        $this->setOutputEncoding($encoding);
        $this->setLanguage($encoding);
        $this->setRegexEncoding($encoding);
        $this->setDefaultCharset($encoding);
        $this->setDefaultMimeType($mimetype);
        //$this->setOutputHandler();
    }

    /**
     * This method is intend to set the internal encoding
     *
     * @param string $encoding The encoding to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setInternalEncoding($encoding = 'UTF-8')
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
     * @access protected
     */
    protected function setOutputEncoding($encoding = 'UTF-8')
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
     * @access protected
     */
    protected function setLanguage($encoding = 'UTF-8')
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
     * @access protected
     */
    protected function setRegexEncoding($encoding = 'UTF-8')
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
     * @access protected
     */
    protected function setDefaultCharset($encoding = 'UTF-8')
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
     * @access protected
     */
    protected function setDefaultMimeType($mimetype = 'text/html')
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
     * @access protected
     */
    protected function setOutputHandler($handler = 'mb_output_handler')
    {
        ob_start($handler);
    }
}
