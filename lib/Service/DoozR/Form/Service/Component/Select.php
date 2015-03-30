<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Form - Service
 *
 * Select.php - Extends Html Base component to build a valid select
 * component.
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2015, Benjamin Carl - All rights reserved.
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
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Form/Service/Component/Formcomponent.php';
require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Form/Service/Component/Interface/Option.php';

/**
 * DoozR - Form - Service
 *
 * Extends Html Base component to build a valid select component.
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Form_Service_Component_Select extends DoozR_Form_Service_Component_Formcomponent
{
    /**
     * This is the tag-name for HTML output.
     * e.g. "input" or "form" => in this case = SELECT
     *
     * @var string
     * @access protected
     */
    protected $tag = DoozR_Form_Service_Constant::HTML_TAG_SELECT;

    /**
     * Mark this component as parent
     *
     * @var string
     * @access protected
     */
    protected $type = DoozR_Form_Service_Constant::COMPONENT_CONTAINER;

    /*------------------------------------------------------------------------------------------------------------------
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for Autofocus
     *
     * @param string $autofocus The autofocus value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setAutofocus($autofocus = null)
    {
        $this->setAttribute('autofocus', $autofocus);
    }

    /**
     * Getter for Autofocus
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function getAutofocus()
    {
        return $this->getAttribute('autofocus');
    }

    /**
     * Setter for Disabled
     *
     * @param string $disabled The disabled value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setDisabled($disabled = 'disabled')
    {
        $this->setAttribute('disabled', $disabled);
    }

    /**
     * Getter for Disabled
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The value if set, otherwise NULL
     * @access public
     */
    public function getDisabled()
    {
        return $this->getAttribute('disabled');
    }

    /**
     * Setter for Form
     *
     * @param string $form The form value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setForm($form)
    {
        $this->setAttribute('form', $form);
    }

    /**
     * Getter for Form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The value if set, otherwise NULL
     * @access public
     */
    public function getForm()
    {
        return $this->getAttribute('form');
    }

    /**
     * Setter for multiple
     *
     * @param string $multiple The multiple value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setMultiple($multiple = 'multiple')
    {
        $this->setAttribute('multiple', $multiple);
    }

    /**
     * Getter for multiple
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The value if set, otherwise NULL
     * @access public
     */
    public function getMultiple()
    {
        return $this->getAttribute('multiple');
    }

    /**
     * Setter for required
     *
     * @param string $required The required value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setRequired($required)
    {
        $this->setAttribute('required', $required);
    }

    /**
     * Getter for required
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The value if set, otherwise NULL
     * @access public
     */
    public function getRequired()
    {
        return $this->getAttribute('required');
    }

    /**
     * Setter for size
     *
     * @param string $size The size to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setSize($size)
    {
        $this->setAttribute('size', $size);
    }

    /**
     * Getter for size
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The value if set, otherwise NULL
     * @access public
     */
    public function getSize()
    {
        return $this->getAttribute('size');
    }

    /**
     * Proxy to addChild() to filter input components
     *
     * @param DoozR_Form_Service_Component_Interface_Option $option The component to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function addOption(DoozR_Form_Service_Component_Interface_Option $option)
    {
        return $this->addChild($option);
    }

    /**
     * Proxy to removeChild() to filter input components
     *
     * @param int $index The index of the component to remove
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function removeOption($index)
    {
        return $this->removeChild($index);
    }
}
