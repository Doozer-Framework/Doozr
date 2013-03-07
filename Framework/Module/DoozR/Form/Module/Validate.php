<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Module - Form - Validate
 *
 * Validate.php - Validation base class for validating basic types and
 * as a base for applications internal validation
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
 * @package    DoozR_Module
 * @subpackage DoozR_Module_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Class/Singleton.php';

/**
 * DoozR - Module - Form - Validate
 *
 * Validate-Class for validating different types of data
 *
 * @category   DoozR
 * @package    DoozR_Module
 * @subpackage DoozR_Module_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Form_Module_Validate extends DoozR_Base_Class_Singleton
{
    /**
     * valid type for required elements/fields
     * "required" means that this element/field must be submitted
     * no matter if it is empty or value is an empty string. it
     * just need to be submitted.
     *
     * if you are looking for a validation that check for a given
     * value - with not accepting empty submissions:
     * @see VALIDATIONTYPE_NOTEMPTY
     *
     * @var string
     * @access public
     */
    const VALIDATIONTYPE_REQUIRED = 'required';

    /**
     * valid type for not-empty elements/fields
     * "notempty" means that this element/field must be submitted
     * with a value. at minimum one char must be submitted.
     * empty submissions are invalid.
     *
     * if you are looking for a validation that just checks if a field
     * was submitted:
     * @see VALIDATIONTYPE_REQUIRED
     *
     * @var string
     * @access public
     */
    const VALIDATIONTYPE_NOTEMPTY = 'notempty';

    /**
     * valid type for empty elements/fields
     * "empty" means that this element/field must be submitted
     * without a value. The counterpart to "notempty"
     *
     * @var string
     * @access public
     */
    const VALIDATIONTYPE_EMPTY = 'empty';

    /**
     * valid type for not-empty elements/fields
     * "notnull" means that this element/field must be submitted
     * with a value. A value != 0 and != NULL !!!
     * at minimum one char must be submitted.
     * empty submissions are invalid.
     *
     * @var string
     * @access public
     */
    const VALIDATIONTYPE_NOTNULL = 'notnull';

    /**
     * valid type for alphabetic elements/fields
     * "alphabetic" means that this element/field must be submitted
     * with a value. at minimum one char must be submitted.
     * empty submissions are invalid.
     *
     * if you are looking for a validation that just checks if a field
     * was submitted:
     * @see VALIDATIONTYPE_REQUIRED
     *
     * @var string
     * @access public
     */
    const VALIDATIONTYPE_ALPHABETIC = 'alphabetic';

    /**
     * valid type for numeric elements/fields
     * "numeric" means that this element/field must be submitted
     * with a value. at minimum one char must be submitted.
     * empty submissions are invalid.
     *
     * @var string
     * @access public
     */
    const VALIDATIONTYPE_NUMERIC = 'numeric';

    /**
     * valid type for boolean elements/fields
     * "boolean" means that this element/field must be submitted
     * with a value => TRUE or FALSE
     *
     * @var string
     * @access public
     */
    const VALIDATIONTYPE_BOOLEAN = 'boolean';

    /**
     * valid type for boolean elements/fields
     * "double" means that this element/field must be submitted
     * with a value e.g. 1.0 1.1 ...
     *
     * @var string
     * @access public
     */
    const VALIDATIONTYPE_DOUBLE = 'double';

    /**
     * valid type for boolean elements/fields
     * "integer" means that this element/field must be submitted
     * with a value e.g. 1 or 2 or 234 or 43547.
     *
     * @var string
     * @access public
     */
    const VALIDATIONTYPE_INTEGER = 'integer';

    /**
     * Validation type for string
     *
     * @var string
     * @access public
     */
    const VALIDATIONTYPE_STRING = 'string';

    /**
     * Validation type for linked fields
     *
     * @var string
     * @access public
     */
    const VALIDATIONTYPE_LINK = 'link';

    /**
     * valid type for exact-value-match elements/fields
     * "value" means that this element/field must exactly (===) match
     * the stored value.
     *
     * @var string
     * @access public
     */
    const VALIDATIONTYPE_VALUE = 'value';

    /**
     * valid type for impact validation (impact is detected by PHP-IDS if enabled)
     * we only check for impact if PHP-IDS is enabled in config. otherwise we have no
     * base for validation.
     *
     * @var string
     * @access public
     */
    const VALIDATIONTYPE_IMPACT = 'impact';

    /**
     * valid-type for minimal length required
     *
     * @var string
     * @access public
     */
    const VALIDATIONTYPE_MINLENGTH = 'minlength';

    /**
     * valid-type for maximal length allowed
     *
     * @var string
     * @access public
     */
    const VALIDATIONTYPE_MAXLENGTH = 'maxlength';

    /**
     * valid-type for email (check syntax)
     *
     * @var string
     * @access public
     */
    const VALIDATIONTYPE_EMAIL = 'email';

    /**
     * valid-type for email (live deliver-to-postbox check)
     *
     * @var string
     * @access public
     */
    const VALIDATIONTYPE_EMAILAUTH = 'emailauth';

    /**
     * valid-type for IP-Addresses
     *
     * @var string
     * @access public
     */
    const VALIDATIONTYPE_IP = 'ip';

    /**
     * valid-type for lowercase input (a-z)
     *
     * @var string
     * @access public
     */
    const VALIDATIONTYPE_LOWERCASE = 'lowercase';

    /**
     * valid-type for uppercase input (A-Z)
     *
     * @var string
     * @access public
     */
    const VALIDATIONTYPE_UPPERCASE = 'uppercase';

    /**
     * valid-type for post-/zipcode input
     *
     * @var string
     * @access public
     */
    const VALIDATIONTYPE_POSTCODE = 'postcode';

    /**
     * valid-type for USTID input
     *
     * @var string
     * @access public
     */
    const VALIDATIONTYPE_USTID = 'ustid';

    /**
     * valid-type for invalid values input
     *
     * @var string
     * @access public
     */
    const VALIDATIONTYPE_INVALID = 'invalid';

    /**
     * holds the preferred order for validations
     * to increase perfomance / speed up validating we use this
     * order to validate values
     *
     * @var array
     * @access private
     * @static
     */
    private static $_typeOrderMatrix = array(
        self::VALIDATIONTYPE_IMPACT      =>  0,
        self::VALIDATIONTYPE_REQUIRED    =>  1,
        self::VALIDATIONTYPE_NOTEMPTY    =>  2,
        self::VALIDATIONTYPE_ALPHABETIC  =>  3,
        self::VALIDATIONTYPE_VALUE       =>  4,
        self::VALIDATIONTYPE_MINLENGTH   =>  5,
        self::VALIDATIONTYPE_MAXLENGTH   =>  6,
        self::VALIDATIONTYPE_EMAIL       =>  7,
        self::VALIDATIONTYPE_EMAILAUTH   =>  8,
        self::VALIDATIONTYPE_NUMERIC     =>  9,
        self::VALIDATIONTYPE_NOTNULL     => 10,
        self::VALIDATIONTYPE_IP          => 11,
        self::VALIDATIONTYPE_LOWERCASE   => 12,
        self::VALIDATIONTYPE_UPPERCASE   => 13,
        self::VALIDATIONTYPE_POSTCODE    => 14,
        self::VALIDATIONTYPE_USTID       => 15,
        self::VALIDATIONTYPE_EMPTY       => 16,
        self::VALIDATIONTYPE_BOOLEAN     => 17,
        self::VALIDATIONTYPE_DOUBLE      => 18,
        self::VALIDATIONTYPE_INTEGER     => 19,
        self::VALIDATIONTYPE_STRING      => 20,
        self::VALIDATIONTYPE_LINK        => 21,
        self::VALIDATIONTYPE_INVALID     => 22
    );

    /**
     * holds allowed chars by valid-type
     * e.g. for help text and/or error-message(s)
     *
     * @var array
     * @access private
     * @static
     */
    private static $_charlist = array(
        'required'     => '',
        'empty'        => '',
        'notempty'     => 'any',
        'notnull'      => 'not 0 and not NULL',
        'alphabetic'   => 'a-z A-Z',
        'numeric'      => '0-9',
        'alphanumeric' => 'a-z A-Z 0-9',
        'lowercase'    => 'a-z',
        'boolean'      => 'TRUE|FALSE'
    );

    /**
     * holds the current processed untouched list of validation-types
     * in its original order
     *
     * @var array
     * @access private
     * @static
     */
    private static $_currentValidationtypes = array();


    /*******************************************************************************************************************
     * // BEGIN PUBLIC STATIC INTERFACES
     ******************************************************************************************************************/

    /**
     * validates a given set of information (value, type, elementtye [e.g. checkbox, radio, text])
     *
     * This method is intend to validate a given set of information.
     *
     * @param mixed $value           The value to check ...
     * @param mixed $validationTypes ... against this validation-types
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if given information is valid, otherwise FALSE if invalid
     * @access public
     * @static
     */
    public static function validate($value, $validationTypes = self::VALIDATIONTYPE_REQUIRED)
    {
        // we assume a valid result
        $valid = true;

        // we add impact-check if not already set
        /*
        if (!isset($validationTypes['impact'])) {
            $validationTypes['impact'] = true;
        }
        */

        // store validation types // e.g. to keep existing "value" => array() validations
        self::$_currentValidationtypes = $validationTypes;

        // if not of type array make it one
        if (!is_array(self::$_currentValidationtypes)) {
            self::$_currentValidationtypes = array(
                self::$_currentValidationtypes => true
            );
        }

        // order the validation given
        $validationTypes = self::_sortValidationtypes(self::$_currentValidationtypes);

        // iterate over given validationmethods
        foreach ($validationTypes as $validationtype) {
            // store the validation
            $validation = $validationtype;

            // check for validation type based on array (like "value")
            if (is_array($validationtype)) {
                // in this case => prepare the data to be valid parameters
                $validationtype = array_keys($validationtype);
                $validationtype = $validationtype[0];
                $validationValues = $validation[$validationtype];
            } else {
                $validationValues = null;
            }

            // construct internal methodname by type
            $validationMethod = self::_getValidationMethodnameByType($validationtype);

            // call validationmethod with value as parameter
            if (!self::$validationMethod($value, self::$_currentValidationtypes[$validationtype])) {

                // transform to "error" for better reading
                $error = $validationtype;

                // transform to "info" for better reading
                $info = self::$_currentValidationtypes[$validationtype];

                // return new error object
                $valid = new DoozR_Form_Module_Error($error, $value, $info);

                break;
            }
        }

        // return final result
        return $valid;
    }

    /*******************************************************************************************************************
     * \\ END PUBLIC STATIC INTERFACES
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN PUBLIC STATIC VALIDATION-METHODS
     ******************************************************************************************************************/

    /**
     * validates if given value fulfill the requirement "required"
     *
     * This method is intend to validate if given value fulfill the requirement "required".
     *
     * @param mixed $value The value to validate
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if fulfilled requirements, otherwise FALSE
     * @access public
     * @static
     */
    public static function validateRequired($value)
    {
        return (!is_null($value) && ((strlen($value) > 0) || is_array($value) || is_object($value)));
    }

    /**
     * validates if given value fulfill the requirement "notnull"
     *
     * This method is intend to validate if given value fulfill the requirement "notnull".
     *
     * @param mixed $value The value to validate
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if fulfilled requirements, otherwise FALSE
     * @access public
     * @static
     */
    public static function validateNotnull($value)
    {
        return ($value) && (!empty($value));
    }

    /**
     * validates if given value fulfill the requirement "notempty"
     *
     * This method is intend to validate if given value fulfill the requirement "notempty".
     *
     * @param mixed $value The value to validate
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if fulfilled requirements, otherwise FALSE
     * @access public
     * @static
     */
    public static function validateNotempty($value)
    {
        settype($value, 'string');
        return (strlen($value));
    }

    /**
     * validates if given value fulfill the requirement "empty"
     *
     * This method is intend to validate if given value fulfill the requirement "empty".
     *
     * @param mixed $value The value to validate
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if fulfilled requirements, otherwise FALSE
     * @access public
     * @static
     */
    public static function validateEmpty($value)
    {
        return (!self::validateNotempty($value));
    }

    /**
     * validates if given value fulfill the requirement "alphabetic"
     *
     * This method is intend to validate if given value fulfill the requirement "alphabetic".
     *
     * @param mixed   $value     The value to validate
     * @param integer $charCount The character limitation (optional)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if fulfilled requirements, otherwise FALSE
     * @access public
     * @static
     */
    public static function validateAlphabetic($value, $charCount = null)
    {
        // check for character limitation
        if ($charCount) {
            $pattern = "/^[a-zA-ZüÜöÖäÄß]{0,".$charCount."}$/";
        } else {
            $pattern = '/^[A-Za-zäÄöÖüÜß]+$/';
        }

        // validate and return
        return (bool)preg_match($pattern, $value);
    }

    /**
     * Validates if given value is the expected value.
     *
     * @param mixed $value       The value to validate
     * @param array $validValues An array of valid values to check given value against
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if fulfilled requirements, otherwise FALSE
     * @access public
     * @static
     */
    public static function validateValue($value, array $validValues)
    {
        $result = true;

        if (is_array($value)) {
            foreach ($value as $key => $value) {
                $result = $result && in_array($value, $validValues);
            }

        } else {
            $result = in_array($value, $validValues);
        }

        return $result;
    }

    /**
     * Validates if passed value is an invalid one.
     *
     * @param mixed $value         The value to validate against the invalid values
     * @param array $invalidValues The invalid values to check $value against
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if fulfilled requirements, otherwise FALSE
     * @access public
     * @static
     */
    public static function validateInvalid($value, array $invalidValues)
    {
        return (!in_array($value, $invalidValues));
    }

    /**
     * Validates if passed values type is boolean
     *
     * @param mixed $value The value to validate
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if fulfilled requirements, otherwise FALSE
     * @access public
     * @static
     */
    public static function validateBoolean($value)
    {
        return (strtoupper($value) === 'TRUE') || (strtoupper($value) === 'FALSE');
    }

    /**
     * validates if given value fulfill the requirement "double"
     *
     * This method is intend to validate if given value fulfill the requirement "double".
     *
     * @param mixed $value The value to validate
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if fulfilled requirements, otherwise FALSE
     * @access public
     * @static
     */
    public static function validateDouble($value)
    {
        $pattern = '/[0-9]*\.{1}[0-9]+/';
        return (bool)preg_match($pattern, $value);
    }

    /**
     * validates if given value fulfill the requirement "integer"
     *
     * This method is intend to validate if given value fulfill the requirement "integer".
     *
     * @param mixed $value The value to validate
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if fulfilled requirements, otherwise FALSE
     * @access public
     * @static
     */
    public static function validateInteger($value)
    {
        $pattern = '/^\d+$/';
        return (bool)preg_match($pattern, $value) && (substr($value, 0, 1) != 0);
    }

    /**
     * Returns TRUE if passed value is a string, otherwise FALSE
     *
     * @param mixed $value The value to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean
     * @static
     */
    public static function validateString($value)
    {
        return is_string($value);
    }

    /**
     * Validates if passed value is the same as the value in a linked element.
     *
     * @param mixed $value  The value to validate against the invalid values
     * @param array $values The invalid values to check $value against
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if fulfilled requirements, otherwise FALSE
     * @access public
     * @static
     */
    public static function validateLink($value, array $values)
    {
        // just a poc!!!!!!! dont't use in the wild
        return ($value == $_POST->{$values[0]});
    }

    /**
     * validates if given value fulfill the requirement "impact"
     *
     * This method is intend to validate if given value fulfill the requirement "impact".
     *
     * @param mixed $value The value to validate
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if fulfilled requirements, otherwise FALSE
     * @access public
     * @static
     */
    public static function validateImpact($value)
    {
        //
        pre($value);
        pred('and how should we get the impact now?');
    }

    /**
     * validates if given value fulfill the requirement "minlength"
     *
     * This method is intend to validate if given value fulfill the requirement "minlength".
     *
     * @param mixed $value       The value to validate
     * @param array $validValues An array of valid values to check given value against
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if fulfilled requirements, otherwise FALSE
     * @access public
     * @static
     */
    public static function validateMinlength($value, array $validValues)
    {
        return (strlen($value) >= $validValues[0]);
    }

    /**
     * validates if given value fulfill the requirement "maxlength"
     *
     * This method is intend to validate if given value fulfill the requirement "maxlength".
     *
     * @param mixed $value       The value to validate
     * @param array $validValues An array of valid values to check given value against
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if fulfilled requirements, otherwise FALSE
     * @access public
     * @static
     */
    public static function validateMaxlength($value, array $validValues)
    {
        return (strlen($value) <= $validValues[0]);
    }

    /**
     * validates if given value fulfill the requirement "email"
     *
     * This method is intend to validate if given value fulfill the requirement "email".
     *
     * @param mixed $value The value to validate
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if fulfilled requirements, otherwise FALSE
     * @access public
     * @static
     */
    public static function validateEmail($value)
    {
        return (bool)preg_match("/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i", $value);
    }

    /**
     * validates if given value fulfill the requirement "emailauth"
     *
     * This method is intend to validate if given value fulfill the requirement "emailauth".
     *
     * @param mixed $value       The value to validate
     * @param array $validValues An array used for passing custom configuration to this method
     *                           required only in this special case. not nice but functional.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if fulfilled requirements, otherwise FALSE
     * @access public
     * @static
     */
    public static function validateEmailauth($value, $validValues)
    {
        // assume invalid email
        $result = false;

        // 1st check if email has valid syntax - to ensure that live-validation does not fail due invalid input
        if (self::validateEmail($value)) {

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
            $user = $matches[1];
            $domain = $matches[2];

            // holds the found mx-hosts
            $mxHosts = array();

            // holds the weight of the found mx-hosts
            $mxweight = array();

            // Check availability of DNS MX records and construct array of available mailservers
            if (getmxrr($domain, $mxHosts, $mxweight)) {
                asort($mxweight);
                //$mxHosts = array_keys($mxweight);

            } elseif (checkdnsrr($domain, 'A')) {
                // mail ... ??!?
                $mxweight[0] = 5;
                $mxHosts[0] = gethostbyname($domain);

            }

            // store count of mailers
            $mxHostCount = count($mxHosts);

            // if mx-host found continue ...
            if ($mxHostCount > 0) {

                foreach ($mxweight as $key => $value) {

                    pre('Checking server '.$mxHosts[$key].'...');
                    $errorNumber = 0;
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
                        $smtpCommands = array(
                                        "HELO $validationHeloHost",
                                        "MAIL FROM: <$validationProbeAddress>",
                                        "RCPT TO: <$value>",
                                        "QUIT",
                        );

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
                            $t = 1000 * (microtime(true) - $before);

                            // log
                            pre(htmlentities('$smtpCommand'."\n".'$response').'('.sprintf('%.2f', $t).' ms)');

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
                pre('No valid MX-Record (DNS-entry) found to validate email: '.$value);

                // no success
                $result = false;
            }
        }

        // if not validatEmail (syntax-check) is true - validateEmailauth is false
        return $result;
    }

    /**
     * validates if given value fulfill the requirement "numeric"
     *
     * This method is intend to validate if given value fulfill the requirement "numeric".
     *
     * @param mixed $value The value to validate
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if fulfilled requirements, otherwise FALSE
     * @access public
     * @static
     */
    public static function validateNumeric($value)
    {
        // check pattern
        $pattern = '/^[0-9]+$/';

        // validate and return
        return (bool)preg_match($pattern, $value);
    }

    /**
     * validates if given value fulfill the requirement "ip"
     *
     * This method is intend to validate if given value fulfill the requirement "ip".
     *
     * @param mixed $value The value to validate
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if fulfilled requirements, otherwise FALSE
     * @access public
     * @static
     */
    public static function validateIp($value)
    {
        // check pattern
        $pattern = '/^([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})$/i';

        // validate and return
        return (bool)preg_match($pattern, $value);
    }

    /**
     * validates if given value fulfill the requirement "lowercase"
     *
     * This method is intend to validate if given value fulfill the requirement "lowercase".
     *
     * @param mixed $value The value to validate
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if fulfilled requirements, otherwise FALSE
     * @access public
     * @static
     */
    public static function validateLowercase($value)
    {
        // check pattern
        $pattern = '/^[a-z]*$/';

        // validate and return
        return (bool)preg_match($pattern, $value);
    }

    /**
     * validates if given value fulfill the requirement "uppercase"
     *
     * This method is intend to validate if given value fulfill the requirement "uppercase".
     *
     * @param mixed $value The value to validate
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if fulfilled requirements, otherwise FALSE
     * @access public
     * @static
     */
    public static function validateUppercase($value)
    {
        // check pattern
        $pattern = '/^[A-Z]*$/';

        // validate and return
        return (bool)preg_match($pattern, $value);
    }

    /**
     * validates if given value fulfill the requirement "postcode"
     *
     * This method is intend to validate if given value fulfill the requirement "postcode".
     *
     * @param mixed $value               The value to validate
     * @param array $additionalParameter Additional parameter to use for validation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if fulfilled requirements, otherwise FALSE
     * @access public
     * @static
     */
    public static function validatePostcode($value, $additionalParameter)
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
        $pattern = array(
            'DE' => '^[0-9]{5,5}$',                                                     // GERMANY
            'AT' =>'^[0-9]{4,4}$',                                                      // AUSTRIA
            'AU' =>'^[2-9][0-9]{2,3}$',                                                 // AUSTRALIA
            'CA' =>'^[a-zA-Z].[0-9].[a-zA-Z].\s[0-9].[a-zA-Z].[0-9].',                  // CANADA
            'EE' =>'^[0-9]{5,5}$',                                                      // ESTONIA
            'NL' =>'^[0-9]{4,4}\s[a-zA-Z]{2,2}$',                                       // NETHERLANDS
            'IT' =>'^[0-9]{5,5}$',                                                      // ITALY
            'PT' =>'^[0-9]{4,4}-[0-9]{3,3}$',                                           // PORTUGAL
            'SE' =>'^[0-9]{3,3}\s[0-9]{2,2}$',                                          // SWEDEN
            'UK' =>'^([A-Z]{1,2}[0-9]{1}[0-9A-Z]{0,1}) ?([0-9]{1}[A-Z]{1,2})$',         // UNITED-KINGDOM (England)
            'US' =>'^[0-9]{5,5}[\-]{0,1}[0-9]{4,4}'                                     // UNITED-STATES (USA)
        );

        if (!isset($pattern[$countrycode])) {
            throw new Exception('Unknown country-code "'.$countrycode.'" for postcode-validation!');
        }

        // validate and return
        return (bool)preg_match('/'.$pattern[$countrycode].'/', $value);
    }

    /**
     * validates if given value fulfill the requirement "ustid"
     *
     * This method is intend to validate if given value fulfill the requirement "ustid".
     * http://de.wikipedia.org/wiki/Umsatzsteuer-Identifikationsnummer
     *
     * @param mixed $value               The value to validate
     * @param array $additionalParameter Additional parameter to use for validation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if fulfilled requirements, otherwise FALSE
     * @access public
     * @static
     */
    public static function validateUstid($value, $additionalParameter)
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
        $pattern = array(
            'DE' => '^(DE)[0-9]{9,9}$'  // GERMANY
        );

        if (!isset($pattern[$countrycode])) {
            throw new Exception('Unknown country-code "'.$countrycode.'" for USTID-validation!');
        }

        // validate and return
        return (bool)preg_match('/'.$pattern[$countrycode].'/', $value);
    }

    /*******************************************************************************************************************
     * \\ END PUBLIC STATIC VALIDATION-METHODS
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN PRIVATE TOOLS AND/OR HELPER-METHOS
     ******************************************************************************************************************/

    /**
     * returns the name of the validation-method by given validationtype
     *
     * This method is intend to return the name of the validation-method by given validationtype.
     *
     * @param string $validationtype The validationtype to return methodname for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The constructed Methodname
     * @access private
     * @static
     */
    private static function _getValidationMethodnameByType($validationtype)
    {
        return 'validate'.ucfirst($validationtype);
    }

    /**
     * orders the given set of validation-types
     *
     * This method is intend to order the given set of validation-types.
     *
     * @param array $validationTypes The validation-types to order
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The ordered list of validation-types
     * @access private
     * @static
     */
    private static function _sortValidationtypes(array $validationTypes)
    {
        // prepare validationtypes for ordering (remove not orderable items)
        $splittedValidationtypes = self::_splitForOrder($validationTypes);

        // set sortabel items to sortable for further processing
        $sortable = $splittedValidationtypes['sortable'];

        // extract just the keys from given set of validation(s)
        $sortable = array_keys($sortable);

        // sort the elements by our preferred order to speed up validation
        usort($sortable, array('self', '_compareValidationtypes'));

        // add previously removed items
        foreach ($splittedValidationtypes['nonsortable'] as $validationtype => $value) {
            $sortable[] = array(
                $validationtype => $value
            );
        }

        // return ordered list
        return $sortable;
    }

    /**
     * splits the validation-types into sortable and non-sortable parts
     *
     * This method is intend to split the validation-types into sortable and non-sortable parts.
     *
     * @param array $validationTypes The validation-types to split
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The splitted list of validation-types
     * @access private
     * @static
     */
    private static function _splitForOrder(array $validationTypes)
    {
        // pre-assume every item is sortable
        $sortable = $validationTypes;

        // hols temporary removed items
        $nonsortable = array();

        // types to be removed before ordering
        $removeTypes = array(
            self::VALIDATIONTYPE_VALUE,
            self::VALIDATIONTYPE_MINLENGTH,
            self::VALIDATIONTYPE_MAXLENGTH,
            self::VALIDATIONTYPE_POSTCODE,
            self::VALIDATIONTYPE_USTID
        );

        // iterate over non-sortable validation-types and split
        foreach ($removeTypes as $removeType) {
            if (isset($sortable[$removeType])) {
                $nonsortable[$removeType] = $sortable[$removeType];
                // remove for sorting
                unset($sortable[$removeType]);
            }
        }

        // return prepared items
        return array(
            'sortable'    => $sortable,
            'nonsortable' => $nonsortable
        );
    }

    /**
     * comparemethod for _sortValidationtypes()
     *
     * comparemethod for _sortValidationtypes()
     *
     * @param mixed $a The value to compare ...
     * @param mixed $b ... against this value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if given information is valid, otherwise FALSE if invalid
     * @access private
     * @static
     */
    private static function _compareValidationtypes($a, $b)
    {
        // same elements?
        if ($a == $b) {
            return 0;
        }

        // check for nonexistent index
        if (!isset(self::$_typeOrderMatrix[$a])) {
            throw new Exception(__METHOD__.': nonexistent index for self::$_typeOrderMatrix found: '.$a);
        } elseif (!isset(self::$_typeOrderMatrix[$b])) {
            throw new Exception(__METHOD__.': nonexistent index for self::$_typeOrderMatrix found: '.$b);
        }

        // return ordered
        return (self::$_typeOrderMatrix[$a] < self::$_typeOrderMatrix[$b]) ? -1 : 1;
    }

    /*******************************************************************************************************************
     * \\ END PRIVATE TOOLS AND/OR HELPER-METHOS
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN PUBLIC INTERFACES
     ******************************************************************************************************************/

    /**
     * returns the validation-type matrix
     *
     * This method is intend to return the validation-type matrix.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The validation-types as ordered-by-priority array
     * @access public
     * @static
     */
    public static function getValidationTypeMatrix()
    {
        return self::$_typeOrderMatrix;
    }

    /*******************************************************************************************************************
     * \\ END PUBLIC INTERFACES
     ******************************************************************************************************************/
}

?>
