<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Extend - Emulate - Linux
 *
 * Php.php - This include extends PHP's functionality by emulating missing
 *           functionality in PHP versions <= 5.3. It use native PHP-Code
 *           replacements of PHP's C-implementations in newer PHP releases.
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2014, Benjamin Carl - All rights reserved.
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
 * @package    DoozR_Extend
 * @subpackage DoozR_Extend_Emulate_Php
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

/***********************************************************************************************************************
 * // BEGIN DEFINING PHP 5.3 ONLY CONSTANTS FOR PHP-INSTALLATIONS <= 5.2
 **********************************************************************************************************************/

// define E_DEPRECATED as E_USER_NOTICE [compatible with trigger_error] for PHP < 5.3
define('E_DEPRECATED', 1024);


// define E_USER_DEPRECATED as E_USER_NOTICE [compatible with trigger_error] for PHP < 5.3
define('E_USER_DEPRECATED', 1024);

/***********************************************************************************************************************
 * \\ END DEFINING PHP 5.3 ONLY CONSTANTS FOR PHP-INSTALLATIONS <= 5.2
 **********************************************************************************************************************/

/***********************************************************************************************************************
 * // BEGIN EMULATING PHP 5.3 ONLY FUNCTIONALITY FOR PHP-INSTALLATIONS <= 5.2
 **********************************************************************************************************************/

/**
 * parse_ini_string
 *
 * Define parse_ini_string if it doesn't exist (available from PHP >= 5.3).
 * Does accept lines starting with ; as comments.
 * Does not accept comments after values
 *
 * @param string $string The string to parse as ini
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return array $array The parsed ini content as array
 * @access public
 */
function parse_ini_string($string)
{
    $array = array();
    $lines = explode("\n", $string);

    foreach ($lines as $line) {
        $statement = preg_match("/^(?!;)(?P<key>[\w+\.\-]+?)\s*=\s*(?P<value>.+?)\s*$/", $line, $match);

        if ($statement) {
            $key   = $match['key'];
            $value = $match['value'];

            // remove quote
            if (preg_match("/^\".*\"$/", $value) || preg_match("/^'.*'$/", $value)) {
                $value = mb_substr($value, 1, mb_strlen($value) - 2);
            }

            $array[$key] = $value;
        }
    }

    // return parsed result
    return $array;
}

/**
 * get_called_class support for php-version < 5.3
 *
 * This method is intend to add get_called_class support for php-version < 5.3
 * Tested and works in PHP 5.2.4 - by http://www.sol1.com.au
 * another (maybe better?) version exists right here:
 * http://www.septuro.com/2009/07/php-5-2-late-static-binding-get_called_class-and-self-new-self/
 * not tested yet
 *
 * @param mixed   $backtrace Boolean FALSE on init, array with retrieved debug_backtrace() result to reduce load
 * @param integer $level     Level of current recursion starting with one (1)
 *
 * @author  Benjamin Carl <opensource@clickalicious.de>
 * @return  string The name of the called class
 */
function get_called_class($backtrace = false, $level = 1)
{
    if (!$backtrace) {
        $backtrace = debug_backtrace();
    }

    if (!isset($backtrace[$level])) {
        throw new Exception("Cannot find called class -> stack level too deep.");
    }

    if (!isset($backtrace[$level]['type'])) {
        throw new Exception('type not set');
    } else {
        switch ($backtrace[$level]['type']) {
        case '::':
            /**
             * test patch for detecting called-class in case of using call_user_func or call_user_func_array
             */
            if (!isset($backtrace[$level]['file']) && isset($backtrace[$level+1]['function'])
                && in_array($backtrace[$level+1]['function'], array('call_user_func', 'call_user_func_array'))
            ) {
                if (is_array($backtrace[$level+1]['args'][0])) {
                    return $backtrace[$level+1]['args'][0][0];
                } else {
                    $signature = explode('::', $backtrace[$level+1]['args'][0]);
                    return $signature[0];
                }
            }
            /**
             * end test patch
             */

            $lines = file($backtrace[$level]['file']);
            $i = 0;
            $callerLine = '';

            do {
                $i++;
                $callerLine = $lines[$backtrace[$level]['line']-$i].$callerLine;
            } while (stripos($callerLine, $backtrace[$level]['function']) === false);

            preg_match('/([a-zA-Z0-9\_]+)::'.$backtrace[$level]['function'].'/', $callerLine, $matches);

            if (!isset($matches[1])) {
                pred('NO CALLER');
                // must be an edge case.
                throw new Exception('Could not find caller class: originating method call is obscured.');
            }

            switch ($matches[1]) {
            case 'self':
            case 'parent':
            return get_called_class($backtrace, $level+1);
            break;

            default:
            return $matches[1];
            break;

            }
            break;

        // won't get here.
        case '->':
            switch ($backtrace[$level]['function']) {
            case '__get':
                // edge case -> get class of calling object
                if (!is_object($backtrace[$level]['object'])) {
                    throw new Exception('Edge case fail. __get called on non object.');
                }
            return get_class($backtrace[$level]['object']);
            break;

            default:
            return $backtrace[$level]['class'];
            }
            break;

        default:
        throw new Exception('Unknown backtrace method type.');
        break;
        }
    }
}

/**
 * lcfirst support for php-version < 5.3
 *
 * lcfirst � Make a string's first character lowercase
 *
 * @param string $string The string to lower the first character in
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return string The string with lower first character
 */
function lcfirst($string)
{
    $string{0} = strtolower($string{0});
    return $string;
}

// check if function allready exists
if (!function_exists('checkdnsrr')) {
    /**
     * checkdnsrr support for php-version < 5.3 on windows
     *
     * This method is a workaround for missing checkdnsrr() for PHP-Version < 5.3 on windows operating systems.
     *
     * @param string $host The host to check (e.g. gmx.de)
     * @param string $type The DNS-type to check (e.g. default = MX)
     *
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @return  boolean TRUE if lookup successful, otherwise FALSE
     */
    function checkdnsrr($host, $type = 'MX')
    {
        // assume false = non-existent
        $result = false;

        // call cli tool nslookup on windows to get result
        @exec('nslookup -type='.$type.' '.$host, $cliResult);

        // iterate over buffer
        foreach ($cliResult as $line) {
            if (preg_match('/^'.$host.'/i', $line)) {
                $result = true;
            }
        }

        // return the result
        return $result;
    }
}

/**
 * getmxrr support for php-version < 5.3 on windows
 *
 * This method is a workaround for missing getmxrr() for PHP-Version < 5.3 on windows operating systems.
 *
 * @param string $host      The host to check (e.g. gmx.de)
 * @param array  &$mxhosts  The array reference to found store mx-records in
 * @param array  &$mxweight The array reference to store the weight of the mx-records in
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return boolean TRUE if lookup successful, otherwise FALSE
 */
function getmxrr($host, array &$mxhosts, array &$mxweight)
{
    // assume false = non-existent
    $result = false;

    // call cli tool nslookup on windows to get result
    @exec('nslookup -type=MX '.$host, $cliResult);

    // iterate over buffer
    foreach ($cliResult as $line) {
        if (preg_match('/^'.$host.'/i', $line)) {
            // split the current line by blank
            $lineParts = explode(' ', $line);

            // store mx record
            $mxhost = $lineParts[count($lineParts)-1];
            $mxhosts[] = $mxhost;

            // retrieve weight
            preg_match('/[0-9]{2}/', $line, $weight);
            $mxweight[$mxhost] = $weight[0];

            // result is true
            $result = true;
        }
    }

    // return the result
    return $result;
}

/***********************************************************************************************************************
 * \\ END EMULATING PHP 5.3 ONLY FUNCTIONALITY FOR PHP-INSTALLATIONS <= 5.2
 **********************************************************************************************************************/
