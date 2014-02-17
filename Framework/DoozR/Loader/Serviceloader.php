<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Loader - Serviceloader
 *
 * Serviceloader.php - The Serviceloader loads services within the DoozR
 * world. No matter which namespace and no matter if singleton or multiple.
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2013, Benjamin Carl - All rights reserved.
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
 * @category   DoozR
 * @package    DoozR_Loader
 * @subpackage DoozR_Loader_Serviceloader
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Class/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Factory/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Factory/Multiple.php';

/**
 * DoozR - Loader - Serviceloader
 *
 * The Serviceloader loads services within the DoozR world. No matter which
 * namespace and no matter if singleton or multiple.
 *
 * @category   DoozR
 * @package    DoozR_Loader
 * @subpackage DoozR_Loader_Serviceloader
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Loader_Serviceloader extends DoozR_Base_Class_Singleton
{
    /**
     * The DoozR registry containing important references.
     *
     * @var DoozR_Registry
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
    private static $loaded = array();

    /**
     * The deoendency map
     *
     * @var DoozR_Di_Map_Annotation
     * @access private
     * @static
     */
    private static $_map;

    /**
     * The dependency-injection container
     *
     * @var DoozR_Di_Container
     * @private
     * @static
     */
    private static $_container;

    /**
     * The default Namespace to load from
     *
     * @var string
     * @access public
     * @const
     */
    const DEFAULT_NAMESPACE = 'DoozR';


    /**
     * Loads a service from any namespace.
     *
     * This method is intend to load services. Its possible to pass a string with just a name of a service
     * to this method or you pass an array with namespace like this -> array('namespace', 'service').
     *
     * @param string|array $service Just the name of the service if a DoozR default service or
     *                              as an array with additional namespace like: array('namespace', 'service')
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object An/the instance of the requested service
     * @access public
     * @static
     */
    public static function load($service)
    {
        // get arguments generic way rip off 1st this is the service name
        $arguments         = array_slice(func_get_args(), 1);
        $namespacedService = self::getNamespacedService($service);
        $classname         = $namespacedService['namespace'].'_'.ucfirst($namespacedService['service']).'_Service';

        // allready instanciated?
        if (!self::$instance) {
            self::init();
        }

        // load file
        self::getService(ucfirst(strtolower($service)), $namespacedService['namespace']);

        // get reflection
        #$reflector = new ReflectionClass($classname);

        // parse DoozR-Annotations out of
        #$properties = self::parseAnnotations($reflector->getDocComment());

        //generate map from annotations in source of current service main entry
        self::$_map->generate($classname);

        // we support only mapping of registry - this is sad :(
        self::$_map->wire(
            DoozR_Di_Container::MODE_STATIC,
            array(
                'DoozR_Registry' => self::$registry
            )
        );

        // store map
        self::$_container->setMap(self::$_map);

        // create instance with arguments ...
        if ($arguments !== null) {
            $instance = self::$_container->build($classname, $arguments);
        } else {
            $instance = self::$_container->build($classname);
        }

        return $instance;
    }


    /**
     * Initializer
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
        self::$registry = DoozR_Registry::getInstance();
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
        // Bootstrap if required (CLI!)
        require_once DOOZR_DOCUMENT_ROOT.'DoozR/Di/Bootstrap.php';

        // get required dependency container for annotations!
        require_once DI_PATH_LIB_DI.'Map/Annotation.php';
        require_once DI_PATH_LIB_DI.'Parser/Annotation.php';
        require_once DI_PATH_LIB_DI.'Dependency.php';

        $collection       = new DoozR_Di_Collection();
        $parser           = new DoozR_Di_Parser_Annotation();
        $dependency       = new DoozR_Di_Dependency();

        self::$_map       = new DoozR_Di_Map_Annotation($collection, $parser, $dependency);
        self::$_container = DoozR_Di_Container::getInstance(__CLASS__);
        self::$_container->setFactory(new DoozR_Di_Factory());
    }

    /**
     * Completes DoozR's default services with namespace (DoozR).
     *
     * This method is intend to format a passed service configuration to a named index array.
     * It will automatic inject "DoozR" as namespace for default services.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Full qualified namespaced service
     * @access protected
     * @static
     */
    protected static function getNamespacedService($service)
    {
        if (is_array($service)) {
            $namespace = $service[0];
            $service   = $service[1];
        } else {
            $namespace = self::DEFAULT_NAMESPACE;
            $service   = $service;
        }

        return array(
            'namespace' => $namespace,
            'service'   => $service
        );
    }

    /**
     * This method is intend to conditional includes the service main classfile.
     *
     * @param string $service    The service to include
     * @param string $namespace The namespace to load service from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access protected
     * @static
     */
    protected static function getService($service, $namespace = self::DEFAULT_NAMESPACE)
    {
        $key = $service.$namespace;

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
     * @param string $namespace The (optional) namespace to use. Default is namespace "DoozR"
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The full path to the service library file.
     * @access protected
     * @static
     */
    protected static function getServiceFile($service, $namespace = self::DEFAULT_NAMESPACE)
    {
        return self::getServicePath($service, $namespace).'Service.php';
    }

    /**
     * Returns the path to a service.
     *
     * This method is intend to return the path to a passed service and (optional) namespace.
     *
     * @param        $service   Name of the service to return path for
     * @param string $namespace The (optional) namespace (default "DoozR")
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The full path to the service library file.
     * @access protected
     * @static
     */
    protected static function getServicePath($service, $namespace = self::DEFAULT_NAMESPACE)
    {
        return DOOZR_DOCUMENT_ROOT.
            'Service'.DIRECTORY_SEPARATOR.
            $namespace.DIRECTORY_SEPARATOR.
            $service.DIRECTORY_SEPARATOR;
    }

    /**
     * Parses out annotations (e.g. @inject ...).
     *
     * This method is intend to parse out the annotations from a passed DocBlock.
     *
     * @param string $docBlock The DocBlock Comment of the class (service)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The parsed (DoozR) annotations
     * @access protected
     * @static
     */
    protected static function parseAnnotations($docBlock = '')
    {
        // holds parsed annotations (raw)
        $annotations = array();
        $properties  = array();

        // parse out annotations
        $result = preg_match_all(
            '/@service(.*?)(\n|$)/i',
            $docBlock,
            $annotations
        );

        // check result, prepare and add to class @ runtime (vars)
        if ($result > 0) {
            for ($i = 0; $i < $result; ++$i) {
                $processed = array_merge(
                    array_filter(
                        explode(' ', $annotations[1][$i])
                    )
                );

                /* TODO: remove this maybe whole block with something more useful */
                $processed[1] = $processed[0];
                $processed[0] = 'type';

                // set at runtime
                $properties[strtolower($processed[0])] = strtolower($processed[1]);
            }
        }

        return $properties;
    }
}
