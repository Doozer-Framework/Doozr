<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Di - Collection
 *
 * Collection.php - Di matrix for mapping a "classname" [indexedByClassname] or an
 * "id" [indexedById] to a collection of dependencies. So the responsibility of
 * this class is collecting and holding this collection of dependencies for a class
 * indexed by its name or id.
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
 * @package    Doozr_Di
 * @subpackage Doozr_Di_Collection
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Di
 */

require_once 'Exception.php';

/**
 * Doozr - Di - Collection
 *
 * Di matrix for mapping a "classname" [indexedByClassname] or an
 * "id" [indexedById] to a collection of dependencies. So the responsibility of
 * this class is collecting and holding this collection of dependencies for a class
 * indexed by its name or id.
 *
 * @category   Doozr
 * @package    Doozr_Di
 * @subpackage Doozr_Di_Collection
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link       https://github.com/clickalicious/Di
 */
class Doozr_Di_Collection
    implements
    ArrayAccess,
    Iterator
{
    /**
     * Mapping from Id to Classname
     *
     * @var array
     * @access protected
     */
    protected $mapIdToClassname = [];

    /**
     * The position of Iterator
     *
     * @var int
     * @access protected
     */
    protected $position = 0;

    /**
     * Numerical index to translate position of Iterator to array index
     *
     * @var array
     * @access protected
     */
    protected $numericalIndex = [];

    /**
     * Collection of arguments to pass to final class
     *
     * @var array
     * @access protected
     */
    protected $argumentsById = [];

    /**
     * Constructor method if not default
     * (eg. for singleton classes)
     *
     * @var array
     * @access protected
     */
    protected $constructorById = [];

    /**
     * Name of the class having the dependency.
     * Indexed by Id.
     *
     * @var array
     * @access protected
     */
    protected $classnameById = [];

    /**
     * Dependencies indexed by id of service.
     *
     * @var array
     * @access protected
     */
    protected $dependenciesById = [];

    /**
     * Dependencies indexed by dependency (classname?!)
     *
     * @var array
     * @access protected
     */
    protected $indexedByClassname = [];

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Adds a dependency to collection and indexes it by id, classname.
     *
     * @param string              $id         Identifier to file the reference under
     * @param string              $classname  Name of the class having this dependency
     * @param Doozr_Di_Dependency $dependency Dependency recipe
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success
     * @access public
     */
    public function addDependency($id, $classname, $constructor, Doozr_Di_Dependency $dependency)
    {
        // Prepare indices ...
        $this->initDependenciesById($id);
        $this->initIndexByClassname($classname);

        // Build index to speedup lookups
        $this->dependenciesById[$id][]          = $dependency;
        $index                                  = count($this->dependenciesById[$id]) - 1;
        $this->indexedByClassname[$classname][] = &$this->dependenciesById[$id][$index];
        $this->classnameById[$id]               = $classname;
        $this->constructorById[$id]             = $constructor;

        // update the numerical index for iterator access
        $this->numericalIndex = array_keys($this->dependenciesById);

        return true;
    }

    public function addMapping($id, $classname)
    {
        $this->classnameById[$id] = $classname;

        //
    }

    public function reset()
    {
        $this->mapIdToClassname = [];
        $this->position = 0;
        $this->numericalIndex = [];
        $this->argumentsById = [];
        $this->constructorById = [];
        $this->classnameById = [];
        $this->dependenciesById = [];
        $this->indexedByClassname = [];
     }

    /**
     * Adds an array of dependencies to collection.
     *
     * @param string               $id         Identifier to file the reference under
     * @param string $classname    The name of the class which depends on the $dependency
     * @param array  $dependencies The dependency recipe as Doozr_Di_Dependency object
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success, otherwise FALSE
     * @access public
     * @throws Doozr_Di_Exception
     */
    public function addDependencies($id, $classname, $constructor, array $dependencies)
    {
        $result = true;

        foreach ($dependencies as $dependency) {
            $result = $result && $this->addDependency($id, $classname, $constructor, $dependency);

            if (!$result) {
                throw new Doozr_Di_Exception(
                    sprintf(
                        'Dependencies could not be added! The dependency with target: "%s" produced an error. ' .
                        'No Dependencies added.',
                        $dependency->getTarget()
                    )
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
     * @param string $id        Id of the class to add arguments for
     * @param array  $arguments The arguments to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setArguments($id, array $arguments)
    {
        $this->argumentsById[$id] = $arguments;
    }

    /**
     * Returns the arguments required to pass to a class having dependencies.
     *
     * @param string $id The id of the class to return arguments for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array|null Arguments as array if set, otherwise NULL
     * @access public
     */
    public function getArguments($id)
    {
        return (isset($this->argumentsById[$id])) ?
            $this->argumentsById[$id] :
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
        $this->constructorById[$classname] = $constructor;
    }

    /**
     * Getter for constructor
     *
     * This method is intend to return the name of the constructor method
     * of the target class.
     *
     * @param string $id The Id of the class to return constructor for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING name of the constructor method if set, otherwise NULL
     * @access public
     */
    public function getConstructorById($id)
    {
        return (isset($this->constructorById[$id])) ?
            $this->constructorById[$id] :
            null;
    }

    /**
     * Returns the recipe by id.
     *
     * @param string $id The name of the class to return recipe for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Arguments as array, or NULL if no arguments set
     * @access public
     */
    public function getRecipeById($id)
    {
        $result = [
            'classname'    => (isset($this->classnameById[$id])) ?
                $this->classnameById[$id] :
                null,
            'arguments'    => (isset($this->argumentsById[$id])) ?
                $this->argumentsById[$id] :
                null,
            'constructor'  => (isset($this->constructorById[$id])) ?
                $this->constructorById[$id] :
                null,
            'dependencies' => (isset($this->dependenciesById[$id])) ?
                $this->dependenciesById[$id] :
                null
        ];

        return $result;
    }

    /**
     * Returns the classname for a passed Id.
     *
     * @param string $id The id to return classname for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The name of the class if set, otherwise NULL
     * @access public
     */
    public function getClassnameById($id)
    {
        return (isset($this->classnameById[$id])) ? $this->classnameById[$id] : null;
    }

    /**
     * Returns dependencies for passed id.
     *
     * @param string $id The id of the class to return dependencies for.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Di_Dependency|null Dependency if set, otherwise NULL
     * @access public
     */
    public function getDependencies($id)
    {
        return (isset($this->dependenciesById[$id])) ?
            $this->dependenciesById[$id] :
            null;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | ITERATOR
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Implements rewind
     *
     * @return void
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Implements current
     *
     * @return mixed current element from array
     */
    public function current()
    {
        return $this->dependenciesById[$this->numericalIndex[$this->position]];
    }

    /**
     * Implements key
     *
     * @return mixed The current key as INTEGER or STRING (depends on array)
     */
    public function key()
    {
        return $this->numericalIndex[$this->position];
    }

    /**
     * Implements next
     *
     * @return void
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Implements valid
     *
     * @return bool TRUE if current element exists, otherwise FALSE
     */
    public function valid()
    {
        return isset($this->numericalIndex[$this->position]);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | ARRAY ACCESS
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Implements offsetExists
     *
     * @param string $offset The offset to check
     *
     * @return bool TRUE if offset is set, otherwise FALSE
     */
    public function offsetExists($offset)
    {
        return isset($this->dependenciesById[$offset]);
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
        return $this->dependenciesById[$offset];
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
        $this->dependenciesById[$offset] = $value;
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
        unset($this->dependenciesById[$offset]);
    }

    /**
     * Initializes the index by id.
     *
     * @param string $id The id of the dependency
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function initDependenciesById($id)
    {
        // Create an entry array if not already created (lazy init)
        if (false === isset($this->dependenciesById[$id])) {
            $this->dependenciesById[$id] = [];
        }
    }

    /**
     * Initializes the index by classname.
     *
     * @param string $classname The name of the class
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function initIndexByClassname($classname)
    {
        // Create an entry array if not already created (lazy init)
        if (false === isset($this->indexedByClassname[$classname])) {
            $this->indexedByClassname[$classname] = [];
        }
    }
}
