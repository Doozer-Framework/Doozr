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

require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Form/Service/Component/Formcomponent.php';
require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Form/Service/Component/Interface/Option.php';

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
 * @version    Git: $Id: $
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Form_Service_Component_Option extends DoozR_Form_Service_Component_Formcomponent implements
    DoozR_Form_Service_Component_Interface_Option
{
    /**
     * The tag for this type of element
     *
     * @var string
     * @access protected
     */
    protected $tag = DoozR_Form_Service_Constant::HTML_TAG_OPTION;

    /*------------------------------------------------------------------------------------------------------------------
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for selected
     *
     * @param string $selected The selected value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setSelected($selected = 'selected')
    {
        $this->setAttribute('selected', $selected);
    }

    /**
     * Getter for selected
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The value of selected if set, otherwise NULL
     * @access public
     */
    public function getSelected()
    {
        return $this->getAttribute('selected');
    }

    /**
     * Setter for disabled status
     *
     * @param boolean $state The status
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setDisabled($state)
    {
        if ($state === true) {
            $this->setAttribute('disabled');
        } else {
            $this->removeAttribute('disabled');
        }
    }

    /**
     * Getter for disabled status
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if disabled, otherwise FALSE
     * @access public
     */
    public function getDisabled()
    {
        return $this->getAttribute('disabled');
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
     * @param string|null $value          The value to set, or NULL to use key as value
     * @param string|null $submittedValue The value which was submitted on last request, or NULL
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setValue($value = null, $submittedValue = null)
    {
        if ($submittedValue !== null && $submittedValue === $value) {
            $this->setAttribute('selected');
        } else {
            $this->removeAttribute('selected');
        }

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
