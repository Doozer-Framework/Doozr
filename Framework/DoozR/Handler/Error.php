<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Handler - Error
 *
 * Error.php - Error-Handler of the DoozR-Framework which overrides PHP's
 * default error-handler (handling)
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
 * @package    DoozR_Handler
 * @subpackage DoozR_Handler_Error
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Class.php';

/**
 * DoozR - Handler - Error
 *
 * Error-Handler of the DoozR-Framework which overrides PHP's default error-handler (handling)
 *
 * @category   DoozR
 * @package    DoozR_Handler
 * @subpackage DoozR_Handler_Error
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
final class DoozR_Handler_Error extends DoozR_Base_Class
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
     * informed about DoozR's state and to pipe the Errors
     * to attached Logger-Subsystem.
     *
     * @param int|string $number  Number of Error (constant)
     * @param string     $message Error description as String
     * @param string     $file    File in which the error occured
     * @param integer    $line    Line in which the error occured
     * @param array      $context The variables with name and value from error context
     *
     * @throws DoozR_Error_Exception
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE always
     * @access public
     * @static
     */
    public static function handle($number = '', $message = '', $file = '', $line = 0, $context = array())
    {
        // get error type
        $errorType = self::getErrorType($number);

        /**
         * pack error into an exception so that the error can be forwarded
         * to exception handler without trickin PHP's default behavior
         */
        $error          = new DoozR_Exception($message, $number);
        $error->type    = $errorType;
        $error->message = $message;
        $error->file    = $file;
        $error->line    = $line;

        // overwrite values above with real error values
        /*
        if ($error = error_get_last()) {
            $exception->type    = $error['type'];
            $exception->message = $error['message'];
            $exception->file    = $error['file'];
            $exception->line    = $error['line'];
        }
        */

        throw new DoozR_Error_Exception($message, $number, $error);

        // we must return TRUE here cause -> we didn't handled this error
        return true;
    }

    /**
     * formats messages
     *
     * This method is intend to format messages (like warnings, errors ...).
     *
     * @param string $type    The type of the message
     * @param mixed  $nr      The error-nummber (error-code)
     * @param string $message The message
     * @param string $file    The filename
     * @param mixed  $line    The linenummber
     * @param array  $context The context variable and value from error context
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean Everytime TRUE
     * @access private
     * @static
     */
    private static function _formatMessage(
        $type    = 'N.A.',
        $nr      = null,
        $message = 'N.A.',
        $file    = 'N.A.',
        $line    = null,
        $context = array()
    ) {
        // format message
        $message  = 'TYPE: '.$type."\n".'NR: ['.$nr.']'."\n".'MESSAGE: '.wordwrap($message, 120)."\n";
        $message .= 'IN FILE: '.$file."\n".'ON LINE: '.$line."\n";
        // add php-version and server-os
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
     * @return boolean True always
     * @access public
     * @static
     */
    public static function handleUnhandable()
    {
        // get last error thrown by php
        $e = error_get_last();

        // if not empty ...
        if (!empty($e)) {
            // Handle
            #return self::handle($e['type'], $e['message'], $e['file'], $e['line']);

            // and log
            #$logger = DoozR_Logger::getInstance();
            #$logger->error($e['message'], 'ERROR', $e['file'], $e['line']);
            #$logger->error($e['message']);
        }

        // return success
        return true;
    }

    /**
     * prints out or return a colorized output (no color in CLI-Mode)
     *
     * This method is intend to print out or return a colorized output (no color in CLI-Mode).
     *
     * @param mixed  $data   The data to show as colorized output
     * @param mixed  $return Defines if the colorized data should be outputted or returned [optional]
     * @param string $color  The color for Text in HEX-notation
     * @param string $cursor The cursor (css) to use
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed True if $return = false and string with colorized html if $return = true
     * @access public
     * @static
     */
    public static function pre($data, $return = false, $color = '#EF4A4A', $cursor = 'crosshair')
    {
        // dispatch to pred() from DoozR.extend.php
        pred($data, $return, $color, $cursor);
    }

    /**
     * Returns the translation from php-errorcode to our internal types
     *
     * This method is intend to return the translation from php-errorcode
     * to our internal types.
     *
     * @param integer $error The PHP-type of the error (PHP constant)
     *
     * @return string The translation type for input
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @static
     */
    public static function getErrorType($error)
    {
        // check for errortype
        switch ($error) {
        case E_ERROR:                            // 1
            // Fatal run-time errors. These indicate errors that can not be recovered from, such as a memory allocation
            // problem. Execution of the script is halted.
        case E_USER_ERROR:                       // 256
            // User-generated error message. This is like an E_ERROR, except it is generated in PHP code by using the
            // PHP function trigger_error() (since PHP 4).
        return 'ERROR';
        break;
        case E_WARNING:                          // 2
            // Run-time warnings (non-fatal errors). Execution of the script is not halted.
        case E_USER_WARNING:                     // 512
            // User-generated warning message. This is like an E_WARNING, except it is generated in PHP code by using
            // the PHP function trigger_error() (since PHP 4).
        return 'WARNING';
        break;
        case E_PARSE:                            // 4
            // Compile-time parse errors. Parse errors should only be generated by the parser.
        return 'PARSE';
        break;
        case E_NOTICE:                           // 8
            // Run-time notices. Indicate that the script encountered something that could indicate an error, but could
            // also happen in the normal course of running a script.
        case E_USER_NOTICE:                      // 1024
            // User-generated notice message. This is like an E_NOTICE, except it is generated in PHP code by using the
            // PHP function trigger_error() (since PHP 4).
        return 'NOTICE';
        break;
        case E_CORE_ERROR:                       // 16
            // Fatal errors that occur during PHP's initial startup. This is like an E_ERROR, except it is generated by
            // the core of PHP (since PHP 4).
        return 'CORE-ERROR';
        break;
        case E_CORE_WARNING:                     // 32
            // Warnings (non-fatal errors) that occur during PHP's initial startup. This is like an E_WARNING, except it
            // is generated by the core of PHP (since PHP 4).
        return 'CORE-WARNING';
        break;
        case E_COMPILE_ERROR:                    // 64
            // Fatal compile-time errors. This is like an E_ERROR, except it is generated by the Zend Scripting Engine
            // (since PHP 4).
        return 'COMPILE-ERROR';
        break;
        case E_COMPILE_WARNING:                  // 128
            // Compile-time warnings (non-fatal errors). This is like an E_WARNING, except it is generated by the Zend
            // Scripting Engine.
        return 'COMPILE-WARNING';
        break;
        case E_STRICT:                           // 2048
            //case E_USER_STRICT:                // ?!?
            // Enable to have PHP suggest changes to your code which will ensure the best interoperability and forward
            // compatibility of your code (since PHP 5)
        return 'STRICT';
        case E_RECOVERABLE_ERROR:                // 4096
            // Catchable fatal error. It indicates that a probably dangerous error occured, but did not leave the
            // Engine in an unstable state. If the error is not caught by a user defined handle
            // (see also set_error_handler()), the application aborts as it was an E_ERROR (since PHP 5.2.0).
        return 'RECOVERABLE';
        break;
        case E_USER_EXCEPTION:                   // 23
        case E_USER_CORE_EXCEPTION:              // 235
            // DoozR custom Error-Type - Error of type exception.
        return 'EXCEPTION';
        break;
        case E_USER_CORE_FATAL_EXCEPTION:        // 23523
            // DoozR custom Error-Type - Fatal-Error of type exception.
        return 'EXCEPTION';
        break;
        }

        // new types in PHP > 5.3
        if (phpversion() >= 5.3) {
            if ($error == E_DEPRECATED || $error == E_USER_DEPRECATED) {
                return 'DEPRECATED';
            }
        }

        // nothing matched?
        return 'UNCLASSIFIED';
    }
}
