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

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Class.php';
require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Form/Service/Validate.php';

/**
 * DoozR - Service - Form - Error
 *
 * Error.php - ...
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
class DoozR_Form_Service_Error extends DoozR_Base_Class
{
    /**
     * holds the error
     *
     * @var string
     * @access private
     */
    private $_error;

    /**
     * holds additional info to error
     *
     * @var mixed
     * @access private
     */
    private $_errorInfo;

    /**
     * holds the value which triggered this error
     *
     * @var mixed
     * @access private
     */
    private $_value;

    /**
     * holds the error-code
     *
     * @var integer
     * @access private
     */
    private $_errorCode = 0;

    /**
     * holds the error-message
     *
     * @var string
     * @access private
     */
    private $_errorMessage = '';

    /**
     * the I18N-error-string-identifier
     *
     * @var string
     * @access private
     */
    private $_errorI18N;

    /**
     * holds the "error to error-code" translation-matrix
     *
     * @var array
     * @access private
     * @static
     */
    private static $_errorCodeMatrix = array();

    /**
     * The "error to error-message" translation-matrix
     *
     * @var array
     * @access private
     * @static
     */
    private static $_errorMessageMatrix = array(
         0 => 'UNKNOWN_ERROR',
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
     * constructs the class
     *
     * This method is intend to construct the class.
     *
     * @param string $error The error to set
     * @param mixed  $value The value which is responible for this error
     * @param mixed  $info  Additional information to error occured (e.g. the count of given chars on error minlength)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object Instance of this class
     * @access public
     */
    public function __construct($error, $value, $info = null)
    {
        // call init
        self::_init();

        // set identifier for localization
        $this->_errorI18N = strtolower(__CLASS__.'_'.$error);

        // store error
        $this->_error = strtolower($error);

        // store value
        $this->_value = $value;

        // store optional additional info
        $this->_errorInfo = $info;

        // get error-code from error
        $this->_errorCode = $this->_getErrorCode($error);

        // get error-message from error
        $this->_errorMessage = $this->_getErrorMessage($this->_errorCode);
    }

    /**
     * makes the input safe for output (remove xss and so on)
     *
     * This method is intend to make the input safe for output (remove xss and so on).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access private
     */
    private static function _init()
    {
        // build the local matrix just once
        if (empty(self::$_errorCodeMatrix)) {
            // get type and order from DoozR_Form_Service_Validate so we don't need to define it manually again
            $typeMatrix = DoozR_Form_Service_Validate::getValidationTypeMatrix();

            // iterate over types and construct error-code-matrix of it
            foreach ($typeMatrix as $type => $order) {
                self::$_errorCodeMatrix[$type] = ($order+1);
            }
        }

        // success
        return true;
    }

    /**
     * makes the input safe for output (remove xss and so on)
     *
     * This method is intend to make the input safe for output (remove xss and so on).
     *
     * @param string $value The string to make safe for output
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The input safe for output
     * @access private
     */
    private function _safeOutput($value)
    {
        return urlencode(strip_tags($value));
    }

    /**
     * returns error-code by error
     *
     * This method is intend to return error-code by error.
     *
     * @param string $error The error to return error-code for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The error-code
     * @access private
     */
    private function _getErrorCode($error)
    {
        return (isset(self::$_errorCodeMatrix[$error])) ? self::$_errorCodeMatrix[$error] : 0;
    }

    /**
     * returns error-message by error-code
     *
     * This method is intend to return error-message by error-code
     *
     * @param integer $errorCode The error-code to return error-message for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The error-message
     * @access private
     * @todo:  Add translation module/class to this class (don't know the pattern exactly)!
     */
    private function _getErrorMessage($errorCode)
    {
        if (!isset(self::$_errorMessageMatrix[$errorCode])) {
            self::$_errorMessageMatrix[$errorCode] = 'ERROR_MSG_UNKNOWN';
        }

        // if additional information exist -> add it here
        if ($this->_errorInfo) {
            if (is_string($this->_errorInfo)) {
                self::$_errorMessageMatrix[$errorCode] .= ' '.$this->_errorInfo;

            } elseif (count($this->_errorInfo) != 1 || $this->_errorInfo[0] != null) {
                foreach ($this->_errorInfo as $info) {
                    $info = serialize($info);
                    self::$_errorMessageMatrix[$errorCode] .= ' '.$info;
                }
            }
        }

        // return costructed error message
        return self::$_errorMessageMatrix[$errorCode];
    }

    /**
     * returns the error-code
     *
     * This method is intend to return the error-code.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The error-code
     * @access public
     */
    public function getErrorCode()
    {
        return $this->_errorCode;
    }

    /**
     * returns the error-message
     *
     * This method is intend to return the error-message.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The error-message
     * @access public
     */
    public function getErrorMessage()
    {
        return $this->_errorMessage;
    }

    /**
     * returns the I18N-error-identifier
     *
     * This method is intend to return the I18N-error-identifier.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The I18N-error-identifier
     * @access public
     */
    public function getI18NIdentifier()
    {
        return $this->_errorI18N;
    }

    /**
     * returns the additional info
     *
     * This method is intend to return the additional info.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The additional info
     * @access public
     */
    public function getErrorInfo()
    {
        return $this->_errorInfo;
    }
}

?>
