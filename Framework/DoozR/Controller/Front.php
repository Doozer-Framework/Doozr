<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Controller - Front
 *
 * Front.php - The Front-Controller of the DoozR-Framework.
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
 * @package    DoozR_Controller
 * @subpackage DoozR_Controller_Front
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Class/Singleton.php';

/**
 * DoozR - Controller - Front
 *
 * The Front-Controller of the DoozR-Framework.
 *
 * @category   DoozR
 * @package    DoozR_Controller
 * @subpackage DoozR_Controller_Front
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Controller_Front extends DoozR_Base_Class_Singleton
{
    /**
     * detected running-mode
     *
     * holds the detected running mode (web or cli)
     *
     * @var string
     * @access public
     */
    private $_runningMode = 'web';

    /**
     * holds the reference to request
     *
     * @var object
     * @access private
     */
    private $_request;

    /**
     * holds the reference to response
     *
     * @var object
     * @access private
     */
    private $_response;

    /**
     * holds instance of logger
     *
     * @var object
     * @access private
     */
    private $_logger;

    /**
     * holds instance of config
     *
     * @var object
     * @access private
     */
    private $_config;

    /**
     * constant RUNNING_MODE_CLI
     *
     * holds the key for "cli" running mode
     *
     * @var string
     * @access public
     */
    const RUNNING_MODE_CLI = 'cli';

    /**
     * constant RUNNING_MODE_WEB
     *
     * holds the key for "web" running mode
     *
     * @var string
     * @access public
     */
    const RUNNING_MODE_WEB = 'web';

    /**
     * constant RUNNING_MODE_HTTPD
     *
     * holds the key for "httpd" running mode
     *
     * @var string
     * @access public
     */
    const RUNNING_MODE_HTTPD = 'httpd';


    /**
     * Constructor.
     *
     * @param DoozR_Config_Interface $config An instance of DoozR_Config
     * @param DoozR_Logger_Interface $logger An instance of DoozR_Logger
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Controller_Front instance of this class
     * @access protected
     */
    protected function __construct(DoozR_Config_Interface $config, DoozR_Logger_Interface $logger)
    {
        // store instance(s)
        $this->_config = $config;
        $this->_logger = $logger;

        // first detect the command source
        $this->_runningMode = $this->detectRunningMode();

        // init request always
        $this->_initialize('request');
    }

    /**
     * This method is intend to include the required files.
     *
     * @param string $part The part (request/response)
     * @param string $mode The active mode (web/cli)  of part
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _includeFile($part, $mode)
    {
        include_once DOOZR_DOCUMENT_ROOT . 'DoozR/'.$part.'/'.$mode.'.php';
    }

    /**
     * This method is intend to initialize request/response.
     *
     * @param string $part The part (request/response)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object The new instanciated class of request/response
     * @access private
     */
    private function _initialize($part)
    {
        // mode + part in correct writing
        $part = ucfirst($part);
        $mode = ucfirst($this->_runningMode);

        // get required include files
        $this->_includeFile($part, $mode);

        // build classname from part + mode
        $classname = 'DoozR_'.$part.'_'.$mode;

        // return new instance
        $instance = $this->instanciate($classname, array($this->_config, $this->_logger));

        return $instance;
    }

    /**
     * This method is intend to return the instance of DoozR_Request_(Web|Cli|Httpd)
     * depending on environment
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Request_Web or DoozR_Request_Cli or DoozR_Request_Httpd instance
     * @access public
     */
    public function getRequest()
    {
        // lazy init
        if ($this->_request === null) {
            $this->_request = $this->_initialize('request');
        }

        // return request instance
        return $this->_request;
    }

    /**
     * Returns the instanciated response class (web | cli)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Base_Response instance of either web or cli
     * @access public
     */
    public function getResponse()
    {
        // lazy init
        if ($this->_response === null) {
            $this->_response = $this->_initialize('response');
        }

        return $this->_response;
    }

    /**
     * Detects and returns the external address under which the app is accessible
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The external address under which the app is accessible
     * @access private
     */
    private function _externalAddress()
    {
        // TODO: REMOVE OR WHAT?
        /*
         // DoozR does not have a URL if running in CLI-mode
         if ($this->_runningMode == self::RUNNING_MODE_CLI) {
         //use document root for document URL in cli mode.
         define('DOOZR_URL', DOOZR_DOCUMENT_ROOT);
         return true;
         } else {

         }
         */

        // construct base URL
        if ($path = stristr($this->_basePath, 'htdocs')) {
            // apache
            $path = str_replace('htdocs', '', $path);
        } elseif ($path = stristr($this->_basePath, 'inetpub')) {
            // iis
            $path = str_replace('inetpub', '', $path);
        }

        // set correct slashes
        $path = str_replace('\\', '/', $path);

        $this->_baseURL = getProtocol().strtolower($_SERVER['SERVER_NAME']).$path;

        define('DOOZR_URL', $this->_baseURL);
    }

    /**
     * Detects the current running-mode
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The current running-mode either [web | cli | cli-server]
     * @access protected
     */
    protected function detectRunningMode()
    {
        // detect running mode through php functionality
        if (PHP_SAPI === 'cli') {
            $runningMode = self::RUNNING_MODE_CLI;

        } elseif (PHP_SAPI === 'cli-server') {
            $runningMode = self::RUNNING_MODE_HTTPD;

        } else {
            $runningMode = self::RUNNING_MODE_WEB;
        }

        return $runningMode;
    }

    /**
     * returns the current running-mode
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The current running-mode either [web | cli]
     * @access private
     */
    public function getRunningMode()
    {
        return $this->_runningMode;
    }
}
