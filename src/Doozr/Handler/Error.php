<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Handler - Error
 *
 * Error.php - Error-Handler of the Doozr-Framework which overrides PHP's
 * default error-handler (handling)
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
 * @package    Doozr_Handler
 * @subpackage Doozr_Handler_Error
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Class.php';

/**
 * Doozr - Handler - Error
 *
 * Error-Handler of the Doozr-Framework which overrides PHP's default error-handler (handling)
 *
 * @category   Doozr
 * @package    Doozr_Handler
 * @subpackage Doozr_Handler_Error
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 * @final
 */
final class Doozr_Handler_Error extends Doozr_Base_Class
{
    /**
     * holds the status of enabling logging of unclassified error(s)
     *
     * @var mixed
     * @access protected
     * @static
     */
    protected static $logUnclassified = null;

    /**
     * Replacement for PHP's default internal error handler.
     * All Errors are dispatched to this method - we decide
     * here what to do with it. We need this hook to stay
     * informed about Doozr's state and to pipe the Errors
     * to attached Logger-Subsystem.
     *
     * @param int|string $number  Number of Error (constant)
     * @param string     $message Error description as String
     * @param string     $file    File in which the error occurred
     * @param int        $line    Line in which the error occurred
     * @param array      $context The variables with name and value from error context
     *
     * @throws Doozr_Error_Exception
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE always
     * @access public
     * @static
     */
    public static function handle($number = '', $message = '', $file = '', $line = 0, $context = [])
    {
        // If we shouldn't care we follow this rule!
        if (!($number & error_reporting())) {
            return true;
        }

        // get error type
        $type = self::getErrorType($number);

        // Pack error into an exception so that the error can be forwarded to exception handler
        $error = new Doozr_Exception($message, $number);
        $error
            ->type($type)
            ->message($message)
            ->file($file)
            ->line($line);

        // Now dispatch the error processable and from userland catchable as Exception
        throw new Doozr_Error_Exception($message, $number, $error);
    }

    /**
     * formats messages
     *
     * This method is intend to format messages (like warnings, errors ...).
     *
     * @param string $type    The type of the message
     * @param mixed  $nr      The error-number (error-code)
     * @param string $message The message
     * @param string $file    The filename
     * @param mixed  $line    The line-number
     * @param array  $context The context variable and value from error context
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Message
     * @access protected
     * @static
     */
    protected static function formatMessage(
        $type    = 'N.A.',
        $nr      = null,
        $message = 'N.A.',
        $file    = 'N.A.',
        $line    = null,
        $context = []
    ) {
        // Format message
        $message  = 'TYPE: '.$type.PHP_EOL.'NR: ['.$nr.']'.PHP_EOL.'MESSAGE: '.wordwrap($message, 120).PHP_EOL;
        $message .= 'IN FILE: '.$file.PHP_EOL.'ON LINE: '.$line.PHP_EOL;
        $message .= 'PHP-Version: '.PHP_VERSION.' ('.PHP_OS.')';

        // finally return formatted message
        return $message;
    }

    /**
     * callback for unhandable error (e.g. E_PARSE)
     *
     * handles errors which are (theoratically) unhandable like E_PARSE
     * and some more (currently not tested which)
     * php.net (http://www.php.net/manual/en/function.set-error-handler.php) says:
     * The following error types cannot be handled with a user defined function:
     * E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING, and most of E_STRICT
     * raised in the file where set_error_handler() is called.).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean|null True always
     * @access public
     * @static
     */
    public static function handleUnhandable()
    {
        // We can retrieve the last error as whole by using PHP's internal function
        $error = error_get_last();

        // Check if can getMetaComponents the error ...
        if (!empty($error) && self::isError($error) === true) {
            // Handle by default handler -> It seems that the types of error catched here cannot be processed like ...
            // Return FALSE to signalize PHP error handled: http://php.net/manual/en/function.set-error-handler.php
            return self::handle($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }


    public static function isError($error)
    {
        $isError = false;

        if (is_array($error) && isset($error['type']) === true) {
            switch($error['type']) {
                case E_ERROR:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                    if (
                        isset($error['type']) &&
                        isset($error['message']) &&
                        isset($error['file']) &&
                        isset($error['line'])
                    ) {
                        $isError = true;
                    }
                    break;
            }
        }

        return $isError;
    }

    /**
     * Returns the translation from php-errorcode to our internal types.
     *
     * @param int $error The PHP-type of the error (PHP error constant value)
     *
     * @return string The translation type for input
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @static
     */
    public static function getErrorType($error)
    {
        // Check for type of error
        switch ($error) {
            case E_ERROR:                            // 1
                // Fatal run-time errors. These indicate errors that can not be recovered from,
                // such as a memory allocation problem. Execution of the script is halted.
            case E_USER_ERROR:                       // 256
                // User-generated error message. This is like an E_ERROR, except it is generated in
                // PHP code by using the PHP function trigger_error() (since PHP 4).
                $type = 'ERROR';
                break;

            case E_WARNING:                          // 2
                // Run-time warnings (non-fatal errors). Execution of the script is not halted.
            case E_USER_WARNING:                     // 512
                // User-generated warning message. This is like an E_WARNING, except it is generated in
                // PHP code by using the PHP function trigger_error() (since PHP 4).
                $type = 'WARNING';
                break;

            case E_PARSE:                            // 4
                // Compile-time parse errors. Parse errors should only be generated by the parser.
                $type = 'PARSE';
                break;

            case E_NOTICE:                           // 8
                // Run-time notices. Indicate that the script encountered something that could indicate an
                // error, but could also happen in the normal course of running a script.
            case E_USER_NOTICE:                      // 1024
                // User-generated notice message. This is like an E_NOTICE, except it is generated in PHP
                // code by using the PHP function trigger_error() (since PHP 4).
                $type = 'NOTICE';
                break;

            case E_CORE_ERROR:                       // 16
                // Fatal errors that occur during PHP's initial startup. This is like an E_ERROR, except it
                // is generated by the core of PHP (since PHP 4).
                $type = 'CORE-ERROR';
                break;

            case E_CORE_WARNING:                     // 32
                // Warnings (non-fatal errors) that occur during PHP's initial startup. This is like an
                // E_WARNING, except it is generated by the core of PHP (since PHP 4).
                $type = 'CORE-WARNING';
                break;

            case E_COMPILE_ERROR:                    // 64
                // Fatal compile-time errors. This is like an E_ERROR, except it is generated by the Zend
                // Scripting Engine (since PHP 4).
                $type = 'COMPILE-ERROR';
                break;

            case E_COMPILE_WARNING:                  // 128
                // Compile-time warnings (non-fatal errors). This is like an E_WARNING, except it is generated
                // by the Zend Scripting Engine.
                $type = 'COMPILE-WARNING';
                break;

            case E_STRICT:                           // 2048
                //case E_USER_STRICT:                // ?!?
                // Enable to have PHP suggest changes to your code which will ensure the best interoperability
                // and forward compatibility of your code (since PHP 5)
                $type = 'STRICT';
                break;

            case E_RECOVERABLE_ERROR:                // 4096
                // Catchable fatal error. It indicates that a probably dangerous error occurred, but did not
                // leave the Engine in an unstable state. If the error is not caught by a user defined getMetaComponents
                // (see also set_error_handler()), the application aborts as it was an E_ERROR (since PHP 5.2.0).
                $type = 'RECOVERABLE';
                break;

            case E_DEPRECATED:                      // 8192
                // Run-time notices. Enable this to receive warnings about code that will not work in future versions.
            case E_USER_DEPRECATED:                 // 16384
                // User-generated warning message. This is like an E_DEPRECATED, except it is generated in PHP code
                // by using the PHP function trigger_error().
                $type = 'DEPRECATED';
                break;

            case E_USER_EXCEPTION:                   // 23
            case E_USER_CORE_EXCEPTION:              // 235
                // Doozr custom Error-Type - Error of type exception.
                $type = 'EXCEPTION';
                break;

            case E_USER_CORE_FATAL_EXCEPTION:        // 23523
                // Doozr custom Error-Type - Fatal-Error of type exception.
                $type = 'EXCEPTION';
                break;

            default:
                // nothing matched?
                $type = 'UNCLASSIFIED';
                break;
        }

        return $type;
    }
}
