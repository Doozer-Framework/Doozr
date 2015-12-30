<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service
 *
 * Html.php - Form component to build forms. This component builds
 * the <form></form> part and provide some more specialized access
 * like getters and setters for action, method, ...
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
 * @package    Doozr_Service
 * @subpackage Doozr_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Service/Doozr/Form/Service/Component/Formcomponent.php';

/**
 * Doozr - Form - Service
 *
 * Form component to build forms. This component builds
 * the <form></form> part and provide some more specialized access
 * like getters and setters for action, method, ...
 *
 * @category   Doozr
 * @package    Doozr_Service
 * @subpackage Doozr_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Form_Service_Component_Form extends Doozr_Form_Service_Component_Formcomponent
{
    /**
     * This is the tag-name for HTML output.
     * e.g. "input" or "form". Default empty string ""
     *
     * @var string
     * @access protected
     */
    protected $tag = Doozr_Form_Service_Constant::HTML_TAG_FORM;

    /*------------------------------------------------------------------------------------------------------------------
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param Doozr_Form_Service_Renderer_Interface  $renderer  Renderer instance for rendering this component
     * @param Doozr_Form_Service_Validator_Interface $validator Validator instance for validating this component
     * @param null                                   $name      The name of the form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \Doozr_Form_Service_Component_Form
     * @access public
     */
    public function __construct(
        Doozr_Form_Service_Renderer_Interface $renderer = null,
        Doozr_Form_Service_Validator_Interface $validator = null,
        $name = null
    ) {
        if ($name !== null) {
            $this->setName($name);
        }

        // Important call so observer storage ... can be initiated
        parent::__construct($renderer, $validator);
    }

    /**
     * Setter for accept.
     *
     * @param string $mimeType The mimetype to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setAccept($mimeType)
    {
        $this->setAttribute('accept', $mimeType);
    }

    /**
     * Getter for accept.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The value of the accept attribute
     * @access public
     */
    public function getAccept()
    {
        return $this->getAttribute('accept');
    }

    /**
     * Setter for accept.
     *
     * @param string $mimeType The mime-type to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setAcceptCharset($mimeType)
    {
        $this->setAttribute('accept-charset', $mimeType);
    }

    /**
     * Getter for accept-charset.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The value of the accept-charset attribute
     * @access public
     */
    public function getAcceptCharset()
    {
        return $this->getAttribute('accept-charset');
    }

    /**
     * Setter for action.
     *
     * @param string $action The action to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setAction($action)
    {
        $this->setAttribute('action', $action);
    }

    /**
     * Getter for action.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The value of the action attribute
     * @access public
     */
    public function getAction()
    {
        return $this->getAttribute('action');
    }

    /**
     * Setter for autocomplete.
     *
     * @param string $autocomplete The autocomplete to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setAutocomplete($autocomplete)
    {
        $this->setAttribute('autocomplete', $autocomplete);
    }

    /**
     * Getter for autocomplete.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The value of the autocomplete attribute
     * @access public
     */
    public function getAutocomplete()
    {
        return $this->getAttribute('autocomplete');
    }

    /**
     * Setter for enctype.
     *
     * @param string $enctype The enctype to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setEnctype($enctype)
    {
        $this->setAttribute('enctype', $enctype);
    }

    /**
     * Getter for enctype.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The value of the enctype attribute
     * @access public
     */
    public function getEnctype()
    {
        return $this->getAttribute('enctype');
    }

    /**
     * Setter for method.
     *
     * @param string $method The method to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setMethod($method)
    {
        $this->setAttribute('method', $method);
    }

    /**
     * Getter for method.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The value of the method attribute
     * @access public
     */
    public function getMethod()
    {
        return $this->getAttribute('method');
    }

    /**
     * Setter for novalidate.
     *
     * @param string $novalidate The novalidate to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setNovalidate($novalidate = null)
    {
        $this->setAttribute('novalidate', $novalidate);
    }

    /**
     * Getter for novalidate.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The value of the novalidate attribute
     * @access public
     */
    public function getNovalidate()
    {
        return $this->getAttribute('novalidate');
    }

    /**
     * Setter for target.
     *
     * @param string $target The target to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setTarget($target)
    {
        $this->setAttribute('target', $target);
    }

    /**
     * Getter for target.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The value of the target attribute
     * @access public
     */
    public function getTarget()
    {
        return $this->getAttribute('target');
    }

    /**
     * Enable the form to handle uploads
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function enableUpload()
    {
        $this->setEncodingType(Doozr_Form_Service_Constant::ENCODING_TYPE_FILEUPLOAD);
    }

    /**
     * Setter for encoding type.
     *
     * @param string $encodingType The correct encoding type
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setEncodingType($encodingType = Doozr_Form_Service_Constant::ENCODING_TYPE_DEFAULT)
    {
        $this->setAttribute('enctype', $encodingType);
    }

    /**
     * Getter for encoding type.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The encoding type of the form
     * @access public
     */
    public function getEncodingType()
    {
        return $this->getAttribute('enctype');
    }

    /**
     * Hook on Setter for value.
     *
     * @param mixed $value The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setValue($value)
    {
        /**
         * Intentionally left blank to block access to set value property -> nonsense for <form>
         */
    }
}
