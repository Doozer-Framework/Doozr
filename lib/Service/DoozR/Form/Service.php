<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Form - Service
 *
 * Service.php - Service for generating valid and 100% x-browser compatible
 * HTML-Forms.
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
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
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Service/Singleton/Facade.php';
require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Form/Service/Constant.php';
require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Form/Service/Validate/Constant.php';
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Service/Interface.php';

use DoozR\Loader\Serviceloader\Annotation\Inject;

/**
 * DoozR - Form - Service
 *
 * Service for generating valid and 100% x-browser compatible HTML-Forms
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @Inject(
 *     class="DoozR_Registry",
 *     identifier="getInstance",
 *     type="constructor",
 *     position=1
 * )
 */
class DoozR_Form_Service extends DoozR_Base_Service_Singleton_Facade implements DoozR_Base_Service_Interface
{
    /**
     * Name of token field.
     *
     * @var string
     * @access protected
     */
    protected $fieldnameToken;

    /**
     * Name of submitted field.
     *
     * @var string
     * @access protected
     */
    protected $fieldnameSubmitted;

    /**
     * Name of step field.
     *
     * @var string
     * @access protected
     */
    protected $fieldnameStep;

    /**
     * Name of the steps field.
     *
     * @var string
     * @access protected
     */
    protected $fieldnameSteps;

    /**
     * Session service instance.
     *
     * @var DoozR_Session_Service
     * @access protected
     */
    protected $session;


    /**
     * Constructor replacement.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __tearup(DoozR_Session_Service $session = null)
    {
        if ($session === null) {
            $session = DoozR_Loader_Serviceloader::load('session');
        }

        // Store session
        $this->setSession($session);

        $this
            ->fieldnameToken(
                DoozR_Form_Service_Constant::PREFIX . DoozR_Form_Service_Constant::FORM_NAME_FIELD_TOKEN
            )
            ->fieldnameSubmitted(
                DoozR_Form_Service_Constant::PREFIX . DoozR_Form_Service_Constant::FORM_NAME_FIELD_SUBMITTED
            )
            ->fieldnameStep(
                DoozR_Form_Service_Constant::PREFIX . DoozR_Form_Service_Constant::FORM_NAME_FIELD_STEP
            )
            ->fieldnameSteps(
                DoozR_Form_Service_Constant::PREFIX . DoozR_Form_Service_Constant::FORM_NAME_FIELD_STEPS
            );

        return true;
    }

    /**
     * Setter for session.
     *
     * @param DoozR_Session_Service $session The session service instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setSession(DoozR_Session_Service $session)
    {
        $this->session = $session;
    }

    /**
     * Setter for session.
     *
     * @param DoozR_Session_Service $session The session service instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function session(DoozR_Session_Service $session)
    {
        $this->setSession($session);
        return $this;
    }

    /**
     * Getter for session.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Session_Service|null The instance if set, otherwise NULL
     * @access protected
     */
    protected function getSession()
    {
        return $this->session;
    }

    /**
     * Setter for fieldname token.
     *
     * @param string $fieldnameToken Fieldname
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setFieldnameToken($fieldnameToken)
    {
        $this->fieldnameToken = $fieldnameToken;
    }

    /**
     * Setter for fieldname token.
     *
     * @param string $fieldnameToken Fieldname
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function fieldnameToken($fieldnameToken)
    {
        $this->setFieldnameToken($fieldnameToken);
        return $this;
    }

    /**
     * Getter for fieldname token.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The name of the token field
     * @access public
     */
    public function getFieldnameToken()
    {
        return $this->fieldnameToken;
    }

    /**
     * Setter for fieldname submitted.
     *
     * @param string $fieldnameSubmitted Fieldname
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setFieldnameSubmitted($fieldnameSubmitted)
    {
        $this->fieldnameSubmitted = $fieldnameSubmitted;
    }

    /**
     * Setter for fieldname submitted.
     *
     * @param string $fieldnameSubmitted Fieldname
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function fieldnameSubmitted($fieldnameSubmitted)
    {
        $this->setFieldnameSubmitted($fieldnameSubmitted);
        return $this;
    }

    /**
     * Getter for fieldname submitted.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The name of the submitted field
     * @access public
     */
    public function getFieldnameSubmitted()
    {
        return $this->fieldnameSubmitted;
    }

    /**
     * Setter for fieldname step.
     *
     * @param string $fieldnameStep Fieldname
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setFieldnameStep($fieldnameStep)
    {
        $this->fieldnameStep = $fieldnameStep;
    }

    /**
     * Setter for fieldname step.
     *
     * @param string $fieldnameStep Fieldname
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function fieldnameStep($fieldnameStep)
    {
        $this->setFieldnameStep($fieldnameStep);
        return $this;
    }

    /**
     * Getter for fieldname step.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The name of the step field
     * @access public
     */
    public function getFieldnameStep()
    {
        return $this->fieldnameStep;
    }

    /**
     * Setter for fieldname steps.
     *
     * @param string $fieldnameSteps Fieldname
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setFieldnameSteps($fieldnameSteps)
    {
        $this->fieldnameSteps = $fieldnameSteps;
    }

    /**
     * Setter for fieldname steps.
     *
     * @param string $fieldnameSteps Fieldname
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function fieldnameSteps($fieldnameSteps)
    {
        $this->setFieldnameSteps($fieldnameSteps);
        return $this;
    }

    /**
     * Getter for fieldname steps.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The name of the steps field
     * @access public
     */
    public function getFieldnameSteps()
    {
        return $this->fieldnameSteps;
    }

    /**
     * Returns name of form handable if current request is handable by DoozR_Form_Service, otherwise FALSE.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|bool The name of the form which is handable by DoozR_Form_Service if exist, otherwise FALSE
     * @access public
     */
    public function isHandable()
    {
        // Assume that request is not! handable by DoozR_Form_Service -> The API does only share some parts with MVP def
        $handable = false;

        /* @var $requestState DoozR_Request_State */
        $requestState = $this->getRegistry()->request;

        // Get required input to search in ...
        $requestArguments = $requestState->getArguments();
        $requestBody      = $requestState->getRequestBody();

        if (isset($requestArguments->{$this->getFieldnameSubmitted()}) === true) {
            $handable = $requestArguments->{$this->getFieldnameSubmitted()};

        } elseif (isset($requestBody->{$this->getFieldnameSubmitted()}) === true) {
            $handable = $requestBody->{$this->getFieldnameSubmitted()};
        }

        return $handable;
    }

    /**
     * Returns Form-Manager instance (yep i know damn name) to manage the form(s).
     *
     * @author Benjamin Carl <benjamin.carl@clickalicious.de>
     * @return DoozR_Form_Service_FormManager
     * @access public
     */
    public function getFormManager($namespace, $arguments = null, $requestMethod = null, $angular = false)
    {
        // Create a new form-container which combines the control-layer and the HTML parts
        return new DoozR_Form_Service_FormManager(
            $namespace,                                                // The namespace (used for session, I18n, ...)
            null,                                                      // Could ne I18n
            new DoozR_Form_Service_Component_Input(                    // Input element <- for cloning [DI]
                new DoozR_Form_Service_Renderer_Html(),
                new DoozR_Form_Service_Validator_Generic()
            ),
            new DoozR_Form_Service_Component_Form(                     // The form element we operate on [DI]
                new DoozR_Form_Service_Renderer_Html(),
                new DoozR_Form_Service_Validator_Generic()
            ),
            new DoozR_Form_Service_Store_Session($this->getSession()), // The session store [DI]
            new DoozR_Form_Service_Renderer_Html(),                    // A Renderer -> Native = HTML [DI]
            new DoozR_Form_Service_Validate_Validator(),               // A Validator to validate the elements [DI]
            new DoozR_Form_Service_Validate_Error(),                   // A Error object <- for cloning [DI]
            $arguments,                                                // The currents requests arguments
            $requestMethod,
            $angular                                                   // Bind to AngularJS directive (inject ng-model!)
        );
    }
}
