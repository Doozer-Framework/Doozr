<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Form - Service
 *
 * Radio.php - Extension to default Input-Element <input type="..." ...
 * but with some specific radio-field tuning.
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

require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Form/Service/Element/Input.php';

/**
 * DoozR - Form - Service
 *
 * Extension to default Input-Element <input type="..." ...
 * but with some specific radio-field tuning.
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
class DoozR_Form_Service_Element_Radio extends DoozR_Form_Service_Element_Input
{
    /**
     * Constructor.
     *
     * @param string $name      The name of the element
     * @param array  $arguments The arguments passed with current request (e.g. $_POST, $_GET ...)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Element_Radio Instance of this class
     * @access public
     */
    public function __construct(
        $name,
        $arguments = array(),
        $registry = array()
    ) {
        $this->setType('radio');
        return parent::__construct($name, $arguments, $registry);
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Checks this element.
     *
     * @param boolean $override TRUE to force check, FALSE to preserve unchecked state if not active
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function check($override = false)
    {
        if ($override === true || $this->wasSubmitted() === false || $this->isActive() === true) {
            $this->setAttribute('checked', 'checked');
        }
    }

    /**
     * Unchecks this element.
     *
     * @param boolean $override TRUE to force uncheck, FALSE to preserve checked state if not active
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function uncheck($override = false)
    {
        if ($override === true || $this->wasSubmitted() === false || $this->isActive() === false) {
            $this->removeAttribute('checked');
        }
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Setter & Getter
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for value.
     *
     * @param string $value The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setValue($value)
    {
        // check if element must be checked
        $arguments = $this->getArguments();
        $registry  = $this->getRegistry('data', array());

        if (
            (isset($arguments[$this->getName()]) && $arguments[$this->getName()] == $value) ||
            (isset($registry[$this->getName()]) && $registry[$this->getName()] == $value)
        ) {
            $this->setAttribute('checked', 'checked');
        }

        $this->setAttribute('value', $value);
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Tools & Helper
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the submission status of this element.
     *
     * @param string $name The name to use for check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if was submitted, otherwise FALSE
     * @access protected
     */
    protected function wasSubmitted()
    {
        $arguments = $this->getArguments();

        return (isset($arguments[$this->getName()]));
    }

    /**
     * Returns the active status of this element.
     * Active = TRUE means that this element was selected (from a group of elements).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if the element is active, FALSE if not
     * @access protected
     */
    protected function isActive()
    {
        // assume not active
        $result = false;

        // check if element must be checked
        $arguments = $this->getArguments();
        $registry  = $this->getRegistry('data', array());

        if (
            (isset($arguments[$this->getName()]) && $arguments[$this->getName()] == $this->getValue()) ||
            (isset($registry[$this->getName()]) && $registry[$this->getName()] == $this->getValue())
        ) {
            $result = true;
        }

        return $result;
    }
}
