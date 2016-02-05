<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Base - Exception - Abstract.
 *
 * Abstract.php - Abstract class for base exception of the Doozr Framework.
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
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */

/**
 * Doozr - Base - Exception - Abstract.
 *
 * Abstract class for base exception of the Doozr Framework.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
abstract class Doozr_Base_Exception_Generic_Abstract extends RuntimeException
{
    /**
     * Exception-message.
     *
     * @var string
     */
    protected $message = 'Doozr -> unknown exception';

    /**
     * Exception error-code/nr/#.
     *
     * @var int
     */
    protected $code = 0;

    /**
     * Filename of the file where the exception was initially thrown.
     *
     * @var string
     */
    protected $file;

    /**
     * Line of the file where the exception was initially thrown.
     *
     * @var int
     */
    protected $line;

    /**
     * Constructor.
     *
     * @param string|null    $message           Exceptions message
     * @param int            $code              Code of the exception
     * @param Exception|null $previousException Previous exception thrown - AS_OF: PHP 5.3 introduced !
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __construct($message = null, $code = 0, $previousException = null)
    {
        // If no message set set default message!
        if (null === $message) {
            throw new $this(
                sprintf('Exception "%s" without message!', get_class($this))
            );
        }

        // Call parents constructor
        parent::__construct($message, $code, $previousException);
    }

    /**
     * Returns a string representation of this class content.
     *
     * This method is intend to return a string representation of this class content
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string A string representation of this class content
     */
    public function __toString()
    {
        $className = get_class($this);
        $error     = sprintf(
            ' %s in %s(%s)\n%s',
            $this->getMessage(),
            $this->getFile(),
            $this->getLine(),
            $this->getTraceAsString()
        );

        return $className.$error;
    }
}
