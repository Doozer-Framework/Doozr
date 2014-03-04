<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Form - Service
 *
 * Option.php - Option part of select field. Extra element cause it
 * has a similar interface like standard html elements. so recycle.
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

require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Form/Service/Element/Html.php';

/**
 * DoozR - Form - Service
 *
 * Option part of select field. Extra element cause it
 * has a similar interface like standard html elements. so recycle.
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
class DoozR_Form_Service_Element_Option extends DoozR_Form_Service_Element_Html
{
    /**
     * The tag for this type of element
     *
     * @var string
     * @access protected
     */
    protected $tag = DoozR_Form_Service_Constant::HTML_TAG_OPTION;

    /**
     * The parent elements name.
     * e.g. to retrieve submitted value.
     *
     * @var DoozR_Form_Service_Element_Select
     * @access protected
     */
    protected $parent;


    /*-----------------------------------------------------------------------------------------------------------------+
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param string $name The name to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Element_Input $this
     * @access public
     */
    public function __construct($key, DoozR_Form_Service_Element_Select &$element, $arguments = array(), $registry = array())
    {
        $this->setKey($key);
        $this->setParent($element);
        $this->setArguments($arguments);
        $this->setRegistry($registry);
    }

    /**
     * Specific renderer for HTML-Elements.
     *
     * @param boolean $forceRender TRUE to force rerendering of cached content
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed|string HTML as string if set, otherwise NULL
     * @access public
     */
    public function render($forceRender = false)
    {
        // Check if this option must be selected before rendering
        $submittedValue = $this->getParent()->getValue();

        if ($submittedValue !== null && $this->getValue() === $submittedValue) {
            $this->setAttribute('selected');
        } else {
            $this->removeAttribute('selected');
        }

        $html     = '';
        $rendered = parent::render($forceRender);

        if ($this->innerHtml !== null) {
            $variables = array(
                'inner-html' => $this->innerHtml
            );

            $html = $this->_tpl($rendered, $variables).DoozR_Form_Service_Constant::NEW_LINE;
            $this->html = $html;
        }

        return $html;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Getter & Setter
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for name of parent <select> element.
     *
     * @param string $parent The name of the parent <select> element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Getter for name of the parent <select> element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The name of the parent <select> element
     * @access public
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Setter for label of this element
     *
     * @param string $label The label to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setLabel($label)
    {
        $this->setAttribute('label', $label);
    }

    /**
     * Getter for label
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The label
     * @access public
     */
    public function getLabel()
    {
        return $this->getAttribute('label');
    }

    /**
     * Setter for value of this element
     *
     * @param string|null $value The value to set or null to use key as value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setValue($value = null)
    {
        /*
        if ($submittedValue !== null &&
            $submittedValue === $this->getValue()
        ) {
            $this->setAttribute('selected');
        } else {
            $this->removeAttribute('selected');
        }
        */

        if ($value === null) {
            $value = $this->getKey();
        }

        $this->setAttribute('value', $value);
    }

    /**
     * Getter for value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The value
     * @access public
     */
    public function getValue()
    {
        return $this->getAttribute('value');
    }

    /**
     * Setter for key [<option>KEY</option>] of this element
     *
     * @param string $key The key to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setKey($key)
    {
        return $this->setInnerHtml($key);
    }

    /**
     * Getter for key
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The key
     * @access public
     */
    public function getKey()
    {
        return $this->getInnerHtml();
    }
}
