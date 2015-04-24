<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr Base Exception Abtsract
 *
 * DoozrBaseExceptionAbstract.class.php - Abstract Class for Base-Exception of the Doozr Framework
 *
 * PHP versions 5.4
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
 * @category   Doozr
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Exception_Generic
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

/**
 * Doozr Base Exception Abtsract
 *
 * Abstract Class for Base-Exception of the Doozr Framework
 *
 * @category   Doozr
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Exception_Generic
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @author     $LastChangedBy$ <doozr@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
abstract class Doozr_Base_Exception_Generic_Abstract extends Exception
{
    /**
     * holds the exception-message
     *
     * @var string
     * @access protected
     */
    protected $message = 'Doozr -> unknown exception';

    /**
     * holds the exception error-code/nr/#
     *
     * @var int
     * @access protected
     */
    protected $code = 0;

    /**
     * filename where the exception was initially thrown
     *
     * @var string
     * @access protected
     */
    protected $file;

    /**
     * line of the file where the exception was initially thrown
     *
     * @var int
     * @access protected
     */
    protected $line;


    /**
     * Constructor.
     *
     * @param string     $message  The exception-message
     * @param int    $code     The code of the exception
     * @param \Exception $previous The previous exception thrown - AS_OF: PHP 5.3 introduced !
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \Doozr_Base_Exception_Generic_Abstract instance of this class
     * @access public
     */
    public function __construct($message = null, $code = 0, $previous = null)
    {
        // if no message set set default message!
        if (!$message) {
            throw new $this('Exception => "' . get_class($this) . '" without message!');
        }

        // call parents constructor
        parent::__construct($message, $code, $previous);
    }

    /**
     * Returns a string representation of this class content
     *
     * This method is intend to return a string representation of this class content
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string A string representation of this class content
     * @access public
     */
    public function __toString()
    {
        return get_class($this)." '{$this->message}' in {$this->file}({$this->line})\n"."{$this->getTraceAsString()}";
    }
}
