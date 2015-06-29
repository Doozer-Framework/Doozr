<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Di - Parser - Typehint
 *
 * Typehint.php - Typehint Parser of the Di-Library
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
 * @subpackage Doozr_Di_Parser_Typehint
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Di
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Parser/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Parser/Interface.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Dependency.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Collection.php';

/**
 * Doozr - Di - Parser - Typehint
 *
 * Typehint Parser of the Di-Library
 *
 * @category   Doozr
 * @package    Doozr_Di
 * @subpackage Doozr_Di_Parser_Typehint
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link       https://github.com/clickalicious/Di
 */
class Doozr_Di_Parser_Typehint extends Doozr_Di_Parser_Abstract
    implements
    Doozr_Di_Parser_Interface
{
    /**
     * Parser
     *
     * @var Doozr_Di_Parser_Constructor $parser
     * @access protected
     */
    protected $parser;


    /**
     * Constructor.
     *
     * @param \Doozr_Di_Parser_Constructor $parser
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     */
    public function __construct(Doozr_Di_Parser_Constructor $parser)
    {
        $this->parser = $parser;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Parses the typehints out of input and return the dependencies based on it as array.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Containing the dependencies build from typehints
     * @access public
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
        if (!class_exists($this->input['class']) && !$this->input['reflection']) {
            throw new Doozr_Di_Exception(
                'Could not parse constructor! Please define at least a "file" which contains the class '.
                'or an existing ReflectionClass instance'
            );
        }

        // get reflection if not already passed to this method
        if (!$this->input['reflection']) {
            $reflectionClass = new ReflectionClass($this->input['class']);
        }

        // get filename of class
        if (!isset($this->input['file'])) {
            $this->input['file'] = $reflectionClass->getFileName();
        }

        // read the file as array
        $sourcecode = file($this->input['file']);

        // return the result
        return $this->parseTypehints($reflectionClass, $sourcecode);
    }

    /**
     * Parses the typehints out of input and return the dependencies based on it as array
     *
     * This method is intend to parse the typehints for the given reflection instance and the sourcecode.
     *
     * @param ReflectionClass $reflectionClass An instance of ReflectionClass
     * @param string          $sourcecode      The sourcecode to parse typehints from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Containing the dependencies build from typehints
     * @access protected
     * @throws Doozr_Di_Exception
     */
    protected function parseTypehints(ReflectionClass $reflectionClass, $sourcecode)
    {
        // lets find all possible public reachable methods
        $reflectionMethods = $reflectionClass->getMethods(ReflectionMethod::IS_STATIC|ReflectionMethod::IS_PUBLIC);

        // assume empty result
        $result = [];

        // set parser input for constructor parser
        $this->parser->setInput(
            array(
                'class'      => $reflectionClass->getName(),
                'reflection' => $reflectionClass
            )
        );

        // get constructor of class for check!
        $constructor = $this->parser->parse();

        // iterate over all found candidates and check for Typehints
        foreach ($reflectionMethods as $reflectionMethod) {
            /* @var $reflectionMethod ReflectionMethod */

            // extract signature from source
            $signature = trim($sourcecode[$reflectionMethod->getStartLine()-1]);

            // extract arguments from signature
            $signature = $this->signatureToArray($reflectionMethod->getName(), $signature);

            // now check the result for typehints
            foreach ($signature as $method => $arguments) {

                $result2 = [];

                foreach ($arguments as $position => $argument) {
                    // get default dependencies (skeleton)
                    $tmp = $this->getDefaultSekeleton();

                    // fill with real data
                    $tmp['class']      = $argument[0];
                    $tmp['target']     = str_replace('$', '', $argument[1]);
                    $tmp['type']       = ($constructor == $method) ?
                        Doozr_Di_Dependency::TYPE_CONSTRUCTOR :
                        Doozr_Di_Dependency::TYPE_METHOD;
                    $tmp['position']   = $position;
                    $tmp['value']      = $method;

                    // store indexed by method
                    $result2[] = $tmp;
                }

                (count($result2)>0) ? $result[$method] = $result2 : '';
            }
        }

        // return the result
        return $result;
    }

    /**
     * Checks if the requirements are fulfilled
     *
     * This method is intend to check if the requirements are fulfilled.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if requirements fulfilled, otherwise FALSE
     * @access public
     * @static
     */
    public function requirementsFulfilled()
    {
        return ($this->input !== null);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PROTECTED
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Parses a given signature of a method for arguments and returns result as array
     *
     * This method is intend to parse a given signature of a method for arguments and returns result as array.
     *
     * @param string  $method    The name of the method to parse signature of
     * @param string  $signature The signature to parse
     * @param bool $cleanup   TRUE to direct cleanup the result and store only arguments with typehint
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Containing the parsed arguments and method name
     * @access protected
     */
    protected function signatureToArray($method, $signature, $cleanup = true)
    {
        // get begin and end of arguments
        $begin = strpos($signature,  '(')+1;
        $end   = strrpos($signature, ')');

        // extract the list of aringuments
        $signature = substr($signature, $begin, $end-$begin);

        // convert to array
        $arguments = explode(',', $signature);

        // empty result
        $result = [];

        $i = 1;

        // iterate arguments and check for typehint
        foreach ($arguments as $argument) {
            $argument = trim($argument);

            if ($cleanup === true) {
                // take it only if typehint exist and if this is not "array"
                if (stristr($argument, ' ') !== false && strtolower(substr($argument, 0, 5)) != 'array') {
                    $argument = explode(' ', $argument);
                    //array_push($result, $argument);
                    $result[$i] = $argument;
                }

            } else {
                //array_push($result, $argument);
                $result[$i] = $argument;

            }

            ++$i;
        }

        // return arguments
        return array($method => $result);
    }
}
