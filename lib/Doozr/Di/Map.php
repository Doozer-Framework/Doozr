<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Di - Map
 *
 * Map.php - Map class of the Di-Library
 *
 * PHP versions 5.5
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
 * @subpackage Doozr_Di_Map
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Exception.php';

/**
 * Doozr - Di - Map
 *
 * Map class of the Di-Library
 *
 * @category   Doozr
 * @package    Doozr_Di
 * @subpackage Doozr_Di_Map
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link       https://github.com/clickalicious/Di
 */
class Doozr_Di_Map
{
    /**
     * The name/identifier of this map
     *
     * @var string
     * @access protected
     */
    protected $namespace = self::DEFAULT_NAMESPACE;

    /**
     * Contains all dependencies as collection (array)
     *
     * @var Doozr_Di_Collection
     * @access protected
     */
    protected $collection;

    /**
     * A Doozr_Di_Dependency instance to clone objects from
     *
     * @var Doozr_Di_Dependency
     * @access protected
     */
    protected $dependency;

    /**
     * A (*) parser instance
     *
     * @var Doozr_Di_Parser_Interface
     * @access protected
     */
    protected $parser;

    /**
     * WIRE_MODE_MANUAL
     * Wiring is done manually by you
     *
     * @var int
     * @access public
     */
    const WIRE_MODE_MANUAL = 1;

    /**
     * WIRE_MODE_AUTOMATIC = Wiring is done automatically
     *
     * @var int
     * @access public
     */
    const WIRE_MODE_AUTOMATIC = 2;

    /**
     * Default namespace
     *
     * @var string
     * @access public
     */
    const DEFAULT_NAMESPACE = 'Di';

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Sets the collection of the map instance
     *
     * This method is intend to set the collection (Doozr_Di_Collection) of
     * this map instance.
     *
     * @param Doozr_Di_Collection $collection The collection to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setCollection(Doozr_Di_Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Returns the collection of the map instance
     *
     * This method is intend to return the collection (Doozr_Di_Collection) of
     * this map instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Di_Collection if set otherwise NULL
     * @access public
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Sets the identifier of the map instance
     *
     * This method is intend to set the identifier of the map instance.
     *
     * @param string $namespace The identifier to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Returns the identifier of the map instance
     *
     * This method is intend to return the identifier of the map instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The namespace of this instance
     * @access public
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Imports a collection of dependencies
     *
     * This method is intend to import a collection of dependencies (Doozr_Di_Collection)
     *
     * @param Doozr_Di_Collection $collection An instance of
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function import(Doozr_Di_Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Exports a collection of dependencies
     *
     * This method is intend to export a collection of dependencies (Doozr_Di_Collection)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Di_Collection A collection of dependencies
     * @access public
     */
    public function export()
    {
        return $this->collection;
    }

    /**
     * Wires existing instances of classes
     *
     * This method is intend to connect existing instances from argument $matrix or retrieved from globals
     * with the existing map. This connection is identified by the "id" in the map and the "key" in the
     * array.
     *
     * @param int $mode   This can be either WIRE_MODE_MANUAL or WIRE_MODE_AUTOMATIC
     * @param array   $matrix A matrix defining the relation between an Id and an Instance as key => value pair
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success, otherwise FALSE
     * @access public
     * @throws Doozr_Di_Exception
     */
    public function wire($mode = self::WIRE_MODE_AUTOMATIC, array $matrix = [])
    {
        if ($mode === self::WIRE_MODE_AUTOMATIC) {
            $matrix = $this->retrieveGlobals();
        }

        if (empty($matrix)) {
            throw new Doozr_Di_Exception(
                'Error while wiring instances! Mode manual requires an array containing key => value pairs.'
            );
        }

        // now we connect our map-setup with existing (real) instances
        $this->wireClassWithDependencies($matrix);

        // success
        return true;
    }

    /**
     * Resets the state of this class
     *
     * This method is intend to reset the state of this class. Currently only used for unit-testing.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function reset()
    {
        $this->collection = null;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PROTECTED
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Adds the given raw dependencies (array) to the collection for given classname
     *
     * This method is intend to add the given raw dependencies (array) to the collection for given classname.
     *
     * @param string $classname       The name of the class the dependencies belong to
     * @param array  $rawDependencies The dependencies as raw array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function addRawDependenciesToCollection($classname, array $rawDependencies)
    {
        // iterate raw dependencies, convert to Doozr_Di_Dependency and add it to Doozr_Di_Collection
        foreach ($rawDependencies as $target => $dependencies) {
            foreach ($dependencies as $setup) {

                if ($setup['type'] === 'constructor' && $target !== '__construct') {
                    $this->getCollection()->setConstructor($classname, $target);
                }

                // tricky clone base dependency object so we don't need a new operator here
                $dependency = clone $this->dependency;

                $dependency->setClassname($setup['class']);
                $dependency->setTarget($setup['target']);
                $dependency->setConfiguration(
                    array('type' => $setup['type'], 'value' => $setup['value'], 'position' => $setup['position'])
                );

                $this->getCollection()->addDependency($classname, $dependency);
            }
        }
    }

    /**
     * Wires the map with given (existing) instances
     *
     * This method is used to wire the instances given via arguments $matrix with
     * the corresponding Id's from the static map.
     *
     * @param array $matrix The matrix containing the instances for wiring (id => instance)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success
     * @access protected
     */
    protected function wireClassWithDependencies(array $matrix)
    {
        /* @var $this->collection Doozr_Di_Collection */
        foreach ($this->collection as $dependencies) {

            /* @var $dependency Doozr_Di_Dependency */
            foreach ($dependencies as $dependency) {

                // if dependency is set to NULL set dependency retrieved from given matrix
                if ($dependency->getInstance() === null) {
                    if (isset($matrix[$dependency->getTarget()])) {
                        $dependency->setInstance(
                            $matrix[$dependency->getTarget()]
                        );
                    }
                }
            }
        }

        // Success
        return true;
    }

    /**
     * Returns all variables from global scope
     *
     * This method is intend to return all variables from PHP's global scope.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The defined variables from global scope
     * @access protected
     */
    protected function retrieveGlobals()
    {
        // Retrieve globals and return them
        global $GLOBALS;
        return $GLOBALS;
    }
}
