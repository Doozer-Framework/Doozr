<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Di Factory
 *
 * Factory.php - Factory of the Di-Framework
 *
 * PHP versions 5
 *
 * LICENSE:
 * Di - The Dependency Injection Framework
 *
 * Copyright (c) 2012, Benjamin Carl - All rights reserved.
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
 * @category   Di
 * @package    DoozR_Di_Framework
 * @subpackage DoozR_Di_Framework_Factory
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2012 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Di
 */

require_once DI_PATH_LIB_DI . 'Exception.php';
require_once DI_PATH_LIB_DI . 'Dependency.php';

/**
 * Di Factory
 *
 * Factory of the Di-Framework
 *
 * @category   Di
 * @package    DoozR_Di_Framework
 * @subpackage DoozR_Di_Framework_Factory
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2012 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link       https://github.com/clickalicious/Di
 */
class DoozR_Di_Factory
{
    /**
     * Contains an reflection-class-instance of the
     * currently processed class
     *
     * @var ReflectionClass
     * @access private
     */
    private $_reflector;

    /**
     * Contains the is-instanciable status of the
     * currently process class
     *
     * @var boolean
     * @access private
     */
    private $_instanciable;

    /**
     * Contains the name of the constructor-method of
     * the currently process class
     *
     * @var string
     * @access private
     */
    private $_constructor;


    /*******************************************************************************************************************
     * PUBLIC API
     ******************************************************************************************************************/

    /**
     * Instantiates a class without further dependencies
     *
     * This method is intend to instanciate a class. The classname is the name of the class to instanciate
     * and arguments is an (optional) array of arguments which are passed to the class as additional arguments
     * when instanciating.
     *
     * @param string $classname    The name of the class to instanciate
     * @param array  $dependencies The complete setup of dependencies
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object The new instance
     * @access public
     */
    public function build($classname, $dependencies = null)
    {
        // get reflection
        $this->_reflector = new ReflectionClass($classname);

        // check if is instanciable (simple runtimeEnvironment)
        $this->_instanciable = $this->_reflector->isInstantiable();

        // default
        if ($dependencies !== null) {
            // store constructor
            if (isset($dependencies['constructor'])) {
                $this->_constructor = $dependencies['constructor'];
            }

            // create instance with dependencies
            return $this->_instanciateWithDependencies($classname, $dependencies);
        } else {
            // create instance without dependencies
            return $this->_instanciateWithoutDependencies($classname);
        }
    }

    /**
     * Constructs an instance of a given class
     *
     * This method is intend to construct an instance of a given class and pass the given (optional) arguments
     * to the constructor. This method looks really ugly and i know this of course. But this way is a tradeoff
     * between functionality and speed optimization.
     *
     * @param string $classname The name of the class to instanciate
     * @param array  $arguments The arguments to pass to constructor
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object Instance of given class(name)
     * @access public
     * @throws DoozR_Di_Exception
     */
    public function construct($classname, array $arguments = array())
    {
        switch(count($arguments)) {
        case 0:
            return new $classname();
        case 1:
            return new $classname($arguments[0]);
        case 2:
            return new $classname($arguments[0],$arguments[1]);
        case 3:
            return new $classname($arguments[0],$arguments[1],$arguments[2]);
        case 4:
            return new $classname($arguments[0],$arguments[1],$arguments[2],$arguments[3]);
        case 5:
            return new $classname($arguments[0],$arguments[1],$arguments[2],$arguments[3],$arguments[4]);
        case 6:
            return new $classname($arguments[0],$arguments[1],$arguments[2],$arguments[3],$arguments[4],$arguments[5]);
        default:
            throw new DoozR_Di_Exception(
                'Too much arguments passed to '.__METHOD__.'. This method can handle not more than 6 arguments'.
                'Your class seems to have a architectural problem. Please reduce count of arguments passed to'.
                'constructor'
            );
        }
    }

    /*******************************************************************************************************************
     * PRIVATE + PROTECTED
     ******************************************************************************************************************/

    /**
     * Instantiates a class including it dependencies
     *
     * This method is intend to instanciate a class and pass the required dependencies to it.
     * The depencies are preconfigured and passed to this method as $setup. The classname is
     * the name of the class to instanciate and arguments is an (optional) array of arguments
     * which are passed to the class as additional arguments when instanciating.
     *
     * @param string $classname The name of the class to instanciate
     * @param array  $setup     The setup for instanciating (contains array of depencies, arguments, ...)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object The new created instance
     * @access private
     */
    private function _instanciateWithDependencies($classname, $setup)
    {
        // get dependencies
        $dependencies = $setup['dependencies'];

        // get arguments for final class from setup
        $arguments = (isset($setup['arguments'])) ? $setup['arguments'] : array();

        // hold the 3 possible methods of injection (constructor, method, property)
        $injections = $this->_initEmptyInjectionContainer();

        // iterate over config
        /* @var $dependency DoozR_Di_Dependency */
        foreach ($dependencies as $target => $dependency) {

            // check if an instance already exists
            if (!$dependency->getInstance()) {

                // create and store new instance
                if ($dependency->hasArguments()) {
                    $dependency->setInstance(
                        $this->construct($dependency->getClassname(), $dependency->getArguments())
                    );
                } else {
                    $dependenyClassname = $dependency->getClassname();
                    $dependency->setInstance(
                        new $dependenyClassname()
                    );
                }
            }

            // get configuration for injection
            $configuration = $dependency->getConfiguration();

            // store position for injection if type = configuration
            if (!isset($configuration['position'])) {
                $configuration['position'] = (isset($injections[$configuration['type']]))
                    ? count($injections[$configuration['type']]) + 1
                    : 1;
            }

            $injections[$configuration['type']][] = array(
                'instance' => $dependency->getInstance(),
                'value'    => (isset($configuration['value'])) ? $configuration['value'] : null,
                'position' => $configuration['position']
            );
        }

        // process injections, create instance and return it
        return $this->_createInstance($classname, $arguments, $injections);
    }

    /**
     * Instantiates a class without further dependencies
     *
     * This method is intend to instanciate a class. The classname is the name of the class to instanciate
     * and arguments is an (optional) array of arguments which are passed to the class as additional arguments
     * when instanciating.
     *
     * @param string $classname The name of the class to instanciate
     * @param mixed  $arguments Can be either a list of additional arguments passed to constructor when instance get
     *                          created or NULL if no arguments needed (default = null)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object The new created instance
     * @access private
     */
    private function _instanciateWithoutDependencies($classname, array $arguments = array())
    {
        return $this->_createInstance($classname, $arguments);
    }

    /**
     * Instantiates a class and process the optional arguments and injections
     *
     * This method is intend to instanciate a class and pass the required dependencies to it.
     * The depencies are preconfigured and passed to this method as $setup. The classname is
     * the name of the class to instanciate and arguments is an (optional) array of arguments
     * which are passed to the class as additional arguments when instanciating.
     *
     * @param string $classname  The name of the class to instanciate
     * @param array  $arguments  The arguments to pass to constructo
     * @param array  $injections The injections to process
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object The new created instance
     * @access private
     */
    private function _createInstance($classname, array $arguments = array(), array $injections = array())
    {
        // process only if $injections exists
        if (count($injections)) {
            // get injections for constructor
            $constructorInjections = $this->_parseInjections(DoozR_Di_Dependency::TYPE_CONSTRUCTOR, $injections);

            // process injections for constructor
            if ($constructorInjections) {

                $arguments = $this->_mergeArguments($constructorInjections, $arguments);

                /*
                // @todo: what is or was this for?
                if (!empty($arguments)) {
                    // combine with arguments
                    $arguments = $this->_mergeArguments($constructorInjections, $arguments);
                    //$arguments = array_merge($constructorInjections, $arguments);
                } else {
                    $arguments = $this->_mergeArguments($constructorInjections, $arguments);
                }
                */
            }
        }

        // get instance - for no dependency calls too
        $instance = $this->_constructorInjection($classname, $arguments);

        // process only if $injections exists
        if (count($injections)) {
            // get injections for setter
            $setterInjections = $this->_parseInjections(DoozR_Di_Dependency::TYPE_METHOD, $injections);

            // process injections for constructor
            if ($setterInjections) {
                // work the other injection types like "setter"
                $this->_setterInjection($instance, $setterInjections);
            }

            // get injections for property
            $propertyInjections = $this->_parseInjections(DoozR_Di_Dependency::TYPE_PROPERTY, $injections);

            // process injections for constructor
            if ($propertyInjections) {
                // work the other injection types like "property"
                $this->_propertyInjection($instance, $propertyInjections);
            }
        }

        // return result
        return $instance;
    }

    /**
     * Returns an instance with injected dependencies
     *
     * This method is intend to return an instance of the given class. It injects
     * the required dependencies into constructor on instanciation.
     *
     * @param string $classname The name of the class to instanciate
     * @param array  $arguments The arguments to pass to constructor
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object The new created instance
     * @access private
     */
    private function _constructorInjection($classname, array $arguments)
    {
        // is the target instanciable (= no singleton stuff ~ new foo())
        if ($this->_instanciable) {
            return $this->construct($classname, $arguments);
        } else {
            if (is_array($this->_constructor)) {
                return call_user_func_array($this->_constructor, $arguments);
            } else {
                // TODO: _constructor == null = suchen?
                return call_user_func_array(array($classname, $this->_constructor), $arguments);
            }
        }
    }

    /**
     * Injects given dependencies through setters
     *
     * This method is intend to injects given dependencies through setters.
     *
     * @param string &$instance  The instance to inject dependencies to
     * @param array  $injections The dependencies to inject as array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _setterInjection(&$instance, array $injections)
    {
        foreach ($injections as $injection) {
            $instance->{$injection['signature']}($injection['argument']);
        }
    }

    /**
     * Injects given dependencies through properties
     *
     * This method is intend to injects given dependencies through properties.
     *
     * @param string &$instance  The instance to inject dependencies to
     * @param array  $injections The dependencies to inject as array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _propertyInjection(&$instance, array $injections)
    {
        foreach ($injections as $injection) {
            $instance->{$injection['signature']} = $injection['argument'];
        }
    }

    /**
     * Parses out the requqested type of injection from list of injections
     *
     * This method is intend to parse out the requqested type of injection from list of injections.
     *
     * @param string $type       The type of injection (can be of: constructor, setter, property)
     * @param array  $injections The dependencies to parse from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed NULL if no dependencies found, otherwise ARRAY containing the dependencies
     * @access private
     */
    private function _parseInjections($type, array $injections)
    {
        // assume no result
        $result = null;

        if (!empty($injections[$type])) {
            $result = array();

            switch ($type) {
            case DoozR_Di_Dependency::TYPE_PROPERTY:
            case DoozR_Di_Dependency::TYPE_METHOD:
                foreach ($injections[$type] as $key => $value) {
                    $result[] = array(
                        'signature' => $value['value'],
                        'argument'  => $value['instance']
                    );
                }
                break;
            default:
                // break intentionally omitted
            case DoozR_Di_Dependency::TYPE_CONSTRUCTOR:
                return $injections[$type];
                break;
            }
        }

        // return result
        return $result;
    }

    /**
     * Merges given constructor injections and arguments for constructor
     *
     * This method is intend to merge injections and arguments for constructor.
     *
     * @param array $injections The injection arguments to pass to constructor
     * @param array $arguments  The additional arguments to pass to constructor
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The merged result ready to pass to targets constructor
     * @access private
     */
    private function _mergeArguments(array $injections, array $arguments)
    {
        // get total count of arguments
        $sum = count($injections) + count($arguments);

        // prepare an array with null values for given count
        $result = array_fill(0, $sum, null);

        // loop counter for positioning of unpositioned constructor injections
        $injectionPosition = 1;

        // fill in array with given position
        foreach ($injections as $injection) {
            if ($injection['position']) {
                $position = $injection['position']-1;
            } else {
                $position = $injectionPosition;
            }

            $result[$injection['position']-1] = $injection['instance'];

            $injectionPosition++;
        }

        // iterate over remaining arguments and fill in the holes
        foreach ($result as $key => $value) {
            if ($result[$key] === null) {
                $result[$key] = array_shift($arguments);
            }
        }

        return $result;
    }

    /**
     * Creates and returns an empty array for the three types of injections
     *
     * This method is intend to create and return an empty array for the three types of injections.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array For the three types of injections
     * @access private
     */
    private function _initEmptyInjectionContainer()
    {
        return array(
            'constructor' => array(),
            'setter'      => array(),
            'property'    => array()
        );
    }
}
