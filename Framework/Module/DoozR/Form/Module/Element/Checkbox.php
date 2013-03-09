<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Module - Form
 *
 * Checkbox.php - Checkbox class for creating fields e.g. of type "Checkbox"
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
 * @package    DoozR_Module
 * @subpackage DoozR_Module_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'Module/DoozR/Form/Module/Element/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT.'Module/DoozR/Form/Module/Element/Interface.php';

/**
 * DoozR - Module - Form
 *
 * Checkbox class for creating fields e.g. of type "checkbox"
 *
 * @category   DoozR
 * @package    DoozR_Module
 * @subpackage DoozR_Module_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class Checkbox extends DoozR_Form_Module_Element_Abstract implements DoozR_Form_Module_Element_Interface
{
    /**
     * The checked status for checkbox-field
     *
     * @var boolean
     * @access private
     */
    private $_checked = false;

    /**
     * Positioning array of this element
     * Default CHECKBOX LABEL ERROR
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
     * preselects this checkbox-field or removes preselection
     *
     * This method is intend to preselect this checkbox-field or removes preselection
     *
     * @param boolean $status TRUE to preselect field, otherwise FALSE to remove preselection
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successful, otherwise FALSE
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

        // success
        return true;
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
