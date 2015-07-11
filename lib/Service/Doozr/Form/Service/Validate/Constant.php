<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Service - Validate
 *
 * Constant.php - Constants used for validation and error-handling
 * context.
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
 * Doozr - Service - Validate
 *
 * Constants used for validation and error-handling context.
 *
 * @category   Doozr
 * @package    Doozr_Service
 * @subpackage Doozr_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Form_Service_Validate_Constant
{
    /*------------------------------------------------------------------------------------------------------------------
    | Errors of elements
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * valid type for impact validation (impact is detected by PHP-IDS if enabled)
     * we only check for impact if PHP-IDS is enabled in config. otherwise we have no
     * base for validation.
     *
     * @var string
     * @access public
     */
    const VALIDATE_IMPACT = 'impact';

    /**
     * valid type for required components/fields "required" means that this component/field
     * must be submitted no matter if it is empty or value is an empty string. it just
     * need to be submitted.
     *
     * if you are looking for a validation that check for a given value - with not
     * accepting empty submissions:
     * @see VALIDATE_NOTEMPTY
     *
     * @var string
     * @access public
     */
    const VALIDATE_REQUIRED = 'required';

    /**
     * valid type for not-empty components/fields "notempty" means that this component/field
     * must be submitted with a value. at minimum one char must be submitted. empty
     * submissions are invalid.
     *
     * if you are looking for a validation that just checks if a field
     * was submitted:
     * @see REQUIRED
     *
     * @var string
     * @access public
     */
    const VALIDATE_NOTEMPTY = 'notempty';

    /**
     * valid type for empty components/fields "empty" means that this component/field must be
     * submitted without a value. The counterpart to "notempty"
     *
     * @var string
     * @access public
     */
    const VALIDATE_EMPTY = 'empty';

    /**
     * valid type for exact-value-match components/fields "value" means that this component/field
     * must exactly (===) match the stored value.
     *
     * @var string
     * @access public
     */
    const VALIDATE_VALUE = 'value';

    /**
     * valid type for alphabetic components/fields "alphabetic" means that this component/field
     * must be submitted with a value. at minimum one char must be submitted. empty
     * submissions are invalid.
     *
     * if you are looking for a validation that just checks if a field was submitted:
     * @see REQUIRED
     *
     * @var string
     * @access public
     */
    const VALIDATE_ALPHABETIC = 'alphabetic';

    /**
     * valid-type for minimal length required
     *
     * @var string
     * @access public
     */
    const VALIDATE_MINLENGTH = 'minlength';

    /**
     * valid-type for maximal length allowed
     *
     * @var string
     * @access public
     */
    const VALIDATE_MAXLENGTH = 'maxlength';

    /**
     * valid-type for email (check syntax)
     *
     * @var string
     * @access public
     */
    const VALIDATE_EMAIL = 'email';

    /**
     * valid-type for email (live deliver-to-postbox check)
     *
     * @var string
     * @access public
     */
    const VALIDATE_EMAILAUTH = 'emailauth';

    /**
     * valid type for numeric components/fields "numeric" means that this component/field must be
     * submitted with a value. at minimum one char must be submitted. empty submissions are
     * invalid.
     *
     * @var string
     * @access public
     */
    const VALIDATE_NUMERIC = 'numeric';

    /**
     * valid type for not-empty components/fields "notnull" means that this component/field must
     * be submitted with a value. A value != 0 and != NULL !!! at minimum one char must be
     * submitted. empty submissions are invalid.
     *
     * @var string
     * @access public
     */
    const VALIDATE_NOTNULL = 'notnull';

    /**
     * valid-type for IP-Addresses
     *
     * @var string
     * @access public
     */
    const VALIDATE_IP = 'ip';

    /**
     * valid-type for lowercase input (a-z)
     *
     * @var string
     * @access public
     */
    const VALIDATE_LOWERCASE = 'lowercase';

    /**
     * valid-type for uppercase input (A-Z)
     *
     * @var string
     * @access public
     */
    const VALIDATE_UPPERCASE = 'uppercase';

    /**
     * valid-type for post-/zipcode input
     *
     * @var string
     * @access public
     */
    const VALIDATE_POSTCODE = 'postcode';

    /**
     * valid-type for USTID input
     *
     * @var string
     * @access public
     */
    const VALIDATE_USTID = 'ustid';

    /**
     * valid type for boolean components/fields "boolean" means that this component/field must be
     * submitted with a value => TRUE or FALSE
     *
     * @var string
     * @access public
     */
    const VALIDATE_BOOLEAN = 'boolean';

    /**
     * valid type for boolean components/fields "double" means that this component/field must be
     * submitted with a value e.g. 1.0 1.1 ...
     *
     * @var string
     * @access public
     */
    const VALIDATE_DOUBLE = 'double';

    /**
     * valid type for boolean components/fields "integer" means that this component/field must be
     * submitted with a value e.g. 1 or 2 or 234 or 43547.
     *
     * @var string
     * @access public
     */
    const VALIDATE_INTEGER = 'integer';

    /**
     * Validation type for string
     *
     * @var string
     * @access public
     */
    const VALIDATE_STRING = 'string';

    /**
     * Validation type for linked fields
     *
     * @var string
     * @access public
     */
    const VALIDATE_LINK = 'link';

    /**
     * valid-type for invalid values input
     *
     * @var string
     * @access public
     */
    const VALIDATE_INVALID = 'invalid';

    /**
     * Error flag for invalid filetype
     *
     * @var string
     *
     * @access public
     */
    const VALIDATE_FILETYPE = 'filetype';

    /**
     * Error flag for invalid min filesize
     *
     * @var string
     *
     * @access public
     */
    const VALIDATE_FILESIZEMIN = 'filesizemin';

    /**
     * Error flag for invalid max filesize
     *
     * @var string
     *
     * @access public
     */
    const VALIDATE_FILESIZEMAX = 'filesizemax';

    /**
     * Error flag for invalid file extension
     *
     * @var string
     *
     * @access public
     */
    const VALIDATE_FILEEXTENSION = 'fileextension';

    /**
     * Error flag for regular expression fails.
     *
     * @var string
     *
     * @access public
     */
    const VALIDATE_REGULAREXPRESSION = 'regularexpression';

    /*------------------------------------------------------------------------------------------------------------------
    | Constants for Error fields & prefixes & suffixes
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Error flag prefix used for all elements
     *
     * @var string
     *
     * @access public
     */
    const ERROR_PREFIX = 'doozr_form_service_error_';

    /*------------------------------------------------------------------------------------------------------------------
    | Errors of form
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * From error flag for form contains invalid elements
     *
     * @var string
     *
     * @access public
     */
    const ERROR_ELEMENTS_INVALID = 'elements_invalid';

    /**
     * From error flag for store containing values is invalid
     *
     * @var string
     *
     * @access public
     */
    const ERROR_STORE_INVALID = 'store_invalid';

    /**
     * From error flag for form was submitted using an invalid method
     * e.g. POST instead of GET.
     *
     * @var string
     *
     * @access public
     */
    const ERROR_REQUESTTYPE_INVALID = 'requesttype_invalid';

    /**
     * From error flag for form was submitted using an invalid token
     * e.g. double submitted or something like that.
     *
     * @var string
     *
     * @access public
     */
    const ERROR_TOKEN_INVALID = 'token_invalid';
}
