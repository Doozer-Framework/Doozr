<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Di - Parser - Constructor.
 *
 * Constructor.php - Constructor Parser of the Di.
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
 * @link       https://github.com/clickalicious/Di
 */
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Di/Constants.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Di/Parser/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Di/Parser/Interface.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Di/Collection.php';

/**
 * Doozr - Di - Parser - Constructor.
 *
 * Constructor Parser of the Di.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @link       https://github.com/clickalicious/Di
 */
class Doozr_Di_Parser_Constructor extends Doozr_Di_Parser_Abstract
    implements
    Doozr_Di_Parser_Interface
{
    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Parse out the constructor of a class (eg. for singleton classes).
     *
     * This method is intend to parse out the name of the constructor. This method is used for singleton classes
     * or classes which can not be instantiated via "new" operator.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The name of the method which act as constructor
     *
     * @throws Doozr_Di_Exception
     */
    public function parse()
    {
        // check if all requirements are fulfilled
        if (!$this->requirementsFulfilled()) {
            throw new Doozr_Di_Exception(
                'Error parsing constructor. Requirements not fulfilled. Please set input to parse constructor from.'
            );
        }

        // prepare input for parser
        $this->prepareInput();

        // if called from outside we maybe need a new instance of reflection
        if (!class_exists($this->input['classname']) && !$this->input['reflection']) {
            throw new Doozr_Di_Exception(
                'Could not parse constructor! Please define at least a "file" which contains the classname '.
                'or an existing ReflectionClass instance'
            );
        }

        // get reflection if not already passed to this method
        if (!$this->input['reflection']) {
            $reflectionInstance = new ReflectionClass($this->input['classname']);
        } else {
            $reflectionInstance = $this->input['reflection'];
        }

        // get filename of classname
        if (!isset($this->input['file'])) {
            $this->input['file'] = $reflectionInstance->getFileName();
        }

        /* @var ReflectionInstance $reflectionInstance */
        if ($reflectionInstance->isInstantiable()) {
            $constructor = Doozr_Di_Constants::CONSTRUCTOR_METHOD;
        } else {
            // read the file as array
            $sourcecode = file($this->input['file']);

            // lets find the "real" constructor -> the instance can only be created by a static method
            $possibleConstructors = $reflectionInstance->getMethods(ReflectionMethod::IS_STATIC);

            // default no constructor
            $constructor = null;

            // iterate over static methods and check for instantiation
            foreach ($possibleConstructors as $possibleConstructor) {
                $start            = $possibleConstructor->getStartLine() + 1;
                $end              = $possibleConstructor->getEndLine() - 1;
                $methodSourcecode = '';

                // concat sourcecode lines
                for ($i = $start; $i < $end; ++$i) {
                    $methodSourcecode .= $sourcecode[$i];
                }

                // check for instantiation
                if (strpos($methodSourcecode, 'new self(')
                    || strpos($methodSourcecode, 'new '.$this->input['classname'].'(')
                ) {
                    $constructor = $possibleConstructor->name;
                    break;
                }
            }
        }

        // return the name of constructor method
        return $constructor;
    }

    /**
     * Checks if the requirements are fulfilled.
     *
     * This method is intend to check if the requirements are fulfilled.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if requirements fulfilled, otherwise FALSE
     * @static
     */
    public function requirementsFulfilled()
    {
        return $this->input !== null;
    }
}
