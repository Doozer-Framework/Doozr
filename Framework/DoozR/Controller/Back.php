<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Controller - Back
 *
 * Back.php - The Back-Controller of the DoozR-Framework.
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
 * @package    DoozR_Controller
 * @subpackage DoozR_Controller_Back
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Class/Singleton.php';

/**
 * DoozR - Controller - Back
 *
 * The Back-Controller of the DoozR-Framework.
 *
 * @category   DoozR
 * @package    DoozR_Controller
 * @subpackage DoozR_Controller_Back
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Controller_Back extends DoozR_Base_Class_Singleton
{
    /**
     * holds the directory separator
     *
     * this var is a shortcut to DIRECTORY_SEPARATOR
     *
     * @var string
     * @access private
     */
    private $_separator = DIRECTORY_SEPARATOR;

    /**
     * holds the instance of model
     *
     * this var holds the instance of the model
     *
     * @var object
     * @access private
     */
    private $_model;

    /**
     * holds the instance of view
     *
     * this var holds the instance of the view
     *
     * @var object
     * @access private
     */
    private $_view;

    /**
     * contains an instance of Presentor if MVP-pattern is used,
     * otherwise an instance of Controller if MVC-pattern is active.
     *
     * @var object
     * @access private
     */
    private $_connector;

    /**
     * holds the translation
     *
     * @var mixed
     * @access private
     */
    private $_translation;

    /**
     * holds the request
     *
     * @var mixed
     * @access private
     */
    private $_request;

    /**
     * holds the original request
     *
     * @var mixed
     * @access private
     */
    private $_originalRequest;

    /**
     * contains the active pattern (MVC/MVP)
     *
     * @var string
     * @access private
     */
    private $_pattern;

    /**
     * Contains an instance of module DoozR_Cache_Service
     *
     * @var DoozR_Cache_Service
     * @access private
     */
    private $_cache;

    /**
     * contains instance of config
     *
     * @var object
     * @access private
     */
    private $_config;

    /**
     * holds instance of logger
     *
     * @var object
     * @access private
     */
    private $_logger;

    /**
     * holds instance of module "filesystem"
     *
     * @var object
     * @access private
     */
    private $_filesystem;

    /**
     * HTTP STATUS 400
     *
     * @var integer
     * @access const
     */
    const HTTP_STATUS_400 = 400;

    /**
     * HTTP STATUS 404
     *
     * @var integer
     * @access const
     */
    const HTTP_STATUS_404 = 404;


    /**
     * Constructor of this class
     *
     * This method is the constructor of this class.
     *
     * @param DoozR_Config_Interface &$config The instance of the DoozR core config
     * @param DoozR_Logger_Interface &$logger The instance of the DoozR huge logging facade (subsystem)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __construct(DoozR_Config_Interface $config, DoozR_Logger_Interface $logger)
    {
        // store instances
        $this->_config = $config;
        $this->_logger = $logger;

        // load and store instance of module filesystem
        $this->_filesystem = DoozR_Loader_Serviceloader::load('filesystem');
    }

    /**
     * This method is intend to dispatch the requested resource
     *
     * @param array  $request         The complete request including the mapping
     * @param array  $originalRequest The original request without modification
     * @param array  $translation     The translation used by DoozR to translate request to objects
     * @param string $pattern         The default pattern to use
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Controller_Back The current instance for chaining
     * @access public
     */
    public function dispatch(array $request, array $originalRequest, array $translation, $pattern = 'MVP')
    {
        // store request and corresponding translation
        $this->_request         = $request;
        $this->_originalRequest = $originalRequest;
        $this->_translation     = $translation;
        $this->_pattern         = $pattern;
        $this->_cache           = DoozR_Loader_Serviceloader::load(
            'cache',
            DOOZR_UNIX,
            $this->_config->cache->container()
        );

        // init MV(P|C) layer
        switch ($this->_pattern) {
        case 'MVP':

            // init layer MODEL (data e.g. Database access ...)
            $this->_model = $this->_initLayer(
                $request[$translation[0]],
                'Model',
                array(
                    $request,
                    $translation,
                    $originalRequest,
                    $this->_cache,
                    $this->_config
                )
            );

            // init layer VIEW (displaying data ...)
            $this->_view = $this->_initLayer(
                $request[$translation[0]],
                'View',
                array(
                    $request,
                    $translation,
                    $originalRequest,
                    $this->_cache,
                    $this->_config,
                    DoozR_Controller_Front::getInstance()
                )
            );

            // init connector - can be either PRESENTOR or CONTROLLER
            $this->_connector = $this->_initLayer(
                $request[$translation[0]],
                'Presenter',
                array(
                    $request,
                    $translation,
                    $originalRequest,
                    $this->_config,
                    $this->_model,
                    $this->_view
                )
            );

            break;

        case 'MVC':
        default:
            pred(__CLASS__.': not implemented yet!');
            break;
        }

        // if we reach this point - we should check if anything could be called otherwise 404
        if (($status = $this->_validateRequest()) !== true) {
            // send error status through front controller
            $front = DoozR_Controller_Front::getInstance();
            $front->getResponse()->sendHttpStatus($status, null, true, implode(DIRECTORY_SEPARATOR, $request));

        } else {
            // the request is valid -> attach the observer(s) to subject
            ($this->_model) ? $this->_connector->attach($this->_model) : null;
            ($this->_view) ? $this->_connector->attach($this->_view) : null;

            // and finally call the main() entry point
            $this->_connector->{$request[$translation[1]]}();
        }

        // return instance for chaining
        return $this;
    }

    /**
     * Validates the existing request data
     *
     * This method is intend to validate the current request data. A request needs
     * at least a connector-instance (Presenter) and an entry point (e.g. Main())
     * to be valid.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed TRUE if request is valid, otherwise this method
     * @access private
     */
    private function _validateRequest()
    {
        // get init class + method from request
        $class  = $this->_connector;
        $method = $this->_request[$this->_translation[1]];

        // assume valid
        $valid  = true;

        // no connector instance = Bad Request = 400
        if (!$this->_connector) {
            $valid = self::HTTP_STATUS_400;

            // no action to call after existing connector exist = Not Found = 404
        } elseif (!method_exists($class, $method) || !is_callable(array($class, $method))) {
            $valid = self::HTTP_STATUS_404;

        }

        return $valid;
    }

    /**
     * Creates and returns an instance of a layer
     *
     * This method is intend to instanciate a new layer
     * (can be either Model|View|Controller|Presenter).
     *
     * @param string $request     The resource requested
     * @param string $layer       The part/layer of the MVC/P structure to instanciate and return
     * @param array  $classParams An array of Parameters to append at instanciation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object An instance of the requested layer (M|V|C|P)
     * @access private
     */
    private function _initLayer($request, $layer = 'Model', $classParams = null)
    {
        // assume instance won't be created
        $instance = null;

        // build classname
        $className = $layer.'_'.$request;

        // build location (path and filename)
        $classFileAndPath = DOOZR_APP_ROOT.str_replace('_', $this->_separator, $className).'.php';

        // check if requested layer file exists
        if ($this->_filesystem->exists($classFileAndPath)) {
            // include file
            include_once $classFileAndPath;

            // instanciate
            $instance = $this->instanciate($className, $classParams);
        }

        // return an instance in either case
        return $instance;
    }
}
