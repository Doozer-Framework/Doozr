<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Form - Service
 *
 * FormManager.php - This class is the outer container which collects
 * a form and all its childs and adds the control layer to it and so
 * on!
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2014, Benjamin Carl - All rights reserved.
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
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

/**
 * DoozR - Form - Service
 *
 * This class is the outer container which collects
 * a form and all its childs and adds the control layer to it and so
 * on!
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Form_Service_FormManager
{
    /**
     * The namespace of this instance. This must
     * be unqiue cause of storage prefix usage ...
     *
     * @var string
     * @access protected
     */
    protected $namespace = DoozR_Form_Service_Constant::DEFAULT_NAMESPACE;

    /**
     * Contains the Form instance and all of its
     * childs and so on ...
     *
     * @var DoozR_Form_Service_Component_Form
     * @access protected
     */
    public $form;

    /**
     * The translator used for translating all types of text within this service.
     *
     * @var DoozR_I18n_Service_Translator
     * @access protected
     */
    protected $i18n;

    /**
     * Jump status of current request. True = we jumped through a deeplink.
     * False = no jump
     *
     * @var boolean
     * @access protected
     */
    protected $wasJumped;

    /**
     * The token of the form
     *
     * @var string
     * @access protected
     */
    protected $token;

    /**
     * The errors of the form.
     *
     * @var array
     * @access protected
     */
    protected $error = array();

    /**
     * The store where our data resides while moving from one page request to the next.
     * Some sort of temporary persistence.
     *
     * @var DoozR_Form_Service_Store_Interface
     * @access protected
     */
    protected $store;

    /**
     * The renderer which finally takes the components
     * and render them to something really useful.
     *
     * @var DoozR_Form_Service_Renderer_Interface
     * @access protected
     */
    protected $renderer;

    /**
     * META: The maximum allowed filesize. This can be set via
     * setter but should not be touched cause this is filled by
     * the <input type="file" ...> class on instanciation with
     * the value from PHP's ini.
     *
     * @var integer
     * @access protected
     */
    protected $maxFileSize;

    /**
     * The submission status of the form
     *
     * @var boolean TRUE = submitted, otherwise FALSE
     * @access protected
     */
    protected $submitted;

    /**
     * The valid status of the form.
     *
     * @var boolean TRUE = valid, otherwise FALSE
     */
    protected $valid;

    /**
     * The finish/completion status of the form.
     *
     * @var boolean TRUE = complete, otherwise FALSE
     */
    protected $complete;

    /**
     * The step we're currently on.
     *
     * @var integer
     * @access protected
     */
    protected $step = DoozR_Form_Service_Constant::STEP_DEFAULT_FIRST;

    /**
     * The last (final) step
     *
     * @var integer
     * @access protected
     */
    protected $steps = DoozR_Form_Service_Constant::STEP_DEFAULT_LAST;

    /**
     * The behavior when an invalid token arrives
     *
     * @var integer
     * @access protected
     */
    protected $invalidTokenBehavior;

    /**
     * The argument passed with current request
     *
     * @var object
     * @access protected
     */
    protected $arguments;

    /**
     * Collection of META fields
     *
     * @var array
     * @access protected
     */
    protected $metaFields = array();

    /**
     * Validator instance for validation.
     *
     * @var DoozR_Form_Service_Validate_Validator
     * @access protected
     */
    protected $validator;

    /**
     * Input instance for cloning.
     *
     * @var DoozR_Form_Service_Component_Input
     * @access protected
     */
    protected $inputInstance;

    /**
     * Error instance for cloning.
     *
     * @var DoozR_Form_Service_Validate_Error
     * @access protected
     */
    protected $errorInstance;


    /**
     * Constructor.
     *
     * @param string                                      $namespace     The namespace to operate in/on (name of form)
     * @param DoozR_I18n_Service_Interface                $i18n          The I18n Translator instance if required
     * @param DoozR_Form_Service_Component_Input          $inputInstance The input instance for cloning meta fields
     * @param DoozR_Form_Service_Component_Interface_Form $form          The Form instance (the main object)
     * @param DoozR_Form_Service_Store_Interface          $store         The Store instance
     * @param DoozR_Form_Service_Renderer_Interface       $renderer      The Renderer instance for rendering HTML e.g.
     * @param DoozR_Form_Service_Validate_Validator       $validator     The validator instance
     * @param DoozR_Form_Service_Validate_Error           $errorInstance The error instance for cloning error messages
     * @param DoozR_Request_Arguments                     $arguments     The Arguments passed with this request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_FormManager Instance
     * @access public
     */
    public function __construct(
        $namespace = DoozR_Form_Service_Constant::DEFAULT_NAMESPACE,
        DoozR_I18n_Service_Interface $i18n = null,
        DoozR_Form_Service_Component_Input $inputInstance = null,
        DoozR_Form_Service_Component_Interface_Form $form = null,
        DoozR_Form_Service_Store_Interface $store = null,
        DoozR_Form_Service_Renderer_Interface $renderer = null,
        DoozR_Form_Service_Validate_Validator $validator = null,
        DoozR_Form_Service_Validate_Error $errorInstance = null,
        DoozR_Request_Arguments $arguments = null
    ) {
        // Store instances for further use
        $this->setNamespace($namespace);
        $this->setI18n($i18n);
        $this->setInputInstance($inputInstance);
        $this->setForm($form);
        $this->setStore($store);
        $this->setRenderer($renderer);
        $this->setValidator($validator);
        $this->setErrorInstance($errorInstance);
        $this->setArguments($arguments);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for namespace.
     *
     * @param string $namespace The namespace to use for this instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Getter for namespace.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The namespace
     * @access public
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Setter for step.
     *
     * @param integer $step The step
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setStep($step = null)
    {
        // only detect if not passed -> if passed then override forced
        if ($step === null) {

            // check submission status
            if ($this->wasSubmitted()) {

                // get current request
                $arguments = $this->getArguments();

                $fieldnameStep = DoozR_Form_Service_Constant::PREFIX . 'Step';

                // try to get from last submit -> fallback to default of service
                $step = (isset($arguments->{$fieldnameStep})) ?
                    $arguments->{$fieldnameStep} :
                    DoozR_Form_Service_Constant::STEP_DEFAULT_FIRST;

                // increment by 1 if form was submitted! valid! and not complete yet!
                $step += ($this->isValid($step)) ? 1 : 0;

            } else {
                // if form wasn't submitted > its default state is step 1
                $step = DoozR_Form_Service_Constant::STEP_DEFAULT_FIRST;
            }
        }

        $this->step = $step;
    }

    /**
     * Getter for step.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The current step
     * @access public
     */
    public function getStep()
    {
        // assume first step = default
        $step = 1;

        // get request from front-controller
        $submittedData = $this->getArguments();

        $registry = $this->getRegistry(
            $this->getRegistrySkeleton()
        );

        // build fieldname by pattern
        $fieldnameStep = DoozR_Form_Service_Constant::PREFIX . DoozR_Form_Service_Constant::FORM_NAME_FIELD_STEP;
        $fieldnameJump = DoozR_Form_Service_Constant::PREFIX . DoozR_Form_Service_Constant::FORM_NAME_FIELD_JUMP;

        // and now check if submission identifier exists in current request
        if (
            isset($submittedData->{$fieldnameStep}) &&
            $submittedData[$fieldnameStep] <= $registry['lastvalidstep']
        ) {
            $step = $submittedData[$fieldnameStep];

        } elseif (
            isset($submittedData[$fieldnameJump]) &&
            $submittedData[$fieldnameJump] <= $registry['lastvalidstep']
        ) {
            $step = $submittedData[$fieldnameJump];

        }

        if ($this->wasSubmitted() && $this->isValid($step)) {
            $step += 1;
            $registry['lastvalidstep'] = $step;
            $this->setRegistry($registry);
        }

        return (int)$step;
    }

    /**
     * Setter for steps.
     *
     * @param integer $steps The steps
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setSteps($steps = DoozR_Form_Service_Constant::STEP_DEFAULT_LAST)
    {
        $this->steps = $steps;
    }

    /**
     * Getter for steps.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The current steps
     * @access public
     */
    public function getSteps()
    {
        return (int)$this->steps;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Renders the forms after the preparation is done. This method does generate a token for the current form
     * add all meta fields required for validation and so on, handles the data transfer from request to the next
     * and finally it renders the HTML and returns a valid form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The generated HTML output
     * @access public
     */
    public function render()
    {
        // important to generate token first!
        $this->generateToken();
        $this->addMetaFields();
        $this->handleDataTransfer();

        return $this->form->render(true)->get();
    }

    /**
     * Returns the submitted value for a passed fieldname or the default value if the field wasn't submitted not
     * submitted.
     *
     * @param string      $name    The name of the component to return data for
     * @param string|null $default The default return value as string or NULL
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The value of the component if exist, otherwise the $default value
     * @access public
     */
    public function getValue($name, $default = null)
    {
        /**
         * WHERE No matter whre from look in step registry last! cause for stepping support
         */
        $value = $this->getSubmittedValue($name);

        // if no value was retrieved
        if ($value === null) {
            // now try to retrieve from registry (store) instead (maybe we're jumped over here)
            $registry = $this->getRegistry(
                $this->getRegistrySkeleton()
            );

            if (isset($registry['data'][$name])) {
                $value = $registry['data'][$name];
            }
        }

        if ($value === null && $default !== null) {
            $value = $default;
        }

        return $value;
    }

    /**
     * Returns TRUE if the form is jumped to a specific step,
     * otherwise FALSE
     *
     * @internal param string $identifier The (optional) identifier
     *
     * @author   Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if the form is jumped to a specific step, otherwise FALSE
     * @access   public
     */
    public function wasJumped()
    {
        // check already done?
        if ($this->wasJumped !== null) {
            return $this->wasJumped;

        } else {
            // get method used for submit
            $arguments = $this->getArguments();

            $fieldnameJump = DoozR_Form_Service_Constant::PREFIX . DoozR_Form_Service_Constant::FORM_NAME_FIELD_JUMP;

            // and now check if submission identifier exists in current request
            if (isset($arguments->{$fieldnameJump})) {
                $this->wasJumped = $arguments[$fieldnameJump];

            } else {
                $this->wasJumped = false;

            }
        }

        // submission status
        return $this->wasJumped;
    }

    /**
     * Returns the error of a component.
     *
     * @param string      $name    The name of the component to return data for
     * @param string|null $default The default return value as string or NULL
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The value of the component if exist, otherwise the $default value
     * @access public
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
     * @param string $string The string to translate
     *
     * @param array  $arguments
     *
     * @throws DoozR_Form_Service_Exception
     * @return mixed The
     */
    public function translate($string, array $arguments = array())
    {
        if ($this->getI18n() === null) {
            throw new DoozR_Form_Service_Exception(
                'Please set an instance of DoozR_I18n_Service (or compatible) first before calling translate()'
            );
        }

        return $this->i18n->_($string, $arguments);
    }

    /**
     * Setter for I18n Translator service.
     *
     * @param DoozR_I18n_Service_Interface $i18n The I18n instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setI18n(DoozR_I18n_Service_Interface $i18n = null)
    {
        $this->i18n = $i18n->getTranslator();
        $this->i18n->setNamespace(
            DoozR_Form_Service_Constant::PREFIX . $this->getNamespace()
        );
    }

    /**
     * Getter for I18n Translator service.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|DoozR_I18n_Service An I18n Translator service instance, or NULL if not set
     * @access public
     */
    public function getI18n()
    {
        return $this->i18n;
    }

    /**
     * Setter for inputInstance.
     *
     * @param DoozR_Form_Service_Component_Input $inputInstance The input component instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setInputInstance(DoozR_Form_Service_Component_Input $inputInstance)
    {
        $this->inputInstance = $inputInstance;
    }

    /**
     * Getter for InputInstance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|DoozR_Form_Service_Component_Input An DoozR_Form_Service_Component_Input instance, or NULL if not set
     * @access public
     */
    public function getInputInstance()
    {
        return $this->inputInstance;
    }

    /**
     * Setter for form.
     *
     * @param DoozR_Form_Service_Component_Interface_Form $form The form instance to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setForm(DoozR_Form_Service_Component_Interface_Form $form)
    {
        $this->form = $form;
    }

    /**
     * Getter for form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|DoozR_I18n_Service_Component_Html_Interface The form instance if set, otherwise NULL
     * @access public
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Returns the token of this form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @param $token
     *
     * @return string The token of this form
     * @access public
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Returns the token of this form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The token of this form
     * @access public
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Setter for store.
     *
     * @param DoozR_Form_Service_Store_Interface $store The store to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setStore(DoozR_Form_Service_Store_Interface $store)
    {
        $this->store = $store;
    }

    /**
     * Getter for store.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Store_Interface|null Instance or NULL if not set
     * @access public
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * Setter for renderer.
     *
     * @param DoozR_Form_Service_Renderer_Interface $renderer The renderer used for rendering the whole form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setRenderer(DoozR_Form_Service_Renderer_Interface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Getter for renderer.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Renderer_Interface|null Instance or NULL if not set
     * @access public
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * Setter for validator.
     *
     * @param DoozR_Form_Service_Validate_Validator $validator The validator instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setValidator(DoozR_Form_Service_Validate_Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Getter for validator.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|DoozR_Form_Service_Validate_Validator The validator instance if set, otherwise NULL
     * @access public
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Setter for Error-Instance.
     *
     * @param DoozR_Form_Service_Validate_Error $errorInstance The error instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setErrorInstance(DoozR_Form_Service_Validate_Error $errorInstance)
    {
        $this->errorInstance = $errorInstance;
    }

    /**
     * Getter for Error-Instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|DoozR_Form_Service_Validate_Error The errorInstance if set, otherwise NULL
     * @access public
     */
    public function getErrorInstance()
    {
        return $this->errorInstance;
    }

    /**
     * Setter for arguments.
     *
     * @param DoozR_Request_Arguments $arguments The arguments to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * Getter for arguments.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|DoozR_Request_Arguments The DoozR_Request_Arguments as object if set, otherwise NULL
     * @access public
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Sets the behavior for the case that an invalid token is used for submission.
     *
     * @param integer $behavior The behavior to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setInvalidTokenBehavior($behavior)
    {
        $this->invalidTokenBehavior = $behavior;
    }

    /**
     * Returns the current active behavior for invalid tokens.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The behavior as integer
     * @access public
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
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @return string The rendered HTML
     * @access  public
     */
    public function __toString()
    {
        return $this->render();
    }

    /*-----------------------------------------------------------------------------------------------------------------*
    | Control layer
    *-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Removes an component from internal registry
     *
     * @param        $key
     * @param string $component The name of the component
     *
     * @throws DoozR_Form_Service_Exception
     * @internal param mixed $value The value to store
     *
     * @author   Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access   protected
     */
    public function removeFromRegistry($key, $component = null)
    {
        $registry = $this->getRegistry(
            $this->getRegistrySkeleton()
        );

        if (!isset($registry[$key])) {
            throw new DoozR_Form_Service_Exception(
                'Could not remove unexisting key: "' . $key . '" from registry.'
            );
        }

        //
        if ($component !== null) {
            unset($registry[$key][$component]);

        } else {
            unset($registry[$key]);

        }

        $this->setRegistry(
            $registry
        );

        return true;
    }

    public function getRegistry($default = null)
    {
        $registry = $this->getStore()->read(
            DoozR_Form_Service_Constant::PREFIX . $this->getNamespace()
        );

        if ($registry === null && $default !== null) {
            $registry = $default;
        }

        return $registry;
    }

    public function setRegistry($registry)
    {
        return $this->getStore()->create(
            DoozR_Form_Service_Constant::PREFIX . $this->getNamespace(),
            $registry
        );
    }

    /**
     * Invalidates the whole registry
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function invalidateRegistry()
    {
        return $this->setRegistry(null);
    }

    /**
     * Checks and returns the submission status of this form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if the form was submitted, otherwise FALSE
     * @access public
     */
    public function wasSubmitted()
    {
        // check already done?
        if ($this->submitted !== null) {
            $submitted = $this->submitted;

        } else {
            // assume not submitted
            $submitted = false;

            // get request from front-controller
            $submittedData = $this->getArguments();

            // build fieldname by pattern
            $fieldnameSubmissionStatus = DoozR_Form_Service_Constant::PREFIX .
                DoozR_Form_Service_Constant::FORM_NAME_FIELD_SUBMITTED;

            // and now check if submission identifier exists in current request
            if (
                isset($submittedData->{$fieldnameSubmissionStatus}) &&
                $submittedData[$fieldnameSubmissionStatus] === $this->namespace
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
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @param int $step
     *
     * @return boolean TRUE is the form is valid, otherwise FALSE
     * @access public
     */
    public function isValid($step = 1)
    {
        // check already done?
        if ($this->valid !== null) {
            $valid = $this->valid;

        } else {
            // check for submission
            if ($this->wasSubmitted() === true) {
                $valid = $this->validate($step);

            } else {
                // assume status valid if not submitted
                $valid = true;
            }

            // store valid status for further faster accessing
            $this->valid = $valid;
        }

        return $valid;
    }

    /**
     * Returns the completion status of the form. If this method returns TRUE then the form steps are
     * completed, otherwise it would return FALSE.
     *
     * @param integer $steps The count of steps to check against
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if form steps are complete, otherwise FALSE
     * @access public
     */
    public function isComplete($steps = 1)
    {
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

                    $fieldnameStep  = DoozR_Form_Service_Constant::PREFIX . DoozR_Form_Service_Constant::FORM_NAME_FIELD_STEP;
                    $fieldnameSteps = DoozR_Form_Service_Constant::PREFIX . DoozR_Form_Service_Constant::FORM_NAME_FIELD_STEPS;

                    $step = (isset($submittedData[$fieldnameStep])) ?
                        $submittedData[$fieldnameStep] :
                        $this->step;

                    $steps = (isset($submittedData[$fieldnameSteps])) ?
                        $submittedData[$fieldnameSteps] :
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
     * @return string $name The name of the component
     * @access protected
     */
    protected function hasError($name)
    {
        return isset($this->error[$name]);
    }

    /**
     * Validates the store.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE is the store is valid, otherwise FALSE
     * @access protected
     */
    protected function validateRegistry()
    {
        return ($this->getRegistry() !== null);
    }

    /**
     * Sets an error. Default to form.
     *
     * @param string $error         The error to set
     * @param string $componentName The component the error is related to
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setError($error, $componentName = 'form')
    {
        // Check for passed context - Some errors are passed without context as string
        if (is_array($error) === false) {
            $error = array(
                'error'   => $error,
                'context' => array(), // needs to be empty array for passing to I18n directly!
            );
        }

        if (!isset($this->error[$componentName])) {
            $this->error[$componentName] = array();
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
     * @return boolean TRUE if valid, otherwise FALSE if invalid
     * @access protected
     */
    protected function validate($step)
    {
        // check if store i still valid - session can be timed out ...
        if (!$this->validateRegistry()) {
            $this->setError(
                DoozR_Form_Service_Validate_Constant::ERROR_PREFIX .
                DoozR_Form_Service_Validate_Constant::STORE_INVALID,
                'form'
            );

            // @todo: Warning! A special case is a session which is invalid and stuck @ step 2,3,4 ... and not 1
            return false;
        }

        // get store data
        $registry = $this->getRegistry(
            $this->getRegistrySkeleton()
        );

        // 2nd step of all check if correct method was used for submission
        if ($this->getArguments()->getSource() !== $registry['method']) {
            $this->setError(
            // use array here -> I18n arguments as seoond key
                array(
                    'error'   => DoozR_Form_Service_Validate_Constant::ERROR_PREFIX .
                        DoozR_Form_Service_Validate_Constant::REQUESTTYPE_INVALID,
                    'context' => array(
                        'method' => ucfirst(strtolower($this->getArguments()->getSource()))
                    )
                )
            );
            return false;
        }

        // 3rd step - validate token used for submit
        if ($this->validateToken() !== true) {
            $this->handleInvalidToken($registry);
            $this->setError(
                DoozR_Form_Service_Validate_Constant::ERROR_PREFIX .
                DoozR_Form_Service_Validate_Constant::TOKEN_INVALID
            );
            return false;
        }

        // Get stored components
        $stored = $this->getStore()->read(DoozR_Form_Service_Constant::PREFIX . $this->getNamespace());

        // 4th step - iterate fields and check for individual error(s) if one found
        // either MISSING, BAD, INCORRECT DATA => FORM INVALID! and error = fields error message
        if (
            $this->validateComponents(
                $registry['components'],
                $step,
                $this->getArguments(),
                $stored
            ) !== true
        ) {
            $this->setError(
                DoozR_Form_Service_Validate_Constant::ERROR_PREFIX .
                DoozR_Form_Service_Validate_Constant::ELEMENTS_INVALID
            );
            return false;
        }

        // valid
        return true;
    }

    /**
     * Validates the passed components.
     *
     * This method is intend to validate the passed components.
     *
     * @param array                                     $components The components to validate from request
     * @param integer                                   $step       The step currently active
     * @param array|\DoozR_Request_Arguments            $arguments  The arguments
     * @param array|\DoozR_Form_Service_Store_Interface $store      The store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if valid, otherwise FALSE if invalid
     * @access protected
     */
    protected function validateComponents(array $components, $step = 1, $arguments = array(), &$store = array())
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

                        $registry = DoozR_Registry::getInstance();
                        $registry->front->getRequest()->FILES();
                        $value = $_FILES[$component];

                        $registry = $this->getRegistry(
                            $this->getRegistrySkeleton()
                        );

                        if (isset($registry['data'][$component]) && $registry['data'][$component] !== null) {
                            $emulatedFileUpload = true;
                            $pathInfo           = pathinfo($registry['data'][$component]);

                            // emulate value
                            $value = array(
                                'name'     => $pathInfo['basename'],
                                'type'     => $this->getMimeTypeByExtension($pathInfo['extension']),
                                'tmp_name' => $registry['data'][$component],
                                'error'    => '0',
                                'size'     => filesize($registry['data'][$component]),
                            );
                        }

                        $check = $this->validateFileUpload($component, $value, $configuration['validation'], $registry);

                    } else {
                        $isFile = false;
                        $value  = isset($arguments[$component]) ? $arguments[$component] : null;
                        $check  = $this->validator->validate($value, $configuration['validation']);
                    }

                    // Here we: Check if the validator returned error in component
                    if ($check !== true) {
                        // The ERROR case!
                        $valid = $valid && false;

                        $this->setError($check, $component);

                        // ON ERROR -> REMOVE
                        $this->removeFromRegistry('data', $component);

                    } else {
                        // COMMENT BLOCK REMOVE: The SUCCESS case!
                        if ($isFile === true && $emulatedFileUpload === false) {
                            $value = $this->handleFileUpload($value);

                        } elseif ($isFile === true) {
                            $value = $value['tmp_name'];
                        }

                        // ON SUCCESS -> ADD
                        $this->addToRegistry($component, $value);
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
     * @return null|string The mime-type as string if found, otherwise NULL
     * @access protected
     */
    protected function getMimeTypeByExtension($extension)
    {
        $matrix = array(
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
            'ice'     => 'x-conference-xcooltalk'
        );

        return (isset($matrix[$extension])) ? $matrix[$extension] : null;
    }

    /**
     * Validates a file upload
     *
     * Prepares globals to validate a file upload afterwards.
     *
     * @param string         $name       The name of the fileupload field
     * @param array          $value      The value to check
     * @param array          $validation The validations for the file upload
     * @param DoozR_Registry $registry   The registry of DoozR
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if file upload is valid, otherwise FALSE
     * @access protected
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
     * @return array|string
     * @access protected
     * @throws DoozR_Form_Service_Exception
     */
    protected function handleFileUpload($file)
    {
        /**
         * file was uploaded and successful validatet so store it's information in the same way as it would be done
         * for other components - but we need some modification on input data to receive this. some more information
         * right here in place.
         */
        $temporaryLocation = $file['tmp_name'];
        $filename          = $file['name'];

        $pathinfo      = pathinfo($temporaryLocation);
        $finalLocation = $pathinfo['dirname'] . DIRECTORY_SEPARATOR . $filename;

        // move
        if (move_uploaded_file($temporaryLocation, $finalLocation) !== true) {
            throw new DoozR_Form_Service_Exception(
                'The uploaded file could not be moved from "' . $temporaryLocation . '" to "' . $finalLocation . '"'
            );
        }

        // return the final location here so that the service is able to store an information
        // about the "value" of the file component which could be easily transported from step to step
        return $finalLocation;
    }

    /**
     * Adds an component to internal registry with passed value
     *
     * @param string $key   The name of the component
     * @param mixed  $value The value to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access protected
     */
    protected function addToRegistry($key, $value)
    {
        $registry = $this->getRegistry(
            $this->getRegistrySkeleton()
        );

        $registry['data'][$key] = $value;

        $this->setRegistry(
            $registry
        );

        return true;
    }

    /**
     * Handles an invalid token. Cause we have different behaviors
     * to deal with an invalid token we need to check which one was
     * set and handle it.
     *
     * @param array $data The meta-information of the form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function handleInvalidToken(array $data)
    {
        // assume that token is valid - all other possible ...
        $status = true;

        // get behavior configured in previous request
        $invalidTokenBehavior = $data['tokenbehavior'];

        // remove token
        if (isset($data['token'])) {
            unset($data['token']);
        }

        $this->getStore()->update(
            DoozR_Form_Service_Constant::PREFIX . $this->getNamespace(),
            $data
        );

        // check for configured behavior
        switch ($invalidTokenBehavior) {
            case DoozR_Form_Service_Constant::TOKEN_BEHAVIOR_IGNORE:
                $status = true;
                break;

            case DoozR_Form_Service_Constant::TOKEN_BEHAVIOR_INVALIDATE:
                $status = false;
                break;

            case DoozR_Form_Service_Constant::TOKEN_BEHAVIOR_DENY:
            default:
                $status = false;

                // try to send correct 404 status ...
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

        // and return the status
        return $status;
    }

    /**
     * This method is intend to manage the token-logic. It checks if token was given as assumed and if it's valid.
     * It also removes used tokens from list of valid tokens and cancel requests without valid tokens.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if token could be validated (valid), otherwise FALSE
     * @access protected
     */
    protected function validateToken()
    {
        // 1st get valid token from store
        $data = $this->getStore()->read(
            DoozR_Form_Service_Constant::PREFIX . $this->getNamespace()
        );

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
     * @param mixed  $usePrefix True to use the DoozR_Form_Service_Constant::PREFIX for $variable
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The value of the requested argument if set, otherwise NULL
     * @access protected
     */
    protected function getSubmittedValue($variable, $usePrefix = false)
    {
        // assume result is NULL
        $result = null;

        // add DoozR prefix?
        if ($usePrefix) {
            $variable = DoozR_Form_Service_Constant::PREFIX . $variable;
        }

        $arguments = $this->getArguments();

        if (isset($arguments[$variable])) {
            $result = $arguments[$variable];
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
     * @return void
     * @access protected
     */
    protected function handleDataTransfer()
    {
        // hole registry
        $registry = $this->getRegistry(
            $this->getRegistrySkeleton()
        );

        // store important meta information about the current form
        $registry['components']    = $this->getComponents($this->form);
        $registry['method']        = $this->form->getMethod();
        $registry['step']          = $this->getStep();
        $registry['steps']         = $this->getSteps();
        $registry['token']         = $this->getToken();
        $registry['tokenbehavior'] = $this->getInvalidTokenBehavior();

        // store
        $this->setRegistry($registry);

        // send header
        $this->sendHeader();
    }

    /**
     * Sends header for service token transport
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function sendHeader()
    {
        // now send token via header for use in XHR & so on
        header('x-doozr-form-service-token: ' . $this->getToken());
    }

    /**
     * Returns a registry skeleton for initializing store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array An empty registry skeleton
     * @access protected
     */
    protected function getRegistrySkeleton()
    {
        // the default skeleton containing entries at the 1st level
        return array(
            'data'          => array(),
            'components'    => array(),
            'method'        => null,
            'step'          => null,
            'steps'         => null,
            'token'         => null,
            'tokenbehavior' => null,
            'lastvalidstep' => 1,
        );
    }

    /**
     * Returns the childs (components) from passed component.
     *
     * @param DoozR_Form_Service_Component_Interface_Form $component The component to return childs for
     * @param array                                       $result    The result used for recursion
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The result with childs
     * @access protected
     */
    protected function getComponents($component, $result = array())
    {
        /**
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
            $component->getType() !== DoozR_Form_Service_Constant::COMPONENT_CONTAINER
        ) {

            foreach ($component as $child) {
                $result = $this->getComponents($child, $result);
            }
        } elseif ($component->getName() !== null) {
            $result[$component->getName()] = array(
                'validation' => $component->getValidation(),
                'type'       => $component->getType(),
            );
        }

        return $result;
    }

    /**
     * Generates a token for the current form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function generateToken()
    {
        // get unique input
        $ip        = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $salt      = $this->getSalt();
        $name      = $this->getNamespace();

        // generate token from unqiue input
        $this->setToken(
            md5($ip . $userAgent . $name . $salt)
        );
    }

    /**
     * Returns a random seed-value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Random seed value
     * @access protected
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
     * @return void
     * @access protected
     */
    protected function addMetaFields()
    {
        // default service fields
        $this->addStepField();
        $this->addTokenField();
        $this->addStepsField();
        $this->addSubmittedField();

        /**
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
     * @return void
     * @access protected
     */
    protected function addField($name, $value, $type = 'hidden')
    {
        $input = clone $this->inputInstance;

        $input->setName($name);
        $input->setType($type);
        $input->setValue($value);

        $this->metaFields[] = $input;
    }

    /**
     * Adds a hidden field with the current step to the form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function addStepField()
    {
        $input = clone $this->inputInstance;
        $input->setName(DoozR_Form_Service_Constant::PREFIX . DoozR_Form_Service_Constant::FORM_NAME_FIELD_STEP);
        $input->setType('hidden');
        $input->setValue($this->getStep());

        $this->form->addChild($input);
    }

    /**
     * Adds a hidden field with the count of steps to the form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function addStepsField()
    {
        $input = clone $this->inputInstance;
        $input->setName(DoozR_Form_Service_Constant::PREFIX . DoozR_Form_Service_Constant::FORM_NAME_FIELD_STEPS);
        $input->setType('hidden');
        $input->setValue($this->getSteps());

        $this->form->addChild($input);
    }

    /**
     * Adds a token field to the form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function addTokenField()
    {
        $input = clone $this->inputInstance;
        $input->setName(DoozR_Form_Service_Constant::PREFIX . DoozR_Form_Service_Constant::FORM_NAME_FIELD_TOKEN);
        $input->setType('hidden');
        $input->setValue($this->getToken());

        $this->form->addChild($input);
    }

    /**
     * Adds the submitted field to the form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function addSubmittedField()
    {
        $input = clone $this->inputInstance;
        $input->setName(DoozR_Form_Service_Constant::PREFIX . DoozR_Form_Service_Constant::FORM_NAME_FIELD_SUBMITTED);
        $input->setType('hidden');
        $input->setValue($this->getNamespace());

        $this->form->addChild($input);
    }
}
