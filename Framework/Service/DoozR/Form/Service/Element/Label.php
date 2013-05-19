<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Service - Form
 *
 * Input.php - Input-field class for creating input-fields e.g. of type "text",
 * "password", ...
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
 * Input-field class for creating input-fields e.g. of type "text", "password", ...
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
class Label extends DoozR_Form_Service_Element_Abstract implements DoozR_Form_Service_Element_Interface
{
    protected $position = array(
        'element' => 0
    );

    protected $content;


    public function isValid()
    {
        // return the valid status of this field = alway NULL in this label case
        return null;
    }

    public function hasImpact()
    {
        return null;
    }


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
        // assume empty html output
        $this->html = '';

        // sort types by position
        asort($this->position);

        // merge existing styles and css-classes for rendering
        $style = $this->combineStyles();

        // iterate the elements of the current processed field
        foreach ($this->position as $part => $position) {
            $this->html .= $this->renderPart($part, $style);
        }

        // add all elements with surrounding div
        $this->html = $this->setContainer($this->html);

        // check for direct output or return of value
        return $this->html;
    }


    protected function renderElement()
    {
        $html = '';

        $message = $this->content;
        $html    = '<label';

        foreach ($this->attributes as $attribute => $value) {
            $html .= ' '.$attribute.'="'.$value.'"';
        }

        $html .= '>'.$message.'</label>'.$this->nl();

        return $html;
    }


    public function html($html)
    {
        $this->content = $html;

        return $this;
    }

    public function text($html)
    {
        $this->content = strip_tags($html);

        return $this;
    }
}

?>
