<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service - Shared Constant(s)
 *
 * Constant.php - Shared Constants used by classes of this service.
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
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

/**
 * Doozr - Form - Service - Shared Constant(s)
 *
 * Shared Constants used by classes of this service.
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
class Doozr_Form_Service_Constant
{
    /**
     * The prefix for fields, fieldsets ...
     *
     * @var string
     * @access public
     */
    const PREFIX = 'Doozr_Form_Service_';

    /**
     * Identifier for the container object
     * in an AngularJS scope. So in this default setup
     * hidden fields available through
     *
     * @example $scope.hidden.[NAME]
     *
     * @var string
     * @access public
     */
    const SCOPE = '';

    /**
     * The default name used at init for default (e.g. for optional I18n namespace)
     *
     * @var string
     * @access public
     */
    const DEFAULT_NAMESPACE = 'Form';

    /**
     * The default step if no step is/was set before
     *
     * @var int
     * @access public
     */
    const STEP_DEFAULT_FIRST = 1;

    /**
     * The default amount of steps till finish is 1 (1/1)
     *
     * @var int
     * @access public
     */
    const STEP_DEFAULT_LAST = 1;

    /**
     * The name/identifier of hidden field for submission status
     *
     * @var string
     * @access public
     */
    const FORM_NAME_FIELD_SUBMITTED = 'Submitted';

    /**
     * The fieldname of the token field.
     *
     * @var string
     * @access public
     */
    const FORM_NAME_FIELD_TOKEN = 'Token';

    /**
     * The name of the element which carries the
     * information about a valid file uploaded.
     *
     * @var string
     * @access public
     */
    const FORM_NAME_FIELD_FILE = 'File';

    /**
     * The fieldname for the step field.
     *
     * @var string
     * @access public
     */
    const FORM_NAME_FIELD_STEP = 'Step';

    /**
     * The fieldname for the steps field.
     *
     * @var string
     * @access public
     */
    const FORM_NAME_FIELD_STEPS = 'Steps';

    /**
     * The fieldname for the jump field.
     *
     * @var string
     * @access public
     */
    const FORM_NAME_FIELD_JUMP = 'Jump';

    /**
     * Token behavior constants
     * DENY = Block access to page (tries to send 404)
     *
     * @var int
     * @access public
     */
    const TOKEN_BEHAVIOR_DENY = 1;

    /**
     * Token behavior constants
     * IGNORE = No matter if valid or invalid - the token just get ignored
     *
     * @var int
     * @access public
     */
    const TOKEN_BEHAVIOR_IGNORE = 2;

    /**
     * Token behavior constants
     * DENY = Block access to page (tries to send 404)
     *
     * @var int
     * @access public
     */
    const TOKEN_BEHAVIOR_INVALIDATE = 3;

    /**
     * Fieldtypes of form elements
     *
     * @var string
     * @access public
     */
    const HTML_TAG_NONE = '';

    /**
     * The tag form
     *
     * @var string
     * @access public
     */
    const HTML_TAG_FORM = 'form';

    /**
     * The tag radio
     *
     * @var string
     * @access public
     */
    const HTML_TAG_RADIO = 'radio';

    /**
     * The tag checkbox
     *
     * @var string
     * @access public
     */
    const HTML_TAG_CHECKBOX = 'checkbox';

    /**
     * The tag select
     *
     * @var string
     * @access public
     */
    const HTML_TAG_SELECT = 'select';

    /**
     * The tag option
     *
     * @var string
     * @access public
     */
    const HTML_TAG_OPTION = 'option';

    /**
     * The tag optgroup
     *
     * @var string
     * @access public
     */
    const HTML_TAG_OPTGROUP = 'optgroup';

    /**
     * The tag textarea
     *
     * @var string
     * @access public
     */
    const HTML_TAG_TEXTAREA = 'textarea';

    /**
     * The tag input
     *
     * @var string
     * @access public
     */
    const HTML_TAG_INPUT = 'input';#

    /**
     * The tag label
     *
     * @var string
     * @access public
     */
    const HTML_TAG_LABEL = 'label';

    /**
     * The tag div
     *
     * @var string
     * @access public
     */
    const HTML_TAG_DIV = 'div';

    /**
     * The tag fieldset
     *
     * @var string
     * @access public
     */
    const HTML_TAG_FIELDSET = 'fieldset';

    /**
     * The tag legend
     *
     * @var string
     * @access public
     */
    const HTML_TAG_LEGEND = 'legend';

    /**
     * The tag button
     *
     * @var string
     * @access public
     */
    const HTML_TAG_BUTTON = 'button';

    /**
     * The tag datalist
     *
     * @var string
     * @access public
     */
    const HTML_TAG_DATALIST = 'datalist';

    /**
     * The tag keygen
     *
     * @var string
     * @access public
     */
    const HTML_TAG_KEYGEN = 'keygen';

    /**
     * The tag output
     *
     * @var string
     * @access public
     */
    const HTML_TAG_OUTPUT = 'output';

    /**
     * HTML-Version 4 flag.
     *
     * @var int
     * @access public
     */
    const HTML_VERSION_4 = 4;

    /**
     * HTML-Version 5 flag.
     *
     * @var int
     * @access public
     */
    const HTML_VERSION_5 = 5;

    /*------------------------------------------------------------------------------------------------------------------
    | Upload related constants
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * The encoding type for "normal" form data (default)
     *
     * @var string
     * @access public
     */
    const ENCODING_TYPE_DEFAULT = 'application/x-www-form-urlencoded';

    /**
     * The encoding type for multitype upload form data
     *
     * @var string
     * @access public
     */
    const ENCODING_TYPE_FILEUPLOAD = 'multipart/form-data';

    /**
     * The encoding type for text which has encoded
     * spaces plus' will become (+).
     *
     * @var string
     * @access public
     */
    const ENCODING_TYPE_TEXT = 'text/plain';

    /**
     * Type for generic components like <textare> <select> ...
     *
     * @var string
     * @access public
     */
    const COMPONENT_GENERIC = 'generic';

    /**
     * Type for default components like <input type="..." ...>
     *
     * @var string
     * @access public
     */
    const COMPONENT_DEFAULT = 'default';

    /**
     * Container component type. Container is a form component
     * which must contain childs to be valid like <select>
     *
     * @var string
     * @access public
     */
    const COMPONENT_CONTAINER = 'container';

    /**
     * Template for closing tag
     *
     * @var string
     * @access public
     */
    const TEMPLATE_DEFAULT_CLOSING = '<{{TAG}}{{ATTRIBUTES}}>{{INNER-HTML}}</{{TAG}}>';

    /**
     * Template for non-closing tag
     *
     * @var string
     * @access public
     */
    const TEMPLATE_DEFAULT_NONCLOSING = '<{{TAG}}{{ATTRIBUTES}} />';
}
