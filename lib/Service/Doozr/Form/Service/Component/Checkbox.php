<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service
 *
 * Checkbox.php - Extension to default Input-Component <input type="..." ...
 * but with some specific radio-field tuning.
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

require_once DOOZR_DOCUMENT_ROOT . 'Service/Doozr/Form/Service/Component/Input.php';
require_once DOOZR_DOCUMENT_ROOT . 'Service/Doozr/Form/Service/Component/Interface/Checkbox.php';

/**
 * Doozr - Form - Service
 *
 * Extension to default Input-Component <input type="..." ...
 * but with some specific radio-field tuning.
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
class Doozr_Form_Service_Component_Checkbox extends Doozr_Form_Service_Component_Input
    implements
    Doozr_Form_Service_Component_Interface_Checkbox
{
    /**
     * Status if component is capable of
     * submitting multi-value values
     *
     * @var bool
     * @access protected
     */
    protected $multiValue = true;

    /**
     * The addition to name for rendering HTML
     * code for multi-value input checkboxes.
     *
     * @example <input type="checkbox" name="foo[]" ...
     *
     * @var string
     * @access  protected
     */
    protected $multiValueSuffix = '[]';

    /*------------------------------------------------------------------------------------------------------------------
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param Doozr_Form_Service_Renderer_Interface  $renderer  Renderer instance for rendering this component
     * @param Doozr_Form_Service_Validator_Interface $validator Validator instance for validating this component
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \Doozr_Form_Service_Component_Checkbox
     * @access public
     */
    public function __construct(
        Doozr_Form_Service_Renderer_Interface $renderer = null,
        Doozr_Form_Service_Validator_Interface $validator = null
    ) {
        $this->setType('checkbox');

        // Important call so observer storage ... can be initiated
        parent::__construct($renderer, $validator);
    }

    /**
     * Checks this element.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function check()
    {
        $this->setAttribute('checked');
    }

    /**
     * Unchecks this element.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function uncheck()
    {
        $this->removeAttribute('checked');
    }

    /**
     * Sets the multi-marker of this element.
     *
     * @param string $suffix The multi suffix something like "[]".
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setMultiValueSuffix($suffix)
    {
        $this->multiValueSuffix = $suffix;
    }

    /**
     * Sets the multi-marker of this element.
     *
     * @param string $suffix The multi suffix something like "[]".
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function multiValueSuffix($suffix)
    {
        $this->setMultiValueSuffix($suffix);
        return $this;
    }

    /**
     * Returns the multi-marker of this element.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The multi suffix
     * @access public
     */
    public function getMultiValueSuffix()
    {
        return $this->multiValue;
    }


    /**
     * Sets the multi-value status of this element.
     *
     * @param bool $status TRUE $boolean to mark this field as multi select field, FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setMultiValue($status)
    {
        $this->multiValue = $status;
    }

    /**
     * Sets the multi-value status of this element.
     *
     * @param bool $status TRUE $boolean to mark this field as multi select field, FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function multiValue($status)
    {
        $this->setMultiValue($status);
        return $this;
    }

    /**
     * Returns the multi-value status of this element.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if field is multi select, FALSE if not
     * @access public
     */
    public function getMultiValue()
    {
        return $this->multiValue;
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Setter & Getter
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for name.
     *
     * @param string $name The name to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setName($name)
    {
        if ($this->getMultiValue() === true) {
            $name .= $this->getMultiValueSuffix();
        }

        return parent::setName($name);
    }

    /**
     * Setter for attributes.
     *
     * @param string $key   The key/name of the attribute to set
     * @param string $value The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setAttribute($key, $value = null)
    {
        if ($key === 'name' && stristr($value, $this->getMultiValueSuffix()) !== false) {
            $this->setMultiValue(true);
        }

        parent::setAttribute($key, $value);
    }

    /**
     * Returns an attribute of this element.
     *
     * @param string $key The name of the key/attribute to return value for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed|boolean The attributes value if set, FALSE if not
     * @access public
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if ($key === 'name') {
            $value = str_replace($this->multiValueSuffix, '', $value);
        }

        return $value;
    }

    /**
     * Returns the name of this element without brackets by default.
     *
     * @param bool $ripBrackets TRUE to remove brackets from name, FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The name of this element with or without brackets, or NULL if not set
     * @access public
     */
    public function getName($ripBrackets = true)
    {
        $name = $this->getAttribute('name');

        if ($ripBrackets === true) {
            $name = str_replace($this->multiValueSuffix, '', $name);
        }

        return $name;
    }

    /**
     * Setter for value.
     *
     * @param string $value          The value to set
     * @param string $submittedValue The value which was submitted
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setValue($value, $submittedValue = null)
    {
        if ($submittedValue !== null && is_array($submittedValue)) {
            $submitted = in_array($value, $submittedValue);

            if ($submitted === true) {
                $this->check();
            }
        }

        return parent::setValue($value);
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Tools & Helper
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the active status of this element.
     * Active = TRUE means that this element was selected (from a group of elements).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if the element is active, FALSE if not
     * @access protected
     */
    protected function isActive()
    {
        $this->getAttribute('checked');
    }
}
