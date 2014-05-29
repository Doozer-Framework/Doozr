<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Service - Validate
 *
 * Constant.php - Constants used for validation and error-handling
 * context.
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
 * DoozR - Service - Validate
 *
 * Constants used for validation and error-handling context.
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Form_Service_Validate_Constant
{
    /*------------------------------------------------------------------------------------------------------------------
    | Errors of elements
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Error flag for invalid values
     *
     * @var string
     *
     * @access public
     * @const
     */
    const VALUE = 'value';

    /**
     * Error flag for required fields
     *
     * @var string
     *
     * @access public
     * @const
     */
    const REQUIRED = 'required';

    /**
     * Error flag for invalid filetype
     *
     * @var string
     *
     * @access public
     * @const
     */
    const FILETYPE = 'filetype';

    /**
     * Error flag for invalid file extension
     *
     * @var string
     *
     * @access public
     * @const
     */
    const FILEEXTENSION = 'fileextension';

    /**
     * Error flag for invalid min filesize
     *
     * @var string
     *
     * @access public
     * @const
     */
    const FILESIZEMIN = 'filesizemin';

    /**
     * Error flag for invalid max filesize
     *
     * @var string
     *
     * @access public
     * @const
     */
    const FILESIZEMAX = 'filesizemax';

    /**
     * Error flag prefix used for all elements
     *
     * @var string
     *
     * @access public
     * @const
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
     * @const
     */
    const ELEMENTS_INVALID = 'elements_invalid';

    /**
     * From error flag for store containing values is invalid
     *
     * @var string
     *
     * @access public
     * @const
     */
    const STORE_INVALID = 'store_invalid';

    /**
     * From error flag for form was submitted using an invalid method
     * e.g. POST instead of GET.
     *
     * @var string
     *
     * @access public
     * @const
     */
    const REQUESTTYPE_INVALID = 'requesttype_invalid';

    /**
     * From error flag for form was submitted using an invalid token
     * e.g. double submitted or something like that.
     *
     * @var string
     *
     * @access public
     * @const
     */
    const TOKEN_INVALID = 'token_invalid';
}
