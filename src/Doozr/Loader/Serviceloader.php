<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Loader - Serviceloader
 *
 * Serviceloader.php - The Serviceloader loads services within the Doozr
 * world. No matter which namespace and no matter if singleton or multiple.
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
 * @package    Doozr_Loader
 * @subpackage Doozr_Loader_Serviceloader
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Class/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Factory/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Factory/Multiple.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Loader/Interface.php';

/**
 * Doozr - Loader - Serviceloader
 *
 * The Serviceloader loads services within the Doozr world. No matter which
 * namespace and no matter if singleton or multiple.
 *
 * @category   Doozr
 * @package    Doozr_Loader
 * @subpackage Doozr_Loader_Serviceloader
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Loader_Serviceloader extends Doozr_Base_Class_Singleton
{
    /**
     * The Doozr registry containing important references.
     *
     * @var Doozr_Registry
     * @access protected
     * @static
     */
    protected static $registry;

    /**
     * Runtime cache for loaded services status/history.
     *
     * @var array
     * @access protected
     * @static
     */
    private static $loaded = [];

    /**
     * The dependency map
     *
     * @var Doozr_Di_Map_Annotation
     * @access protected
     * @static
     */
    protected static $map;

    /**
     * The dependency-injection container
     *
     * @var Doozr_Di_Container
     * @access protected
     * @static
     */
    protected static $container;

    /**
     * The default Namespace to load from
     *
     * @var string
     * @access public
     */
    const DEFAULT_NAMESPACE = 'Doozr';

    /**
     * The default alias for skeleton.
     *
     * @var null
     * @access public
     */
    const DEFAULT_ALIAS = null;

    /**
     * The default name
     *
     * @var string
     * @access public
     */
    const DEFAULT_NAME = null;

    /**
     * The default info text for the service
     *
     * @var string
     * @access public
     */
    const DEFAULT_INFO = null;

    /**
     * Loads a service from any namespace.
     *
     * This method is intend to load services. Its possible to pass a string with just a name of a service
     * to this method or you pass an array with namespace like this -> array('namespace', 'service').
     *
     * @param string|array $service Just the name of the service if a Doozr default service or
     *                              as an array with additional namespace like: array('namespace', 'service')
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Base_Service_Interface An/the instance of the requested service
     * @access public
     * @static
     */
    public static function load($service)
    {
        // Get arguments generic way rip off 1st this is the service name
        $arguments            = array_slice(func_get_args(), 1);
        $fullQualifiedService = self::getFullQualifiedService($service);
        $classname            = $fullQualifiedService['namespace'] .
            '_' . ucfirst($fullQualifiedService['name']) . '_Service';

        // Instantiated?
        (self::$instance === null) ? self::init() : null;

        // Load file
        self::getService($fullQualifiedService['name'], $fullQualifiedService['namespace']);

        // Generate map from annotations in source of current service main entry
        self::$map
            ->reset()
            ->generate($classname);

        // Store map
        self::$registry->getContainer()->addToMap(self::$map);

        // Create instance ...
        /* @var $instance Doozr_Base_Service_Interface */
        $instance = self::$registry->getContainer()->build($classname, $arguments);

        // Decide which identifier to use
        $identifier = strtolower(
            ($fullQualifiedService['alias'] !== null) ?
                $fullQualifiedService['alias'] :
                $fullQualifiedService['name']
        );

        // Register service and put the UUID as reference into response
        $instance->setUuid(
            self::registerService($identifier, $instance)
        );

        return $instance;
    }

    /**
     * Registers a service in Doozr's registry.
     *
     * @param string                       $name    The name of the service.
     * @param Doozr_Base_Service_Interface $service The service instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Uuid handler generated by registry
     * @access protected
     */
    protected static function registerService($name, Doozr_Base_Service_Interface $service)
    {
        // Check how to store service in registry
        $uuid = ($service->isSingleton() === true) ?
            self::getRegistry()->set($service, $name) :
            self::getRegistry()->add($service, $name);

        return $uuid;
    }

    /**
     * Initialize the instance, registry and DI.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @static
     */
    public static function init()
    {
        // create instance like we would by calling getInstance()
        self::getInstance();
        self::$registry = Doozr_Registry::getInstance();
        self::initDependencyInjection();
    }

    /**
     * Initializes the dependency injection path' parser, map, ...
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     * @static
     */
    protected static function initDependencyInjection()
    {
        // Get required dependency container for annotations!
        require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Map/Annotation.php';
        require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Parser/Annotation.php';
        require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Dependency.php';

        $collection = new Doozr_Di_Collection();
        $parser     = new Doozr_Di_Parser_Annotation();
        $dependency = new Doozr_Di_Dependency();

        self::$map  = new Doozr_Di_Map_Annotation($collection, $parser, $dependency);

        #self::$container = Doozr_Di_Container::getInstance();
        #self::$container->setFactory(new Doozr_Di_Factory(self::$registry));
    }

    /**
     * Completes Doozr's default services with namespace (Doozr).
     *
     * This method is intend to format a passed service configuration to a named index array.
     * It will automatic inject "Doozr" as namespace for default services.
     *
     * @param array|string $service The service as full qualified entry array otherwise string name (Doozr default ns)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Full qualified and namespaced service
     * @access protected
     * @throws Doozr_Loader_Serviceloader_Exception
     * @static
     */
    protected static function getFullQualifiedService($service)
    {
        // Get skeleton with defaults for all minimum required fields
        $fullQualifiedService = self::getFullQualifiedServiceSkeleton();

        // Check for array mode
        if (is_array($service) === true) {

            // If an array is found we need at least namespace and service
            if (isset($service['name']) === false) {
                throw new Doozr_Loader_Serviceloader_Exception(
                    'Serviceloader requires at least a "service" name when passing an []!'
                );
            }

            // Extract the data from array
            $fullQualifiedService['name']   = ucfirst(strtolower($service['name']));
            $fullQualifiedService['namespace'] = (isset($service['namespace']) === true) ?
                $service['namespace'] :
                $fullQualifiedService['namespace'];
            $fullQualifiedService['alias']     = (isset($service['alias']) === true) ?
                $service['alias'] :
                $fullQualifiedService['alias'];

        } else {
            $fullQualifiedService['name'] = ucfirst(strtolower($service));
        }

        return $fullQualifiedService;
    }

    /**
     * Returns a skeleton for a service configuration array.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Full qualified service skeleton
     * @access protected
     * @static
     */
    protected static function getFullQualifiedServiceSkeleton()
    {
        return [
            'namespace' => self::DEFAULT_NAMESPACE,
            'name'      => self::DEFAULT_NAME,
            'alias'     => self::DEFAULT_ALIAS,
            'info'      => self::DEFAULT_INFO,
        ];
    }

    /**
     * This method is intend to conditional includes the service main classfile.
     *
     * @param string $service    The service to include
     * @param string $namespace The namespace to load service from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success, otherwise FALSE
     * @access protected
     * @static
     */
    protected static function getService($service, $namespace = self::DEFAULT_NAMESPACE)
    {
        $key = $service . $namespace;

        if (!isset(self::$loaded[$key]) || self::$loaded[$key] !== true) {
            include_once self::getServiceFile($service, $namespace);
            self::$loaded[$key] = true;
        }

        return true;
    }

    /**
     * Comnbines the passed service name and namespace with the path to the services.
     *
     * This method is intend ...
     *
     * @param string $service   The name of the service to return the path for
     * @param string $namespace The (optional) namespace to use. Default is namespace "Doozr"
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The full path to the service library file.
     * @access protected
     * @static
     */
    protected static function getServiceFile($service, $namespace = self::DEFAULT_NAMESPACE)
    {
        return self::getServicePath($service, $namespace) . 'Service.php';
    }

    /**
     * Returns the path to a service.
     *
     * This method is intend to return the path to a passed service and (optional) namespace.
     *
     * @param string $service   Name of the service to return path for
     * @param string $namespace The (optional) namespace (default "Doozr")
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The full path to the service library file.
     * @access protected
     * @static
     */
    protected static function getServicePath($service, $namespace = self::DEFAULT_NAMESPACE)
    {
        return DOOZR_DOCUMENT_ROOT . 'Service' . DIRECTORY_SEPARATOR . $namespace . DIRECTORY_SEPARATOR .
            $service . DIRECTORY_SEPARATOR;
    }
}
