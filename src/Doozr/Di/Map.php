<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Di - Map.
 *
 * Map.php - Abstract base for Di-Map implementations like Annotation, Static,
 * Fluent, Typehint. The difference in implementation is how the maps are generated.
 * So because of that this is just an abstract base.
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
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Di/Constants.php';

/**
 * Doozr - Di - Map.
 *
 * Abstract base for Di-Map implementations like Annotation, Static,
 * Fluent, Typehint. The difference in implementation is how the maps are generated.
 * So because of that this is just an abstract base.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @link       https://github.com/clickalicious/Di
 * @abstract
 */
abstract class Doozr_Di_Map
{
    /**
     * Dependencies as collection (array).
     *
     * @var Doozr_Di_Collection
     */
    protected $collection;

    /**
     * Doozr_Di_Dependency instance to clone objects from.
     *
     * @var Doozr_Di_Dependency
     */
    protected $dependency;

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Sets the collection of the map instance
     * This method is intend to set the collection (Doozr_Di_Collection) of this map instance.
     *
     * @param Doozr_Di_Collection $collection The collection to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setCollection(Doozr_Di_Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Setter for collection.
     *
     * @param Doozr_Di_Collection $collection The collection of the collection.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function collection(Doozr_Di_Collection $collection)
    {
        $this->setCollection($collection);

        return $this;
    }

    /**
     * Returns the collection of the map instance
     * This method is intend to return the collection (Doozr_Di_Collection) of this map instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Collection if set otherwise NULL
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Imports a collection of dependencies
     * This method is intend to import a collection of dependencies (Doozr_Di_Collection).
     *
     * @param Doozr_Di_Collection $collection An instance of
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success
     */
    public function import(Doozr_Di_Collection $collection)
    {
        foreach ($collection as $id => $dependencies) {

            // Don't import duplicates!
            if (null === $this->getCollection()->getDependencies($id)) {
                foreach ($dependencies as $position => $dependency) {
                    $arguments   = $collection->getArgumentsById($id);
                    $className   = $collection->getClassNameById($id);
                    $constructor = $collection->getConstructorById($id);

                    // Have arguments set?
                    if (null !== $arguments) {
                        $this->getCollection()->setArguments($id, $arguments);
                    }

                    $this->getCollection()->addDependency($id, $className, $constructor, $dependency);
                }
            }
        }

        return true;
    }

    /**
     * Exports a collection of dependencies.
     *
     * This method is intend to export a collection of dependencies (Doozr_Di_Collection)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Collection A collection of dependencies
     */
    public function export()
    {
        return $this->getCollection();
    }

    /**
     * Wires existing instances of classes.
     * This method is intend to connect existing instances from argument $matrix or retrieved from globals
     * with the existing map. This connection is identified by the "id" in the map and the "key" in the array.
     *
     * @param array $matrix A matrix defining the relation between an Id and an Instance as key => value pair
     * @param int   $mode   This can be either WIRE_MODE_MANUAL or WIRE_MODE_AUTOMATIC
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     *
     * @throws Doozr_Di_Exception
     */
    public function wire(array $matrix = [], $mode = Doozr_Di_Constants::WIRE_MODE_AUTOMATIC)
    {
        // If automatic mode is enabled
        if (Doozr_Di_Constants::WIRE_MODE_AUTOMATIC === $mode) {
            // @todo Retrieve also instances from di container for wiring!!!
            $matrix = array_merge($this->retrieveGlobals(), $matrix);
        }

        if (empty($matrix)) {
            throw new Doozr_Di_Exception(
                'Error while wiring instances! Mode manual requires an array containing key => value pairs.'
            );
        }

        // Now we connect our map-recipe with existing (real) instances
        $this->wireDependenciesWithInstances($matrix);

        // Success
        return true;
    }

    /**
     * Resets the state of this class
     * This method is intend to reset the state of this class. Currently only used for unit-testing.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function reset()
    {
        $this->getCollection()->reset();

        return $this;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PROTECTED
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Adds the given raw dependencies (array) to the collection.
     *
     * @param array $rawDependencies The dependencies as raw array structure (may come from file in JSON-syntax)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function addRawDependenciesToCollection(array $rawDependencies)
    {
        // Iterate all dependencies ...
        foreach ($rawDependencies as $id => $setup) {

            // Have arguments set?
            if (true === isset($setup['arguments']) && true === is_array($setup['arguments'])) {
                $this->getCollection()->setArguments($id, $setup['arguments']);
            }

            if (true === isset($setup['dependencies'])) {
                // Iterate every dependency for current id
                foreach ($setup['dependencies'] as $recipe) {

                    // Clone base dependency object so we don't need a new operator here
                    $dependency = clone $this->dependency;
                    $dependency->import($recipe);

                    // Retrieve constructor - the configured one is prioritized ...
                    $constructor = (true === isset($setup['constructor'])) ? $setup['constructor'] : null;

                    // Add current dependency to list of dependencies
                    $this->getCollection()->addDependency($id, $setup['className'], $constructor, $dependency);
                }

            } else {
                // No dependencies? We will keep those classes loadable with default behavior!
                $this->getCollection()->addMapping($id, $setup['className']);

            }
        }
    }

    /**
     * Wires the map with given (existing) instances
     * This method is used to wire the instances given via arguments $matrix with the corresponding Id's from
     * the static map.
     *
     * @param array $matrix    The matrix containing the instances for wiring (id => instance)
     * @param bool  $overwrite TRUE to overwrite previous instances, FALSE to do not (default).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success
     */
    protected function wireDependenciesWithInstances(array $matrix, $overwrite = false)
    {
        foreach ($this->getCollection() as $id => $dependencies) {
            foreach ($dependencies as $dependency) {
                /* @var $dependency Doozr_Di_Dependency */

                // If dependency is set to NULL set dependency retrieved from given matrix
                if (null === $dependency->getInstance() || true === $overwrite) {

                    // Everything's getting wired need a link!
                    if (null !== $dependency->getLink()) {
                        if (true === isset($matrix[$dependency->getLink()])) {
                            $dependency->setInstance(
                                $matrix[$dependency->getLink()]
                            );
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Returns all variables from global scope
     * This method is intend to return all variables from PHP's global scope.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The defined variables from global scope
     */
    protected function retrieveGlobals()
    {
        // Retrieve globals and return them
        global $GLOBALS;

        return $GLOBALS;
    }

    /**
     * Setter for dependency.
     *
     * @param string $dependency The dependency of the dependency.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setDependency($dependency)
    {
        $this->dependency = $dependency;
    }

    /**
     * Setter for dependency.
     *
     * @param string $dependency The dependency of the dependency.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function dependency($dependency)
    {
        $this->setDependency($dependency);

        return $this;
    }

    /**
     * Getter for Dependency.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Dependency The Dependency if set, otherwise NULL
     */
    protected function getDependency()
    {
        return $this->dependency;
    }
}
