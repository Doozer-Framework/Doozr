<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Form - Service - Error
 *
 * Error.php - Form Error class
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
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

/**
 * DoozR - Service - Form - Error
 *
 * Error.php - ...
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
class DoozR_Form_Service_Validate_Error
{
    /**
     * The error
     *
     * @var string
     * @access protected
     */
    protected $error;

    /**
     * Additional info to error
     *
     * @var mixed
     * @access protected
     */
    protected $info;

    /**
     * The value which triggered this error
     *
     * @var mixed
     * @access private
     */
    private $value;

    /**
     * The error-code
     *
     * @var int
     * @access protected
     */
    protected $errorCode = 0;

    /**
     * The error-message
     *
     * @var string
     * @access protected
     */
    protected $errorMessage = '';

    /**
     * The "error to error-code" translation-matrix
     *
     * @var array
     * @access protected
     * @static
     */
    protected $errorCodeMatrix = array();

    /**
     * The "error to error-message" translation-matrix
     *
     * @var array
     * @access protected
     * @static
     */
    protected $errorMessageMatrix = array(
         0 => 'UNKNOWN_ERROR',
         1 => 'N.A.',
         2 => 'This field is required.',
         3 => 'This field must not be empty.',
         4 => 'The input should only contain alphabetic characters in the range a-z or A-Z.',
         5 => 'This field must be checked.',
         6 => 'The minimum input length for this field is:',
         7 => 'The maximum input length for this field is:',
         8 => 'This emailaddress seems to be invalid.',
         9 => 'This emailaddress seems to be non-existent. We couldn\'t deliver our email.',
        10 => 'The input should only contain numbers in the range 0-9.',
        11 => 'The input must be a valid value. 0 and empty-values (Null) are invalid.',
        12 => 'The input must be a valid IP-Address (e.g. 192.168.0.1 or 10.8.2.216)',
        13 => 'The input must be lowercase. Only Characters in range a-z are allowed',
        14 => 'The input must be uppercase. Only Characters in range A-Z are allowed',
        15 => 'The input must be a valid postcode of the country:',
        16 => 'The input must be a valid USTID of the country:',
        17 => 'This field must be empty.',
        18 => 'This field can be either TRUE or FALSE.',
        19 => 'This field must be of type double (e.g. 1.0 or 2.3 ...).',
        20 => 'This field must be of type integer (e.g. 1 or 2 or 374 or 9384984)'
    );

    /**
     * Constructor.
     *
     * @param string      $error The error to set
     * @param string|null $value The value which is responsible for this error
     * @param array|null  $info  Additional information to error (e.g. the count of given chars on error minlength)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Validate_Error Instance of this class
     * @access public
     */
    public function __construct($error = null, $value = null, $info = null)
    {
        $this->init();

        $this->setError($error);
        $this->setValue($value);
        $this->setInfo($info);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for error.
     *
     * @param string $error The error
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * Getter for error.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The error if set, otherwise NULL
     * @access public
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Setter for Value.
     *
     * @param string $value The value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Getter for value.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The value if set, otherwise NULL
     * @access public
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Setter for info.
     *
     * @param string $info The info
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setInfo($info)
    {
        $this->info = $info;
    }

    /**
     * Returns the additional info.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The additional info
     * @access public
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Getter for the I18N-error-identifier
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The I18N-error-identifier
     * @access public
     */
    public function getI18nIdentifier()
    {
        // set identifier for localization
        return strtolower(__CLASS__ . '_' . $this->getError());
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Tools & Helper
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Initializes the validation matrix
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access protected
     */
    protected function init()
    {
        // build the local matrix just once
        if (empty($this->errorCodeMatrix)) {
            // get type and order from DoozR_Form_Service_Validate so we don't need to define it manually again
            $typeMatrix = DoozR_Form_Service_Validate_Validator::getValidationTypeMatrix();

            // iterate over types and construct error-code-matrix of it
            foreach ($typeMatrix as $type => $order) {
                $this->errorCodeMatrix[$type] = ($order+1);
            }
        }

        // success
        return true;
    }

    /**
     * Makes the input safe for output (remove xss and so on).
     *
     * @param string $value The string to make safe for output
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The input safe for output
     * @access protected
     */
    protected function safeOutput($value)
    {
        return urlencode(strip_tags($value));
    }

    /**
     * Returns error-code by error.
     *
     * @param string $error The error to return error-code for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The error-code
     * @access protected
     */
    protected function getErrorCode($error)
    {
        return (isset($this->errorCodeMatrix[$error])) ? $this->errorCodeMatrix[$error] : 0;
    }

    /**
     * Returns error-message by error-code
     *
     * @param int $errorCode The error-code to return error-message for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The error-message
     * @access protected
     */
    protected function getErrorMessage($errorCode)
    {
        if (!isset($this->errorMessageMatrix[$errorCode])) {
            $this->errorMessageMatrix[$errorCode] = 'ERROR_MSG_UNKNOWN';
        }

        // if additional information exist -> add it here
        if ($this->info) {
            if (is_string($this->info)) {
                $this->errorMessageMatrix[$errorCode] .= ' '.$this->info;

            } elseif (count($this->info) != 1 || $this->info[0] != null) {
                foreach ($this->info as $info) {
                    $info = serialize($info);
                    $this->errorMessageMatrix[$errorCode] .= ' '.$info;
                }
            }
        }

        // return costructed error message
        return $this->errorMessageMatrix[$errorCode];
    }
}
