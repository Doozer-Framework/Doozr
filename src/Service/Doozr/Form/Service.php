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
     * Session service instance.
     *
     * @var Doozr_Session_Service|null
     */
    protected $session;

    /**
     * Constructor replacement.
     *
     * @param Doozr_Session_Service_Interface $session        Instance of Service Session
     * @param string                          $fieldnameToken Name of the field for "token" value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE On success, otherwise FALSE
     */
    public function __tearup(
        Doozr_Session_Service_Interface $session = null,
    # HERE ADD: Renderer Inject,
        $fieldnameToken = null,
        $fieldnameSubmitted = null,
        $fieldnameStep = null,
        $fieldnameSteps = null
    ) {
        if (null === $fieldnameToken) {
            $fieldnameToken = Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_TOKEN;
        }

        if (null === $fieldnameSubmitted) {
            $fieldnameSubmitted = Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_SUBMITTED;
        }

        if (null === $fieldnameStep) {
            $fieldnameStep = Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_STEP;
        }

        if (null === $fieldnameSteps) {
            $fieldnameSteps = Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_STEPS;
        }

        $this
            ->session($session)
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
     * Setter for session.
     *
     * @param Doozr_Session_Service $session The session service instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setSession(Doozr_Session_Service $session = null)
    {
        $this->session = $session;
    }

    /**
     * Setter for session.
     *
     * @param Doozr_Session_Service $session The session service instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function session(Doozr_Session_Service $session = null)
    {
        $this->setSession($session);

        return $this;
    }

    /**
     * Getter for session.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Session_Service|null The instance if set, otherwise NULL
     */
    public function getSession()
    {
        return $this->session;
    }

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
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string|bool The name of the form which is handable by Doozr_Form_Service if exist, otherwise FALSE
     */
    public function isHandable()
    {
        // Assume that request is not! handable by Doozr_Form_Service -> The API does only share some parts with MVP def
        $handable = false;

        /* @var $requestState Doozr_Request_State */
        $requestState = $this->getRegistry()->request;

        // Get required input to search in ...
        $requestArguments = $requestState->getQueryParams();
        $requestBody      = $requestState->getRequestBody();

        if (isset($requestArguments->{$this->getFieldnameSubmitted()}) === true) {
            $handable = $requestArguments->{$this->getFieldnameSubmitted()};
        } elseif (isset($requestBody->{$this->getFieldnameSubmitted()}) === true) {
            $handable = $requestBody->{$this->getFieldnameSubmitted()};
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
        array $arguments = [],
              $requestMethod = null,
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
     * @return \Doozr_Form_Service_FormHandler
     */
    protected function formHandlerFactory($scope, array $arguments, $requestMethod, $angularDirectives)
    {
        $collection = new Doozr_Di_Collection();
        $importer   = new Doozr_Di_Importer_Json();
        $dependency = new Doozr_Di_Dependency();
        $map        = new Doozr_Di_Map_Static($collection, $importer, $dependency);

        // Generate map from static JSON map of Doozr
        $map->generate($this->retrievePathToCurrentClass().'.map.json');

        // Get container instance from registry
        $container = self::getRegistry()->getContainer();

        // Add map to existing maps in container
        $container->addToMap($map);

        // Create container and set factory and map
        $container->getMap()->wire(
            [
                'doozr.i18n.service'               => Doozr_Loader_Serviceloader::load('i18n'),
                'doozr.form.service.store'         => new Doozr_Form_Service_Store_UnitTest(),
                'doozr.form.service.renderer.html' => new Doozr_Form_Service_Renderer_Html(),
            ]
        );

        /* @var Doozr_Form_Service_FormHandler $formHandler */
        return self::$registry->getContainer()->build(
            'doozr.form.service.formhandler',
            [$scope, $arguments, $requestMethod, $angularDirectives]
        );
    }
}
