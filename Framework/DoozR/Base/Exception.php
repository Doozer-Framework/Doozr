<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Base-Exception
 *
 * Exception.php - Simple basic exception class of the DoozR Framework.
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
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Exception
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Exception/Generic.php';

/**
 * DoozR - Base-Exception
 *
 * Simple basic exception class of the DoozR Framework.
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Exception
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @author     $LastChangedBy$ <develop@doozr.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Base_Exception extends DoozR_Base_Exception_Generic
{
    /**
     * overrides parents constructor to add context to each exception of type:
     * DoozR_Base_Exception
     *
     * This method is intend to override parents constructor to add context to each exception.
     *
     * @param string  $message  The exception-message
     * @param integer $code     The code of the exception
     * @param object  $previous The previous exception thrown - AS_OF: PHP 5.3 introduced !
     *
     * @return  object instance of this class
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function __construct($message = null, $code = 0, $previous = null)
    {
        // if no message set set => throw us again
        if (!$message) {
            throw new $this('Exception => "'.get_class($this).'" without message!');
        }

        // add context to message!
        //$message = 'DoozR Exception => '.$message;

        // call parents constructor
        parent::__construct($message, $code, $previous);
    }

    /**
     * generates an unique code for an exception
     *
     * This method is intend to generate an unique code for an exception.
     *
     * @param string  $file The exception-message
     * @param integer $code The error-code of the exception
     *
     * @return  integer An unique error-code
     * @access  protected
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    protected function generateUniqueCode($file, $code)
    {
        $base  = 999 - $this->_getFileNestingLevel($file);
        $base -= floor(sqrt($base * $this->_getChecksum($file))/1000);
        $base  = crossfoot($base);
        $base  = sprintf("%03d", $base);
        $base  = strrev($base);
        $base .= $code;

        return $base;
    }

    /**
     * calculates a checksum for a given string of data
     *
     * This method is intend to calculate a checksum for a given string of data.
     *
     * @param string $string The data as string
     *
     * @return  string The calculated checksum
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _getChecksum($string)
    {
        $length = strlen($string);
        $checksum = 0;

        for ($i = 0; $i < $length; ++$i) {
            $checksum += ord($string{$i}) * $i;
        }

        return $checksum;
    }

    /**
     * calculcates nesting level
     *
     * This method is intend to calculcate the nesting level.
     *
     * @param string $file The file to return nesting level for
     *
     * @return  string The nesting level of file
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _getFileNestingLevel($file)
    {
        return substr_count($file, DIRECTORY_SEPARATOR);
    }
}

?>
