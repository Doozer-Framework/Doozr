<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Request - Web
 *
 * Web.php - Request-Handler for requests passed through WEB to Front-Controller.
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
 * @package    DoozR_Request
 * @subpackage DoozR_Request_Web
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Request.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Request/Interface.php';

/**
 * DoozR Request-Web
 *
 * Request-Web of the DoozR-Framework
 *
 * @category   DoozR
 * @package    DoozR_Request
 * @subpackage DoozR_Request_Web
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Request_Web extends DoozR_Base_Request implements DoozR_Request_Interface
{
    /**
     * need to identify HTML Redirect Type
     *
     * @var string
     * @access const
     */
    const REDIRECT_TYPE_HTML = 'html';

    /**
     * need to identify Header Redirect Type
     *
     * @var string
     * @access const
     */
    const REDIRECT_TYPE_HEADER = 'header';

    /**
     * need to identify HTML Redirect Type
     *
     * @var string
     * @access const
     */
    const REDIRECT_TYPE_JS = 'js';

    /**
     * holds the own type of Request
     *
     * @var string
     * @access const
     */
    const TYPE = 'web';


    /**
     * Constructor of this class
     *
     * This method is the constructor of this class.
     *
     * @param DoozR_Config $config An instance of config
     * @param DoozR_Logger $logger An instance of logger
     *
     * @return void
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __construct(DoozR_Config $config, DoozR_Logger $logger)
    {
        // store instance(s)
        $this->logger = $logger;
        $this->config = $config;

        // set type - TODO: really needed? seems to be senseless
        self::$type = self::TYPE;

        // LIST VALID REQUEST-SOURCES
        $this->_requestSources = array(
            'GET',
            'POST',
            'COOKIE',
            'REQUEST',
            'SESSION',
            'ENVIRONMENT',
            'SERVER'
        );

        // get securitylayer functionality (htmlpurifier + phpids)
        // sanitize global arrays, retrieve impacts and maybe cancle the whole request
        parent::__construct();

        // check for ssl forcement
        $this->_checkForceSecureConnection();

        // this is expensive so only in debug available
        if ($this->config->debug->enabled()) {

            // log Request-Parameter and Request-Header
            $this->logger->log(
                "Request-Header: \n".self::getRequestHeader(true)
            );

            // if request defined -> log its parameter (all)
            if (count($_REQUEST) > 0) {
                $this->logger->log(
                    "Request-Parameter: \n".self::getRequestAsString()
                );
            }
        }
    }

    /**
     * returns the status of "is current request a get-request"
     *
     * This method is intend to return the status of "is current request a get-request".
     *
     * @return boolean TRUE if current request is GET, otherwise FALSE
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function isGet()
    {
        return ($this->getRequestMethod() == 'GET');
    }

    /**
     * returns the status of "is current request a get-request"
     *
     * This method is intend to return the status of "is current request a get-request".
     *
     * @return boolean TRUE if current request is GET, otherwise FALSE
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function isPost()
    {
        return ($this->getRequestMethod() == 'POST');
    }

    /**
     * returns the global $_GET vars if current request is of type GET
     *
     * This method is intend to return the global $_GET vars if current request is of type GET.
     *
     * @return mixed Request_Parameter ($_GET) if request is of type GET, otherwise NULL
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function getGet()
    {
        if ($this->isGet()) {
            return $_GET;
        }

        // return null if not GET
        return null;
    }

    /**
     * returns the global $_POST vars if current request is of type POST
     *
     * This method is intend to return the global $_POST vars if current request is of type POST.
     *
     * @return mixed Request_Parameter ($_POST) if request is of type POST, otherwise NULL
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function getPost()
    {
        if ($this->isPost()) {
            return $_POST;
        }

        // return null if not POST
        return null;
    }

    /**
     * returns the global $_REQUEST vars if current request is of type REQUEST
     *
     * This method is intend to return the global $_REQUEST vars if current request is of type REQUEST.
     *
     * @return mixed Request_Parameter ($_REQUEST) if request is of type REQUEST, otherwise NULL
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function getRequest()
    {
        return $_REQUEST;
    }

    /**
     * check if ssl forcement is enabled
     *
     * if FORCE_SSL is defined in TRANSMISSION part of the core-config and set to true
     * every non-ssl request will be redirected to ssl (https)
     *
     * @return boolean true if successful otherwise false
     * @access private
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    private function _checkForceSecureConnection()
    {
        if ($this->config->transmission->ssl->force()) {
            if (!$this->isSsl()) {
                header('Location: https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
            }
        }
    }

    /**
     * get all request-headers
     *
     * if the $string parameter is setted to true, this function will return
     * an string instead of an array
     *
     * @param bool $string Set to true to retrive header as string, otherwise array is returned
     *
     * @return mixed All request-headers as: "string" if parameter $string is set to true, otherwise as "array"
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @static
     */
    public static function getRequestHeader($string = false)
    {
        if (!$string) {
            return getallheaders();
        } else {
            $allheaders = getallheaders();
            $header = '';
            foreach ($allheaders as $headerName => $headerValue) {
                $header .= $headerName.' = '.$headerValue."\n";
            }
            return $header;
        }
    }

    /**
     * returns the global $_REQUEST as string
     *
     * returns the PHP Global Array $_REQUEST as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array All given $_REQUEST-vars as string
     * @access public
     * @static
     */
    public static function getRequestAsString()
    {
        $request = '';

        foreach ($_REQUEST as $parameter => $value) {
            $cleaned  = self::clean($value);
            $request .= $parameter.' = '.((is_array($cleaned)) ? serialize($cleaned) : $cleaned)."\n";
        }

        return $request;
    }

    /**
     * Cleans the input variable. This method removes tags with strip_tags and afterwards
     * it turns all non-safe characters to its htmlentities.
     *
     * @param mixed $mixed The input to clean
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The cleaned input
     * @access public
     * @static
     */
    public static function clean($mixed)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = self::clean($value);
            }

        } else {
            $mixed = htmlentities(strip_tags($mixed));
        }

        return $mixed;
    }

    /**
     * returns the SSL-status of the current request
     *
     * This method is intend to return the SSL-status of the current request.
     *
     * @return boolean TRUE if the request is SSL, otherwise FALSE
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function isSsl()
    {
        return (
            isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == '1' || strtolower($_SERVER['HTTPS']) == 'on')
        );
    }

    /**
     * checks for protocol and returns it
     *
     * This method is intend to check for protocol and returns it
     *
     * @return string The protocol used while accessing a resource
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function getProtocol()
    {
        if ($this->isSsl()) {
            return 'https://';
        } else {
            return 'http://';
        }
    }

    /**
     * shortcut to request-params
     *
     * this is a shortcut to allmost every (public-)method DoozR offers
     *
     * @param string $method    The name of the method called
     * @param array  $arguments The parameter of the method call
     *
     * @return mixed depends on input!
     *
     * @access magic
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __call($method, $arguments)
    {
        // check if init done for requested source;
        if (!isset(self::$initialized[$method])) {
            self::$initialized[$method] = $this->transform($method);
        }

        if (in_array($method, $this->_requestSources) && count($arguments)==0) {
            return $GLOBALS['_'.$method];
        }
    }
}

?>
