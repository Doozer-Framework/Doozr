<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Service - Form
 *
 * Interface.php - Interface for input-field class
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
 * @see        -
 * @since      -
 */

/**
 * DoozR - Service - Form
 *
 * Interface input-field class - Base for all other input-field types
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
interface DoozR_Form_Service_Element_Interface
{
    /**
     * sets the size of a form element
     *
     * This method is intend to set the size of a form element (displaylength in chars).
     *
     * @param integer $size The size in chars to set
     *
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     */
    public function setSize($size);

    /**
     * returns the size of a form element
     *
     * This method is intend to return the size of a form element (displaylength in chars).
     */
    public function getSize();

    /**
     * sets a given attribute (generic attribute setter)
     *
     * This method is intend to set a given attribute with a given value.
     *
     * @param string $attribute The attribute to set
     * @param mixed  $value     The value to store for the given attribute
     */
    public function setAttribute($attribute, $value = null);

    /**
     * returns a requested attribute (generic attribute getter)
     *
     * This method is intend to return a requested attribute.
     *
     * @param string $attribute The attributes name to return value from
     */
    public function getAttribute($attribute);

    /**
     * sets the name of the input field
     *
     * This method is intend to set the name of the input field.
     *
     * @param string $name The name of the input-field
     */
    public function setName($name);

    /**
     * returns the name of the input field
     *
     * This method is intend to return the name of the input field.
     */
    public function getName();

    /**
     * sets the id of the input field
     *
     * This method is intend to set the id of the input field.
     *
     * @param string $id The id of the input-field
     */
    public function setId($id);

    /**
     * returns the id of the input field
     *
     * This method is intend to return the id of the input field.
     */
    public function getId();

    /**
     * sets the class (css) of the input field
     *
     * This method is intend to set the class (css) of the input field.
     *
     * @param string $classname The classname (css) of the input-field
     */
    public function setCss($classname);

    /**
     * returns the class (css) of the input field
     *
     * This method is intend to return the class (css) of the input field.
     */
    public function getCss();

    /**
     * sets the disabled status of the input field
     *
     * This method is intend to set the disabled status of the input field.
     *
     * @param boolean $disabled The disabled-status of the input-field
     */
    public function setDisabled($disabled = true);

    /**
     * returns the disabled status of the input field
     *
     * This method is intend to return the disabled status of the input field.
     */
    public function getDisabled();

    /**
     * sets the hidden status of the input field
     *
     * This method is intend to set the hidden status of the input field.
     *
     * @param boolean $hidden The hidden-status of the input-field
     */
    public function setHidden($hidden = true);

    /**
     * returns the hidden status of the input field
     *
     * This method is intend to return the hidden status of the input field.
     */
    public function getHidden();

    /**
     * sets the required status of the input field
     *
     * This method is intend to set the required status of the input field.
     *
     * @param boolean $required The required-status of the input-field
     */
    public function setRequired($required = false);

    /**
     * returns the required status of the input field
     *
     * This method is intend to return the required status of the input field.
     */
    public function getRequired();

    /**
     * sets the label of the input field
     *
     * This method is intend to set the label of the input field.
     *
     * @param string $label The label-text to set
     */
    public function setLabel($label = '');

    /**
     * returns the label of the input-field
     *
     * This method is intend to return the label of the input-field
     */
    public function getLabel();

    /**
     * sets the impact (IDS - danger input indicator) of the input field
     *
     * This method is intend to set the impact (IDS - danger input indicator) of the input field
     *
     * @param integer $impact The impact-value to set
     */
    public function setImpact($impact = 0);

    /**
     * returns the impact (IDS - danger input indicator) of the input field
     *
     * This method is intend to return the impact (IDS - danger input indicator) of the input field
     */
    public function getImpact();

    /**
     * returns status of impact
     *
     * This method is intend to return the status of impact of input-field
     */
    public function hasImpact();

    /**
     * sets the read-only status of the input field
     *
     * This method is intend to set the read-only status of the input field
     *
     * @param boolean $readonly The read-only status of the input field
     */
    public function setReadonly($readonly = true);

    /**
     * returns the read-only status of the input field
     *
     * This method is intend to return the read-only status of the input field
     */
    public function getReadonly();

    /**
     * removes an attribute from input-field
     *
     * This method is intend to remove an attribute from input-field.
     *
     * @param string $attribute The attributes name to remove
     */
    public function removeAttribute($attribute);

    /**
     * sets the maximum input length of the input field
     *
     * This method is intend to set the maximum input length of the input field
     *
     * @param integer $maxlength The maximum input length of the input field
     */
    public function setMaxlength($maxlength);

    /**
     * returns the maximum input length of the input field
     *
     * This method is intend to return the maximum input length of the input field
     */
    public function getMaxlength();

    /**
     * sets the value of the input field
     *
     * This method is intend to set the value of the input field
     *
     * @param mixed   $value                  The value to set for this input-field
     * @param boolean $overrideSubmittedValue TRUE to override a submitted value
     */
    public function setValue($value, $overrideSubmittedValue = false);

    /**
     * returns the value of the input field
     *
     * This method is intend to return the value of the input field
     */
    public function getValue($raw = false);

    /**
     * returns the HTML-code for the input-field
     *
     * This method is intend to return the HTML-code for the input-field
     */
    public function render();
}

?>
