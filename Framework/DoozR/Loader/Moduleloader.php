<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Moduleloader
 *
 * Moduleloader.php - Moduleloader
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
 * @subpackage DoozR_Loader_Moduleloader
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
 * DoozR Moduleloader
 *
 * Moduleloader
 *
 * @category   DoozR
 * @package    DoozR_Loader
 * @subpackage DoozR_Loader_Moduleloader
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Loader_Moduleloader extends DoozR_Base_Class_Singleton
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
     * loads DoozR-modules from all namespaces (e.g. DoozR, ...)
     *
     * This method is intend to load modules used by DoozR-Core, Applications based on DoozR ...
     *
     * @param string $module    The module to load
     * @param mixed  $arguments The arguments to pass to module instance
     * @param string $namespace The namespace to load module from
     *
     * @return  object An/The instance of the requested module
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @static
     */
    public static function load($module, $arguments = null, $namespace = 'DoozR')
    {
        // allready instanciated?
        if (!self::$instance) {
            self::getInstance();

            // get the singleton instance of registry - containing important object instances
            self::$_registry = DoozR_Registry::getInstance();
        }

        // correct module name
        $module = ucfirst(strtolower($module));

        // load file
        self::_getModule($module, $namespace);

        // combine Module name
        $classname = $namespace.'_'.$module.'_Module';

        // get reflection
        $reflector = new ReflectionClass($classname);

        // parse DoozR-Annotations out of it
        $properties = self::_parseAnnotations(
            $reflector->getDocComment()
        );

        // inject registry in arguments
        if (!$arguments) {
            $arguments = array(
                self::$_registry
            );
        } else {
            // convert if not is array already
            if (!is_array($arguments)) {
                $arguments = array(self::$_registry, $arguments);
            } else {
                // simply join and done
                $arguments = array_merge(array(self::$_registry), $arguments);
            }
        }

        // get correct type if no type explicit given
        if (!isset($properties['type']) || $properties['type'] != 'singleton') {

            // return fresh (multi) instance
            return DoozR_Factory_Multiple::create(
                $classname,
                $arguments,
                null,
                $reflector
            );

        } else {
            // custom instanciate method given?
            if (!isset($properties['call'])) {
                $properties['call'] = 'getInstance';
            }

            // return singleton instance
            return DoozR_Factory_Singleton::create(
                $classname,
                $arguments,
                $properties['call'],
                null
            );

        }
    }

    /**
     * conditional includes the module main classfile
     *
     * This method is intend to conditional includes the module main classfile.
     *
     * @param string $module    The module to include
     * @param string $namespace The namespace to load module from
     *
     * @return  void
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @static
     */
    private static function _getModule($module, $namespace)
    {
        if (isset(self::$_loaded[$module.$namespace])) {
            return;

        } else {
            // get file
            include_once DOOZR_DOCUMENT_ROOT.'Module'.DIRECTORY_SEPARATOR.$namespace.DIRECTORY_SEPARATOR.$module.
                DIRECTORY_SEPARATOR.'Module.php';

            self::$_loaded[$module.$namespace] = true;
        }
    }

    /**
     * parses the annotations (DoozR) out of a DocBlock
     *
     * This method is intend to parse out the annotations (DoozR) of a DocBlock
     *
     * @param string $docBlock The DocBlock Comment of the class to instanciate (module)
     *
     * @return  array The parsed (DoozR) annotations
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
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

?>
