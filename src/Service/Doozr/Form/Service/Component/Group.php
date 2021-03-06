<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service.
 *
 * Group.php - The group element control layer which adds validation,
 * and so on to an HTML element.
 *
 * PHP versions 5.5
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
 *
 * Copyright (c) 2005 - 2016, Benjamin Carl - All rights reserved.
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
 *   must display the following acknowledgment: This product includes software
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
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
require_once DOOZR_DOCUMENT_ROOT.'Service/Doozr/Form/Service/Component/Formcomponent.php';

/**
 * Doozr - Form - Service.
 *
 * The group element control layer which adds validation,
 * and so on to an HTML element.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Form_Service_Component_Group extends Doozr_Form_Service_Component_Formcomponent
{
    /**
     * The tag of this component.
     *
     * @var string
     */
    protected $tag = Doozr_Form_Service_Constant::HTML_TAG_DIV;

    /**
     * The template is required for output. Each HTML-Component inherits
     * this base template and so every element based on this base class
     * is renderable. This template produces at least a correct HTML tag
     * which must not be valid in an other context!
     *
     * @var string
     */
    protected $template = '<{{TAG}}{{ATTRIBUTES}}>{{INNER-HTML}}</{{TAG}}>';

    /**
     * This defines the order of the components
     * if one of those components does not exist
     * it won't be rendered and the next item
     * is used.
     *
     * @var array
     */
    protected $order = [
        self::LABEL,
        self::COMPONENT,
        self::MESSAGE,
    ];

    /**
     * The order constant for LABEL.
     *
     * @var string
     */
    const LABEL = 'label';

    /**
     * The order constant for COMPONENTS.
     *
     * @var string
     */
    const COMPONENT = 'component';

    /**
     * The order constant for MESSAGE.
     *
     * @var string
     */
    const MESSAGE = 'message';

    /**
     * An index mapping elements to its type.
     *
     * @var array
     */
    protected $index = [];

    /**
     * The index in reverse lookup preparation (key <=> value).
     *
     * @var array
     */
    protected $indexReverse = [];

    /**
     * Constructor.
     *
     * @param Doozr_Form_Service_Renderer_Interface                                                     $renderer
     * @param Doozr_Form_Service_Validator_Interface                                                    $validator
     * @param Doozr_Form_Service_Component_Interface_Html|Doozr_Form_Service_Component_Interface_Html[] $label     0 to n Label
     * @param Doozr_Form_Service_Component_Interface_Html|Doozr_Form_Service_Component_Interface_Html[] $component 0 to n Components
     * @param Doozr_Form_Service_Component_Message|Doozr_Form_Service_Component_Message[]               $message   0 to n Message components
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Form_Service_Component_Group $this
     */
    public function __construct(
        Doozr_Form_Service_Renderer_Interface  $renderer = null,
        Doozr_Form_Service_Validator_Interface $validator = null,
                                               $label = null,
                                               $component = null,
                                               $message = null
    ) {
        // Parse label if set ...
        if ($label !== null) {
            // Check for collection or single item ...
            if (is_array($label)) {
                foreach ($label as $singleLabel) {
                    $this->addLabel($singleLabel);
                }
            } else {
                $this->addLabel($label);
            }
        }

        // Parse component if set ...
        if (null !== $component) {
            // Check for collection or single item ...
            if (is_array($component)) {
                foreach ($component as $singleComponent) {
                    $this->addComponent($singleComponent);
                }
            } else {
                $this->addComponent($component);
            }
        }

        #if ($message instanceof Doozr_Form_Service_Component_Message) {
        // Parse message if set ...
        if (null !== $message) {
            // Check for collection or single item ...
            if (is_array($message)) {
                foreach ($message as $singleMessage) {
                    $this->addMessage($singleMessage);
                }
            } else {
                $this->addMessage($message);
            }
        }

        parent::__construct($renderer, $validator);

        // automagic management
        $this->wire();
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Control layer
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Adds a label to the group.
     *
     * @param Doozr_Form_Service_Component_Interface_Html $label The label instance to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int The index of the added label
     */
    public function addLabel(Doozr_Form_Service_Component_Interface_Html $label)
    {
        $index               = $this->addChild($label);
        $this->index[$index] = self::LABEL;

        (!isset($this->indexReverse[self::LABEL])) ? $this->indexReverse[self::LABEL] = [] : null;
        $this->indexReverse[self::LABEL][]                                            = $index;

        return $index;
    }

    /**
     * Adds an element to the group.
     *
     * @param Doozr_Form_Service_Component_Interface_Html $element Instance of element to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int The index of the added component
     */
    public function addComponent(Doozr_Form_Service_Component_Interface_Html $element)
    {
        $index               = $this->addChild($element);
        $this->index[$index] = self::COMPONENT;

        (!isset($this->indexReverse[self::COMPONENT])) ? $this->indexReverse[self::COMPONENT] = [] : null;
        $this->indexReverse[self::COMPONENT][]                                                = $index;

        return $index;
    }

    /**
     * Adds a message to the group.
     *
     * @param Doozr_Form_Service_Component_Message $message Instance of message to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int The index of the added message
     */
    public function addMessage(Doozr_Form_Service_Component_Message $message)
    {
        $index               = $this->addChild($message);
        $this->index[$index] = self::MESSAGE;

        (!isset($this->indexReverse[self::MESSAGE])) ? $this->indexReverse[self::MESSAGE] = [] : null;
        $this->indexReverse[self::MESSAGE][]                                              = $index;

        return $index;
    }

    /**
     * Sets the label of this group.
     *
     * @param Doozr_Form_Service_Component_Label $label
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int The index of the added label
     */
    public function setLabel(Doozr_Form_Service_Component_Label $label)
    {
        return $this->addLabel($label);
    }

    /**
     * Returns the label of this group.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Form_Service_Component_Label|null The Instance of label if set, otherwise NULL
     */
    public function getLabels()
    {
        $result = [];

        $labels = (isset($this->indexReverse[self::LABEL])) ? $this->indexReverse[self::LABEL] : [];

        foreach ($labels as $index) {
            $result[] = $this->getChild($index);
        }

        return $result;
    }

    /**
     * Sets the element of this group.
     *
     * @param Doozr_Form_Service_Component_Interface_Html $component The component to set
     *
     * @author   Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int The index of the added component
     */
    public function setComponent(Doozr_Form_Service_Component_Interface_Html $component)
    {
        return $this->addComponent($component);
    }

    /**
     * Returns the Component of this group.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Form_Service_Component_Input[]|null The Instance of element if set, otherwise NULL
     */
    public function getComponents()
    {
        $components = [];

        foreach ($this->indexReverse[self::COMPONENT] as $index) {
            $components[] = $this->getChild($index);
        }

        return $components;
    }

    /**
     * Sets the message of this group.
     *
     * @param Doozr_Form_Service_Component_Message $message
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setMessage(Doozr_Form_Service_Component_Message $message)
    {
        $this->addChild($message, 'message');
    }

    /**
     * Returns the message of this group.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Form_Service_Component_Message|null The Instance of Message if set, otherwise NULL
     */
    public function getMessage()
    {
        $result = [];

        $messages = (isset($this->indexReverse[self::MESSAGE])) ? $this->indexReverse[self::MESSAGE] : [];

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
     *
     * @throws Doozr_Form_Service_Exception
     */
    public function setOrder(array $order)
    {
        // Check requirements
        if (
            in_array(self::LABEL, $order) === false ||
            in_array(self::COMPONENT, $order) === false ||
            in_array(self::MESSAGE, $order) === false
        ) {
            throw new Doozr_Form_Service_Exception(
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
     *
     * @return $this Instance for chaining
     *
     * @throws Doozr_Form_Service_Exception
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
     *
     * @return array The order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Renders a component a returns it HTML code.
     *
     * @param bool $force TRUE to re-render a already rendered component,
     *                    otherwise FALSE to use cached result if exist
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string|null A string containing the resulting HTML code,
     *                     NULL on error
     */
    public function render($force = false)
    {
        // Do custom sort and stuff like this and proxy forward the call to render to renderer->render(...)
        $this->setChildren(
            $this->sort($this->order)
        );

        // Return the rendered result
        return parent::render($force);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Tools & Helper
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Sorts an array by passed order.
     *
     * @param array $order The order of the components
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The ordered result
     */
    protected function sort($order)
    {
        $ordered = [];

        $matrix = [
            'label'     => 'getLabels',
            'component' => 'getComponents',
            'message'   => 'getMessage',
        ];

        foreach ($order as $identifier) {
            $components = $this->{$matrix[$identifier]}();
            foreach ($components as $component) {
                $index                              = count($ordered);
                $this->indexReverse[$identifier][0] = $index;
                $this->index[$index]                = $identifier;
                $ordered[]                          = $component;
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
     */
    protected function wire()
    {
        $labels     = (isset($this->indexReverse[self::LABEL])) ? $this->indexReverse[self::LABEL] : null;
        $components = (isset($this->indexReverse[self::COMPONENT])) ? $this->indexReverse[self::COMPONENT] : null;

        /* @var Doozr_Form_Service_Component_Formcomponent $child */
        for ($i = 0; $i < count($labels); ++$i) {
            if (isset($components[$i])) {
                $label = $this->getChild($labels[$i]);
                $label->setAttribute('for', $this->getChild($components[$i])->getId());
                $this->addChild($label, $labels[$i]);
            }
        }
    }
}
