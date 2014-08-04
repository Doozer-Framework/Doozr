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
 * Copyright (c) 2005 - 2014, Benjamin Carl - All rights reserved.
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
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Connector/Interface.php';
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Model/Interface.php';
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/View/Interface.php';
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Class/Singleton.php';

/**
 * DoozR - Controller - Back
 *
 * The Back-Controller of the DoozR-Framework.
 *
 * @category   DoozR
 * @package    DoozR_Controller
 * @subpackage DoozR_Controller_Back
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Controller_Back extends DoozR_Base_Class_Singleton
{
    /**
     * The object of active route.
     * E.g. for [../foo/bar/baz/...] = [foo]
     *
     * @var string
     * @access protected
     */
    protected $object;

    /**
     * The action of the current route
     *
     * @var string
     * @access protected
     */
    protected $action;

    /**
     * The route currently processed
     *
     * @var array
     * @access protected
     */
    protected $route;

    /**
     * The translation for current route
     *
     * @var array
     * @access protected
     */
    protected $translation;


    /**
     * holds the directory separator
     *
     * this var is a shortcut to DIRECTORY_SEPARATOR
     *
     * @var string
     * @access protected
     */
    protected $separator = DIRECTORY_SEPARATOR;

    /**
     * holds the instance of model
     *
     * this var holds the instance of the model
     *
     * @var object
     * @access protected
     */
    protected $model;

    /**
     * holds the instance of view
     *
     * this var holds the instance of the view
     *
     * @var object
     * @access protected
     */
    protected $view;

    /**
     * contains an instance of Presentor if MVP-pattern is used,
     * otherwise an instance of Controller if MVC-pattern is active.
     *
     * @var object
     * @access protected
     */
    protected $connector;

    /**
     * contains instance of config
     *
     * @var DoozR_Config_Interface
     * @access protected
     */
    protected $config;

    /**
     * Logger instance
     *
     * @var DoozR_Logger_Interface
     * @access protected
     */
    protected $logger;

    /**
     * Instance of filesystem service.
     *
     * @var DoozR_Filesystem_Service
     * @access protected
     */
    protected $filesystem;

    /**
     * DoozR caching service instance.
     *
     * @var DoozR_Cache_Service
     * @access protected
     */
    protected $cache;

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
     * Constructor.
     *
     * @param DoozR_Config_Interface   $config     The instance of the DoozR core config
     * @param DoozR_Logger_Interface   $logger     The instance of the DoozR huge logging facade (subsystem)
     * @param DoozR_Filesystem_Service $filesystem Instance of filesystem service
     * @param DoozR_Cache_Service      $cache      Instance of cache service
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Controller_Back
     * @access public
     */
    public function __construct(
        DoozR_Config_Interface   $config,
        DoozR_Logger_Interface   $logger,
        DoozR_Filesystem_Service $filesystem,
        DoozR_Cache_Service      $cache
    ) {
        $this
            ->configuration($config)
            ->logger($logger)
            ->filesystem($filesystem)
            ->cache($cache);
    }

    /**
     * Runs the request passed as argument by dispatching it to the backend layers.
     * This can be "Model" "View" "Presenter" in MVP mode or "Model" "View" "Controller"
     * in MVC mode.
     *
     * @param DoozR_Base_State_Interface $requestState The request state
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     * @throws DoozR_Exception
     */
    public function run(DoozR_Base_State_Interface $requestState)
    {
        $this->setRoute($requestState->getActiveRoute());
        $this->setTranslation($requestState->getTranslationMatrix());
        $this->setObject($this->getRoute()[$this->getTranslation()[0]]);
        $this->setAction($this->getRoute()[$this->getTranslation()[1]]);

        // Init MV(P|C) layer
        switch ($requestState->getPattern()) {

            case 'MVP':
            // MODEL
            $this->setModel(
                $this->initModel(
                    $this->getObject(),
                    array(
                        $requestState,
                        $this->getRoute(),
                        $this->getTranslation(),
                        $this->getCache(),
                        $this->getConfig(),
                    )
                )
            );

            // VIEW
            $this->setView (
                $this->initView(
                    $this->getObject(),
                    array(
                        $requestState,
                        $this->getRoute(),
                        $this->getTranslation(),
                        $this->getCache(),
                        $this->getConfig(),
                        DoozR_Controller_Front::getInstance(DoozR_Registry::getInstance()),
                    )
                )
            );

            // CONNECTOR => PRESENTER or CONTROLLER
            $this->setConnector(
                $this->initConnector(
                    $this->getObject(),
                    'Presenter',
                    array(
                        $requestState,
                        $this->getRoute(),
                        $this->getTranslation(),
                        $this->getConfig(),
                        $this->model,
                        $this->view,
                    )
                )
            );
            break;

        case 'MVC':
            default:
            throw new DoozR_Exception(
                'MVC pattern not yet implemented!'
            );
            break;
        }

        // Dispatch the prepared objects
        return $this->dispatch(
            $this->getConnector(),
            $this->getModel(),
            $this->getView(),
            $this->getObject(),
            $this->getAction()
        );
    }

    /**
     *
     * @param $connector
     * @param $model
     * @param $view
     *
     * @return bool TRUE on success, otherwise FALSE
     * @access protected
     * @throws DoozR_Connector_Exception
     */
    protected function dispatch($connector, $model, $view, $object, $action)
    {
        // We must respond with an exception here cause this should never ever happen and so its an
        // exceptional state and nothing we must handle with a nice response! We see this as an server
        // triggered error so its an 5XXer. 501 means: Not Implemented (sounds good)
        if (($status = $this->validateRequest($connector, $action)) !== true) {
            throw new DoozR_Connector_Exception(
                'Method: "' . $action . '()" in instance of class: "' . $object .
                '" not available/callable. Sure it exists?',
                501
            );

        } else {
            if ($view !== null && $view instanceof DoozR_Base_View) {
                $this->connector->attach($this->view);
            }

            // We try to execute the call to presenter e.g. $presenter->Main()
            try {
                $this->connector->{$action}();

            } catch (DoozR_Base_Presenter_Rest_Exception $e) {
                $this->sendHttpResponse($e->getCode(), $e->getMessage(), true);

            } catch (DoozR_Base_Presenter_Exception $e) {
                $this->sendHttpResponse($e->getCode(), $e->getMessage());

            }
        }

        // return instance for chaining
        return $this;
    }


    protected function sendHttpResponse($code, $message, $json = false)
    {
        /* @var $front DoozR_Controller_Front */
        $front = DoozR_Controller_Front::getInstance();

        /* @var $response DoozR_Response_Web */
        $response = $front->getResponse();

        if ($json === true) {
            $response->sendJson(
                json_encode(
                    array('error' => array($message))
                ),
                null,
                'UTF-8',
                true,
                false,
                true,
                $code
            );
        } else {
            $response->sendHttpStatus(
                $code,
                null,
                true,
                $message
            );
        }

        exit;
    }

    /**
     * Validates the existing request data. A request needs at least a connector-instance
     * (Presenter) and an entry point (e.g. Main()) to be valid.
     *
     * @param string $instance The name of the connector class (Presenter, Controller)
     * @param string $method   The name of the method
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean|integer TRUE if request is valid, otherwise HTTP-Error like 400 ...
     * @access protected
     */
    protected function validateRequest($instance, $method)
    {
        // Assume valid
        $valid = true;

        // no connector instance = Bad Request = 400
        if ($instance === null) {
            $valid = self::HTTP_STATUS_400;

        } elseif (method_exists($instance, $method) === false) {
            // No action to call after existing connector exist = Not Found = 404
            $valid = self::HTTP_STATUS_404;
        }

        return $valid;
    }

    protected function setObject($object)
    {
        $this->object = $object;
    }

    protected function object($object)
    {
        $this->setObject($object);
        return $this;
    }

    protected function getObject()
    {
        return $this->object;
    }

    protected function setRoute($route)
    {
        $this->route = $route;
    }

    protected function route($route)
    {
        $this->setRoute($route);
        return $this;
    }

    protected function getRoute()
    {
        return $this->route;
    }

    protected function setTranslation($translation)
    {
        $this->translation = $translation;
    }

    protected function translation($translation)
    {
        $this->setTranslation($translation);
        return $this;
    }

    protected function getTranslation()
    {
        return $this->translation;
    }

    protected function setAction($action)
    {
        $this->action = $action;
    }

    protected function action($action)
    {
        $this->setAction($action);
        return $this;
    }

    protected function getAction()
    {
        return $this->action;
    }

    protected function setConfig($configuration)
    {
        $this->config = $configuration;
    }

    protected function configuration($configuration)
    {
        $this->setConfig($configuration);
        return $this;
    }

    protected function getConfig()
    {
        return $this->config;
    }

    protected function setLogger(DoozR_Logger_Interface $logger)
    {
        $this->logger = $logger;
    }

    protected function logger(DoozR_Logger_Interface $logger)
    {
        $this->setLogger($logger);
        return $this;
    }

    protected function getLogger()
    {
        return $this->logger;
    }

    protected function setFilesystem(DoozR_Filesystem_Service $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    protected function filesystem(DoozR_Filesystem_Service $filesystem)
    {
        $this->setFilesystem($filesystem);
        return $this;
    }

    protected function getFilesystem()
    {
        return $this->filesystem;
    }

    protected function setCache(DoozR_Cache_Service $cache)
    {
        $this->cache = $cache;
    }

    protected function cache(DoozR_Cache_Service $cache)
    {
        $this->setCache($cache);
        return $this;
    }

    protected function getCache()
    {
        return $this->cache;
    }

    protected function setConnector($connector)
    {
        $this->connector = $connector;
    }

    protected function connector($connector)
    {
        $this->setConnector($connector);
        return $this;
    }

    /**
     * @return DoozR_Base_Connector_Interface
     */
    protected function getConnector()
    {
        return $this->connector;
    }

    protected function setModel($model)
    {
        $this->model = $model;
    }

    protected function model($model)
    {
        $this->setConnector($model);
        return $this;
    }

    /**
     * @return DoozR_Base_Model_Interface
     */
    protected function getModel()
    {
        return $this->model;
    }

    protected function setView($view)
    {
        $this->view = $view;
    }

    protected function view($view)
    {
        $this->setView($view);
        return $this;
    }

    /**
     * @return DoozR_Base_View_Interface
     */
    protected function getView()
    {
        return $this->view;
    }

    /**
     * Returns the model layer.
     *
     * @param string     $model     The name of the current model.
     * @param null|array $arguments The optional arguments to pass to model.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Base_Model
     * @access protected
     */
    protected function initModel($model, $arguments = null)
    {
        return $this->initLayer($model, 'Model', $arguments);
    }

    /**
     * Returns the view layer.
     *
     * @param string     $view      The name of the current view.
     * @param null|array $arguments The optional arguments to pass to view.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Base_View
     * @access protected
     */
    protected function initView($view, $arguments = null)
    {
        return $this->initLayer($view, 'View', $arguments);
    }

    /**
     * Returns the connector layer.
     *
     * @param string     $connector The name of the current connector.
     * @param string     $type      The type of the current connector.
     * @param null|array $arguments The optional arguments to pass to connector.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Base_Connector_Interface
     * @access protected
     */
    protected function initConnector($connector, $type, $arguments = null)
    {
        return $this->initLayer($connector, $type, $arguments);
    }

    /**
     * Creates and returns an instance of a layer
     *
     * This method is intend to instanciate a new layer
     * (can be either Model|View|Controller|Presenter).
     *
     * @param string $request   The resource requested
     * @param string $layer     The part/layer of the MVC/P structure to instanciate and return
     * @param array  $arguments An array of Parameters to append at instanciation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Base_Connector_Interface|DoozR_Base_Model|DoozR_Base_View
     * @access protected
     */
    protected function initLayer($request, $layer = 'Model', $arguments = null)
    {
        // assume instance won't be created
        $instance = null;

        // build classname
        $classname = $layer . '_' . $request;

        // build location (path and filename)
        $classFileAndPath = DOOZR_APP_ROOT . str_replace('_', $this->separator, $classname) . '.php';

        // check if requested layer file exists
        if ($this->filesystem->exists($classFileAndPath)) {

            // Include file
            include_once $classFileAndPath;

            // instanciate
            $instance = $this->instanciate($classname, $arguments);
        }

        // return an instance in either case
        return $instance;
    }
}
