<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service
 *
 * Formcomponent.php - This abstract base class is used for extending the default
 * HTML component Doozr_Form_Service_Html and provides generic form field/component
 * functionality. So extend this class for building form components and get
 * functionality like getName(), setName() ... on top!
 *
 * PHP versions 5.5
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
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
 * @category   Doozr
 * @package    Doozr_Service
 * @subpackage Doozr_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

/**
 * Doozr - Form - Service
 *
 * This abstract base class is used for extending the default
 * HTML component Doozr_Form_Service_Html and provides generic form field/component
 * functionality. So extend this class for building form components and get
 * functionality like getName(), setName() ... on top!
 *
 * @category   Doozr
 * @package    Doozr_Service
 * @subpackage Doozr_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
abstract class Doozr_Form_Service_Component_Formcomponent extends Doozr_Form_Service_Component_Html
    implements
    Doozr_Form_Service_Component_Interface_Form
{
    /**
     * The validations of this component
     *
     * @var array
     * @access protected
     */
    protected $validation = [];

    /**
     * Status if component is capable of
     * submitting multiple values
     *
     * @var bool
     * @access protected
     */
    protected $multiValue = false;

    /**
     * Validity of this component
     *
     * @var array
     * @access protected
     */
    protected $valid = true;

    /**
     * The value here is handled slightly different from other components
     *
     * @var string
     * @access protected
     */
    protected $value;

    /**
     * The submitted value
     *
     * @var mixed
     * @access protected
     */
    protected $submittedValue;

    /**
     * Mark this component as generic
     *
     * @var string
     * @access protected
     */
    protected $type = Doozr_Form_Service_Constant::COMPONENT_GENERIC;

    /**
     * A validator instance used to validate this component
     *
     * @var Doozr_Form_Service_Validator_Interface
     * @access protected
     */
    protected $validator;

    /**
     * Constructor.
     *
     * @param Doozr_Form_Service_Renderer_Interface  $renderer  The renderer instance which renders this component
     * @param Doozr_Form_Service_Validator_Interface $validator The validator instance which validates this component
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \Doozr_Form_Service_Component_Formcomponent
     * @access public
     */
    public function __construct(
        Doozr_Form_Service_Renderer_Interface $renderer = null,
        Doozr_Form_Service_Validator_Interface $validator = null
    ) {
        if ($validator !== null) {
            $this->setValidator($validator);
        }

        // Important call so observer storage ... can be initiated
        parent::__construct(null, null, $renderer);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for type of input.
     *
     * @param string $type The type to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setType($type)
    {
        $this->setAttribute('type', $type);
    }

    /**
     * Getter for type.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The type if set, otherwise NULL
     * @access public
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Setter for validator.
     *
     * @param Doozr_Form_Service_Validator_Interface $validator The validator instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Form_Service_Validator_Interface The validator instance
     * @access public
     */
    public function setValidator(Doozr_Form_Service_Validator_Interface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Getter for validator.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Form_Service_Validator_Interface The validator instance
     * @access public
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Returns the validity state of the form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if valid, otherwise FALSE
     * @access public
     */
    public function isValid()
    {
        // BASIC = everything's valid
        return $this->valid;
    }

    /**
     * Stores/adds the passed validation information.
     *
     * @param string      $validation The type of validation
     * @param null|string $value      The value for validation or NULL
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function addValidation($validation, $value = null)
    {
        if (!isset($this->validation[$validation])) {
            $this->validation[$validation] = [];
        }

        $this->validation[$validation][] = $value;
    }

    /**
     * Stores/adds the passed validation information.
     *
     * @param string      $validation The type of validation
     * @param null|string $value      The value for validation or NULL
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function validation($validation, $value = null)
    {
        $this->addValidation($validation, $value);
        return $this;
    }

    /**
     * Getter for validation.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Validations as array
     * @access public
     */
    public function getValidation()
    {
        return $this->validation;
    }

    /**
     * Setter for name
     *
     * Sets the name of this component.
     *
     * @param string $name The name of the component to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setName($name)
    {
        $this->setAttribute('name', $name);
    }

    /**
     * Getter for name.
     *
     * Returns the name of an component.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The name of the component as string, otherwise NULL if not set
     * @access public
     */
    public function getName()
    {
        return $this->getAttribute('name');
    }

    /**
     * Setter for submitted value.
     *
     * @param mixed $value The submitted value of this component
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setSubmittedValue($value)
    {
        $this->submittedValue = $value;
    }

    /**
     * Getter for submitted value.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Submitted value of the component
     * @access public
     */
    public function getSubmittedValue()
    {
        return $this->submittedValue;
    }

    /**
     * Setter for value.
     *
     * @param mixed $value The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setValue($value)
    {
        $this->setAttribute('value', $value);
    }

    /**
     * Getter for value.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The value as string if set, otherwise NULL
     * @access public
     */
    public function getValue()
    {
        return $this->getAttribute('value');
    }
}
