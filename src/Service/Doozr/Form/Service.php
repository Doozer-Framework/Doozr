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
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Service/Interface.php';

use Psr\Http\Message\ServerRequestInterface as Request;
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
     * META: Name of "token" field.
     *
     * @var string
     */
    protected $fieldnameToken;

    /**
     * META: Name of "submitted" field.
     *
     * @var string
     */
    protected $fieldnameSubmitted;

    /**
     * META: Name of "step" field.
     *
     * @var string
     */
    protected $fieldnameStep;

    /**
     * META: Name of the "steps" field.
     *
     * @var string
     */
    protected $fieldnameSteps;

    /**
     * META: Name of the "jump" field.
     *
     * @var string
     */
    protected $fieldnameJump;

    /**
     * META: Name of the "upload" field.
     *
     * @var string
     */
    protected $fieldnameUpload;

    /**
     * Service entry point.
     *
     * @param string $fieldnameToken     Name of form field for "token" value
     * @param string $fieldnameSubmitted Name of form field for "submitted" value
     * @param string $fieldnameStep      Name of form field for "step" value
     * @param string $fieldnameSteps     Name of form field for "steps" value
     * @param string $fieldnameJump      Name of form field for "jump" indicator
     * @param string $fieldnameUpload    Name of form field for "upload" indicator
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     */
    public function __tearup(
        $fieldnameToken = Doozr_Form_Service_Constant::DEFAULT_NAME_FIELD_TOKEN,
        $fieldnameSubmitted = Doozr_Form_Service_Constant::DEFAULT_NAME_FIELD_SUBMITTED,
        $fieldnameStep = Doozr_Form_Service_Constant::DEFAULT_NAME_FIELD_STEP,
        $fieldnameSteps = Doozr_Form_Service_Constant::DEFAULT_NAME_FIELD_STEPS,
        $fieldnameJump = Doozr_Form_Service_Constant::DEFAULT_NAME_FIELD_JUMP,
        $fieldnameUpload = Doozr_Form_Service_Constant::DEFAULT_NAME_FIELD_UPLOAD
    ) {
        $this
            ->fieldnameToken($fieldnameToken)
            ->fieldnameSubmitted($fieldnameSubmitted)
            ->fieldnameStep($fieldnameStep)
            ->fieldnameSteps($fieldnameSteps)
            ->fieldnameJump($fieldnameJump)
            ->fieldnameUpload($fieldnameUpload);

        return true;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns whether the current request is handable by this service.
     * In case of no form is found in request it returns FALSE, otherwise it returns the id/scope/name of the form
     * found in request.
     *
     * @param Request $request Request PSR
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string|bool The name of the form which is handable by Doozr_Form_Service if exist, otherwise FALSE
     */
    public function getHandableScopeByRequest(Request $request)
    {
        // Assume that nothing in request is handable
        $handable = false;

        $requestArguments   = $request->getQueryParams();
        $requestBody        = $request->getParsedBody();
        $fieldnameSubmitted = $this->getFieldnameSubmitted();

        if (true === isset($requestArguments[$fieldnameSubmitted])) {
            // Check for passed _GET arguments from URI like: /?jump=1
            $handable = $requestArguments[$fieldnameSubmitted];

            // Check for passed _POST arguments from request body like: &jump=1
        } elseif (true === isset($requestBody[$fieldnameSubmitted])) {
            $handable = $requestBody[$fieldnameSubmitted];
        }

        return $handable;
    }

    /**
     * Returns FormHandler instance for scope.
     *
     * @param string  $scope             Scope of form (identifier/name)
     * @param Request $request           PSR compatible Request
     * @param string  $method            Method to use for form (only the 1st call is generally possible with get|post)
     * @param bool    $angularDirectives Controls if angular-directives are required when rendering form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Form_Service_Handler_FormHandler
     */
    public function getFormHandler(
                $scope = DOOZR_NAMESPACE,
        Request $request = null,
                $method = Doozr_Form_Service_Constant::DEFAULT_METHOD,
                $angularDirectives = false
    ) {
        // Return form handler from factory
        return $this->formHandlerFactory(
            $scope,
            $request,
            $method,
            $angularDirectives
        );
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Factory for FormHandler.
     *
     * @param string  $scope             Scope of form (identifier/name)
     * @param Request $request           Request instance to getMetaComponents
     * @param string  $method            Method to use for form (only the 1st call is generally possible with get|post)
     * @param bool    $angularDirectives Controls if angular-directives are required when rendering form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return \Doozr_Form_Service_Handler_FormHandler Instance of form handler full ready to process forms
     */
    protected function formHandlerFactory(
        $scope,
        Request $request,
        $method,
        $angularDirectives
    ) {
        /* @var Doozr_Form_Service_Handler_FormHandler $formHandler */
        return self::$registry->getContainer()->build(
            'doozr.form.service.handler.formhandler',
            [
                $scope,
                $request,
                $method,
                $angularDirectives,
                [
                    Doozr_Form_Service_Constant::IDENTIFIER_TOKEN     => $this->getFieldnameToken(),
                    Doozr_Form_Service_Constant::IDENTIFIER_SUBMITTED => $this->getFieldnameSubmitted(),
                    Doozr_Form_Service_Constant::IDENTIFIER_STEP      => $this->getFieldnameStep(),
                    Doozr_Form_Service_Constant::IDENTIFIER_STEPS     => $this->getFieldnameSteps(),
                    Doozr_Form_Service_Constant::IDENTIFIER_JUMP      => $this->getFieldnameJump(),
                    Doozr_Form_Service_Constant::IDENTIFIER_UPLOAD    => $this->getFieldnameUpload(),
                ],
            ]
        );
    }

    /**
     * Setter for fieldname token.
     *
     * @param string $fieldnameToken Fieldname
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
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
     *
     * @return $this Instance for chaining
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
     *
     * @return string The name of the token field
     */
    protected function getFieldnameToken()
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
     *
     * @return $this Instance for chaining
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
     *
     * @return string The name of the submitted field
     */
    protected function getFieldnameSubmitted()
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
     *
     * @return $this Instance for chaining
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
     *
     * @return string The name of the step field
     */
    protected function getFieldnameStep()
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
     *
     * @return $this Instance for chaining
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
     *
     * @return string The name of the steps field
     */
    protected function getFieldnameSteps()
    {
        return $this->fieldnameSteps;
    }

    /**
     * Setter for fieldname jump.
     *
     * @param string $fieldnameJump Fieldname
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setFieldnameJump($fieldnameJump)
    {
        $this->fieldnameJump = $fieldnameJump;
    }

    /**
     * Setter for fieldname jump.
     *
     * @param string $fieldnameJump Fieldname
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function fieldnameJump($fieldnameJump)
    {
        $this->setFieldnameJump($fieldnameJump);

        return $this;
    }

    /**
     * Getter for fieldname jump.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The name of the jump field
     */
    protected function getFieldnameJump()
    {
        return $this->fieldnameJump;
    }

    /**
     * Setter for fieldname upload.
     *
     * @param string $fieldnameUpload Fieldname upload.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setFieldnameUpload($fieldnameUpload)
    {
        $this->fieldnameUpload = $fieldnameUpload;
    }

    /**
     * Fluent: Setter for fieldname upload.
     *
     * @param string $fieldnameUpload Fieldname upload.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function fieldnameUpload($fieldnameUpload)
    {
        $this->setFieldnameUpload($fieldnameUpload);

        return $this;
    }

    /**
     * Getter for fieldname upload.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The name of the upload field
     */
    protected function getFieldnameUpload()
    {
        return $this->fieldnameUpload;
    }
}
