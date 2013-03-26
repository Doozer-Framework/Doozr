<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Handler Exception
 *
 * Exception.php - Exception-Handler of the DoozR-Framework which overrides
 * the PHP default exception-handler (handling)
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
 * @package    DoozR_Handler
 * @subpackage DoozR_Handler_Exception
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Class.php';

// DoozR constants for the three main exception-types (codes like PHP error-types)
define('E_USER_EXCEPTION', 23);
define('E_USER_CORE_EXCEPTION', 235);
define('E_USER_CORE_FATAL_EXCEPTION', 23523);

/**
 * DoozR-Handler-Exception
 *
 * Exception-Handler of the DoozR-Framework which overrides the PHP default exception-handler.
 *
 * @category   DoozR
 * @package    DoozR_Handler
 * @subpackage DoozR_Handler_Exception
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
final class DoozR_Handler_Exception extends DoozR_Base_Class
{
    /**
     * handles error(s)
     *
     * handles errors which are dispatched to this method
     *
     * @param object $exception The thrown and uncaught exception object
     *
     * @return  boolean Everytime TRUE
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public static function handle($exception)
    {
        pred($exception);


        // defaults
        $file = $line = 'N.A.';

        // get stack as array
        $stack = $exception->getTrace();

        // fetch needed vars for error-handler
        $error = ($exception->getCode() != 0) ? $exception->getCode() : E_USER_EXCEPTION;

        if ($stack) {
            $elements = count($stack);

            $file = $stack[($elements>=1) ? ($elements - 1) : $elements]['file'];
            $line = $stack[($elements>=1) ? ($elements - 1) : $elements]['line'];
        }

        // dispatch exception to error_handler for further processing and! logging!
        // return true - signal for exception was handled
        return DoozR_Handler_Error::handle($error, $exception, $file, $line);
    }
}

?>
