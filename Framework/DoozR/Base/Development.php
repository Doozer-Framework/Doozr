<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Development.php - DoozR Base-Tools
 * A toolset which is useful while developing classes which give you features like
 * ...
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2013, Benjamin Carl - All rights reserved.
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
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Tools
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

/**
 * DoozR Base-Tools
 * A toolset which is useful while developing classes which give you features like
 * ...
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Tools
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @author     $LastChangedBy$
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Base_Development
{
    /**
     * Stores details about the last profiled method
     *
     * @var mixed
     * @access private
     */
    private $_details;

    /**
     * holds the information if the profiled method was type static
     *
     * @var boolean
     * @access private
     */
    private $_profileStatic = false;


    /**
     * Runs a method with the provided arguments for profiling
     *
     * Runs a method with the provided arguments, and returns details about how long it took.
     * Works with instance methods and static methods.
     *
     * @param mixed   $class       The name of the class to profile or an existing instance
     * @param string  $methodname  The name of the method to profile
     * @param array   $methodargs  The arguments to pass to the function
     * @param integer $invocations The number of times to call the method
     *
     * @return float The average invocation duration in seconds
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function profile($class, $methodname, $methodargs = null, $invocations = 1)
    {
        if (is_object($class)) {
            $classname = get_class($class);
        } else {
            $classname = $class;
        }

        if (!class_exists($classname)) {
            throw new Exception("{$classname} doesn't exist");
        }

        // reflect
        $method = new ReflectionMethod($classname, $methodname);

        $instance = null;

        if ($method->isStatic()) {
            // mark last profiling session as static
            $this->_profileStatic = true;
        } elseif (!$method->isStatic() && !$class instanceof $classname) {
            $class = new ReflectionClass($classname);
            $instance = $class->newInstance();
        } else {
            $instance = $class;
        }

        $durations = array();

        for ($i = 0; $i < $invocations; $i++) {
            $start = microtime(true);
            if (is_null($methodargs)) {
                $method->invoke($instance);
            } else {
                $method->invokeArgs($instance, $methodargs);
            }

            $durations[] = microtime(true) - $start;
        }

        $duration['total']   = round(array_sum($durations), 4);
        $duration['average'] = round($duration['total'] / count($durations), 4);
        $duration['worst']   = round(max($durations), 4);

        $this->_details = array(
            'class'       => $classname,
            'method'      => $methodname,
            'arguments'   => $methodargs,
            'duration'    => $duration,
            'invocations' => $invocations
        );

        return $duration['average'];
    }


    /**
     * Returns a string representing the last invoked method
     *
     * This method is intend to return a string representing the last invoked method,
     * including any arguments.
     *
     * @return string The last invoked message
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _invokedMethod()
    {
        if (isset($this->_details)) {
            if ($this->_profileStatic) {
                $scopeResolution = '::';
            } else {
                $scopeResolution = '->';
            }
            if (!is_null($this->_details['arguments'])) {
                $args = join(", ", $this->_details['arguments']);
            } else {
                $args = '';
            }
            return "{$this->_details['class']}{$scopeResolution}{$this->_details['method']}(".$args.")";
        } else {
            return null;
        }
    }


    /**
     * Prints out details about the last profiled method
     *
     * This method is intend to print out the details about the last profiled method.
     *
     * @param boolean $print True [default] to print/echo the profiling-details otherwise it returns the data
     *
     * @return mixed [optional] The result of the last profiling operation (only if $print = false)
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getProfilingDetails($print = true)
    {
        if (isset($this->_details)) {
            $methodString = $this->_invokedMethod();
            $numInvoked   = $this->_details['invocations'];

            if ($numInvoked == 1) {
                $profilingDetails = "{$methodString} took {$this->_details['duration']['average']}s\n";
            } else {
                $profilingDetails = "{$methodString} was invoked {$numInvoked} times\n";
                $profilingDetails .= "Total duration:   {$this->_details['duration']['total']}s\n";
                $profilingDetails .= "Average duration: {$this->_details['duration']['average']}s\n";
                $profilingDetails .= "Worst duration:   {$this->_details['duration']['worst']}s\n";
            }

            // echo or return
            if ($print) {
                echo $profilingDetails;
            } else {
                return $profilingDetails;
            }
        } else {
            return null;
        }
    }
}
