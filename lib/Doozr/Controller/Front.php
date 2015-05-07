<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Controller - Front
 *
 * Front.php - The Front-Controller of the Doozr-Framework.
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
 * @package    Doozr_Controller
 * @subpackage Doozr_Controller_Front
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Class/Singleton.php';

/**
 * Doozr - Controller - Front
 *
 * The Front-Controller of the Doozr-Framework.
 *
 * @category   Doozr
 * @package    Doozr_Controller
 * @subpackage Doozr_Controller_Front
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Controller_Front extends Doozr_Base_Class_Singleton
{
    /**
     * The request state of active request
     *
     * @var Doozr_Request_State
     * @access protected
     */
    protected $requestState;

    /**
     * The response state.
     *
     * @var Doozr_Response_State
     * @access protected
     */
    protected $responseState;


    /**
     * Constructor.
     *
     * @param Doozr_Registry       $registry      Central registry of the application
     * @param Doozr_Request_State  $requestState  Request state
     * @param Doozr_Response_State $responseState Response state
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \Doozr_Controller_Front instance of this class
     * @access protected
     */
    protected function __construct(
        Doozr_Registry       $registry,
        Doozr_Request_State  $requestState,
        Doozr_Response_State $responseState
    ) {
        self::setRegistry($registry);

        // Store the request state
        $this
            ->requestState($requestState)
            ->responseState($responseState);
    }

    /**
     * Setter for request state.
     *
     * @param Doozr_Request_State $requestState The request state.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setRequestState(Doozr_Request_State $requestState)
    {
        $this->requestState = $requestState;
    }

    /**
     * Setter for request state.
     *
     * @param Doozr_Request_State $requestState The request state.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function requestState(Doozr_Request_State $requestState)
    {
        $this->setRequestState($requestState);
        return $this;
    }

    /**
     * Getter for request state.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Request_State|null The stored instance if set, otherwise NULL
     * @access protected
     */
    protected function getRequestState()
    {
        return $this->requestState;
    }

    /**
     * Setter for response state.
     *
     * @param Doozr_Response_State $responseState The response state.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setResponseState(Doozr_Response_State $responseState)
    {
        $this->responseState = $responseState;
    }

    /**
     * Setter for response state.
     *
     * @param Doozr_Response_State $responseState The response state.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function responseState(Doozr_Response_State $responseState)
    {
        $this->setResponseState($responseState);
        return $this;
    }

    /**
     * Getter for response state.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Response_State|null The stored instance if set, otherwise NULL
     * @access protected
     */
    protected function getResponseState()
    {
        return $this->responseState;
    }

    /**
     * Returns the request state for userland (developer) as "request".
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Request_State The request state
     * @access public
     */
    public function getRequest()
    {
        return $this->getRequestState();
    }

    /**
     * Returns the response state for userland (developer) as "response".
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Response_State The response state
     * @access public
     */
    public function getResponse()
    {
        return $this->getResponseState();
    }
}
