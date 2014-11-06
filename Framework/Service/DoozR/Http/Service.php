<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Http - Service
 *
 * Service.php - Http Service
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
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Http
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Service/Multiple.php';
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Service/Interface.php';

/**
 * DoozR - Http - Service
 *
 * Http Service
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Http
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @inject     DoozR_Registry:DoozR_Registry identifier:__construct type:constructor position:1
 */
class DoozR_Http_Service extends DoozR_Base_Service_Multiple implements DoozR_Base_Service_Interface
{
    /**
     * The curl sessions/references/handles
     *
     * @var array
     * @access protected
     */
    protected $sessions = array();

    /**
     * The HOST
     *
     * @var string
     * @access protected
     */
    protected $host;

    /**
     * The PORT
     *
     * @var int
     * @access protected
     */
    protected $port;

    /**
     * The username
     *
     * @var string
     * @access protected
     */
    protected $user;

    /**
     * The password
     *
     * @var string
     * @access protected
     */
    protected $password;

    /**
     * The protocol
     *
     * @var string
     * @access protected
     */
    protected $protocol;

    /**
     * The multi connect state
     *
     * @var bool
     * @access protected
     */
    protected $multiple = false;

    /**
     * An array of DoozR_Http_Service instances
     *
     * @var DoozR_Http_Service[]
     * @access protected
     */
    protected $append = array();

    /**
     * The silent runtimeEnvironment
     * TRUE = suppress exceptions / FALSE show exceptions
     *
     * @var bool
     * @access protected
     */
    protected $silent = false;

    /**
     * The requests currently on stack
     *
     * @var array
     * @access protected
     */
    protected $requests = array();

    /**
     * The header used when requesting data
     *
     * @var array
     * @access protected
     */
    protected $header = array();

    /**
     * The status for OK = 200
     *
     * @const
     * @access public
     */
    const HTTP_STATUS_OK      = 200;

    /**
     * The status for CREATED = 201
     *
     * @const
     * @access public
     */
    const HTTP_STATUS_CREATED = 201;

    /**
     * The status for ACCEPTED = 202
     *
     * @const
     * @access public
     */
    const HTTP_STATUS_ACCEPTED = 202;

    /**
     * The protocol HTTP
     *
     * @const
     * @access public
     */
    const CONNECTION_PROTOCOL_HTTP  = 'http';

    /**
     * The protocol HTTPS
     *
     * @const
     * @access public
     */
    const CONNECTION_PROTOCOL_HTTPS = 'https';

    /**
     * The method GET
     * API's -> Read
     *
     * @const
     * @access public
     */
    const GET = 'GET';

    /**
     * The method POST
     * API's -> Create
     *
     * @const
     * @access public
     */
    const POST = 'POST';

    /**
     * The method PUT
     * API's -> Update
     *
     * @const
     * @access public
     */
    const PUT = 'PUT';

    /**
     * The method PATCH
     *
     * @const
     * @access public
     */
    const PATCH = 'PATCH';

    /**
     * The method DELETE
     * API's -> Delete
     *
     * @const
     * @access public
     */
    const DELETE = 'DELETE';

    /**
     * The method HEAD
     *
     * @const
     * @access public
     */
    const HEAD = 'HEAD';

    /**
     * The method OPTIONS
     *
     * @const
     * @access public
     */
    const OPTIONS = 'OPTIONS';

    /**
     * The method CONNECT
     *
     * @const
     * @access public
     */
    const CONNECT = 'CONNECT';

    /**
     * The method TRACE
     *
     * @const
     * @access public
     */
    const TRACE   = 'TRACE';

    /**
     * The time paused between two executions
     *
     * @const
     * @access public
     */
    const REQUEST_SLEEP_BETWEEN = 0.2;


    /**
     * Constructor replacement for services of DoozR Framework
     *
     * @param null|string $host     The name/IP of the host to connect to
     * @param int         $port     The port to connect to
     * @param string      $protocol The protocol to user (can be either HTTP|HTTPS)
     * @param bool        $multiple TRUE to use a multi-connect, otherwise FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object Instance of this class
     * @access public
     */
    public function __tearup($host = null, $port = 80, $protocol = self::CONNECTION_PROTOCOL_HTTP, $multiple = false)
    {
        $this->host     = $host;
        $this->port     = ($port === null && $protocol === self::CONNECTION_PROTOCOL_HTTPS) ? 443 : $port;
        $this->protocol = $protocol;
        $this->multiple = $multiple;
    }

    /**
     * Set credentials (user + password) for connection/request
     *
     * @param string $user     The username
     * @param string $password The password
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function setCredentials($user, $password)
    {
        $this->user     = $user;
        $this->password = $password;

        return true;
    }

    /**
     * Shortcut to setCredentials
     *
     * @param string $user     The username
     * @param string $password The password
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Http_Service Instance for chaining
     * @access public
     */
    public function credentials($user, $password)
    {
        $this->setCredentials($user, $password);

        return $this;
    }

    /**
     * Return the user-credentials
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The user-credentials
     * @access public
     */
    public function getCredentials()
    {
        return array(
            'user'     => $this->user,
            'password' => $this->password
        );
    }

    /**
     * Sets the protocol
     *
     * @param string $protocol The protocol to use for request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;

        return true;
    }

    /**
     * Shortcut to setProtocol
     *
     * @param string $protocol The protocol to use for request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Http_Service
     * @access public
     */
    public function protocol($protocol)
    {
        $this->setProtocol($protocol);

        return $this;
    }

    /**
     * Returns the protocol
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The protocol
     * @access public
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Sets the host
     *
     * @param string $host The host to use for request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function setHost($host)
    {
        $this->host = $host;

        return true;
    }

    /**
     * Shortcut to setHost
     *
     * @param string $host The host to use for request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Http_Service Instance for chaining
     * @access public
     */
    public function host($host)
    {
        $this->setHost($host);

        return $this;
    }

    /**
     * Returns the host
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The host
     * @access public
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Sets the port used for request
     *
     * @param int $port The port to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function setPort($port)
    {
        $this->port = $port;

        return true;
    }

    /**
     * Shortcut to setPort
     *
     * @param int $port The port to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Http_Service Instance for chaining
     * @access public
     */
    public function port($port)
    {
        $this->setPort($port);

        return $this;
    }

    /**
     * Returns the port
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The port
     * @access public
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Sets the silent-runtimeEnvironment
     *
     * @param bool $mode TRUE to be silent, otherwise FALSE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function setSilent($mode = true)
    {
        $this->silent = $mode;

        return true;
    }

    /**
     * Shortcut to setSilentMode
     *
     * @param bool $mode TRUE to be silent, otherwise FALSE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Http_Service $this The current instance for chaining
     * @access public
     */
    public function silent($mode = true)
    {
        $this->setSilentMode($mode);

        return $this;
    }

    /**
     * Returns the current silent-runtimeEnvironment
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if silent-runtimeEnvironment enabled, otherwise FALSE
     * @access public
     */
    public function getSilent()
    {
        return $this->silent;
    }

    /**
     * Sets the header for request
     *
     * @param array $header The header to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function setHeader($header)
    {
        if (is_array($header)) {
            $header = array($header);
        }

        $this->header = $header;

        return true;
    }

    /**
     * Shortcut to setHeader for request
     *
     * @param array $header The header to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Http_Service
     * @access public
     */
    public function header($header)
    {
        $this->setHeader($header);

        return $this;
    }

    /**
     * Returns the header
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Http_Service
     * @access public
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Returns the stored requests
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The requests
     * @access public
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * Prepare for a GET request
     *
     * @param string $url       The URL to request
     * @param array  $parameter The parameter to pass to request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Http_Service $this The current instance for chaining
     * @access public
     */
    public function get($url = null, $parameter = array())
    {
        $this->requests[] = array(
            'method'    => self::GET,
            'url'       => $this->url(($url === null) ? '' : $url),
            'parameter' => $parameter
        );

        return $this;
    }

    /**
     * Prepare for a POST request
     *
     * @param string $url       The URL to request
     * @param array  $parameter The parameter to pass to request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Http_Service $this The current instance for chaining
     * @access public
     */
    public function post($url = null, $parameter = array())
    {
        $this->requests[] = array(
            'method'    => self::POST,
            'url'       => $this->url(($url === null) ? '' : $url),
            'parameter' => $parameter
        );

        return $this;
    }

    /**
     * Prepare for a PUT request
     *
     * @param string $url       The URL to request
     * @param array  $parameter The parameter to pass to request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Http_Service $this The current instance for chaining
     * @access public
     */
    public function put($url = null, $parameter = array())
    {
        $this->requests[] = array(
            'method'    => self::PUT,
            'url'       => $this->url(($url === null) ? '' : $url),
            'parameter' => $parameter
        );

        return $this;
    }

    /**
     * Prepare for a DELETE request
     *
     * @param string $url       The URL to request
     * @param array  $parameter The parameter to pass to request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Http_Service $this The current instance for chaining
     * @access public
     */
    public function delete($url = null, $parameter = array())
    {
        $this->requests[] = array(
            'method'    => self::DELETE,
            'url'       => $this->url(($url === null) ? '' : $url),
            'parameter' => $parameter
        );

        return $this;
    }

    /**
     * Add an instance of DoozR_Http_Service to requests
     *
     * @param DoozR_Http_Service $httpService
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Http_Service $this The current instance for chaining
     * @access public
     */
    public function add(DoozR_Http_Service $httpService)
    {
        $this->append[] = $httpService;

        return $this;
    }

    /**
     * PUT request
     *
     * @param string $url
     * @param array  $parameter
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The result as an array status => 400,401,403..., response => '<html ...>'
     * @access public
     */
    public function doPut($url = null, $parameter = array())
    {
        return $this->_exec(
            self::PUT,
            $this->url(($url === null) ? '' : $url),
            $parameter
        );
    }

    /**
     * POST request
     *
     * @param string $url
     * @param array  $parameter
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The result as an array status => 400,401,403..., response => '<html ...>'
     * @access public
     */
    public function doPost($url = null, $parameter = array())
    {
        return $this->_exec(
            self::POST,
            $this->url(($url === null) ? '' : $url),
            $parameter
        );
    }

    /**
     * GET Request
     *
     * @param string $url
     * @param array  $parameter
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The result as an array status => 400,401,403..., response => '<html ...>'
     * @access public
     */
    public function doGet($url = null, $parameter = array())
    {
        return $this->_exec(
            self::GET,
            $this->url(($url === null) ? '' : $url),
            $parameter
        );
    }

    /**
     * DELETE Request
     *
     * @param string $url
     * @param array  $parameter
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The result as an array status => 400,401,403..., response => '<html ...>'
     * @access public
     */
    public function doDelete($url = null, $parameter = array())
    {
        return $this->_exec(
            self::DELETE,
            $this->url(($url === null) ? '' : $url),
            $parameter
        );
    }

    public function run()
    {
        return $this->_run();
    }

    /**
     * Magic destruct
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __destruct()
    {
        foreach ($this->sessions as $session) {
            curl_close($session);
        }
    }






    /**
     * Builds the URL
     *
     * @param sring $url The URL to use for building complete URL
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The resulting URL
     * @access protected
     */
    protected function url($url = null)
    {
        return "{$this->protocol}://{$this->host}:{$this->port}/{$url}";
    }

    /**
     * Format the response as array
     *
     * @param $status   The statuscode of the response
     * @param $response The response' content
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The formatted response
     * @access protected
     */
    protected function formatResponse($status, $response)
    {
        return array(
            'code'     => $status,
            'response' => $response
        );
    }




    /**
     * Initializes a new curl session and returns it
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return resource a cURL handle on success, false on errors.
     * @access private
     */
    private function _init()
    {
        $count = count($this->sessions);
        $this->sessions[$count] = curl_init();

        return $this->sessions[$count];
    }

    /**
     * Configures the credentials for the request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access private
     */
    private function _configureCredentials($session)
    {
        $result = false;

        if ($this->user !== null && $this->password !== null) {
            $result = curl_setopt($session, CURLOPT_USERPWD, $this->user . ':' . $this->password);
        }

        return $result;
    }

    /**
     * Configures curl for the passed method (PUT; GET; POST ...)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _method($session, $method, $url, $parameter)
    {
        switch ($method) {

            case self::DELETE:
                curl_setopt($session, CURLOPT_URL, $url . '?' . http_build_query($parameter));
                curl_setopt($session, CURLOPT_CUSTOMREQUEST, self::DELETE);
                break;

            case self::PUT:
                curl_setopt($session, CURLOPT_URL, $url);
                curl_setopt($session, CURLOPT_CUSTOMREQUEST, self::PUT);
                curl_setopt($session, CURLOPT_POSTFIELDS, $parameter);
                break;

            case self::POST:
                curl_setopt($session, CURLOPT_URL, $url);
                curl_setopt($session, CURLOPT_POST, true);
                curl_setopt($session, CURLOPT_POSTFIELDS, $parameter);
                break;

            case self::GET:
                curl_setopt($session, CURLOPT_URL, $url . '?' . http_build_query($parameter));
                break;
        }
    }

    /**
     * Configures curl for the header
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _header($session, $header)
    {
        return curl_setopt($session, CURLOPT_HTTPHEADER, $header);
    }

    /**
     * Closes curl session
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _close($session)
    {
        curl_close($session);
    }

    /**
     * Executes a curl request and optionally closes the connection afterwards
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The response/result
     * @access private
     */
    private function _call($session, $autoClose = false)
    {
        $result = curl_exec($session);

        if ($autoClose === true) {
            $this->_close($session);
        }

        return $result;
    }

    /**
     * Configures the Return-Transfer option
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access private
     */
    private function _returnTransfer($session, $state)
    {
        return curl_setopt($session, CURLOPT_RETURNTRANSFER, $state);
    }

    /**
     * Adds a passed session to a passed curl multi handle
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return resource The new resource
     * @access private
     */
    private function _addToMultiRequest($multiHandle, $session)
    {
        return curl_multi_add_handle($multiHandle, $session);
    }

    /**
     * Executes a multi curl transaction
     *
     * @param resource $multiHandle The curl multi handle
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _execMultiRequest($multiHandle)
    {
        $running = null;

        do {
            curl_multi_exec($multiHandle, $running);
            sleep(self::REQUEST_SLEEP_BETWEEN);

        } while ($running > 0);
    }

    /**
     * Merges multiple DoozR_Http_Service instances (added via add()) to this instance
     *
     * @param array                $base   The base to merge requests with
     * @param DoozR_Http_Service[] $append The services to add requests from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The resulting array
     * @access private
     */
    private function _mergeMultiple(array $base, array $append)
    {
        $result = $base;

        if (count($append) > 0) {
            foreach ($append as $httpService) {
                $result = array_merge($result, $httpService->getRequests());
            }
        }

        return $result;
    }

    /**
     * The execution method which handles the complete request
     * from connection to response for doXXX Methods
     *
     * @param string $method    The method to use
     * @param string $url       The url to request
     * @param array  $parameter The parameter to use for the request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The resulting array
     * @access private
     */
    private function _exec($method, $url, array $parameter)
    {
        $result = array();

        // init a new curl session
        $session = $this->_init();

        // manage credentials
        $this->_configureCredentials($session);

        // set method
        $this->_method($session, $method, $url, $parameter);

        // configure return transfer
        $this->_returnTransfer($session, true);

        // set additional header for request
        $this->_header($session,  $this->header);

        // get response
        $response = $this->_call($session, true);

        // construct result
        $result = $this->formatResponse(curl_getinfo($session, CURLINFO_HTTP_CODE), $response);

        // return the result of operation
        return $result;
    }

    /**
     * The execution method which handles the complete request
     * from connection to response
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The resulting array
     * @access private
     */
    private function _run()
    {
        $result = array();

        // prepare data for multi-run if active
        if ($this->multiple) {
            $this->requests = $this->_mergeRequests($this->requests, $this->append);
        }

        // get a handle for multi requests
        $multiHandle = curl_multi_init();

        // iterate requests and start request(s)
        foreach ($this->requests as $id => $request) {

            $session = $this->_init();

            $this->_configureCredentials($session);

            $this->_method($session, $request['method'], $request['url'], $request['parameter']);

            $this->_returnTransfer($session, true);

            $this->_header($session, $this->header);

            $this->_addToMultiRequest($multiHandle, $session);
        }

        // exec multi request and manage loop
        $this->_execMultiRequest($multiHandle);

        // iterate results!
        foreach ($this->sessions as $id => $session) {

            $status = curl_getinfo($session, CURLINFO_HTTP_CODE);

            $result[] = $this->formatResponse($status, curl_multi_getcontent($session));

            curl_multi_remove_handle($multiHandle, $session);
        }

        curl_multi_close($multiHandle);

        return $result;
    }
}
