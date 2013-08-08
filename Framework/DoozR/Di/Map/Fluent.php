<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Di Map Fluent
 *
 * Fluent.php - Fluent map class of the Di-Framework
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
 * @subpackage DoozR_Di_Framework_Map_Fluent
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2012 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id: $
 * @link       https://github.com/clickalicious/Di
 * @see        -
 * @since      -
 */

require_once DI_PATH_LIB_DI.'Map.php';
require_once DI_PATH_LIB_DI.'Factory.php';
require_once DI_PATH_LIB_DI.'Container.php';
require_once DI_PATH_LIB_DI.'Dependency.php';
require_once DI_PATH_LIB_DI.'Collection.php';

/**
 * Di Map Fluent
 *
 * Fluent map class of the Di-Framework
 *
 * @category   Di
 * @package    DoozR_Di_Framework
 * @subpackage DoozR_Di_Framework_Map_Fluent
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2012 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link       https://github.com/clickalicious/Di
 * @see        -
 * @since      -
 */
class DoozR_Di_Map_Fluent extends DoozR_Di_Map
{
    /**
     * The current active classname to add dependencies for
     *
     * @var string
     * @access private
     */
    private $_classname;

    /**
     * The last active classname
     *
     * @var string
     * @access private
     */
    private $_lastClassname;

    /**
     * The base dependency object
     *
     * @var DoozR_Di_Dependency
     * @access private
     */
    private $_dependency;

    /**
     * The current active dependency
     *
     * @var DoozR_Di_Dependency
     * @access private
     */
    private $_current;


    /*******************************************************************************************************************
     * PHP CONSTRUCT
     ******************************************************************************************************************/

    /**
     * Constructor
     *
     * Constructor of this class
     *
     * @param DoozR_Di_Collection $collection An instance of DoozR_Di_Collection used to store dependencies in
     * @param DoozR_Di_Dependency $dependency An instance of DoozR_Di_Dependency used as base object for cloning new dependencies
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __construct(DoozR_Di_Collection $collection, DoozR_Di_Dependency $dependency)
    {
        // store instances
        $this->collection  = $collection;
        $this->_dependency = $dependency;
    }

    /*******************************************************************************************************************
     * PUBLIC API
     ******************************************************************************************************************/

    /**
     * Empty container method to keep the interface consistent with other DoozR_Di_Map_* classes
     *
     * This method is intend as empty container method to keep the interface consistent with
     * other DoozR_Di_Map_* classes.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Di_Map_Fluent The current instance for chaining method calls
     * @access public
     */
    public function generate()
    {
        // empty container method to keep the interface consistent with
        // DoozR_Di_Map_Static and DoozR_Di_Map_Annotation
        return $this;
    }

    /**
     * Setter for the name of the class which has dependencies
     *
     * This method is intend to set the name of the class which has dependencies.
     *
     * @param string $classname   The name of the class which has dependencies
     * @param mixed  $arguments   The arguments to pass to constructor of class
     * @param mixed  $constructor The constructor of the class
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Di_Map_Fluent Instance of this class (for method chaining)
     * @access public
     */
    public function classname($classname, $arguments = null, $constructor = null)
    {
        // flush maybe exisiting content
        $this->_flush();

        // store classname
        $this->_classname = $classname;

        // add arguments if given
        if (!is_null($arguments) && is_array($arguments)) {
            $this->collection->addArguments($classname, $arguments);
        }

        // add constructor if given
        if (!is_null($constructor)) {
            $this->collection->setConstructor($classname, $constructor);
        }

        // fluent interface
        return $this;
    }

    /**
     * Setter for the name of the dependency class
     *
     * This method is intend to set the name of the dependency class.
     *
     * @param string $classname The name of the dependency class
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Di_Map_Fluent Instance of this class (for method chaining)
     * @access public
     */
    public function dependsOn($classname)
    {
        // store last created temporary dependency
        if ($this->_current) {
            $this->_flush();
        }

        /* @var $this->_current DoozR_Di_Dependency */
        $this->_current = clone $this->_dependency;

        $this->_current->setClassname($classname);

        // fluent interface
        return $this;
    }

    /**
     * Setter for the identifier of the dependency class
     *
     * This method is intend to set the identifier of the dependency class.
     *
     * @param string $identifier The identifier of the dependency class
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Di_Map_Fluent Instance of this class (for method chaining)
     * @access public
     */
    public function identifier($identifier)
    {
        /* @var $this->_current DoozR_Di_Dependency */
        $this->_current->setIdentifier($identifier);

        // fluent interface
        return $this;
    }

    /**
     * Setter for the identifier of the dependency class
     *
     * This method is intend to set the identifier of the dependency class.
     *
     * @param string $identifier The identifier of the dependency class
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Di_Map_Fluent Instance of this class (for method chaining)
     * @access public
     */
    public function id($identifier)
    {
        return $this->identifier($identifier);
    }

    /**
     * Setter for the instance of the dependency class
     *
     * This method is intend to set the instance of the dependency class.
     *
     * @param mixed $instance The instance to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Di_Map_Fluent Instance of this class (for method chaining)
     * @access public
     */
    public function instance($instance)
    {
        /* @var $this->_current DoozR_Di_Dependency */
        $this->_current->setInstance($instance);

        // fluent interface
        return $this;
    }

    /**
     * Setter for the configuration of the dependency class
     *
     * This method is intend to set the configuration of the dependency class.
     *
     * @param array $configuration The configuration to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Di_Map_Fluent Instance of this class (for method chaining)
     * @access public
     */
    public function configuration(array $configuration)
    {
        /* @var $this->_current DoozR_Di_Dependency */
        $this->_current->setConfiguration($configuration);

        // fluent interface
        return $this;
    }

    /**
     * Setter for the arguments of the dependency class
     *
     * This method is intend to set the arguments of the dependency class.
     *
     * @param array $arguments The arguments to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Di_Map_Fluent Instance of this class (for method chaining)
     * @access public
     */
    public function arguments(array $arguments)
    {
        /* @var $this->_current DoozR_Di_Dependency */
        $this->_current->setArguments($arguments);

        // fluent interface
        return $this;
    }

    /**
     * Stores the name of the last processed class
     *
     * This method is required for the fluent interface.
     *
     * @param string $classname The name of the last processed class
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setLastProcessedClass($classname)
    {
        $this->_lastClassname = $classname;
    }

    /**
     * Returns the name of the last processed class
     *
     * This method is intend to return the name of the last processed class
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The name of the last processed class
     * @access public
     */
    public function getLastProcessedClass()
    {
        return $this->_lastClassname;
    }

    /**
     * Shortcut to DoozR_Di_Map::wire()
     *
     * This method is a shortcut to DoozR_Di_Map::wire().
     *
     * @param integer $mode   The mode to use for wiring
     * @param array   $matrix The wire matrix containing instances to wire
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Di_Map_Fluent Instance of this class (for method chaining)
     * @access public
     */
    public function wire($mode = self::WIRE_MODE_AUTOMATIC, array $matrix = array())
    {
        // flush maybe existing temporary content
        $this->_flush();

        parent::wire($mode, $matrix);

        // fluent interface
        return $this;
    }

    /**
     * Setter for the configuration of the dependency class
     *
     * This method is intend to set the configuration of the dependency class.
     *
     * @param boolean $returnContainer TRUE to return container instance, FALSE to return map instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Di_Container Instance of the container for active namespace
     * @access public
     */
    public function store($returnContainer = true)
    {
        // reset state
        $this->_reset();

        // store this map to container
        $this->container->setMap($this);

        if ($returnContainer === true) {
            return $this->container;

        } else {
            // fluent interface
            return $this;
        }
    }

    /**
     * Shortcut for build()
     *
     * This method is intend to act as a shortcut to build().
     *
     * @param array   $arguments The arguments to pass to build()
     * @param string  $classname The (optional) name of the class to build instance of
     * @param boolean $wire      TRUE to automatic wire instances, otherwise FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Di_Map_Fluent Instance of this class (for method chaining)
     * @access public
     */
    public function build($arguments = null, $classname = null, $wire = true)
    {
        $classname = ($classname) ? $classname : $this->_classname;

        if ($wire) {
            $this->wire(DoozR_Di_Map::WIRE_MODE_AUTOMATIC);
        }

        return $this->store()->build($classname, $arguments);
    }


    /*******************************************************************************************************************
     * PRIVATE
     ******************************************************************************************************************/

    /**
     * Resets the state of this instance to default
     *
     * This method is intend to set the configuration of the dependency class.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _reset()
    {
        $this->_classname = null;
        $this->_dependency = null;
    }

    /**
     * Flushes the content
     *
     * This method is intend to flush the temporary content.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _flush()
    {
        $this->_lastClassname = $this->_classname;

        if ($this->_current !== null) {
            $this->collection->addDependency($this->_classname, $this->_current);
        }

        $this->_current = null;
    }
}
