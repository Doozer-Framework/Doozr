<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Service - Form
 *
 * Abstract.php - Abstract class as template or base for input fields of the
 * Form module. It can act as template for input-field types like text,radio,
 * select, checkboxes and so on.
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
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Form/Service/Exception.php';

/**
 * DoozR - Service - Form
 *
 * Abstract class as template or base for input fields of the Form module.
 * It can act as template for input-field types like text,radio, select,
 * checkboxes and so on.
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 * @abstract
 */
abstract class DoozR_Form_Service_Element_Abstract
{
    /**
     * The reference to the form containing this fields
     * instance - back/circular reference for easier
     * chaining access -> be careful
     *
     * @var DoozR_Form_Service
     * @access protected
     */
    protected $form;

    /**
     * The I18n module instance
     *
     * @var DoozR_I18n_Service
     * @access protected
     */
    protected $i18n;

    /**
     * The name of the input-field no matter
     * which type
     *
     * @var string
     * @access protected
     */
    protected $name = '';

    /**
     * The type (default = text)
     *
     * @var string
     * @access protected
     */
    protected $type = 'text';

    /**
     * Flag for hidden input fields
     *
     * @var boolean
     * @access protected
     */
    protected $hidden = false;

    /**
     * The attributes for current element
     *
     * @var array
     * @access protected
     */
    protected $attributes = array();

    /**
     * The HTML-code generated for current element
     *
     * @var string
     * @access protected
     */
    protected $html = '';

    /**
     * The impact (e.g. PHPIDS[default]) of current
     * field
     *
     * @var integer
     * @access protected
     */
    protected $impact;

    /**
     * The status of field required
     *
     * @var boolean
     * @access protected
     */
    protected $required = false;

    /**
     * The validations of this field
     *
     * @var array
     * @access protected
     */
    protected $validation = array();

    /**
     * The status of form was allready submitted
     *
     * @var boolean
     * @access protected
     */
    protected $submitted = false;

    /**
     * The status of adding surrounding div element
     *
     * @var boolean
     * @access protected
     */
    protected $useContainer = true;

    /**
     * The label for the current field
     *
     * @var string
     * @access protected
     */
    protected $label;

    /**
     * Optional HTML-code which can be added before or
     * after an element
     *
     * @var string
     * @access protected
     */
    protected $additionalHtml;

    /**
     * The request method used for last request
     *
     * @var string
     * @access protected
     */
    protected $requestMethod;

    /**
     * The valid status of this element
     *
     * @var mixed
     * @access protected
     */
    protected $valid;

    /**
     * All form errors occured before
     * instanciation
     *
     * @var mixed
     * @access protected
     */
    protected $formError;

    /**
     * The error of the element
     *
     * @var string
     * @access protected
     */
    protected $error;

    /**
     * All all form element impacts
     *
     * @var mixed
     * @access protected
     */
    protected $formImpact;

    /**
     * Tabulator replacement
     *
     * @var string
     * @access public
     */
    const TABULATOR = "\t";

    /**
     * New-line replacement
     *
     * @var string
     * @access public
     */
    const NEW_LINE = "\n";

    /**
     * Name of the class for surrounding div-element
     *
     * @var string
     * @access public
     */
    const DIV_CONTAINER_CLASSNAME = 'DoozR_Form_Service_Fieldset_Container';

    /**
     * Name of the class for invalid input-field (either IMPACT or NO VALID INPUT)
     *
     * @var string
     * @access public
     */
    const CLASS_INVALID = 'invalid';

    /**
     * Name of the class for valid input-field (neither IMPACT nor NO VALID INPUT)
     *
     * @var string
     * @access public
     */
    const CLASS_VALID = 'valid';

    /**
     * Name of the class for dirty (e.g. injected code) fields
     *
     * @var string
     * @access public
     */
    const CLASS_DIRTY = 'dirty';

    const DEFAULT_POSITION_LABEL   = 0;
    const DEFAULT_POSITION_ELEMENT = 1;
    const DEFAULT_POSITION_ERROR   = 2;

    /**
     * Positioning array of this element
     * Default LABEL ELEMENT ERROR
     *
     * @var array
     * @access protected
     */
    protected $position = array(
        'label'   => self::DEFAULT_POSITION_LABEL,
        'element' => self::DEFAULT_POSITION_ELEMENT,
        'error'   => self::DEFAULT_POSITION_ERROR
    );


    /**
     * Constructor
     *
     * @param array $config The configuration for creating this input-element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object Instance of this class
     * @access public
     */
    public function __construct(array $config)
    {
        // the form reference
        $this->form = $config['form'];

        // the i18n module/class having a _() method for translation
        $this->i18n = $config['i18n'];

        // set type of this class
        $this->type = $config['type'];

        // store parents (form) name
        $this->parent = $config['parent'];

        // set status of submission
        $this->submitted = $config['submitted'];

        // set jump status of request
        $this->jumped = $config['jumped'];

        // store error status of all form fields from last request
        $this->formError = $config['error'];

        // store form field impacts
        $this->formImpact = $config['impact'];

        // store request method of current request
        $this->requestMethod = $config['requestMethod'];

        // sets the div-container status
        $this->useContainer = $config['container'];

        // store classname for div-container
        $this->containerClass = $config['containerclass'];
    }

    /*******************************************************************************************************************
     * // BEGIN - PUBLIC PROPERTY SETTER AND GETTER
     ******************************************************************************************************************/

    /**
     * Sets the title of the form-element
     *
     * This method is intend to set the title of the form-element.
     *
     * @param string $title The title of form-element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setTitle($title)
    {
        return ($this->attributes['title'] = $title);
    }

    /**
     * Sets the title of the form-element
     *
     * This method is intend to set the title of the form-element.
     *
     * @param string $title The title of form-element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function title($title)
    {
        return $this->setTitle($title);
    }

    /**
     * Returns the title of the form-field
     *
     * This method is intend to return the title of the form-field.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING title if set, otherwise NULL
     * @access public
     */
    public function getTitle()
    {
        return (isset($this->attributes['title'])) ? $this->attributes['title'] : null;
    }

    /**
     * Sets the tabindex of the form-element
     *
     * This method is intend to set the tabindex of the form-element.
     *
     * @param integer $tabindex The tabindex of form-element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setTabindex($tabindex)
    {
        return ($this->attributes['tabindex'] = $tabindex);
    }

    /**
     * Sets the tabindex of the form-element
     *
     * This method is intend to set the tabindex of the form-element.
     *
     * @param integer $tabindex The tabindex of form-element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Abstract Current active instance
     * @access public
     */
    public function tabindex($tabindex)
    {
        $this->setTabindex($tabindex);

        return $this;
    }

    /**
     * Returns the tabindex of the form-field
     *
     * This method is intend to return the tabindex of the form-field.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed INTEGER language if set, otherwise NULL
     * @access public
     */
    public function getTabindex()
    {
        return (isset($this->attributes['tabindex'])) ? $this->attributes['tabindex'] : null;
    }

    /**
     * Sets the language of the form-element
     *
     * This method is intend to specify a language code for the content in an element.
     *
     * @param string $lang The language code for the content in an element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setLang($lang)
    {
        return ($this->attributes['lang'] = $lang);
    }

    /**
     * Sets the language of the form-element
     *
     * This method is intend to specify a language code for the content in an element.
     *
     * @param string $lang The language code for the content in an element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object DoozR_Form_Service_Abstract Current active instance
     * @access public
     */
    public function lang($lang)
    {
        $this->setLang($lang);

        return $this;
    }

    /**
     * Returns the language of the form-field
     *
     * This method is intend to return the language of the form-field.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING language if set, otherwise NULL
     * @access public
     */
    public function getLang()
    {
        return (isset($this->attributes['lang'])) ? $this->attributes['lang'] : null;
    }

    /**
     * Sets the accesskey of the form-element
     *
     * This method is intend to return the accesskey of the form-element.
     *
     * @param string $accesskey The accesskey to use for current form-element.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setAccesskey($accesskey)
    {
        return ($this->attributes['accesskey'] = $accesskey);
    }

    /**
     * Sets the accesskey of the form-element
     *
     * This method is intend to return the accesskey of the form-element.
     *
     * @param string $accesskey The accesskey to use for current form-element.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function accesskey($accesskey)
    {
        return $this->setAccesskey($accesskey);
    }

    /**
     * Returns the accesskey of the form-field
     *
     * This method is intend to return the accesskey of the form-field.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING accesskey if set, otherwise NULL
     * @access public
     */
    public function getAccesskey()
    {
        return (isset($this->attributes['accesskey'])) ? $this->attributes['accesskey'] : null;
    }

    /**
     * Sets the direction of the text (LTF || RTL)
     *
     * This method is intend to set the direction of the text (LTF || RTL).
     *
     * @param string $dir The direction of the text (either LTR or RTL)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setDir($dir)
    {
        return ($this->attributes['dir'] = $dir);
    }

    /**
     * Sets the direction of the text (LTF || RTL)
     *
     * This method is intend to set the direction of the text (LTF || RTL).
     *
     * @param string $dir The direction of the text (either LTR or RTL)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object DoozR_Form_Service_Abstract Current active instance
     * @access public
     */
    public function dir($dir)
    {
        $this->setDir($dir);

        return $this;
    }

    /**
     * Returns the direction of the text
     *
     * This method is intend to return the direction of the text.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING direction (either TRL || RTL) if set, otherwise NULL
     * @access public
     */
    public function getDir()
    {
        return (isset($this->attributes['dir'])) ? $this->attributes['dir'] : null;
    }

    /**
     * Sets the style (class) of the form element
     *
     * This method is intend to set the style (class) of the form element.
     *
     * @param string $class The class to set for this element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setCss($class)
    {
        return $this->setAttribute('class', $class);
    }

    /**
     * Adds a CSS classname to this element
     *
     * @param string $class The class to add to this element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return current active instance
     * @access public
     */
    public function addCssClass($class)
    {
        $classes = $this->getCss();
        $classes = ($classes) ? array($classes, $class) : array($class);
        $classes = (count($classes) > 1) ? implode(' ', $classes) : $classes[0];
        $this->setCss($classes);

        return $this;
    }

    /**
     * Removes a CSS classname from this element
     *
     * @param string $class The class to remove from this element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return current active instance
     * @access public
     */
    public function removeCssClass($class)
    {
        $classes = $this->getCss();
        $classes = str_replace(' '.$class,  '', $classes);
        $classes = str_replace($class,  '', $classes);
        $this->setCss($classes);

        return $this;
    }

    /**
     * Sets the style (class) of the form element
     *
     * @param string $class The class to set for this element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function css($class)
    {
        $this->setCss($class);
        return $this;
    }

    /**
     * Returns the style (inline css) of the form element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING style if set, otherwise NULL
     * @access public
     */
    public function getCss()
    {
        return $this->getAttribute('class');
    }

    /**
     * Sets the size of a form element
     *
     * @param integer $size The size in chars to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setSize($size)
    {
        return ($this->attributes['size'] = $size);
    }

    /**
     * Sets the size of a form element
     *
     * @param integer $size The size in chars to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object DoozR_Form_Service_Abstract Current active instance
     * @access public
     */
    public function size($size)
    {
        $this->setSize($size);

        return $this;
    }

    /**
     * Returns the size of a form element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed INTEGER displaylength in chars if set, otherwise NULL
     * @access public
     */
    public function getSize()
    {
        return (isset($this->attributes['size'])) ? $this->attributes['size'] : null;
    }

    /**
     * Sets the attribute with given value
     *
     * @param string $attribute The attribute to set
     * @param mixed  $value     The value of the attribute
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed MIXED value if set, otherwise NULL
     * @access public
     */
    public function setAttribute($attribute, $value = null)
    {
        return ($this->attributes[$attribute] = $value);
    }

    /**
     * Sets the attribute with given value
     *
     * @param string $attribute The attribute to set
     * @param mixed  $value     The value of the attribute
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object DoozR_Form_Service_Abstract Current active instance
     * @access public
     */
    public function attribute($attribute, $value = null)
    {
        $this->setAttribute($attribute, $value);

        return $this;
    }

    /**
     * Returns the value of a requested attribute
     *
     * @param string $attribute The attribute to return value from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed MIXED value if set, otherwise NULL
     * @access public
     */
    public function getAttribute($attribute)
    {
        return (isset($this->attributes[$attribute])) ? $this->attributes[$attribute] : null;
    }

    /**
     * Stores additional HTML-Code with given position for rendering
     *
     * @param string $html     The HTML-Code to store (render)
     * @param string $position The position of where to render the content to (before | after)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function setAdditionalHtml($html = null, $position = 'before')
    {
        // check for given content
        if (!is_null($html)) {
            // get currently set html-code
            $additionalHtml = $this->additionalHtml;

            // if null then just create
            if (is_null($additionalHtml)) {
                $this->additionalHtml = array(
                    $position => $html
                );
            } else {
                // add
                if (isset($this->additionalHtml[$position])) {
                    $this->additionalHtml[$position] .= $html;
                } else {
                    $this->additionalHtml[$position] = $html;
                }
            }
        }

        // success
        return true;
    }

    /**
     * Stores additional HTML-Code with given position for rendering
     *
     * @param string $html     The HTML-Code to store (render)
     * @param string $position The position of where to render the content to (before | after)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object DoozR_Form_Service_Abstract Current active instance
     * @access public
     */
    public function additionalHtml($html = null, $position = 'before')
    {
        $this->setAdditionalHtml($html, $position);

        // for chaining
        return $this;
    }

    /**
     * Returns stored additional HTML-Code including position for rendering
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed NULL if not set, otherwise STRING HTML-Code
     * @access public
     */
    public function getAdditionalHtml()
    {
        return $this->additionalHtml;
    }

    /**
     * Sets the name of the input field
     *
     * @param string $name The name of the input-field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setName($name)
    {
        return (($this->name = $name) && $this->setAttribute('name', $name));
    }

    /**
     * Sets the name of the input field
     *
     * @param string $name The name of the input-field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object DoozR_Form_Service_Abstract Current active instance
     * @access public
     */
    public function name($name)
    {
        $this->setName($name);

        return $this;
    }

    /**
     * Returns the name of the input field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING name of the input field if exists, otherwise NULL
     * @access public
     */
    public function getName()
    {
        return $this->getAttribute('name');
    }

    /**
     * Sets the id of the input field
     *
     * @param string $id The id of the input-field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setId($id)
    {
        return $this->setAttribute('id', $id);
    }

    /**
     * Sets the id of the input field
     *
     * @param string $id The id of the input-field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object DoozR_Form_Service_Abstract Current active instance
     * @access public
     */
    public function id($id)
    {
        $this->setId($id);

        return $this;
    }

    /**
     * Returns the id of the input field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING id of the input field if exists, otherwise NULL
     * @access public
     */
    public function getId()
    {
        return $this->getAttribute('id');
    }

    /**
     * Sets the style (css) (inline or class) of the input field
     *
     * @param string  $style  The classname (css) or the inline-style of the input-field
     * @param boolean $inline TRUE to set the given style inline, otherwise FALSE to use style as classname (class="")
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setStyle($style, $inline = false)
    {
        if (!$inline) {
            return $this->setAttribute('class', $style);
        } else {
            return $this->setAttribute('style', $style);
        }
    }

    /**
     * Sets the style (css) (inline or class) of the input field
     *
     * @param string  $style  The classname (css) or the inline-style of the input-field
     * @param boolean $inline TRUE to set the given style inline, otherwise FALSE to use style as classname (class="")
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object DoozR_Form_Service_Abstract Current active instance
     * @access public
     */
    public function style($style, $inline = false)
    {
        $this->setStyle($style, $inline);

    }

    /**
     * Returns the style (css) (inline or class) of the input field
     *
     * @param boolean $inline TRUE to return the current inline-style, otherwise FALSE to return class
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING class of the input field if exists, otherwise NULL
     * @access public
     */
    public function getStyle($inline = false)
    {
        if (!$inline) {
            return $this->getAttribute('class');
        } else {
            return $this->getAttribute('style');
        }
    }

    /**
     * Sets the disabled status of the input field
     *
     * @param boolean $disabled The disabled-status of the input-field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setDisabled($disabled = true)
    {
        if ($disabled) {
            $status = $this->setAttribute('disabled', 'disabled');
        } else {
            $status = ($this->removeAttribute('disabled'));
        }

        // return the status
        return $status;
    }

    /**
     * Returns the disabled status of the input field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if disabled, otherwise FALSE
     * @access public
     */
    public function getDisabled()
    {
        return ($this->getAttribute('disabled')) ? true : false;
    }

    /**
     * Sets the hidden status of the input field
     *
     * @param boolean $hidden The hidden-status of the input-field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setHidden($hidden = true)
    {
        return ($this->hidden = $hidden);
    }

    /**
     * Returns the hidden status of the input field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if disabled, otherwise FALSE
     * @access public
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Sets the given validation of the input field
     *
     * @param string $validation The validation for the input-field
     * @param mixed  $value      The value to set (optional)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setValidate($validation, $value = null)
    {
        if (!isset($this->validation[$validation])) {
            $this->validation[$validation] = array($value);
        } else {
            $this->validation[$validation][] = $value;
        }

        //return isset($this->validation[$validation]);
        return true;
    }

    /**
     * Sets the given validation of the input field
     *
     * @param string $validation The validation for the input-field
     * @param mixed  $value      The value to set (optional)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function addValidate($validation, $value = null)
    {
        return $this->setValidate($validation, $value);
    }

    /**
     * Sets the given validation of the input field
     *
     * @param string $validation The validation for the input-field
     * @param mixed  $value      The value to set (optional)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function validate($validation, $value = null)
    {
        $this->setValidate($validation, $value);

        // for chaining
        return $this;
    }

    /**
     * Returns the stored validations of the input field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The stored validations of this field if set, otherwise empty array
     * @access public
     */
    public function getValidate()
    {
        return $this->validation;
    }

    /**
     * Sets the required status of the input field
     *
     * @param boolean $required The required-status of the input-field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setRequired($required = true)
    {
        $this->setValidate('required');
        return ($this->required = $required);
    }

    /**
     * Sets the required status of the input field
     *
     * @param boolean $required The required-status of the input-field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function required($required = true)
    {
        $this->setRequired($required);
        return $this;
    }

    /**
     * Returns the required status of the input field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if field is required, otherwise FALSE
     * @access public
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Sets the label of the input field
     *
     * @param string $label The label-text to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setLabel($label = '')
    {
        // set label
        $this->label = $label;

        // success
        return true;
    }

    /**
     * Sets the label of the input field
     *
     * @param string $label The label-text to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Element_Abstract The current active instance
     * @access public
     */
    public function label($label = '')
    {
        $this->setLabel($label);

        // for chaining
        return $this;
    }

    /**
     * Returns the label of the input-field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING label of the input field if set, otherwise NULL
     * @access public
     */
    public function getLabel()
    {
        return (isset($this->label)) ? $this->label : null;
    }

    /**
     * Sets the impact (IDS - danger input indicator) of the input field
     *
     * @param integer $impact The impact-value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The impact of the input-field
     * @access public
     */
    public function setImpact($impact = 0)
    {
        return ($this->impact = $impact);
    }

    /**
     * Returns the impact (IDS - danger input indicator) of the input field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The impact of the input-field
     * @access public
     * @throws DoozR_Form_Service_Exception
     */
    public function getImpact()
    {
        // if impact wasn't checked before do it now
        if (is_null($this->impact)) {
            // if name not set we can't set a valid status
            if ($this->name == '') {
                throw new DoozR_Form_Service_Exception(
                    'You need to set a name for an element first to check its impact-status!'
                );
            }

            // check if impact is set for this field
            if (isset($this->formImpact[$this->name])) {
                $this->impact = $this->formImpact[$this->name];
            } else {
                $this->impact = 0;
            }
        }

        // return the impact detected
        return $this->impact;
    }

    /**
     * Returns status of impact
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The impact of the input-field
     * @access public
     */
    public function hasImpact()
    {
        return ($this->getImpact() > 0) ? true : false;
    }

    /**
     * Sets the read-only status of the input field
     *
     * @param boolean $readonly The read-only status of the input field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successful, otherwise FALSE
     * @access public
     */
    public function setReadonly($readonly = true)
    {
        if ($readonly) {
            $this->attributes['readonly'] = 'readonly';
        } else {
            $this->removeAttribute('readonly');
        }

        // success
        return true;
    }

    /**
     * Sets the read-only status of the input field
     *
     * @param boolean $readonly The read-only status of the input field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successful, otherwise FALSE
     * @access public
     */
    public function readonly($readonly = true)
    {
        $this->setReadonly($readonly);
        return $this;
    }

    /**
     * Returns the read-only status of the input field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if field is read-only, otherwise FALSE
     * @access public
     */
    public function getReadonly()
    {
        return (isset($this->attributes['readonly'])) ? $this->attributes['readonly'] : false;
    }

    /**
     * Sets the maximum input length of the input field
     *
     * @param integer $maxlength The maximum input length of the input field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successful, otherwise FALSE
     * @access public
     */
    public function setMaxlength($maxlength)
    {
        // setting maxlength to an exact value has a special meaning!
        // so we can assume that it would be good to automatic validate this setup!
        $status = ($this->addValidate('maxlength', $maxlength)) && ($this->attributes['maxlength'] = $maxlength);
        return $status;
    }

    /**
     * Sets the maximum input length of the input field
     *
     * @param integer $maxlength The maximum input length of the input field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successful, otherwise FALSE
     * @access public
     */
    public function maxlength($maxlength)
    {
        $this->setMaxlength($maxlength);
        return $this;
    }

    /**
     * Returns the maximum input length of the input field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed INTEGER maxlength if set, otherwise NULL
     * @access public
     */
    public function getMaxlength()
    {
        return (isset($this->attributes['maxlength'])) ? $this->attributes['maxlength'] : null;
    }

    /**
     * Sets the value of the input field
     *
     * @param mixed   $value                  The value to set for this input-field
     * @param boolean $overrideSubmittedValue TRUE to override a submitted value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successful, otherwise FALSE
     * @access public
     */
    public function setValue($value, $overrideSubmittedValue = false)
    {
        // check for override mode - submit does not take it's value from submission!
        if ($this->type != 'submit' && !$overrideSubmittedValue) {
            // try to retrieve submitted value
            $submittedValue = $this->getValue();

            // if value use it
            if (!is_null($submittedValue)) {
                $value = $submittedValue;
            }
        }

        // set value and return result
        return ($this->attributes['value'] = $value);
    }

    /**
     * Sets the value of the input field
     *
     * @param mixed   $value                  The value to set for this input-field
     * @param boolean $overrideSubmittedValue TRUE to override a submitted value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Element_Abstract The current active instance
     * @access public
     */
    public function value($value, $overrideSubmittedValue = false)
    {
        $this->setValue($value, $overrideSubmittedValue);

        // for chaining
        return $this;
    }

    /**
     * Returns the value of the input field
     *
     * @param boolean $fromRequest True to retrieve value from request, false to retrieve from previously input/config
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Value of the input-field
     * @access public
     */
    public function getValue($fromRequest = false)
    {
        $attributeValue = $this->getAttribute('value');

        if (!is_null($attributeValue) && $fromRequest == false) {
            return $attributeValue;
        } else {
            if ($fromRequest) {
                return $this->getSubmittedValue();
            } else {
                $value = null;

                // if form was allready submitted retrieve value right now
                if ($this->submitted) {
                    // try to retrieve the submitted value of field
                    $value = $this->getSubmittedValue();

                } elseif ($this->jumped) {
                    // try to retrieve the jump value of field
                    $value = $this->getJumpValue();

                }

                // and store it for further access/operations
                if (!is_null($value)) {
                    $this->attributes['value'] = $value;
                }

                return (isset($this->attributes['value'])) ? $this->attributes['value'] : null;
            }
        }
    }

    /**
     * Returns the type (text, password, file ...) of the current input-field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The type of the input-field
     * @access public
     */
    public function getType()
    {
        return $this->type;
    }

    /*******************************************************************************************************************
     * \\ END - PUBLIC PROPERTY SETTER AND GETTER
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN - HELPER METHODS
     ******************************************************************************************************************/

    /**
     * Removes an attribute from input-field
     *
     * @param string $attribute The attributes name to remove
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute was successful removed, otherwise FALSE
     * @access public
     */
    public function removeAttribute($attribute)
    {
        if (isset($this->attributes[$attribute])) {
            unset($this->attributes[$attribute]);
        }

        return true;
    }

    /**
     * This method is intend to combine all css-styles and store them.
     * The order for css-classes (last item counts) is => field-class - error-class - impact-class.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Combined styles for "style" and "class"
     * @access protected
     */
    protected function combineStyles()
    {
        // the resulting/rendered list of style(s)
        $combined = array(
            'css' => null,
            'style' => null
        );

        // fetch elements class-name
        $css = $this->getCss();

        // if element has a css class add it
        if ($css) {
            $combined['css'] = $css;
        }

        // fetch elements inline style
        $style = $this->getStyle(true);

        // if element has style add it
        if ($style) {
            $combined['style'] = $style;
        }

        // now check if we must add the build in class for in-/valid
        // if isValid() returns NULL then the field is neither "in-" nor "valid"
        if ($this->isValid() === true) {
            // need an exact match on TRUE to be valid
            $combined['css'] .= (isset($combined['css'])) ? ' '.self::CLASS_VALID : self::CLASS_VALID;

        } elseif ($this->isValid() === false) {
            // or an extact match on FALSE to be invalid
            $combined['css'] .= (isset($combined['css'])) ? ' '.self::CLASS_INVALID : self::CLASS_INVALID;

        }

        // check if error (validation failed)  or intrusion (IDS -> impact) detected
        if ($this->hasImpact() === true) {
            // or an extact match on FALSE to be invalid
            $combined['css'] .= (isset($combined['css'])) ? ' '.self::CLASS_DIRTY : self::CLASS_DIRTY;
        }

        // return the combined values
        return $combined;
    }

    /**
     * Returns tabulator control-char
     *
     * @param integer $count Defines how many tabulator control-chars should be returned
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The requested amount of tabulator control-chars
     * @access protected
     */
    protected function t($count = 1)
    {
        return str_repeat(self::TABULATOR, $count);
    }

    /**
     * Returns new-line control-char
     *
     * @param integer $count Defines how many new-line control-chars should be returned
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The requested amount of new-line control-chars
     * @access protected
     */
    protected function nl($count = 1)
    {
        return str_repeat(self::NEW_LINE, $count);
    }

    /**
     * Returns the value for this element for jumped step
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Value for this element if set, otherwise NULL
     * @access public
     */
    public function getJumpValue()
    {
        $data = $this->form->getData();
        $step = $this->form->getStep();
        $value = (isset($data[$step][$this->name])) ? $data[$step][$this->name] : null;
        return $value;
    }

    /**
     * Returns the submitted value for this input-field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING submitted value if form was submitted, otherwise NULL
     * @access public
     */
    public function getSubmittedValue()
    {
        // check if form was submitted
        if ($this->submitted) {
            // get correct request-method
            $requestMethod = $this->requestMethod;

            // check for source of parameter and return
            switch ($requestMethod) {
            case 'get':
            return (isset($_GET->{$this->name})) ? $_GET->{$this->name} : null;
            break;

            case 'post':
            return (isset($_POST->{$this->name})) ? $_POST->{$this->name} : null;
            break;

            default:
            return (isset($_REQUEST->{$this->name})) ? $_REQUEST->{$this->name} : null;
            break;
            }
        }

        // default value = null
        return null;
    }

    /**
     * Returns the validity status of the element
     *
     * This method is intend to return the validity status of the element.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if field is valid, otherwise FALSE
     * @access public
     */
    public function isValid()
    {
        // if valid-status wasn't checked before do it now
        if ($this->valid === null) {
            $this->_checkValidity();
        }

        // return the valid status of this field
        return $this->valid;
    }

    /**
     * Returns the error of the element (if exist)
     *
     * This method is intend to return the error of the element (if exist).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if field is valid, otherwise FALSE
     * @access public
     */
    public function getError()
    {
        // if valid-status wasn't checked before do it now
        if ($this->error === null) {
            $this->_checkValidity();
        }

        // return the valid status of this field
        return $this->error;
    }

    /**
     * Returns the HTML-code for the input-field
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Generated HTML of input-field
     * @access public
     */
    public function renderDefault()
    {
        // add elements html-code
        $html = '<input type="'.$this->type.'"';

        foreach ($this->attributes as $attribute => $value) {
            $html .= ' '.$attribute.'="'.$value.'"';
        }

        // end of element
        $html .= ' />'.$this->nl();

        // return generated html code
        return $html;
    }

    /*******************************************************************************************************************
     * \\ END - HELPER METHODS
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN - RENDERER -> THIS DIFFERS FOR EACH INPUT-FIELD-TYPE
     ******************************************************************************************************************/

    /**
     * checks the validity and error of the current element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     * @throws DoozR_Form_Service_Exception
     */
    private function _checkValidity()
    {
        // if name not set we can't set a valid status
        if ($this->name == '') {
            throw new DoozR_Form_Service_Exception(
                'You need to set a name for an element first to check its valid-status!'
            );
        }

        // check if form was submitted
        if ($this->submitted) {
            // check error array for existence of current field
            if (isset($this->formError[$this->name])) {
                // store valid-status
                $this->valid = false;

                // store error
                $this->error = $this->formError[$this->name];
            } else {
                $this->error = false;
                $this->valid = true;
            }
        } else {
            // not submitted neither "valid" nor "invalid"  just NULL
            $this->valid = true;
            $this->error = false;
        }
    }

    /**
     * Returns the original passed in Form handle of parent form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service The instance of parent form
     * @access public
     */
    public function done()
    {
        // return the original form handle for further chaining
        return $this->form;
    }

    /**
     * Renders a passed part + style and returns HTML-code
     *
     * @param string $part The part to render (label|element|error)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string generated HTML-code
     * @access public
     */
    public function renderPart($part)
    {
        // default no html code
        $html = '';

        // check part
        if ($part === 'label' && $this->hasLabel()) {
            $html = $this->renderLabel();

        } elseif ($part === 'element') {
            $html = $this->renderElement();

        } elseif ($part === 'error' && $this->hasError()) {
            $message = $this->getMessage($part);
            $html    = '<div id="error_'.$this->getName().'" class="'.$this->getName().' '.
                       self::CLASS_INVALID.'">'.$message.'</div>'.$this->nl();
        }

        // return html code for element
        return $html;
    }

    /**
     * Renders the label and returns HTML-code
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string generated HTML-code
     * @access protected
     */
    protected function renderLabel()
    {
        $message = $this->getMessage('label');
        $id      = ($this->getId()) ? ' id="label_'.$this->getId().'"' : '';
        $style   = (isset($style['style']) && $style['style'] !== null) ? ' style="'.$style['style'].'"' : '';
        $css     = (isset($style['css']) && $style['css'] !== null) ? ' class="'.$style['css'].'" ' : '';
        $html    = '<label for="'.$this->getName().'"'.$id.$css.$style.'>'.$message.'</label>'.$this->nl();

        return $html;
    }

    /**
     * Renders the element and returns HTML-code
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string generated HTML-code
     * @access protected
     */
    protected function renderElement()
    {
        $html = '';

        //
        $this->_restore();

        $message = $this->getMessage('element');
        $html    = '<input type="'.$this->type.'"';

        foreach ($this->attributes as $attribute => $value) {
            $html .= ' '.$attribute.'="'.$value.'"';
        }

        $html .= ' />'.$this->nl();

        return $html;
    }


    private function _restore()
    {
        // check if the value of element is still empty - so it's
        // possible that we haven't yet finished restoring value

        // value = null -> not submitted but maybe jumped
        // or value =
        if ($this->getValue() === null || $this->getValue() === '') {
            if ($this->jumped === true && ($this->name !== DoozR_Form_Service::PREFIX.'Token')) {
                $this->setAttribute('value', $this->getJumpValue());
            }
        }
    }

    /**
     * Returns the message of this element by passed part (label|element|error).
     * This can be either a label or the value of the element and the input is
     * always retrieved by I18n if set.
     *
     * @param string $part The part to return message for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string generated HTML-code
     * @access protected
     */
    protected function getMessage($part)
    {
        //
        $message = '';

        switch ($part) {
        case 'label':
            if ($this->i18n !== null) {
                $message = $this->i18n->_($this->label);
            } else {
                $message = $this->label;
            }
            break;
        case 'element':
            $message = '';
            break;
        case 'error':
            if ($this->i18n !== null) {
                $message = $this->i18n->_($this->getError()->getI18NIdentifier(), $this->getError()->getErrorInfo());
            } else {
                $message = $this->getError()->getErrorMessage();
            }
            break;
        }

        return $message;
    }

    /**
     * Returns true if element has a label otherwise false
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if element has label, otherwise FALSE
     * @access public
     */
    public function hasLabel()
    {
        return ($this->label !== null && is_string($this->label));
    }

    /**
     * Returns true if element has an error otherwise false
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if element has label, otherwise FALSE
     * @access public
     */
    public function hasError()
    {
        $hasError = ($this->isValid() !== true);
        return $hasError;
    }

    /**
     * Sets the position of a passed part [(label|element|error)]
     *
     * @param string  $part     The part to set
     * @param integer $position The position of the passed part (0,1,2)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setPosition($part, $position)
    {
        $this->position[$part] = $position;
    }

    /**
     * Shortcut to setPosition
     *
     * @param string  $part     The part to set
     * @param integer $position The position of the passed part (0,1,2)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Current active instance
     * @access public
     */
    public function position($part, $position)
    {
        $this->setPosition($part, $position);
        return $this;
    }

    /**
     * Sets all positions at once (batch)
     *
     * @param array $positions The positions to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setPositions(array $positions)
    {
        $this->position = $positions;
    }

    /**
     * Shortcut to setPositions
     *
     * @param array $positions The positions to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Current active instance
     * @access public
     */
    public function positions(array $positions)
    {
        $this->setPositions($positions);
        return $this;
    }

    /**
     * Adds a surrounding container to current element
     *
     * @param string $html The html to place within container
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Current active instance
     * @access protected
     */
    protected function setContainer($html)
    {
        // container use configured?
        if ($this->useContainer) {
            $containerClass = '';

            if ($this->containerClass !== null) {
                $containerClass = ' '.$this->containerClass;
            }

            $html = '<div class="'.self::DIV_CONTAINER_CLASSNAME.$containerClass.'" id="container_'.
                    $this->getId().'">'.$this->nl().$html.$this->nl().'</div>'.$this->nl();
        }

        return $html;
    }

    /**
     * Returns the generated HTML-code of this element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The generated HTML-code for this element
     * @access public
     */
    public function render()
    {
        // assume empty html output
        $this->html = '';

        // sort types by position
        asort($this->position);

        // merge existing styles and css-classes for rendering
        $style = $this->combineStyles();

        // iterate the elements of the current processed field
        foreach ($this->position as $part => $position) {
            $this->html .= $this->renderPart($part, $style);
        }

        // add all elements with surrounding div
        $this->html = $this->setContainer($this->html);

        // check for direct output or return of value
        return $this->html;
    }

    /*******************************************************************************************************************
     * \\ END - RENDERER -> THIS DIFFERS FOR EACH INPUT-FIELD-TYPE
     ******************************************************************************************************************/
}

?>
