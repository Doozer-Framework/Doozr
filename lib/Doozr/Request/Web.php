<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Request - Web
 *
 * Web.php - Doozr request implementation for Web (HTTP) response(s).
 *
 * PHP versions 5.5
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
 * @package    Doozr_Request
 * @subpackage Doozr_Request_Web
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Request.php';

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Doozr - Request - Web
 *
 * Doozr request implementation for Web (HTTP) response(s).
 *
 * @category   Doozr
 * @package    Doozr_Request
 * @subpackage Doozr_Request_Web
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Request_Web extends Doozr_Request
    implements
    ServerRequestInterface
{
    /**
     * Type of the Response
     *
     * @example Cli, Web, ...
     * @var string
     * @access protected
     */
    protected $type = Doozr_Kernel::RUNTIME_ENVIRONMENT_WEB;

    /**
     * The request sources valid for active running runtimeEnvironment.
     *
     * @var array
     * @access protected
     */
    protected $requestSources;

    /**
     * The type native for PHP request sources
     *
     * @var int
     * @access const
     */
    const NATIVE = 0;

    /**
     * The type emulated for PHP request sources
     *
     * @var int
     * @access const
     */
    const EMULATED = 1;

    /*------------------------------------------------------------------------------------------------------------------
    | FULFILL: @see Doozr_Request_Interface
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Receive method.
     *
     * @param string $sapi The sapi to use for receiving values and settings.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success, otherwise FALSE
     * @access public
     */
    public function receive($sapi = PHP_SAPI)
    {
        // Set valid request sources
        $this->setRequestSources(
            $this->emitValidRequestSources(
                DOOZR_RUNTIME_ENVIRONMENT
            )
        );

        // Emulate the request in case of PUT ...
        $this->emulateRequest($this->receiveMethod());

        // Store SAPI (CLI, HTTPD, APACHE ....)
        #$this->setSapi($sapi);

        // Store headers normalized to prevent System/OS/PHP mismatches
        $headers = $this->normalizeHeaders(getallheaders());
        foreach ($headers as $header => $value) {
            $this->withHeader($header, $value);
        }

        // Store query params as array
        $this->withQueryParams(
            $this->receiveQueryParams($this->getMethod())
        );
    }

    /*------------------------------------------------------------------------------------------------------------------
    | SETTER & GETTER & ISSER & HASSER
    +-----------------------------------------------------------------------------------------------------------------*/

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
     * Fluent: Setter for request sources.
     *
     * @param array $requestSources The request sources to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
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

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

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
        $normalized = [];

        foreach ($headers as $header => $value) {
            $normalized[str_replace('-', '_', strtoupper($header))] = $value;
        }

        return $normalized;
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
            case Doozr_Kernel::RUNTIME_ENVIRONMENT_CLI:
                $requestSources = array_merge(
                    $requestSources,
                    array(
                        'CLI' => self::NATIVE
                    )
                );
                break;

            case Doozr_Kernel::RUNTIME_ENVIRONMENT_WEB:
            case Doozr_Kernel::RUNTIME_ENVIRONMENT_HTTPD:
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
     * Returns the query parameter for a HTTP method as array.
     *
     * @param string $method The HTTP method to receive query parameter for.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The query parameter
     * @access public
     */
    protected function receiveQueryParams($method = Doozr_Http::REQUEST_METHOD_GET)
    {
        global $_GET, $_POST, $_DELETE, $_PUT;

        // Retrieve parameter/arguments by method
        switch ($method) {

            case Doozr_Http::REQUEST_METHOD_GET:
                /* @var $arguments $_GET */
                $arguments = $_GET;
                break;

            case Doozr_Http::REQUEST_METHOD_POST:
                /* @var $arguments $_POST */
                $arguments = $_POST;
                break;

            case Doozr_Http::REQUEST_METHOD_PUT:
                /* @var $arguments $_PUT */
                $arguments = $_PUT;
                break;

            case Doozr_Http::REQUEST_METHOD_DELETE:
                /* @var $arguments $_DELETE */
                $arguments = $_DELETE;
                break;

            default:
                $arguments = [];
        }

        return $arguments;
    }

    /**
     * This method emulates those requests which are not implemented in PHP's global by default.
     * So you can access PUT via $_PUT and DELETE via $_DELETE and so on ...
     *
     * @param string $method The active HTTP method
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success, otherwise FALSE
     * @access protected
     */
    protected function emulateRequest($method)
    {
        global $_PUT, $_DELETE, $_REQUEST;

        $headers        = $this->getHeaders();
        $requestSources = $this->getRequestSources();

        // Check if current request type must be emulated OR
        // If we reach here cause of a POST request without header content-type application/x-www-form-urlencoded
        if (
            (self::EMULATED === $requestSources[$method]) ||
            (
                Doozr_Http::REQUEST_METHOD_POST === $method &&
                (
                    (isset($headers['CONTENT_TYPE']) === false) ||
                    (stristr(strtolower($headers['CONTENT_TYPE']), 'application/x-www-form-urlencoded') === false)
                )
            )
        ) {
            echo 'EMULATED!!!!!!!!';die;
            //$GLOBALS['_' . $method]['DOOZR_REQUEST_BODY'] = file_get_contents("php://input");
            // So we @ Doozr decided that we equalize the accessibility of arguments passed to a PHP process.
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
     * Returns the method (POST / GET / PUT ... || CLI) of the current processed request.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string the method of current processed request (GET / POST / PUT ... || CLI)
     * @access public
     */
    public function receiveMethod()
    {
        if ($requestMethod = (isset($_SERVER['REQUEST_METHOD'])) ? $_SERVER['REQUEST_METHOD'] : null) {
            $requestMethod = strtoupper($requestMethod);
        }

        return $requestMethod;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | FULFILL: @see RequestInterface
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * {@inheritdoc}
     */
    public function getServerParams()
    {
        return $this->getStateObject()->getServerParams();
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieParams()
    {
        return $this->getStateObject()->getCookieParams();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this Instance for chaining
     */
    public function withCookieParams(array $cookies)
    {
        $this->getStateObject()->withCookieParams($cookies);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryParams()
    {
        return $this->getStateObject()->getQueryParams();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this Instance for chaining
     */
    public function withQueryParams(array $query)
    {
        $this->getStateObject()->withQueryParams($query);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUploadedFiles()
    {
        return $this->getStateObject()->getUploadedFiles();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this Instance for chaining
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        return $this->getStateObject()->withUploadedFiles($uploadedFiles);
    }

    /**
     * {@inheritdoc}
     */
    public function getParsedBody()
    {
        return $this->getStateObject()->getParsedBody();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this Instance for chaining
     */
    public function withParsedBody($data)
    {
        $this->getStateObject()->withParsedBody($data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->getStateObject()->getAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($name, $default = null)
    {
        return $this->getStateObject()->getAttribute($name, $default);
    }

    /**
     * {@inheritdoc}
     *
     * @return $this Instance for chaining
     */
    public function withAttribute($name, $value)
    {
        $this->getStateObject()->withAttribute($name, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this Instance for chaining
     */
    public function withoutAttribute($name)
    {
        $this->getStateObject()->withoutAttribute($name);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this Instance for chaining
     */
    public function getRequestTarget()
    {
        return $this->getStateObject()->getRequestTarget();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this Instance for chaining
     */
    public function withRequestTarget($requestTarget)
    {
        $this->getStateObject()->withRequestTarget($requestTarget);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->getStateObject()->getMethod();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this Instance for chaining
     */
    public function withMethod($method)
    {
        $this->getStateObject()->withMethod($method);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUri()
    {
        return $this->getStateObject()->getUri();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this Instance for chaining
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $this->getStateObject()->withUri($uri, $preserveHost);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion()
    {
        return $this->getStateObject()->getProtocolVersion();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this Instance for chaining
     */
    public function withProtocolVersion($version)
    {
        $this->getStateObject()->withProtocolVersion($version);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->getStateObject()->getHeaders();
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($name)
    {
        return $this->getStateObject()->getHeader($name);
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader($name)
    {
        return $this->getStateObject()->hasHeader($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderLine($name)
    {
        return $this->getStateObject()->getHeaderLine($name);
    }

    /**
     * {@inheritdoc}
     *
     * @return $this Instance for chaining
     */
    public function withHeader($name, $value)
    {
        $this->getStateObject()->withHeader($name, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this Instance for chaining
     */
    public function withAddedHeader($name, $value)
    {
        $this->getStateObject()->withAddedHeader($name, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this Instance for chaining
     */
    public function withoutHeader($name)
    {
        $this->getStateObject()->withoutHeader($name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->getStateObject()->getBody();
    }

    /**
     * {@inheritdoc}
     */
    public function withBody(StreamInterface $body)
    {
        $this->getStateObject()->withBody($body);

        return $this;
    }
}
