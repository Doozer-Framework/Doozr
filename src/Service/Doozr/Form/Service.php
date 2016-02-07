<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service.
 *
 * Service.php - Service for generating valid and 100% x-browser compatible
 * HTML-Forms.
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
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Service/Singleton/Facade.php';
require_once DOOZR_DOCUMENT_ROOT.'Service/Doozr/Form/Service/Constant.php';
require_once DOOZR_DOCUMENT_ROOT.'Service/Doozr/Form/Service/Validate/Constant.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Service/Interface.php';

use Psr\Http\Message\ServerRequestInterface;
use Doozr\Loader\Serviceloader\Annotation\Inject;

/**
 * Doozr - Form - Service.
 *
 * Service for generating valid and 100% x-browser compatible HTML-Forms
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
 * @Inject(
 *     link   = "doozr.registry",
 *     type   = "constructor",
 *     target = "getInstance"
 * )
 */
class Doozr_Form_Service extends Doozr_Base_Service_Singleton_Facade
    implements
    Doozr_Base_Service_Interface
{
    /**
     * Name of token field.
     *
     * @var string
     */
    protected $fieldnameToken;

    /**
     * Name of submitted field.
     *
     * @var string
     */
    protected $fieldnameSubmitted;

    /**
     * Name of step field.
     *
     * @var string
     */
    protected $fieldnameStep;

    /**
     * Name of the steps field.
     *
     * @var string
     */
    protected $fieldnameSteps;

    /**
     * Name of field in form for TOKEN transfer.
     *
     * @var string
     */
    const FIELD_TOKEN = Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_TOKEN;

    /**
     * Name of field in form for SUBMITTED status transfer.
     *
     * @var string
     */
    const FIELD_SUBMITTED = Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_SUBMITTED;

    /**
     * Name of field in form for STEP transfer.
     *
     * @var string
     */
    const FIELD_STEP = Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_STEP;

    /**
     * Name of field in form for STEPS transfer.
     *
     * @var string
     */
    const FIELD_STEPS = Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_STEPS;

    /**
     * Service entry point.
     *
     * @param string $fieldnameToken     Name of form field for "token" value
     * @param string $fieldnameSubmitted Name of form field for "submitted" value
     * @param string $fieldnameStep      Name of form field for "step" value
     * @param string $fieldnameSteps     Name of form field for "steps" value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE On success, otherwise FALSE
     */
    public function __tearup(
        $fieldnameToken     = self::FIELD_TOKEN,
        $fieldnameSubmitted = self::FIELD_SUBMITTED,
        $fieldnameStep      = self::FIELD_STEP,
        $fieldnameSteps     = self::FIELD_STEPS
    ) {
        $this
            ->fieldnameToken($fieldnameToken)
            ->fieldnameSubmitted($fieldnameSubmitted)
            ->fieldnameStep($fieldnameStep)
            ->fieldnameSteps($fieldnameSteps);

        return true;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for fieldname token.
     *
     * @param string $fieldnameToken Fieldname
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setFieldnameToken($fieldnameToken)
    {
        $this->fieldnameToken = $fieldnameToken;
    }

    /**
     * Setter for fieldname token.
     *
     * @param string $fieldnameToken Fieldname
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function fieldnameToken($fieldnameToken)
    {
        $this->setFieldnameToken($fieldnameToken);

        return $this;
    }

    /**
     * Getter for fieldname token.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The name of the token field
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
     */
    public function setFieldnameSubmitted($fieldnameSubmitted)
    {
        $this->fieldnameSubmitted = $fieldnameSubmitted;
    }

    /**
     * Setter for fieldname submitted.
     *
     * @param string $fieldnameSubmitted Fieldname
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function fieldnameSubmitted($fieldnameSubmitted)
    {
        $this->setFieldnameSubmitted($fieldnameSubmitted);

        return $this;
    }

    /**
     * Getter for fieldname submitted.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The name of the submitted field
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
     */
    public function setFieldnameStep($fieldnameStep)
    {
        $this->fieldnameStep = $fieldnameStep;
    }

    /**
     * Setter for fieldname step.
     *
     * @param string $fieldnameStep Fieldname
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function fieldnameStep($fieldnameStep)
    {
        $this->setFieldnameStep($fieldnameStep);

        return $this;
    }

    /**
     * Getter for fieldname step.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The name of the step field
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
     */
    public function setFieldnameSteps($fieldnameSteps)
    {
        $this->fieldnameSteps = $fieldnameSteps;
    }

    /**
     * Setter for fieldname steps.
     *
     * @param string $fieldnameSteps Fieldname
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function fieldnameSteps($fieldnameSteps)
    {
        $this->setFieldnameSteps($fieldnameSteps);

        return $this;
    }

    /**
     * Getter for fieldname steps.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The name of the steps field
     */
    public function getFieldnameSteps()
    {
        return $this->fieldnameSteps;
    }

    /**
     * Returns name of form handable if current request is handable by Doozr_Form_Service, otherwise FALSE.
     *
     * @param ServerRequestInterface $request Psr request instance for getting information from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string|bool The name of the form which is handable by Doozr_Form_Service if exist, otherwise FALSE
     */
    public function isHandable(ServerRequestInterface $request = null)
    {
        // Assume that request is not! handable by Doozr_Form_Service -> The API does only share some parts with MVP def
        $handable = false;

        // Get required input to search in ...
        $requestArguments = $request->getQueryParams();
        $requestBody      = $request->getBody();

        if (true === isset($requestArguments[$this->getFieldnameSubmitted()])) {
            $handable = $requestArguments[$this->getFieldnameSubmitted()];

        } elseif (true === isset($requestBody[$this->getFieldnameSubmitted()])) {
            $handable = $requestBody[$this->getFieldnameSubmitted()];

        }

        return $handable;
    }

    /**
     * Returns FormHandler instance (yep i know damn name) to manage the form(s).
     *
     * @param string $scope             Scope for the form (form identifier or name)
     * @param array  $arguments         Arguments from request or cli
     * @param string $requestMethod     Request method used for request
     * @param bool   $angularDirectives TRUE ...
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Form_Service_FormHandler
     */
    public function getFormHandler(
              $scope,
        array $arguments         = [],
              $requestMethod     = null,
              $angularDirectives = false
    ) {
        // Return form handler from factory
        return $this->formHandlerFactory($scope, $arguments, $requestMethod, $angularDirectives);
    }

    /**
     * Factory for FormHandler.
     *
     * @param string $scope             Scope for the form (form identifier or name)
     * @param array  $arguments         Arguments from request or cli
     * @param string $requestMethod     Request method used for request
     * @param bool   $angularDirectives TRUE ...
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return \Doozr_Form_Service_FormHandler Instance of form handler full ready to process forms
     */
    protected function formHandlerFactory($scope, array $arguments, $requestMethod, $angularDirectives)
    {
        /* @var Doozr_Form_Service_FormHandler $formHandler */
        return self::$registry->getContainer()->build(
            'doozr.form.service.formhandler',
            [$scope, $arguments, $requestMethod, $angularDirectives]
        );
    }
}
