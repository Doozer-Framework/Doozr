<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Base Decorator Singleton
 *
 * Singleton.php - Base class for decorators.
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
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Decorator
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Class/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Exception.php';

/**
 * DoozR Base Decorator Singleton
 *
 * Base class for decorators.
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Decorator
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Base_Decorator_Singleton extends DoozR_Base_Class_Singleton
{
    /**
     * Configuration for decorator
     *
     * @var array
     * @access protected
     */
    protected $configuration;

    /**
     * This instance contains the routing matrix.
     * (e.g. route "foo()" to "bar()")
     *
     * @var object
     * @access protected
     */
    protected $route;

    /**
     * Contains the current status of decorated class
     *
     * @var boolean
     * @access protected
     */
    protected $enabled;

    /**
     * The chaining classname memory
     *
     * @var string
     * @access protected
     * @static
     */
    protected static $chainClassname;

    /**
     * The transformer which takes
     *
     * @var object
     */
    protected static $transformer;


    /**
     * Initializes the decorator by passed through configuration.
     * The configuration is used for checking the required information:
     *
     * path
     * name
     *
     * and afterwards check for bootstrapping script of used Drivers
     * Library (e.g. boostrap.php or something like that). If a boot-
     * strapper is found it is executed.
     *
     * @param array $configuration Reference to the configuration for decorator
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     * @throws DoozR_Exception
     */
    protected function init(array $configuration, DoozR_Path $path)
    {
        // the path to the class to decorate
        if (!isset($configuration['path'])) {
            throw new DoozR_Exception(
                'Base decorator needs path to class which should be decorated!'
            );
        }

        // the name of the class to decorate
        if (!isset($configuration['name'])) {
            throw new DoozR_Exception(
                'Base decorator needs name for decoration (route ...)!'
            );
        }
        $name = ucfirst(strtolower($configuration['name']));

        // bootstrap script required for decorated class to run
        if (isset($configuration['bootstrap']) && $configuration['bootstrap'] !== false) {
            include_once $configuration['path'].$configuration['bootstrap'];
        }

        // the glue between ???
        // TODO: check sense comment ...
        if (!isset($configuration['glue'])) {
            $configuration['glue'] = '';
        }

        // if a route script is set, we install this (aka proxy or better proxies!) here
        if (isset($configuration['docroot']) && isset($configuration['route'])) {
            // include the route
            include_once $configuration['docroot'].$configuration['route'];
            $classname = 'Doodi_'.$name.'_Route';
            $this->route = new $classname();
        }

        // store configuration for further processing
        $this->configuration = $configuration;
    }

    /**
     * Translates given method signature and arguments to
     * target signature and arguments.
     *
     * @param string $signature The signature of the method to translate
     * @param array  $arguments The arguments to pass to method
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function translate($signature, $arguments = null)
    {
        if (!self::$transformer) {
            self::$transformer = $this->_initTransformer(
                $this->decoratorConfiguration['docroot'],
                $this->decoratorConfiguration['name']
            );
        };

        // return translation of input
        return self::$transformer->transform($this, $signature, $arguments);
    }

    /**
     * This method is intend to initialize the transformer-class if exists
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     * @throws Exception
     */
    private function _initTransformer($docroot, $vendor = 'DoozR')
    {
        // vendor (can be anything - must match folder containing transformation
        // e.g.
        $vendor = ucfirst($vendor);

        // reg
        $registry = DoozR_Registry::getInstance();

        // path
        $path = $registry->path;

        // transformation exists?
        $transformationFile = $docroot.'Transformation.php';

        // check for transformation-class-file
        if (file_exists($transformationFile)) {
            // include if exist
            include_once $transformationFile;

            // combine parts to transformation classname
            $transformerClass = 'Doodi_'.$vendor.'_Transformation';

            // and instanciate the transformer
            return new $transformerClass($this->_configuration);

        } else {
            // no transformer => no function
            throw new Exception(
                'No "Transformer ('.$transformationFile.')" for Mode: "'.$vendor.'" // Driver: "'.$vendor.
                '" found. Can\'t continue!'
            );
        }
    }

    /**
     * This method is intend to fetch all methods-calls for non-existant methods. This calls
     * get routed by the previously generated matrix of the routing class.
     *
     * @param string $methodSignature The called method
     * @param array  $arguments       The arguments of the method call
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the message call
     * @access public
     */
    public function __call($methodSignature, $arguments)
    {
        // minor corrections on input
        $methodSignature = str_replace('___', '', $methodSignature);

        // check translate!
        return $this->translate($methodSignature, $arguments);

        /*
        // generate uid
        $id = md5(self::$chainClassname.$methodSignature);

        // get route config for this uid
        $config = $this->route->matrix[$id];

        // reset chaining
        self::$chainClassname = null;

        // check for type of call
        if ($config['type'] == 'static') {
            // combine parameter for static method call
            $staticMethod = $config['class'].'::'.$config['method'];

            // call method and get its return value
            $result = call_user_func_array($staticMethod, $arguments);

            // if this method is constructor -> store instance
            if ($config['constructor'] === true) {
                $this->route->matrix[$config['class']] = $result;
            }

        } else {

            // its not a static call - so we need an instance first
            if (!isset($this->route->matrix[$config['class']])) {
                throw new DoozR_Exception(
                    'Instance of class: "'.$config['class'].'" required before calling method: "'.$methodSignature.'"!'.
                    'Please call constructor first.'
                );
            }

            //
            $instance = $this->route->matrix[$config['class']];

            // call method and get its return value
            $result = call_user_func_array(array($instance, $methodSignature), $arguments);
        }

        // return the result
        return $result;
        */
    }

    /**
     * This magic method fetches all propertie accesses to this class
     * and pass them to the decorated class - this is done by
     *
     * @param string $chainClassname The requested property
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object The current instance of this class
     * @access public
     */
    public function __get($chainClassname)
    {
        if (!self::$chainClassname) {
            if (strtolower($chainClassname) == strtolower($this->decoratorConfiguration['name'])) {
                $chainClassname = (isset($this->decoratorConfiguration['translate']))
                     ? $this->decoratorConfiguration['translate']
                     : $chainClassname;
            }

            self::$chainClassname = $chainClassname;

        } else {
            self::$chainClassname = self::$chainClassname.$this->decoratorConfiguration['glue'].
                ucfirst($chainClassname);
        }

        return $this;
    }
}
