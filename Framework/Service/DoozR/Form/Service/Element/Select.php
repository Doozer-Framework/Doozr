<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Form - Service
 *
 * Select.php - Extends Html Base element to build a valid select
 * element.
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

require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Form/Service/Element/Html/Base.php';
require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Form/Service/Element/Interface.php';

/**
 * DoozR - Form - Service
 *
 * Extends Html Base element to build a valid select element.
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
class DoozR_Form_Service_Element_Select extends DoozR_Form_Service_Element_Html_Base
    implements
    DoozR_Form_Service_Element_Interface,
    SplObserver
{
    /**
     * The template.
     *
     * @var string
     * @access protected
     */
    protected $template = '<{{TAG}}{{ATTRIBUTES}}>{{INNER-HTML}}</{{TAG}}>';

    /**
     * This is the tag-name for HTML output.
     * e.g. "input" or "form" => in this case = SELECT
     *
     * @var string
     * @access protected
     */
    protected $tag = DoozR_Form_Service_Constant::HTML_TAG_SELECT;

    /**
     * The inner HTML string
     *
     * @var string
     * @access protected
     */
    protected $innerHtml = '';

    /**
     * The validations of this field
     *
     * @var array
     * @access protected
     */
    protected $validation = array();

    /**
     * The arguments passed with current request
     *
     * @var array
     * @access protected
     */
    protected $arguments;

    /**
     * The registry as source for element values
     *
     * @var array
     * @access protected
     */
    protected $registry;

    /**
     * The value here is handled slightly different from other elements
     *
     * @var string
     * @access protected
     */
    protected $value;

    /**
     * The options added to select element
     *
     * @var array
     * @access protected
     */
    protected $options = array();


    /**
     * Constructor.
     *
     * @param string $name The name to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Element_Input $this
     * @access public
     */
    public function __construct($name = '', $arguments = array(), $registry = array())
    {
        $this->setAttribute('name', $name);
        $this->setArguments($arguments);
        $this->setRegistry($registry);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the validity state of the element.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if valid, otherwise FALSE
     * @access public
     */
    public function isValid(
        $arguments = array(),
        $store = array(),
        DoozR_Form_Service_Validate_Validator $validator = null
    ) {
        $valid = true;

        // this is the only element currently which requires a REAL validation
        if (count($this->getValidation()) > 0) {

            $value       = (isset($arguments->{$this->getName()})) ? $arguments->{$this->getName()} : $this->getValue();
            $validations = (isset($store['elements'][$this->getName()]['validation'])) ?
                $store['elements'][$this->getName()]['validation'] :
                $this->getValidation();

            foreach ($validations as $type => $validValues) {
                $valid = $valid && $validator->validate(
                        $type,                                      // the validation type
                        $value,                                     // the value of submitted element
                        $validValues                                // the array / set of validation(s)
                    );
            }
        }

        return $valid;
    }

    /**
     * Specific renderer for DIV.
     *
     * @param boolean $forceRender TRUE to force rerendering of cached content
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed|string HTML as string if set, otherwise NULL
     * @access public
     */
    public function render($forceRender = false)
    {
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

    /*-----------------------------------------------------------------------------------------------------------------+
    | Setter & Getter
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Stores/adds the passed validation information.
     *
     * @param string      $validation The type of validation
     * @param null|string $value      The value for validation or NULL
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Element_Input
     * @access public
     */
    public function addValidation($validation, $value = null)
    {
        if (!isset($this->validation[$validation])) {
            $this->validation[$validation] = array();
        }

        $this->validation[$validation][] = $value;
    }


    public function addOption(DoozR_Form_Service_Element_Option $option)
    {
        $this->options[$option->getKey()] = $option;

        $this->innerHtml = $this->renderInnerHtml($this->options);
    }

    /**
     * Getter for validation.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Validations as array
     * @access public
     */
    public function getValidation()
    {
        return $this->validation;
    }

    /**
     * Setter for value.
     *
     * @param mixed $value The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Getter for value.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Value of this element
     * @access public
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Setter for arguments.
     *
     * @param array|DoozR_Request_Arguments $arguments The arguments
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * Getter for arguments.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array|DoozR_Request_Arguments $arguments The arguments
     * @access public
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Setter for registry.
     *
     * @param array $registry The registry
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setRegistry($registry)
    {
        $this->registry = $registry;
    }

    /**
     * Getter for registry.
     *
     * @param string $key     The key to return from registry
     * @param mixed  $default The default value to return if key does not exist
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The value from registry if key passed, otherwise the whole registry
     * @access public
     */
    public function getRegistry($key = null, $default = null)
    {
        $result = $this->registry;

        if ($key !== null) {
            $result = (isset($result[$key])) ? $result[$key] : $default;
        }

        return $result;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | SPL-Observer
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Update method for SplObserver Interface.
     *
     * @param SplSubject $subject The subject
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function update(SplSubject $subject)
    {
        var_dump($subject);
        pred(__METHOD__);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Internal Helper
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Renders the inner-HTML of thi select element,
     *
     * @param DoozR_Form_Service_Element_Option[] $source The source array containing options
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string HTML of rendered options
     * @access protected
     */
    protected function renderInnerHtml(array $source)
    {
        $html = '';

        foreach ($source as $key => $value) {
            $html .= $value->render();
        }

        return $html;
    }
}
