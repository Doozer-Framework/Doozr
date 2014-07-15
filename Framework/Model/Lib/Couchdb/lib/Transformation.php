<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Doodi <-> CouchDB - Transformation Class
 *
 * TransformationCouchdb.class.php - The Transformation Class for CouchDB calls
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2014, Benjamin Carl - All rights reserved.
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
 * @package    DoozR_Model
 * @subpackage DoozR_Model_Doodi
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Class.php';

/**
 * DoozR - Doodi <-> CouchDB - Transformation Class
 *
 * The Transformation Class for CouchDB calls
 *
 * @category   DoozR
 * @package    DoozR_Model
 * @subpackage DoozR_Model_Doodi
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
final class Doodi_Couchdb_Transformation extends DoozR_Base_Class
{
    /**
     * out intelligent transformation matrix
     * full-qualified example:
     *
     * @example
     * 'connect' => array(
     *     'class'            => 'phpillowConnection',
     *     'method'           => 'createInstance',
     *     'type'             => 'static',
     *     'argumentCount'    => 2,
     *     'argumentMap'      => array(
     *                               0 => 1,
     *                               1 => 0
     *                           ),
     *     'defaultArguments' => array(
     *                               0 => 'HOST',
     *                               1 => 'PORT'
     *                           ),
     *     'trigger'          => 'getInstance'
     *     )
     *
     * @var array
     * @access private
     */
    private $_transformations = array(
        'connect' => array(
            'class'            => 'phpillowConnection',
            'method'           => 'createInstance',
            'type'             => 'static',
            'argumentCount'    => 2,
            'defaultArguments' => array(
                0 => 'HOST',
                1 => 'PORT'
            ),
            'trigger'          => 'getInstance'
        ),
        'getInstance' => array(
            'class'            => 'phpillowConnection',
            'method'           => 'getInstance',
            'type'             => 'static',
            'argumentCount'    => 0
        ),
        'open' => array(
            'class'            => 'phpillowConnection',
            'method'           => 'setDatabase',
            'type'             => 'static',
            'argumentCount'    => 1,
            'defaultArguments' => array(
                0 => 'DATABASE'
            )
        ),
        'close' => array(),
        'disconnect' => array()
    );

    /*******************************************************************************************************************
     * // BEGIN MAIN CONTROL METHODS (CONSTRUCTOR AND INIT)
     ******************************************************************************************************************/

    /**
     * Constructor.
     *
     * @param array $configuration The configuration to use
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \Doodi_Couchdb_Transformation Instance of this class
     * @access public
     */
    public function __construct($configuration)
    {
        // store the configuration as basic-data
        $this->_basics = $configuration;

        // call parents constructor
        parent::__construct();
    }

    /**
     * transforms generic methods from Doodi (like connect, open, create, read, update, delete)
     * to callable original method behind Facade or Bridge
     *
     * This method is intend to transform generic methods from Doodi
     *
     * @param object $caller    The caller (calling instance)
     * @param string $method    The signature (name of method)
     * @param mixed  $arguments ARRAY of arguments if given, otherwise NULL
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object Instance of this class
     * @access public
     */
    public function transform($caller, $method, $arguments = null)
    {
        // check if a transformation for call is defined ...
        if (isset($this->_transformations[$method])) {

            // retrieve data for transformation
            $transformation = $this->_transformations[$method];

            // check for empty transformation => /dev/null transformation
            if (!empty($transformation)) {

                // the name of the class
                $class  = $transformation['class'];

                // the name of the method
                $method = $transformation['method'];

                // check for arguments
                if ($transformation['argumentCount'] > 0) {

                    // retrieve argument-count assumed
                    $argumentCount = $transformation['argumentCount'];

                    // retrieve count of arguments of current call
                    $argumentCountCall = count($arguments);

                    // if argument-count differs from retrieved arguments fill with default!
                    if ($argumentCountCall < $argumentCount) {
                        // calculate whats missing
                        //$missingArguments = ($argumentCount - $argumentCountCall);

                        // if not an array => make it one
                        $arguments = (!is_array($arguments)) ? array() : $arguments;

                        // iterate over missing and add from default
                        for ($i = $argumentCountCall; $i < $argumentCount; ++$i) {
                            // add the missing argument from default
                            $arguments[] = $this->{$transformation['defaultArguments'][$i]};
                        }
                    }

                    // check for remapping of arguments
                    if (isset($transformation['argumentMap'])) {
                        // get matrix for remapping arguments
                        $matrix = $transformation['argumentMap'];

                        // iterate over transformation
                        for ($i = 0; $i < count($arguments); ++$i) {
                            $target[$matrix[$i]] = $arguments[$i];
                        }

                        // remount + sort
                        $arguments = $target;
                        ksort($arguments);
                    }

                    // get result from call
                    $result = call_user_func_array(array($class, $method), $arguments);

                } else {
                    // get result from call
                    $result = call_user_func(array($class, $method));
                }

                // check for trigger
                if (isset($transformation['trigger'])) {
                    $result = $this->transform($caller, $transformation['trigger']);
                }

                return $result;
            } else {
                // /dev/null transform
                return null;
            }
        } else {
            // if not  a valid transformation -> return to sender (:
            if ($caller) {
                return $this->dynamicCall($caller, $method, $arguments);
            } else {
                return null;
            }
        }
    }

    /*******************************************************************************************************************
     * \\ END MAIN CONTROL METHODS (CONSTRUCTOR AND INIT)
     ******************************************************************************************************************/

    /**
     * returns the transformations
     *
     * This method is intend to return the transformations
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The transformations
     * @access public
     */
    public function getTransformations()
    {
        return $this->_transformations;
    }


    /**
     * magic __get - generic attribute getter
     *
     * This method is intend to act as generic attribute getter
     *
     * @param string $variable The name of the variable to return value for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The requested data
     * @access public
     */
    public function __get($variable)
    {
        var_dump($variable);
        return (isset($this->_basics[$variable])) ?
            $this->_basics[$variable] :
            $this->_triggerError(__METHOD__, $variable, debug_backtrace());
    }


    /**
     * magic __get - generic attribute getter
     *
     * This method is intend to act as generic attribute getter
     *
     * @param string $method  The signature of the method where the error was detected
     * @param string $context The context (variable-name) on which the error was detected
     * @param array  $trace   The stacktrace-snapshot at moment of error-detected
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed NULL
     * @access private
     */
    private function _triggerError($method, $context, $trace)
    {
        // trigger
        trigger_error('Undefined property: '.__CLASS__.'::$'.$context, E_USER_NOTICE);

        // result = null
        return null;
    }
}

?>
