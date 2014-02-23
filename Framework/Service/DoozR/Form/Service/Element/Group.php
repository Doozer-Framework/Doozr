<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Form - Service
 *
 * Group.php - The group element control layer which adds validation,
 * and so on to an HTML element.
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
 * The group element control layer which adds validation,
 * and so on to an HTML element.
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
class DoozR_Form_Service_Element_Group extends DoozR_Form_Service_Element_Html_Container
    implements DoozR_Form_Service_Element_Interface, SplObserver
{
    /**
     * The template is required for output. Each HTML-Element inherits
     * this base template and so every element based on this base class
     * is renderable. This template produces at least a correct HTML tag
     * which must not be valid in an other context!
     *
     * @var string
     * @access protected
     */
    protected $template = '{{ELEMENTS}}';

    /**
     * This defines the order of the elements
     * if one of those elements does not exist
     * it won't be rendered and the next item
     * is used.
     *
     * @var array
     * @access protected
     */
    protected $order = array(
        self::LABELS,
        self::ELEMENTS,
        self::MESSAGE
    );

    /**
     * The validations of this field
     *
     * @var array
     * @access protected
     */
    protected $validation = array();

    /**
     * The order constant for LABEL
     *
     * @var string
     * @access public
     * @const
     */
    const LABELS   = 'labels';

    /**
     * The order constant for ELEMENTS
     *
     * @var string
     * @access public
     * @const
     */
    const ELEMENTS = 'elements';

    /**
     * The order constant for MESSAGE
     *
     * @var string
     * @access public
     * @const
     */
    const MESSAGE  = 'message';


    /**
     * Constructor.
     *
     * @param DoozR_Form_Service_Element_Label[]|DoozR_Form_Service_Element_Label $label    The label instance
     * @param DoozR_Form_Service_Element_Input[]|DoozR_Form_Service_Element_Input $elements The element instance
     * @param DoozR_Form_Service_Element_Message                                  $message  The message instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Element_Group $this
     * @access public
     */
    public function __construct(
        $labels = null,
        $elements = null,
        DoozR_Form_Service_Element_Message $message = null
    ) {
        // is an array of elements -> checkbox, radio ...
        if (is_array($labels)) {
            foreach ($labels as $label) {
                $this->add($label, 'labels');
            }

        } elseif ($labels !== null) {
            $this->add($labels, 'labels');

        }

        // is an array of elements -> checkbox, radio ...
        if (is_array($elements)) {
            foreach ($elements as $element) {
                $this->add($element, 'elements');
            }

        } elseif ($elements !== null) {
            $this->add($elements, 'elements');

        }

        if ($message !== null) {
            $this->add($message, 'message');
        }

        // automagic management
        $this->wire();
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Control layer
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Adds a label to the group.
     *
     * @param DoozR_Form_Service_Element_Label $label The label instance to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function addLabel(DoozR_Form_Service_Element_Label $label)
    {
        $this->add($label, 'labels');
    }

    /**
     * Adds an element to the group.
     *
     * @param DoozR_Form_Service_Element_Interface $element Instance of element to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function addElement(DoozR_Form_Service_Element_Interface $element)
    {
        $this->add($element, 'elements');
    }

    /**
     * Adds a message to the group.
     *
     * @param DoozR_Form_Service_Element_Message $message Instance of message to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function addMessage(DoozR_Form_Service_Element_Message $message)
    {
        $this->add($message, 'message');
    }

    /**
     * Adds a child to the collection of childs
     *
     * @param DoozR_Form_Service_Element_Interface $child
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The id of the child as reference
     * @access public
     */
    public function add(DoozR_Form_Service_Element_Interface $child, $id = null)
    {
        $id = ($id === null) ? count($this->childs) : $id;

        if (!isset($this->childs[$id])) {
            $this->childs[$id] = array();
        }

        $this->childs[$id][] = $child;

        return $id;
    }

    /**
     * Sets the label of this group.
     *
     * @param DoozR_Form_Service_Element_Label $label
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setLabel(DoozR_Form_Service_Element_Label $label)
    {
        $this->add($label, 'labels');
    }

    /**
     * Returns the label of this group.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Element_Label|null The Instance of label if set, otherwise NULL
     * @access public
     */
    public function getLabels()
    {
        return $this->getChild('labels');
    }

    /**
     * Sets the element of this group.
     *
     * @param DoozR_Form_Service_Element_Interface $element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setElement(DoozR_Form_Service_Element_Interface $element)
    {
        $this->add($element, 'elements');
    }

    /**
     * Returns the Element of this group.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Element_Input[]|null The Instance of element if set, otherwise NULL
     * @access public
     */
    public function getElements()
    {
        return $this->getChild('elements');
    }

    /**
     * Sets the message of this group.
     *
     * @param DoozR_Form_Service_Element_Message $message
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setMessage(DoozR_Form_Service_Element_Message $message)
    {
        $this->add($message, 'message');
    }

    /**
     * Returns the message of this group.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Element_Message|null The Instance of Message if set, otherwise NULL
     * @access public
     */
    public function getMessage()
    {
        return $this->getChild('message');
    }

    /**
     * Returns the validity state of the form.
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

        /* @var DoozR_Form_Service_Element_Interface $child */
        foreach ($this->getChilds() as $child) {
            $valid = $valid && $child->isValid($arguments, $store, $validator);
        }

        return $valid;
    }

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
        $this->setAttribute('value', $value);
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
        $this->getAttribute('value');
    }

    /**
     * Default renderer for all basic HTML-elements. This renderer is capable
     * of parsing HTML-elements "properties" and makes use of the $template
     * variable and the mini-templating functionality of this class (_tpl()).
     *
     * @param boolean $output      TRUE to echo out the rendered result (HTML), FALSE to do not (default)
     * @param boolean $forceRender TRUE to override primitive caching (FALSE = default)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed|string
     * @see _tpl(), $template
     */
    public function render($output = false, $forceRender = false)
    {
        $html = '';

        if ($this->html === null || $forceRender === true) {

            $orderedElements = $this->sort($this->childs, $this->order);

            foreach ($orderedElements as $object) {
                $html .= ' '.$object->render().DoozR_Form_Service_Constant::NEW_LINE;
            }

            $templateVariables = array(
                'elements' => $html,
                'tag'      => $this->tag
            );

            $html = $this->_tpl($this->template, $templateVariables);
            $this->html = $html.DoozR_Form_Service_Constant::NEW_LINE;
        }

        if ($output === true) {
            echo $this->html;
        }

        return $this->html;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Tools & Helper
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Sorts an array by passed order
     *
     * @param array $attributes The subject we work on
     * @param array $order      The order of the elements
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The ordered result
     * @access protected
     */
    protected function sort(array $attributes, $order)
    {
        $ordered = array();

        // flatten the array
        foreach ($attributes as $identifier => $objects) {
            foreach ($objects as $object) {
                $ordered[] = $object;
            }
        }

        return $ordered;
    }

    /**
     * Locates an element in an array and return its position.
     *
     * @param string $identifier The identifier used for lookup
     * @param array  $subject    The subject to look in
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    /*
    private function _locate($identifier, array $subject)
    {
        $countElements = count($subject);

        for ($i = 0; $i < $countElements; ++$i) {
            if ($subject[$i] === $identifier) {
                return $i;
            }
        }
    }
    */

    /*-----------------------------------------------------------------------------------------------------------------*
    | Automagic Layer
    *-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Connects the element with the for attribute of the label.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function wire()
    {
        // 1st get name of element attached
        if ($elements = $this->getElements()) {

            if (is_array($elements)) {

                $i = 0;
                /* @var DoozR_Form_Service_Element_Input $element */
                foreach ($elements as $element) {
                    $id = $element->getAttribute('id');

                    if ($id !== null) {
                        $this->childs['labels'][$i]->setAttribute('for', $id);
                    }

                    ++$i;
                }
            } else {
                $id = $elements->getAttribute('id');

                if ($id !== null) {
                    $this->getLabels()->setAttribute('for', $id);
                }
            }
        }
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
}
