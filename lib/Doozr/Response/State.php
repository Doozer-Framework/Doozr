<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Response - State
 *
 * State.php - Response state class for transportation of response data
 *
 * PHP versions 5.4
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
 *
 * Copyright (c) 2005 - 2015, Benjamin Carl - All rights reserved.
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
 * @category   Doozr
 * @package    Doozr_Response
 * @subpackage Doozr_Response_State
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/State.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/State/Interface.php';

/**
 * Doozr - Response - State
 *
 * Response state class for transportation of Response data
 *
 * @category   Doozr
 * @package    Doozr_Response
 * @subpackage Doozr_Response_State
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Response_State extends Doozr_Base_State
    implements
    Doozr_Base_State_Interface
{
    /**
     * The request headers
     *
     * @var array
     * @access protected
     */
    protected $headers;

    /**
     * History to trace changes in flow.
     *
     * @var array
     * @access protected
     */
    protected $history = array();

    /**
     * The response arguments
     *
     * @var Doozr_Response_Arguments[]
     * @access protected
     */
    protected $arguments = array();


    /**
     * Extracts variables from current requests URL
     *
     * @param string  $pattern  The pattern to use for extracting variables from URL (e.g. /{{foo}}/{{bar}}/
     * @param closure $callback The callback/closure to execute
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed
     * @access public
     * @throws Exception
     */
    public function get($pattern, $callback = null)
    {
        // Check for required url
        if ($this->getUrl() === null) {
            throw new Exception('Set URL ($this->setUrl(...)) first.');
        }

        $count   = 0;
        $pattern = explode('/', trim($pattern));
        $url     = explode('/', $this->getUrl());

        array_shift($pattern);
        array_shift($url);

        $result = array();
        $matrix = array();

        foreach ($pattern as $key => $partial) {
            $variable = preg_match('/{{(.*)}}/i', $partial, $result);

            $count += $variable;
            if ($variable === 1 && isset($url[$key])) {
                $matrix[substr($partial, 1, strlen($partial) - 1)] = $url[$key];
            }
        }

        if ($callback !== null) {
            while (count($matrix) < $count) {
                $matrix[] = null;
            }

            $result = call_user_func_array($callback, $matrix);
            return $result;

        } else {
            return $matrix;
        }
    }

    /**
     * Adding argument to collection of arguments (key => value inline store).
     *
     * @param string $argument The argument to add
     * @param mixed  $value    The value of argument
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function addArgument($argument, $value)
    {
        $this->arguments[$argument] = $value;
    }

    /**
     * Removing argument from collection of arguments.
     *
     * @param string $argument The argument to remove
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function removeArgument($argument)
    {
        if (isset($this->arguments[$argument]) === true) {
            unset($this->arguments[$argument]);
            $this->arguments = array_values($this->arguments);
        }
    }

    /**
     * Setter for request arguments.
     *
     * @param Doozr_Response_Arguments $arguments
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Response_Arguments The arguments
     * @access public
     */
    public function setArguments(Doozr_Response_Arguments $arguments)
    {
        $this->addHistory(__METHOD__, func_get_args());
        $this->arguments = $arguments;
    }

    /**
     * Setter for request arguments.
     *
     * @param Doozr_Response_Arguments $arguments
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function arguments(Doozr_Response_Arguments $arguments)
    {
        $this->setArguments($arguments);
        return $this;
    }

    /**
     * Getter for arguments.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Response_Arguments The arguments
     * @access public
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Setter for HTTP-Verb
     *
     * @param string $verb The HTTP-Verb
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setVerb($verb)
    {
        $this->setMethod($verb);
    }

    /**
     * Setter for HTTP-Verb
     *
     * @param string $verb The HTTP-Verb
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Response_State Instance for chaining
     * @access public
     */
    public function verb($verb)
    {
        $this->setVerb($verb);
        return $this;
    }

    /**
     * Getter for HTTP-Verb
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The HTTP-Verb for Method used for requesting data from Doozr
     * @access public
     */
    public function getVerb()
    {
        return $this->getMethod();
    }

    /**
     * Setter for HTTP-Method
     *
     * @param string $method The method used for requesting data from Doozr
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setMethod($method)
    {
        $this->addHistory(__METHOD__, func_get_args());
        $this->method = $method;
    }

    /**
     * Setter for HTTP-Method
     *
     * @param string $method The method used for requesting data from Doozr
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Response_State Instance for chaining
     * @access public
     */
    public function method($method)
    {
        $this->setMethod($method);
        return $this;
    }

    /**
     * Getter for HTTP-Method
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The HTTP-Verb for Method used for requesting data from Doozr
     * @access public
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Setter for runtimeEnvironment.
     *
     * @param string $mode The runtimeEnvironment Doozr is running in (WEB, CLI, CLI-SERVER)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setMode($mode)
    {
        $this->addHistory(__METHOD__, func_get_args());
        $this->mode = $mode;
    }

    /**
     * Setter for runtimeEnvironment.
     *
     * @param string $mode The runtimeEnvironment Doozr is running in (WEB, CLI, CLI-SERVER)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Response_State Instance for chaining
     * @access public
     */
    public function mode($mode)
    {
        $this->setMode($mode);
        return $this;
    }

    /**
     * Getter for runtimeEnvironment.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The runtimeEnvironment Doozr is running in (WEB, CLI, CLI-SERVER)
     * @access public
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Setter for url.
     *
     * @param string $url The url
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setUrl($url)
    {
        $this->addHistory(__METHOD__, func_get_args());
        $this->url = $url;
    }

    /**
     * Setter for url.
     *
     * @param string $url The url
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Response_State Instance for chaining
     * @access public
     */
    public function url($url)
    {
        $this->setUrl($url);
        return $this;
    }

    /**
     * Getter for url.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The url
     * @access public
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Setter for requestUri.
     *
     * @param string $requestUri The request URI used for request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setResponseUri($requestUri)
    {
        $this->addHistory(__METHOD__, func_get_args());
        $this->requestUri = $requestUri;
    }

    /**
     * Setter for requestUri.
     *
     * @param string $requestUri The request URI used for request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Response_State Instance for chaining
     * @access public
     */
    public function requestUri($requestUri)
    {
        $this->setResponseUri($requestUri);
        return $this;
    }

    /**
     * Getter for requestUri.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The request URI used for request
     * @access public
     */
    public function getResponseUri()
    {
        return $this->requestUri;
    }

    /**
     * Setter for sapi.
     *
     * @param string $sapi The sapi Doozr running on
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setSapi($sapi)
    {
        $this->addHistory(__METHOD__, func_get_args());
        $this->sapi = $sapi;
    }

    /**
     * Setter for sapi.
     *
     * @param string $sapi The sapi Doozr running on
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Response_State Instance for chaining
     * @access public
     */
    public function sapi($sapi)
    {
        $this->setSapi($sapi);
        return $this;
    }

    /**
     * Getter for sapi.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The sapi Doozr running on
     * @access public
     */
    public function getSapi()
    {
        return $this->sapi;
    }

    /**
     * Setter for SSL.
     *
     * @param bool $ssl SSL state
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setSsl($ssl)
    {
        $this->addHistory(__METHOD__, func_get_args());
        $this->ssl = $ssl;
    }

    /**
     * Setter for SSL.
     *
     * @param bool $ssl SSL state
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Response_State Instance for chaining
     * @access public
     */
    public function ssl($ssl)
    {
        $this->setSsl($ssl);
        return $this;
    }

    /**
     * Getter for SSL.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean SSL state
     * @access public
     */
    public function getSsl()
    {
        return $this->ssl;
    }

    /**
     * Setter for headers.
     *
     * @param array $headers The headers to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setHeaders(array $headers)
    {
        $this->addHistory(__METHOD__, func_get_args());
        $this->headers = $headers;
    }

    /**
     * Setter for headers.
     *
     * @param array $headers The headers to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Response_State Instance for chaining
     * @access public
     */
    public function headers(array $headers)
    {
        $this->setHeaders($headers);
        return $this;
    }

    /**
     * Getter for headers.
     *
     * @param bool $string TRUE to return string, FALSE to return array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array|string The headers
     * @access public
     */
    public function getHeaders($string = false)
    {
        if ($string === true) {
            $headers = '';
            foreach ($this->headers as $headerName => $headerValue) {
                $headers .= $headerName . ' = ' . $headerValue . PHP_EOL;
            }
        } else {
            $headers = $this->headers;
        }

        return $headers;
    }

    /**
     * Setter for protocol.
     *
     * @param string $protocol The protocol used
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setProtocol($protocol)
    {
        $this->addHistory(__METHOD__, func_get_args());
        $this->protocol = $protocol;
    }

    /**
     * Setter for protocol.
     *
     * @param string $protocol The protocol used
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Response_State Instance for chaining
     * @access public
     */
    public function protocol($protocol)
    {
        $this->setProtocol($protocol);
        return $this;
    }

    /**
     * Getter for protocol.
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
     * Setter for request body.
     *
     * @param \stdClass $requestBody The request body to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setResponseBody(\stdClass $requestBody)
    {
        $this->requestBody = $requestBody;
    }

    /**
     * Setter for request body.
     *
     * @param \stdClass $requestBody The request body to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function requestBody(\stdClass $requestBody)
    {
        $this->setResponseBody($requestBody);
        return $this;
    }

    /**
     * Getter for requestBody.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \stdClass|null The request body if set otherwise NULL
     * @access public
     */
    public function getResponseBody($associative = false)
    {
        if ($associative === true) {
            $requestBody = object_to_array($this->requestBody);
        } else {
            $requestBody = $this->requestBody;
        }

        return $requestBody;
    }

    /**
     * Setter for routes
     *
     * @param stdClass $routes The routes object (often retrieved from config)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setRoutes(\stdClass $routes)
    {
        $this->addHistory(__METHOD__, func_get_args());
        $this->routes = $routes;
    }

    /**
     * Setter for routes
     *
     * @param stdClass $routes The routes object (often retrieved from config)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Response_State Instance for chaining
     * @access public
     */
    public function routes(\stdClass $routes)
    {
        $this->setProtocol($routes);
        return $this;
    }

    /**
     * Getter for routes.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \stdClass The routes
     * @access public
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Setter for active route.
     *
     * @param array $activeRoute The active and dispatched/processed route!
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setActiveRoute(array $activeRoute)
    {
        $this->addHistory(__METHOD__, func_get_args());
        $this->activeRoute = $activeRoute;
    }

    /**
     * Setter for active route.
     *
     * @param array $activeRoute The active and dispatched/processed route!
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Response_State Instance for chaining
     * @access public
     */
    public function activeRoute(array $activeRoute)
    {
        $this->setActiveRoute($activeRoute);
        return $this;
    }

    /**
     * Getter for active route.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The active route
     * @access public
     */
    public function getActiveRoute()
    {
        return $this->activeRoute;
    }

    /**
     * Setter for request.
     *
     * @param array $request The request in Doozr format
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setResponse(array $request)
    {
        $this->addHistory(__METHOD__, func_get_args());
        $this->request = $request;
    }

    /**
     * Setter for request.
     *
     * @param array $request The request in Doozr format
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Response_State Instance for chaining
     * @access public
     */
    public function request(array $request)
    {
        $this->setResponse($request);
        return $this;
    }

    /**
     * Getter for request.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The request
     * @access public
     */
    public function getResponse()
    {
        return $this->request;
    }

    /**
     * Setter for translation matrix.
     *
     * @param array $translationMatrix The translation matrix
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setTranslationMatrix(array $translationMatrix)
    {
        $this->addHistory(__METHOD__, func_get_args());
        $this->translationMatrix = $translationMatrix;
    }

    /**
     * Setter for translation matrix.
     *
     * @param array $translationMatrix The translation matrix
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Response_State Instance for chaining
     * @access public
     */
    public function translationMatrix(array $translationMatrix)
    {
        $this->setTranslationMatrix($translationMatrix);
        return $this;
    }

    /**
     * Getter for translation matrix.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The translation matrix
     * @access public
     */
    public function getTranslationMatrix()
    {
        return $this->translationMatrix;
    }

    /**
     * Setter for pattern.
     *
     * @param string $pattern The pattern (MVP) ...
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setPattern($pattern)
    {
        $this->addHistory(__METHOD__, func_get_args());
        $this->pattern = $pattern;
    }

    /**
     * Setter for pattern.
     *
     * @param string $pattern The pattern (MVP) ...
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Response_State Instance for chaining
     * @access public
     */
    public function pattern($pattern)
    {
        $this->setPattern($pattern);
        return $this;
    }

    /**
     * Getter for pattern.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The pattern
     * @access public
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Setter for rest.
     *
     * @param bool TRUE to mark state REST, otherwise FALSE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setRest($rest)
    {
        $this->addHistory(__METHOD__, func_get_args());
        $this->rest = $rest;
    }

    /**
     * Setter for rest.
     *
     * @param bool TRUE to mark state REST, otherwise FALSE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Response_State Instance for chaining
     * @access public
     */
    public function rest($rest)
    {
        $this->setPattern($rest);
        return $this;
    }

    /**
     * Getter for rest.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean The REST state
     * @access public
     */
    public function getRest()
    {
        return $this->rest;
    }

    /**
     * Sends "text/html" to client
     *
     * This method is intend to send "text/html" to client.
     *
     * @param string  $buffer  The data to send
     * @param string  $etag    The Etag to send, null to prevent sending
     * @param bool $exit    Close connection after output?
     * @param string  $charset The charset/encoding to use for sending (header-value)
     *
     * @return Doozr_Response_Web The current instance for chaining
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function sendHtml($buffer, $etag = null, $exit = true, $charset = null)
    {
        $registry = Doozr_Registry::getInstance();

        if (DOOZR_DEBUG === true) {
            $debugbar = $registry->getDebugbar();

            $archive = $registry->getLogger()->getArchive();

            foreach ($archive as $logentry) {
                if (!empty($logentry)) {
                    $debugbar['messages']->{$logentry['type']}($logentry['message']);
                }
            }

            $debugbar['time']->stopMeasure('request-cycle');
            $debugbarRenderer = $debugbar->getJavascriptRenderer();
            $debugbarRenderer->setBaseUrl('/assets');
            $buffer .= $debugbarRenderer->renderHead() . $debugbarRenderer->render();
        }

        // we send html
        header('Content-type: text/html; charset=UTF-8');

        // send the buffer/data
        echo $buffer;

        die;
    }
}
