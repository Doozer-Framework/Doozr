<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Base - Development.
 *
 * Development.php - Doozr base development tools.
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
 * Doozr - Base - Development.
 *
 * Doozr base development tools.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @author     $LastChangedBy$
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Base_Development
{
    /**
     * Details about the last profiled method.
     *
     * @var array|null
     */
    protected $profilingDetails;

    /**
     * Information if the profiled method is static.
     *
     * @var bool
     */
    protected $profileStatic = false;

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Runs a method with the provided arguments for profiling.
     *
     * Runs a method with the provided arguments, and returns profilingDetails about how long it took.
     * Works with instance methods and static methods.
     *
     * @param mixed      $class           Name of the class to profile or an existing instance
     * @param string     $methodName      Name of the method to profile
     * @param array|null $methodArguments Arguments to pass to the function
     * @param int        $invocations     Number of times to call the method
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     *
     * @throws Doozr_Exception
     */
    public function profile($class, $methodName, array $methodArguments = null, $invocations = 1)
    {
        if (true === is_object($class)) {
            $className = get_class($class);
        } else {
            $className = $class;

            if (false === class_exists($className)) {
                throw new Doozr_Exception(
                    sprintf('%s does not exist.', $className)
                );
            }
        }

        $method   = new \ReflectionMethod($className, $methodName);
        $instance = null;

        if (true === $method->isStatic()) {
            // mark last profiling session as static
            $this->profileStatic = true;
        } elseif (false === $method->isStatic() && !($class instanceof $className)) {
            $class    = new \ReflectionClass($className);
            $instance = $class->newInstance();
        } else {
            $instance = $class;
        }

        $durations = [];

        for ($i = 0; $i < $invocations; ++$i) {
            $start = microseconds();

            if (is_null($methodArguments)) {
                $method->invoke($instance);
            } else {
                $method->invokeArgs($instance, $methodArguments);
            }

            $durations[] = microseconds() - $start;
        }

        $total = round(array_sum($durations), 8);

        return $this->profilingDetails([
            'class'     => $className,
            'method'    => $methodName,
            'arguments' => $methodArguments,
            'duration'  => [
                'microseconds' => [
                    'total'   => $total,
                    'average' => round($total / count($durations), 8),
                    'worst'   => round(max($durations), 8),
                ],
                'seconds' => [
                    'total'   => $total / 1000,
                    'average' => round($total / 1000 / count($durations), 8),
                    'worst'   => round(max($durations) / 1000, 8),
                ],
            ],
            'invocations' => $invocations,
        ]);
    }

    /**
     * Prints out profilingDetails about the last profiled method.
     *
     * @param bool $print TRUE to print/echo the profiling-profilingDetails otherwise it returns the data
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string|null Report of last profiling operation
     */
    public function getProfilingReport($print = false)
    {
        // Check for existing profilingDetails ...
        if (null !== $details = $this->getProfilingDetails()) {
            $methodName       = $this->invokedMethod();
            $countInvocations = $details['invocations'];

            if (1 === $countInvocations) {
                $report = sprintf('%s took %ss', $methodName, $details['duration']['microseconds']['average']);
            } else {
                $report = sprintf('%s was invoked %s times\n', $methodName, $countInvocations);
                $report .= sprintf('Total duration:   %sms\n', $details['duration']['microseconds']['total']);
                $report .= sprintf('Average duration: %sms\n', $details['duration']['microseconds']['average']);
                $report .= sprintf('Worst duration:   %sms\n', $details['duration']['microseconds']['worst']);
            }

            if (true === $print) {
                echo $report;
            }
        }

        return $report;
    }

    /**
     * Getter for profilingDetails.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array|null profilingDetails set, otherwise NULL
     */
    public function getProfilingDetails()
    {
        return $this->profilingDetails;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns a string representing the last invoked method.
     *
     * This method is intend to return a string representing the last invoked method,
     * including any arguments.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The last invoked message
     */
    protected function invokedMethod()
    {
        $result = null;

        // Existing profile profilingDetails ...
        if (null !== $details = $this->getProfilingDetails()) {
            if (true === $this->profileStatic) {
                $scopeResolution = '::';
            } else {
                $scopeResolution = '->';
            }

            if (null !== $this->getProfilingDetails()['arguments']) {
                $arguments = implode(', ', $details['arguments']);
            } else {
                $arguments = '';
            }

            $result = sprintf('%s%s%s("%s")', $details['class'], $scopeResolution, $details['method'], $arguments);
        }

        return $result;
    }

    /**
     * Setter for profilingDetails.
     *
     * @param array $profilingDetails profilingDetails to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setProfilingDetails(array $profilingDetails)
    {
        $this->profilingDetails = $profilingDetails;
    }

    /**
     * Fluent: Setter for profilingDetails.
     *
     * @param array $profilingDetails profilingDetails to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function profilingDetails(array $profilingDetails)
    {
        $this->setProfilingDetails($profilingDetails);

        return $this;
    }
}
