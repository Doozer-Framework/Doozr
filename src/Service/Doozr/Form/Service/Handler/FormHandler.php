<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service - Handler - FormHandler.
 *
 * FormHandler.php - Container of a form and it children. It adds the control (meta) layer to it, handles the stepping & validation ...
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

use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Doozr - Form - Service - Handler - FormHandler.
 *
 * Container of a form and all its children. It adds the control (meta) layer to it, handles the stepping & validation ...
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
class Doozr_Form_Service_Handler_FormHandler extends Doozr_Base_Class
    implements Doozr_Form_Service_Handler_Interface
{
    /**
     * Data pool for storing and sharing data between requests.
     *
     * @var array
     */
    protected $dataPool;

    /**
     * Submission status of the form.
     * TRUE = submitted, FALSE = not submitted.
     *
     * @var bool
     */
    protected $submitted = Doozr_Form_Service_Constant::DEFAULT_SUBMITTED;

    /**
     * The scope of this instance (should be unique).
     *
     * @var string
     */
    protected $scope;

    /**
     * Result of inquiry (Form process).
     *
     * @var array
     */
    protected $result;

    /**
     * Valid status of the form.
     * TRUE = valid, FALSE = invalid.
     *
     * @var bool
     */
    protected $valid;

    /**
     * The finish/completion status of the form.
     *
     * @var bool TRUE = complete, otherwise FALSE
     */
    protected $complete = Doozr_Form_Service_Constant::DEFAULT_COMPLETE_STATUS;

    /**
     * Token for securing transportation of the form.
     *
     * @var string
     */
    protected $token;

    /**
     * Request method used for transportation.
     *
     * @var string|null
     */
    protected $method;

    /**
     * META: The maximum allowed filesize. This can be set via setter but should not be touched cause this is filled by
     * the <input type="file" ...> class on instantiation with the value from PHP's ini.
     *
     * @var int
     */
    protected $maxFileSize = Doozr_Form_Service_Constant::DEFAULT_MAX_UPLOAD_FILESIZE;

    /**
     * The step we're currently on.
     *
     * @var int
     */
    protected $step;

    /**
     * Active step we must process (validation, store, etc).
     *
     * @var int
     */
    public $activeStep;

    /**
     * The last (final) step.
     *
     * @var int
     */
    protected $steps;

    /**
     * Whether the FormHandler's active scope has file upload elements or not.
     *
     * @var bool
     */
    protected $upload;

    /**
     * The behavior when an invalid token arrives.
     *
     * @var int
     */
    protected $invalidTokenBehavior = Doozr_Form_Service_Constant::DEFAULT_TOKEN_BEHAVIOR;

    /**
     * Whether to inject angular model directives for hidden fields.
     *
     * @example If set to TRUE hidden fields will get a ng-model="" directive automagically injected bound to the name
     *          of the hidden field: <input type="hidden" name="send" ng-model="send" />
     *
     * @var bool
     */
    protected $angularDirectives = false;

    /**
     * Collection of META fields.
     *
     * @var array
     */
    protected $metaComponents = [];

    /**
     * Jumped status of current request.
     *
     * @var bool|int FALSE if not jumped, otherwise step from jump as int
     */
    protected $jumped = Doozr_Form_Service_Constant::DEFAULT_JUMPED;

    /**
     * The store where our data resides while moving from one page request to the next.
     * Some sort of temporary persistence.
     *
     * @var Doozr_Form_Service_Store_Interface
     */
    protected $store;

    /**
     * Uploaded files.
     *
     * @var array
     */
    protected $files;

    /**
     * The renderer which finally takes the components and render them to something really useful.
     *
     * @var Doozr_Form_Service_Renderer_Interface
     */
    protected $renderer;

    /**
     * Contains the Form instance and all of its children and ...
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
     * Validator instance for validation.
     *
     * @var Doozr_Form_Service_Handler_ValidationHandler
     */
    protected $validationHandler;

    /**
     * File upload handler (handles uploaded files [PSR-7] and/or pool data as source when jumped).
     *
     * @var Doozr_Form_Service_Handler_FileUploadHandler
     */
    protected $fileUploadHandler;

    /**
     * Token handler.
     *
     * @var Doozr_Form_Service_Handler_TokenHandler
     */
    protected $tokenHandler;

    /**
     * DotNotation Accessor.
     *
     * @var Doozr_Form_Service_Accessor_DotNotation
     */
    protected $dotNotationAccessor;

    /**
     * Registry.
     *
     * @var Doozr_Registry
     */
    protected $registry;

    /**
     * PSR Request.
     *
     * @var Request
     */
    protected $request;

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
     * META: Name of "upload" field.
     *
     * @var string
     */
    protected $fieldnameUpload;

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param string                                       $scope               Scope to operate on (name of form).
     * @param Doozr_Registry                               $registry            Registry instance (??? only Di !!!).
     * @param Doozr_I18n_Service_Interface                 $i18n                I18n translator instance.
     * @param Doozr_Form_Service_Component_Interface_Form  $form                Form instance.
     * @param Doozr_Form_Service_Store_Interface           $store               Store instance.
     * @param Doozr_Form_Service_Renderer_Interface        $renderer            Renderer instance (e.g. HTML-output).
     * @param Doozr_Form_Service_Accessor_DotNotation      $dotNotationAccessor For dot notation access to array
     * @param Doozr_Form_Service_Handler_ValidationHandler $validationHandler   Validation handler instance.
     * @param Doozr_Form_Service_Handler_FileUploadHandler $fileUploadHandler   File upload handler (upload & pool).
     * @param Doozr_Form_Service_Handler_TokenHandler      $tokenHandler        Token handler for generating & security.
     * @param Doozr_Form_Service_Handler_DataHandler       $dataHandler         Responsible for ALL data management!
     * @param Request                                      $request             Request instance (PSR).
     * @param string                                       $method              Request method for transportation.
     * @param bool                                         $angularDirectives   Whether to render angular directives.
     * @param array                                        $metaComponentNames  Collection of key value pairs with names
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __construct(
                                                     $scope = Doozr_Form_Service_Constant::DEFAULT_SCOPE,
        Doozr_Registry                               $registry = null,
        Doozr_I18n_Service_Interface                 $i18n = null,
        Doozr_Form_Service_Component_Interface_Form  $form = null,
        Doozr_Form_Service_Store_Interface           $store = null,
        Doozr_Form_Service_Renderer_Interface        $renderer = null,
        Doozr_Form_Service_Accessor_DotNotation      $dotNotationAccessor = null,
        Doozr_Form_Service_Handler_ValidationHandler $validationHandler = null,
        Doozr_Form_Service_Handler_FileUploadHandler $fileUploadHandler = null,
        Doozr_Form_Service_Handler_TokenHandler      $tokenHandler = null,
        Doozr_Form_Service_Handler_DataHandler       $dataHandler = null,
        Request                                      $request = null,
                                                     $method = Doozr_Form_Service_Constant::DEFAULT_METHOD,
                                                     $angularDirectives = false,
        array                                        $metaComponentNames
    ) {
        // Store instances for further use
        $this
            ->scope($scope)
            ->registry($registry)
            ->i18n($i18n)
            ->form($form)
            ->store($store)
            ->renderer($renderer)
            ->dotNotationAccessor($dotNotationAccessor)
            ->validationHandler($validationHandler)
            ->fileUploadHandler($fileUploadHandler)
            ->tokenHandler($tokenHandler)
            ->dataHandler($dataHandler)
            ->request($request)
            ->method($method)
            ->angularDirectives($angularDirectives)
            ->fieldnameToken($metaComponentNames[Doozr_Form_Service_Constant::IDENTIFIER_TOKEN])
            ->fieldnameSubmitted($metaComponentNames[Doozr_Form_Service_Constant::IDENTIFIER_SUBMITTED])
            ->fieldnameStep($metaComponentNames[Doozr_Form_Service_Constant::IDENTIFIER_STEP])
            ->fieldnameSteps($metaComponentNames[Doozr_Form_Service_Constant::IDENTIFIER_STEPS])
            ->fieldnameJump($metaComponentNames[Doozr_Form_Service_Constant::IDENTIFIER_JUMP])
            ->fieldnameUpload($metaComponentNames[Doozr_Form_Service_Constant::IDENTIFIER_UPLOAD])
            ->init();
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Init state by request or passed in override values.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function init()
    {
        // Status
        $processed = false;

        // Receive information from: Store, Request and process it to a valid state representation

        // We can do the following without any side effects ...
        //   a) Load data pool from session - fall back to default one for first call
        //   b) Generate a fresh token used at least for next/first render()
        //   c) Detect if the form was in any kind submitted to here - maybe through a satellite form entry

        $this
            ->initializeDataPool()
            ->submitted($this->isASubmittedFormInRequest())
            ->jumped($this->receiveJumpFieldValueFromRequest());

        // We can now check for submitted data which can either control the flow (e.g. JUMP) or required to correctly bootstrap the form
        if (true === $this->wasSubmitted()) {

            // Collect data from request cause we have one!
            $step   = (int) $this->receiveStepFieldValueFromRequest();
            $steps  = (int) $this->receiveStepsFieldValueFromRequest();
            $token  = $this->receiveTokenFieldValueFromRequest();
            $upload = (int) $this->receiveUploadFieldValueFromRequest();
            $valid  = true;

            // When submitted we must have also this information! So we can use to check this as well
            $stepFromDataPool  = (int) $this->getDataPoolValue(Doozr_Form_Service_Constant::IDENTIFIER_STEP);
            $stepsFromDataPool = (int) $this->getDataPoolValue(Doozr_Form_Service_Constant::IDENTIFIER_STEPS);
            $lastValidStep     = (int) $this->getDataPoolValue(Doozr_Form_Service_Constant::IDENTIFIER_LASTVALIDSTEP);

            // Adjust by following rule:
            // On a switch from one step to the next we need to adjust the step
            $step  = ($stepFromDataPool > $step)   ? $stepFromDataPool  : $step;
            $steps = ($stepsFromDataPool > $steps) ? $stepsFromDataPool : $steps;

            // If validation now fails for basic manipulation protection fallback to default
            if (
                $step                        <=  0                    ||    // ~ e.g. no valid step found
                $steps                       <=  0                    ||    // ~ e.g. no valid steps limit/end found
                null                         === $token               ||    // ~ e.g. no token submitted
                $step                        >   $steps               ||    // ~ e.g. stepped over goal
                ($lastValidStep              === 0      && $step > 1) ||    // ~ e.g. If no last valid step but where in a step > 1
                (abs($lastValidStep - $step) !=  1      && $step > 1)       // ~ e.g. More than 1 step diff is only allowed for jump
            ) {
                $valid = false;
            }

            if (true === $valid) {
                // Store the detected values for further processing
                $this->setStep($step);
                $this->setSteps($steps);
                $this->setToken($token);
                $this->setUpload($upload);

                // Check for form has file upload (in general not on current request limited) - and getMetaComponents it ...
                if (true === $this->hasFileUpload()) {
                    // ... getMetaComponents in this context is to ensure that we can't
                    /* @todo DI refactor to DI */
                    $fileUploadHandler = new Doozr_Form_Service_Handler_FileUploadHandler();

                    // Retrieve files for this step (either from pool or fresh uploaded)
                    $files = $fileUploadHandler->getUploadedFiles(
                        $step,
                        $this->getDataPool(),
                        $this->getRequest()->getUploadedFiles()
                    );
                } else {
                    $files = [];
                }

                // Store files ...
                $this->setFiles($files);





                // Extract components for validation in this steps
                $validationRules = $this->getDataPool()[Doozr_Form_Service_Constant::IDENTIFIER_COMPONENTS][$step];

                // Manually inject validation for method
                $validationRules[Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::IDENTIFIER_METHOD] = [
                    'validation' => ['value' => [$this->getDataPool()[Doozr_Form_Service_Constant::IDENTIFIER_METHOD]]],
                    'type'       => 'generic',
                ];

                $submittedValues = $this->buildValueArray(
                    array_keys($validationRules)
                );

                $submittedValues[Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::IDENTIFIER_METHOD] = $this->getRequest()->getMethod();


                // Manually inject validation for token
                $validationRules[Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::IDENTIFIER_TOKEN] = [
                    'validation' => ['value' => [$this->getDataPool()[Doozr_Form_Service_Constant::IDENTIFIER_TOKEN]]],
                    'type'       => 'generic',
                ];

                $submittedValues[Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::IDENTIFIER_TOKEN] = $this->getToken();

                // Run validation by using validation handler ...
                $this->setValid(
                    $this->getValidationHandler()->validate(
                        $submittedValues,
                        $validationRules
                    )
                );


                /**
                 * Required to handle
                 *
                 * Token-Behavior -> comes from history
                 * $invalidTokenBehavior = $this->getDataPoolValue(Doozr_Form_Service_Constant::IDENTIFIER_TOKENBEHAVIOR)
                 *
                 */
                $invalidTokenBehavior = $this->getDataPoolValue(Doozr_Form_Service_Constant::IDENTIFIER_TOKENBEHAVIOR);
                $tokenValid           = $this->getError(
                    Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::IDENTIFIER_TOKEN,
                    true
                );

                // Handle outsourced to token handler
                $this->getTokenHandler()->handleToken(
                    $invalidTokenBehavior,
                    $tokenValid
                );

                // Transfer valid data ...
                $this->transferValidDataToPool($step);

                // If valid we must increase active step to the next one :)
                if (true === $this->isValid()) {
                    // We only accept completion of forms on a submit. This is a condition which prevent us from
                    // running into raise conditions with jumped forms which are valid ... and it makes the rule easy:
                    $this->setComplete($step === $steps);

                    if (true === $this->isComplete()) {
                        $this->setResult(
                            $this->getDataHandler()->enrichWithMetaInformation(
                                $this->invalidateDataPool()
                            )
                        );
                    }

                    $this->setActiveStep($step + 1);
                } else {
                    $this->setActiveStep($step);
                }

                $processed = true;
            }

            // We can also check if form was not submitted - maybe it was jumped to this place!!!
            // The we must! have a valid data pool entry for each single element and restore the values from there to emulate a valid request
        } elseif (true === $this->wasJumped()) {

            // Collect data from store ...
            $step   = (int) $this->getDataPoolDataValue($this->getJumped(), $this->getFieldnameStep());
            $steps  = (int) $this->getDataPoolDataValue($this->getJumped(), $this->getFieldnameSteps());
            $token  = $this->getDataPoolDataValue($this->getJumped(), $this->getFieldnameToken());
            $data   = $this->getDataPoolValue(Doozr_Form_Service_Constant::IDENTIFIER_DATA);
            $upload = (int) $this->getDataPoolValue(Doozr_Form_Service_Constant::IDENTIFIER_UPLOAD);
            $valid  = true;

            // If validation now fails for basic manipulation protection fallback to default
            if ($step === 0 || $steps === 0 || null === $token || true === empty($data)) {
                $valid = false;
            }

            // At this point the basic validation must be done!

            if (true === $valid) {
                // Store the detected values for further processing
                $this->setStep($step);
                $this->setSteps($steps);
                $this->setToken($token);
                $this->setUpload($upload);

                // We could now call the validation. But why? We validated that the call to here is correct (validation)
                // and the data we would now validation ir already validated. The request method will probably fail cause
                // we jumped via ?...Step=x to here ;) So we do not validation again!
                $this->setValid(true);
                $this->setComplete(false);
                $this->setActiveStep($step);

                $processed = true;
            }
        }

        // If neither submit nor successful jump step back to defaults!
        if (false === $processed) {
            $step       = Doozr_Form_Service_Constant::DEFAULT_STEP_FIRST;
            $activeStep = $step;
            $steps      = Doozr_Form_Service_Constant::DEFAULT_STEP_FIRST;
            $upload     = Doozr_Form_Service_Constant::DEFAULT_HAS_FILE_UPLOAD;

            // Store the detected values for further processing
            $this->setStep($step);
            $this->setActiveStep($activeStep);
            $this->setSteps($steps);
            $this->setUpload($upload);
            $this->setFiles([]);
            $this->setValid(false);
            $this->setComplete(false);
        }

        return $this;
    }


    /**
     * Returns built value array indexed by component name.
     *
     * @param array $componentNames Name of components to build array for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array Values indexed by component name
     */
    protected function buildValueArray(array $componentNames)
    {
        $result = [];

        foreach ($componentNames as $componentName) {
            $result[$componentName] = $this->getValue($componentName);
        };

        return $result;
    }

    /**
     * Initializes the data pool by creating an initial pool based on a default skeleton.
     *
     * @return $this Instance for chaining
     */
    protected function initializeDataPool()
    {
        // Start a session
        // @TODO: Remove by store!
        session_start();

        if (false === isset($_SESSION[$this->addPrefix($this->getScope())])) {
            $dataPool                                      = $this->getDataPoolSkeleton();
            $_SESSION[$this->addPrefix($this->getScope())] = $dataPool;
        } else {
            $dataPool = $_SESSION[$this->addPrefix($this->getScope())];
        }

        return $this->dataPool($dataPool);
    }

    /**
     * Transfers data from request for a passed step to store. This happens mostly after validation.
     *
     * @param int $step Step of data to transfer
     *
     * @return $this Instance for chaining
     */
    protected function transferValidDataToPool($step)
    {
        // Get active pool
        $pool = $this->getDataPool();

        if (null === $pool) {
            throw new Doozr_Form_Service_Exception(
                'Pool is invalid. Need either to be loaded first or a handler to prevent invalid call'
            );
        }

        // We prevent execution above and so we assume that components is set without further checks
        $components = $pool[Doozr_Form_Service_Constant::IDENTIFIER_COMPONENTS][$step];
        $data       = [];

        foreach ($components as $componentName => $configuration) {
            if (false === $this->hasError($componentName)) {
                $data[$componentName] = $this->getValue($componentName);
            }
        }

        $pool[Doozr_Form_Service_Constant::IDENTIFIER_DATA][$step] = $data;
        $this->setDataPool($pool);

        return $this;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | SETTER & GETTER
    +-----------------------------------------------------------------------------------------------------------------*/

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
    protected function token($token)
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
    protected function getToken()
    {
        return $this->token;
    }

    protected function setUpload($upload)
    {
        $this->upload = $upload;
    }

    protected function upload($upload)
    {
        $this->setUpload($upload);

        return $this;
    }

    protected function getUpload()
    {
        return $this->upload;
    }

    /**
     * Setter for activeStep.
     *
     * @param int $step The step to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setActiveStep($step)
    {
        $this->activeStep = $step;
    }

    /**
     * Fluent: Setter for activeStep.
     *
     * @param int $activeStep Value of activeStep.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function activeStep($activeStep)
    {
        $this->setActiveStep($activeStep);

        return $this;
    }

    /**
     * Setter for dataPool.
     *
     * @param array|null $pool Value for dataPool to set, NULL to reset (e.g.)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setDataPool(array $pool = null)
    {
        $this->dataPool = $pool;
    }

    /**
     * Fluent: Setter for dataPool.
     *
     * @param array $pool Value for dataPool to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function dataPool(array $pool)
    {
        $this->setDataPool($pool);

        return $this;
    }

    /**
     * Getter for dataPool.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array|null Value of dataPool
     */
    protected function getDataPool()
    {
        return $this->dataPool;
    }

    /**
     * Setter for result.
     *
     * @param array $result Value for result
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setResult(array $result)
    {
        $this->result = $result;
    }

    /**
     * Fluent: Setter for result.
     *
     * @param array $result Value for result
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function result(array $result)
    {
        $this->setResult($result);

        return $this;
    }

    /**
     * Setter for form.
     *
     * @param Doozr_Form_Service_Component_Interface_Form $form The form instance to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setForm(Doozr_Form_Service_Component_Interface_Form $form = null)
    {
        // Check for scope inject requirement
        if (null !== $form && null === $form->getName()) {
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
    protected function form(Doozr_Form_Service_Component_Interface_Form $form = null)
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
     * Setter for validationHandler.
     *
     * @param Doozr_Form_Service_Handler_ValidationHandler $validationHandler The validationHandler instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setValidationHandler(Doozr_Form_Service_Handler_ValidationHandler $validationHandler = null)
    {
        $this->validationHandler = $validationHandler;
    }

    /**
     * Fluent: Setter for validationHandler.
     *
     * @param Doozr_Form_Service_Handler_ValidationHandler $validator The validationHandler instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function validationHandler(Doozr_Form_Service_Handler_ValidationHandler $validator = null)
    {
        $this->setValidationHandler($validator);

        return $this;
    }

    /**
     * Setter for dotNotationAccessor.
     *
     * @param Doozr_Form_Service_Accessor_DotNotation $dotNotationAccessor Value for dotNotationAccessor
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setDotNotationAccessor(Doozr_Form_Service_Accessor_DotNotation $dotNotationAccessor)
    {
        $this->dotNotationAccessor = $dotNotationAccessor;
    }

    /**
     * Fluent: Setter for dotNotationAccessor.
     *
     * @param Doozr_Form_Service_Accessor_DotNotation $dotNotationAccessor Value for dotNotationAccessor
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function dotNotationAccessor(Doozr_Form_Service_Accessor_DotNotation $dotNotationAccessor)
    {
        $this->setDotNotationAccessor($dotNotationAccessor);

        return $this;
    }

    /**
     * Getter for dotNotationAccessor.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Form_Service_Accessor_DotNotation
     */
    protected function getDotNotationAccessor()
    {
        return $this->dotNotationAccessor;
    }

    /**
     * Getter for validationHandler.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|Doozr_Form_Service_Handler_ValidationHandler The validationHandler instance if set, otherwise NULL
     */
    protected function getValidationHandler()
    {
        return $this->validationHandler;
    }

    /**
     * Setter for fileUploadHandler.
     *
     * @param Doozr_Form_Service_Handler_FileUploadHandler $fileUploadHandler The fileUploadHandler instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setFileUploadHandler(Doozr_Form_Service_Handler_FileUploadHandler $fileUploadHandler = null)
    {
        $this->fileUploadHandler = $fileUploadHandler;
    }

    /**
     * Fluent: Setter for fileUploadHandler.
     *
     * @param Doozr_Form_Service_Handler_FileUploadHandler $fileUploadHandler The fileUploadHandler instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function fileUploadHandler(Doozr_Form_Service_Handler_FileUploadHandler $fileUploadHandler = null)
    {
        $this->setFileUploadHandler($fileUploadHandler);

        return $this;
    }

    /**
     * Getter for fileUploadHandler.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|Doozr_Form_Service_Handler_FileUploadHandler The fileUploadHandler instance if set, otherwise NULL
     */
    protected function getFileUploadHandler()
    {
        return $this->fileUploadHandler;
    }

    /**
     * Setter for tokenHandler.
     *
     * @param Doozr_Form_Service_Handler_TokenHandler $tokenHandler The tokenHandler instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setTokenHandler(Doozr_Form_Service_Handler_TokenHandler $tokenHandler = null)
    {
        $this->tokenHandler = $tokenHandler;
    }

    /**
     * Fluent: Setter for tokenHandler.
     *
     * @param Doozr_Form_Service_Handler_TokenHandler $tokenHandler The tokenHandler instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function tokenHandler(Doozr_Form_Service_Handler_TokenHandler $tokenHandler = null)
    {
        $this->setTokenHandler($tokenHandler);

        return $this;
    }

    /**
     * Getter for tokenHandler.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|Doozr_Form_Service_Handler_TokenHandler The tokenHandler instance if set, otherwise NULL
     */
    protected function getTokenHandler()
    {
        return $this->tokenHandler;
    }

    /**
     * Setter for complete.
     *
     * @param bool $complete The valid
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setComplete($complete)
    {
        $this->complete = $complete;
    }

    /**
     * Fluent: Setter for complete.
     *
     * @param bool $complete The valid
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function complete($complete)
    {
        $this->setComplete($complete);

        return $this;
    }

    /**
     * Getter for complete.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool Complete status
     */
    protected function getComplete()
    {
        return $this->complete;
    }

    /**
     * Setter for valid.
     *
     * @param bool $valid The valid
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setValid($valid)
    {
        $this->valid = $valid;
    }

    /**
     * Setter for valid.
     *
     * @param bool $valid The valid
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function valid($valid)
    {
        $this->setValid($valid);

        return $this;
    }

    /**
     * Getter for valid.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool The valid
     */
    protected function getValid()
    {
        return $this->valid;
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

    protected function setFieldnameUpload($fieldnameUpload)
    {
        $this->fieldnameUpload = $fieldnameUpload;
    }

    protected function fieldnameUpload($fieldnameUpload)
    {
        $this->setFieldnameUpload($fieldnameUpload);

        return $this;
    }

    protected function getFieldnameUpload()
    {
        return $this->fieldnameUpload;
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
     * Setter for request.
     *
     * @param Request|null $request Request instance (PSR)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    /**
     * Fluent: Setter for request.
     *
     * @param Request|null $request Request instance (PSR)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function request(Request $request = null)
    {
        $this->setRequest($request);

        return $this;
    }

    /**
     * Getter for request.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Request|null Request instance (PSR) if set, otherwise NULL
     */
    protected function getRequest()
    {
        return $this->request;
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
     * Setter for method.
     *
     * @param string $method Value for method.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Fluent: Setter for method.
     *
     * @param string $method Value for method.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function method($method)
    {
        $this->setMethod($method);

        return $this;
    }

    /**
     * Getter for method.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string|null Method if set, otherwise NULL
     */
    protected function getMethod()
    {
        return $this->method;
    }

    /**
     * Setter for submitted.
     *
     * @param bool $submitted Value for submitted.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setSubmitted($submitted)
    {
        $this->submitted = $submitted;
    }

    /**
     * Fluent: Setter for submitted.
     *
     * @param bool $submitted Value for submitted.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function submitted($submitted)
    {
        $this->setSubmitted($submitted);

        return $this;
    }

    /**
     * Getter for submitted.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if current form was submitted with current request, otherwise FALSE
     */
    protected function getSubmitted()
    {
        return $this->submitted;
    }

    /**
     * Setter for store.
     *
     * @param Doozr_Form_Service_Store_Interface $store The store to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setStore(Doozr_Form_Service_Store_Interface $store)
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
    protected function store(Doozr_Form_Service_Store_Interface $store)
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
    protected function getStore()
    {
        return $this->store;
    }

    /**
     * Setter for files.
     *
     * @param array $files The files to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setFiles(array $files)
    {
        $this->files = $files;
    }

    /**
     * Fluent: Setter for files.
     *
     * @param array $files The files to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function files(array $files)
    {
        $this->setFiles($files);

        return $this;
    }

    /**
     * Getter for files.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array|null Instance or NULL if not set
     */
    protected function getFiles()
    {
        return $this->files;
    }

    /**
     * Setter for jumped.
     *
     * @param bool|int $jumped FALSE if not jumped, otherwise step from jump as int
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setJumped($jumped)
    {
        $this->jumped = $jumped;
    }

    /**
     * Setter for jumped.
     *
     * @param bool|int $jumped FALSE if not jumped, otherwise step from jump as int
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function jumped($jumped)
    {
        $this->setJumped($jumped);

        return $this;
    }

    /**
     * Getter for jumped.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool|int FALSE if not jumped, otherwise step from jump as int
     */
    public function getJumped()
    {
        return $this->jumped;
    }

    /**
     * Setter for renderer.
     *
     * @param Doozr_Form_Service_Renderer_Interface $renderer The renderer used for rendering the whole form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setRenderer(Doozr_Form_Service_Renderer_Interface $renderer = null)
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
    protected function renderer(Doozr_Form_Service_Renderer_Interface $renderer = null)
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
    protected function getRenderer()
    {
        return $this->renderer;
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
     * Setter for I18n Translator service.
     *
     * @param Doozr_I18n_Service_Interface $i18n The I18n instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setI18n(Doozr_I18n_Service_Interface $i18n = null)
    {
        // Check if I18n Service was passed ...
        if (null !== $i18n) {
            try {
                /* @var Doozr_I18n_Service $i18n */
                $i18n->addDomain($this->getScope());
                $i18n->addDomain('default');
            } catch (Doozr_I18n_Service_Exception $exception) {
                // Intentionally do nothing
            }

            $this->i18n = $i18n;
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
    protected function i18n(Doozr_I18n_Service_Interface $i18n = null)
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
    protected function getI18n()
    {
        return $this->i18n;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

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
        // Verify that we do not make the mistake of braking session when debugging with echoes!
        $file = '';
        $line = '';

        if (true === headers_sent($file, $line)) {
            throw new Doozr_Form_Service_Exception(
                sprintf(
                    'If any output is sent before render() was successfully called '.
                    'then transportation of token can not be guaranteed! File %s Line %s',
                    $file,
                    $line
                )
            );
        }

        // Transfer required data transparently to form and it components so the developer doesn't need to but can.
        // We use the POST as default and accept GET for the step 1 in general as well.
        // So the user does not need to adjust the transport method but he can. So if we got null on getMethod()
        // when rendering, we should inject the POST if we got a result, the dev probably has configured some
        // transport and we will use this value instead.
        $requestMethod = $this->getForm()->getMethod();
        if (null !== $requestMethod) {
            $this->setMethod($requestMethod);
        } else {
            $this->getForm()->setMethod($this->getMethod());
        }

        // Generate a fresh token, store all the meta information and ensure that storage is up to date!
        $this
            ->token($this->getTokenHandler()->generateToken())
            ->addMetaComponents()
            ->handleBackgroundDataTransfer();

        // Return the result from rendering (depends on renderer passed in)
        return $this->getForm()->render()->get();
    }

    /**
     * Getter for step.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int Value of step
     */
    public function getActiveStep()
    {
        return $this->activeStep;
    }

    /**
     * Getter for result.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array Value of result if set, otherwise NULL
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Translates the passed value to the current locale via I18n service.
     *
     * @param string $string     String to be translated
     * @param array  $arguments  Arguments to pass to translator (e.g. for values in translations).
     * @param bool   $htmlEscape TRUE to HTML-escape translated string. Never HTML-escape interpolated variables.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string Result of translation, input string as fallback
     *
     * @throws Doozr_Form_Service_Exception
     */
    public function translate($string, array $arguments = null, $htmlEscape = true)
    {
        if (null === $this->getI18n()) {
            throw new Doozr_Form_Service_Exception(
                'Please set an instance of Doozr_I18n_Service (or compatible) first before calling translate()'
            );
        }

        return $this->getI18n()->translate($string, $htmlEscape, $arguments);
    }

    /**
     * Returns jump status of current request.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if the form is jumped to a specific step, otherwise FALSE.
     */
    public function wasJumped()
    {
        return false !== $this->getJumped();
    }

    /**
     * Returns TRUE if the form was submitted with current request.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if form was submitted, otherwise FALSE
     */
    public function wasSubmitted()
    {
        return true === $this->getSubmitted();
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
     * Returns whether the passed component use array notation.
     *
     * @param string $componentName Name of component to check for array notation.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if has array notation, otherwise FALSE
     */
    protected function hasArrayNotation($componentName)
    {
        return preg_match('/(\[.*\])/iu', $componentName) > 0;
    }

    /**
     * Returns the submitted value for a passed fieldname or the default value if the field wasn't submitted not
     * submitted.
     *
     * @param string      $componentName The name of the component to return data for
     * @param string|null $default       The default return value as string or NULL
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed The value of the component if exist, otherwise the $default value
     */
    public function getValue($componentName, $default = null)
    {
        // Try to get fresh submitted value ...
        $value = $this->getSubmittedValue($componentName);

        // If not received try to load from pool ...
        if (
            null === $value ||
            (
                true === is_array($value) &&
                true === array_key_exists('error', $value) &&
                $value['error'] !== UPLOAD_ERR_OK
            )
        ) {
            $value = $this->getPoolValue($componentName, $this->getStep());
        }

        // Still not received? assign default
        if (null === $value) {
            $value = $default;
        }

        return $value;
    }

    public function getPoolValue($componentName, $step = Doozr_Form_Service_Constant::DEFAULT_STEP_FIRST)
    {
        $pool   = $this->getDataPool();
        $result = null;

        if (
            true === isset($pool[Doozr_Form_Service_Constant::IDENTIFIER_DATA][$step][$componentName])
        ) {
            $result = $pool[Doozr_Form_Service_Constant::IDENTIFIER_DATA][$step][$componentName];
        }

        return $result;
    }

    /**
     * Returns submitted value of a component (input, file).
     *
     * @param string $componentName Name of the component to get value for.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed Value if submitted, otherwise NULL
     */
    public function getSubmittedValue($componentName)
    {
        // Check component name for array notation ...
        $hasArrayNotation = $this->hasArrayNotation($componentName);
        $result           = null;
        $argumentSources  = [
            $this->getRequest()->getQueryParams(),
            $this->getRequest()->getParsedBody(),
            $this->getFiles(),
        ];

        // Iterate sources and parse them for components value ...
        foreach ($argumentSources as $argumentSource) {

            // How to access the value - array notation?
            if (true === $hasArrayNotation) {
                $this->getDotNotationAccessor()->setValues($argumentSource);
                $dotName = $this->getDotNotationAccessor()->translateArrayToDotNotation($componentName);
                $result  = $this->getDotNotationAccessor()->get($dotName);
            } else {
                // Or just simple value access ...
                $result = (true === isset($argumentSource[$componentName])) ? $argumentSource[$componentName] : null;
            }

            if (null !== $result) {
                break;
            }
        }

        return $result;
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
        return $this->getValidationHandler()->getError($name, $default);
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
     * @param $value
     *
     * @return string
     */
    protected function addPrefix($value)
    {
        return sprintf(
            '%s%s',
            Doozr_Form_Service_Constant::PREFIX,
            $value
        );
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

    /*------------------------------------------------------------------------------------------------------------------
    | META
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the validity of the form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE is the form is valid, otherwise FALSE
     */
    public function isValid()
    {
        return true === $this->valid;
    }

    /**
     * Returns the completion status of the form. If this method returns TRUE then the form steps are
     * completed, otherwise it would return FALSE.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if form steps are complete, otherwise FALSE
     */
    public function isComplete()
    {
        return true === $this->getComplete();
    }

    /**
     * Checks and returns the token of current request.
     *
     * @param string $value Default token used as fallback.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string|null Token as string if submitted, otherwise NULL
     */
    protected function receiveTokenFieldValueFromRequest($value = null)
    {
        $requestQueryParameter = $this->getRequest()->getQueryParams();
        $requestBody           = $this->getRequest()->getParsedBody();
        $fieldname             = $this->getFieldnameToken();

        if (true === isset($requestQueryParameter[$fieldname])) {
            $value = $requestQueryParameter[$fieldname];
        } elseif (true === isset($requestBody[$fieldname])) {
            $value = $requestBody[$fieldname];
        }

        return $value;
    }

    /**
     * Returns whether the current form scope has file upload elements not matter if valid or not.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if file upload exists, otherwise FALSE
     */
    public function hasFileUpload()
    {
        return 1 === $this->getUpload();
    }

    public function enableUpload()
    {
        $this->setUpload(1);

        return $this;
    }

    public function disableUpload()
    {
        $this->setUpload(0);

        return $this;
    }

    /**
     * Checks and returns the submission status of this form.
     *
     * @param bool $value Submitted status default
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if the form was submitted, otherwise FALSE
     */
    protected function isASubmittedFormInRequest($value = Doozr_Form_Service_Constant::DEFAULT_SUBMITTED)
    {
        $requestQueryParameter = $this->getRequest()->getQueryParams();
        $requestBody           = $this->getRequest()->getParsedBody();
        $fieldname             = $this->getFieldnameSubmitted();

        if (
            true === isset($requestQueryParameter[$fieldname]) &&
            $this->getScope() === $requestQueryParameter[$fieldname]
        ) {
            $value = true;
        } elseif (
            true === isset($requestBody[$fieldname]) &&
            $this->getScope() === $requestBody[$fieldname]
        ) {
            $value = true;
        }

        return $value;
    }

    /**
     * Returns the upload status of form.
     * It looks 1) in request for passed value and 2) in storage and 3) fallback to default (1).
     *
     * @param int $value Default value to return.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int Upload value
     */
    protected function receiveUploadFieldValueFromRequest($value = 0)
    {
        $requestQueryParameter = $this->getRequest()->getQueryParams();
        $requestBody           = $this->getRequest()->getParsedBody();
        $fieldname             = $this->getFieldnameUpload();

        if (
            true === isset($requestQueryParameter[$fieldname]) &&
            1 == $requestQueryParameter[$fieldname]
        ) {
            $value = 1;
        } elseif (
            true === isset($requestBody[$fieldname]) &&
            1 == $requestBody[$fieldname]
        ) {
            $value = 1;
        }

        return $value;
    }

    /**
     * Returns the active step of form.
     * It looks 1) in request for passed value and 2) in storage and 3) fallback to default (1).
     *
     * @param int $value Step default
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int Active step
     */
    protected function receiveStepFieldValueFromRequest($value = Doozr_Form_Service_Constant::DEFAULT_STEP_FIRST)
    {
        $requestQueryParameter = $this->getRequest()->getQueryParams();
        $requestBody           = $this->getRequest()->getParsedBody();
        $fieldname             = $this->getFieldnameStep();

        if (true === isset($requestQueryParameter[$fieldname])) {
            // Check for passed _GET arguments from URI like: /?jump=1
            $value = $requestQueryParameter[$fieldname];
        } elseif (true === isset($requestBody[$fieldname])) {
            // Check for passed _POST arguments from request body like: &jump=1
            $value = $requestBody[$fieldname];
        }

        return $value;
    }

    /**
     * Returns the number of total steps to complete the form.
     * It looks 1) in request for already defined value and 2) in storage and 3) fallback to default (1).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int Active number of steps
     */
    protected function receiveStepsFieldValueFromRequest()
    {
        $value = Doozr_Form_Service_Constant::DEFAULT_STEP_LAST;

        $requestQueryParameter = $this->getRequest()->getQueryParams();
        $requestBody           = $this->getRequest()->getParsedBody();
        $fieldname             = $this->getFieldnameSteps();

        if (true === isset($requestQueryParameter[$fieldname])) {
            $value = $requestQueryParameter[$fieldname];
        } elseif (true === isset($requestBody[$fieldname])) {
            $value = $requestBody[$fieldname];
        }

        return $value;
    }

    /**
     * Returns whether we jumped to a step.
     * It looks 1) in request ...
     *
     * @param bool|int $value Step for jump or false if not jumped as default
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool|int FALSE if we're not jumped to this step, otherwise the step as int
     */
    protected function receiveJumpFieldValueFromRequest($value = Doozr_Form_Service_Constant::DEFAULT_JUMPED)
    {
        $requestQueryParameter = $this->getRequest()->getQueryParams();
        $requestBody           = $this->getRequest()->getParsedBody();
        $fieldname             = $this->getFieldnameJump();

        if (true === isset($requestQueryParameter[$fieldname])) {
            $value = (int) $requestQueryParameter[$fieldname];
        } elseif (true === isset($requestBody[$fieldname])) {
            $value = (int) $requestBody[$fieldname];
        }

        return $value;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Tools & Helper
    +-----------------------------------------------------------------------------------------------------------------*/


    /**
     * Invalidates the whole data pool.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array Value of data pool right before invalidation.
     */
    protected function invalidateDataPool()
    {
        // Assume data is empty
        $data = null;

        // Check if pool exists ...
        if (true === isset($_SESSION[$this->addPrefix($this->getScope())])) {
            // $_SESSION[$this->addPrefix($this->getScope())];
            $data = $this->getDataPool();
            unset($_SESSION[$this->addPrefix($this->getScope())]);
            $this->setDataPool(null);
        }

        return $data;
    }

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
        return true === isset($this->error[$name]);
    }

    /**
     * Adds an component to internal registry with passed value.
     *
     * @param string $key   Name of the component
     * @param mixed  $value Value to store
     * @param int    $step  Step to add data for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     */
    protected function addToDataPool($key, $value, $step = Doozr_Form_Service_Constant::DEFAULT_STEP_FIRST)
    {
        // Get storage ...
        $pool = $this->getDataPool();

        $pool[Doozr_Form_Service_Constant::IDENTIFIER_DATA][$step][$key] = $value;

        $this->setDataPool($pool);

        return true;
    }

    /**
     * Returns ...
     *
     * @param $variable
     *
     * @return mixed|null
     */
    public function getDataPoolValue($variable)
    {
        $value = null;
        $pool  = $this->getDataPool();

        if (
            true === isset($pool[$variable]) &&
            true === isset($pool[$variable])
        ) {
            $value = $pool[$variable];
        }

        return $value;
    }

    public function getDataPoolDataValue($step, $variable)
    {
        $value = null;
        $pool  = $this->getDataPool();

        if (
            true === isset($pool[Doozr_Form_Service_Constant::IDENTIFIER_DATA][$step]) &&
            true === isset($pool[Doozr_Form_Service_Constant::IDENTIFIER_DATA][$step][$variable])
        ) {
            $value = $pool[Doozr_Form_Service_Constant::IDENTIFIER_DATA][$step][$variable];
        }

        return $value;
    }

    /**
     * Handles the data transfer from one request to next one. Important to keep the meta-data being transferred between
     * two separate requests.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function handleBackgroundDataTransfer()
    {
        // Get stored information
        $pool = $this->getDataPool();

        $lastValidStep = (
            true === $this->wasSubmitted() &&
            true === $this->isValid()
        ) ? $this->getStep() : $this->getDataPoolValue(Doozr_Form_Service_Constant::IDENTIFIER_LASTVALIDSTEP);

        // Collect important information
        $pool[Doozr_Form_Service_Constant::IDENTIFIER_COMPONENTS][$this->getActiveStep()] = $this->getComponents($this->getForm());
        $pool[Doozr_Form_Service_Constant::IDENTIFIER_METHOD]                             = $this->getMethod();
        $pool[Doozr_Form_Service_Constant::IDENTIFIER_STEP]                               = $this->getActiveStep();
        $pool[Doozr_Form_Service_Constant::IDENTIFIER_FILES][$this->getActiveStep()]      = null; /*@todo  FILES?! */
        $pool[Doozr_Form_Service_Constant::IDENTIFIER_STEPS]                              = $this->getSteps();
        $pool[Doozr_Form_Service_Constant::IDENTIFIER_TOKEN]                              = $this->getToken();
        $pool[Doozr_Form_Service_Constant::IDENTIFIER_UPLOAD]                             = $this->getUpload();
        $pool[Doozr_Form_Service_Constant::IDENTIFIER_LASTVALIDSTEP]                      = $lastValidStep;
        $pool[Doozr_Form_Service_Constant::IDENTIFIER_TOKENBEHAVIOR]                      = $this->getInvalidTokenBehavior();

        // Store the information
        $this->setDataPool($pool);

        $_SESSION[$this->addPrefix($this->getScope())] = $pool;

        // Send header
        $this->sendHeader();
    }

    /**
     * Sends header for service token transport.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function sendHeader()
    {
        // Now send token via header for use in XHR & so on
        header(
            sprintf('x-doozr-form-service-token: %s', $this->getToken())
        );
    }

    /**
     * Returns a registry skeleton for initializing store.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array An empty registry skeleton
     */
    protected function getDataPoolSkeleton()
    {
        // Default skeleton containing entries at the 1st level
        return [
            Doozr_Form_Service_Constant::IDENTIFIER_DATA          => [],                                                   // Valid! values after validation
            Doozr_Form_Service_Constant::IDENTIFIER_FILES         => [],                                                   // Valid! files after validation
            Doozr_Form_Service_Constant::IDENTIFIER_COMPONENTS    => [],                                                   // Form components of current step
            Doozr_Form_Service_Constant::IDENTIFIER_METHOD        => Doozr_Form_Service_Constant::METHOD_GET,              // Method to use for transport
            Doozr_Form_Service_Constant::IDENTIFIER_STEP          => Doozr_Form_Service_Constant::DEFAULT_STEP_FIRST,      // Active step
            Doozr_Form_Service_Constant::IDENTIFIER_STEPS         => Doozr_Form_Service_Constant::DEFAULT_STEP_LAST,       // Last step
            Doozr_Form_Service_Constant::IDENTIFIER_TOKEN         => null,                                                 // Token for next request/response
            Doozr_Form_Service_Constant::IDENTIFIER_TOKENBEHAVIOR => Doozr_Form_Service_Constant::DEFAULT_TOKEN_BEHAVIOR,  // How to behave on invalid token
            Doozr_Form_Service_Constant::IDENTIFIER_LASTVALIDSTEP => null,                                                 // Last successful validated step
            Doozr_Form_Service_Constant::IDENTIFIER_UPLOAD        => Doozr_Form_Service_Constant::DEFAULT_HAS_FILE_UPLOAD, // Whether the form has an upload
        ];
    }

    /**
     * Returns the children (components) from passed component.
     *
     * @param Doozr_Form_Service_Component_Formcomponent $component Component to return children for.
     * @param array                                      $result    Resulting array (used for recursion).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array Child components of passed form
     */
    protected function getComponents(Doozr_Form_Service_Component_Formcomponent $component, array $result = [])
    {
        /*
         * Check if the component has any children... Why do we exclude container components here? Easy to understand
         * we have currently only one component which is a container containing child elements
         * <select>
         *   <optgroup>
         *     <option></option>
         *   </optgroup>
         * </select>
         * So we would iterate its children on the search for value & validation but this does not make sense. One
         * possible way for a good refactoring would be an interface and a check for instanceof ... So we would
         * exclude some elements by its interface or exclude others for their.
         */
        if (
            true === $component->hasChildren() &&
            Doozr_Form_Service_Constant::COMPONENT_CONTAINER !== $component->getType()
        ) {
            foreach ($component as $child) {
                $result = $this->getComponents($child, $result);
            }
        } elseif (null !== $component->getName()) {
            $result[$component->getName()] = [
                'validation' => $component->getValidation(),
                'type'       => $component->getType(),
            ];
        }

        return $result;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | META
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Adds meta control layer components to form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function addMetaComponents()
    {
        // Get component handler instance
        /* @var Doozr_Form_Service_Handler_MetaComponentHandler $metaComponentHandler */
        $metaComponentHandler = $this->getRegistry()->getContainer()->build('doozr.form.service.handler.metacomponenthandler', [
                $this->getScope(),
                $this->getRegistry(),
                $this->getToken(),
                $this->getStep(),
                $this->getSteps(),
                $this->getUpload(),
                $this->getAngularDirectives(),
                $this->getFieldnameToken(),
                $this->getFieldnameSubmitted(),
                $this->getFieldnameStep(),
                $this->getFieldnameSteps(),
                $this->getFieldnameJump(),
                $this->getFieldnameUpload(),
            ]
        );

        // Generate meta components of form
        $metaDataComponents = $metaComponentHandler->getMetaComponents();

        // Dynamic at runtime added fields (added at last -> to be able to override service default behavior!)
        foreach ($metaDataComponents as $metaDataComponent) {
            $this->getForm()->addChild($metaDataComponent);
        }

        return $this;
    }

    /**
     * @return Doozr_Form_Service_Handler_FormComponentHandler
     */
    public function getFormComponentHandler()
    {
        // Get component handler instance
        /* @var Doozr_Form_Service_Handler_MetaComponentHandler $formComponentHandler */
        $formComponentHandler = $this->getRegistry()->getContainer()->build('doozr.form.service.handler.formcomponenthandler', [
                $this->getRegistry(),
            ]
        );

        return $formComponentHandler;
    }
}
