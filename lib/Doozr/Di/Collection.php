<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Di - Collection
 *
 * Collection.php - Collection class of the Di-Library
 *
 * PHP versions 5.4
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
 * Collection class of the Di-Library
 *
 * @category   Doozr
 * @package    Doozr_Di
 * @subpackage Doozr_Di_Map
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
     * The position of Iterator
     *
     * @var int
     * @access protected
     */
    protected $position = 0;

    /**
     * The numerical index to translate
     * position of Iterator to array index
     *
     * @var array
     * @access protected
     */
    protected $numericalIndex;

    /**
     * The collection of arguments to pass to final class
     *
     * @var array
     * @access protected
     */
    protected $arguments = array();

    /**
     * Contains the constructor method if not default
     * (eg. for singleton classes)
     *
     * @var array
     * @access protected
     */
    protected $constructor = array();

    /**
     * Indexed dependencies with target as key
     *
     * @var array
     * @access protected
     */
    protected $indexByTarget = array();

    /**
     * Indexed dependencies with dependency as key
     *
     * @var array
     * @access protected
     */
    protected $indexByDependency = array();


    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Adds a dependency to collection
     *
     * @param string              $classname  The name of the class which depends on the $dependency
     * @param Doozr_Di_Dependency $dependency The dependency setup as Doozr_Di_Dependency object
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function addDependency($classname, Doozr_Di_Dependency $dependency)
    {
        // init (lazy stuff)
        $this->initIndexByTarget($classname);
        $this->initIndexByDependency($dependency->getClassname());

        // store dependency object in indices
        $this->indexByTarget[$classname][] = $dependency;

        // a ref to real object
        $this->indexByDependency[$dependency->getClassname()][]
            = &$this->indexByTarget[$classname][(count($this->indexByTarget[$classname])-1)];

        // update the numerical index for iterator access
        $this->numericalIndex = array_keys($this->indexByTarget);

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
     * @param array  $dependencies The dependency setup as Doozr_Di_Dependency object
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     * @throws Doozr_Di_Exception
     */
    public function addDependencies($classname, array $dependencies)
    {
        $result = true;

        foreach ($dependencies as $dependency) {
            $result = $result && $this->addDependency($classname, $dependency);

            if (!$result) {
                throw new Doozr_Di_Exception(
                    sprintf(
                        'Dependencies could not be added! The dependency with identifier: "%s" produced an error. ' .
                        'No Dependencies added.',
                        $dependency->getIdentifier()
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
     * @param string $classname The name of the class to add arguments for
     * @param array  $arguments The arguments to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function addArguments($classname, array $arguments)
    {
        $this->arguments[$classname] = $arguments;
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
        return (isset($this->arguments[$classname])) ?
            $this->arguments[$classname] :
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
        $this->constructor[$classname] = $constructor;
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
        return (isset($this->constructor[$classname])) ?
            $this->constructor[$classname] :
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
            'arguments'    => (isset($this->arguments[$classname])) ?
                $this->arguments[$classname] :
                null,
            'constructor'  => (isset($this->constructor[$classname])) ?
                $this->constructor[$classname] :
                null,
            'dependencies' => (isset($this->indexByTarget[$classname])) ?
                $this->indexByTarget[$classname] :
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
     * @return mixed Doozr_Di_Dependency if dependency is set, otherwise NULL
     * @access public
     */
    public function getDependencies($classname)
    {
        return (isset($this->indexByTarget[$classname])) ?
            $this->indexByTarget[$classname] :
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
        return $this->indexByTarget[$this->numericalIndex[$this->position]];
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
     * @return boolean TRUE if current element exists, otherwise FALSE
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
     * @return boolean TRUE if offset is set, otherwise FALSE
     */
    public function offsetExists($offset)
    {
        return isset($this->indexByTarget[$offset]);
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
        return $this->indexByTarget[$offset];
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
        $this->indexByTarget[$offset] = $value;
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
        unset($this->indexByTarget[$offset]);
    }

    /**
     * Inits the index - Index by Target
     *
     * This method is intend to init the index by target.
     *
     * @param string $target The name of the class
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function initIndexByTarget($target)
    {
        // create an entry array if not already created (lazy init)
        if (!isset($this->indexByTarget[$target])) {
            $this->indexByTarget[$target] = array();
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
     * @access protected
     */
    protected function initIndexByDependency($dependency)
    {
        // create an entry array if not already created (lazy init)
        if (!isset($this->indexByDependency[$dependency])) {
            $this->indexByDependency[$dependency] = array();
        }
    }
}
