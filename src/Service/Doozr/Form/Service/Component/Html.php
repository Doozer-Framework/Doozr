<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service.
 *
 * Html.php - Generic renderable HTML component like <p>...</p> or
 * <span>...</span> ...
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
require_once DOOZR_DOCUMENT_ROOT.'Service/Doozr/Form/Service/Component/Html/Html.php';

/**
 * Doozr - Form - Service.
 *
 * Generic renderable HTML component like <p>...</p> or
 * <span>...</span> ...
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
 * @abstract
 */
abstract class Doozr_Form_Service_Component_Html extends Doozr_Form_Service_Component_Html_Html
    implements
    Iterator
{
    /**
     * Tag-name for HTML output. e.g.
     * "input" or "form". Default empty string "".
     *
     * @var string
     */
    protected $tag = Doozr_Form_Service_Constant::HTML_TAG_NONE;

    /**
     * Child components added to this component.
     *
     * @var array
     */
    protected $children = [];

    /**
     * Pointer which points to the last component in the loop.
     *
     * @var int
     */
    protected $pointer = 0;

    /**
     * Type of the component.
     *
     * @var string
     */
    protected $type;

    /**
     * Attached renderer to render the component.
     *
     * @var Doozr_Form_Service_Renderer_Interface
     */
    protected $renderer;

    /**
     * Instance of Registry.
     *
     * @var Doozr_Registry_Interface
     */
    protected $registry;

    /**
     * Arguments.
     *
     * @var array
     */
    protected $arguments = [];

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param string                                $tag      Tag name of the component
     * @param string                                $template Template used for rendering component
     * @param Doozr_Form_Service_Renderer_Interface $renderer Renderer instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __construct(
        $tag                                            = null,
        $template                                       = null,
        Doozr_Form_Service_Renderer_Interface $renderer = null
    ) {
        if ($tag !== null) {
            $this->tag = $tag;
        }

        if ($template !== null) {
            $this->template = $template;
        }

        if ($renderer !== null) {
            $this->setRenderer($renderer);
        }

        parent::__construct();
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Hook on default renderer for some slighty required modifications on input.
     *
     * @param bool $force TRUE to force rerendering, otherwise FALSE to use cached result
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Form_Service_Renderer_Interface Renderer
     */
    public function render($force = false)
    {
        $template   = $this->getTemplate();
        $tag        = $this->getTag();
        $variables  = [];
        $children     = $this->getChildren();
        $attributes = $this->getAttributes();
        $innerHtml  = $this->getInnerHtml();

        $renderer = $this->getRenderer();

        $result   = $renderer->render(
            $force,
            $template,
            $tag,
            $variables,
            $children,
            $attributes,
            $innerHtml
        );

        return $result;
    }

    /**
     * Sets a renderer instance.
     *
     * @param Doozr_Form_Service_Renderer_Interface $renderer A renderer instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setRenderer(Doozr_Form_Service_Renderer_Interface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Getter for renderer instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Form_Service_Renderer_Interface A renderer instance
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * Magic shortcut to renderer.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The resulting HTML code
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Aplies the passed stylesheet/css string to the component.
     *
     * @param string $style The style to apply
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setStyle($style)
    {
        $this->setAttribute('style', $style);
    }

    /**
     * @return mixed|null
     */
    public function getStyle()
    {
        return $this->getAttribute('style');
    }


    public function setCssClassname($cssClassname)
    {
        $this->setAttribute('class', $cssClassname);
    }

    public function cssClassname($cssClassname)
    {
        $this->setCssClassname($cssClassname);

        return $this;
    }

    public function getCssClassname()
    {
        return $this->getAttribute('class');
    }

    /**
     * Adds a child to the component.
     *
     * @param Doozr_Form_Service_Component_Interface_Html $child A child component to add to component
     * @param string                                      $id    An id to used as index
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int The index of the child component
     */
    public function addChild(Doozr_Form_Service_Component_Interface_Html $child, $id = null)
    {
        $id = ($id === null) ? count($this->children) : $id;

        $this->children[$id] = $child;

        // Notify all attached components -> render again
        $this->notify();

        return $id;
    }

    /**
     * Removes a child from the component.
     *
     * @param int $index The index of the child component to remove from component
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool|null TRUE  if child was removed successfully,
     *                   FALSE if child could not be removed,
     *                   NULL  if child was not found
     */
    public function removeChild($index)
    {
        if (!isset($this->children[$index])) {
            $result = null;
        } else {
            $result = (array_splice($this->children, $index, 1) !== null);
        }

        // Notify all attached components -> render again
        $this->notify();

        return $result;
    }

    /**
     * Setter for children.
     *
     * @param array $children The children to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setChildren(array $children)
    {
        $this->children = $children;
    }

    /**
     * Setter for children.
     *
     * @param array $children The children to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function children(array $children)
    {
        $this->setChildren($children);

        return $this;
    }

    /**
     * Returns all attached children.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array An array containing the attached children
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Returns a child by passed Id.
     *
     * @param string $id The id to return child for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed The child
     */
    public function getChild($id)
    {
        return isset($this->children[$id]) ? $this->children[$id] : null;
    }

    /**
     * Returns the parent status of a component.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if the component has children, otherwise FALSE
     */
    public function hasChildren()
    {
        return count($this->getChildren()) > 0;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Getter & Setter
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for Id.
     *
     * @param string $id The id to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setId($id)
    {
        $this->setAttribute('id', $id);
    }

    /**
     * Getter for Id.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The attribute id
     */
    public function getId()
    {
        return $this->getAttribute('id');
    }

    /**
     * Setter for inner-HTML of the component.
     *
     * @param string $html The HTML to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setInnerHtml($html)
    {
        $this->innerHtml = $html;
    }

    /**
     * Getter for inner-HTML.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string|null The inner-HTML if set, otherwise NULL
     */
    public function getInnerHtml()
    {
        return $this->innerHtml;
    }

    /**
     * Setter for tag.
     *
     * @param string $tag The tag of this component
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * Getter for tag.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string|null The tag if set, otherwise NULL
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Setter for arguments.
     *
     * @param array $arguments The arguments
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * Getter for arguments.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array $arguments The arguments
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
     *
     * @return mixed The value from registry if key passed, otherwise the whole registry
     */
    public function getRegistry($key = null, $default = null)
    {
        $result = $this->registry;

        if ($key !== null) {
            $result = (isset($result[$key])) ? $result[$key] : $default;
        }

        return $result;
    }

    /*-----------------------------------------------------------------------------------------------------------------*
     | Iterator Pattern Implementation
     *----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the current element where
     * pointer points to.
     *
     * @return mixed|null
     */
    public function current()
    {
        return $this->children[$this->pointer];
    }

    /**
     * Steps to next element.
     */
    public function next()
    {
        ++$this->pointer;
    }

    /**
     * Returns the current positions pointer.
     *
     * @return int The current position
     */
    public function key()
    {
        return $this->pointer;
    }

    /**
     * Returns the validity of the current pointer
     * position as boolean (TRUE|FALSE).
     *
     * @return bool TRUE of pointer is valid, otherwise FALSE
     */
    public function valid()
    {
        return $this->pointer < count($this->children);
    }

    /**
     * Rewinds the pointer to position 0 (1st).
     */
    public function rewind()
    {
        $this->pointer = 0;
    }
}
