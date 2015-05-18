<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Base - Rest - Exception
 *
 * Exception.php - Exception for REST in general as base for M,V,P ...
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
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Rest
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Exception.php';

/**
 * Doozr - Base - Rest - Exception
 *
 * Exception for REST in general as base for M,V,P ...
 *
 * @category   Doozr
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Rest
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Base_Rest_Exception extends Doozr_Base_Exception
{
    /**
     * The token of the request to manage exchange for failed API calls.
     *
     * @var array
     * @access public
     */
    public $token = null;

    /**
     * The error(s) of the request for an detailed error response to provide
     * good data for frontend(s).
     *
     * @var array
     * @access public
     */
    public $error = array();


    /**
     * Constructor.
     *
     * @param string         $message  The exception-message
     * @param int        $code     The code of the exception
     * @param Exception|null $previous The previous exception thrown - AS_OF: PHP 5.3 introduced !
     * @param array          $token    A new token for exchange with client.
     * @param array          $error    An array containing detailed errors for each submitted field with validation set!
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \Doozr_Base_Rest_Exception
     * @access public
     */
    public function __construct(
              $message  = null,
              $code     = 0,
              $previous = null,
        array $token    = null,
        array $error    = null
    ) {
        // Store error & token in this layer is a REST API thing!
        $this->setToken($token);
        $this->setError($error);

        // call parents constructor
        parent::__construct($message, $code, $previous);
    }

    /**
     * Setter for token.
     *
     * @param array $token The token to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setToken(array $token = null) {
        $this->token = $token;
    }

    /**
     * Setter for token.
     *
     * @param array $token The token to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function token(array $token) {
        $this->setToken($token);
        return $this;
    }

    /**
     * Getter for token.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array|null The token if set, otherwise NULL
     * @access public
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * Setter for error.
     *
     * @param array $error The error(s) to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setError(array $error = null)
    {
        $this->error = $error;
    }

    /**
     * Setter for error.
     *
     * @param array $error The error(s) to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function error(array $error)
    {
        $this->setError($error);
        return $this;
    }

    /**
     * Getter for error.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The error(s) if set, otherwise empty
     * @access public
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Exports current state as json for API responses.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The response for the API
     * @access public
     */
    public function toJson()
    {
        $response = new Doozr_Base_Response_Rest();
        $response
            ->status(Doozr_Base_Response_Rest::STATUS_ERROR)
            ->message($this->getMessage())
            ->data(array('error' => $this->getError()));

        $data = $response->getData();

        // Check token exchange required then inject
        if ($this->getToken() !== null) {
            $data['security'] = array('token' => $this->getToken());
        }

        // Now here is the interesting part in this logic ...
        if (defined('DOOZR_DEBUGGING') === true && DOOZR_DEBUGGING === true) {
            // add debug information!
            $data['meta'] = array(
                'message' => $this->getMessage(),
                'code' => $this->getCode(),
                'file' => $this->getFile(),
                'line' => $this->getLine(),
            );
        }

        $response->setData($data);

        return $response->toJson();
    }
}
