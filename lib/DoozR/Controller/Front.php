<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Controller - Front
 *
 * Front.php - The Front-Controller of the DoozR-Framework.
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
 * Please feel free to contact us via e-mail: opensource@clickalicious.de
 *
 * @category   DoozR
 * @package    DoozR_Controller
 * @subpackage DoozR_Controller_Front
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Class/Singleton.php';

/**
 * DoozR - Controller - Front
 *
 * The Front-Controller of the DoozR-Framework.
 *
 * @category   DoozR
 * @package    DoozR_Controller
 * @subpackage DoozR_Controller_Front
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Controller_Front extends DoozR_Base_Class_Singleton
{
    /**
     * Detected runtime environment (web|cli|httpd)
     *
     * @var string
     * @access protected
     */
    protected $runtimeEnvironment = 'web';

    /**
     * The request state of active request
     *
     * @var DoozR_Request_State
     * @access protected
     */
    protected $requestState;

    /**
     * The response state.
     *
     * @var DoozR_Response_State
     * @access protected
     */
    protected $responseState;

    /**
     * constant RUNTIME_ENVIRONMENT_CLI
     *
     * holds the key for "cli" running runtimeEnvironment
     *
     * @var string
     * @access public
     */
    const RUNTIME_ENVIRONMENT_CLI = 'cli';

    /**
     * constant RUNTIME_ENVIRONMENT_WEB
     *
     * holds the key for "web" running runtimeEnvironment
     *
     * @var string
     * @access public
     */
    const RUNTIME_ENVIRONMENT_WEB = 'web';

    /**
     * constant RUNTIME_ENVIRONMENT_HTTPD
     *
     * holds the key for "httpd" running runtimeEnvironment
     *
     * @var string
     * @access public
     */
    const RUNTIME_ENVIRONMENT_HTTPD = 'httpd';


    /**
     * Constructor.
     *
     * @param DoozR_Registry       $registry      Central registry of the application
     * @param DoozR_Request_State  $requestState  Request state
     * @param DoozR_Response_State $responseState Response state
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Controller_Front instance of this class
     * @access protected
     */
    protected function __construct(
        DoozR_Registry       $registry,
        DoozR_Request_State  $requestState,
        DoozR_Response_State $responseState
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
     * @param DoozR_Request_State $requestState The request state.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setRequestState(DoozR_Request_State $requestState)
    {
        $this->requestState = $requestState;
    }

    /**
     * Setter for request state.
     *
     * @param DoozR_Request_State $requestState The request state.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function requestState(DoozR_Request_State $requestState)
    {
        $this->setRequestState($requestState);
        return $this;
    }

    /**
     * Getter for request state.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Request_State|null The stored instance if set, otherwise NULL
     * @access protected
     */
    protected function getRequestState()
    {
        return $this->requestState;
    }

    /**
     * Setter for response state.
     *
     * @param DoozR_Response_State $responseState The response state.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setResponseState(DoozR_Response_State $responseState)
    {
        $this->responseState = $responseState;
    }

    /**
     * Setter for response state.
     *
     * @param DoozR_Response_State $responseState The response state.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function responseState(DoozR_Response_State $responseState)
    {
        $this->setResponseState($responseState);
        return $this;
    }

    /**
     * Getter for response state.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Response_State|null The stored instance if set, otherwise NULL
     * @access protected
     */
    protected function getResponseState()
    {
        return $this->responseState;
    }

    /**
     * Setter for runtime environment.
     *
     * @param string $runtimeEnvironment The runtime environment.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setRuntimeEnvironment($runtimeEnvironment)
    {
        $this->runtimeEnvironment = $runtimeEnvironment;
    }

    /**
     * Setter for runtime environment.
     *
     * @param string $runtimeEnvironment The runtime environment.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function runtimeEnvironment($runtimeEnvironment)
    {
        $this->setRuntimeEnvironment($runtimeEnvironment);
        return $this;
    }

    /**
     * Getter for runtime environment.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The current runtime environment
     * @access public
     */
    public function getRuntimeEnvironment()
    {
        return $this->runtimeEnvironment;
    }

    /**
     * Returns the request state for userland (developer) as "request".
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Request_State The request state
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
     * @return DoozR_Response_State The response state
     * @access public
     */
    public function getResponse()
    {
        return $this->getResponseState();
    }
}
