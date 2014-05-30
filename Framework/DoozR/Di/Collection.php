<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Di Collection
 *
 * Collection.php - Collection class of the Di-Framework
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
 * @subpackage DoozR_Di_Framework_Collection
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2012 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Di
 */

require_once 'Exception.php';

/**
 * Di Collection
 *
 * Collection class of the Di-Framework
 *
 * @category   Di
 * @package    DoozR_Di_Framework
 * @subpackage DoozR_Di_Framework_Map
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2012 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link       https://github.com/clickalicious/Di
 */
class DoozR_Di_Collection implements ArrayAccess, Iterator
{
    /**
     * The position of Iterator
     *
     * @var integer
     * @access private
     */
    private $_position = 0;

    /**
     * The numerical index to translate
     * position of Iterator to array index
     *
     * @var array
     * @access private
     */
    private $_numericalIndex;

    /**
     * The collection of arguments to pass to final class
     *
     * @var array
     * @access private
     */
    private $_arguments = array();

    /**
     * Contains the constructor method if not default
     * (eg. for singleton classes)
     *
     * @var array
     * @access private
     */
    private $_constructor = array();

    /**
     * Indexed dependencies with target as key
     *
     * @var array
     * @access private
     */
    private $_indexByTarget = array();

    /**
     * Indexed dependencies with dependency as key
     *
     * @var array
     * @access private
     */
    private $_indexByDependency = array();


    /*******************************************************************************************************************
     * PUBLIC API
     ******************************************************************************************************************/

    /**
     * Adds a dependency to collection
     *
     * This method is intend to add a dependency to the collection of
     * dependencies.
     *
     * @param string        $classname  The name of the class which depends on the $dependency
     * @param DoozR_Di_Dependency $dependency The dependency setup as DoozR_Di_Dependency object
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function addDependency($classname, DoozR_Di_Dependency $dependency)
    {
        // init (lazy stuff)
        $this->_initIndexByTarget($classname);
        $this->_initIndexByDependency($dependency->getClassname());

        // store dependency object in indices
        $this->_indexByTarget[$classname][] = $dependency;

        // a ref to real object
        $this->_indexByDependency[$dependency->getClassname()][]
            = &$this->_indexByTarget[$classname][(count($this->_indexByTarget[$classname])-1)];

        // update the numerical index for iterator access
        $this->_numericalIndex = array_keys($this->_indexByTarget);

        // successs
        return true;
    }

    /**
     * Adds an array of dependencies to collection
     *
     * This method is intend to add an array of dependencies to the collection of
     * dependencies.
     *
     * @param string $classname    The name of the class which depends on the $dependency
     * @param array  $dependencies The dependency setup as DoozR_Di_Dependency object
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     * @throws DoozR_Di_Exception
     */
    public function addDependencies($classname, array $dependencies)
    {
        $result = true;

        foreach ($dependencies as $dependency) {
            $result = $result && $this->addDependency($classname, $dependency);

            if (!$result) {
                throw new DoozR_Di_Exception(
                    'Dependencies could not be added! The dependency with identifier: "'.
                    $dependency->getIdentifier().'" produced an error. No Dependencies added.'
                );
            }
        }

        return $result;
    }

    /**
     * Adds arguments of a target to collection
     *
     * This method is intend to add arguments of a target to collection.
     *
     * @param string $classname The name of the class to add arguments for
     * @param array  $arguments The arguments to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function addArguments($classname, array $arguments)
    {
        $this->_arguments[$classname] = $arguments;
    }

    /**
     * Returns the arguments of a target
     *
     * This method is intend to return the arguments of a target.
     *
     * @param string $classname The name of the class to return arguments for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Arguments as array, or NULL if no arguments set
     * @access public
     */
    public function getArguments($classname)
    {
        return (isset($this->_arguments[$classname])) ?
            $this->_arguments[$classname] :
            null;
    }

    /**
     * Setter for constructor
     *
     * This method is intend to set the name of the constructor method
     * of the target class.
     *
     * @param string $classname   The name of the class to set constructor for
     * @param string $constructor The name of the constructor method
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setConstructor($classname, $constructor)
    {
        $this->_constructor[$classname] = $constructor;
    }

    /**
     * Getter for constructor
     *
     * This method is intend to return the name of the constructor method
     * of the target class.
     *
     * @param string $classname The name of the class to return constructor for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING name of the constructor method if set, otherwise NULL
     * @access public
     */
    public function getConstructor($classname)
    {
        return (isset($this->_constructor[$classname])) ?
            $this->_constructor[$classname] :
            null;
    }

    /**
     * Returns the setup by a given target
     *
     * This method is intend to return the setup of a target.
     *
     * @param string $classname The name of the class to return setup for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Arguments as array, or NULL if no arguments set
     * @access public
     */
    public function getSetup($classname)
    {
        return array(
            'arguments'    => (isset($this->_arguments[$classname])) ?
                $this->_arguments[$classname] :
                null,
            'constructor'  => (isset($this->_constructor[$classname])) ?
                $this->_constructor[$classname] :
                null,
            'dependencies' => (isset($this->_indexByTarget[$classname])) ?
                $this->_indexByTarget[$classname] :
                null
        );
    }

    /**
     * Returns the dependencies for given classname
     *
     * This method is intend to return the dependencies for given classname.
     *
     * @param string $classname The name of the class to lookup dependencies for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed DoozR_Di_Dependency if dependency is set, otherwise NULL
     * @access public
     */
    public function getDependencies($classname)
    {
        return (isset($this->_indexByTarget[$classname])) ?
            $this->_indexByTarget[$classname] :
            null;
    }

    /*******************************************************************************************************************
     * ITERATOR
     ******************************************************************************************************************/

    /**
     * Implements rewind
     *
     * @return void
     */
    public function rewind()
    {
        $this->_position = 0;
    }

    /**
     * Implements current
     *
     * @return mixed current element from array
     */
    public function current()
    {
        return $this->_indexByTarget[$this->_numericalIndex[$this->_position]];
    }

    /**
     * Implements key
     *
     * @return mixed The current key as INTEGER or STRING (depends on array)
     */
    public function key()
    {
        return $this->_numericalIndex[$this->_position];
    }

    /**
     * Implements next
     *
     * @return void
     */
    public function next()
    {
        ++$this->_position;
    }

    /**
     * Implements valid
     *
     * @return boolean TRUE if current element exists, otherwise FALSE
     */
    public function valid()
    {
        return isset($this->_numericalIndex[$this->_position]);
    }

    /*******************************************************************************************************************
     * ARRAY ACCESS
     ******************************************************************************************************************/

    /**
     * Implements offsetExists
     *
     * @param string $offset The offset to check
     *
     * @return boolean TRUE if offset is set, otherwise FALSE
     */
    public function offsetExists($offset)
    {
        return isset($this->_indexByTarget[$offset]);
    }

    /**
     * Implements offsetGet
     *
     * @param string $offset The offset to return
     *
     * @return mixed The data from offset
     */
    public function offsetGet($offset)
    {
        return $this->_indexByTarget[$offset];
    }

    /**
     * Implements offsetSet
     *
     * @param string $offset The offset to set
     * @param mixed  $value  The value to set
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->_indexByTarget[$offset] = $value;
    }

    /**
     * Implements offsetUnset
     *
     * @param string $offset The offset to unset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->_indexByTarget[$offset]);
    }

    /*******************************************************************************************************************
     * PRIVATE
     ******************************************************************************************************************/

    /**
     * Inits the index - Index by Target
     *
     * This method is intend to init the index by target.
     *
     * @param string $target The name of the class
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _initIndexByTarget($target)
    {
        // create an entry array if not already created (lazy init)
        if (!isset($this->_indexByTarget[$target])) {
            $this->_indexByTarget[$target] = array();
        }
    }

    /**
     * Inits the index - Index by Dependency
     *
     * This method is intend to init the index by dependency.
     *
     * @param string $dependency The name of the class
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _initIndexByDependency($dependency)
    {
        // create an entry array if not already created (lazy init)
        if (!isset($this->_indexByDependency[$dependency])) {
            $this->_indexByDependency[$dependency] = array();
        }
    }
}
