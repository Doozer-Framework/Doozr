<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Form - Service
 *
 * Class DoozR_Form_Service_Element_Html_Base is a basic HTML-Element
 * which provides some simple rendering and templating capabilities.
 * It's a concrete implementation which extends the HTML-skeleton abstract.
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

require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Form/Service/Element/Html/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Form/Service/Element/Html/Interface.php';

/**
 * DoozR - Form - Service
 *
 * Class DoozR_Form_Service_Element_Html_Base is a basic HTML-Element
 * which provides some simple rendering and templating capabilities.
 * It's a concrete implementation which extends the HTML-skeleton abstract.
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
class DoozR_Form_Service_Element_Html_Base extends DoozR_Form_Service_Element_Html_Abstract
    implements
    DoozR_Form_Service_Element_Html_Interface,
    ArrayAccess,
    SplSubject
{
    /**
     * This index is maintained when setting or removing
     * attributes. Values will be added or removed when
     * using setAttribute() or getAttribute()
     *
     * The index is structured like this:
     *
     * $index[0] => 'id'
     * ...
     * $index[3] => 'onclick'
     *
     * So we can use an integer based pointer for
     * Iterator-Loops and lookup in this index for
     * the relation.
     *
     * @var array
     * @access protected
     */
    protected $index = array();

    /**
     * This is the pointer which points to the last
     * element in the loop.
     *
     * @var int
     * @access protected
     */
    protected $pointer = 0;

    /**
     * The observers references
     *
     * @var array
     * @access protected
     */
    protected $observers = array();

    /**
     * The template is required for output. Each HTML-Element inherits
     * this base template and so every element based on this base class
     * is renderable. This template produces at least a correct HTML tag
     * which must not be valid in an other context!
     *
     * @var string
     * @access protected
     */
    protected $template = '<{{TAG}}{{ATTRIBUTES}}></{{TAG}}>';

    /**
     * This contains the rendered HTML when rendered. Its kept till render
     * is forced to render again = override cache!
     *
     * @var string
     * @access protected
     */
    protected $html;


    /*-----------------------------------------------------------------------------------------------------------------*
    | General Functionality
    *-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Default renderer for all basic HTML-elements. This renderer is capable
     * of parsing HTML-elements "properties" and makes use of the $template
     * variable and the mini-templating functionality of this class (_tpl()).
     *
     * @param boolean $forceRender TRUE to override primitive caching (FALSE = default)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed|string
     * @see _tpl(), $template
     */
    public function render($forceRender = false)
    {
        $attributes = '';

        if ($this->html === null || $forceRender === true) {
            foreach ($this->attributes as $attribute => $value) {
                // check value-less attributes to be embedded properly
                if ($value === null) {
                    $attributes .= ' '.$attribute;
                } else {
                    $attributes .= ' '.$attribute.'="'.$value.'"';
                }
            }

            $templateVariables = array(
                'attributes' => $attributes,
                'tag'        => $this->tag
            );

            $html = $this->_tpl($this->template, $templateVariables);
            $this->html = $html.DoozR_Form_Service_Constant::NEW_LINE;
        }

        return $this->html;
    }

    /**
     * Nice shortcut to render with output = true! This method provides
     * us the functionality to be able to just "echo $instance" to get
     * an element rendered.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The result of render()
     * @access public
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Setter for $html which is originally filled by render(); -> override
     *
     * @param string $html The HTML to set override
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setHtml($html = '')
    {
        $this->html = $html;
    }

    /**
     * Getter for $html
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null HTML as string if set, otherwise NULL
     * @access public
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * Setter for attribute[] which also maintains the index.
     * This setter also take care for handling references in
     * index when storing new attributes.
     *
     * @param      $key   The attributes name
     * @param null $value The attributes value
     *
     * @return void
     */
    public function setAttribute($key, $value = null)
    {
        if (!isset($this->attributes[$key])) {
            $this->index[] = $key;
        }

        parent::setAttribute($key, $value);

        // notify all elements which observing this element so the observers stay informed
        // about changes in this class an can react => e.g. re-render HTML and so on.
        $this->notify();
    }

    /**
     * Removes an attribute.
     *
     * @param string $key The name of the attribute
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function removeAttribute($key)
    {
        if (isset($this->attributes[$key])) {
            unset($this->attributes[$key]);
        }
    }

    /*-----------------------------------------------------------------------------------------------------------------*
    | Micro Templating
    *-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Micro templating engine used for processing templates
     * of tags for example.
     *
     * @param string $template          The template to use
     * @param array  $templateVariables The variables used for replace
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The result
     * @access protected
     */
    protected function _tpl($template, array $templateVariables)
    {
        // micro templating engine
        foreach ($templateVariables as $templateVariable => $value) {
            $template = str_replace('{{'.strtoupper($templateVariable).'}}', $value, $template);
        }

        return $template;
    }

    /*-----------------------------------------------------------------------------------------------------------------*
    | Magic Attribute Access
    *-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Magic implementation to prevent us spamming the class
     * body with thousands of getters and setters for all those
     * special attributes that exist. Like id, name, style, on...
     * and so on. This basic implementation transforms calls like
     *
     * @example
     *  - getName() <=> getAttribute('name');
     *  - setName('foo') <=> setAttribute('name', 'foo');
     *  - ...
     *
     * @param $method    The method called (e.g. setId())
     * @param $arguments The arguments passed while calling $method
     *
     * @return null|void
     * @throws DoozR_Form_Service_Exception
     */
    public function __call($method, $arguments)
    {
        // assume we can't call anything!
        $result = null;

        // try to split the method
        $methodSplitted = str_split_camelcase($method);

        // check requirements
        if (
            ($methodSplitted[0] !== 'set' && $methodSplitted[0] !== 'get') ||
            !isset($methodSplitted[1])
        ) {
            trigger_error(
                'Call to undefined function: '.$method.'. Arguments: '.var_export($arguments, true)."\n"
                #'Callstack: '.var_export(, true)
            );

        }

        // extract the property from call
        $property = strtolower($methodSplitted[1]);

        // dispatch to correct method
        if ($methodSplitted[0] === 'get') {
            return $this->getAttribute($property);

        } else {
            if (count($arguments) === 0) {
                $arguments[0] = null;
            }

            $this->setAttribute($property, $this->value($arguments[0]));
        }

        // for chaining calls
        return $this;
    }

    /**
     * Helper for converting native PHP values in HTML usable ones.
     *
     * @param mixed $input The value to convert
     *
     * @return string
     */
    protected function value($input)
    {
        $output = $input;

        if (is_string($input) === false) {
            switch (gettype($input)) {
                case 'boolean':
                    $output = ($input === true) ? 'true' : 'false';
                    break;
                case 'integer':
                case 'double':
                    $output = (string)$input;
                    break;
                case 'object':
                case 'array':
                case 'resource':
                case 'unknown type':
                    $output = serialize($input);
                    break;
                case 'NULL':
                default:
                    // intentionally omitted
                    break;
            }
        }

        return $output;
    }

    /*-----------------------------------------------------------------------------------------------------------------*
     | Observer Pattern Implementation
     *----------------------------------------------------------------------------------------------------------------*/

    /**
     * Attach a observer which is notified about changes
     *
     * @param SplObserver $observer The observer to attach
     *
     * @return void
     * @access public
     */
    public function attach(SplObserver $observer)
    {
        $this->observers[] = $observer;
    }

    /**
     * Detaches an observer from this instance
     *
     * @param SplObserver $observer
     *
     * @return void
     * @access public
     */
    public function detach(SplObserver $observer)
    {
        if (($idx = array_search($observer, $this->observers, true)) !== false) {
            unset($this->observers[$idx]);
        }
    }

    /**
     * Notifies the attached observers about changes by
     * calling the observers update() method by passing
     * the current instance to it.
     *
     * @return void
     * @access public
     */
    public function notify()
    {
        foreach($this->observers as $observer){
            $observer->update($this);
        }
    }

    /*-----------------------------------------------------------------------------------------------------------------*
     | ArrayAccess Pattern Implementation
     *----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the information about the existence
     * of a passed offset as boolean (TRUE|FALSE)
     *
     * @param mixed $offset The offset to check
     *
     * @return bool
     * @access public
     */
    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * Returns the value for a passed offset
     *
     * @param mixed $offset The offset to return value for
     *
     * @return mixed|null The value for the attribute if exist, otherwise NULL
     * @access public
     */
    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
    }

    /**
     * Sets a new offset and its value
     *
     * @param mixed $offset The offset to set
     * @param mixed $value  The value to set
     *
     * @return void
     * @access public
     */
    public function offsetSet($offset, $value)
    {
        // IMPORTANT: Don't set this value directly ->
        // or the index won't be extended!!!
        return $this->setAttribute($offset, $value);
    }

    /**
     * Removes an offset
     *
     * @param mixed $offset The offset to remove
     *
     * @return void
     * @access public
     */
    public function offsetUnset($offset)
    {
        $key = array_search($offset, $this->index);
        $this->index[$key] = null;
        unset($this->attributes[$offset]);
    }
}
