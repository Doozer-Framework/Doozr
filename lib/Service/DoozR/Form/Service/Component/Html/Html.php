<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Form - Service
 *
 * Class DoozR_Form_Service_Component_Html_Html is a basic HTML-Component
 * which provides some simple rendering and templating capabilities.
 * It's a concrete implementation which extends the HTML-skeleton abstract.
 *
 * PHP versions 5.4
 *
 * LICENSE:
 * DoozR - The lightweight PHP-Framework for high-performance websites
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

require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Form/Service/Component/Html/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Form/Service/Component/Interface/Html.php';

/**
 * DoozR - Form - Service
 *
 * Class DoozR_Form_Service_Component_Html_Html is a basic HTML-Component
 * which provides some simple rendering and templating capabilities.
 * It's a concrete implementation which extends the HTML-skeleton abstract.
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @abstract
 */
abstract class DoozR_Form_Service_Component_Html_Html extends DoozR_Form_Service_Component_Html_Abstract implements
    DoozR_Form_Service_Component_Interface_Html,
    SplSubject,
    SplObserver,
    ArrayAccess
{
    /**
     * The observers references.
     * Is array on default so that a access as array
     * won't fail till construction
     *
     * @var SplObjectStorage
     * @access protected
     */
    protected $observers = array();

    /**
     * The template is required for output. Each HTML-Component inherits
     * this base template and so every component based on this base class
     * is renderable. This template produces at least a correct HTML tag
     * which must not be valid in an other context!
     *
     * @var string
     * @access protected
     */
    protected $template = DoozR_Form_Service_Constant::TEMPLATE_DEFAULT_CLOSING;

    /**
     * The inner HTML string
     *
     * @var string
     * @access protected
     */
    protected $innerHtml = '';

    /**
     * This contains the rendered HTML when rendered. Its kept till render
     * is forced to render again = override cache!
     *
     * @var string
     * @access protected
     */
    protected $html;

    /**
     * Constructor.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Form_Service_Component_Html_Html
     * @access public
     */
    public function __construct()
    {
        $this->observers = new SplObjectStorage();
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Default renderer for all basic HTML-components. This renderer is capable
     * of parsing HTML-components "properties" and makes use of the $template
     * variable and the mini-templating functionality of this class (tpl()).
     *
     * @param bool $force TRUE to override primitive caching (FALSE = default)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed|string
     * @access public
     * @see    tpl(), $template
     */
    public function render($force = false)
    {
        // default no attributes
        $attributes = '';

        // Render only if not already rendered OR if forced
        if ($this->html === null || $force === true) {

            foreach ($this->attributes as $attribute => $value) {

                // check value-less attributes to be embedded properly
                if ($value === null) {
                    $attributes .= ' ' . $attribute;
                } else {
                    $value = (is_array($value)) ? $value[0] : $value;
                    $attributes .= ' ' . $attribute . '="' . $value . '"';
                }
            }

            // Set template variables for our default template
            $templateVariables = array(
                'attributes' => $attributes,
                'tag'        => $this->tag
            );

            $html = $this->tpl($this->template, $templateVariables);

            if ($this->innerHtml !== null) {

                $variables = array(
                    'inner-html' => $this->innerHtml
                );

                $html = $this->tpl($html, $variables);
            }

            $this->html = $html . PHP_EOL;
        }

        return $this->html;
    }

    /**
     * Nice shortcut to render with output = true! This method provides
     * us the functionality to be able to just "echo $instance" to get
     * an component rendered.
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
     * Setter for template.
     *
     * @param string $template The template to use for rendering HTML
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Getter for template.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The template of the component
     * @access public
     */
    public function getTemplate()
    {
        return $this->template;
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

        // Notify all attached components -> render again
        #$this->notify();
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
    protected function tpl($template, array $templateVariables)
    {
        // micro templating engine
        foreach ($templateVariables as $templateVariable => $value) {
            $template = str_replace('{{' . strtoupper($templateVariable) . '}}', $value, $template);
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
                'Call to undefined function: ' . $method . '. Arguments: ' . var_export($arguments, true) . PHP_EOL
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
        $this->observers->attach($observer);
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
        $this->observers->detach($observer);
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
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    /*-----------------------------------------------------------------------------------------------------------------*
     | SplSubject Fullfilment
     *----------------------------------------------------------------------------------------------------------------*/

    public function update(SplSubject $subject)
    {
        pred($subject);
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
        $key               = array_search($offset, $this->index);
        $this->index[$key] = null;
        unset($this->attributes[$offset]);
    }
}
