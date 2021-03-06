<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Di - Parser - Abstract.
 *
 * Abstract.php - Abstract base class for all Parser of Di.
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

/**
 * Doozr - Di - Parser - Abstract.
 *
 * Abstract base class for all Parser of Di.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @link       https://github.com/clickalicious/Di
 * @abstract
 */
abstract class Doozr_Di_Parser_Abstract
{
    /**
     * Input to parse content from.
     *
     * @var mixed
     */
    protected $input;

    /**
     * Last result parsed.
     *
     * @var array
     */
    protected $lastResult;

    /**
     * Temporary data from parsing.
     *
     * @var array
     */
    protected $data = [];

    /*------------------------------------------------------------------------------------------------------------------
    | PROTECTED API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Prepares input for later use (e.g. in parse()).
     * This method is intend to prepare  input for later use (e.g. in parse()).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @throws Doozr_Di_Exception
     */
    protected function prepareInput()
    {
        $input = [
            'className'  => null,
            'reflection' => null,
        ];

        if (is_string($this->input)) {
            $input['className'] = $this->input;
        } else {
            extract($this->input);

            if (!isset($className)) {
                throw new Doozr_Di_Exception(
                    'Error preparing input. No className to parse defined!'
                );
            }

            $input['className'] = $className;

            if (isset($file)) {
                $this->loadFile($file);
                $input['file'] = $file;
            }

            if (isset($reflection)) {
                $input['reflection'] = $reflection;
            }
        }

        $this->input = $input;
    }

    /**
     * Loads a file from filesystem.
     * This method is intend to load a file from filesystem.
     *
     * @param string $file The name (and path) of the file
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @throws Doozr_Di_Exception
     */
    protected function loadFile($file)
    {
        if (!is_file($file)) {
            throw new Doozr_Di_Exception(
                sprintf(
                    'Error loading file! File "%s" is not a valid file.',
                    $file
                )
            );
        }

        include_once $file;
    }

    /**
     * Returns the default skeleton for storing a dependency.
     * This method returns the default skeleton for storing a dependency.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array An array containing the default skeleton
     */
    protected function getDefaultSkeleton()
    {
        return [
            'className' => null,
            'type'      => null,
            'target'    => null,
            'instance'  => null,
            'value'     => null,
            'position'  => 1,
        ];
    }

    /**
     * Returns all variables from global scope.
     * This method is intend to return all variables from PHP's global scope.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The defined variables from global scope
     */
    protected function retrieveGlobals()
    {
        // Retrieve globals and return them
        global $GLOBALS;

        return $GLOBALS;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for input to parse from.
     *
     * @param string $input The input to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function setInput($input)
    {
        // reset on setting new input!
        $this->reset();
        $this->input = $input;
    }

    /**
     * Fluent: Setter for input to parse from.
     *
     * @param string $input The input to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function input($input)
    {
        $this->setInput($input);

        return $this;
    }

    /**
     * Returns the input.
     * This method is intend to return the input.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The input
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Resets the state of this instance.
     * This method is intend to reset the state of this instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function reset()
    {
        $this->input      = null;
        $this->data       = [];
        $this->lastResult = '';

        // fluent / chaining
        return $this;
    }
}
