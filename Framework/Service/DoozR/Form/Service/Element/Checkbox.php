<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Form - Service
 *
 * Checkbox.php - Extension to default Input-Element <input type="..." ...
 * but with some specific checkbox-field tuning.
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

require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Form/Service/Element/Radio.php';

/**
 * DoozR - Form - Service
 *
 * Checkbox.php - Extension to default Input-Element <input type="..." ...
 * but with some specific checkbox-field tuning.
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
class DoozR_Form_Service_Element_Checkbox extends DoozR_Form_Service_Element_Radio
{
    /**
     * The addition to name for rendering HTML
     * code for multiple input checkboxes.
     *
     * @example <input type="checkbox" name="foo[]" ...
     *
     * @var string
     * @access protected
     * @static
     */
    protected static $multiMarker = '[]';

    /**
     * The multiple input
     *
     * @var boolean
     * @access protected
     */
    protected $multiple = false;


    /**
     * Constructor.
     *
     * @param string $name      The name of this element
     * @param array  $arguments The arguments passed with current request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Element_Checkbox Instance of this class
     * @access public
     */
    public function __construct(
        $name,
        $arguments = array(),
        $registry = array()
    ) {
        $instance = parent::__construct($name, $arguments, $registry);

        // override the type
        $this->setType('checkbox');

        return $instance;
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Setter & Getter
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the name of this element without brackets by default.
     *
     * @param boolean $ripBrackets TRUE to remove brackets from name, FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The name of this element with or without brackets, or NULL if not set
     * @access public
     */
    public function getName($ripBrackets = true)
    {
        $name = $this->getAttribute('name');

        if ($ripBrackets === true) {
            $name = str_replace(self::$multiMarker, '', $this->getAttribute('name'));
        }

        return $name;
    }

    /**
     * Setter for attributes.
     *
     * @param string $key   The key/name of the attribute to set
     * @param string $value The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setAttribute($key, $value = null)
    {
        if ($key === 'name' && stristr($value, self::$multiMarker) !== false) {
            $this->setMultiple(true);
        }

        parent::setAttribute($key, $value);
    }

    /**
     * Returns an attribute of this element.
     *
     * @param string $key The name of the key/attribute to return value for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed|boolean The attributes value if set, FALSE if not
     * @access public
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if ($key === 'name') {
            $value = str_replace(self::$multiMarker, '', $value);
        }

        return $value;
    }

    /**
     * Sets the value of this element
     *
     * @param string $value The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setValue($value)
    {
        // check if element was submitted
        if ($this->wasSubmitted() || $this->existsInRegistry()) {

            if ($this->wasSubmitted()) {
                // get arguments
                $arguments = $this->getArguments();
            } else {
                $arguments = $this->getRegistry();
                $arguments = $arguments['data'];
            }

            if ($this->getMultiple() === true) {
                $checked = in_array($value, $arguments[$this->getName()]);
            } else {
                $checked = $this->isActive();
            }

            if ($checked === true) {
                $this->setAttribute('checked', 'checked');
            }
        }

        // set value
        $this->setAttribute('value', $value);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Tools & Helper
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Sets the multiple status of this element.
     *
     * @param boolean TRUE to mark this field as multi select field,
     *                FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setMultiple($status)
    {
        $this->multiple = $status;
    }

    /**
     * Returns the multiple status of this element.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if field is multi select, FALSE if not
     * @access protected
     */
    protected function getMultiple()
    {
        return $this->multiple;
    }

    /**
     * Returns the submission status of this element.
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
     * Checks if this element was already stored in registry before
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if this element exists in registry, otherwise FALSE
     * @access protected
     */
    protected function existsInRegistry()
    {
        $registry = $this->getRegistry();

        return (isset($registry['data'][$this->getName()]));
    }
}
