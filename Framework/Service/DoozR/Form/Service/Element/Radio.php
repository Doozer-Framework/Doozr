<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Service - Form
 *
 * Radio.php - Radio class for creating fields e.g. of type "Radio"
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
 * Radio class for creating fields e.g. of type "radio"
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Release: @package_version@
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class Radio extends DoozR_Form_Service_Element_Abstract implements DoozR_Form_Service_Element_Interface
{
    /**
     * The checked status for radio-field
     *
     * @var boolean
     * @access private
     */
    private $_checked = false;

    /**
     * Positioning array of this element
     * Default RADIO LABEL ERROR
     *
     * @var array
     * @access protected
     */
    protected $position = array(
        'label'   => 1,
        'element' => 0,
        'error'   => 2
    );


    /*******************************************************************************************************************
     * // BEGIN - PUBLIC TEXTAREA SPECIFIC PROPERTY SETTER AND GETTER
     ******************************************************************************************************************/

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
    public function setValue($value, $overrideSubmittedValue = true)
    {
        return parent::setValue($value, true);
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
     * @return Radio The current active instance
     * @access public
     */
    public function value($value, $overrideSubmittedValue = true)
    {
        $this->setValue($value, $overrideSubmittedValue);

        // for chaining
        return $this;
    }

    /**
     * preselects this radio-field or removes preselection
     *
     * This method is intend to preselect this radio-field or removes preselection
     *
     * @param boolean $status TRUE to preselect field, otherwise FALSE to remove preselection
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Radio The current active instance
     * @access public
     */
    public function preselected($status = true)
    {
        // preselect?
        if ($status) {
            // now ensure that form wasn't submitted or no value was submitted
            if (!$this->submitted || is_null($this->getValue())) {
                $this->_checked = true;
            }
        } else {
            // remove preselection
            $this->_checked = false;
        }

        // for chaining
        return $this;
    }

    /**
     * checks the input-field is valid
     *
     * This method is intend to check if the input-field is valid
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if field is valid, otherwise FALSE
     * @access protected
     */
    public function isValid()
    {
        // check if form was submitted
        if ($this->submitted) {

            // get session module
            $session = DoozR_Loader_Serviceloader::load('session');

            // get swtup/config of form from session
            $validationType = $session->get($this->parent);

            // check if field is set and if it is required
            if (isset($validationType[$this->name]['validType'])) {
                // retrieve the value we expect for match-check
                $valueExpected = $validationType[$this->name]['validType'];

                // retrieve the retrieved value from request
                $valueRetrieved = $this->getValue(true);

                // if both values don't match => return FALSE (invalid)
                if (!in_array($valueRetrieved, $valueExpected)) {
                    return false;
                }
            }
        }

        // if not submitted all values are valid ones
        return true;
    }

    /**
     * Renders the label and returns HTML-code
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string generated HTML-code
     * @access protected
     */
    protected function renderLabel()
    {
        $message = $this->getMessage('label');
        $id      = ($this->getId()) ? ' id="label_'.$this->getId().'"' : '';
        $style   = (isset($style['style']) && $style['style'] !== null) ? ' style="'.$style['style'].'"' : '';
        $css     = (isset($style['css']) && $style['css'] !== null) ? ' class="'.$style['css'].'" ' : '';
        $html    = '<label for="'.(($this->getId()) ? $this->getId() : $this->getName()).'"'.$id.$css.$style.'>'.
                        $message.'</label>'.$this->nl();

        return $html;
    }

    /**
     * Renders the element and returns HTML-code
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string generated HTML-code
     * @access protected
     */
    protected function renderElement()
    {
        $html = '';

        $message = $this->getMessage('element');
        $html    = '<input type="'.$this->type.'"';

        foreach ($this->attributes as $attribute => $value) {
            $html .= ' '.$attribute.'="'.$value.'"';
        }

        if ($this->submitted) {
            // get submitted value for element
            $value = $this->getSubmittedValue();

        } elseif ($this->jumped) {
            // get jump value for element
            $value = $this->getJumpValue();
        }

        // and check if match
        if ($this->_checked || (($value !== null) && $value == $this->getValue())) {
            $html .= ' checked="checked"';
        }

        $html .= ' />'.$this->nl();

        return $html;
    }

    /*******************************************************************************************************************
     * \\ END - PUBLIC TEXTAREA SPECIFIC PROPERTY SETTER AND GETTER
     ******************************************************************************************************************/
}

?>
