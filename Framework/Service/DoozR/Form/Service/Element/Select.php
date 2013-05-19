<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Service - Form
 *
 * Select.php - Select class for creating fields e.g. of type "select"
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

require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Form/Service/Element/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Form/Service/Element/Interface.php';

/**
 * DoozR - Service - Form
 *
 * Select.php - Select class for creating fields e.g. of type "select"
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
class Select extends DoozR_Form_Service_Element_Abstract implements DoozR_Form_Service_Element_Interface
{
    /**
     * The defined options for this select
     *
     * @var array
     * @access private
     */
    private $_options = array();

    /**
     * The multiline-status
     *
     * @var bool
     * @access private
     */
    private $_multiline = false;

    /**
     * The count of rows for multiline-representation
     * default: 2 lines
     *
     * @var integer
     * @access private
     */
    private $_multilinesize = 2;

    /**
     * The multiple-status
     *
     * @var bool
     * @access private
     */
    private $_multiple = false;


    /**
     * adds an option to the select-field
     *
     * This method is intend to add an option to the select-field
     *
     * @param string $text  The text to display for this option
     * @param mixed  $value The value for this option
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Hash of the created input as reference for removal
     * @access public
     */
    public function addOption($text = '', $value = '')
    {
        // build option
        $option = array();
        $option['hash'] = md5($value.$text);
        $option['value'] = $value;
        $option['text'] = $text;
        $option['selected'] = null;

        // store the option
        $this->_options[] = $option;

        // success
        return $option['hash'];
    }

    /**
     * removes an option of the select-field
     *
     * This method is intend to remove an option of the select-field
     *
     * @param mixed  $reference The hash/reference to identify the option to remove
     * @param string $text      The text of the option to remove
     * @param string $value     The value of the option to remove
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successfully removed, otherwise FALSE on error or option not found
     * @access public
     */
    public function removeOption($reference = null, $text = '', $value = '')
    {
        // remove by reference?
        if (is_null($reference)) {
            $reference = $md5($text.$value);
        }

        // try to remove by hash/reference
        foreach ($this->_options as $key => $option) {
            if ($this->_options[$key]['hash'] == $reference) {
                unset($this->_options[$key]);
                // success
                return true;
            }
        }

        // not found?
        return false;
    }

    /**
     * returns the option(s) of this select-field
     *
     * This method is intend to return the option(s) of this select-field.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The options of this select-field
     * @access public
     */
    public function getOptions()
    {
        // return the options of this select field
        return $this->_options;
    }

    /**
     * set the multiline status and the linecount
     *
     * This method is intend to set the multiline status and the linecount
     *
     * @param boolean $status    The multiline status
     * @param integer $linecount The linecount to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successfully set, otherwise FALSE
     * @access public
     */
    public function setMultiline($status = true, $linecount = 2)
    {
        // store for further access
        $this->_multiline = $status;
        $this->_multilinesize = $linecount;

        // check if set or remove
        if ($status) {
            // set attribute for html-generating
            return $this->setAttribute('size', $linecount);
        } else {
            return $this->removeAttribute('size');
        }
    }

    /**
     * set the multiline status and the linecount
     *
     * This method is intend to set the multiline status and the linecount
     *
     * @param boolean $status    The multiline status
     * @param integer $linecount The linecount to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Select Current active instance
     * @access public
     */
    public function multiline($status = true, $linecount = 2)
    {
        $this->setMultiline($status, $linecount);

        // for chaining
        return $this;
    }

    /**
     * returns the multiline status
     *
     * This method is intend to set the multiline status
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if multiline, otherwise FALSE
     * @access public
     */
    public function getMultiline()
    {
        return $this->_multiline;
    }

    /**
     * set the multiple status
     *
     * This method is intend to set the multiple status
     *
     * @param boolean $status The multiple status
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successfully set, otherwise FALSE
     * @access public
     */
    public function setMultiple($status = true)
    {
        $this->_multiple = $status;

        if ($status) {
            $this->name($this->getName().'[]');
            return $this->setAttribute('multiple', 'multiple');
        } else {
            $this->name(str_replace('[]', '', $this->getName()));
            return $this->removeAttribute('multiple');
        }
    }

    /**
     * set the multiple status
     *
     * This method is intend to set the multiple status
     *
     * @param boolean $status The multiple status
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Select Current active instance
     * @access public
     */
    public function multiple($status = true)
    {
        $this->setMultiple($status);

        // for chaining
        return $this;
    }

    /**
     * returns the multiline status
     *
     * This method is intend to set the multiline status
     *
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @return  boolean TRUE if multiline, otherwise FALSE
     * @access  public
     */
    public function getMultiple()
    {
        return $this->_multiple;
    }

    /**
     * sets the value of the input field - override for abstract's setValue() cause we need always
     * overwrite existing value (second parameter TRUE) for correct working radio-fields
     *
     * This method is intend to set the value of the input field
     *
     * @param mixed   $value                  The value to set for this input-field
     * @param boolean $overrideSubmittedValue TRUE to override a submitted value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successful, otherwise FALSE
     * @access public
     */
    public function setValue($value, $overrideSubmittedValue = false)
    {
        // check for override already submitted value
        if (!$overrideSubmittedValue) {
            // try to retrieve submitted value
            $submittedValue = $this->getValue();

            // and set value
            if (!is_null($submittedValue)) {
                $value = $submittedValue;
            }
        }

        // set options selected status
        for ($i = 0; $i < count($this->_options); ++$i) {
            if (!is_array($value)) {
                $value = array($value);
            }

            if (in_array($this->_options[$i]['value'], $value)) {
                $this->_options[$i]['selected'] = 'selected';
            } else {
                $this->_options[$i]['selected'] = null;
            }
        }

        // success
        return true;
    }

    /**
     * sets the value of the input field - override for abstract's setValue() cause we need always
     * overwrite existing value (second parameter TRUE) for correct working radio-fields
     *
     * This method is intend to set the value of the input field
     *
     * @param mixed   $value                  The value to set for this input-field
     * @param boolean $overrideSubmittedValue TRUE to override a submitted value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Select Current active instance
     * @access public
     */
    public function value($value, $overrideSubmittedValue = false)
    {
        $this->setValue($value, $overrideSubmittedValue);

        // for chaining
        return $this;
    }

    /**
     * returns the value of the input field
     *
     * This method is intend to return the value of the input field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Value of the input-field
     * @access public
     */
    public function getValue($raw = false)
    {
        $value = null;

        // if form was allready submitted retrieve value right now
        if ($this->submitted && $raw === false) {
            $value = $this->getSubmittedValue();
        } else {
            for ($i = 0; $i < count($this->_options); $i++ ) {
                if ($this->_options[$i]['selected'] == 'selected') {
                    $value = $this->_options[$i]['value'];
                }
            }
        }

        return $value;
    }

    /**
     * sets the active value of select field
     *
     * This method is intend to set the active value of select field.
     *
     * @param string  $value                  The value to set as active selection
     * @param boolean $overrideSubmittedValue TRUE to override a submitted value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successful, otherwise FALSE
     * @access public
     */
    public function setActive($value, $overrideSubmittedValue = false)
    {
        return $this->setValue($value, $overrideSubmittedValue);
    }

    /**
     * sets the active value of select field
     *
     * This method is intend to set the active value of select field.
     *
     * @param string  $value                  The value to set as active selection
     * @param boolean $overrideSubmittedValue TRUE to override a submitted value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Select Current active instance
     * @access public
     */
    public function active($value, $overrideSubmittedValue = false)
    {
        $this->setActive($value, $overrideSubmittedValue);

        // for chaining
        return $this;
    }

    /*******************************************************************************************************************
     * // BEGIN - PUBLIC HTML-CODE GENERATOR
     ******************************************************************************************************************/

    /**
     * returns the generated HTML-code of this element
     *
     * This method is intend to return the generated HTML-code of this element.
     *
     * @param integer $tabcount Count of tabulators to add for line intend
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The generated HTML-code for this element
     * @access public
     */
    public function render($tabcount = 0)
    {
        // add elements html-code
        $html  = $this->t($tabcount).'<'.$this->type;

        // check multiple name array
        if ($this->getMultiple()) {
            if (!stristr($this->getName(), '[]')) {
                $this->name($this->getName().'[]');
            }
        }

        // iterate over attributes
        foreach ($this->attributes as $attribute => $value) {
            $html .= ' '.$attribute.'="'.$value.'"';
        }

        // break the line and start with adding options
        $html .= '>'.$this->nl();

        // <option value="1" selected="selected">1</option>
        foreach ($this->_options as $option) {
            $selected = (!is_null($option['selected'])) ? ' selected="selected"' : '';
            $html .= $this->t($tabcount+1).'<option value="'.$option['value'].'"'.$selected.'>'.
                     $option['text'].'</option>'."\n";
        }

        // close/end tag
        $html .= $this->t($tabcount).'</select>'.$this->nl();

        // return generated html code
        return $html;
    }

    /*******************************************************************************************************************
     * \\ END - PUBLIC HTML-CODE GENERATOR
     ******************************************************************************************************************/
}

?>
