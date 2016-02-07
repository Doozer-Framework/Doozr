<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Di - Container.
 *
 * Container.php - The Di Container is responsible for
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
 * @link       https://github.com/clickalicious/Di
 */
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Di/Constants.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Di/Map.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Di/Dependency.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Di/Factory.php';

/**
 * Doozr - Di - Container.
 *
 * Di container.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @link       https://github.com/clickalicious/Di
 */
class Doozr_Di_Container
{
    /**
     * Static array of dependency maps. Cause it's static it contains the dependency
     * maps of all containers across all instances.
     *
     * @var array
     * @static
     */
    private static $dependencyMaps = [];

    /**
     * The scope of this container instance.
     *
     * @var string
     */
    protected $scope;

    /**
     * The mode the instance operates in.
     * Can be either ...
     *
     * @var int
     */
    protected $mode;

    /**
     * Contains container instances.
     *
     * @var object
     * @static
     */
    private static $instances = [];

    /**
     * Cache for instances created.
     * Indexed by $id.
     *
     * @var array
     * @static
     */
    private static $cache = [];

    /**
     * Instance of Doozr_Di_Factory for creating instances.
     *
     * @var Doozr_Di_Factory
     */
    protected $factory;

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param string $scope Scope used for handling resources, references and so on.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    private function __construct($scope)
    {
        $this
            ->scope($scope);
    }

    /**
     * Singleton Constructor.
     *
     * This method is intend to construct and return a singleton instance of Doozr_Di_Container. Each container
     * singleton is bound to a scope (eg. 'default'). By passing a scope through argument $scope you are able to
     * create more than one instance of container if needed/required by your application.
     *
     * @param string $scope The scope of this instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Container Instance
     * @static
     */
    public static function getInstance($scope = Doozr_Di_Constants::DEFAULT_SCOPE)
    {
        // Check if instance exists ...
        if (false === isset(self::$instances[$scope])) {
            self::$instances[$scope] = new self(
                $scope
            );
        }

        // Return instance
        return self::$instances[$scope];
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for Factory.
     *
     * @param Doozr_Di_Factory $factory The factory instance to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setFactory(Doozr_Di_Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Fluent: Setter for Factory.
     *
     * @param Doozr_Di_Factory $factory The factory instance to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function factory(Doozr_Di_Factory $factory)
    {
        $this->setFactory($factory);

        return $this;
    }

    /**
     * Getter for Factory.
     *
     * This method is intend to return the instance of Doozr_Di_Factory
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Factory The instance of Doozr_Di_Factory if set, otherwise NULL
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Adds a Doozr_Di_Map to existing.
     *
     * @param Doozr_Di_Map $map Map to merge in
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool|null TRUE on success, otherwise FALSE
     */
    public function addToMap(Doozr_Di_Map $map)
    {
        // Merge
        $existingMap = $this->getMap();

        if (null !== $existingMap) {
            $map = $this->mergeMaps($existingMap, $map);
        }

        $this->setDependencyMap($this->getScope(), $map);
    }

    /**
     * Setter for map.
     *
     * @param Doozr_Di_Map $map The map to merge in
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setMap(Doozr_Di_Map $map)
    {
        $this->setDependencyMap($this->getScope(), $map);
    }

    /**
     * Fluent: Setter for map.
     *
     * @param Doozr_Di_Map $map The map to merge in
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function map(Doozr_Di_Map $map)
    {
        $this->setMap($map);

        return $this;
    }

    /**
     * Returns the dependency map of this container.
     *
     * This method is intend to return the dependency map instance of this container instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Map_Interface|Doozr_Di_Map_Static|Doozr_Di_Map_Fluent|Doozr_Di_Map_Annotation|
     *         Doozr_Di_Map_Typehint The dependency map instance as Doozr_Di_Map if set, otherwise NULL
     */
    public function getMap()
    {
        return $this->getDependencyMap($this->getScope());
    }

    /**
     * Returns the Dependency-Map from another scope.
     *
     * This method is intend to return the Dependency-Map of another scope.
     *
     * @param string $scope The scope to load map from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Map_Interface The dependency instance from another scope
     *
     * @throws Doozr_Di_Exception
     */
    public function getMapFromOtherScope($scope)
    {
        if (null === $map = $this->getDependencyMap($scope)) {
            throw new Doozr_Di_Exception(
                sprintf(
                    'Dependency map could not be found. Dependency map for scope "%s" does not exist.',
                    $scope
                )
            );
        }

        // Return requested map
        return $map;
    }

    /**
     * Imports a Dependency-Map from another scope.
     *
     * This method is intend to import a Dependency-Map from another scope.
     *
     * @param string $scope The scope to load dependency map from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function importMapFromOtherScope($scope)
    {
        $this->setDependencyMap($this->getScope(), $this->getMapFromOtherScope($scope));
    }

    /**
     * Builds an instance of requested Id with all dependencies from recipe in one single call.
     *
     * @param string $id        Id of class to build
     * @param array  $arguments Arguments to pass to class (works only in dynamic mode)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return object|mixed Instance of the given class
     *
     * @throws Doozr_Di_Exception
     */
    public function build($id, array $arguments = null)
    {
        // Load a fresh instance of {id}
        #if (false === $instance = self::loadFromCacheById($id)) {

            // Check if all required dependencies are set [Doozr_Di_Factory, Doozr_Di_Map, ...]
            if (!$this->requirementsFulfilled()) {
                throw new Doozr_Di_Exception(
                    'Error building an instance. Requirements not fulfilled. Provide all required dependencies.'
                );
            }

            // Receive recipe for requested Id as well as name of class ...
            $recipe = $this->getMap()->getCollection()->getRecipeById($id);

            // Check for arguments override
            if (null === $arguments) {
                $arguments = (null !== $recipe['arguments']) ? $recipe['arguments'] : [];
            }

            // Check if className could be retrieved
            if (null === $recipe['className']) {
                throw new Doozr_Di_Exception(
                    sprintf(
                        'Please provide a configuration for the Id "%s". Or did you mean: "%s"?',
                        $id,
                        $this->getSimilar($id)
                    )
                );
            }

            // Build the requested instance ...
            $instance = $this->getFactory()->build(
                $recipe,
                $arguments
            );

            // Store in cache!
            #self::addInstanceToCache($id, $instance);
        #}

        // ... and return it
        return $instance;
    }

    /**
     * Checks if the requirements are fulfilled.
     *
     * This method is intend to check if the requirements are fulfilled.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if requirements fulfilled, otherwise FALSE
     */
    public function requirementsFulfilled()
    {
        // Check map = requirements fulfilled
        if ($this->getMap() && $this->getMap()->getCollection()) {
            return true;
        }

        return false;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for scope.
     *
     * @param string $scope The scope of the scope.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setScope($scope)
    {
        $this->scope = $scope;
    }

    /**
     * Fluent: Setter for scope.
     *
     * @param string $scope The scope of the scope.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function scope($scope)
    {
        $this->setScope($scope);

        return $this;
    }

    /**
     * Getter for Scope.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The Scope if set, otherwise NULL
     */
    protected function getScope()
    {
        return $this->scope;
    }

    /**
     * Merges a new map with existing one.
     *
     * @param Doozr_Di_Map $target The map in which the $source is merged into
     * @param Doozr_Di_Map $source The map which is merged into $target
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Map The current instance of the Container for chaining/fluent-interface
     */
    protected function mergeMaps(Doozr_Di_Map $target, Doozr_Di_Map $source)
    {
        // Import of dependencies is built in functionality of map
        $target->import(
            $source->export()
        );

        return $target;
    }

    /**
     * Try to give a hint for a misspelled configuration entry.
     *
     * @param string $misspelled The misspelled id
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The resulting comma seperated list of hints for possible entries
     */
    protected function getSimilar($misspelled)
    {
        $result            = [];
        $soundexMisspelled = soundex($misspelled);

        foreach ($this->getMap()->getCollection() as $id => $dependency) {
            if ($soundexMisspelled === soundex($id)) {
                $result[] = $id;
            }
        }

        return implode(', ', $result);
    }

    /**
     * Returns a previously created instance from runtime cache.
     * To speedup execution and mapping of dependencies.
     *
     * @param string $id The Id to return dependencies for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return object|bool The loaded instance on success, otherwise FALSE
     * @static
     */
    protected static function loadFromCacheById($id)
    {
        $instance = false;

        if (true === isset(self::$cache[$id])) {
            $instance = self::$cache[$id];
        }

        return $instance;
    }

    /**
     * Stores a passed instance to local runtime cache.
     *
     * @param string $id       The Id to store instance under
     * @param object $instance The instance to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @static
     */
    protected static function addInstanceToCache($id, $instance)
    {
        self::$cache[$id] = $instance;
    }



    /**
     *
     */
    public function setDependencyMap($identifier, Doozr_Di_Map_Interface $map)
    {
        self::$dependencyMaps[$identifier] = $map;
    }

    public function dependencyMap($identifier, Doozr_Di_Map_Interface $map)
    {
        $this->setDependencyMap($identifier, $map);

        return $this;
    }

    public function getDependencyMap($identifier)
    {
        return (true === isset(self::$dependencyMaps[$identifier])) ? self::$dependencyMaps[$identifier] : null;
    }
}
