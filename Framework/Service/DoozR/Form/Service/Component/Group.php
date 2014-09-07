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
 * Copyright (c) 2005 - 2014, Benjamin Carl - All rights reserved.
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
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Form/Service/Component/Html.php';

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
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Form_Service_Component_Group extends DoozR_Form_Service_Component_Html
{
    /**
     * The tag of this component
     *
     * @var string
     * @access protected
     */
    protected $tag = DoozR_Form_Service_Constant::HTML_TAG_DIV;

    /**
     * The template is required for output. Each HTML-Component inherits
     * this base template and so every element based on this base class
     * is renderable. This template produces at least a correct HTML tag
     * which must not be valid in an other context!
     *
     * @var string
     * @access protected
     */
    protected $template = '<{{TAG}}{{ATTRIBUTES}}>{{INNER-HTML}}</{{TAG}}>';

    /**
     * This defines the order of the components
     * if one of those components does not exist
     * it won't be rendered and the next item
     * is used.
     *
     * @var array
     * @access protected
     */
    protected $order = array(
        self::LABEL,
        self::COMPONENT,
        self::MESSAGE,
    );

    /**
     * The order constant for LABEL
     *
     * @var string
     * @access public
     * @const
     */
    const LABEL = 'label';

    /**
     * The order constant for COMPONENTS
     *
     * @var string
     * @access public
     * @const
     */
    const COMPONENT = 'component';

    /**
     * The order constant for MESSAGE
     *
     * @var string
     * @access public
     * @const
     */
    const MESSAGE = 'message';

    /**
     * An index mapping elements to its type
     *
     * @var array
     * @access protected
     */
    protected $index = array();

    /**
     * The index in reverse lookup preparation (key <=> value)
     *
     * @var array
     * @access protected
     */
    protected $indexReverse = array();

    /**
     * Constructor.
     *
     * @param DoozR_Form_Service_Renderer_Interface                                                     $renderer
     * @param DoozR_Form_Service_Validator_Interface                                                    $validator
     * @param DoozR_Form_Service_Component_Interface_Html|DoozR_Form_Service_Component_Interface_Html[] $label     0 to n Label
     * @param DoozR_Form_Service_Component_Interface_Html|DoozR_Form_Service_Component_Interface_Html[] $component 0 to n Components
     * @param DoozR_Form_Service_Component_Message|DoozR_Form_Service_Component_Message[]               $message   0 to n Message components
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Component_Group $this
     * @access public
     */
    public function __construct(
        DoozR_Form_Service_Renderer_Interface  $renderer  = null,
        DoozR_Form_Service_Validator_Interface $validator = null,
                                               $label     = null,
                                               $component = null,
                                               $message   = null
    ) {
        if ($label !== null) {
            if (is_array($label)) {
                foreach ($label as $singleLabel) {
                    $this->addLabel($singleLabel);
                }
            } else {
                $this->addLabel($label);
            }
        }

        if ($component !== null) {
            if (is_array($component)) {
                foreach ($component as $singleComponent) {
                    $this->addComponent($singleComponent);
                }
            } else {
                $this->addComponent($component);
            }
        }

        if ($message instanceof DoozR_Form_Service_Component_Message) {
            if (is_array($message)) {
                foreach ($message as $singleMessage) {
                    $this->addMessage($singleMessage);
                }
            } else {
                $this->addMessage($message);
            }
        }

        parent::__construct(null, null, $renderer);

        // automagic management
        $this->wire();
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Control layer
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Adds a label to the group.
     *
     * @param DoozR_Form_Service_Component_Interface_Html $label The label instance to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The index of the added label
     * @access public
     */
    public function addLabel(DoozR_Form_Service_Component_Interface_Html $label)
    {
        $index               = $this->addChild($label);
        $this->index[$index] = self::LABEL;

        (!isset($this->indexReverse[self::LABEL])) ? $this->indexReverse[self::LABEL] = array() : null;
        $this->indexReverse[self::LABEL][] = $index;

        return $index;
    }

    /**
     * Adds an element to the group.
     *
     * @param DoozR_Form_Service_Component_Interface_Html $element Instance of element to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The index of the added component
     * @access public
     */
    public function addComponent(DoozR_Form_Service_Component_Interface_Html $element)
    {
        $index               = $this->addChild($element);
        $this->index[$index] = self::COMPONENT;

        (!isset($this->indexReverse[self::COMPONENT])) ? $this->indexReverse[self::COMPONENT] = array() : null;
        $this->indexReverse[self::COMPONENT][] = $index;

        return $index;
    }

    /**
     * Adds a message to the group.
     *
     * @param DoozR_Form_Service_Component_Message $message Instance of message to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The index of the added message
     * @access public
     */
    public function addMessage(DoozR_Form_Service_Component_Message $message)
    {
        $index               = $this->addChild($message);
        $this->index[$index] = self::MESSAGE;

        (!isset($this->indexReverse[self::MESSAGE])) ? $this->indexReverse[self::MESSAGE] = array() : null;
        $this->indexReverse[self::MESSAGE][] = $index;

        return $index;
    }

    /**
     * Sets the label of this group.
     *
     * @param DoozR_Form_Service_Component_Label $label
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The index of the added label
     * @access public
     */
    public function setLabel(DoozR_Form_Service_Component_Label $label)
    {
        return $this->addLabel($label);
    }

    /**
     * Returns the label of this group.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Component_Label|null The Instance of label if set, otherwise NULL
     * @access public
     */
    public function getLabels()
    {
        $result = array();

        $labels = (isset($this->indexReverse[self::LABEL])) ? $this->indexReverse[self::LABEL] : array();

        foreach ($labels as $index) {
            $result[] = $this->getChild($index);
        }

        return $result;
    }

    /**
     * Sets the element of this group.
     *
     * @param DoozR_Form_Service_Component_Interface_Html $component The component to set
     *
     * @author   Benjamin Carl <opensource@clickalicious.de>
     * @return   integer The index of the added component
     * @access   public
     */
    public function setComponent(DoozR_Form_Service_Component_Interface_Html $component)
    {
        return $this->addComponent($component);
    }

    /**
     * Returns the Component of this group.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Component_Input[]|null The Instance of element if set, otherwise NULL
     * @access public
     */
    public function getComponents()
    {
        $components = array();

        foreach ($this->indexReverse[self::COMPONENT] as $index) {
            $components[] = $this->getChild($index);
        }

        return $components;
    }

    /**
     * Sets the message of this group.
     *
     * @param DoozR_Form_Service_Component_Message $message
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setMessage(DoozR_Form_Service_Component_Message $message)
    {
        $this->addChild($message, 'message');
    }

    /**
     * Returns the message of this group.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Component_Message|null The Instance of Message if set, otherwise NULL
     * @access public
     */
    public function getMessage()
    {
        $result = array();

        $messages = (isset($this->indexReverse[self::MESSAGE])) ? $this->indexReverse[self::MESSAGE] : array();

        foreach ($messages as $index) {
            $result[] = $this->getChild($index);
        }

        return $result;
    }

    /**
     * Setter for order.
     *
     * @param array $order The order of elements. MUST include: self::LABEL, self::COMPONENT, self::MESSAGE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @throws DoozR_Form_Service_Exception
     */
    public function setOrder(array $order)
    {
        // Check requirements
        if (
            in_array(self::LABEL,     $order) === false ||
            in_array(self::COMPONENT, $order) === false ||
            in_array(self::MESSAGE,   $order) === false
        ) {
            throw new DoozR_Form_Service_Exception(
                'Alda'
            );
        }

        $this->order = $order;
    }

    /**
     * Setter for order.
     *
     * @param array $order The order of elements. MUST include: self::LABEL, self::COMPONENT, self::MESSAGE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     * @throws DoozR_Form_Service_Exception
     */
    public function order(array $order)
    {
        $this->setOrder($order);
        return $this;
    }

    /**
     * Getter for order.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The order
     * @access public
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Renders a component a returns it HTML code.
     *
     * @param boolean $force TRUE to re-render a already rendered component,
     *                       otherwise FALSE to use cached result if exist
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null A string containing the resulting HTML code,
     *                     NULL on error
     * @access public
     */
    public function render($force = false)
    {
        // Do custom sort and stuff like this and proxy forward the call to render to renderer->render(...)
        $this->setChilds(
            $this->sort($this->order)
        );

        // Return the rendered result
        return parent::render($force);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Tools & Helper
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Sorts an array by passed order
     *
     * @param array $order The order of the components
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The ordered result
     * @access protected
     */
    protected function sort($order)
    {
        $ordered = array();

        $matrix = array(
            'label'     => 'getLabels',
            'component' => 'getComponents',
            'message'   => 'getMessage',
        );

        foreach ($order as $identifier) {
            $components = $this->{$matrix[$identifier]}();
            foreach ($components as $component) {
                $index = count($ordered);
                $this->indexReverse[$identifier][0] = $index;
                $this->index[$index] = $identifier;
                $ordered[] = $component;
            }
        }

        return $ordered;
    }

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
        $labels     = (isset($this->indexReverse[self::LABEL])) ? $this->indexReverse[self::LABEL] : null;
        $components = (isset($this->indexReverse[self::COMPONENT])) ? $this->indexReverse[self::COMPONENT] : null;

        /* @var DoozR_Form_Service_Component_Formcomponent $child */
        for ($i = 0; $i < count($labels); ++$i) {
            if (isset($components[$i])) {
                $label = $this->getChild($labels[$i]);
                $label->setAttribute('for', $this->getChild($components[$i])->getId());
                $this->addChild($label, $labels[$i]);
            }
        }
    }
}
