<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service - Handler - MetaDataHandler.
 *
 * MetaDatHandler.php - Handler for meta data control layer (step, steps, token, ...).
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
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Class.php';

/**
 * Doozr - Form - Service - Handler - MetaDataHandler.
 *
 * Handler for meta data control layer (step, steps, token, ...).
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
class Doozr_Form_Service_Handler_MetaComponentHandler extends Doozr_Base_Class
{
    /**
     * Registry.
     *
     * @var Doozr_Registry
     */
    protected $registry;

    /**
     * The scope of this instance (should be unique).
     *
     * @var string
     */
    protected $scope;

    /**
     * Token for securing transportation of the form.
     *
     * @var string
     */
    protected $token;

    /**
     * Upload field.
     *
     * @var string
     */
    protected $upload;

    /**
     * The step we're currently on.
     *
     * @var int
     */
    protected $step;

    /**
     * The last (final) step.
     *
     * @var int
     */
    protected $steps;

    /**
     * META: Name of "upload" field.
     *
     * @var string
     */
    protected $fieldnameUpload;

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
     * Whether to inject angular model directives for hidden fields.
     *
     * @example If set to TRUE hidden fields will get a ng-model="" directive automagically injected bound to the name
     *          of the hidden field: <input type="hidden" name="send" ng-model="send" />
     *
     * @var bool
     */
    protected $angularDirectives = false;

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param string         $scope    Scope of this instance.
     * @param Doozr_Registry $registry Registry instance (??? only Di !!!)
     */
    public function __construct(
                       $scope,
        Doozr_Registry $registry,
                       $token,
                       $step,
                       $steps,
                       $upload,
                       $angularDirectives,
                       $fieldnameToken,
                       $fieldnameSubmitted,
                       $fieldnameStep,
                       $fieldnameSteps,
                       $fieldnameJump,
                       $fieldnameUpload
    ) {
        $this
            ->scope($scope)
            ->registry($registry)
            ->token($token)
            ->step($step)
            ->steps($steps)
            ->upload($upload)
            ->angularDirectives($angularDirectives)
            ->fieldnameToken($fieldnameToken)
            ->fieldnameSubmitted($fieldnameSubmitted)
            ->fieldnameStep($fieldnameStep)
            ->fieldnameSteps($fieldnameSteps)
            ->fieldnameJump($fieldnameJump)
            ->fieldnameUpload($fieldnameUpload);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns a collection of meta control layer fields.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array A collection of meta control layer fields for current form
     */
    public function getMetaComponents()
    {
        // Return array of Meta Data Control fields
        return [
            $this->getFieldnameToken()     => $this->generateTokenComponent(),
            $this->getFieldnameStep()      => $this->generateStepField(),
            $this->getFieldnameSteps()     => $this->generateStepsField(),
            $this->getFieldnameSubmitted() => $this->generateSubmittedField(),
            $this->getFieldnameUpload()    => $this->generateUploadField(),
        ];
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Factory for creating meta layer fields.
     *
     * @param string $name              Name of the component
     * @param string $value             Value of the component
     * @param bool   $angularDirectives Whether to render with angular directives or not
     * @param string $scope             Scope to use for rendering
     * @param string $type              Type of the component
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Form_Service_Component_Input Input element
     */
    protected function metaComponentFactory($name, $value, $angularDirectives = false, $scope = '', $type = 'hidden')
    {
        /* @var Doozr_Form_Service_Component_Input $input */
        $input = $this->getRegistry()->getContainer()->build('doozr.form.service.component.input');

        $input->setName($name);
        $input->setType($type);
        $input->setValue($value);

        // Check for directive inject
        if (true === $angularDirectives) {
            $input->setAttribute('ng-model', sprintf('%s%s', $scope, $name));
            $input->setAttribute('value-transfer', $scope);
        }

        return $input;
    }

    /**
     * Returns a generated "token" field.
     * This field contains the token required to submit data back to the bucket/store (security).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Form_Service_Component_Input Generated field.
     */
    protected function generateTokenComponent()
    {
        return $this->metaComponentFactory(
            $this->getFieldnameToken(), $this->getToken(), $this->getAngularDirectives(), $this->getScope()
        );
    }

    /**
     * Returns a generated "step" field.
     * This field contains the current step for state transfer and for progress indication and so on.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function generateStepField()
    {
        return $this->metaComponentFactory(
            $this->getFieldnameStep(), $this->getStep(), $this->getAngularDirectives(), $this->getScope()
        );
    }

    /**
     * Returns a generated "steps" field.
     * This field contains the total of steps available in the form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Form_Service_Component_Input Generated field.
     */
    protected function generateStepsField()
    {
        return $this->metaComponentFactory(
            $this->getFieldnameSteps(), $this->getSteps(), $this->getAngularDirectives(), $this->getScope()
        );
    }

    /**
     * Returns a generated "submitted" field.
     * This field contains the scope identifier and is the signal for Doozr Form Service for a submitted form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Form_Service_Component_Input Generated field.
     */
    protected function generateSubmittedField()
    {
        return $this->metaComponentFactory(
            $this->getFieldnameSubmitted(), $this->getScope(), $this->getAngularDirectives(), $this->getScope()
        );
    }

    protected function generateUploadField()
    {
        return $this->metaComponentFactory(
            $this->getFieldnameUpload(), $this->getUpload(), $this->getAngularDirectives(), $this->getScope()
        );
    }

    /**
     * Setter of registry.
     *
     * @param Doozr_Registry $registry Value of registry.
     *
     * @return $this Instance for chaining
     */
    protected function setRegistry(Doozr_Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Fluent: Setter for registry.
     *
     * @param Doozr_Registry $registry Value of registry.
     *
     * @return $this Instance for chaining
     */
    protected function registry(Doozr_Registry $registry)
    {
        $this->setRegistry($registry);

        return $this;
    }

    /**
     * Getter for registry.
     *
     * @return Doozr_Registry Value of registry.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function getRegistry()
    {
        return $this->registry;
    }


    /**
     * Setter for scope.
     *
     * @param string $scope The scope to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setScope($scope)
    {
        $this->scope = $scope;
    }

    /**
     * Fluent: Setter for scope.
     *
     * @param string $scope The scope to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function scope($scope)
    {
        $this->setScope($scope);

        return $this;
    }

    /**
     * Getter for scope.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string|null The scope set, otherwise NULL
     */
    protected function getScope()
    {
        return $this->scope;
    }

    /**
     * Setter for token.
     *
     * @param string $token The token to store.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Fluent: Setter for token.
     *
     * @param string $token The token to store.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function token($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Getter for token.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string|null The token stored, otherwise NULL
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Setter for step.
     *
     * @param int $step The step to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setStep($step)
    {
        $this->step = $step;
    }

    /**
     * Fluent: Setter for step.
     *
     * @param int $step The step to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function step($step = null)
    {
        $this->setStep($step);

        return $this;
    }

    /**
     * Getter for step.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int|null The step set, otherwise NULL
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * Setter for steps.
     *
     * @param int $steps The steps
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setSteps($steps)
    {
        $this->steps = $steps;
    }

    /**
     * Setter for steps.
     *
     * @param int $steps The steps
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function steps($steps)
    {
        $this->setSteps($steps);

        return $this;
    }

    /**
     * Getter for steps.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int The current steps
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * Setter for upload.
     *
     * @param string $upload Value for upload
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setUpload($upload)
    {
        $this->upload = $upload;
    }

    /**
     * Fluent: Setter for upload.
     *
     * @param string $upload Value for upload
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function upload($upload)
    {
        $this->setUpload($upload);

        return $this;
    }

    /**
     * Getter for upload.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string Value of upload
     */
    protected function getUpload()
    {
        return $this->upload;
    }

    /**
     * Setter for angularDirectives.
     *
     * @param bool TRUE to enable automagically inject of angular directive ng-model for hidden meta components.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setAngularDirectives($angularDirectives)
    {
        $this->angularDirectives = $angularDirectives;
    }

    /**
     * Setter for angularDirectives.
     *
     * @param bool TRUE to enable automagically inject of angular directive ng-model for hidden meta components.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function angularDirectives($angularDirectives)
    {
        $this->setAngularDirectives($angularDirectives);

        return $this;
    }

    /**
     * Getter for arguments.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE or FALSE depending on state
     */
    protected function getAngularDirectives()
    {
        return $this->angularDirectives;
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
     * @param string $fieldnameUpload Value to set
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
     * @param string $fieldnameUpload Value to set
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
