<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Extend
 *
 * Extend.php - This include extends PHP's built-in functionality with plain PHP functions.
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
 * @package    Doozr_Extend
 * @subpackage Doozr_Extend_Global
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

/*----------------------------------------------------------------------------------------------------------------------
| DOOZR RUNTIME GLOBAL CONSTANTS
+---------------------------------------------------------------------------------------------------------------------*/

define('DOOZR_PHP_VERSION',    floatval(PHP_VERSION));
define('DOOZR_PHP_ERROR_MAX',  PHP_INT_MAX);
define('DOOZR_OS',             strtoupper(PHP_OS));
define('DOOZR_WINDOWS',        (substr(DOOZR_OS, 0, 3) === 'WIN') && DIRECTORY_SEPARATOR !== '/');
define('DOOZR_UNIX',           (DIRECTORY_SEPARATOR === '/' && DOOZR_WINDOWS === false));
define('DOOZR_SECURE_HASH',    (DOOZR_PHP_VERSION > 5.11));
define('DOOZR_SAPI',           php_sapi_name());
define('DOOZR_VERSION',        '$Id$');
define('DOOZR_NAME',           'Doozr');
define('DOOZR_NAMESPACE',      'Doozr');
define('DOOZR_NAMESPACE_FLAT', 'doozr');

/*----------------------------------------------------------------------------------------------------------------------
| EXTENDING PHP SUPERGLOBALS
+---------------------------------------------------------------------------------------------------------------------*/

$_CLI               = [];
$_PUT               = [];
$_DELETE            = [];
$GLOBALS['_CLI']    = &$_CLI;
$GLOBALS['_PUT']    = &$_PUT;
$GLOBALS['_DELETE'] = &$_DELETE;

/*----------------------------------------------------------------------------------------------------------------------
| EQUALIZING CLI & WEB & HTTPD - PATCHING REQUEST_URI
+---------------------------------------------------------------------------------------------------------------------*/

if (!isset($_SERVER['REQUEST_URI']) || $_SERVER['REQUEST_URI'] === '') {
    $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
}

/*----------------------------------------------------------------------------------------------------------------------
| SNAPSHOT OF CURRENTLY DEFINED VARIABLES
+---------------------------------------------------------------------------------------------------------------------*/

$_DOOZR = get_defined_vars();

/*----------------------------------------------------------------------------------------------------------------------
| PHP EXTENDING FUNCTIONS (WORKAROUNDS AND SMART-HACKS)
+---------------------------------------------------------------------------------------------------------------------*/

/**
 * This method is an extension to PHP's builtin functions array_merge and array_merge_recursive but
 * with a little difference - This method replace existing values for duplicate keys instead of adding
 * a numeric index like array_merge_recursive does!
 * And it does not simply replace the existing keys like array_merge does - instead it extend existing
 * keys with new keys/values from second array given.
 *
 * @param array $array_1 The array which should be extended ($array_2 overwrites values of duplicate keys!) by $array_2
 * @param array $array_2 The array which extend / overwrite values of $array_1
 *
 * @example $resultingArray = merge_array($array_1, $array_2);
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return array The resulting / merged array
 * @access public
 */
function merge_array(array $array_1, array $array_2)
{
    // Iterate over array which overwrites/supplements array_1
    foreach ($array_2 as $key => $value) {
        // Check if element is an array or a value
        if (is_array($value)) {
            if (!isset($array_1[$key])) {
                // If key does not exist - just set it
                $array_1[$key] = $value;
            } else {
                // If key already exist - start recursion
                $array_1[$key] = merge_array($array_1[$key], $value);
            }
        } else {
            // Values could be stored directly
            $array_1[$key] = $value;
        }
    }

    // Return the new merged array
    return $array_1;
}

/**
 * A recursive array_change_key_case function
 *
 * @param array $input The array to change keys in
 * @param int   $case  The case - can be either CASE_UPPER or CASE_LOWER (constants)
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return array The resulting (processed) array
 * @access public
 */
function array_change_key_case_recursive($input, $case = CASE_LOWER)
{
    if (!is_array($input)) {
        trigger_error("Invalid input array '{$input}'", E_USER_NOTICE);
        exit;
    }

    // CASE_UPPER|CASE_LOWER
    if (!in_array($case, array(CASE_UPPER, CASE_LOWER))) {
        trigger_error("Case parameter '{$case}' is invalid.", E_USER_NOTICE);
        exit;
    }

    $input = array_change_key_case($input, $case);

    foreach ($input as $key => $array) {
        if (is_array($array)) {
            $input[$key] = array_change_key_case_recursive($array, $case);
        }
    }

    return $input;
}

/**
 * Traverses a passed path' ".." out and return a valid filesystem path. Intend to normalize a path from a resource.
 * May it be to prevent directory traversal attacks or just to build correct absolute path'.
 *
 * @param string $path The path (may include a filename) of a resource
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return string The correct un-dotted path
 * @access public
 */
function traverse($path)
{
    $result = [];

    // Correct and may equalize slashes so we operate correctly
    $path = str_replace('\\', '/', $path);

    // Split into parts and traverse if .. found
    $segments = explode('/', $path);

    // Iterate the segments and rebuild the path
    foreach ($segments as $segment) {
        if (($segment == '.') || empty($segment)) {
            continue;
        } elseif ($segment === '..') {
            array_pop($result);
        } else {
            array_push($result, $segment);
        }
    }

    $path = ((DOOZR_UNIX === true) ? '/' : '') . implode(DIRECTORY_SEPARATOR, $result);

    return $path;
}

/**
 * Realpath implementation with a switch to NOT RESOLVE SYMLINKS.
 *
 * @param string $path            The path to return without resolving symlinks
 * @param bool   $resolveSymlinks TRUE to resolve, FALSE to do not
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return string|null The resulting path as string or NULL if failed
 * @access public
 */
function realpath_ext($path, $resolveSymlinks = false)
{
    if ($resolveSymlinks === false && $_SERVER['DOCUMENT_ROOT'] != '') {
        $result   = [];
        $realpath = traverse($path);
        $prepared = '';
        $root     = (DIRECTORY_SEPARATOR === '\\')
            ? str_replace('/', '\\', $_SERVER['DOCUMENT_ROOT'])
            : str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);

        // iterate over partials and try to find the correct path
        for ($i = count($result)-1; $i > -1; --$i) {
            $prepared = (($i > 0) ? DIRECTORY_SEPARATOR : '') . $result[$i] . $prepared;

            if (realpath($root . $prepared) === $path) {
                $realpath = $root . $prepared;
                break;
            }
        }

        // This is important!
        if (false === file_exists($realpath)) {
            $realpath = null;
        }

    } else {
        $realpath = realpath($path);
    }

    return $realpath;
}

/**
 * Changes all values of input to lower- or upper-case
 *
 * @param array $input The array to change values in
 * @param int   $case  The case - can be either CASE_UPPER or CASE_LOWER (constants)
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return array The resulting (processed) array
 * @access public
 */
function array_change_value_case($input, $case = CASE_LOWER)
{
    $result = [];

    if (!is_array($input)) {
        return $result;
    }

    foreach ($input as $key => $value) {
        if (is_array($value)) {
            $result[$key] = array_change_value_case($value, $case);
            continue;
        }
        $result[$key] = ($case == CASE_UPPER ? strtoupper($value) : strtolower($value));
    }

    return $result;
}

/**
 * Removes an element (key + value) from an array by elements value
 *
 * @param array  $array         The array to remove element from
 * @param string $value         The value to remove (item + key)
 * @param bool   $preserve_keys TRUE to keep keys untouched, otherwise FALSE to reindex
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return array The resulting (processed) array
 * @access public
 */
function array_remove_value(array $array, $value = '', $preserve_keys = true)
{
    // do not process empty arrays
    if (empty($array)) {
        return $array;
    }

    // do not process if value not in array
    if (!in_array($value, $array)) {
        return $array;
    }

    // iterate over array elements and remove
    foreach ($array as $array_key => $array_value) {
        if ($array_value == $value) {
            unset($array[$array_key]);
        }
    }

    return ($preserve_keys === true) ? $array : array_values($array);
}

/**
 * Removes the last element of an array.
 *
 * @param array $array     The array to remove element from
 * @param mixed $reference The variable to check against
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return void
 * @access public
 */
function removeLastElementIfSame(array &$array, $reference)
{
    if (end($array) === $reference) {
        unset($array[key($array)]);
    }
}

/**
 * Iterator for recursive array check.
 *
 * @param array $array     The array to check
 * @param stdClass $reference The variable to check against
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return bool TRUE if is recursive, otherwise FALSE
 * @access public
 */
function isRecursiveArrayIteration(array &$array, $reference)
{
    $last_element = end($array);

    if($reference === $last_element) {
        return true;
    }
    $array[]    = $reference;

    foreach ($array as &$element) {
        if (is_array($element)) {
            if (isRecursiveArrayIteration($element, $reference)) {
                removeLastElementIfSame($array, $reference);
                return true;
            }
        }
    }

    removeLastElementIfSame($array, $reference);

    return false;
}

/**
 * Check if array is recursive.
 *
 * @param array $array The array to check
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return bool TRUE if is recursive, otherwise FALSE
 * @access public
 */
function array_recursive(array $array)
{
    $some_reference = new stdclass();
    return isRecursiveArrayIteration($array, $some_reference);
}

/**
 * Converts an object to an array
 *
 * @param object $object The object to convert
 * @param bool $recursive TRUE to call this method recursively till last node of input, FALSE to do not
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return array The resulting array
 * @access public
 */
function object_to_array($object, $recursive = true)
{
    $array = json_decode(
        str_replace("\u0000*\u0000", '', json_encode((array)$object)),
        true
    );

    return $array;
}

/**
 * Converts input array to an object
 *
 * @param mixed   $array     The array to convert to an object
 * @param bool $recursive TRUE to call this method recursively till last node of input, FALSE to do not
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return object The resulting object
 * @access public
 */
function array_to_object(array $array, $recursive = true)
{
    $array = (object)$array;
    foreach ($array as $key => &$value) {
        if (is_array($value) && $recursive) {
            $value = array_to_object($value);
        }
    }

    return $array;
}

/**
 * Takes a string and split it by camel-case
 *
 * @param string $string The string to split by camel-case
 *
 * @example $string = str_split_camelcase('MyTestString');
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return array The splitted string as array
 * @access public
 */
function str_split_camelcase($string)
{
    return preg_split('/(?<=\\w)(?=[A-Z���])/', $string);
}

/**
 * checks if a given string is json
 *
 * @param string $string The string to check
 *
 * @example $string = is_json('MyTestString');
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return bool TRUE if string is json, otherwise FALSE
 * @access public
 */
function is_json($string)
{
    if (is_string($string)) {
        $result = !preg_match(
            '/[^,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]/',
            preg_replace(
                '/"(\\.|[^"\\\])*"/',
                '',
                $string
            )
        );

        return $result && (json_decode($string) !== null);
    }

    return false;
}

/**
 * Calculates the crossfoot of a given number
 *
 * This method is intend to calculate the crossfoot of a given number.
 *
 * @param int $number The number to create crossfoot of
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return integer The calculated crossfoot
 * @access public
 */
function crossfoot($number)
{
    $number = (string)$number;
    $length = strlen($number);

    for ($result = $i = 0; $i < $length; ++$i) {
        $result += $number{$i};
    }

    return $result;
}

// Check if function already exists
if (false === function_exists('filename')) {
    /**
     * Inofficial counterpart to dirname()
     *
     * Given a string containing a path to a file, this function will return the base name of the file.
     * This method is intend to be the inofficial counterpart for dirname() (is used to retrieve the directory
     * from a given path) cause the official counterpart is called basename() ?!? instead of filename()
     *
     * @param string $path   The path to retrieve the filename from
     * @param string $suffix An optional suffix to add before the resulting string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Given a string containing a path to a file, this function will return the base name of the file.
     * @access public
     */
    function filename($path, $suffix = '')
    {
        return basename($path, $suffix);
    }
}

/**
 * helper for creating regular-expressions
 *
 * This method is intend to help creating regular-expressions out of a predefined set of expressions.
 *
 * @param string $input The input to create regexp from
 * @param string $mode  The runtimeEnvironment to use (e.g. default = creating exclude regexp)
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return string The regexp result string
 * @access public
 */
function regexp($input, $mode = 'default')
{
    // generally make input an array
    $input = explode(',', $input);

    // assume empty output
    $output = '';

    // operate in requested runtimeEnvironment
    switch ($mode) {
    case 'default':
    case 'exclude':
    default:
        for ($i = 0; $i < count($input); ++$i) {
            $output .= (strlen($output)) ? '|'.$input[$i] : $input[$i];
        }
        $output = (strlen($output)) ? '(?!'.$output.')' : '';
        break;
    }

    // return result
    return $output;
}

// Check if method already exists
if (false === function_exists('is_ip')) {
    /**
     * Checks if a given string is an IP and returns result as boolean
     *
     * @param string $ip The string to check if it is an IP
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if is IP otherwise FALSE
     */
    function is_ip($ip)
    {
        $result = false;
        if (is_string($ip)) {
            $result = (is_numeric(str_replace('.', '', $ip)) && (substr_count($ip, '.') === 3));
        }

        return $result;
    }
}

/**
 * Generic checksum function creates a checksum of any given input (generic).
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return integer The crc32 checksum of input
 */
function checksum()
{
    // get all arguments of method
    $values = func_get_args();

    // assume empty checksum input
    $input = '';

    foreach ($values as $value) {
        $input .= serialize($value);
    }

    // return the calculated checksum (hash)
    return crc32($input);
}

// Check if method already exists
if (false === function_exists('pre')) {
    /**
     * prints out or return a colorized output (no color in CLI-Mode)
     *
     * This method is intend to print out or return a colorized output (no color in CLI-Mode).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed True if $return = false and string with colorized html if $return = true
     * @access public
     */
    function pre()
    {
        $arguments = func_get_args();

        foreach ($arguments as $argument) {
            dump($argument);
        }
    }
}

// Check if method already exists
if (false === function_exists('pred')) {
    /**
     * prints out or return a colorized output (no color in CLI-Mode) and dies! after output
     *
     * This method is intend to print out or return a colorized output (no color in CLI-Mode).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed True if $return = false and string with colorized html if $return = true
     */
    function pred()
    {
        $arguments = func_get_args();

        foreach ($arguments as $argument) {
            pre($argument);
        }
        die;
    }
}

// Check if method already exists
if (false === function_exists('json_last_error_msg')) {
    /**
     * Returns the last JSON error as string.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The last JSON error as string
     */
    function json_last_error_msg() {
        static $errors = array(
            JSON_ERROR_NONE             => null,
            JSON_ERROR_DEPTH            => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH   => 'Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR        => 'Unexpected control character found',
            JSON_ERROR_SYNTAX           => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8             => 'Malformed UTF-8 characters, possibly incorrectly encoded'
        );
        $error = json_last_error();
        return array_key_exists($error, $errors) ? $errors[$error] : "Unknown error ({$error})";
    }
}

/**
 * Explode any single-dimensional array into a full blown tree structure,
 * based on the delimiters found in it's keys.
 *
 * The following code block can be utilized by PEAR's Testing_DocTest
 * <code>
 * // Input //
 * $key_files = array(
 *   "/etc/php5" => "/etc/php5",
 *   "/etc/php5/cli" => "/etc/php5/cli",
 *   "/etc/php5/cli/conf.d" => "/etc/php5/cli/conf.d",
 *   "/etc/php5/cli/php.ini" => "/etc/php5/cli/php.ini",
 *   "/etc/php5/conf.d" => "/etc/php5/conf.d",
 *   "/etc/php5/conf.d/mysqli.ini" => "/etc/php5/conf.d/mysqli.ini",
 *   "/etc/php5/conf.d/curl.ini" => "/etc/php5/conf.d/curl.ini",
 *   "/etc/php5/conf.d/snmp.ini" => "/etc/php5/conf.d/snmp.ini",
 *   "/etc/php5/conf.d/gd.ini" => "/etc/php5/conf.d/gd.ini",
 *   "/etc/php5/apache2" => "/etc/php5/apache2",
 *   "/etc/php5/apache2/conf.d" => "/etc/php5/apache2/conf.d",
 *   "/etc/php5/apache2/php.ini" => "/etc/php5/apache2/php.ini"
 * );
 *
 * // Execute //
 * $tree = explodeTree($key_files, "/", true);
 *
 * // Show //
 * print_r($tree);
 *
 * // expects:
 * // Array
 * // (
 * //    [etc] => Array
 * //        (
 * //            [php5] => Array
 * //                (
 * //                    [__base_val] => /etc/php5
 * //                    [cli] => Array
 * //                        (
 * //                            [__base_val] => /etc/php5/cli
 * //                            [conf.d] => /etc/php5/cli/conf.d
 * //                            [php.ini] => /etc/php5/cli/php.ini
 * //                        )
 * //
 * //                    [conf.d] => Array
 * //                        (
 * //                            [__base_val] => /etc/php5/conf.d
 * //                            [mysqli.ini] => /etc/php5/conf.d/mysqli.ini
 * //                            [curl.ini] => /etc/php5/conf.d/curl.ini
 * //                            [snmp.ini] => /etc/php5/conf.d/snmp.ini
 * //                            [gd.ini] => /etc/php5/conf.d/gd.ini
 * //                        )
 * //
 * //                    [apache2] => Array
 * //                        (
 * //                            [__base_val] => /etc/php5/apache2
 * //                            [conf.d] => /etc/php5/apache2/conf.d
 * //                            [php.ini] => /etc/php5/apache2/php.ini
 * //                        )
 * //
 * //                )
 * //
 * //        )
 * //
 * // )
 * </code>
 *
 * @author    Kevin van Zonneveld <kevin@vanzonneveld.net>
 * @author    Lachlan Donald
 * @author    Takkie
 * @copyright 2008 Kevin van Zonneveld (http://kevin.vanzonneveld.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD Licence
 * @link      http://kevin.vanzonneveld.net/
 *
 * @param array  $array
 * @param string $delimiter
 * @param bool   $baseval
 *
 * @return array
 */
function explodeTree($array, $delimiter = '_', $baseval = false)
{
    // Check 1st if passed array
    if (false === is_array($array)) {
        return false;
    }

    $regularExpressionSplit = '/' . preg_quote($delimiter, '/') . '/';
    $result                 = [];

    foreach ($array as $key => $value) {
        // Get parent parts and the current leaf
        $parts    = preg_split($regularExpressionSplit, $key, -1, PREG_SPLIT_NO_EMPTY);
        $leafPart = array_pop($parts);

        // Build parent structure - Might be slow for really deep and large structures!
        $parentArr = &$result;
        foreach ($parts as $part) {
            if (false === isset($parentArr[$part])) {
                $parentArr[$part] = [];

            } elseif (false === is_array($parentArr[$part])) {
                if ($baseval) {
                    $parentArr[$part] = array('__base_val' => $parentArr[$part]);
                } else {
                    $parentArr[$part] = [];
                }
            }
            $parentArr = &$parentArr[$part];
        }

        // Add the final part to the structure
        if (empty($parentArr[$leafPart])) {
            $parentArr[$leafPart] = $value;

        } elseif ($baseval && is_array($parentArr[$leafPart])) {
            $parentArr[$leafPart]['__base_val'] = $value;
        }
    }

    return $result;
}

/**
 * Creates a banner (formatted ASCII-output) from a passed string.
 *
 * @param mixed $data The data to format as banner
 * @param mixed $nl   The new-line operator (control-char) to use for banner
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return string True if $return = false and string with colorized html if $return = true
 */
function banner($data = '', $nl = PHP_EOL)
{
    // console buffer max width
    $maxWidthLine  = 78;

    // fillUp-char
    $fillUpChar = ' ';

    // Bugfix -> could not find the real bug
    $maxWidthLine++;

    // console words in a line max width
    $maxWidthWords = $maxWidthLine-2;

    // the bar (formatting)
    $bar    = str_repeat('#', $maxWidthLine-1);

    // the space between text and bar (formatting)
    $spacer = '#' . str_repeat($fillUpChar, $maxWidthWords-1).'#';

    // check for HTML
    $data = str_replace('<br />', '', $data);
    $data = str_replace('<br>', '', $data);
    $data = str_replace("\n", '', $data);
    $data = str_replace("\t", '', $data);
    $data = str_replace("\\", "\\\\", $data);
    $data = trim($data);

    $words = preg_split('/\s+/', $data);

    // check for to long "words" and force splitting
    $tmp = [];
    foreach ($words as $word) {
        if (mb_strlen($word) > ($maxWidthWords)) {
            $wordParts = str_split($word, ($maxWidthWords-4));
            for ($i = 0; $i < count($wordParts); ++$i) {
                if ($i == 0) {
                    $tmp[] = $wordParts[$i].'~';
                } else {
                    $tmp[] = '~'.$wordParts[$i];
                }
            }
        } else {
            $tmp[] = $word;
        }
    }

    // remount
    $words = $tmp;

    // start with preset buffer
    $buffer = '# ';

    // line count
    $lineCount = 1;

    for ($i = 0; $i < count($words); ++$i) {
        if ((strlen($buffer)+strlen($words[$i])+1 ) <= ($lineCount * $maxWidthWords) - 1) {
            $buffer .= $words[$i] . $fillUpChar;
        } else {
            $frame   = ($lineCount * $maxWidthLine) - (strlen($buffer) + 2);
            $buffer .= str_repeat($fillUpChar, ($frame >= 0) ? $frame : 0) . '#' . $nl . '# ' . $words[$i] . ' ';
            ++$lineCount;
        }
    }

    $fillCount = ($lineCount * $maxWidthLine) - (strlen($buffer) + 2);
    $buffer   .= str_repeat($fillUpChar, $fillCount) . '#' . $nl;
    $output    = $nl . $bar . $nl . $spacer . $nl . $buffer . $spacer . $nl . $bar . $nl;

    return $output;
}

// Check if method already exists
if (false === function_exists('is_ssl')) {
    /**
     * Checks if the current connection is SSL secured.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|boolean TRUE if connection is SSL secured, otherwise FALSE
     */
    function is_ssl()
    {
        static $ssl = null;

        if (null === $ssl) {
            if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == '1') || strtolower($_SERVER['HTTPS']) == 'on')) {
                $ssl = true;
            } else {
                $ssl = false;
            }
        }

        return $ssl;
    }
}

/*----------------------------------------------------------------------------------------------------------------------
| LINUX EMULATION IF WE RUN ON WINDOWS OS'
+---------------------------------------------------------------------------------------------------------------------*/

include_once DOOZR_DOCUMENT_ROOT . 'Doozr/Emulate/Linux.php';
