<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Form - Service
 *
 * Html.php - Form component to build forms. This component builds
 * the <form></form> part and provide some more specialized access
 * like getters and setters for action, method, ...
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

require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Form/Service/Component/Formcomponent.php';

/**
 * DoozR - Form - Service
 *
 * Form component to build forms. This component builds
 * the <form></form> part and provide some more specialized access
 * like getters and setters for action, method, ...
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
class DoozR_Form_Service_Component_Form extends DoozR_Form_Service_Component_Formcomponent
{
    /**
     * This is the tag-name for HTML output.
     * e.g. "input" or "form". Default empty string ""
     *
     * @var string
     * @access protected
     */
    protected $tag = DoozR_Form_Service_Constant::HTML_TAG_FORM;


    /*------------------------------------------------------------------------------------------------------------------
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param DoozR_Form_Service_Renderer_Interface  $renderer  Renderer instance for rendering this component
     * @param DoozR_Form_Service_Validator_Interface $validator Validator instance for validating this component
     *
     * @param null                                   $name
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Form_Service_Component_Form
     * @access public
     */
    public function __construct(
        DoozR_Form_Service_Renderer_Interface  $renderer  = null,
        DoozR_Form_Service_Validator_Interface $validator = null,
        $name = null
    ) {
        if ($name !== null) {
            $this->setName($name);
        }

        // Important call so observer storage ... can be initiated
        parent::__construct($renderer, $validator);
    }

    public function setAccept($mimeType)
    {
        $this->setAttribute('accept', $mimeType);
    }

    public function getAccept()
    {
        return $this->getAttribute('accept');
    }

    public function setAcceptCharset($mimeType)
    {
        $this->setAttribute('accept-charset', $mimeType);
    }

    public function getAcceptCharset()
    {
        return $this->getAttribute('accept-charset');
    }

    public function setAction($action)
    {
        $this->setAttribute('action', $action);
    }

    public function getAction()
    {
        return $this->getAttribute('action');
    }

    public function setAutocomplete($state)
    {
        $this->setAttribute('autocomplete', $state);
    }

    public function getAutocomplete()
    {
        return $this->getAttribute('autocomplete');
    }

    public function setEnctype($enctype)
    {
        $this->setAttribute('enctype', $enctype);
    }

    public function getEnctype()
    {
        return $this->getAttribute('enctype');
    }

    public function setMethod($method)
    {
        $this->setAttribute('method', $method);
    }

    public function getMethod()
    {
        return $this->getAttribute('method');
    }

    public function setNovalidate($novalidate = null)
    {
        $this->setAttribute('novalidate', $novalidate);
    }

    public function getNovalidate()
    {
        return $this->getAttribute('novalidate');
    }

    public function setTarget($target)
    {
        $this->setAttribute('target', $target);
    }

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
        $this->setEncodingType(DoozR_Form_Service_Constant::ENCODING_TYPE_FILEUPLOAD);
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
    public function setEncodingType($encodingType = DoozR_Form_Service_Constant::ENCODING_TYPE_DEFAULT)
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
        // Intentionally left blank to block access to set value property -> nonsense for <form>
    }
}
