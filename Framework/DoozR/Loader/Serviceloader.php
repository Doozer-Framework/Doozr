<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Loader - Serviceloader
 *
 * Serviceloader.php - The Serviceloader is responsible for loading services no
 * matter from which namespace and no matter if singleton or multiple.
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
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Class/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Factory/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Factory/Multiple.php';

/**
 * DoozR - Loader - Serviceloader
 *
 * The Serviceloader is responsible for loading services no
 * matter from which namespace and no matter if singleton or multiple.
 *
 * @category   DoozR
 * @package    DoozR_Loader
 * @subpackage DoozR_Loader_Serviceloader
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Loader_Serviceloader extends DoozR_Base_Class_Singleton
{
    /**
     * Contains an instance of DoozR_Registry.
     * It contains instances of all important objects.
     *
     * @var object
     * @access private
     * @static
     */
    private static $_registry;

    /**
     * Contains the Status
     *
     * @var array
     * @access private
     * @static
     */
    private static $_loaded = array();

    /**
     * @var DoozR_Di_Map_Annotation
     */
    private static $_map;

    /**
     * @var DoozR_Di_Container
     */
    private static $_container;


    protected static function initDependencyInjection()
    {
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


    protected static function getNamespacedService($service)
    {
        if (is_array($service)) {
            $namespace = $service[0];
            $service   = $service[1];
        } else {
            $namespace = 'DoozR';
            $service   = $service;
        }

        return array(
            'namespace' => $namespace,
            'service'   => $service
        );
    }

    public static function init()
    {
        // create instance like we would by calling getInstance()
        self::getInstance();
        self::$_registry = DoozR_Registry::getInstance();
        self::initDependencyInjection();
    }

    /**
     * This method is intend to load services used by DoozR-Core, Applications based on DoozR ...
     *
     * @param mixed $service The service as string or plus additional namespace as array('namespace', 'service')
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object An/The instance of the requested service
     * @access public
     * @static
     */
    public static function load($service)
    {
        // the arguments default
        $arguments = null;
        $classname = null;

        // check for namespace in service
        $namespacedService = self::getNamespacedService($service);

        // allready instanciated?
        if (!self::$instance) {
            self::init();
        }

        // correct service name
        $service = ucfirst(strtolower($service));

        // load file
        self::_getService($service, $namespacedService['namespace']);

        // combine Service name
        $classname = $namespacedService['namespace'].'_'.$namespacedService['service'].'_Service';

        // get reflection
        $reflector = new ReflectionClass($classname);

        // parse DoozR-Annotations out of
        $properties = self::_parseAnnotations(
            $reflector->getDocComment()
        );

        // get arguments generic way rip off 1st this is the service name
        $arguments = array_slice(func_get_args(), 1);

        //generate map from annotations in source of current service main entry
        self::$_map->generate($classname);

        // we support only mapping of registry - this is the funnel. each
        self::$_map->wire(
            DoozR_Di_Container::MODE_STATIC,
            array(
                'DoozR_Registry' => self::$_registry
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
     * This method is intend to conditional includes the service main classfile.
     *
     * @param string $service    The service to include
     * @param string $namespace The namespace to load service from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access private
     * @static
     */
    private static function _getService($service, $namespace = 'DoozR')
    {
        if (!isset(self::$_loaded[$service.$namespace])) {
            include_once self::_getPathAndFile($service, $namespace);
            self::$_loaded[$service.$namespace] = true;
        }

        // success
        return true;
    }


    private static function _getPathAndFile($service, $namespace = 'DoozR')
    {
        return self::_getPath($service, $namespace).'Service.php';
    }

    private static function _getPath($service, $namespace = 'DoozR')
    {
        return DOOZR_DOCUMENT_ROOT.
            'Service'.DIRECTORY_SEPARATOR.
            $namespace.DIRECTORY_SEPARATOR.
            $service.DIRECTORY_SEPARATOR;
    }

    /**
     * This method is intend to parse out the annotations (DoozR) of a DocBlock
     *
     * @param string $docBlock The DocBlock Comment of the class to instanciate (service)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The parsed (DoozR) annotations
     * @access private
     * @static
     */
    private static function _parseAnnotations($docBlock = '')
    {
        // holds parsed annotations (raw)
        $annotations = array();
        $properties  = array();

        // parse out annotations
        $result = preg_match_all(
            '/@DoozR(.*?)(\n|$)/i',
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

                // set at runtime
                $properties[strtolower($processed[0])] = strtolower($processed[1]);
            }
        }

        return $properties;
    }
}
