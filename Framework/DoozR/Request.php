<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Request
 *
 * Request.php - Request state container.
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
 * @package    DoozR_Request
 * @subpackage DoozR_Request
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Registry.php';
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/State/Container.php';
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/State/Interface.php';

/**
 * DoozR - Request
 *
 * Request state container.
 *
 * @category   DoozR
 * @package    DoozR_Request
 * @subpackage DoozR_Request
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Request extends DoozR_Base_State_Container
{
    /**
     * The type native for PHP request sources
     *
     * @var integer
     * @access const
     */
    const NATIVE = 0;

    /**
     * The type emulated for PHP request sources
     *
     * @var integer
     * @access const
     */
    const EMULATED = 1;

    /**
     * The request sources valid for active running runtimeEnvironment.
     *
     * @var array
     * @access protected
     */
    protected $requestSources;


    /**
     * Constructor.
     *
     * Custom constructor which is required to set app.
     * And then it calls the parent constructor which does the bootstrapping.
     *
     * @param DoozR_Registry             $registry    The registry containing all important instances
     * @param DoozR_Base_State_Interface $stateObject The state object instance to use for saving state (DI)
     * @param string                     $requestUri  The request URI for overriding detection of real
     * @param string                     $sapi        The SAPI runtimeEnvironment of active PHP Instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Request
     * @access public
     */
    public function __construct(
        DoozR_Registry             $registry,
        DoozR_Base_State_Interface $stateObject,
                                   $requestUri  = null,
                                   $sapi        = PHP_SAPI
    ) {
        $this->setRegistry($registry);

        parent::__construct($stateObject);

        // Check for override URI
        if ($requestUri === null) {
            $requestUri = $_SERVER['REQUEST_URI'];
        }

        // Start the job
        $this->determineState($requestUri, $sapi);
    }

    /**
     * Detects important parts of request and brings them in a more ore better usable order.
     *
     * @param string $requestUri The request URI of current request
     * @param string $sapi       The SAPI used
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function determineState($requestUri, $sapi = PHP_SAPI)
    {
        // Store request URI
        $this->getStateObject()->setRequestUri($requestUri);

        // Store clean URL
        $this->getStateObject()->setUrl(strtok($requestUri, '?'));

        // Store SAPI (CLI, HTTPD, APACHE ....)
        $this->getStateObject()->setSapi($sapi);

        // Store runtimeEnvironment the framework runs in. Something like CLI or CLI-SERVER (PHP's internal webserver) or ...
        $mode = $this->getModeBySapi($sapi);

        // Set valid request sources
        $this->setRequestSources(
            $this->emitValidRequestSources($mode)
        );

        // Store the runtimeEnvironment
        $this->getStateObject()->setRuntimeEnvironment($mode);

        // Store method
        $this->getStateObject()->setMethod(
            $this->getMethod()
        );

        // Store SSL state
        $this->getStateObject()->setSsl(
            isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == '1' || strtolower($_SERVER['HTTPS']) == 'on')
        );

        // Store headers normalized to prevent System/OS/PHP mismatches
        $this->getStateObject()->setHeaders(
            $this->normalizeHeaders(getallheaders())
        );

        $this->getStateObject()->setProtocol(
            ($this->getStateObject()->isSsl() === true) ? 'https://' : 'http://'
        );

        $this->emulateRequest(
            $this->getStateObject()->getMethod()
        );

        $this->getStateObject()->setArguments(
            $this->transformToRequestObject($this->getStateObject()->getMethod())
        );
    }

    /**
     * Returns the method (POST / GET / PUT ... || CLI) of the current processed request.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string the method of current processed request (GET / POST / PUT ... || CLI)
     * @access public
     */
    public function getMethod()
    {
        if ($requestMethod = (isset($_SERVER['REQUEST_METHOD'])) ? $_SERVER['REQUEST_METHOD'] : null) {
            return strtoupper($requestMethod);
        } else {
            return strtoupper($this->getRequestType());
        }
    }

    /**
     * Transforms a given PHP-Global (e.g. SERVER [without "$_"]) to an object with an array interface
     *
     * This method is intend to transform a given PHP-Global (e.g. SERVER [without "$_"])
     * to an object with an array interface.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @throws DoozR_Exception
     * @return void
     * @access protected
     */
    protected function transform()
    {
        // get dynamic the sources
        $requestSources = array_change_value_case(func_get_args(), CASE_UPPER);

        // iterate over given sources
        foreach ($requestSources as $requestSource) {
            if (!in_array($requestSource, $this->_requestSources)) {
                throw new DoozR_Exception(
                    'Invalid request-source "$_'.$requestSource.'" passed to '.__METHOD__
                );
            }

            // build objects from global request array(s) like SERVER, GET, POST | CLI
            $this->transformToRequestObject($requestSource);
        }

        // successful transformed
        return true;
    }

    /**
     * Getter for request type.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The active request type
     * @access protected
     */
    protected function getRequestType()
    {
        return $this->requestType;
    }

    /**
     * Transforms a given superglobal (e.g. _GET, _POST ...) to an object
     *
     * This method is intend to transforms a given global to an object and replace the original
     * PHP-Global with the new object.
     *
     * @param string $globalVariable The PHP-global to process (POST, GET, COOKIE, SESSION ...)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function transformToRequestObject($globalVariable, $injectInRootScope = false)
    {
        // get prefix
        $globalVariable = $this->addPrefix($globalVariable);

        if (
            (isset($GLOBALS[$globalVariable]) === true) &&
            (($GLOBALS[$globalVariable] instanceof DoozR_Request_Arguments) === false)
        ) {
            $arguments = new DoozR_Request_Arguments($globalVariable);

            // Replace passed superglobal with object-interface
            if ($injectInRootScope === true) {
                $GLOBALS[$globalVariable] = $arguments;
            }

        } else {
            // this enables us to use a quick preset without the dependency to run a detection twice
            $arguments = $GLOBALS[$globalVariable];

        }

        return $arguments;
    }

    /**
     * This method emulates those requests which are not implemented
     * in PHP's global by default. So you can access PUT via $_PUT
     * and DELETE via $_DELETE and so on.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access private
     */
    protected function emulateRequest($method)
    {
        global $_PUT, $_DELETE, $_REQUEST;

        $headers = $this->getStateObject()->getHeaders();

        // Check if current request type must be emulated OR
        // If we reach here cause of a POST request without header content-type application/x-www-form-urlencoded
        if (
            ($this->requestSources[$method] === self::EMULATED) ||
            (
                $method === DoozR_Request_State::METHOD_POST &&
                (
                    (isset($headers['CONTENT_TYPE']) === false) ||
                    (stristr(strtolower($headers['CONTENT_TYPE']), 'application/x-www-form-urlencoded') === false)
                )
            )
        ) {
            //$GLOBALS['_' . $method]['DOOZR_REQUEST_BODY'] = file_get_contents("php://input");
            // So we @ DoozR decided that we equalize the accessibility of arguments passed to a PHP process.
            // To do so we extract the data from request body as single arguments instead of taking them as something
            // completely different. So we also inject the values into global $_REQUEST.
            $requestBody = file_get_contents("php://input");

            // Check for empty request body
            if (strlen($requestBody) > 0) {

                // Automagically prepare data send in body (often JSON!) as object (auto extract)- Why? Just to be nice :O
                $data = json_decode($requestBody, false);

                // Check if response could be extracted (= JSON input) if not do conversion to stdClass now:
                if ($data === null) {
                    $data  = new \stdClass();
                    $input = explode('&', $requestBody);

                    foreach ($input as $argumentSet) {
                        $keyValue = explode('=', $argumentSet);
                        $data->{$keyValue[0]} = isset($keyValue[1]) ? $keyValue[1] : null;
                    }
                }

                foreach ($data as $argument => $value) {
                    $GLOBALS['_' . $method][$argument] = $value;
                    $_REQUEST[$argument] = $value;
                }
            }
        }

        return true;
    }

    /**
     * Setter for request sources.
     *
     * @param array $requestSources The request sources to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setRequestSources(array $requestSources)
    {
        $this->requestSources = $requestSources;
    }

    /**
     * Setter for request sources.
     *
     * @param array $requestSources The request sources to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function requestSources(array $requestSources)
    {
        $this->setRequestSources($requestSources);
        return $this;
    }

    /**
     * Getter for request sources.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array|null The request sources stored, otherwise NULL
     * @access protected
     */
    protected function getRequestSources()
    {
        return $this->requestSources;
    }

    /**
     * Combines the request sources to a single array by passed runtimeEnvironment.
     *
     * @param string $mode The active runtimeEnvironment to return request sources for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The combined request sources
     * @access protected
     */
    protected function emitValidRequestSources($mode)
    {
        $requestSources = array(
            'ENVIRONMENT' => self::NATIVE,
            'SERVER'      => self::NATIVE,
        );

        switch ($mode) {
            case DoozR_Request_State::RUNTIME_ENVIRONMENT_CLI:
                $requestSources = array_merge(
                    $requestSources,
                    array(
                        'CLI' => self::NATIVE
                    )
                );
                break;

            case DoozR_Request_State::RUNTIME_ENVIRONMENT_WEB:
            case DoozR_Request_State::RUNTIME_ENVIRONMENT_HTTPD:
            default:
                $requestSources = array_merge(
                    $requestSources,
                    array(
                        'GET'         => self::NATIVE,
                        'POST'        => self::NATIVE,
                        'HEAD'        => self::EMULATED,
                        'OPTIONS'     => self::EMULATED,
                        'PUT'         => self::EMULATED,
                        'DELETE'      => self::EMULATED,
                        'TRACE'       => self::EMULATED,
                        'CONNECT'     => self::EMULATED,
                        'COOKIE'      => self::NATIVE,
                        'REQUEST'     => self::NATIVE,
                        'SESSION'     => self::NATIVE,
                        'FILES'       => self::NATIVE,
                    )
                );
                break;
        }

        return $requestSources;
    }

    /**
     * Returns input prefixed with an underscore.
     *
     * @param string $value The string to add an underscore to
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The prefixed string
     * @access protected
     */
    protected function addPrefix($value)
    {
        // check if already prefixed
        if ($value == 'argv' || strpos($value, '_')) {
            return $value;
        }

        return '_'.$value;
    }

    /**
     * Normalizes headers so they are accessible on all OS' in the same way/naming ...
     *
     * @param array $headers The headers to normalize
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The normalized array of headers (all keys = uppercase & underscore!)
     * @access protected
     */
    protected function normalizeHeaders(array $headers)
    {
        $normalized = array();

        foreach ($headers as $header => $value) {
            $normalized[str_replace('-', '_', strtoupper($header))] = $value;
        }

        return $normalized;
    }

    /**
     * Proxy to teach IDE the correct return type ;)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Request_State The state object
     * @access protected
     */
    protected function getStateObject()
    {
        return parent::getStateObject();
    }

    /**
     * Detect and returns SAPI PHP running in/on.
     * (aolserver, apache, apache2filter, apache2handler, caudium, cgi (until PHP 5.3), cgi-fcgi, cli, continuity,
     * embed, isapi, litespeed, milter, nsapi, phttpd, pi3web, roxen, thttpd, tux und webjames)
     *
     * @param string $sapi The SAPI of PHP
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The runtimeEnvironment [web | cli | cli-server]
     * @access protected
     */
    protected function getModeBySapi($sapi)
    {
        // Assume default running runtimeEnvironment
        $mode = DoozR_Request_State::RUNTIME_ENVIRONMENT_WEB;

        // Detect running runtimeEnvironment through php functionality
        switch ($sapi) {
            case 'cli':
                $mode = DoozR_Request_State::RUNTIME_ENVIRONMENT_CLI;
                break;
            case 'cli-server':
                $mode = DoozR_Request_State::RUNTIME_ENVIRONMENT_HTTPD;
                break;
        }

        return $mode;
    }
}
