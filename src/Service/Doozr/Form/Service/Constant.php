<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service - Shared Constant(s).
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
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */

/**
 * Doozr - Form - Service - Shared Constant(s).
 *
 * Shared Constants used by classes of this service.
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
class Doozr_Form_Service_Constant
{
    /*------------------------------------------------------------------------------------------------------------------
    | GENERAL
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * The prefix for fields, fieldset ...
     *
     * @var string
     */
    const PREFIX = 'Doozr_Form_Service_';

    /**
     * Identifier for data pool "components".
     *
     * @var string
     */
    const IDENTIFIER_COMPONENTS = 'components';

    /**
     * Identifier for data pool "lastvalidstep".
     *
     * @var string
     */
    const IDENTIFIER_LASTVALIDSTEP = 'lastvalidstep';

    /**
     * Identifier for data pool "step".
     *
     * @var string
     */
    const IDENTIFIER_STEP = 'step';

    /**
     * Identifier for data pool "steps".
     *
     * @var string
     */
    const IDENTIFIER_STEPS = 'steps';

    /**
     * Identifier for data pool "data".
     *
     * @var string
     */
    const IDENTIFIER_DATA = 'data';

    /**
     * Identifier for data pool "files".
     *
     * @var string
     */
    const IDENTIFIER_FILES = 'files';

    /**
     * Identifier for data pool "token".
     *
     * @var string
     */
    const IDENTIFIER_TOKEN = 'token';

    /**
     * Identifier for data pool "submitted".
     *
     * @var string
     */
    const IDENTIFIER_SUBMITTED = 'submitted';

    /**
     * Identifier for data pool "jump".
     *
     * @var string
     */
    const IDENTIFIER_JUMP = 'jump';

    /**
     * Identifier for data pool "method".
     *
     * @var string
     */
    const IDENTIFIER_METHOD = 'method';

    /**
     * Identifier for data pool "tokenbehavior".
     *
     * @var string
     */
    const IDENTIFIER_TOKENBEHAVIOR = 'tokenbehavior';

    /**
     * Identifier for upload.
     *
     * @var string
     */
    const IDENTIFIER_UPLOAD = 'upload';

    /*------------------------------------------------------------------------------------------------------------------
    | HTML TAG
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Field-types of form elements.
     *
     * @var string
     */
    const HTML_TAG_NONE = '';

    /**
     * The tag form.
     *
     * @var string
     */
    const HTML_TAG_FORM = 'form';

    /**
     * The tag radio.
     *
     * @var string
     */
    const HTML_TAG_RADIO = 'radio';

    /**
     * The tag checkbox.
     *
     * @var string
     */
    const HTML_TAG_CHECKBOX = 'checkbox';

    /**
     * The tag select.
     *
     * @var string
     */
    const HTML_TAG_SELECT = 'select';

    /**
     * The tag option.
     *
     * @var string
     */
    const HTML_TAG_OPTION = 'option';

    /**
     * The tag optgroup.
     *
     * @var string
     */
    const HTML_TAG_OPTGROUP = 'optgroup';

    /**
     * The tag textarea.
     *
     * @var string
     */
    const HTML_TAG_TEXTAREA = 'textarea';

    /**
     * The tag input.
     *
     * @var string
     */
    const HTML_TAG_INPUT = 'input';#

    /**
     * The tag label.
     *
     * @var string
     */
    const HTML_TAG_LABEL = 'label';

    /**
     * The tag div.
     *
     * @var string
     */
    const HTML_TAG_DIV = 'div';

    /**
     * The tag fieldset.
     *
     * @var string
     */
    const HTML_TAG_FIELDSET = 'fieldset';

    /**
     * The tag legend.
     *
     * @var string
     */
    const HTML_TAG_LEGEND = 'legend';

    /**
     * The tag button.
     *
     * @var string
     */
    const HTML_TAG_BUTTON = 'button';

    /**
     * The tag datalist.
     *
     * @var string
     */
    const HTML_TAG_DATALIST = 'datalist';

    /**
     * The tag keygen.
     *
     * @var string
     */
    const HTML_TAG_KEYGEN = 'keygen';

    /**
     * The tag output.
     *
     * @var string
     */
    const HTML_TAG_OUTPUT = 'output';

    /**
     * HTML-Version 4 flag.
     *
     * @var int
     */
    const HTML_VERSION_4 = 4;

    /**
     * HTML-Version 5 flag.
     *
     * @var int
     */
    const HTML_VERSION_5 = 5;

    /*------------------------------------------------------------------------------------------------------------------
    | UPLOAD
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Max size of file upload in bytes.
     *
     * @var int
     */
    const DEFAULT_MAX_UPLOAD_FILESIZE = 65535;

    /**
     * The encoding type for "normal" form data (default).
     *
     * @var string
     */
    const ENCODING_TYPE_NOFILEUPLOAD = 'application/x-www-form-urlencoded';

    /**
     * The encoding type for multipart upload form data.
     *
     * @var string
     */
    const ENCODING_TYPE_FILEUPLOAD = 'multipart/form-data';

    /**
     * Default for has upload.
     *
     * @@int
     */
    const DEFAULT_HAS_FILE_UPLOAD = 1;

    /**
     * The encoding type for text which has encoded
     * spaces plus' will become (+).
     *
     * @var string
     */
    const ENCODING_TYPE_TEXT = 'text/plain';

    /**
     * Type for generic components like <textarea> <select> ...
     *
     * @var string
     */
    const COMPONENT_GENERIC = 'generic';

    /**
     * Type for default components like <input type="..." ...>.
     *
     * @var string
     */
    const COMPONENT_DEFAULT = 'default';

    /**
     * Container component type. Container is a form component
     * which must contain children to be valid like <select>.
     *
     * @var string
     */
    const COMPONENT_CONTAINER = 'container';

    /*------------------------------------------------------------------------------------------------------------------
    | DEFAULTS
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Default "method" (post|get) for form submission.
     *
     */
    const DEFAULT_METHOD = Doozr_Http::REQUEST_METHOD_POST;

    /**
     * Default encoding type of forms.
     *
     * @var string
     */
    const DEFAULT_ENCODING_TYPE = self::ENCODING_TYPE_NOFILEUPLOAD;

    /**
     * Default name used at init for default (e.g. for optional I18n scope).
     *
     * @var string
     */
    const DEFAULT_SCOPE = 'Form';

    /**
     * Default step if no step is/was set before.
     *
     * @var int
     */
    const DEFAULT_STEP_FIRST = 1;

    /**
     * Default amount of steps till finish is 1 (1/1).
     *
     * @var int
     */
    const DEFAULT_STEP_LAST = 1;

    /**
     * The name/identifier of hidden field for submission status.
     *
     * @var string
     */
    const DEFAULT_NAME_FIELD_SUBMITTED = 'Doozr_Form_Service_Submitted';

    /**
     * The fieldname of the token field.
     *
     * @var string
     */
    const DEFAULT_NAME_FIELD_TOKEN = 'Doozr_Form_Service_Token';

    /**
     * The name of the element which carries the
     * information about a valid file uploaded.
     *
     * @var string
     */
    const DEFAULT_NAME_FIELD_FILE = 'Doozr_Form_Service_File';

    /**
     * The fieldname for the step field.
     *
     * @var string
     */
    const DEFAULT_NAME_FIELD_STEP = 'Doozr_Form_Service_Step';

    /**
     * The fieldname for the steps field.
     *
     * @var string
     */
    const DEFAULT_NAME_FIELD_STEPS = 'Doozr_Form_Service_Steps';

    /**
     * The fieldname for the jump field.
     *
     * @var string
     */
    const DEFAULT_NAME_FIELD_JUMP = 'Doozr_Form_Service_Jump';

    /**
     * Name of the upload meta field.
     *
     * @var string
     */
    const DEFAULT_NAME_FIELD_UPLOAD = 'Doozr_Form_Service_Upload';

    /**
     * Template for closing tag.
     *
     * @var string
     */
    const DEFAULT_TEMPLATE_CLOSING = '<{{TAG}}{{ATTRIBUTES}}>{{INNER-HTML}}</{{TAG}}>';

    /**
     * Template for non-closing tag.
     *
     * @var string
     */
    const DEFAULT_TEMPLATE_NONCLOSING = '<{{TAG}}{{ATTRIBUTES}} />';

    /**
     * Default behavior for invalid token.
     *
     * @var int
     */
    const DEFAULT_TOKEN_BEHAVIOR = Doozr_Form_Service_Handler_TokenHandler::TOKEN_BEHAVIOR_DEFAULT;

    /**
     * Default jumped status.
     *
     * @var bool
     */
    const DEFAULT_JUMPED = false;

    /**
     * Default submitted status.
     *
     * @var bool
     */
    const DEFAULT_SUBMITTED = false;

    /**
     * Default valid status of form.
     *
     * @var bool
     */
    const DEFAULT_VALID_STATUS = true;

    /**
     * Default complete status of form.
     *
     * @var bool
     */
    const DEFAULT_COMPLETE_STATUS = false;

    /*------------------------------------------------------------------------------------------------------------------
    | TRANSPORTATION
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Request method GET.
     *
     * @var string
     */
    const METHOD_GET = Doozr_Http::REQUEST_METHOD_GET;

    /**
     * Request method POST.
     *
     * @var string
     */
    const METHOD_POST = Doozr_Http::REQUEST_METHOD_POST;

    /*------------------------------------------------------------------------------------------------------------------
    | ERROR
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Identifier for error messages of form component.
     *
     * @var string
     */
    const ERROR_IDENTIFIER_FORM = 'form';
}
