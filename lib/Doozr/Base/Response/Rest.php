<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Base - Response - Rest
 *
 * Rest.php - Response Model for default REST responses. This implements the
 * JSend API pattern and supports the complete signing of responses using JWT.
 *
 * A toolset which is useful while developing classes which give you features like
 * ...
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
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Response
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Class.php';

/**
 * Doozr - Base - Response - Rest
 *
 * Response Model for default REST responses. This implements the
 * JSend API pattern and supports the complete signing of responses using JWT.
 *
 * @category   Doozr
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Response
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Base_Response_Rest extends Doozr_Base_Class
{
    /**
     * The status of this response.
     * As described in JSend we use "success", "error" and/or "fail"
     *
     * @var string
     * @access protected
     */
    protected $status;

    /**
     * The data to return.
     *
     * @var array
     * @access protected
     */
    protected $data;

    /**
     * The message according to the last operations result.
     *
     * @var string
     * @access protected
     */
    protected $message;

    /**
     * The code of the response often used for error codes or response codes
     *
     * @var string
     * @access protected
     */
    protected $code;

    /**
     * States this instance can handle/have.
     *
     * @var string
     */
    const STATUS_SUCCESS  = 'success';
    const STATUS_ERROR    = 'error';
    const STATUS_FAIL     = 'fail';

    const ELEMENT_STATUS  = 'status';
    const ELEMENT_MESSAGE = 'message';
    const ELEMENT_CODE    = 'code';
    const ELEMENT_DATA    = 'data';

    protected $responseStructure = array(
        self::ELEMENT_STATUS,
        self::ELEMENT_MESSAGE,
        self::ELEMENT_CODE,
        self::ELEMENT_DATA,
    );


    /**
     * Setter for status.
     *
     * @param int $status The HTTP Status of current operation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Base_Model_Rest Instance for chaining
     * @access public
     * @throws Doozr_Base_Response_Rest_Exception
     */
    public function setStatus($status)
    {
        if (
            !in_array(
                $status,
                array(
                    self::STATUS_SUCCESS,
                    self::STATUS_ERROR,
                    self::STATUS_FAIL,
                )
            )
        ) {
            throw new Doozr_Base_Response_Rest_Exception(

            );
        }

        $this->status = $status;
    }

    /**
     * Setter for status.
     *
     * @param int $status The HTTP Status of current operation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function status($status)
    {
        $this->setStatus($status);
        return $this;
    }

    /**
     * Getter for status.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The HTTP Status of current operation
     * @access public
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Setter for data.
     *
     * @param array $data The data to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Setter for data.
     *
     * @param array $data The data to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function data($data)
    {
        $this->setData($data);
        return $this;
    }

    /**
     * Getter for data.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array|null Data
     * @access public
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Setter for message.
     *
     * @param string $message The message to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Setter for message.
     *
     * @param array $message The message to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function message($message)
    {
        $this->setMessage($message);
        return $this;
    }

    /**
     * Getter for message.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null Message
     * @access public
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Setter for code.
     *
     * @param string $code The code to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Setter for code.
     *
     * @param string $code The code to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function code($code)
    {
        $this->setCode($code);
        return $this;
    }

    /**
     * Getter for code.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null Code
     * @access public
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Generic export of "known" class properties (order above in def).
     * Returns the current state es json string.
     *
     * @param bool $encode TRUE to json_encode result, FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The JSON response structure as string
     * @access public
     */
    public function toJson($encode = true)
    {
        $response = [];

        foreach ($this->responseStructure as $node) {
            $value = call_user_func(array($this, 'get' . ucfirst($node)));
            if ($value !== null) {
                $response[$node] = $value;
            }
        }

        // Our artificial json response build from inline data
        return ($encode === true) ? json_encode($response) : $response;
    }
}
