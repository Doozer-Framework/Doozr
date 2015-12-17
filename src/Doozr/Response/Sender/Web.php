<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Response - Sender - Web
 *
 * Web.php - Sends a HTTP response to the client.
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
 * @package    Doozr_Response
 * @subpackage Doozr_Response_Sender
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr\Base\Class.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\StreamInterface as Stream;

/**
 * Doozr - Response - Sender - Web
 *
 * Sends a HTTP response to the client.
 *
 * @category   Doozr
 * @package    Doozr_Response
 * @subpackage Doozr_Response_Sender
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Response_Sender_Web extends Doozr_Base_Class
{
    /**
     * Instance of Doozr_Response_X-Class compatible to PSR-7 Response
     *
     * @var Response
     * @access protected
     */
    protected $response;

    /**
     * The output pipe identifier (e.g. php://stdout).
     *
     * @var string
     * @access protected
     */
    protected $output;

    /**
     * Prefix for HTTP version e.g. used for building header(s).
     *
     * @var string
     * @access public
     */
    const DEFAULT_PROTOCOL_PREFIX = 'HTTP/';

    /**
     * Default output pipeline.
     *
     * @var string
     * @access public
     */
    const DEFAULT_OUTPUT = 'php://stdout';

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param Psr\Http\Message\ResponseInterface $response A complete response which will be send.
     * @param string                             $output   The output pipe
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     */
    public function __construct(Response $response = null, $output = self::DEFAULT_OUTPUT)
    {
        $this
            ->response($response)
            ->output($output);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for output.
     *
     * @param string $output The output to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * Fluent: Setter for output.
     *
     * @param string $output The output to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function output($output)
    {
        $this->setOutput($output);

        return $this;
    }

    /**
     * Getter for output.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The output pipe identifier
     * @access protected
     */
    protected function getOutput()
    {
        return $this->output;
    }

    /**
     * Setter for response.
     *
     * @param Psr\Http\Message\ResponseInterface $response The response to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Fluent: Setter for response.
     *
     * @param Psr\Http\Message\ResponseInterface $response The response to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function response(Response $response)
    {
        $this->setResponse($response);

        return $this;
    }

    /**
     * Getter for response.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Psr\Http\Message\ResponseInterface
     * @access protected
     */
    protected function getResponse()
    {
        return $this->response;
    }

    /**
     * Returns ready to use protocol string.
     *
     * @example HTTP/1.1
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The protocol version as string
     * @access protected
     */
    protected function getProtocolLine()
    {
        return self::DEFAULT_PROTOCOL_PREFIX . $this->getResponse()->getProtocolVersion();
    }

    /**
     * Sends Header(s).
     *
     * @param Psr\Http\Message\ResponseInterface $response The response to send headers from.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function sendHeaders(Response $response)
    {
        // Send HTTP status & reason-phrase
        header(
            sprintf(
                '%s %s %s',
                $this->getProtocolLine(),
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ),
            true,
            $response->getStatusCode()
        );

        // Send headers
        $headers = $response->getHeaders();
        foreach ($headers as $name => $valueCollection) {
            foreach ($valueCollection as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }
    }

    /**
     * Sends Body.
     *
     * @param Psr\Http\Message\StreamInterface $body The body to send as stream
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function sendBody(Stream $body)
    {
        // I don't trust that this will be at the beginning of the stream, so reset.
        $body->rewind();

        // writing to an arbitrary stream.
        // @todo Use stream operations to make this more robust and allow
        $bytes = 0;
        if ($bytes = $body->getSize() && $bytes < 500) {
            print $body->getContents();

        } else {
            while (!$body->eof()) {
                $data = $body->read(1024);
                print $data;
            }
        }
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Sends the response to the client.
     *
     * @param bool $exit TRUE to exit after sending
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function send($exit = true)
    {
        $response = $this->getResponse();

        $this->sendHeaders($response);
        $this->sendBody($response->getBody());

        if (true === $exit) {
            exit;
        }
    }
}
