<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Form - Service
 *
 * Input.php - The Input component control layer which adds validation,
 * and so on to an HTML component.
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

require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Form/Service/Component/Formcomponent.php';
require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Form/Service/Component/Interface/Input.php';

/**
 * DoozR - Form - Service
 *
 * The Input component control layer which adds validation,
 * and so on to an HTML component.
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id: 5e30d07525fe2d0cbb9781237cfff999f16ff57e $
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Form_Service_Component_Input extends DoozR_Form_Service_Component_Formcomponent
    implements
    DoozR_Form_Service_Component_Interface_Input
{
    /**
     * This is the tag-name for HTML output.
     * e.g. "input" or "form". Default empty string ""
     *
     * @var string
     * @access protected
     */
    protected $tag = DoozR_Form_Service_Constant::HTML_TAG_INPUT;

    /**
     * The template is required for output. Each HTML-Component inherits
     * this base template and so every component based on this base class
     * is renderable. This template produces at least a correct HTML tag
     * which must not be valid in an other context!
     *
     * @var string
     * @access protected
     */
    protected $template = DoozR_Form_Service_Constant::TEMPLATE_DEFAULT_NONCLOSING;


    /*------------------------------------------------------------------------------------------------------------------
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    public function setAutocomplete($state)
    {
        $this->setAttribute('autocomplete', $state);
    }

    public function getAutocomplete()
    {
        return $this->getAttribute('autocomplete');
    }

    public function setAutofocus($autofocus = 'autofocus')
    {
        $this->setAttribute('autofocus', $autofocus);
    }

    public function getAutofocus()
    {
        return $this->getAttribute('autofocus');
    }

    public function setDisabled($disabled = 'disabled')
    {
        $this->setAttribute('disabled', $disabled);
    }

    public function getDisabled()
    {
        return $this->getAttribute('disabled');
    }

    public function setForm($form)
    {
        $this->setAttribute('form', $form);
    }

    public function getForm()
    {
        return $this->getAttribute('form');
    }

    public function setFormaction($formaction)
    {
        $this->setAttribute('formction', $formaction);
    }

    public function getFormaction()
    {
        return $this->getAttribute('formaction');
    }

    public function setFormEnctype($formEnctype)
    {
        $this->setAttribute('formenctype', $formEnctype);
    }

    public function getFormEnctype()
    {
        return $this->getAttribute('formenctype');
    }

    public function setFormMethod($formMethod)
    {
        $this->setAttribute('formmethod', $formMethod);
    }

    public function getFormMethod()
    {
        return $this->getAttribute('formmethod');
    }

    public function setFormNovalidate($formNovalidate = 'formnovalidate')
    {
        $this->setAttribute('formnovalidate', $formNovalidate);
    }

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
     * @return void
     * @access public
     */
    public function setList($listname)
    {
        $this->setAttribute('list', $listname);
    }

    /**
     * Returns the list the component is bound to.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|string The name of the list this component is bound to, NULL if not bound
     * @access public
     */
    public function getList()
    {
        return $this->getAttribute('list');
    }

    public function setMax($max)
    {
        $this->setAttribute('max', $max);
    }

    public function getMax()
    {
        return $this->getAttribute('max');
    }

    public function setMaxlength($maxlength)
    {
        $this->setAttribute('maxlength', $maxlength);
    }

    public function getMaxlength()
    {
        return $this->getAttribute('maxlength');
    }

    public function setMin($min)
    {
        $this->setAttribute('min', $min);
    }

    public function getMin()
    {
        return $this->getAttribute('min');
    }

    public function setMultiple($multiple)
    {
        $this->setAttribute('multiple', $multiple);
    }

    public function getMultiple()
    {
        return $this->getAttribute('multiple');
    }

    public function setPattern($pattern)
    {
        $this->setAttribute('pattern', $pattern);
    }

    public function getPattern()
    {
        return $this->getAttribute('pattern');
    }

    public function setPlaceholder($placeholder)
    {
        $this->setAttribute('placeholder', $placeholder);
    }

    public function getPlaceholder()
    {
        return $this->getAttribute('placeholder');
    }

    public function setReadonly($readonly)
    {
        $this->setAttribute('readonly', $readonly);
    }

    public function getReadonly()
    {
        return $this->getAttribute('readonly');
    }

    public function setRequired($required)
    {
        $this->setAttribute('required', $required);
    }

    public function getRequired()
    {
        return $this->getAttribute('required');
    }

    public function setSize($size)
    {
        $this->setAttribute('size', $size);
    }

    public function getSize()
    {
        return $this->getAttribute('size');
    }

    public function setStep($step)
    {
        $this->setAttribute('step', $step);
    }

    public function getStep()
    {
        return $this->getAttribute('step');
    }





    /**
     * Sets the HTML input element property "autocapitalize"
     *
     * @param boolean $state The state to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
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
     * @return boolean TRUE if autocapitalize is on, otherwise FALSE
     * @access public
     */
    public function getAutocapitalize()
    {
        return ($this->getAttribute('autocapitalize') === 'on') ? true : false;
    }
}
