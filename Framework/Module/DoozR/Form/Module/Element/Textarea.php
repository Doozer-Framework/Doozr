<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Module - Form
 *
 * Textarea.php - Textarea class for creating fields e.g. of type "textarea"
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
 * Textarea class for creating fields e.g. of type "textarea"
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
class Textarea extends DoozR_Form_Module_Element_Abstract implements DoozR_Form_Module_Element_Interface
{
    /*******************************************************************************************************************
     * // BEGIN - PUBLIC TEXTAREA SPECIFIC PROPERTY SETTER AND GETTER
     ******************************************************************************************************************/

    /**
     * sets the count of cols of form-element textarea
     *
     * This method is intend to set the count of cols of form-element textarea.
     *
     * @param integer $cols The count of cols to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setCols($cols)
    {
        return $this->setAttribute('cols', $cols);
    }

    /**
     * sets the count of cols of form-element textarea
     *
     * This method is intend to set the count of cols of form-element textarea.
     *
     * @param integer $cols The count of cols to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Textarea The current active instance
     * @access public
     */
    public function cols($cols)
    {
        $this->setCols($cols);

        // for chaining
        return $this;
    }

    /**
     * returns the count of cols of form-element textarea
     *
     * This method is intend to return the count of cols of form-element textarea.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed INTEGER count of cols of form-element, otherwise NULL
     * @access public
     */
    public function getCols()
    {
        return $this->getAttribute('cols');
    }

    /**
     * sets the count of rows of form-element textarea
     *
     * This method is intend to set the count of rows of form-element textarea.
     *
     * @param integer $rows The count of rows to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setRows($rows)
    {
        return $this->setAttribute('rows', $rows);
    }

    /**
     * sets the count of rows of form-element textarea
     *
     * This method is intend to set the count of rows of form-element textarea.
     *
     * @param integer $rows The count of rows to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Textarea The current active instance
     * @access public
     */
    public function rows($rows)
    {
        $this->setRows($rows);

        // for chaining
        return $this;
    }

    /**
     * returns the count of rows of form-element textarea
     *
     * This method is intend to return the count of rows of form-element textarea.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed INTEGER count of rows of form-element, otherwise NULL
     * @access public
     */
    public function getRows()
    {
        return $this->getAttribute('rows');
    }

    /*******************************************************************************************************************
     * \\ END - PUBLIC TEXTAREA SPECIFIC PROPERTY SETTER AND GETTER
     ******************************************************************************************************************/

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
        // begin of html-code block
        $html = $this->t($tabcount).'<'.$this->type;

        // iterate over attributes and set
        foreach ($this->attributes as $attribute => $value) {
            $html .= ' '.$attribute.'="'.$value.'"';
        }

        // set value - close tag and add line break
        $html .= '>'.$this->attributes['value'].'</textarea>'.$this->nl();

        // return generated HTML-code
        return $html;
    }

    /*******************************************************************************************************************
     * \\ END - PUBLIC HTML-CODE GENERATOR
     ******************************************************************************************************************/
}

?>
