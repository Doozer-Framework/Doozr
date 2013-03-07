<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Module - Form
 *
 * Html.php - Html class for creating block-elements filled with HTML-Code
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
 * Html class for creating block-elements filled with HTML-Code
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
class Html extends DoozR_Form_Module_Element_Abstract implements DoozR_Form_Module_Element_Interface
{
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
        // render and return html-code
        $html = $this->t($tabcount);

        // sort alpha inverted
        sort($this->additionalHtml);
        $this->additionalHtml = array_reverse($this->additionalHtml, true);

        // iterate over stored html and add
        foreach ($this->additionalHtml as $key => $htmlCode) {
            $html .= $htmlCode;
        }

        // finalize with newline
        $html .= $this->nl();

        // and return the result
        return $html;
    }

    /*******************************************************************************************************************
     * \\ END - PUBLIC HTML-CODE GENERATOR
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN - RENDERER -> THIS DIFFERS FOR EACH INPUT-FIELD-TYPE
     ******************************************************************************************************************/

    /**
     * renders the complete HTML-Block for this input-field
     *
     * This method is intend to render the complete HTML-Block for this
     * input-field including optional DIV-Container ...
     *
     * @param boolean $output Controls if rendered HTML should be returned (FALSE) or printed (TRUE)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Rendered HTML
     * @access public
     */
    public function render($output = false)
    {
        // add elements html-code
        $this->html = $this->render(3);

        if (!$output) {
            return $this->html;
        } else {
            echo $this->html;
        }
    }

    /*******************************************************************************************************************
     * \\ END - RENDERER -> THIS DIFFERS FOR EACH INPUT-FIELD-TYPE
     ******************************************************************************************************************/
}

?>
