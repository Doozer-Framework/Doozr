<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Base View Rest
 *
 * Rest.php - Base View Rest of the DoozR Framework.
 *
 * PHP versions 5.4
 *
 * LICENSE:
 * DoozR - The lightweight PHP-Framework for high-performance websites
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
 * Please feel free to contact us via e-mail: <opensource@clickalicious.de>
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_View
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/View.php';
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/View/Interface.php';

/**
 * DoozR - Base View Rest
 *
 * Base View Rest of the DoozR Framework.
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_View
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Base_View_Rest extends DoozR_Base_View
{
    /**
     * Default REST API response header(s)
     *
     * @var array
     * @access protected
     */
    protected $defaultApiResponseHeader = array(
        'X-Rate-Limit-Limit'     => 0,                  // The number of allowed requests in the current period
        'X-Rate-Limit-Remaining' => 0,                  // The number of remaining requests in the current period
        'X-Rate-Limit-Reset'     => 0,                  // The number of seconds left in the current period
    );


    /**
     * This method is the magic renderer von View = Main.
     * Upon creating this method gets automagically called when data
     * is set to view via setData(). And we always deliver JSON so
     * its really simple.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __renderMain()
    {
        // Send Header
        $this->sendHeader();

        // Send Data
        $this->sendData();
    }

    /**
     * Setter for defaultApiResponseHeader
     *
     * @param array $defaultApiResponseHeader
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setDefaultApiResponseHeader(array $defaultApiResponseHeader = array())
    {
        $this->defaultApiResponseHeader = $defaultApiResponseHeader;
    }

    /**
     * Getter for defaultApiResponseHeader.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Containing default API Response Header
     * @access public
     */
    public function getDefaultApiResponseHeader()
    {
        return $this->defaultApiResponseHeader;
    }

    /**
     * Brings the input in a normalized (string) form ready to send.
     *
     * @example Will convert 0 => array('content-type' => 'xhtml') TO 'content-type: xhtml'
     *
     * @param array $headers The headers to normalize
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Containing default API Response Header
     * @access public
     */
    protected function normalizeHeaders(array $headers)
    {
        $responseHeaders = array();

        foreach ($headers as $header => $value) {
            $key = md5(strtolower($header));
            $responseHeaders[$key] = $header . ':' . (($value !== null) ? $value : '');
        }

        return $responseHeaders;
    }

    /**
     * Sends header of the API to the client. Those header containing the API-Version
     * and some more fields.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @throws DoozR_Exception
     */
    protected function sendHeader()
    {
        /* @var $response DoozR_Response_Web */
        $response = $this->front->getResponse();

        // Custom default header configured?
        try {
            $headers = object_to_array(
                $this->configuration->transmission->header->api->rest
            );

        } catch (Exception $e) {
            $headers = array();
        }

        // add our REST API Header set ... $headers
        $headers = $this->normalizeHeaders(
            merge_array($this->getDefaultApiResponseHeader(), $headers)
        );

        // Send configured header
        foreach ($headers as $header) {
            $response->sendHeader($header);
        }
    }

    /**
     * This method (container) is intend to return the data for a requested runtimeEnvironment.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Base_Response_Rest
     * @access public
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Sends header of the API to the client. Those header containing the API-Version
     * and some more fields.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @throws DoozR_Exception
     */
    protected function sendData()
    {
        // So we assume that ...
        /* @var $response DoozR_Response_Web */
        $response = $this->front->getResponse();

        // Send our data as JSON through response
        $response->sendJson(
            $this->getData()->toJson(false),
            $this->generateFingerprint(1),
            null,
            false,
            false,
            true,
            $this->getData()->getCode()
        );
    }

    /**
     * Maybe a bit spooky but a good solution to get data into this part of the MVP structure.
     *
     * @param SplSubject $subject The subject which is automatically dispatched
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function __update(SplSubject $subject)
    {
        // store data internal and call renderer!
        $this->setData($subject->getData(), true);
    }
}
