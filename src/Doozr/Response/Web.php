<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Response - Web.
 *
 * Web.php - Doozr response implementation for Web (HTTP) response(s).
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
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Kernel.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Response.php';

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Doozr - Response - Web.
 *
 * Doozr response implementation for Web (HTTP) response(s).
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
class Doozr_Response_Web extends Doozr_Response
    implements
    ResponseInterface
{
    /**
     * Type of response.
     *
     * @example Httpd, Web, ...
     *
     * @var string
     */
    protected $type = Doozr_Kernel::RUNTIME_ENVIRONMENT_WEB;

    /*------------------------------------------------------------------------------------------------------------------
    | FULFILL: @see ResponseInterface
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->getStateObject()->getStatusCode();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this Instance for chaining
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $this->getStateObject()->setStatusCode($code);
        $this->getStateObject()->setReasonPhrase($reasonPhrase);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase()
    {
        if ('' === $reasonPhrase = $this->getStateObject()->getReasonPhrase()) {
            $status = 'REASONPHRASE_'.$this->getStatusCode();
            $reasonPhrase = constant('Doozr_Http::'.$status);
        }

        return $reasonPhrase;
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
    public function getProtocolVersion()
    {
        return $this->getStateObject()->getProtocolVersion();
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
    public function hasHeader($name)
    {
        return $this->getStateObject()->hasHeader($name);
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
     *
     * @return $this Instance for chaining
     */
    public function withBody(StreamInterface $body)
    {
        $this->getStateObject()->withBody($body);

        return $this;
    }

    /**
     * Getter for data.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Response_Body The data if set, otherwise NULL
     */
    public function getData()
    {
        return $this->getStateObject()->getData();
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns ready to use protocol line.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The protocol line as string
     */
    protected function getProtocolLine()
    {
        return $this->getStateObject()->getProtocolLine();
    }
}
