<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Di - Parser - Abstract
 *
 * Abstract.php - Abstract base class for all Parser of the Di-Library
 *
 * PHP versions 5.5
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
 * @package    Doozr_Di
 * @subpackage Doozr_Di_Parser_Abstract
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Di
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Exception.php';

/**
 * Doozr - Di - Parser - Abstract
 *
 * Abstract base class for all Parser of the Di-Library
 *
 * @category   Doozr
 * @package    Doozr_Di
 * @subpackage Doozr_Di_Parser_Abstract
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link       https://github.com/clickalicious/Di
 */
abstract class Doozr_Di_Parser_Abstract
{
    /**
     * Contains the input to parse content from
     *
     * @var mixed
     * @access protected
     */
    protected $input;

    /**
     * Contains the last result parsed
     *
     * @var array
     * @access protected
     */
    protected $lastResult;

    /**
     * Contains the temporary data from parsing
     *
     * @var array
     * @access protected
     */
    protected $data = [];


    /*------------------------------------------------------------------------------------------------------------------
    | PROTECTED API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Prepares input for later use (e.g. in parse())
     *
     * This method is intend to prepare  input for later use (e.g. in parse()).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     * @throws Doozr_Di_Exception
     */
    protected function prepareInput()
    {
        $input = array(
            'class'      => null,
            'reflection' => null
        );

        if (is_string($this->input)) {
            $input['class'] = $this->input;

        } else {
            extract($this->input);

            if (!isset($class)) {
                throw new Doozr_Di_Exception(
                    'Error preparing input. No class to parse defined!'
                );
            }

            $input['class'] = $class;

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
     * Loads a file from filesystem
     *
     * This method is intend to load a file from filesystem.
     *
     * @param string $file The name (and path) of the file
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
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
     * Returns the default skeleton for storing a dependency
     *
     * This method returns the default skeleton for storing a dependency.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array An array containing the default skeleton
     * @access protected
     */
    protected function getDefaultSekeleton()
    {
        return array(
            'class'      => null,
            'target'     => null,
            'instance'   => null,
            'type'       => null,
            'value'      => null,
            'position'   => 1
        );
    }

    /**
     * Returns all variables from global scope
     *
     * This method is intend to return all variables from PHP's global scope.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The defined variables from global scope
     * @access protected
     */
    protected function retrieveGlobals()
    {
        // retrieve globals and return them
        global $GLOBALS;
        return $GLOBALS;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for input to parse from
     *
     * This method is the setter for $input.
     *
     * @param string $input The input to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setInput($input)
    {
        // reset on setting new input!
        $this->reset();

        // store input
        $this->input = $input;

        // fluent / chaining
        return $this;
    }

    /**
     * Returns the input
     *
     * This method is intend to return the input.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The input
     * @access public
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Resets the state of this instance
     *
     * This method is intend to reset the state of this instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function reset()
    {
        $this->input = null;
        $this->data  = [];
        $this->lastResult = '';

        // fluent / chaining
        return $this;
    }
}
