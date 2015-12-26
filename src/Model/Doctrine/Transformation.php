<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Model - Doctrine - Transformation.
 *
 * Transformation.php - Transformation for x calls.
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
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Class.php';

/**
 * Doozr - Model - Doctrine - Transformation.
 *
 * Transformation for x calls.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 * @final
 */
final class Model_Doctrine_Transformation extends \Doozr_Base_Class
{
    /**
     * The basics.
     *
     * @var array
     */
    protected $basics;

    /**
     * out intelligent transformation matrix
     * full-qualified example:.
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
     */
    protected $transformations = array(
        'connect' => array(
            'class' => 'phpillowConnection',
            'method' => 'createInstance',
            'type' => 'static',
            'argumentCount' => 2,
            'defaultArguments' => array(
                0 => 'HOST',
                1 => 'PORT',
            ),
            'trigger' => 'getInstance',
        ),
        'getInstance' => array(
            'class' => 'phpillowConnection',
            'method' => 'getInstance',
            'type' => 'static',
            'argumentCount' => 0,
        ),
        'open' => array(
            'class' => 'phpillowConnection',
            'method' => 'setDatabase',
            'type' => 'static',
            'argumentCount' => 1,
            'defaultArguments' => array(
                0 => 'DATABASE',
            ),
        ),
        'close' => [],
        'disconnect' => [],
    );

    /**
     * Constructor.
     *
     * @param array $configuration The configuration to use
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __construct($configuration)
    {
        // Store the configuration as basic-data
        $this->basics = $configuration;
    }

    /**
     * transforms generic methods from Doodi (like connect, open, create, read, update, delete)
     * to callable original method behind Facade or Bridge.
     *
     * This method is intend to transform generic methods from Doodi
     *
     * @param object $caller    The caller (calling instance)
     * @param string $method    The signature (name of method)
     * @param mixed  $arguments ARRAY of arguments if given, otherwise NULL
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return object Instance of this class
     */
    public function transform($caller, $method, $arguments = null)
    {
        // check if a transformation for call is defined ...
        if (isset($this->transformations[$method])) {

            // retrieve data for transformation
            $transformation = $this->transformations[$method];

            // check for empty transformation => /dev/null transformation
            if (!empty($transformation)) {

                // the name of the class
                $class = $transformation['class'];

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
                        $arguments = (!is_array($arguments)) ? [] : $arguments;

                        // iterate over missing and add from default
                        for ($i = $argumentCountCall; $i < $argumentCount; ++$i) {
                            // add the missing argument from default
                            $arguments[] = $this->{$transformation['defaultArguments'][$i]};
                        }
                    }

                    // Check for remapping of arguments
                    if (isset($transformation['argumentMap'])) {
                        // Get matrix for remapping arguments
                        $matrix = $transformation['argumentMap'];

                        // Iterate over transformation
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
                return;
            }
        } else {
            // if not  a valid transformation -> return to sender (:
            if ($caller) {
                return $this->dynamicCall($caller, $method, $arguments);
            } else {
                return;
            }
        }
    }

    /**
     * returns the transformations.
     *
     * This method is intend to return the transformations
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The transformations
     */
    public function getTransformations()
    {
        return $this->transformations;
    }

    /**
     * magic __get - generic attribute getter.
     *
     * @param string $variable The name of the variable to return value for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed The requested data
     */
    public function __get($variable)
    {
        return (isset($this->basics[$variable])) ?
            $this->basics[$variable] :
            $this->triggerError(__METHOD__, $variable, debug_backtrace());
    }

    /**
     * magic __get - generic attribute getter.
     *
     * @param string $method  The signature of the method where the error was detected
     * @param string $context The context (variable-name) on which the error was detected
     * @param array  $trace   The stacktrace-snapshot at moment of error-detected
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed
     */
    protected function triggerError($method, $context, $trace)
    {
        // trigger
        trigger_error('Undefined property: '.__CLASS__.'::$'.$context, E_USER_NOTICE);

        // result = null
        return;
    }
}
