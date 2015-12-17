<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Http - State
 *
 * State.php - Http state used as immutable http state representation.
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
 * @package    Doozr_Http
 * @subpackage Doozr_Http_State
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Http.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/State.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/State/Interface.php';

use Psr\Http\Message\StreamInterface;

/**
 * Doozr - Http - State
 *
 * Http state used as immutable http state representation.
 *
 * @category   Doozr
 * @package    Doozr_Http
 * @subpackage Doozr_Http_State
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Http_State extends Doozr_Base_State
    implements
    Doozr_Base_State_Interface
{
    /**
     * The headers.
     *
     * @var array
     * @access protected
     */
    protected $headers = [];

    /**
     * The shadow reference by.
     *
     * @var array
     * @access protected
     */
    protected $shadow = [];

    /**
     * Stream for body.
     *
     * @var StreamInterface
     * @access protected
     */
    protected $body;

    /**
     * The protocol version.
     *
     * @var string
     * @access protected
     */
    protected $protocolVersion = self::DEFAULT_PROTOCOL_VERSION;

    /**
     * The default request target used when not specifically defined.
     *
     * @var string
     * @access public
     */
    const DEFAULT_PROTOCOL_VERSION = Doozr_Http::VERSION_1_1;

    /**
     * Prefix for HTTP version e.g. used for building header(s).
     *
     * @var string
     * @access public
     */
    const DEFAULT_PROTOCOL_PREFIX = 'HTTP/';

    /**
     * Setter for headers.
     *
     * @param array $headers The headers.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * Fluent: Setter for headers.
     *
     * @param array $headers The headers.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function headers($headers)
    {
        $this->headers = $headers;
    }

    /**
     * Getter for headers.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The headers
     * @access public
     */
    public function getHeaders()
    {
        $result = [];

        foreach ($this->shadow as $reference => $index) {
            $result[$index] = $this->headers[$reference];
        }

        return $result;
    }

    /**
     * Returns TRUE if header is set, otherwise FALSE.
     *
     * @param string $name The name of the header to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if header is set, otherwise FALSE
     * @access public
     */
    public function hasHeader($name)
    {
        $internalName = strtolower($name);
        return (true === isset($this->headers[$internalName]));
    }

    /**
     * Setter for header.
     *
     * @param string       $name  The header
     * @param string|array $value The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setHeader($name, $value)
    {
        $internalName = strtolower($name);

        if (true === is_array($value)) {
            $this->headers[$internalName] = $value;
        } else {
            $this->headers[$internalName] = array($value);
        }
    }

    /**
     * Fluent: Setter for header.
     *
     * @param string       $name  The header
     * @param string|array $value The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function header($name, $value)
    {
        $this->setHeader($name, $value);

        return $this;
    }

    /**
     * Getter for header.
     *
     * @param string $name The key to return header(s) for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array|null The header if set
     * @access public
     */
    public function getHeader($name)
    {
        $internalName = strtolower($name);

        if (true === $this->hasHeader($internalName)) {
            $value = $this->headers[$internalName];

        } else {
            $value = null;
        }

        return $value;
    }

    /**
     * Getter for header line.
     *
     * @param string $name The name of the heade to return line for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The header line if set
     * @access public
     */
    public function getHeaderLine($name)
    {
        $internalName = strtolower($name);
        return (true === isset($this->headers[$internalName])) ? implode(',', $this->headers[$internalName]) : '';
    }

    /**
     * Return an instance without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the named header.
     *
     * @param string $name Case-insensitive header field name to remove.
     *
     * @return self
     */
    public function withoutHeader($name)
    {
        $internalName = strtolower($name);

        if (true === $this->hasHeader($internalName)) {
            unset($this->headers[$internalName]);
            unset($this->shadow[$internalName]);
        }

        return $this;
    }

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new header and/or value.
     *
     * @param string          $name  Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     *
     * @return self
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withAddedHeader($name, $value)
    {
        $internalName = strtolower($name);

        if (true === $this->hasHeader($internalName)) {
            $header   = $this->getHeader($internalName);
            $header[] = $value;
            $this->setHeader($internalName, $header);

        } else {
            $this->setHeader($internalName, $value);

        }

        $this->setShadow($internalName, $name);

        return $this;
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * While header names are case-insensitive, the casing of the header will
     * be preserved by this function, and returned from getHeaders().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated header and value.
     *
     * @param string          $name  Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     *
     * @return self
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withHeader($name, $value)
    {
        $internalName = strtolower($name);

        $this->setHeader($internalName, $value);
        $this->setShadow($internalName, $name);
    }

    /**
     * Setter for body
     *
     * @param StreamInterface $body
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setBody(StreamInterface $body)
    {
        $this->body = $body;
    }

    /**
     * Fluent: Setter for body
     *
     * @param StreamInterface $body
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function body(StreamInterface $body)
    {
        $this->setBody($body);

        return $this;
    }

    /**
     * Gets the body of the message.
     *
     * @return Doozr_Response_Body Returns the body as a stream.
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Return an instance with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     * @param StreamInterface $body Body.
     *
     * @return self
     * @throws \InvalidArgumentException When the body is not valid.
     */
    public function withBody(StreamInterface $body)
    {
        $this->setBody($body);

        return $this;
    }

    /**
     * Getter for data.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Response_Body Data stored
     * @access public
     */
    public function getData()
    {
        return $this->getBody();
    }

    /**
     * Getter for protocol version.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The protocol version if set
     * @access public
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * Setter for protocol version.
     *
     * @param string $protocolVersion The protocol version.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function withProtocolVersion($protocolVersion)
    {
        $this->setProtocolVersion($protocolVersion);
    }

    /**
     * Setter for protocol version.
     *
     * @param string $protocolVersion The protocol version.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setProtocolVersion($protocolVersion)
    {
        $this->protocolVersion = $protocolVersion;
    }

    /**
     * Fluent: Setter for protocol version.
     *
     * @param string $protocolVersion The protocol version.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function protocolVersion($protocolVersion)
    {
        $this->protocolVersion = $protocolVersion;
    }

    /**
     * Returns ready to use protocol string.
     *
     * @example HTTP/1.1
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The protocol version as string
     * @access public
     */
    public function getProtocolLine()
    {
        return self::DEFAULT_PROTOCOL_PREFIX . $this->getProtocolVersion();
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for shadow.
     *
     * @param string $name  The name of the shadow entry.
     * @param mixed  $value The value of the shadow entry.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setShadow($name, $value)
    {
        $this->shadow[$name] = $value;
    }

    /**
     * Fluent: Setter for shadow.
     *
     * @param string $name  The name of the shadow entry.
     * @param mixed  $value The value of the shadow entry.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining.
     * @access protected
     */
    protected function shadow($name, $value)
    {
        $this->setShadow($name, $value);

        return $this;
    }

    /**
     * Getter for shadow.
     *
     * @param string $name The name of the shadow entry.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The value of the shadow entry
     * @access protected
     * @throws Doozr_Exception_Http
     */
    protected function getShadow($name)
    {
        if (false === isset($this->shadow[$name])) {
            throw new Doozr_Exception_Http(
                500, sprintf('No shadow entry for "%s"', $name)
            );
        }

        return $this->shadow[$name];
    }
}
