<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service
 *
 * Html.php - Contract for all HTML/DOM components. This contract garantues
 * that a class using this interface is renderable through render() can take
 * child components (also if it doesn't make sense for some components!) and
 * that a call on render() will return the HTML for the whole component.
 *
 * PHP versions 5.5
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
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
 * @category   Doozr
 * @package    Doozr_Service
 * @subpackage Doozr_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

/**
 * Doozr - Form - Service
 *
 * Contract for all HTML/DOM components. This contract garantues
 * that a class using this interface is renderable through render() can take
 * child components (also if it doesn't make sense for some components!) and
 * that a call on render() will return the HTML for the whole component.
 *
 * @category   Doozr
 * @package    Doozr_Service
 * @subpackage Doozr_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
interface Doozr_Form_Service_Component_Interface_Html
{
    /**
     * Adds a child to the component.
     *
     * @param Doozr_Form_Service_Component_Interface_Html $child A child component to add to component
     * @param string                                      $id    An id to used as index
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The index of
     * @access public
     */
    public function addChild(Doozr_Form_Service_Component_Interface_Html $child, $id = null);

    /**
     * Removes a child from the component.
     *
     * @param int $index The index of the child component to remove from component
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool|null TRUE  if child was removed successfully,
     *                      FALSE if child could not be removed,
     *                      NULL  if child wasn't found
     * @access public
     */
    public function removeChild($index);

    /**
     * Returns all attached childs
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array An array containing the attached childs
     * @access public
     */
    public function getChilds();

    /**
     * Returns a child by passed Id.
     *
     * @param string $id The id to return child for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The child
     * @access public
     */
    public function getChild($id);

    /**
     * Returns the parent status of a component.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if the component has childs, otherwise FALSE
     * @access public
     */
    public function hasChilds();

    /**
     * Renders a component a returns it HTML code.
     *
     * @param bool $force TRUE to re-render a already rendered component,
     *                       otherwise FALSE to use cached result if exist
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null A string containing the resulting HTML code,
     *                     NULL on error
     * @access public
     */
    public function render($force = false);

    /**
     * Sets a renderer instance.
     *
     * @param Doozr_Form_Service_Renderer_Interface $renderer A renderer instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setRenderer(Doozr_Form_Service_Renderer_Interface $renderer);

    /**
     * Getter for renderer instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function getRenderer();

    /**
     * Setter for style
     *
     * @param string $style The style to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setStyle($style);

    /**
     * Getter for style.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The style if set, otherwise NULL
     * @access public
     */
    public function getStyle();

    /**
     * Setter for Id.
     *
     * @param string $id The id to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setId($id);

    /**
     * Getter for Id.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The id if set, otherwise NULL
     * @access public
     */
    public function getId();

    /**
     * Setter for inner-HTML of the componennt.
     *
     * @param string $html The HTML to set for the component
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setInnerHtml($html);

    /**
     * Getter for inner-HTML.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The inner-HTML if set, otherwise NULL
     * @access public
     */
    public function getInnerHtml();

    /**
     * Setter for tag.
     *
     * @param string $tag The tag of this component
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setTag($tag);

    /**
     * Getter for tag.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The tag if set, otherwise NULL
     * @access public
     */
    public function getTag();

    /**
     * Setter for template.
     *
     * @param string $template The template to use for rendering HTML
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setTemplate($template);

    /**
     * Getter for template.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The template of the component
     * @access public
     */
    public function getTemplate();

    /**
     * Setter for attribute
     *
     * @param      $key   The name of the attribute
     * @param null $value The value of the attribute
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setAttribute($key, $value = null);

    /**
     * Getter for attributes[]
     *
     * @param $key The name of the attribute
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed|boolean The attributes value if set, FALSE if not
     * @access public
     */
    public function getAttribute($key);

    /**
     * Setter for an array of attributes[]
     *
     * @param array $attributes The attributes to set as an array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setAttributes(array $attributes);

    /**
     * Getter for attributes[]
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array An array containing the attributes
     * @access public
     */
    public function getAttributes();

    /**
     * Setter for arguments.
     *
     * @param array|Doozr_Request_Arguments $arguments The arguments
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setArguments($arguments);

    /**
     * Getter for arguments.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array|Doozr_Request_Arguments $arguments The arguments
     * @access public
     */
    public function getArguments();

    /**
     * Setter for registry.
     *
     * @param array $registry The registry
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setRegistry($registry);

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
    public function getRegistry($key = null, $default = null);

    /**
     * Intention:
     * This magic should be used to return the result
     * of a render-call.
     */
    public function __toString();
}
