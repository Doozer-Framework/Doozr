<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Handler - Exception.
 *
 * Exception.php - Exception-Handler of the Doozr-Framework which overrides
 * the PHP default exception-handler (handling)
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
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Class.php';

// Doozr constants for the three main exception-types (codes like PHP error-types)
define('E_USER_EXCEPTION', 23);
define('E_USER_CORE_EXCEPTION', 235);
define('E_USER_CORE_FATAL_EXCEPTION', 23523);

/**
 * Doozr - Handler - Exception.
 *
 * Exception-Handler of the Doozr-Framework which overrides
 * the PHP default exception-handler (handling)
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 * @final
 */
final class Doozr_Handler_Exception extends Doozr_Base_Class
{
    /**
     * Replacement for PHP's default internal exception handler. All Exceptions are forwarded
     * to this method - we decide here what to do with it.
     * We need this hook to stay informed about Doozr's state and to pipe the Exceptions to
     * attached Logger-Subsystem.
     *
     * @param Exception $exception The thrown and uncaught exception object
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @static
     */
    public static function handle(Exception $exception)
    {
        // Sometimes errors thrown before subsystem is ready - in this case its always told to outside but logged
        $debug = (true === defined('DOOZR_DEBUGGING')) ? DOOZR_DEBUGGING : true;

        // In range of 100 - 599 we do send a HTTP response by logic and laws of Doozr
        if ($exception->getCode() < 100 || $exception->getCode() > 599) {
            $statusCode = 500;
        } else {
            $statusCode = $exception->getCode();
        }

        // Information disclosure protection. Only in debug we show what really happened
        if (true === $debug) {
            $message = $exception->getMessage();
            $code    = $exception->getCode();
        } else {
            $message = constant('Doozr_Http::REASONPHRASE_'.$statusCode);
            $code    = $statusCode;
        }

        // Simple exception switch by runtime environment
        if (Doozr_Kernel::RUNTIME_ENVIRONMENT_WEB === DOOZR_RUNTIME_ENVIRONMENT) {
            self::handleHtml(
                $statusCode,
                $code,
                $message,
                $exception->getPrevious()->getFile(),
                $exception->getPrevious()->getLine()
            );
        } else {
            self::handleText(
                $code,
                $message,
                $exception->getPrevious()->getFile(),
                $exception->getPrevious()->getLine()
            );
        }
    }

    /**
     * Output of exception for HTML ready environments.
     *
     * @param int    $statusCode HTTP status code of the exception
     * @param int    $code       Code of the exception
     * @param string $message    Message of the exception
     * @param string $file       File where exception was thrown
     * @param int    $line       Line number where exception was thrown
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected static function handleHtml($statusCode, $code, $message, $file, $line)
    {
        $file = '';
        $line = '';

        if (false === headers_sent($file, $line)) {
            header('HTTP/'.Doozr_Http::VERSION_1_1.' '.$statusCode.' '.$message);
        }

        // Show the message
        echo sprintf('<h1>%s %s</h1>', $code, $message);
    }

    /**
     * Output of exception for text-only environments.
     *
     * @param int    $code    Code of exception
     * @param string $message Message of exception
     * @param string $file    File where exception was thrown
     * @param int    $line    Line number where exception was thrown
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected static function handleText($code, $message, $file, $line)
    {
        echo sprintf('%s %s %s %s', $code, $message, $file, $line);
    }
}
