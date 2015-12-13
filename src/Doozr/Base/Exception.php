<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Base - Exception
 *
 * Exception.php - Simple basic exception class of the Doozr Framework.
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
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Exception
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Exception/Generic.php';

/**
 * Doozr - Base - Exception
 *
 * Simple basic exception class of the Doozr Framework.
 *
 * @category   Doozr
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Exception
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Base_Exception extends Doozr_Base_Exception_Generic
{
    /**
     * The type of the exception (is in 99% the classname)
     *
     * @var string
     * @access public
     */
    public $type;

    /**
     * The message of the exception
     *
     * @var string
     * @access public
     */
    public $message;

    /**
     * The filename in which the exception was thrown
     *
     * @var string
     * @access public
     */
    public $file;

    /**
     * The line where the exception was thrown
     *
     * @var int
     * @access public
     */
    public $line;

    /**
     * The code of the exception
     *
     * @var int
     * @access public
     */
    public $code;

    /**
     * The last exception if the exception was forwarded
     *
     * @var Exception
     * @access public
     */
    public $previous;

    /**
     * The arguments passed to the last executed method
     *
     * @var array
     * @access public
     */
    public $arguments;


    /**
     * Constructor.
     *
     * @param string    $message  Exception-message
     * @param int       $code     Code of the exception
     * @param Exception $previous Previous exception thrown - AS_OF: PHP 5.3 introduced !
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \Doozr_Base_Exception
     * @access public
     */
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        // No message set set => throw us again
        if (null === $message) {
            throw new $this(
                sprintf('Exception "%s" without message!', get_class($this))
            );
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * Generates an unique code for each exception type.
     *
     * @param string  $file The exception-message
     * @param int $code The error-code of the exception
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string An unique error-code
     * @access protected
     */
    protected function generateUniqueCode($file, $code)
    {
        $base  = 999 - $this->getFileNestingLevel($file);
        $base -= floor(sqrt($base * $this->getChecksum($file))/1000);
        $base  = crossfoot($base);
        $base  = sprintf("%03d", $base);
        $base  = strrev($base);
        $base .= $code;

        return $base;
    }

    /**
     * Returns checksum for passed input.
     *
     * @param string $string The data as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The calculated checksum
     * @access protected
     */
    protected function getChecksum($string)
    {
        $length = strlen($string);
        $checksum = 0;

        for ($i = 0; $i < $length; ++$i) {
            $checksum += ord($string{$i}) * $i;
        }

        return $checksum;
    }

    /**
     * Calculates the nesting level of a passed filename.
     *
     * @param string $file The file to return nesting level for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The nesting level of file
     * @access protected
     */
    protected function getFileNestingLevel($file)
    {
        return substr_count($file, DIRECTORY_SEPARATOR);
    }

    /**
     * Setter for type.
     *
     * @param string $type The type to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Setter for type.
     *
     * @param string $type The type to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Base_Exception
     * @access public
     */
    public function type($type)
    {
        $this->setType($type);
        return $this;
    }

    /**
     * Getter for type
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The type
     * @access public
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Setter for message.
     *
     * @param string $message The message to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Setter for message.
     *
     * @param string $message The message to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Base_Exception
     * @access public
     */
    public function message($message)
    {
        $this->setMessage($message);
        return $this;
    }

    /**
     * Setter for file.
     *
     * @param string $file The filename to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * Setter for file.
     *
     * @param string $file The filename to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Base_Exception
     * @access public
     */
    public function file($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Setter for line.
     *
     * @param integer $line The line to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setLine($line)
    {
        $this->line = $line;
    }

    /**
     * Setter for line.
     *
     * @param integer $line The line to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Base_Exception
     * @access public
     */
    public function line($line)
    {
        $this->setLine($line);
        return $this;
    }
}
