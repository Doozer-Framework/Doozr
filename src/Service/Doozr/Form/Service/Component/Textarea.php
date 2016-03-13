<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service.
 *
 * Textarea.php - More specialized version of a form component.
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

/**
 * Doozr - Form - Service.
 *
 * Textarea.php - More specialized version of a form component.
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
class Doozr_Form_Service_Component_Textarea extends Doozr_Form_Service_Component_Formcomponent
{
    /**
     * This is the tag-name for HTML output.
     * e.g. "input" or "form". Default empty string "".
     *
     * @var string
     */
    protected $tag = Doozr_Form_Service_Constant::HTML_TAG_TEXTAREA;

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for autofocus.
     *
     * @param string $autofocus The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setAutofocus($autofocus = null)
    {
        $this->setAttribute('autofocus', $autofocus);
    }

    /**
     * Getter for autofocus.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|string The value if set, otherwise NULL
     */
    public function getAutofocus()
    {
        return $this->getAttribute('autofocus');
    }

    /**
     * Setter for cols.
     *
     * @param string $cols The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setCols($cols)
    {
        $this->setAttribute('cols', $cols);
    }

    /**
     * Getter for cols.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|string The value if set, otherwise NULL
     */
    public function getCols()
    {
        return $this->getAttribute('cols');
    }

    /**
     * Setter for disabled.
     *
     * @param string $disabled The value to set
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
     * @return null|string The value if set, otherwise NULL
     */
    public function getDisabled()
    {
        return $this->getAttribute('disabled');
    }

    /**
     * Setter for form.
     *
     * @param string $form The value to set
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
     * @return null|string The value if set, otherwise NULL
     */
    public function getForm()
    {
        return $this->getAttribute('form');
    }

    /**
     * Setter for max-length.
     *
     * @param string $maxlength The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setMaxlength($maxlength)
    {
        $this->setAttribute('maxlength', $maxlength);
    }

    /**
     * Getter for max-length.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|string The value if set, otherwise NULL
     */
    public function getMaxlength()
    {
        return $this->getAttribute('maxlength');
    }

    /**
     * Setter for rows.
     *
     * @param string $rows The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setRows($rows)
    {
        $this->setAttribute('rows', $rows);
    }

    /**
     * Getter for rows.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|string The value if set, otherwise NULL
     */
    public function getRows()
    {
        return $this->getAttribute('rows');
    }

    /**
     * Setter for wrap.
     *
     * @param string $wrap The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setWrap($wrap)
    {
        $this->setAttribute('wrap', $wrap);
    }

    /**
     * Getter for wrap.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|string The value if set, otherwise NULL
     */
    public function getWrap()
    {
        return $this->getAttribute('wrap');
    }

    /**
     * Setter for value.
     *
     * @param mixed $value The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setValue($value)
    {
        ($value === null) ? $value = '' : null;

        $this->setInnerHtml($value);
    }

    /**
     * Getter for value.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed Value of this element
     */
    public function getValue()
    {
        $value = $this->getInnerHtml();

        return ($value !== null) ? $value : '';
    }
}
