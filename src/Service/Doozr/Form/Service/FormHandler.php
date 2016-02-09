<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service.
 *
 * FormHandler.php - This class is the outer container which collects
 * a form and all its childs and adds the control layer to it and so
 * on!
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

/**
 * Doozr - Form - Service.
 *
 * This class is the outer container which collects
 * a form and all its childs and adds the control layer to it and so
 * on!
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
class Doozr_Form_Service_FormHandler
{
    /**
     * The scope of this instance (should be unique).
     *
     * @var string
     */
    protected $scope = Doozr_Form_Service_Constant::DEFAULT_SCOPE;

    /**
     * Contains the Form instance and all of its
     * childs and so on ...
     *
     * @var Doozr_Form_Service_Component_Form
     */
    public $form;

    /**
     * The translator used for translating all types of text within this service.
     *
     * @var Doozr_I18n_Service_Translator
     */
    protected $i18n;

    /**
     * Jump status of current request. True = we jumped through a deeplink.
     * False = no jump.
     *
     * @var bool
     */
    protected $wasJumped;

    /**
     * The token of the form.
     *
     * @var string
     */
    protected $token;

    /**
     * The errors of the form.
     *
     * @var array
     */
    protected $error = [];

    /**
     * The store where our data resides while moving from one page request to the next.
     * Some sort of temporary persistence.
     *
     * @var Doozr_Form_Service_Store_Interface
     */
    protected $store;

    /**
     * The renderer which finally takes the components
     * and render them to something really useful.
     *
     * @var Doozr_Form_Service_Renderer_Interface
     */
    protected $renderer;

    /**
     * META: The maximum allowed filesize. This can be set via
     * setter but should not be touched cause this is filled by
     * the <input type="file" ...> class on instantiation with
     * the value from PHP's ini.
     *
     * @var int
     */
    protected $maxFileSize;

    /**
     * The submission status of the form.
     *
     * @var bool TRUE = submitted, otherwise FALSE
     */
    protected $submitted;

    /**
     * The valid status of the form.
     *
     * @var bool TRUE = valid, otherwise FALSE
     */
    protected $valid;

    /**
     * The finish/completion status of the form.
     *
     * @var bool TRUE = complete, otherwise FALSE
     */
    protected $complete;

    /**
     * The step we're currently on.
     *
     * @var int
     */
    protected $step = Doozr_Form_Service_Constant::STEP_DEFAULT_FIRST;

    /**
     * The last (final) step.
     *
     * @var int
     */
    protected $steps = Doozr_Form_Service_Constant::STEP_DEFAULT_LAST;

    /**
     * The behavior when an invalid token arrives.
     *
     * @var int
     */
    protected $invalidTokenBehavior;

    /**
     * Arguments passed with current request.
     *
     * @var array
     */
    protected $arguments;

    /**
     * The request method used for current request.
     *
     * @var string
     */
    protected $requestMethod;

    /**
     * TRUE to inject angular model directives for hidden fields.
     *
     * @example If set to TRUE hidden fields will get a ng-model=""
     *          directive automagically injected bound to the name
     *          of the hidden field:
     *          <input type="hidden" name="send" ng-model="send" />
     *
     * @var bool
     */
    protected $angularDirectives = false;

    /**
     * Collection of META fields.
     *
     * @var array
     */
    protected $metaFields = [];

    /**
     * Validator instance for validation.
     *
     * @var Doozr_Form_Service_Validate_Validator
     */
    protected $validator;

    /**
     * Input instance for cloning.
     *
     * @var Doozr_Form_Service_Component_Input
     */
    protected $inputInstance;

    /**
     * Error instance for cloning.
     *
     * @var Doozr_Form_Service_Validate_Error
     */
    protected $errorInstance;

    /**
     * Constructor.
     *
     * @param string                                      $scope             Scope to operate on (name of form)
     * @param Doozr_Registry                              $registry          Registry for Di and some other operations.
     * @param Doozr_I18n_Service_Interface                $i18n              I18n Translator instance if required
     * @param Doozr_Form_Service_Component_Input          $inputInstance     Input instance for cloning meta fields
     * @param Doozr_Form_Service_Component_Interface_Form $form              Form instance (the main object)
     * @param Doozr_Form_Service_Store_Interface          $store             Store instance
     * @param Doozr_Form_Service_Renderer_Interface       $renderer          Renderer instance for e.g. HTML-output
     * @param Doozr_Form_Service_Validate_Validator       $validator         Validator instance
     * @param Doozr_Form_Service_Validate_Error           $errorInstance     Instance for cloning error messages
     * @param array                                       $arguments         Arguments passed with this request
     * @param string                                      $requestMethod     Request method
     * @param bool                                        $angularDirectives Controls angular directives to hidden elems
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __construct(
                                                    $scope = Doozr_Form_Service_Constant::DEFAULT_SCOPE,
        Doozr_Registry                              $registry = null,
        Doozr_I18n_Service_Interface                $i18n = null,
        Doozr_Form_Service_Component_Input          $inputInstance = null,
        Doozr_Form_Service_Component_Interface_Form $form = null,
        Doozr_Form_Service_Store_Interface          $store = null,
        Doozr_Form_Service_Renderer_Interface       $renderer = null,
        Doozr_Form_Service_Validate_Validator       $validator = null,
        Doozr_Form_Service_Validate_Error           $errorInstance = null,
        array                                       $arguments = null,
                                                    $requestMethod = null,
                                                    $angularDirectives = false
    ) {
        // Store instances for further use
        $this
            ->scope($scope)
            ->registry($registry)
            ->i18n($i18n)
            ->inputInstance($inputInstance)
            ->form($form)
            ->store($store)
            ->renderer($renderer)
            ->validator($validator)
            ->errorInstance($errorInstance)
            ->arguments($arguments)
            ->requestMethod($requestMethod)
            ->angularDirectives($angularDirectives);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for scope.
     *
     * @param string $scope The scope to use for this instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
    }

    /**
     * Fluent: Setter for scope.
     *
     * @param string $scope The scope to use for this instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function scope($scope)
    {
        if (false === is_string($scope)) {
            throw new Doozr_Form_Service_Exception(
                sprintf('Scope need to be passed as string!')
            );
        }

        $this->setScope($scope);

        return $this;
    }

    /**
     * Getter for scope.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The scope
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Setter for step.
     *
     * @param int $step The step
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setStep($step = null)
    {
        // only detect if not passed -> if passed then override forced
        if ($step === null) {

            // check submission status
            if ($this->wasSubmitted()) {

                // get current request
                $arguments = $this->getArguments();

                $fieldnameStep = Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_STEP;

                // try to get from last submit -> fallback to default of service
                $step = (isset($arguments->{$fieldnameStep})) ?
                    $arguments->{$fieldnameStep} :
                    Doozr_Form_Service_Constant::STEP_DEFAULT_FIRST;

                // increment by 1 if form was submitted! valid! and not complete yet!
                $step += ($this->isValid($step)) ? 1 : 0;
            } else {
                // if form wasn't submitted > its default state is step 1
                $step = Doozr_Form_Service_Constant::STEP_DEFAULT_FIRST;
            }
        }

        $this->step = $step;
    }

    /**
     * Setter for step.
     *
     * @param null|int $step The step to set
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
     * @return int The current step
     */
    public function getStep()
    {
        // assume first step = default
        $step = 1;

        // get request from front-controller
        $submittedData = $this->getArguments();

        // Get storage ...
        $storage = $this->getStorage($this->getStorageSkeleton());

        // build fieldname by pattern
        $fieldnameStep = Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_STEP;
        $fieldnameJump = Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_JUMP;

        // and now check if submission identifier exists in current request
        if (
            true === isset($submittedData[$fieldnameStep]) &&
            $submittedData[$fieldnameStep] <= $storage['lastvalidstep']
        ) {
            $step = $submittedData[$fieldnameStep];
        } elseif (
            true === isset($submittedData[$fieldnameJump]) &&
            $submittedData->{$fieldnameJump} <= $storage['lastvalidstep']
        ) {
            $step = $submittedData[$fieldnameJump];
        }

        if ($this->wasSubmitted() && $this->isValid($step)) {
            $step += 1;
            $storage['lastvalidstep'] = $step;
            $this->setStorage($storage);
        }

        return (int) $step;
    }

    /**
     * Setter for steps.
     *
     * @param int $steps The steps
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setSteps($steps = Doozr_Form_Service_Constant::STEP_DEFAULT_LAST)
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
    public function steps($steps = Doozr_Form_Service_Constant::STEP_DEFAULT_LAST)
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
        return (int) $this->steps;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Generates a new token which is set to registry and returned after operation.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The fresh generated token
     */
    public function updateToken()
    {
        $stored = $this->getStorage();
        $this->generateToken();
        $token           = $this->getToken();
        $stored['token'] = $token;
        $this->setStorage($stored);

        return $token;
    }


    /**
     * Renders the forms after the preparation is done. This method does generate a token for the current form
     * add all meta fields required for validation and so on, handles the data transfer from request to the next
     * and finally it renders the HTML and returns a valid form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The generated HTML output
     */
    public function render()
    {
        // important to generate token first!
        $this->generateToken();
        $this->addMetaFields();
        $this->handleDataTransfer();

        return $this->getForm()->render(true)->get();
    }

    /**
     * Returns the submitted value for a passed fieldname or the default value if the field wasn't submitted not
     * submitted.
     *
     * @param string      $name    The name of the component to return data for
     * @param string|null $default The default return value as string or NULL
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed The value of the component if exist, otherwise the $default value
     */
    public function getValue($name, $default = null)
    {
        /*
         * WHERE No matter where from look in step registry last! cause for stepping support
         */
        $value = $this->getSubmittedValue($name);

        // No value was retrieved?
        if (null === $value) {
            // Get storage ...
            $storage = $this->getStorage($this->getStorageSkeleton());

            if (isset($storage['data'][$name])) {
                $value = $storage['data'][$name];
            }
        }

        if ($value === null && $default !== null) {
            $value = $default;
        }

        return $value;
    }

    /**
     * Returns TRUE if the form is jumped to a specific step, otherwise FALSE.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int|bool Step as int where the form was jumped to, otherwise FALSE if not jumped
     */
    public function wasJumped()
    {
        // Inline cache ...
        if (null !== $this->wasJumped) {
            return $this->wasJumped;
        } else {
            $arguments     = $this->getArguments();
            $fieldnameJump = Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_JUMP;

            // and now check if submission identifier exists in current request
            if (true === isset($arguments[$fieldnameJump])) {
                $this->wasJumped = $arguments[$fieldnameJump];
            } else {
                $this->wasJumped = false;
            }
        }

        return $this->wasJumped;
    }

    /**
     * Returns the error of a component.
     *
     * @param string      $name    The name of the component to return data for
     * @param string|null $default The default return value as string or NULL
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed The value of the component if exist, otherwise the $default value
     */
    public function getError($name = null, $default = null)
    {
        $result = $default;

        if ($name === null) {
            $result = $this->error;
        } else {
            if ($this->hasError($name)) {
                $result = $this->error[$name];
            }
        }

        return $result;
    }

    /**
     * Translates the passed value to the current locale via I18n service.
     *
     * @param string $string    The string to translate
     * @param array  $arguments
     *
     * @throws Doozr_Form_Service_Exception
     *
     * @return mixed The
     */
    public function translate($string, array $arguments = [])
    {
        if ($this->getI18n() === null) {
            throw new Doozr_Form_Service_Exception(
                'Please set an instance of Doozr_I18n_Service (or compatible) first before calling translate()'
            );
        }

        return $this->i18n->_($string, $arguments);
    }

    /**
     * Setter for I18n Translator service.
     *
     * @param Doozr_I18n_Service_Interface $i18n The I18n instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setI18n(Doozr_I18n_Service_Interface $i18n = null)
    {
        if (null !== $i18n) {
            try {
                $i18n->useDomain($this->getScope());
                $this->i18n = $i18n->getTranslator();
            } catch (Doozr_I18n_Service_Exception $exception) {
                // Intentionally do nothing
            }
        }
    }

    /**
     * Fluent: Setter for I18n Translator service.
     *
     * @param Doozr_I18n_Service_Interface $i18n The I18n instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function i18n(Doozr_I18n_Service_Interface $i18n = null)
    {
        $this->setI18n($i18n);

        return $this;
    }

    /**
     * Getter for I18n Translator service.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|Doozr_I18n_Service An I18n Translator service instance, or NULL if not set
     */
    public function getI18n()
    {
        return $this->i18n;
    }

    /**
     * Setter for inputInstance.
     *
     * @param Doozr_Form_Service_Component_Input $inputInstance The input component instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setInputInstance(Doozr_Form_Service_Component_Input $inputInstance = null)
    {
        $this->inputInstance = $inputInstance;
    }

    /**
     * Fluent: Setter for inputInstance.
     *
     * @param Doozr_Form_Service_Component_Input $inputInstance The input component instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function inputInstance(Doozr_Form_Service_Component_Input $inputInstance = null)
    {
        $this->setInputInstance($inputInstance);

        return $this;
    }

    /**
     * Getter for InputInstance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|Doozr_Form_Service_Component_Input An Doozr_Form_Service_Component_Input instance, or NULL if not set
     */
    public function getInputInstance()
    {
        return $this->inputInstance;
    }

    /**
     * Setter for form.
     *
     * @param Doozr_Form_Service_Component_Interface_Form $form The form instance to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setForm(Doozr_Form_Service_Component_Interface_Form $form = null)
    {
        // Check for scope inject requirement
        if (null !== $form && $this->getScope() !== null && $form->getName() === null) {
            $form->setName($this->getScope());
        }

        $this->form = $form;
    }

    /**
     * Fluent: Setter for form.
     *
     * @param Doozr_Form_Service_Component_Interface_Form $form The form instance to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function form(Doozr_Form_Service_Component_Interface_Form $form = null)
    {
        $this->setForm($form);

        return $this;
    }

    /**
     * Getter for form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|Doozr_Form_Service_Component_Form The form instance if set, otherwise NULL
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Registry.
     *
     * @var Doozr_Registry
     */
    protected $registry;

    /**
     * Setter.
     *
     * @param \Doozr_Registry $registry
     */
    protected function setRegistry(Doozr_Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Fluent: Setter.
     *
     * @param \Doozr_Registry $registry
     *
     * @return $this
     */
    protected function registry(Doozr_Registry $registry)
    {
        $this->setRegistry($registry);

        return $this;
    }

    /**
     * Getter.
     *
     * @return \Doozr_Registry
     */
    protected function getRegistry()
    {
        return $this->registry;
    }




    public function getLabel($text = null)
    {
        /* @var Doozr_Form_Service_Component_Label $element */
        $element = $this->getRegistry()->getContainer()->build('doozr.form.service.component.label');

        if (null !== $text) {
            $element->setText($text);
        }

        return $element;
    }

    public function getElement($name = null)
    {
        /* @var Doozr_Form_Service_Component_Text $element */
        $element = $this->getRegistry()->getContainer()->build('doozr.form.service.component.text');

        if (null !== $name) {
            $element->setName($name);
        }

        return $element;
    }


    /**
     * Returns the token of this form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @param $token
     *
     * @return string The token of this form
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Returns the token of this form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The token of this form
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Setter for store.
     *
     * @param Doozr_Form_Service_Store_Interface $store The store to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setStore(Doozr_Form_Service_Store_Interface $store = null)
    {
        $this->store = $store;
    }

    /**
     * Fluent: Setter for store.
     *
     * @param Doozr_Form_Service_Store_Interface $store The store to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function store(Doozr_Form_Service_Store_Interface $store = null)
    {
        $this->setStore($store);

        return $this;
    }

    /**
     * Getter for store.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Form_Service_Store_Interface|null Instance or NULL if not set
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * Setter for renderer.
     *
     * @param Doozr_Form_Service_Renderer_Interface $renderer The renderer used for rendering the whole form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setRenderer(Doozr_Form_Service_Renderer_Interface $renderer = null)
    {
        $this->renderer = $renderer;
    }

    /**
     * Fluent: Setter for renderer.
     *
     * @param Doozr_Form_Service_Renderer_Interface $renderer The renderer used for rendering the whole form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function renderer(Doozr_Form_Service_Renderer_Interface $renderer = null)
    {
        $this->setRenderer($renderer);

        return $this;
    }

    /**
     * Getter for renderer.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Form_Service_Renderer_Interface|null Instance or NULL if not set
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * Setter for validator.
     *
     * @param Doozr_Form_Service_Validate_Validator $validator The validator instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setValidator(Doozr_Form_Service_Validate_Validator $validator = null)
    {
        $this->validator = $validator;
    }

    /**
     * Fluent: Setter for validator.
     *
     * @param Doozr_Form_Service_Validate_Validator $validator The validator instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function validator(Doozr_Form_Service_Validate_Validator $validator = null)
    {
        $this->setValidator($validator);

        return $this;
    }

    /**
     * Getter for validator.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|Doozr_Form_Service_Validate_Validator The validator instance if set, otherwise NULL
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Setter for Error-Instance.
     *
     * @param Doozr_Form_Service_Validate_Error $errorInstance The error instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setErrorInstance(Doozr_Form_Service_Validate_Error $errorInstance = null)
    {
        $this->errorInstance = $errorInstance;
    }

    /**
     * Fluent: Setter for Error-Instance.
     *
     * @param Doozr_Form_Service_Validate_Error $errorInstance The error instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function errorInstance(Doozr_Form_Service_Validate_Error $errorInstance = null)
    {
        $this->setErrorInstance($errorInstance);

        return $this;
    }

    /**
     * Getter for Error-Instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|Doozr_Form_Service_Validate_Error The errorInstance if set, otherwise NULL
     */
    public function getErrorInstance()
    {
        return $this->errorInstance;
    }

    /**
     * Setter for arguments.
     *
     * @param array $arguments The arguments to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setArguments(array $arguments = null)
    {
        $this->arguments = $arguments;
    }

    /**
     * Fluent: Setter for arguments.
     *
     * @param array $arguments The arguments to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function arguments(array $arguments = null)
    {
        $this->setArguments($arguments);

        return $this;
    }

    /**
     * Getter for arguments.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|array Arguments as an array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Setter for requestMethod.
     *
     * @param string $requestMethod The request method
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setRequestMethod($requestMethod)
    {
        $this->requestMethod = $requestMethod;
    }

    /**
     * Setter for requestMethod.
     *
     * @param string $requestMethod The request method
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function requestMethod($requestMethod)
    {
        $this->setRequestMethod($requestMethod);

        return $this;
    }

    /**
     * Getter for requestMethod.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string|null The request method used, or NULL if not set
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    /**
     * Setter for angularDirectives.
     *
     * @param bool TRUE to enable automagically inject of angular directive ng-model for hidden meta components.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setAngularDirectives($angularDirectives)
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
    public function angularDirectives($angularDirectives)
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
    public function getAngularDirectives()
    {
        return $this->angularDirectives;
    }

    /**
     * Sets the behavior for the case that an invalid token is used for submission.
     *
     * @param int $behavior The behavior to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setInvalidTokenBehavior($behavior)
    {
        $this->invalidTokenBehavior = $behavior;
    }

    /**
     * Sets the behavior for the case that an invalid token is used for submission.
     *
     * @param int $behavior The behavior to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function invalidTokenBehavior($behavior)
    {
        $this->setInvalidTokenBehavior($behavior);

        return $this;
    }

    /**
     * Returns the current active behavior for invalid tokens.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int The behavior as integer
     */
    public function getInvalidTokenBehavior()
    {
        return $this->invalidTokenBehavior;
    }

    /**
     * Magic => this is for echoing this class so it gets
     * rendered.
     *
     * @example echo $instance WILL provide you an echo
     *          of the rendered HTML
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The rendered HTML
     */
    public function __toString()
    {
        return $this->render();
    }

    /*-----------------------------------------------------------------------------------------------------------------*
    | Control layer
    *-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Removes an component from internal registry.
     *
     * @param        $key
     * @param string $component The name of the component
     *
     * @throws Doozr_Form_Service_Exception
     *
     * @internal param mixed $value The value to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     */
    public function removeFromStorage($key, $component = null)
    {
        // Get storage ...
        $storage = $this->getStorage($this->getStorageSkeleton());

        if (!isset($storage[$key])) {
            throw new Doozr_Form_Service_Exception(
                sprintf('Could not remove not existing key: "%s" from registry.', $key)
            );
        }

        //
        if ($component !== null) {
            unset($storage[$key][$component]);
        } else {
            unset($storage[$key]);
        }

        $this->setStorage($storage);

        return true;
    }

    /**
     * Setter for registry.
     *
     * @param mixed $storage The registry
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The registry
     */
    public function setStorage($storage)
    {
        return $this->getStore()->create(
            Doozr_Form_Service_Constant::PREFIX.$this->getScope(),
            $storage
        );
    }

    /**
     * Getter for registry (not the framework one -> this here is the internal store/storage-engine).
     *
     * @param null|mixed $default The default return value in case of an error.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The registry
     */
    public function getStorage($default = null)
    {
        $storage = null;

        try {
            $storage = $this->getStore()->read(
                Doozr_Form_Service_Constant::PREFIX.$this->getScope()
            );
        } catch (Doozr_Form_Service_Exception $e) {
            // Nothing
        }

        if (null === $storage && null !== $default) {
            $storage = $default;
        }

        return $storage;
    }

    /**
     * Invalidates the whole registry.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function invalidateStorage()
    {
        return $this->setStorage(null);
    }

    /**
     * Checks and returns the submission status of this form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if the form was submitted, otherwise FALSE
     */
    public function wasSubmitted()
    {
        // Inline cache [expensive]
        if (null !== $this->submitted) {
            $submitted = $this->submitted;
        } else {
            // assume not submitted
            $submitted = false;

            // get request from front-controller
            $submittedData = $this->getArguments();

            // build fieldname by pattern
            $fieldnameSubmissionStatus = Doozr_Form_Service_Constant::PREFIX.
                                         Doozr_Form_Service_Constant::FORM_NAME_FIELD_SUBMITTED;

            // and now check if submission identifier exists in current request
            if (
                true === isset($submittedData[$fieldnameSubmissionStatus]) &&
                $this->getScope() === $submittedData[$fieldnameSubmissionStatus]
            ) {
                $submitted = true;
            }

            // store retrieved result to speedup further lookups
            $this->submitted = $submitted;
        }

        return $submitted;
    }

    /**
     * Returns the validity of the form.
     *
     * @param int $step The step to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE is the form is valid, otherwise FALSE
     */
    public function isValid($step = 1)
    {
        // Inline cache [expensive]
        if (null !== $this->valid) {
            $valid = $this->valid;
        } else {
            // Check for submission
            if (true === $this->wasSubmitted()) {
                $valid = $this->validate($step);
            } else {
                // Assume status valid if not submitted
                $valid = true;
            }

            // Store valid status for further faster accessing
            $this->valid = $valid;
        }

        return $valid;
    }

    /**
     * Inverted isValid from above.
     *
     * @param int $step The step to return for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if is invalid, otherwise FALSE
     */
    public function isInvalid($step = 1)
    {
        return !$this->isValid($step);
    }

    /**
     * Returns the completion status of the form. If this method returns TRUE then the form steps are
     * completed, otherwise it would return FALSE.
     *
     * @param int $steps The count of steps to check against
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if form steps are complete, otherwise FALSE
     */
    public function isComplete($steps = 1)
    {
        dump('AAAA');
        die;

        // check already done?
        if ($this->complete !== null) {
            $complete = $this->complete;
        } else {
            // assume not yet complete
            $complete = false;

            // a form which wasn't ever submitted can't be complete
            if ($this->wasSubmitted() === false) {
                $complete = false;
            } else {
                // a form who want to be complete has to be valid as well
                if ($this->isValid() === false) {
                    $complete = false;
                } else {
                    $submittedData = $this->getArguments();

                    $fieldnameStep  = Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_STEP;
                    $fieldnameSteps = Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_STEPS;

                    $step = (isset($submittedData->{$fieldnameStep})) ?
                        $submittedData->{$fieldnameStep} :
                        $this->step;

                    $steps = (isset($submittedData->{$fieldnameSteps})) ?
                        $submittedData->{$fieldnameSteps} :
                        $this->steps;

                    if ($step === $steps) {
                        $complete = true;
                    }
                }
            }

            $this->complete = $complete;
        }

        return $complete;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Tools & Helper
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the error status of an component.
     *
     * @param string $name The name of the component to return status for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string $name The name of the component
     */
    protected function hasError($name)
    {
        return isset($this->error[$name]);
    }

    /**
     * Validates the store.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE is the store is valid, otherwise FALSE
     */
    protected function validateStorage()
    {
        return null !== $this->getStorage();
    }

    /**
     * Sets an error. Default to form.
     *
     * @param string $error         The error to set
     * @param string $componentName The component the error is related to
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setError($error, $componentName = 'form')
    {
        // Check for passed context - Some errors are passed without context as string
        if (is_array($error) === false) {
            $error = [
                'error'   => $error,
                'context' => [], // needs to be empty array for passing to I18n directly!
            ];
        }

        if (!isset($this->error[$componentName])) {
            $this->error[$componentName] = [];
        }

        $this->error[$componentName][] = $error;
    }

    /**
     * Validates the current form (if submitted) and returns the valid
     * state of the form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @param $step
     *
     * @return bool TRUE if valid, otherwise FALSE if invalid
     */
    protected function validate($step)
    {
        // Check if store i still valid - session can be timed out ...
        /*
        if (false === $this->validateStorage()) {

            $this->setError(
                Doozr_Form_Service_Validate_Constant::ERROR_PREFIX.
                Doozr_Form_Service_Validate_Constant::ERROR_STORE_INVALID,
                'form'
            );

            // @todo: Warning! A special case is a session which is invalid and stuck @ step 2,3,4 ... and not 1
            return false;
        }
        */

        // Get storage ...
        $storage = $this->getStorage($this->getStorageSkeleton());

        // 2nd step of all check if correct method was used for submission
        if (strtolower($this->getRequestMethod()) !== strtolower($storage['method'])) {
            $this->setError(
                // use array here -> I18n arguments as second key
                [
                    'error' => Doozr_Form_Service_Validate_Constant::ERROR_PREFIX.
                        Doozr_Form_Service_Validate_Constant::ERROR_REQUESTTYPE_INVALID,

                    'context' => [
                        'method' => ucfirst(
                            strtolower(
                                $this->getRequestMethod()
                            )
                        ),
                    ],
                ]
            );

            return false;
        }

        // 3rd step - validate token used for submit
        if (true !== $this->validateToken()) {
            $this->handleInvalidToken($storage);
            $this->setError(
                Doozr_Form_Service_Validate_Constant::ERROR_PREFIX.
                Doozr_Form_Service_Validate_Constant::ERROR_TOKEN_INVALID
            );

            return false;
        }

        // Get stored components
        /*
        $stored = $this->getStore()->read(Doozr_Form_Service_Constant::PREFIX.$this->getScope());

        // 4th step - iterate fields and check for individual error(s) if one found
        // either MISSING, BAD, INCORRECT DATA => FORM INVALID! and error = fields error message
        if (
            $this->validateComponents(
                $storage['components'],
                $step,
                $this->getArguments(),
                $stored
            ) !== true
        ) {
            $this->setError(
                Doozr_Form_Service_Validate_Constant::ERROR_PREFIX.
                Doozr_Form_Service_Validate_Constant::ERROR_ELEMENTS_INVALID
            );

            return false;
        }
        */

        // valid
        return true;
    }

    /**
     * Validates the passed components.
     *
     * This method is intend to validate the passed components.
     *
     * @param array                                     $components Components to validate from request
     * @param int                                       $step       Step currently active
     * @param array                                     $arguments  Arguments
     * @param array|\Doozr_Form_Service_Store_Interface $store      Store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if valid, otherwise FALSE if invalid
     */
    protected function validateComponents(array $components, $step = 1, $arguments = null, &$store = [])
    {
        $valid = true;

        foreach ($components as $component => $configuration) {

            // some components arrive with empty name ... [Could be a BUG in prser!]
            if ($component !== '') {

                // Not every component has a configration
                if (isset($configuration['validation'])) {

                    // Check for file upload
                    if (isset($configuration['type']) && $configuration['type'] === 'file') {
                        $isFile             = true;
                        $emulatedFileUpload = false;

                        $registry = Doozr_Registry::getInstance();
                        $registry->front->getRequest()->FILES();
                        $value = $_FILES[$component];

                        // Get storage ...
                        $storage = $this->getStorage($this->getStorageSkeleton());

                        if (isset($storage['data'][$component]) && $storage['data'][$component] !== null) {
                            $emulatedFileUpload = true;
                            $pathInfo           = pathinfo($storage['data'][$component]);

                            // emulate value
                            $value = [
                                'name'     => $pathInfo['basename'],
                                'type'     => $this->getMimeTypeByExtension($pathInfo['extension']),
                                'tmp_name' => $storage['data'][$component],
                                'error'    => '0',
                                'size'     => filesize($storage['data'][$component]),
                            ];
                        }

                        $check = $this->validateFileUpload($component, $value, $configuration['validation'], $storage);
                    } else {
                        $isFile = false;
                        $value  = isset($arguments->{$component}) ? $arguments->{$component} : null;
                        $check  = $this->validator->validate($value, $configuration['validation']);
                    }

                    // Here we: Check if the validator returned error in component
                    if ($check !== true) {
                        // The ERROR case!
                        $valid = $valid && false;

                        $this->setError($check, $component);

                        // ON ERROR -> REMOVE
                        $this->removeFromStorage('data', $component);
                    } else {
                        // COMMENT BLOCK REMOVE: The SUCCESS case!
                        if ($isFile === true && $emulatedFileUpload === false) {
                            $value = $this->handleFileUpload($value);
                        } elseif ($isFile === true) {
                            $value = $value['tmp_name'];
                        }

                        // ON SUCCESS -> ADD
                        $this->addToStorage($component, $value);
                    }
                }
            }
        }

        return $valid;
    }

    /**
     * Returns the mime-type of a file by its extension.
     *
     * @param string $extension The extension to use for lookup.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|string The mime-type as string if found, otherwise NULL
     */
    protected function getMimeTypeByExtension($extension)
    {
        $matrix = [
            'ez'      => 'application/andrew-inset',
            'hqx'     => 'application/mac-binhex40',
            'cpt'     => 'application/mac-compactpro',
            'doc'     => 'application/msword',
            'bin'     => 'application/octet-stream',
            'dms'     => 'application/octet-stream',
            'lha'     => 'application/octet-stream',
            'lzh'     => 'application/octet-stream',
            'exe'     => 'application/octet-stream',
            'class'   => 'application/octet-stream',
            'so'      => 'application/octet-stream',
            'dll'     => 'application/octet-stream',
            'oda'     => 'application/oda',
            'pdf'     => 'application/pdf',
            'ai'      => 'application/postscript',
            'eps'     => 'application/postscript',
            'ps'      => 'application/postscript',
            'smi'     => 'application/smil',
            'smil'    => 'application/smil',
            'wbxml'   => 'application/vnd.wap.wbxml',
            'wmlc'    => 'application/vnd.wap.wmlc',
            'wmlsc'   => 'application/vnd.wap.wmlscriptc',
            'bcpio'   => 'application/x-bcpio',
            'vcd'     => 'application/x-cdlink',
            'pgn'     => 'application/x-chess-pgn',
            'cpio'    => 'application/x-cpio',
            'csh'     => 'application/x-csh',
            'dcr'     => 'application/x-director',
            'dir'     => 'application/x-director',
            'dxr'     => 'application/x-director',
            'dvi'     => 'application/x-dvi',
            'spl'     => 'application/x-futuresplash',
            'gtar'    => 'application/x-gtar',
            'hdf'     => 'application/x-hdf',
            'js'      => 'application/x-javascript',
            'skp'     => 'application/x-koan',
            'skd'     => 'application/x-koan',
            'skt'     => 'application/x-koan',
            'skm'     => 'application/x-koan',
            'latex'   => 'application/x-latex',
            'nc'      => 'application/x-netcdf',
            'cdf'     => 'application/x-netcdf',
            'sh'      => 'application/x-sh',
            'shar'    => 'application/x-shar',
            'swf'     => 'application/x-shockwave-flash',
            'sit'     => 'application/x-stuffit',
            'sv4cpio' => 'application/x-sv4cpio',
            'sv4crc'  => 'application/x-sv4crc',
            'tar'     => 'application/x-tar',
            'tcl'     => 'application/x-tcl',
            'tex'     => 'application/x-tex',
            'texinfo' => 'application/x-texinfo',
            'texi'    => 'application/x-texinfo',
            't'       => 'application/x-troff',
            'tr'      => 'application/x-troff',
            'roff'    => 'application/x-troff',
            'man'     => 'application/x-troff-man',
            'me'      => 'application/x-troff-me',
            'ms'      => 'application/x-troff-ms',
            'ustar'   => 'application/x-ustar',
            'src'     => 'application/x-wais-source',
            'xhtml'   => 'application/xhtml+xml',
            'xht'     => 'application/xhtml+xml',
            'zip'     => 'application/zip',
            'au'      => 'audio/basic',
            'snd'     => 'audio/basic',
            'mid'     => 'audio/midi',
            'midi'    => 'audio/midi',
            'kar'     => 'audio/midi',
            'mpga'    => 'audio/mpeg',
            'mp2'     => 'audio/mpeg',
            'mp3'     => 'audio/mpeg',
            'aif'     => 'audio/x-aiff',
            'aiff'    => 'audio/x-aiff',
            'aifc'    => 'audio/x-aiff',
            'm3u'     => 'audio/x-mpegurl',
            'ram'     => 'audio/x-pn-realaudio',
            'rm'      => 'audio/x-pn-realaudio',
            'rpm'     => 'audio/x-pn-realaudio-plugin',
            'ra'      => 'audio/x-realaudio',
            'wav'     => 'audio/x-wav',
            'pdb'     => 'chemical/x-pdb',
            'xyz'     => 'chemical/x-xyz',
            'bmp'     => 'image/bmp',
            'gif'     => 'image/gif',
            'ief'     => 'image/ief',
            'jpeg'    => 'image/jpeg',
            'jpg'     => 'image/jpeg',
            'jpe'     => 'image/jpeg',
            'png'     => 'image/png',
            'tiff'    => 'image/tiff',
            'tif'     => 'image/tif',
            'djvu'    => 'image/vnd.djvu',
            'djv'     => 'image/vnd.djvu',
            'wbmp'    => 'image/vnd.wap.wbmp',
            'ras'     => 'image/x-cmu-raster',
            'pnm'     => 'image/x-portable-anymap',
            'pbm'     => 'image/x-portable-bitmap',
            'pgm'     => 'image/x-portable-graymap',
            'ppm'     => 'image/x-portable-pixmap',
            'rgb'     => 'image/x-rgb',
            'xbm'     => 'image/x-xbitmap',
            'xpm'     => 'image/x-xpixmap',
            'xwd'     => 'image/x-windowdump',
            'igs'     => 'model/iges',
            'iges'    => 'model/iges',
            'msh'     => 'model/mesh',
            'mesh'    => 'model/mesh',
            'silo'    => 'model/mesh',
            'wrl'     => 'model/vrml',
            'vrml'    => 'model/vrml',
            'css'     => 'text/css',
            'html'    => 'text/html',
            'htm'     => 'text/html',
            'asc'     => 'text/plain',
            'txt'     => 'text/plain',
            'rtx'     => 'text/richtext',
            'rtf'     => 'text/rtf',
            'sgml'    => 'text/sgml',
            'sgm'     => 'text/sgml',
            'tsv'     => 'text/tab-seperated-values',
            'wml'     => 'text/vnd.wap.wml',
            'wmls'    => 'text/vnd.wap.wmlscript',
            'etx'     => 'text/x-setext',
            'xml'     => 'text/xml',
            'xsl'     => 'text/xml',
            'mpeg'    => 'video/mpeg',
            'mpg'     => 'video/mpeg',
            'mpe'     => 'video/mpeg',
            'qt'      => 'video/quicktime',
            'mov'     => 'video/quicktime',
            'mxu'     => 'video/vnd.mpegurl',
            'avi'     => 'video/x-msvideo',
            'movie'   => 'video/x-sgi-movie',
            'ice'     => 'x-conference-xcooltalk',
        ];

        return (isset($matrix[$extension])) ? $matrix[$extension] : null;
    }

    /**
     * Validates a file upload.
     *
     * Prepares globals to validate a file upload afterwards.
     *
     * @param string         $name       The name of the fileupload field
     * @param array          $value      The value to check
     * @param array          $validation The validations for the file upload
     * @param Doozr_Registry $registry   The registry of Doozr
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if file upload is valid, otherwise FALSE
     */
    protected function validateFileUpload($name, $value, $validation, $registry)
    {
        $value['component'] = 'file';
        $_FILES->{$name}    = $value;

        $result = $this->validator->validate($value, $validation);

        return $result;
    }

    /**
     * Whats is this for ...
     *
     * @param array $file The file upload fields
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array|string
     *
     * @throws Doozr_Form_Service_Exception
     */
    protected function handleFileUpload($file)
    {
        /*
         * file was uploaded and successful validatet so store it's information in the same way as it would be done
         * for other components - but we need some modification on input data to receive this. some more information
         * right here in place.
         */
        $temporaryLocation = $file['tmp_name'];
        $filename          = $file['name'];

        $pathinfo      = pathinfo($temporaryLocation);
        $finalLocation = $pathinfo['dirname'].DIRECTORY_SEPARATOR.$filename;

        // move
        if (move_uploaded_file($temporaryLocation, $finalLocation) !== true) {
            throw new Doozr_Form_Service_Exception(
                'The uploaded file could not be moved from "'.$temporaryLocation.'" to "'.$finalLocation.'"'
            );
        }

        // return the final location here so that the service is able to store an information
        // about the "value" of the file component which could be easily transported from step to step
        return $finalLocation;
    }

    /**
     * Adds an component to internal registry with passed value.
     *
     * @param string $key   The name of the component
     * @param mixed  $value The value to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     */
    protected function addToStorage($key, $value)
    {
        // Get storage ...
        $storage = $this->getStorage($this->getStorageSkeleton());

        $storage['data'][$key] = $value;

        $this->setStorage($storage);

        return true;
    }

    /**
     * Handles an invalid token. Cause we have different behaviors to deal with an invalid token we need to check
     * which one was set and handle it.
     *
     * @param array $data The meta-information of the form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE om success, otherwise FALSE
     */
    protected function handleInvalidToken(array $data)
    {
        // get behavior configured in previous request
        $invalidTokenBehavior = $data['tokenbehavior'];

        // remove token
        if (isset($data['token'])) {
            unset($data['token']);
        }

        $this->getStore()->update(
            Doozr_Form_Service_Constant::PREFIX.$this->getScope(),
            $data
        );

        // check for configured behavior
        switch ($invalidTokenBehavior) {
            case Doozr_Form_Service_Constant::TOKEN_BEHAVIOR_IGNORE:
                $status = true;
                break;

            case Doozr_Form_Service_Constant::TOKEN_BEHAVIOR_INVALIDATE:
                $status = false;
                break;

            case Doozr_Form_Service_Constant::TOKEN_BEHAVIOR_DENY:
            default:
                $status = false;

                // Try to send correct 404 status ...
                try {
                    $header = 'HTTP/1.0 400 Bad Request';
                    header($header);
                } catch (Exception $e) {
                    // ... if this fails (header already sent) break execution - hard
                    echo $header;
                    exit;
                }
                break;
        }

        return $status;
    }

    /**
     * This method is intend to manage the token-logic. It checks if token was given as assumed and if it's valid.
     * It also removes used tokens from list of valid tokens and cancel requests without valid tokens.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if token could be validated (valid), otherwise FALSE
     */
    protected function validateToken()
    {
        // 1st get valid token from store
        try {
            $data = $this->getStore()->read(
                Doozr_Form_Service_Constant::PREFIX.$this->getScope()
            );
        } catch (Doozr_Form_Service_Exception $exception) {
            // Intentionally left empty
        }

        $validToken = (isset($data['token']) === true) ? $data['token'] : null;

        // 2nd get submitted token from request
        $submittedToken = $this->getSubmittedValue('Token', true);

        // check if submitted token is exactly the same as previously stored
        return ($submittedToken === $validToken) ? true : false;
    }

    /**
     * This method is intend to return the (sumitted) value of a requested request-variable.
     *
     * @param string $variable  The variable-name to get value from
     * @param mixed  $usePrefix True to use the Doozr_Form_Service_Constant::PREFIX for $variable
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed The value of the requested argument if set, otherwise NULL
     */
    protected function getSubmittedValue($variable, $usePrefix = false)
    {
        // assume result is NULL
        $result = null;

        // add Doozr prefix?
        if ($usePrefix) {
            $variable = Doozr_Form_Service_Constant::PREFIX.$variable;
        }

        $arguments = $this->getArguments();

        if (isset($arguments->{$variable})) {
            $result = $arguments->{$variable};
        }

        // and return the variables value
        return $result;
    }

    /**
     * Handles the data transfer from one request to another.
     * Important to keep the meta-data being transfered between
     * two separate requests.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function handleDataTransfer()
    {
        // Get storage
        $storage = $this->getStorage($this->getStorageSkeleton());

        // store important meta information about the current form
        $storage['components']    = $this->getComponents($this->form);
        $storage['method']        = $this->form->getMethod();
        $storage['step']          = $this->getStep();
        $storage['steps']         = $this->getSteps();
        $storage['token']         = $this->getToken();
        $storage['tokenbehavior'] = $this->getInvalidTokenBehavior();

        // Store
        $this->setStorage($storage);

        // send header
        $this->sendHeader();
    }

    /**
     * Sends header for service token transport.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function sendHeader()
    {
        // now send token via header for use in XHR & so on
        header('x-doozr-form-service-token: '.$this->getToken());
    }

    /**
     * Returns a registry skeleton for initializing store.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array An empty registry skeleton
     */
    protected function getStorageSkeleton()
    {
        // the default skeleton containing entries at the 1st level
        return [
            'data'          => [],
            'components'    => [],
            'method'        => null,
            'step'          => null,
            'steps'         => null,
            'token'         => null,
            'tokenbehavior' => null,
            'lastvalidstep' => 1,
        ];
    }

    /**
     * Returns the childs (components) from passed component.
     *
     * @param Doozr_Form_Service_Component_Interface_Form $component The component to return childs for
     * @param array                                       $result    The result used for recursion
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The result with childs
     */
    protected function getComponents($component, $result = [])
    {
        /*
         * Check if the component has any childs... Why do we exclude container components here? Easy to understand
         * we have currently only one component which is a container containing child elements
         * <select>
         *   <optgroup>
         *     <option></option>
         *   </optgroup>
         * </select>
         * So we would iterate its childs on the search for value & validation but this does not make sense. One
         * possible way for a good refactoring would be an interface and a check for instanceof ... So we would
         * exclude some elements by its interface or exclude others for their.
         */
        if (
            $component->hasChilds() === true &&
            $component->getType() !== Doozr_Form_Service_Constant::COMPONENT_CONTAINER
        ) {
            foreach ($component as $child) {
                $result = $this->getComponents($child, $result);
            }
        } elseif ($component->getName() !== null) {
            $result[$component->getName()] = [
                'validation' => $component->getValidation(),
                'type'       => $component->getType(),
            ];
        }

        return $result;
    }

    /**
     * Generates a token for the current form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function generateToken()
    {
        // get unique input
        $ip        = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $salt      = $this->getSalt();
        $scope     = $this->getScope();

        // generate token from unique input
        $this->setToken(
            md5($ip.$userAgent.$scope.$salt)
        );
    }

    /**
     * Returns a random seed-value.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string Random seed value
     */
    protected function getSalt()
    {
        srand(time());

        return md5(rand(0, 9999));
    }

    /**
     * Adds Meta-Information to the form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function addMetaFields()
    {
        // default service fields
        $this->addStepField();
        $this->addTokenField();
        $this->addStepsField();
        $this->addSubmittedField();

        /*
         * dynamic at runtime added fields
         * (added at last -> to be able to override service default behavior!)
         */
        foreach ($this->metaFields as $metaField) {
            $this->form->addChild($metaField);
        }
    }

    /**
     * Generic adding of form component/field.
     *
     * @param string $name  The name of the component
     * @param string $value The value of the component
     * @param string $type  The type of the component
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function addField($name, $value, $type = 'hidden')
    {
        $input = clone $this->getInputInstance();

        $input->setName($name);
        $input->setType($type);
        $input->setValue($value);

        $this->metaFields[] = $input;
    }

    /**
     * Adds a hidden field with the current step to the form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function addStepField()
    {
        $input = clone $this->getInputInstance();

        $input->setName(Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_STEP);
        $input->setType('hidden');
        $input->setValue($this->getStep());

        // Check for directive inject
        if ($this->getAngularDirectives() === true) {
            $input->setAttribute(
                'ng-model',
                Doozr_Form_Service_Constant::SCOPE.Doozr_Form_Service_Constant::PREFIX.
                Doozr_Form_Service_Constant::FORM_NAME_FIELD_STEP
            );

            $input->setAttribute(
                'value-transfer', $this->getScope()
            );
        }

        $this->form->addChild($input);
    }

    /**
     * Adds a hidden field with the count of steps to the form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function addStepsField()
    {
        $input = clone $this->getInputInstance();

        $input->setName(Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_STEPS);
        $input->setType('hidden');
        $input->setValue($this->getSteps());

        // Check for directive inject
        if ($this->getAngularDirectives() === true) {
            $input->setAttribute(
                'ng-model',
                Doozr_Form_Service_Constant::SCOPE.Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_STEPS
            );

            $input->setAttribute(
                'value-transfer', $this->getScope()
            );
        }

        $this->form->addChild($input);
    }

    /**
     * Adds a token field to the form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function addTokenField()
    {
        $input = clone $this->getInputInstance();

        $input->setName(Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_TOKEN);
        $input->setType('hidden');
        $input->setValue($this->getToken());

        // Check for directive inject
        if ($this->getAngularDirectives() === true) {
            $input->setAttribute(
                'ng-model',
                Doozr_Form_Service_Constant::SCOPE.Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_TOKEN
            );

            $input->setAttribute(
                'value-transfer', $this->getScope()
            );
        }

        $this->form->addChild($input);
    }

    /**
     * Adds the submitted field to the form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function addSubmittedField()
    {
        $input = clone $this->getInputInstance();

        $input->setName(Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_SUBMITTED);
        $input->setType('hidden');
        $input->setValue($this->getScope());

        // Check for directive inject
        if ($this->getAngularDirectives() === true) {
            $input->setAttribute(
                'ng-model',
                Doozr_Form_Service_Constant::SCOPE.Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_SUBMITTED
            );

            $input->setAttribute(
                'value-transfer', $this->getScope()
            );
        }

        $this->form->addChild($input);
    }
}
