<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Development.php - Doozr Base-Tools
 * A toolset which is useful while developing classes which give you features like
 * ...
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
 * @subpackage Doozr_Base_Development
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

/**
 * Doozr Base-Tools
 * A toolset which is useful while developing classes which give you features like
 * ...
 *
 * @category   Doozr
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Development
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @author     $LastChangedBy$
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Base_Development
{
    /**
     * Details about the last profiled method
     *
     * @var mixed
     * @access protected
     */
    protected $details;

    /**
     * Information if the profiled method was type static
     *
     * @var bool
     * @access protected
     */
    protected $profileStatic = false;


    /**
     * Runs a method with the provided arguments for profiling
     *
     * Runs a method with the provided arguments, and returns details about how long it took.
     * Works with instance methods and static methods.
     *
     * @param mixed      $class           Name of the class to profile or an existing instance
     * @param string     $methodname      Name of the method to profile
     * @param array|null $methodarguments Arguments to pass to the function
     * @param int        $invocations     Number of times to call the method
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return float The average invocation duration in seconds
     * @access public
     * @throws Doozr_Exception
     */
    public function profile($class, $methodname, array $methodarguments = null, $invocations = 1)
    {
        if (is_object($class)) {
            $classname = get_class($class);
        } else {
            $classname = $class;
        }

        if (!class_exists($classname)) {
            throw new Doozr_Exception(
                sprintf('%s does not exist.', $classname)
            );
        }

        // reflect
        $method = new ReflectionMethod($classname, $methodname);

        $instance = null;

        if ($method->isStatic()) {
            // mark last profiling session as static
            $this->profileStatic = true;
        } elseif (!$method->isStatic() && !$class instanceof $classname) {
            $class = new ReflectionClass($classname);
            $instance = $class->newInstance();
        } else {
            $instance = $class;
        }

        $durations = [];

        for ($i = 0; $i < $invocations; $i++) {
            $start = microtime(true);
            if (is_null($methodarguments)) {
                $method->invoke($instance);
            } else {
                $method->invokeArgs($instance, $methodarguments);
            }

            $durations[] = microtime(true) - $start;
        }

        $duration['total']   = round(array_sum($durations), 4);
        $duration['average'] = round($duration['total'] / count($durations), 4);
        $duration['worst']   = round(max($durations), 4);

        $this->details = array(
            'class'       => $classname,
            'method'      => $methodname,
            'arguments'   => $methodarguments,
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
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The last invoked message
     * @access private
     */
    private function _invokedMethod()
    {
        if (isset($this->details)) {

            if ($this->profileStatic) {
                $scopeResolution = '::';

            } else {
                $scopeResolution = '->';
            }

            if (!is_null($this->details['arguments'])) {
                $arguments = join(', ', $this->details['arguments']);

            } else {
                $arguments = '';
            }

            $result = sprintf(
                '%s%s%s("%s")',
                $this->details['class'],
                $scopeResolution,
                $this->details['method'],
                $arguments
            );

        } else {
            $result = null;

        }

        return $result;
    }

    /**
     * Prints out details about the last profiled method
     *
     * This method is intend to print out the details about the last profiled method.
     *
     * @param bool $print True [default] to print/echo the profiling-details otherwise it returns the data
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null [optional] The result of the last profiling operation (only if $print = false)
     * @access public
     */
    public function getProfilingDetails($print = true)
    {
        if (isset($this->details)) {
            $methodString = $this->_invokedMethod();
            $numInvoked   = $this->details['invocations'];

            if ($numInvoked == 1) {
                $profilingDetails = sprintf(
                    "%s took %ss\n",
                    $methodString,
                    $this->details['duration']['average']
                );
            } else {
                $profilingDetails  = sprintf('%s was invoked %s times\n', $methodString, $numInvoked);
                $profilingDetails .= sprintf('Total duration:   %ss\n', $this->details['duration']['total']);
                $profilingDetails .= sprintf('Average duration: %ss\n', $this->details['duration']['average']);
                $profilingDetails .= sprintf('Worst duration:   %ss\n', $this->details['duration']['worst']);
            }

            $result = $profilingDetails;

            // echo or return
            if (true === $print) {
                echo $result;
            }

        } else {
            $result = null;
        }

        return $result;
    }
}
