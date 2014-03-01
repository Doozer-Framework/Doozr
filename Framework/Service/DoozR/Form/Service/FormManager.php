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
 * Copyright (c) 2005 - 2013, Benjamin Carl - All rights reserved.
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
 * @copyright  2005 - 2013 Benjamin Carl
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
 * @copyright  2005 - 2013 Benjamin Carl
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
     * @var DoozR_Form_Service_Element_Form
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
     * The renderer which finally takes the elements
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
     * @var DoozR_Form_Service_Element_Input
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


    /*-----------------------------------------------------------------------------------------------------------------*
    | General Functionality
    *-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param string                                    $namespace     The namespace to operate in/on (name for the form)
     * @param DoozR_I18n_Service_Interface              $i18n          The I18n Translator instance if required
     * @param DoozR_Form_Service_Element_Input          $inputInstance The input instance for cloning meta fields
     * @param DoozR_Form_Service_Element_Html_Interface $form          The Form instance (the main object)
     * @param DoozR_Form_Service_Store_Interface        $store         The Store instance
     * @param DoozR_Form_Service_Renderer_Interface     $renderer      The Renderer instance for rendering HTML e.g.
     * @param DoozR_Form_Service_Validate_Validator     $validator     The validator instance
     * @param DoozR_Form_Service_Validate_Error         $errorInstance The error instance for cloning error messages
     * @param DoozR_Request_Arguments                   $arguments     The Arguments passed with this request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_FormManager Instance
     * @access public
     */
    public function __construct(
        $namespace                                               = DoozR_Form_Service_Constant::DEFAULT_NAMESPACE,
        DoozR_I18n_Service_Interface              $i18n          = null,
        DoozR_Form_Service_Element_Input          $inputInstance = null,
        DoozR_Form_Service_Element_Html_Interface $form          = null,
        DoozR_Form_Service_Store_Interface        $store         = null,
        DoozR_Form_Service_Renderer_Interface     $renderer      = null,
        DoozR_Form_Service_Validate_Validator     $validator     = null,
        DoozR_Form_Service_Validate_Error         $errorInstance = null,
        DoozR_Request_Arguments                   $arguments     = null
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
     | Getter & Setter
     +----------------------------------------------------------------------------------------------------------------*/

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
        $fieldnameStep = DoozR_Form_Service_Constant::PREFIX.DoozR_Form_Service_Constant::FORM_NAME_FIELD_STEP;
        $fieldnameJump = DoozR_Form_Service_Constant::PREFIX.DoozR_Form_Service_Constant::FORM_NAME_FIELD_JUMP;

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
     +----------------------------------------------------------------------------------------------------------------*/

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

        return $this->form->render(true);
    }

    /**
     * Returns the submitted value for a passed fieldname or the default value if the field wasn't submitted not
     * submitted.
     *
     * @param string      $name    The name of the element to return data for
     * @param string|null $default The default return value as string or NULL
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The value of the element if exist, otherwise the $default value
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
     * @param string $identifier The (optional) identifier
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if the form is jumped to a specific step, otherwise FALSE
     * @access public
     */
    public function wasJumped()
    {
        // check already done?
        if ($this->wasJumped !== null) {
            return $this->wasJumped;

        } else {
            // get method used for submit
            $arguments = $this->getArguments();

            $fieldnameJump = DoozR_Form_Service_Constant::PREFIX.DoozR_Form_Service_Constant::FORM_NAME_FIELD_JUMP;

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
     * Returns the error of a element.
     *
     * @param string      $name    The name of the element to return data for
     * @param string|null $default The default return value as string or NULL
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The value of the element if exist, otherwise the $default value
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
     * @return mixed The
     * @throws DoozR_Form_Service_Exception
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
     * @param DoozR_I18n_Service $i18n The I18n instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setI18n(DoozR_I18n_Service $i18n = null)
    {
        $this->i18n = $i18n->getTranslator();
        $this->i18n->setNamespace(
            DoozR_Form_Service_Constant::PREFIX.$this->getNamespace()
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
     * @param DoozR_Form_Service_Element_Input $inputInstance The input element instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setInputInstance(DoozR_Form_Service_Element_Input $inputInstance)
    {
        $this->inputInstance = $inputInstance;
    }

    /**
     * Getter for InputInstance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|DoozR_Form_Service_Element_Input An DoozR_Form_Service_Element_Input instance, or NULL if not set
     * @access public
     */
    public function getInputInstance()
    {
        return $this->inputInstance;
    }

    /**
     * Setter for form.
     *
     * @param DoozR_Form_Service_Element_Html_Interface $form The form instance to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setForm(DoozR_Form_Service_Element_Html_Interface $form)
    {
        $this->form = $form;
    }

    /**
     * Getter for form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|DoozR_I18n_Service_Element_Html_Interface The form instance if set, otherwise NULL
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
        $this->_renderer = $renderer;
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
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The rendered HTML
     * @access public
     */
    public function __toString()
    {
        return $this->render();
    }

    /*-----------------------------------------------------------------------------------------------------------------*
    | Control layer
    *-----------------------------------------------------------------------------------------------------------------*/

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
            $fieldnameSubmissionStatus = DoozR_Form_Service_Constant::PREFIX.
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

                    $fieldnameStep  = DoozR_Form_Service_Constant::PREFIX.DoozR_Form_Service_Constant::FORM_NAME_FIELD_STEP;
                    $fieldnameSteps = DoozR_Form_Service_Constant::PREFIX.DoozR_Form_Service_Constant::FORM_NAME_FIELD_STEPS;

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
     * Returns the error status of an element.
     *
     * @param string $name The name of the element to return status for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string $name The name of the element
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
     * @param string $error       The error to set
     * @param string $elementName The element the error is related to
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setError($error, $elementName = 'form')
    {
        // Check for passed context - Some errors are passed without context as string
        if (is_array($error) === false) {
            $error = array(
                'error'   => $error,
                'context' => array(),                       // needs to be empty array for passing to I18n directly!
            );
        }

        if (!isset($this->error[$elementName])) {
            $this->error[$elementName] = array();
        }

        $this->error[$elementName][] = $error;
    }

    /**
     * Validates the current form (if submitted) and returns the valid
     * state of the form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if valid, otherwise FALSE if invalid
     * @access protected
     */
    protected function validate($step)
    {
        // check if store i still valid - session can be timed out ...
        if (!$this->validateRegistry()) {
            $this->setError(
                DoozR_Form_Service_Validate_Constant::ERROR_PREFIX.
                DoozR_Form_Service_Validate_Constant::STORE_INVALID,
                'form'
            );

            // @TODO: Warning! A special case is a session which is invalid and stuck @ step 2,3,4 ... and not 1
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
                    'error' => DoozR_Form_Service_Validate_Constant::ERROR_PREFIX.
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
                DoozR_Form_Service_Validate_Constant::ERROR_PREFIX.
                DoozR_Form_Service_Validate_Constant::TOKEN_INVALID
            );
            return false;
        }


        $stored = $this->getStore()->read(DoozR_Form_Service_Constant::PREFIX . $this->getNamespace());

        // 4th step - iterate fields and check for individual error(s) if one found
        // either MISSING, BAD, INCORRECT DATA => FORM INVALID! and error = fields error message
        if (
            $this->validateElements(
                $registry['elements'],
                $step,
                $this->getArguments(),
                $stored
            ) !== true
        ) {
            $this->setError(
                DoozR_Form_Service_Validate_Constant::ERROR_PREFIX.
                DoozR_Form_Service_Validate_Constant::ELEMENTS_INVALID
            );
            return false;
        }

        // valid
        return true;
    }

    /**
     * Validates the passed elements.
     *
     * This method is intend to validate the passed elements.
     *
     * @param array                              $elements  The elements to validate from request
     * @param integer                            $step      The step currently active
     * @param DoozR_Request_Arguments            $arguments The arguments
     * @param DoozR_Form_Service_Store_Interface $store     The store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if valid, otherwise FALSE if invalid
     * @access protected
     */
    protected function validateElements(array $elements, $step = 1, $arguments = array(), &$store = array())
    {
        $valid = true;

        foreach ($elements as $element => $configuration) {

            // some elements arrive with empty name ... [Could be a BUG in prser!]
            if ($element !== '') {

                // Not every element has a configration
                if (isset($configuration['validation'])) {

                    // file input check -> replace value by $_FILES
                    if (isset($configuration['type']) && $configuration['type'] === 'file') {

                        $isFile             = true;
                        $emulatedFileUpload = false;

                        $registry = DoozR_Registry::getInstance();
                        $registry->front->getRequest()->FILES();

                        $value = $_FILES[$element];
                        $value['element'] = 'file';
                        $_FILES->{$element} = $value;

                        // check for error and then try to get file information from registry
                        if (isset($value['error']) && $value['error'] === 4) {

                            $registry = $this->getRegistry(
                                $this->getRegistrySkeleton()
                            );

                            if (isset($registry['data'][$element]) && $registry['data'][$element] !== null) {

                                $emulatedFileUpload = true;

                                $value = array(
                                    'name' => 'MeinAuto.txt',
                                    'type' => 'text/plain',
                                    'tmp_name' => $registry['data'][$element],
                                    'error' => '0',
                                    'size' => filesize($registry['data'][$element]),
                                );
                            }
                        }

                        $check = $this->validator->validate($value, $configuration['validation']);

                    } else {
                        $isFile = false;
                        $value  = isset($arguments[$element]) ? $arguments[$element] : null;
                        $check  = $this->validator->validate($value, $configuration['validation']);
                    }

                    // Here we: Check if the validator returned error in element
                    if ($check !== true) {
                        // The ERROR case!
                        $valid = $valid && false;

                        $this->setError($check, $element);

                        // ON ERROR -> REMOVE
                        $this->removeFromRegistry('data', $element);

                    } else {
                        // COMMENT BLOCK REMOVE: The SUCCESS case!
                        if ($isFile === true && $emulatedFileUpload === false) {
                            $value = $this->handleFileUpload($value);

                        } elseif ($isFile === true) {
                            $value = $value['tmp_name'];
                        }

                        // ON SUCCESS _> ADD
                        $this->addToRegistry($element, $value);
                    }
                }
            }
        }

        return $valid;
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
         * for other elements - but we need some modification on input data to receive this. some more information
         * right here in place.
         */
        $temporaryLocation = $file['tmp_name'];
        $filename          = $file['name'];

        // get target path + name
        #$finalLocation = explode(DIRECTORY_SEPARATOR, $temporaryLocation);
        #array_pop($finalLocation);
        #array_push($finalLocation, $filename);
        #$finalLocation = implode(DIRECTORY_SEPARATOR, $finalLocation);

        $pathinfo = pathinfo($temporaryLocation);
        $finalLocation = $pathinfo['dirname'] . DIRECTORY_SEPARATOR . $filename;

        // move
        if (move_uploaded_file($temporaryLocation, $finalLocation) !== true) {
            throw new DoozR_Form_Service_Exception(
                'The uploaded file could not be moved from "'.$temporaryLocation.'" to "'.$finalLocation.'"'
            );
        }

        // return the final location here so that the service is able to store an information
        // about the "value" of the file element which could be easily transported from step to step
        return $finalLocation;
    }

    /**
     * Adds an element to internal registry with passed value
     *
     * @param string $key The name of the element
     * @param mixed  $value   The value to store
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
     * Removes an element from internal registry
     *
     * @param string $element The name of the element
     * @param mixed  $value   The value to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access protected
     */
    public function removeFromRegistry($key, $element = null)
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
        if ($element !== null) {
            unset($registry[$key][$element]);

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
            DoozR_Form_Service_Constant::PREFIX.$this->getNamespace()
        );

        if ($registry === null && $default !== null) {
            $registry = $default;
        }

        return $registry;
    }


    public function setRegistry($registry)
    {
        return $this->getStore()->create(
            DoozR_Form_Service_Constant::PREFIX.$this->getNamespace(),
            $registry
        );
    }


    public function invalidateRegistry()
    {
        return $this->setRegistry(null);
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
            DoozR_Form_Service_Constant::PREFIX.$this->getNamespace(),
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
            DoozR_Form_Service_Constant::PREFIX.$this->getNamespace()
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
            $variable = DoozR_Form_Service_Constant::PREFIX.$variable;
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
        $registry['elements']      = $this->getElements($this->form);
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


    protected function sendHeader()
    {
        // now send token via header for use in XHR & so on
        header('x-doozr-form-service-token: '.$this->getToken());
    }


    protected function getRegistrySkeleton()
    {
        // the default skeleton containing entries at the 1st level
        return array(
            'data'          => array(),
            'elements'      => array(),
            'method'        => null,
            'step'          => null,
            'steps'         => null,
            'token'         => null,
            'tokenbehavior' => null,
            'lastvalidstep' => 1,
        );
    }


    /**
     * Returns the childs (elements) from passed element.
     *
     * @param DoozR_Form_Service_Element_Interface $element The element to return childs for
     * @param array                                $result  The result used for recursion
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The result with childs
     * @access protected
     */
    protected function getElements($element, $result = array())
    {
        if (method_exists($element, 'getChilds')) {
            foreach ($element->getChilds() as $child) {
                if (is_array($child)) {
                    foreach ($child as $subChild) {
                        $result = $this->getElements($subChild, $result);
                    }
                } else {
                    $result = $this->getElements($child, $result);
                }
            }
        } else {
            $result[$element->getName()] = array(
                'validation' => $element->getValidation(),
                'type'       => $element->getType()
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
        $ip           = $_SERVER['REMOTE_ADDR'];
        $userAgent    = $_SERVER['HTTP_USER_AGENT'];
        $salt         = $this->getSalt();
        $name         = $this->getNamespace();

        // generate token from unqiue input
        $this->setToken(md5($ip.$userAgent.$name.$salt));
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
            $this->form->add($metaField);
        }
    }

    /**
     * Generic adding of form element/field.
     *
     * @param string $name  The name of the element
     * @param string $value The value of the element
     * @param string $type  The type of the element
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
        $input->setName(DoozR_Form_Service_Constant::PREFIX.DoozR_Form_Service_Constant::FORM_NAME_FIELD_STEP);
        $input->setType('hidden');
        $input->setValue($this->getStep());

        $this->form->add($input);
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
        $input->setName(DoozR_Form_Service_Constant::PREFIX.DoozR_Form_Service_Constant::FORM_NAME_FIELD_STEPS);
        $input->setType('hidden');
        $input->setValue($this->getSteps());

        $this->form->add($input);
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
        $input->setName(DoozR_Form_Service_Constant::PREFIX.DoozR_Form_Service_Constant::FORM_NAME_FIELD_TOKEN);
        $input->setType('hidden');
        $input->setValue($this->getToken());

        $this->form->add($input);
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
        $input->setName(DoozR_Form_Service_Constant::PREFIX.DoozR_Form_Service_Constant::FORM_NAME_FIELD_SUBMITTED);
        $input->setType('hidden');
        $input->setValue($this->getNamespace());

        $this->form->add($input);
    }
}
