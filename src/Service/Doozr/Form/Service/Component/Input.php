<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service.
 *
 * Input.php - The Input component control layer which adds validation,
 * and so on to an HTML component.
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
require_once DOOZR_DOCUMENT_ROOT.'Service/Doozr/Form/Service/Component/Formcomponent.php';
require_once DOOZR_DOCUMENT_ROOT.'Service/Doozr/Form/Service/Component/Interface/Input.php';

/**
 * Doozr - Form - Service.
 *
 * The Input component control layer which adds validation,
 * and so on to an HTML component.
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
class Doozr_Form_Service_Component_Input extends Doozr_Form_Service_Component_Formcomponent
    implements
    Doozr_Form_Service_Component_Interface_Input
{
    /**
     * This is the tag-name for HTML output.
     * e.g. "input" or "form". Default empty string "".
     *
     * @var string
     */
    protected $tag = Doozr_Form_Service_Constant::HTML_TAG_INPUT;

    /**
     * The template is required for output. Each HTML-Component inherits
     * this base template and so every component based on this base class
     * is renderable. This template produces at least a correct HTML tag
     * which must not be valid in an other context!
     *
     * @var string
     */
    protected $template = Doozr_Form_Service_Constant::DEFAULT_TEMPLATE_NONCLOSING;

    /**
     * Type for <input type="text">.
     *
     * @var string
     */
    const TYPE_TEXT = 'text';

    /**
     * Type for <input type="button">.
     *
     * @var string
     */
    const TYPE_BUTTON = 'button';

    /**
     * Type for <input type="image">.
     *
     * @var string
     */
    const TYPE_IMAGE = 'image';

    /**
     * Type for <input type="abort">.
     *
     * @var string
     */
    const TYPE_ABORT = 'abort';

    /**
     * Type for <input type="reset">.
     *
     * @var string
     */
    const TYPE_RESET = 'reset';

    /**
     * Type for <input type="upload">.
     *
     * @var string
     */
    const TYPE_UPLOAD = 'upload';

    /**
     * Type for <input type="submit">.
     *
     * @var string
     */
    const TYPE_SUBMIT = 'submit';


    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for autocomplete.
     *
     * @param string $autocomplete The autocomplete state as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setAutocomplete($autocomplete = 'autocomplete')
    {
        $this->setAttribute('autocomplete', $autocomplete);
    }

    /**
     * Getter for autocomplete.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The autocomplete value if set, otherwise NULL
     */
    public function getAutocomplete()
    {
        return $this->getAttribute('autocomplete');
    }

    /**
     * Setter for autofocus.
     *
     * @param string $autofocus The autofocus state as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setAutofocus($autofocus = 'autofocus')
    {
        $this->setAttribute('autofocus', $autofocus);
    }

    /**
     * Getter for autofocus.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The autofocus value if set, otherwise NULL
     */
    public function getAutofocus()
    {
        return $this->getAttribute('autofocus');
    }

    /**
     * Setter for disabled.
     *
     * @param string $disabled The disabled state as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setDisabled($disabled = 'disabled')
    {
        $this->setAttribute('disabled', $disabled);
    }

    /**
     * Getter for disabled.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The disabled value if set, otherwise NULL
     */
    public function getDisabled()
    {
        return $this->getAttribute('disabled');
    }

    /**
     * Setter for form.
     *
     * @param string $form The form state as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setForm($form)
    {
        $this->setAttribute('form', $form);
    }

    /**
     * Getter for form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The form value if set, otherwise NULL
     */
    public function getForm()
    {
        return $this->getAttribute('form');
    }

    /**
     * Setter for formaction.
     *
     * @param string $formaction The formaction as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setFormaction($formaction)
    {
        $this->setAttribute('formction', $formaction);
    }

    /**
     * Getter for formaction.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The formaction value if set, otherwise NULL
     */
    public function getFormaction()
    {
        return $this->getAttribute('formaction');
    }

    /**
     * Setter for formenctype.
     *
     * @param string $formEnctype The formenctype as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setFormEnctype($formEnctype)
    {
        $this->setAttribute('formenctype', $formEnctype);
    }

    /**
     * Getter for formenctype.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The formenctype value if set, otherwise NULL
     */
    public function getFormEnctype()
    {
        return $this->getAttribute('formenctype');
    }

    /**
     * Setter for formmethod.
     *
     * @param string $formMethod The formmethod as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setFormMethod($formMethod)
    {
        $this->setAttribute('formmethod', $formMethod);
    }

    /**
     * Getter for formmethod.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The formmethod value if set, otherwise NULL
     */
    public function getFormMethod()
    {
        return $this->getAttribute('formmethod');
    }

    /**
     * Setter for formnovalidate.
     *
     * @param string $formNovalidate The formnovalidate value as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setFormNovalidate($formNovalidate = 'formnovalidate')
    {
        $this->setAttribute('formnovalidate', $formNovalidate);
    }

    /**
     * Getter for formnovalidate.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The formnovalidate value if set, otherwise NULL
     */
    public function getFormNovalidate()
    {
        return $this->getAttribute('formnovalidate');
    }

    /**
     * Sets the name of the list the input element is bound to.
     *
     * @param string $listname The name of the list the input refers to
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setList($listname)
    {
        $this->setAttribute('list', $listname);
    }

    /**
     * Returns the list the component is bound to.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|string The name of the list this component is bound to, NULL if not bound
     */
    public function getList()
    {
        return $this->getAttribute('list');
    }

    /**
     * Setter for max.
     *
     * @param string $max The max value as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setMax($max)
    {
        $this->setAttribute('max', $max);
    }

    /**
     * Getter for max.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The max value if set, otherwise NULL
     */
    public function getMax()
    {
        return $this->getAttribute('max');
    }

    /**
     * Setter for maxlength.
     *
     * @param string $maxlength The maxlength value as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setMaxlength($maxlength)
    {
        $this->setAttribute('maxlength', $maxlength);
    }

    /**
     * Getter for maxlength.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The maxlength value if set, otherwise NULL
     */
    public function getMaxlength()
    {
        return $this->getAttribute('maxlength');
    }

    /**
     * Setter for min.
     *
     * @param string $min The min value as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setMin($min)
    {
        $this->setAttribute('min', $min);
    }

    /**
     * Getter for min.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The min value if set, otherwise NULL
     */
    public function getMin()
    {
        return $this->getAttribute('min');
    }

    /**
     * Setter for multiple.
     *
     * @param string $multiple The multiple value as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setMultiValue($multiple)
    {
        $this->setAttribute('multiple', $multiple);
    }

    /**
     * Getter for multiple.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The multiple value if set, otherwise NULL
     */
    public function getMultiValue()
    {
        return $this->getAttribute('multiple');
    }

    /**
     * Setter for pattern.
     *
     * @param string $pattern The pattern value as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setPattern($pattern)
    {
        $this->setAttribute('pattern', $pattern);
    }

    /**
     * Getter for pattern.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The pattern value if set, otherwise NULL
     */
    public function getPattern()
    {
        return $this->getAttribute('pattern');
    }

    /**
     * Setter for placeholder.
     *
     * @param string $placeholder The placeholder value as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setPlaceholder($placeholder)
    {
        $this->setAttribute('placeholder', $placeholder);
    }

    /**
     * Getter for placeholder.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The placeholder value if set, otherwise NULL
     */
    public function getPlaceholder()
    {
        return $this->getAttribute('placeholder');
    }

    /**
     * Setter for readonly.
     *
     * @param string $readonly The readonly value as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setReadonly($readonly)
    {
        $this->setAttribute('readonly', $readonly);
    }

    /**
     * Getter for readonly.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The readonly value if set, otherwise NULL
     */
    public function getReadonly()
    {
        return $this->getAttribute('readonly');
    }

    /**
     * Setter for required.
     *
     * @param string $required The required value as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setRequired($required)
    {
        $this->setAttribute('required', $required);
    }

    /**
     * Getter for required.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The required value if set, otherwise NULL
     */
    public function getRequired()
    {
        return $this->getAttribute('required');
    }

    /**
     * Setter for size.
     *
     * @param string $size The size value as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setSize($size)
    {
        $this->setAttribute('size', $size);
    }

    /**
     * Getter for size.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The size value if set, otherwise NULL
     */
    public function getSize()
    {
        return $this->getAttribute('size');
    }

    /**
     * Setter for step.
     *
     * @param string $step The step value as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setStep($step)
    {
        $this->setAttribute('step', $step);
    }

    /**
     * Getter for step.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The step value if set, otherwise NULL
     */
    public function getStep()
    {
        return $this->getAttribute('step');
    }

    /**
     * Sets the HTML input element property "autocapitalize".
     *
     * @param bool $state The state to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setAutocapitalize($state)
    {
        if (is_bool($state)) {
            if ($state === true) {
                $state = 'on';
            } else {
                $state = 'off';
            }
        }

        $this->setAttribute('autocapitalize', $state);
    }

    /**
     * Returns the autocapitalize state.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if autocapitalize is on, otherwise FALSE
     */
    public function getAutocapitalize()
    {
        return ($this->getAttribute('autocapitalize') === 'on') ? true : false;
    }
}
