<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Request - State.
 *
 * State.php - Request state used as immutable request state representation.
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
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Http.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Http/State.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Request/Route/State.php';

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Doozr - Request - State.
 *
 * Request state used as immutable request state representation.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 * @final
 */
final class Doozr_Request_State extends Doozr_Http_State
    implements
    ServerRequestInterface
{
    /**
     * The server ($_SERVER) params or similar implementation (array = key:value).
     * Should be filled on __construct.
     *
     * @var array
     */
    protected $serverParams = [];

    /**
     * The cookie ($_COOKIE) params or similar implementation (array = key:value).
     * Should be filled on __construct.
     *
     * @var array
     */
    protected $cookieParams = [];

    /**
     * The query parameters of this request. If any.
     *
     * @var array
     */
    protected $queryParams = [];

    /**
     * Normalized leaf tree representation of uploaded files.
     *
     * @var array
     */
    protected $uploadedFiles = [];

    /**
     * The parsed body.
     *
     * @var null
     */
    protected $parsedBody = null;

    /**
     * The attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * @var null|string
     */
    protected $requestTarget = self::DEFAULT_REQUEST_TARGET;

    /**
     * The uri.
     *
     * @var UriInterface
     */
    protected $uri;

    /**
     * The HTTP method used for request.
     * Defaults to GET.
     *
     * @var string
     */
    protected $method = Doozr_Http::REQUEST_METHOD_GET;

    /**
     * The default request target used when not specifically defined.
     *
     * @var string
     */
    const DEFAULT_REQUEST_TARGET = '/';

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __construct()
    {
        if (false === $uri = Doozr_Http::getUrl()) {
            throw new Doozr_Request_Exception(
                'Error retrieving URI for further request analysis!'
            );
        }

        $this
            ->uri(
                new Doozr_Request_Uri(
                    $uri
                )
            )
            ->serverParams(
                $_SERVER
            );
    }

    /*------------------------------------------------------------------------------------------------------------------
    | FULFILL: @see ServerRequestInterface
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Retrieve server parameters.
     *
     * Retrieves data related to the incoming request environment,
     * typically derived from PHP's $_SERVER superglobal. The data IS NOT
     * REQUIRED to originate from $_SERVER.
     *
     * @return array
     */
    public function getServerParams()
    {
        return $this->serverParams;
    }

    /**
     * Retrieve cookies.
     *
     * Retrieves cookies sent by the client to the server.
     *
     * The data MUST be compatible with the structure of the $_COOKIE superglobal.
     *
     * @return array
     */
    public function getCookieParams()
    {
        return $this->cookieParams;
    }

    /**
     * Return an instance with the specified cookies.
     *
     * The data IS NOT REQUIRED to come from the $_COOKIE superglobal, but MUST
     * be compatible with the structure of $_COOKIE. Typically, this data will
     * be injected at instantiation.
     *
     * This method MUST NOT update the related Cookie header of the request
     * instance, nor related values in the server params.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated cookie values.
     *
     * @param array $cookies Array of key/value pairs representing cookies.
     *
     * @return self
     */
    public function withCookieParams(array $cookies)
    {
        $this->setCookieParams($cookies);
    }

    /**
     * Retrieve query string arguments.
     *
     * Retrieves the deserialized query string arguments, if any.
     *
     * Note: the query params might not be in sync with the URI or server
     * params. If you need to ensure you are only getting the original
     * values, you may need to parse the query string from `getUri()->getQuery()`
     * or from the `QUERY_STRING` server param.
     *
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * Return an instance with the specified query string arguments.
     *
     * These values SHOULD remain immutable over the course of the incoming
     * request. They MAY be injected during instantiation, such as from PHP's
     * $_GET superglobal, or MAY be derived from some other value such as the
     * URI. In cases where the arguments are parsed from the URI, the data
     * MUST be compatible with what PHP's parse_str() would return for
     * purposes of how duplicate query parameters are handled, and how nested
     * sets are handled.
     *
     * Setting query string arguments MUST NOT change the URI stored by the
     * request, nor the values in the server params.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated query string arguments.
     *
     * @param array $query Array of query string arguments, typically from $_GET.
     *
     * @return self
     */
    public function withQueryParams(array $query)
    {
        $this->setQueryParams($query);
    }

    /**
     * Retrieve normalized file upload data.
     *
     * This method returns upload metadata in a normalized tree, with each leaf
     * an instance of Psr\Http\Message\UploadedFileInterface.
     *
     * These values MAY be prepared from $_FILES or the message body during
     * instantiation, or MAY be injected via withUploadedFiles().
     *
     * @return array An array tree of UploadedFileInterface instances;
     *               an empty array MUST be returned if no data is present.
     */
    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    /**
     * Create a new instance with the specified uploaded files.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     *
     * @param array $uploadedFiles An array tree of UploadedFileInterface instances.
     *
     * @return self
     *
     * @throws \InvalidArgumentException if an invalid structure is provided.
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $this->setUploadedFiles($uploadedFiles);
    }

    /**
     * Retrieve any parameters provided in the request body.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, this method MUST
     * return the contents of $_POST.
     *
     * Otherwise, this method may return any results of deserializing
     * the request body content; as parsing returns structured content, the
     * potential types MUST be arrays or objects only. A null value indicates
     * the absence of body content.
     *
     * @return null|array|object The deserialized body parameters, if any.
     *                           These will typically be an array or object.
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * Return an instance with the specified body parameters.
     *
     * These MAY be injected during instantiation.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, use this method
     * ONLY to inject the contents of $_POST.
     *
     * The data IS NOT REQUIRED to come from $_POST, but MUST be the results of
     * deserializing the request body content. Deserialization/parsing returns
     * structured data, and, as such, this method ONLY accepts arrays or objects,
     * or a null value if nothing was available to parse.
     *
     * As an example, if content negotiation determines that the request data
     * is a JSON payload, this method could be used to create a request
     * instance with the deserialized parameters.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     *
     * @param null|array|object $data The deserialized body data. This will
     *                                typically be in an array or object.
     *
     * @return self
     *
     * @throws \InvalidArgumentException if an unsupported argument type is
     *                                   provided.
     */
    public function withParsedBody($data)
    {
        $this->setParsedBody($data);
    }

    /**
     * Retrieve attributes derived from the request.
     *
     * The request "attributes" may be used to allow injection of any
     * parameters derived from the request: e.g., the results of path
     * match operations; the results of decrypting cookies; the results of
     * deserializing non-form-encoded message bodies; etc. Attributes
     * will be application and request specific, and CAN be mutable.
     *
     * @return array Attributes derived from the request.
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Retrieve a single derived request attribute.
     *
     * Retrieves a single derived request attribute as described in
     * getAttributes(). If the attribute has not been previously set, returns
     * the default value as provided.
     *
     * This method obviates the need for a hasAttribute() method, as it allows
     * specifying a default value to return if the attribute is not found.
     *
     * @see getAttributes()
     *
     * @param string $name    The attribute name.
     * @param mixed  $default Default value to return if the attribute does not exist.
     *
     * @return Doozr_Request_Route_State
     */
    public function getAttribute($name, $default = null)
    {
        $value      = $default;
        $attributes = $this->getAttributes();

        if (true === isset($attributes[$name])) {
            $value = $attributes[$name];
        }

        return $value;
    }

    /**
     * Return an instance with the specified derived request attribute.
     *
     * This method allows setting a single derived request attribute as
     * described in getAttributes().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated attribute.
     *
     * @see getAttributes()
     *
     * @param string $name  The attribute name.
     * @param mixed  $value The value of the attribute.
     *
     * @return self
     */
    public function withAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Return an instance that removes the specified derived request attribute.
     *
     * This method allows removing a single derived request attribute as
     * described in getAttributes().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the attribute.
     *
     * @see getAttributes()
     *
     * @param string $name The attribute name.
     *
     * @return self
     */
    public function withoutAttribute($name)
    {
        if (true === isset($this->attributes[$name])) {
            unset($this->attributes[$name]);
        }
    }

    /*------------------------------------------------------------------------------------------------------------------
    | FULFILL: @see RequestInterface
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for clients), as it appeared at request
     * (for servers), or as it was specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI, unless a value was provided to the concrete
     * implementation (see withRequestTarget() below).
     *
     * If no URI is available, and no request-target has been spec. provided, this method MUST return the string "/".
     *
     * @return string
     */
    public function getRequestTarget()
    {
        return $this->requestTarget;
    }

    /**
     * Return an instance with the specific request-target.
     *
     * If the request needs a non-origin-form request-target — e.g., for
     * specifying an absolute-form, authority-form, or asterisk-form —
     * this method may be used to create an instance with the specified
     * request-target, verbatim.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request target.
     *
     * @link http://tools.ietf.org/html/rfc7230#section-2.7 (for the various request-target forms allowed in request)
     *
     * @param mixed $requestTarget
     *
     * @return self
     */
    public function withRequestTarget($requestTarget)
    {
        $this->setRequestTarget($requestTarget);

        return $this;
    }

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Return an instance with the provided HTTP method.
     *
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request method.
     *
     * @param string $method Case-sensitive method.
     *
     * @return self
     *
     * @throws \InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod($method)
    {
        $this->setMethod($method);
    }

    /**
     * Retrieves the URI instance.
     *
     * This method MUST return a UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     *
     * @return Doozr_Request_Uri Returns a UriInterface instance
     *                           representing the URI of the request.
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Returns an instance with the provided URI.
     *
     * This method MUST update the Host header of the returned request by
     * default if the URI contains a host component. If the URI does not
     * contain a host component, any pre-existing Host header MUST be carried
     * over to the returned request.
     *
     * You can opt-in to preserving the original state of the Host header by
     * setting `$preserveHost` to `true`. When `$preserveHost` is set to
     * `true`, this method interacts with the Host header in the following ways:
     *
     * - If the the Host header is missing or empty, and the new URI contains
     *   a host component, this method MUST update the Host header in the returned
     *   request.
     * - If the Host header is missing or empty, and the new URI does not contain a
     *   host component, this method MUST NOT update the Host header in the returned
     *   request.
     * - If a Host header is present and non-empty, this method MUST NOT update
     *   the Host header in the returned request.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     *
     * @param UriInterface $uri          New request URI to use.
     * @param bool         $preserveHost Preserve the original state of the Host header.
     *
     * @return self
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        if (false === $preserveHost) {
            $host = $uri->getHost();

            if (null !== $host) {
                $this->withHeader('Host', $host);
            }
        } else {
            $hostHeader = $this->getHeader('host');
            if (null === $hostHeader) {
                $host = $uri->getHost();
                $this->withHeader('host', $host);
            }
        }
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Set server params.
     *
     * @param array $serverParams The server params to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function withServerParams(array $serverParams)
    {
        $this->setServerParams($serverParams);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for serverParams.
     *
     * @param array $serverParams The server params to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setServerParams(array $serverParams)
    {
        $this->serverParams = $serverParams;
    }

    /**
     * Fluent: Setter for serverParams.
     *
     * @param array $serverParams The server params to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function serverParams(array $serverParams)
    {
        $this->setServerParams($serverParams);

        return $this;
    }

    /**
     * Setter for cookieParams.
     *
     * @param array $cookieParams The cookie params to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setCookieParams(array $cookieParams)
    {
        $this->cookieParams = $cookieParams;
    }

    /**
     * Fluent: Setter for cookieParams.
     *
     * @param array $cookieParams The server params to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function cookieParams(array $cookieParams)
    {
        $this->setCookieParams($cookieParams);

        return $this;
    }

    /**
     * Setter for queryParams.
     *
     * @param array $queryParams The query params to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setQueryParams(array $queryParams)
    {
        $this->queryParams = $queryParams;
    }

    /**
     * Fluent: Setter for queryParams.
     *
     * @param array $queryParams The server params to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function queryParams(array $queryParams)
    {
        $this->setQueryParams($queryParams);

        return $this;
    }

    /**
     * Setter for uploadedFiles.
     *
     * @param array $uploadedFiles The uploaded files to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setUploadedFiles(array $uploadedFiles)
    {
        $this->uploadedFiles = $uploadedFiles;
    }

    /**
     * Fluent: Setter for uploadedFiles.
     *
     * @param array $uploadedFiles The uploaded files to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function uploadedFiles(array $uploadedFiles)
    {
        $this->setUploadedFiles($uploadedFiles);

        return $this;
    }

    /**
     * Setter for parsedBody.
     *
     * @param null|array|object $parsedBody The deserialized body data.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setParsedBody($parsedBody)
    {
        $this->parsedBody = $parsedBody;
    }

    /**
     * Fluent: Setter for parsedBody.
     *
     * @param null|array|object $parsedBody The deserialized body data.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function parsedBody($parsedBody)
    {
        $this->setParsedBody($parsedBody);

        return $this;
    }

    /**
     * Setter for attributes.
     *
     * @param array $attributes Attributes to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Fluent: Setter for attributes.
     *
     * @param array $attributes Attributes to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function attributes(array $attributes)
    {
        $this->setAttributes($attributes);

        return $this;
    }

    /**
     * Setter for requestTarget.
     *
     * @param string $requestTarget requestTarget to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setRequestTarget($requestTarget)
    {
        $this->requestTarget = $requestTarget;
    }

    /**
     * Fluent: Setter for requestTarget.
     *
     * @param string $requestTarget requestTarget to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function requestTarget($requestTarget)
    {
        $this->setRequestTarget($requestTarget);

        return $this;
    }

    /**
     * Setter for method.
     *
     * @param string $method method to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Fluent: Setter for method.
     *
     * @param string $method method to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function method($method)
    {
        $this->setMethod($method);

        return $this;
    }

    /**
     * Setter for uri.
     *
     * @param UriInterface $uri uri to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * Fluent: Setter for uri.
     *
     * @param UriInterface $uri uri to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function uri($uri)
    {
        $this->setUri($uri);

        return $this;
    }
}
