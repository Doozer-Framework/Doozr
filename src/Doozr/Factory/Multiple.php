<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr Factory Multiple.
 *
 * Multiple.php - Doozr's factory for creating instances of multi-instance-classes
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
 * Doozr Factory Multiple.
 *
 * Doozr's factory for creating instances of multi-instance-classes
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
class Doozr_Factory_Multiple
    implements
    Doozr_Factory_Interface
{
    /**
     * This method is intend to create instances of multi-instance-classes. You can
     * pass optional arguments to the factory for creating instance and passing arguments
     * to it. You can pass an optional already existing reflection instance of the class
     * if one exist to speed up instantiation. If you don't use an autoloader the you must
     * include the file containing the class right before you call this method.
     *
     * @param string $className   The name of the class to instantiate
     * @param mixed  $arguments   The arguments to pass to class for instantiation
     * @param string $constructor NOT USED but part of interface/contract
     * @param string $reflection  An optional already existing reflection of the class to create
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return object An instance of the requested class
     * @static
     */
    public static function create($className, $arguments = null, $constructor = null, $reflection = null)
    {
        // get reflection if not passed
        if (!$reflection) {
            $reflection = new ReflectionClass($className);
        }

        // check for given arguments
        if ($arguments) {
            // check if parameter is already of type array
            if (!is_array($arguments)) {
                // if not make array
                $arguments = [$arguments];
            }

            // return instance with parameter
            return $reflection->newInstanceArgs($arguments);
        } else {
            // return instance without parameter
            return $reflection->newInstance();
        }
    }
}
