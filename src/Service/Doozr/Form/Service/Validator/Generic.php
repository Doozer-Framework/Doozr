<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service.
 *
 * Generic.php - Generic Validator. Validates Form(s) & Component(s).
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
require_once DOOZR_DOCUMENT_ROOT.'Service/Doozr/Form/Service/Validator/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT.'Service/Doozr/Form/Service/Validator/Interface.php';

/**
 * Doozr - Form - Service.
 *
 * Generic.php - Generic Validator. Validates Form(s) & Component(s).
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
class Doozr_Form_Service_Validator_Generic extends Doozr_Form_Service_Validator_Abstract
    implements
    Doozr_Form_Service_Validator_Interface
{
    /**
     * Preferred order for validations to increase performance when validating (we use this order to validation values).
     *
     * @var array
     * @static
     */
    protected static $typeOrderMatrix = [
        Doozr_Form_Service_Validator_Constant::VALIDATE_IMPACT            => 0,
        Doozr_Form_Service_Validator_Constant::VALIDATE_REQUIRED          => 1,
        Doozr_Form_Service_Validator_Constant::VALIDATE_NOTEMPTY          => 2,
        Doozr_Form_Service_Validator_Constant::VALIDATE_EMPTY             => 3,
        Doozr_Form_Service_Validator_Constant::VALIDATE_VALUE             => 4,
        Doozr_Form_Service_Validator_Constant::VALIDATE_ALPHABETIC        => 5,
        Doozr_Form_Service_Validator_Constant::VALIDATE_MINLENGTH         => 6,
        Doozr_Form_Service_Validator_Constant::VALIDATE_MAXLENGTH         => 7,
        Doozr_Form_Service_Validator_Constant::VALIDATE_EMAIL             => 8,
        Doozr_Form_Service_Validator_Constant::VALIDATE_EMAILAUTH         => 9,
        Doozr_Form_Service_Validator_Constant::VALIDATE_NUMERIC           => 10,
        Doozr_Form_Service_Validator_Constant::VALIDATE_NOTNULL           => 11,
        Doozr_Form_Service_Validator_Constant::VALIDATE_IP                => 12,
        Doozr_Form_Service_Validator_Constant::VALIDATE_LOWERCASE         => 13,
        Doozr_Form_Service_Validator_Constant::VALIDATE_UPPERCASE         => 14,
        Doozr_Form_Service_Validator_Constant::VALIDATE_POSTCODE          => 15,
        Doozr_Form_Service_Validator_Constant::VALIDATE_USTID             => 16,
        Doozr_Form_Service_Validator_Constant::VALIDATE_BOOLEAN           => 17,
        Doozr_Form_Service_Validator_Constant::VALIDATE_DOUBLE            => 18,
        Doozr_Form_Service_Validator_Constant::VALIDATE_INTEGER           => 19,
        Doozr_Form_Service_Validator_Constant::VALIDATE_STRING            => 20,
        Doozr_Form_Service_Validator_Constant::VALIDATE_LINK              => 21,
        Doozr_Form_Service_Validator_Constant::VALIDATE_INVALID           => 22,
        Doozr_Form_Service_Validator_Constant::VALIDATE_REGULAREXPRESSION => 23,
        Doozr_Form_Service_Validator_Constant::VALIDATE_FILETYPE          => 24,
        Doozr_Form_Service_Validator_Constant::VALIDATE_FILESIZEMIN       => 25,
        Doozr_Form_Service_Validator_Constant::VALIDATE_FILESIZEMAX       => 26,
        Doozr_Form_Service_Validator_Constant::VALIDATE_FILEEXTENSION     => 27,
    ];

    /**
     * holds allowed chars by valid-type
     * e.g. for help text and/or error-message(s).
     *
     * @var array
     * @static
     */
    /*private static $_charlist = [
        'required'     => '',
        'empty'        => '',
        'notempty'     => 'any',
        'notnull'      => 'not 0 and not NULL',
        'alphabetic'   => 'a-z A-Z',
        'numeric'      => '0-9',
        'alphanumeric' => 'a-z A-Z 0-9',
        'lowercase'    => 'a-z',
        'boolean'      => 'TRUE|FALSE',
    ];*/

    /**
     * The current processed untouched list of validation-types
     * in its original order.
     *
     * @var array
     */
    protected $currentValidationtypes = [];


    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * validates a given set of information (value, type, componenttye [e.g. checkbox, radio, text]).
     *
     * This method is intend to validation a given set of information.
     *
     * @param mixed $value           The value to check ...
     * @param mixed $validationTypes ... against this validation-types
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array|bool TRUE if given information is valid, otherwise error as array
     */
    public function validation($value, $validationTypes = Doozr_Form_Service_Validator_Constant::VALIDATE_REQUIRED)
    {
        // We assume a valid result
        $valid = true;

        // store validation types // e.g. to keep existing "value" => [] validations
        $this->currentValidationtypes = $validationTypes;

        // if not of type array make it one
        if (!is_array($this->currentValidationtypes)) {
            $this->currentValidationtypes = [
                $this->currentValidationtypes => true,
            ];
        }

        // order the validation given
        $validationTypes = $this->sortValidationtypes($this->currentValidationtypes);

        // iterate over given validation methods
        foreach ($validationTypes as $validationType) {
            // store the validation
            $validation = $validationType;

            // check for validation type based on array (like "value")
            if (is_array($validationType)) {
                // in this case => prepare the data to be valid parameters
                $validationType   = array_keys($validationType);
                $validationType   = $validationType[0];
                $validationValues = $validation[$validationType];
            } else {
                $validationValues = null;
            }

            // construct internal methodName by type
            $validationMethod = $this->getValidationMethodNameByType($validationType);

            // call validation method with value as parameter
            if (!$this->{$validationMethod}($value, $this->currentValidationtypes[$validationType])) {

                // return new error object
                $valid = [
                    'error' => $validationType,
                    'value' => $value,
                    'info'  => $this->currentValidationtypes[$validationType],
                ];

                break;
            }
        }

        // return final result
        return $valid;
    }

    /**
     * Validates if given value is the expected value.
     *
     * @param mixed $value       The value to validation
     * @param array $validValues An array of valid values to check given value against
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if fulfilled requirements, otherwise FALSE
     */
    public function validateValue($value, array $validValues)
    {
        $result = true;

        if (is_array($value)) {
            $values = $value;
            foreach ($values as $value) {
                $result = $result && in_array($value, $validValues);
            }
        } else {
            $result = in_array($value, $validValues);
        }

        return $result;
    }

    /**
     * Validates that passed file upload has extension exactly
     * matching one within passed valid values for that.
     *
     * @param array $value       A file upload array structure (object) like $_SESSION
     * @param array $validValues The valid values to validation against
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if could be validated, otherwise FALSE
     */
    public function validateFileExtension($value, array $validValues)
    {
        if (isset($validValues[0]) && is_array($validValues[0])) {
            $validValues = $validValues[0];
        }

        $partials  = explode('.', $value['name']);
        $extension = array_pop(
            $partials
        );

        return in_array($extension, $validValues);
    }

    /**
     * ...
     *
     * @param array $value       A file upload array structure (object) like $_SESSION
     * @param array $validValues The valid values to validation against
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if could be validatet, otherwise FALSE
     */
    public function validateRegularexpression($value, array $validValues)
    {
        if (isset($validValues[0]) && is_array($validValues[0])) {
            $validValues = $validValues[0];
        }

        return preg_match($validValues[0], $value) > 0;
    }

    /**
     * Validates that passed file upload has mime/type exactly
     * matching one within passed valid values for that.
     *
     * @param array $value       A file upload array structure (object) like $_SESSION
     * @param array $validValues The valid values to validation against
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if could be validated, otherwise FALSE
     */
    public function validateFileType($value, array $validValues)
    {
        if (isset($validValues[0]) && is_array($validValues[0])) {
            $validValues = $validValues[0];
        }

        $type = (true === isset($value['type'])) ? $value['type'] : null;

        return in_array($type, $validValues);
    }

    /**
     * Validates that the passed file-upload's size is at minimum the passed
     * value in bytes.
     *
     * @param array $value       A file upload array structure (object) like $_SESSION
     * @param array $validValues The valid values to validation against
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if could be validatet, otherwise FALSE
     */
    public function validateFileSizeMin($value, array $validValues)
    {
        if (isset($validValues[0]) && is_array($validValues[0])) {
            $validValues = $validValues[0];
        }

        $validLimit = $validValues[0];

        $size = $value['size'];

        return $size >= $validLimit;
    }

    /**
     * Validates that the passed file-upload's size is at maximum the passed
     * value in bytes.
     *
     * @param array $value       A file upload array structure (object) like $_SESSION
     * @param array $validValues The valid values to validation against
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if could be validatet, otherwise FALSE
     */
    public function validateFileSizeMax($value, array $validValues)
    {
        if (isset($validValues[0]) && is_array($validValues[0])) {
            $validValues = $validValues[0];
        }

        $validLimit = $validValues[0];

        $size = $value['size'];

        return $size <= $validLimit;
    }

    /**
     * Validates if given value fulfill the requirement "required".
     *
     * @param mixed $value The value to validation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if fulfilled requirements, otherwise FALSE
     */
    public function validateRequired($value)
    {
        // Check for file!
        if (true === is_array($value) && true === array_key_exists('tmp_name', $value)) {
            // At least it was uploaded its valid in meaning of "required" <= NOT EMPTY
            $valid = ($value['error'] < UPLOAD_ERR_PARTIAL);
        } else {
            // Plain value ...
            $valid = (!is_null($value) && (is_array($value) || is_object($value) || (strlen($value) > 0)));
        }

        return $valid;
    }

    /**
     * Validates if given value fulfill the requirement "notnull".
     *
     * @param mixed $value The value to validation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if fulfilled requirements, otherwise FALSE
     */
    public function validateNotnull($value)
    {
        return ($value) && (!empty($value));
    }

    /**
     * Validates if given value fulfill the requirement "notempty".
     *
     * @param mixed $value The value to validation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if fulfilled requirements, otherwise FALSE
     */
    public function validateNotempty($value)
    {
        settype($value, 'string');

        return strlen($value);
    }

    /**
     * validates if given value fulfill the requirement "empty".
     *
     * This method is intend to validation if given value fulfill the requirement "empty".
     *
     * @param mixed $value The value to validation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if fulfilled requirements, otherwise FALSE
     */
    public function validateEmpty($value)
    {
        return !$this->validateNotempty($value);
    }

    /**
     * Validates if given value fulfill the requirement "alphabetic".
     *
     * @param mixed $value     The value to validation
     * @param int   $charCount The character limitation (optional)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if fulfilled requirements, otherwise FALSE
     */
    public function validateAlphabetic($value, $charCount = null)
    {
        if (is_array($charCount)) {
            $charCount = $charCount[0];
        }

        // check for character limitation
        if ($charCount !== null) {
            $pattern = '/^[a-zA-Z???????]{0,'.$charCount.'}$/';
        } else {
            $pattern = '/^[A-Za-z???????]+$/';
        }

        // validation and return
        return (bool) preg_match($pattern, $value);
    }

    /**
     * Validates if passed value is an invalid one.
     *
     * @param mixed $value         The value to validation against the invalid values
     * @param array $invalidValues The invalid values to check $value against
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if fulfilled requirements, otherwise FALSE
     */
    public function validateInvalid($value, array $invalidValues)
    {
        return !in_array($value, $invalidValues);
    }

    /**
     * Validates if passed values type is boolean.
     *
     * @param mixed $value The value to validation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if fulfilled requirements, otherwise FALSE
     */
    public function validateBoolean($value)
    {
        return (strtoupper($value) === 'TRUE') || (strtoupper($value) === 'FALSE');
    }

    /**
     * validates if given value fulfill the requirement "double".
     *
     * This method is intend to validation if given value fulfill the requirement "double".
     *
     * @param mixed $value The value to validation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if fulfilled requirements, otherwise FALSE
     */
    public function validateDouble($value)
    {
        $pattern = '/[0-9]*\.{1}[0-9]+/';

        return (bool) preg_match($pattern, $value);
    }

    /**
     * validates if given value fulfill the requirement "integer".
     *
     * This method is intend to validation if given value fulfill the requirement "integer".
     *
     * @param mixed $value The value to validation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if fulfilled requirements, otherwise FALSE
     */
    public function validateInteger($value)
    {
        $pattern = '/^\d+$/';

        return (bool) preg_match($pattern, $value) && (substr($value, 0, 1) != 0);
    }

    /**
     * Returns TRUE if passed value is a string, otherwise FALSE.
     *
     * @param mixed $value The value to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool
     */
    public function validateString($value)
    {
        return is_string($value);
    }

    /**
     * Validates if passed value is the same as the value in a linked component.
     *
     * @param mixed $value  The value to validation against the invalid values
     * @param array $values The invalid values to check $value against
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if fulfilled requirements, otherwise FALSE
     */
    public function validateLink($value, array $values)
    {
        // just a poc!!!!!!! dont't use in the wild
        return $value == $_POST->{$values[0]};
    }

    /**
     * validates if given value fulfill the requirement "impact".
     *
     * This method is intend to validation if given value fulfill the requirement "impact".
     *
     * @param mixed $value The value to validation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if fulfilled requirements, otherwise FALSE
     */
    public function validateImpact($value)
    {
        //
        pre($value);
        pred('and how should we get the impact now?');
    }

    /**
     * validates if given value fulfill the requirement "minlength".
     *
     * This method is intend to validation if given value fulfill the requirement "minlength".
     *
     * @param mixed $value       The value to validation
     * @param array $validValues An array of valid values to check given value against
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if fulfilled requirements, otherwise FALSE
     */
    public function validateMinlength($value, array $validValues)
    {
        return strlen($value) >= $validValues[0];
    }

    /**
     * Validates if given value fulfill the requirement "maxlength".
     *
     * @param mixed $value       The value to validation
     * @param array $validValues An array of valid values to check given value against
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if fulfilled requirements, otherwise FALSE
     */
    public function validateMaxlength($value, array $validValues)
    {
        return strlen($value) <= $validValues[0];
    }

    /**
     * Validate if given value fulfill the requirement "ustid".
     * http://de.wikipedia.org/wiki/Umsatzsteuer-Identifikationsnummer.
     *
     * @param mixed $value               The value to validation
     * @param array $additionalParameter Additional parameter to use for validation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if fulfilled requirements, otherwise FALSE
     *
     * @throws Doozr_Form_Service_Exception
     */
    public function validateUstid($value, $additionalParameter)
    {
        // if no parameter given (the parameter is true by internal mapping)
        if ($additionalParameter === true) {
            // default = DE (zipcode)
            $countrycode = 'DE';
        } else {
            // get correct lowercase countrycode for validation
            $countrycode = strtoupper($additionalParameter[0]);
        }

        // regular expressions for postcode(s)
        $pattern = [
            'DE' => '^(DE)[0-9]{9,9}$',  // GERMANY
        ];

        if (!isset($pattern[$countrycode])) {
            throw new Doozr_Form_Service_Exception(
                'Unknown country-code "'.$countrycode.'" for USTID-validation!'
            );
        }

        // validation and return
        return (bool) preg_match('/'.$pattern[$countrycode].'/', $value);
    }

    /**
     * Validates if given value fulfill the requirement "is postcode".
     *
     * @param mixed $value               The value to validation
     * @param array $additionalParameter Additional parameter to use for validation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if fulfilled requirements, otherwise FALSE
     *
     * @throws Doozr_Form_Service_Exception
     */
    public function validatePostcode($value, $additionalParameter)
    {
        // if no parameter given (the parameter is true by internal mapping)
        if ($additionalParameter === true) {
            // default = DE (zipcode)
            $countrycode = 'DE';
        } else {
            // get correct lowercase countrycode for validation
            $countrycode = strtoupper($additionalParameter[0]);
        }

        // regular expressions for postcode(s)
        $pattern = [
            'DE' => '^[0-9]{5,5}$',                                                      // GERMANY
            'AT' => '^[0-9]{4,4}$',                                                      // AUSTRIA
            'AU' => '^[2-9][0-9]{2,3}$',                                                 // AUSTRALIA
            'CA' => '^[a-zA-Z].[0-9].[a-zA-Z].\s[0-9].[a-zA-Z].[0-9].',                  // CANADA
            'EE' => '^[0-9]{5,5}$',                                                      // ESTONIA
            'NL' => '^[0-9]{4,4}\s[a-zA-Z]{2,2}$',                                       // NETHERLANDS
            'IT' => '^[0-9]{5,5}$',                                                      // ITALY
            'PT' => '^[0-9]{4,4}-[0-9]{3,3}$',                                           // PORTUGAL
            'SE' => '^[0-9]{3,3}\s[0-9]{2,2}$',                                          // SWEDEN
            'UK' => '^([A-Z]{1,2}[0-9]{1}[0-9A-Z]{0,1}) ?([0-9]{1}[A-Z]{1,2})$',         // UNITED-KINGDOM (England)
            'US' => '^[0-9]{5,5}[\-]{0,1}[0-9]{4,4}',                                    // UNITED-STATES (USA)
        ];

        if (!isset($pattern[$countrycode])) {
            throw new Doozr_Form_Service_Exception(
                'Unknown country-code "'.$countrycode.'" for postcode-validation!'
            );
        }

        // validation and return
        return (bool) preg_match('/'.$pattern[$countrycode].'/', $value);
    }

    /**
     * Validates if given value fulfill the requirement "uppercase".
     *
     * @param mixed $value The value to validation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if fulfilled requirements, otherwise FALSE
     */
    public function validateUppercase($value)
    {
        // check pattern
        $pattern = '/^[A-Z]*$/';

        // validation and return
        return (bool) preg_match($pattern, $value);
    }

    /**
     * Validates if given value fulfill the requirement "lowercase".
     *
     * @param mixed $value The value to validation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if fulfilled requirements, otherwise FALSE
     */
    public function validateLowercase($value)
    {
        // check pattern
        $pattern = '/^[a-z]*$/';

        // validation and return
        return (bool) preg_match($pattern, $value);
    }

    /**
     * Validates if given value fulfill the requirement "ip".
     *
     * @param mixed $value The value to validation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if fulfilled requirements, otherwise FALSE
     */
    public function validateIp($value)
    {
        // check pattern
        $pattern = '/^([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})$/i';

        // validation and return
        return (bool) preg_match($pattern, $value);
    }

    /**
     * Validates if given value fulfill the requirement "numeric".
     *
     * @param mixed $value The value to validation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if fulfilled requirements, otherwise FALSE
     */
    public function validateNumeric($value)
    {
        // check pattern
        $pattern = '/^[0-9]+$/';

        // validation and return
        return (bool) preg_match($pattern, $value);
    }

    /**
     * Validates if given value fulfill the requirement "email".
     *
     * @param mixed $value The value to validation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if fulfilled requirements, otherwise FALSE
     */
    public function validateEmail($value)
    {
        return (bool) preg_match("/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i", $value);
    }

    /**
     * Validates if given value fulfill the requirement "emailauth".
     *
     * @param mixed $value       The value to validation
     * @param array $validValues An array used for passing custom configuration to this method
     *                           required only in this special case. not nice but functional.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if fulfilled requirements, otherwise FALSE
     */
    public function validateEmailauth($value, $validValues)
    {
        // assume invalid email
        $result = false;

        // 1st check if email has valid syntax - to ensure that live-validation does not fail due invalid input
        if ($this->validateEmail($value)) {

            // timeout in seconds. Some servers deliberately wait a while (tarpitting)
            // http://en.wikipedia.org/wiki/Tarpit_%28networking%29
            $serverTimeout = 180;

            // the hostname (ip) we use for HELO (smtp-auth)
            $validationHeloHost = $validValues[0]['hostname'];

            // the email-address used for testing
            $validationProbeAddress = 'no-reply@'.$validationHeloHost;

            // split email into smaller pieces
            preg_match(
                '/^([a-zA-Z0-9\._\+-]+)\@((\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,7}|[0-9]{1,3})(\]?))$/',
                $value,
                $matches
            );

            // give pieces a name
            $user   = $matches[1];
            $domain = $matches[2];

            // holds the found mx-hosts
            $mxHosts = [];

            // holds the weight of the found mx-hosts
            $mxweight = [];

            // Check availability of DNS MX records and construct array of available mailservers
            if (getmxrr($domain, $mxHosts, $mxweight)) {
                asort($mxweight);
                //$mxHosts = array_keys($mxweight);
            } elseif (checkdnsrr($domain, 'A')) {
                // mail ... ??!?
                $mxweight[0] = 5;
                $mxHosts[0]  = gethostbyname($domain);
            }

            // store count of mailers
            $mxHostCount = count($mxHosts);

            // if mx-host found continue ...
            if ($mxHostCount > 0) {
                foreach ($mxweight as $key => $value) {
                    pre('Checking server '.$mxHosts[$key].'...');
                    $errorNumber  = 0;
                    $errorMessage = 0;

                    // try to open socket
                    $sock = @fsockopen($mxHosts[$key], 25, $errorNumber, $errorMessage, $serverTimeout);

                    // try to open up socket
                    if ($sock) {
                        $response = fgets($sock);

                        // log
                        pre('Opening up socket to '.$mxHosts[$key].'... success!');

                        // set timeout in seconds to 30
                        stream_set_timeout($sock, 30);

                        $meta = stream_get_meta_data($sock);

                        // log
                        pre($mxHosts[$key].' replied with: '.$response);

                        // array holding the SMTP-commands in correct order
                        $smtpCommands = [
                            "HELO $validationHeloHost",
                            "MAIL FROM: <$validationProbeAddress>",
                            "RCPT TO: <$value>",
                            'QUIT',
                        ];

                        // hard error on connect -> break out // Error means 'any reply that does not start with 2xx'
                        // List of SMTP-Status-Codes: http://www.elektronik-kompendium.de/sites/net/0903081.htm
                        if (!$meta['timed_out'] && !preg_match('/^2\d\d[ -]/', $response)) {
                            // log
                            pre('Error: '.$mxHosts[$key].' said: '.$response);
                            break;
                        }

                        // iterate over commands and send them
                        foreach ($smtpCommands as $smtpCommand) {
                            $before = microtime(true);
                            fputs($sock, "$smtpCommand\r\n");
                            $response = fgets($sock, 4096);
                            $t        = 1000 * (microtime(true) - $before);

                            // log
                            pre(htmlentities('$smtpCommand'.PHP_EOL.'$response').'('.sprintf('%.2f', $t).' ms)');

                            if (!$meta['timed_out'] && preg_match('/^5\d\d[ -]/', $response)) {
                                // log
                                pre('Unverified address: '.$mxHosts[$key].' said: '.$response);
                                break 2;
                            }
                        }

                        // close socket
                        fclose($sock);

                        // log
                        pre('Succesful communicated with '.$mxHosts[$key].', no hard errors, assuming OK');

                        // successful authenticated
                        $result = true;

                        // exit here
                        break;
                    }
                }
            } elseif ($mxHostCount <= 0) {
                // log
                pre('No valid MX-Record (DNS-entry) found to validation email: '.$value);

                // no success
                $result = false;
            }
        }

        // if not validatEmail (syntax-check) is true - validateEmailauth is false
        return $result;
    }

    /**
     * Returns the  the validation-type matrix.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The validation-types as ordered-by-priority array
     * @static
     */
    public static function getValidationTypeMatrix()
    {
        return self::$typeOrderMatrix;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Tools & Helper
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * returns the name of the validation-method by given validationtype.
     *
     * This method is intend to return the name of the validation-method by given validationtype.
     *
     * @param string $validationtype The validationtype to return methodName for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The constructed MethodName
     */
    protected function getValidationMethodNameByType($validationtype)
    {
        return 'validate'.ucfirst($validationtype);
    }

    /**
     * Orders the given set of validation-types.
     *
     * @param array $validationTypes The validation-types to order
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The ordered list of validation-types
     */
    protected function sortValidationtypes(array $validationTypes)
    {
        // prepare validationtypes for ordering (remove not orderable items)
        $splittedValidationtypes = $this->splitForOrder($validationTypes);

        // set sortabel items to sortable for further processing
        $sortable = $splittedValidationtypes['sortable'];

        // extract just the keys from given set of validation(s)
        $sortable = array_keys($sortable);

        // sort the components by our preferred order to speed up validation
        usort($sortable, ['self', '_compareValidationtypes']);

        // add previously removed items
        foreach ($splittedValidationtypes['nonsortable'] as $validationtype => $value) {
            $sortable[] = [
                $validationtype => $value,
            ];
        }

        // return ordered list
        return $sortable;
    }

    /**
     * Comparemethod for _sortValidationtypes().
     *
     * @param mixed $a The value to compare ...
     * @param mixed $b ... against this value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if given information is valid, otherwise FALSE if invalid
     * @static
     *
     * @throws Doozr_Form_Service_Exception
     */
    private static function _compareValidationtypes($a, $b)
    {
        // same components?
        if ($a == $b) {
            return 0;
        }

        // check for nonexistent index
        if (!isset(self::$typeOrderMatrix[$a])) {
            throw new Doozr_Form_Service_Exception(
                __METHOD__.': nonexistent index for self::$_typeOrderMatrix found: '.$a
            );
        } elseif (!isset(self::$typeOrderMatrix[$b])) {
            throw new Doozr_Form_Service_Exception(
                __METHOD__.': nonexistent index for self::$_typeOrderMatrix found: '.$b
            );
        }

        // return ordered
        return (self::$typeOrderMatrix[$a] < self::$typeOrderMatrix[$b]) ? -1 : 1;
    }

    /**
     * Splits the validation-types into sortable and non-sortable parts.
     *
     * @param array $validationTypes The validation-types to split
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The splitted list of validation-types
     */
    protected function splitForOrder(array $validationTypes)
    {
        // pre-assume every item is sortable
        $sortable = $validationTypes;

        // hols temporary removed items
        $nonsortable = [];

        // types to be removed before ordering
        $removeTypes = [
            Doozr_Form_Service_Validator_Constant::VALIDATE_VALUE,
            Doozr_Form_Service_Validator_Constant::VALIDATE_MINLENGTH,
            Doozr_Form_Service_Validator_Constant::VALIDATE_MAXLENGTH,
            Doozr_Form_Service_Validator_Constant::VALIDATE_POSTCODE,
            Doozr_Form_Service_Validator_Constant::VALIDATE_USTID,
        ];

        // iterate over non-sortable validation-types and split
        foreach ($removeTypes as $removeType) {
            if (isset($sortable[$removeType])) {
                $nonsortable[$removeType] = $sortable[$removeType];
                // remove for sorting
                unset($sortable[$removeType]);
            }
        }

        // return prepared items
        return [
            'sortable'    => $sortable,
            'nonsortable' => $nonsortable,
        ];
    }
}
