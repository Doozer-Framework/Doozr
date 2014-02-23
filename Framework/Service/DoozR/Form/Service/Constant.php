<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Form - Service - Shared Constant(s)
 *
 * Constant.php - Shared Constants used by classes of this service.
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

/**
 * DoozR - Form - Service - Shared Constant(s)
 *
 * Shared Constants used by classes of this service.
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
class DoozR_Form_Service_Constant
{
    /**
     * The prefix for fields, fieldsets ...
     *
     * @var string
     * @access public
     */
    const PREFIX = 'DoozR_Form_Service_';

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
     * @var integer
     * @acces public
     */
    const STEP_DEFAULT_FIRST = 1;

    /**
     * The default amount of steps till finish is 1 (1/1)
     *
     * @var integer
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
     * @const
     */
    const FORM_NAME_FIELD_TOKEN = 'Token';

    /**
     * The name of the element which carries the
     * information about a valid file uploaded.
     *
     * @var string
     * @access public
     * @const
     */
    const FORM_NAME_FIELD_FILE = 'File';

    /**
     * The fieldname for the step field.
     *
     * @var string
     * @access public
     * @const
     */
    const FORM_NAME_FIELD_STEP = 'Step';

    /**
     * The fieldname for the steps field.
     *
     * @var string
     * @access public
     * @const
     */
    const FORM_NAME_FIELD_STEPS = 'Steps';

    /**
     * The fieldname for the jump field.
     *
     * @var string
     * @access public
     * @const
     */
    const FORM_NAME_FIELD_JUMP = 'Jump';

    /**
     * Token behavior constants
     * DENY = Block access to page (tries to send 404)
     *
     * @var integer
     * @access public
     */
    const TOKEN_BEHAVIOR_DENY = 1;

    /**
     * Token behavior constants
     * IGNORE = No matter if valid or invalid - the token just get ignored
     *
     * @var integer
     * @access public
     */
    const TOKEN_BEHAVIOR_IGNORE = 2;

    /**
     * Token behavior constants
     * DENY = Block access to page (tries to send 404)
     *
     * @var integer
     * @access public
     */
    const TOKEN_BEHAVIOR_INVALIDATE = 3;

    /**
     * Fieldtypes of form elements
     *
     * @var string
     * @access public
     */
    const HTML_TAG_NONE     = '';
    const HTML_TAG_FORM     = 'form';
    const HTML_TAG_RADIO    = 'radio';
    const HTML_TAG_CHECKBOX = 'checkbox';
    const HTML_TAG_SELECT   = 'select';
    const HTML_TAG_TEXTAREA = 'textarea';
    const HTML_TAG_INPUT    = 'input';
    const HTML_TAG_LABEL    = 'label';
    const HTML_TAG_DIV      = 'div';
    const HTML_TAG_FIELDSET = 'fieldset';
    const HTML_TAG_LEGEND   = 'legend';

    /**
     * HTML-Version 4 flag.
     *
     * @var integer
     * @access public
     * @const
     */
    const HTML_VERSION_4 = 4;

    /**
     * HTML-Version 5 flag.
     *
     * @var integer
     * @access public
     * @const
     */
    const HTML_VERSION_5 = 5;

    /**
     * New line character(s) used when echoing HTML.
     *
     * @var string
     * @access public
     * @const
     */
    const NEW_LINE = "\n";

    /*------------------------------------------------------------------------------------------------------------------
    | Upload related constants
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * The encoding type for "normal" form data (default)
     *
     * @var string
     * @access public
     * @const
     */
    const ENCODING_TYPE_DEFAULT = 'application/x-www-form-urlencoded';

    /**
     * The encoding type for multitype upload form data
     *
     * @var string
     * @access public
     * @const
     */
    const ENCODING_TYPE_FILEUPLOAD = 'multipart/form-data';

    /**
     * The encoding type for text which has encoded
     * spaces plus' will become (+).
     *
     * @var string
     * @access public
     * @const
     */
    const ENCODING_TYPE_TEXT = 'text/plain';
}
