<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Di - Container
 *
 * Container.php - Container class of the Di-Library
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
 * @subpackage Doozr_Di_Container
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Di
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Map.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Dependency.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Factory.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Exception.php';

/**
 * Doozr - Di - Container
 *
 * Container class of the Di-Library
 *
 * @category   Doozr
 * @package    Doozr_Di
 * @subpackage Doozr_Di_Container
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link       https://github.com/clickalicious/Di
 */
class Doozr_Di_Container
{
    /**
     * The namespace of this container instance
     *
     * @var string
     * @access protected
     */
    protected $namespace;

    /**
     * The runtimeEnvironment the instance operates in
     *
     * @var int
     * @access protected
     */
    protected $mode;

    /**
     * Contains the dependency maps of all containers
     *
     * @var array
     * @access private
     * @static
     */
    private static $dependencyMaps = array();

    /**
     * Contains container instances
     *
     * @var object
     * @access protected
     * @static
     */
    private static $instances = array();

    /**
     * Instance of Doozr_Di_Factory for creating instances
     *
     * @var Doozr_Di_Factory
     * @access protected
     */
    protected $factory;

    /**
     * Default namespace
     *
     * @var string
     * @access public
     */
    const DEFAULT_NAMESPACE   = 'Di';

    /**
     * The runtimeEnvironment used to handle maps
     * STATIC = Used for static
     *
     * @var int
     * @access public
     * @const
     */
    const MODE_STATIC = 1;

    /**
     * The runtimeEnvironment used to handle maps
     * DYNAMIC = Used for dynamic creation of instances
     *
     * @var int
     * @access public
     * @const
     */
    const MODE_DYNAMIC = 2;


    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Adds a Doozr_Di_Map to an existing Map by merging it in
     *
     * This method is intend to merge a new Doozr_Di_Map with an existing one.
     *
     * @param Doozr_Di_Map  $map      The map to merge in
     * @param bool $override TRUE to override the existing map, FALSE to merge the maps
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setMap(Doozr_Di_Map $map, $override = true)
    {
        if ($override === false) {
            $existingMap = $this->getMap();

            if ($existingMap) {
                $map = $this->mergeMaps($existingMap, $map);
            }
        }

        // store
        self::$dependencyMaps[$this->namespace] = $map;

        // success
        return true;
    }

    /**
     * Returns the dependency map of this container
     *
     * This method is intend to return the dependency map instance of this
     * container instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Di_Map The dependency map instance as Doozr_Di_Map if set, otherwise NULL
     * @access public
     */
    public function getMap()
    {
        return (isset(self::$dependencyMaps[$this->namespace]))
            ? self::$dependencyMaps[$this->namespace]
            : null;
    }

    /**
     * Returns the Dependency-Map from another namespace
     *
     * This method is intend to return the Dependency-Map of another namespace.
     *
     * @param string $namespace The namespace to load map from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Di_Map The dependency instance from another namespace
     * @access public
     * @throws Doozr_Di_Exception
     */
    public function getMapFromOtherNamespace($namespace)
    {
        if (!isset(self::$dependencyMaps[$namespace])) {
            throw new Doozr_Di_Exception(
                sprintf(
                    'Dependency-Map could not be found. Dependency-Map with namespace "%s" does not exist.',
                    $namespace
                )
            );
        }

        // return requested map
        return self::$dependencyMaps[$namespace];
    }

    /**
     * Imports a Dependency-Map from another namespace
     *
     * This method is intend to import a Dependency-Map from another namespace.
     *
     * @param string $namespace The namespace to load dependency map from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function importMapFromOtherNamespace($namespace)
    {
        self::$dependencyMaps[$this->namespace] = $this->getMapFromOtherNamespace($namespace);
    }

    /**
     * Setter for Factory
     *
     * This method is intend to set the instance of Doozr_Di_Factory
     *
     * @param Doozr_Di_Factory $factory The factory instance to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setFactory(Doozr_Di_Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Getter for Factory
     *
     * This method is intend to return the instance of Doozr_Di_Factory
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Di_Factory The instance of Doozr_Di_Factory if set, otherwise NULL
     * @access public
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Constructs all single parts and returns a new instance
     *
     * This method is intend to combine all defined dependencies and returns a
     * instance of requested class.
     *
     * @param string $classname The name of the class to build
     * @param mixed  $arguments Arguments to pass to class (works only in dynamic runtimeEnvironment)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object|mixed Instance of the given class
     * @access public
     * @throws Doozr_Di_Exception
     */
    public function build($classname, $arguments = null)
    {
        // Check if all required dependencies are set [Doozr_Di_Factory, Doozr_Di_Map, ...]
        if (!$this->requirementsFulfilled()) {
            throw new Doozr_Di_Exception(
                'Error building an instance. Requirements not fulfilled. Provide all required dependencies.'
            );
        }

        // Get setup for static || dynamic
        if ($this->mode === self::MODE_DYNAMIC) {
            $setup = $this->getMap()->getCollection()->getSetup($classname);

        } else {
            $setup = $this->getMap()->getCollection()->getSetup($classname);

        }

        // Store arguments if given
        if ($arguments !== null && is_array($arguments)) {
            $setup['arguments'] = $arguments;
        }

        // Check if a setup exists
        if ($setup['dependencies'] === null) {
            throw new Doozr_Di_Exception(
                'Error building instance. No recipe for class "' . $classname . '" found!'
            );
        }

        // build and return the object
        return $this->getFactory()->build(
            $classname,
            $setup
        );
    }

    /**
     * Checks if the requirements are fulfilled
     *
     * This method is intend to check if the requirements are fulfilled.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if requirements fulfilled, otherwise FALSE
     * @access public
     */
    public function requirementsFulfilled()
    {
        // get map
        $map = $this->getMap();

        // check map
        if ($map && $map->getCollection()) {
            return true;
        }

        // failed
        return false;
    }

    /**
     * Singleton Constructor
     *
     * This method is intend to construct and return a singleton instance of
     * Doozr_Di_Container. Each container singleton is bound to a namespace (eg. 'default').
     * By passing a namespace through argument $namespace you are able to create
     * more than one instance of container if needed/required by your application.
     *
     * @param string  $namespace The namespace of the Doozr_Di_Container instance
     * @param int $mode      The runtimeEnvironment used to handle maps
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Di_Container Instance
     * @access public
     * @static
     */
    public static function getInstance($namespace = self::DEFAULT_NAMESPACE, $mode = self::MODE_STATIC)
    {
        if (!isset(self::$instances[$namespace])) {
            self::$instances[$namespace] = new self(
                $namespace,
                $mode
            );
        }

        // return instance
        return self::$instances[$namespace];
    }

    /**
     * Merges a new map with existing one.
     *
     * @param Doozr_Di_Map $target The map in which the $source is merged into
     * @param Doozr_Di_Map $source The map which is merged into $target
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Di_Container The current instance of the Container for chaining/fluent-interface
     * @access protected
     */
    protected function mergeMaps(Doozr_Di_Map $target, Doozr_Di_Map $source)
    {
        // import of dependencies is built in functionality of map
        $target->import(
            $source->export()
        );

        // set state to state of source class
        //$target->setLastProcessedClass($source->getLastProcessedClass());

        // return the filled target
        return $target;
    }

    /**
     * Constructor
     *
     * This method is the constructor.
     *
     * @param string  $namespace The namespace to operate
     * @param int $mode      The runtimeEnvironment used to handle maps
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Di_Container The current instance of the Container for chaining/fluent-interface
     * @access private
     */
    private function __construct($namespace, $mode)
    {
        $this->namespace = $namespace;
        $this->mode      = $mode;
    }
}
