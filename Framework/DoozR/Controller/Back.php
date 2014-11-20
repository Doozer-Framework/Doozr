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
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Http.php';

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

    const HTTP_STATUS_400 = 400;
    const HTTP_STATUS_404 = 404;


    /**
     * Constructor.
     *
     * @param DoozR_Registry           $registry   Instance of DoozR_Registry containing all core components
     * @param DoozR_Config_Interface   $config     Instance of the DoozR core config
     * @param DoozR_Logger_Interface   $logger     Instance of the DoozR logging facade (subsystem)
     * @param DoozR_Filesystem_Service $filesystem Instance of filesystem service
     * @param DoozR_Cache_Service      $cache      Instance of cache service
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Controller_Back
     * @access public
     */
    public function __construct(
        DoozR_Registry           $registry,
        DoozR_Config_Interface   $config,
        DoozR_Logger_Interface   $logger,
        DoozR_Filesystem_Service $filesystem,
        DoozR_Cache_Service      $cache
    ) {
        $this
            ->registry($registry)
            ->configuration($config)
            ->logger($logger)
            ->filesystem($filesystem)
            ->cache($cache);
    }

    /**
     * Runs the request passed as argument by dispatching it to the backend layers.
     * This can be "Model" "View" "Presenter" in MVP runtimeEnvironment or "Model" "View" "Controller"
     * in MVC runtimeEnvironment.
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

        // MODEL
        $this->setModel(
            $this->initModel(
                $this->getObject(),
                array(
                    $this->getRegistry(),
                    $requestState,
                    $this->getRoute(),
                    $this->getTranslation(),
                    $this->getCache(),
                    $this->getConfiguration(),
                )
            )
        );

        // VIEW
        $this->setView (
            $this->initView(
                $this->getObject(),
                array(
                    $this->getRegistry(),
                    $requestState,
                    $this->getRoute(),
                    $this->getTranslation(),
                    $this->getCache(),
                    $this->getConfiguration(),
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
                    $this->getRegistry(),
                    $requestState,
                    $this->getRoute(),
                    $this->getTranslation(),
                    $this->getConfiguration(),
                    $this->model,
                    $this->view,
                )
            )
        );

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
     * Dispatches a call.
     *
     * @param $connector
     * @param $model
     * @param $view
     * @param $object
     * @param $action
     *
     * @return $this Instance for chaining
     * @access protected
     * @throws DoozR_Connector_Exception
     */
    protected function dispatch($connector, $model, $view, $object, $action)
    {
        // Adjust to non official standard fooAction() for actions in presenter.
        $method = $action . 'Action';

        /**
         * We must respond with an exception here cause this should never ever happen and so its an
         * exceptional state and nothing we must handle with a nice response! This can be a client or server
         * triggered error/exception so we decide to give the client the responsibility by returning 400 resp. 404
         */
        if (($status = $this->validateRequest($connector, $method)) !== true) {

            switch ($status) {
                case self::HTTP_STATUS_400:
                    $message = 'No connector instance to execute route ("/' . $object .'/' . $action . '") on. Sure it exists?';
                    break;

                case self::HTTP_STATUS_404:
                default:
                    $message = 'Method: "' . $method . '()" in instance of class: "' . $object . '" not callable. Sure it exists?';
                    break;
            }

            throw new DoozR_Connector_Exception(
                $message,
                $status
            );

        } else {
            if ($view !== null && $view instanceof DoozR_Base_View) {
                $this->connector->attach($this->view);
            }

            // We try to execute the call to presenter e.g. $presenter->Main()
            try {
                $this->connector->{$method}();

            } catch (DoozR_Base_Presenter_Rest_Exception $e) {
                // Send JSON response on REST requests
                //$this->sendHttpResponse($e->getCode(), $e->getMessage(), true);

                /**
                 * At this point the exception was populated throughout the whole subsystem.
                 * Its totally clear what the error was and now the question should! be
                 * Is this a dev session? (debug === true) then send the whole exception + our
                 * default format for REST responses! If not debug send only the default fields!
                 */
                $this->sendJsonResponse($this->repackExceptionData($e));

            } catch (DoozR_Base_Presenter_Exception $e) {
                // Send "normal" (default) response on default requests
                $this->sendHttpResponse($e->getCode(), $e->getMessage());

            }
        }

        // Return instance for chaining
        return $this;
    }

    /**
     * Disassemble exception to a usable format.
     *
     * @param Exception $exception The exception to disassemble
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \stdClass New assembled object
     * @access protected
     */
    protected function repackExceptionData(Exception $exception)
    {
        // Get debug state
        $debug = (defined('DOOZR_DEBUG') === true) ? DOOZR_DEBUG : true;

        $data           = new \stdClass();
        $data->message  = $exception->getMessage();
        $data->code     = $exception->getCode();
        $data->meta     = array();
        $data->security = array();

        if (isset($exception->token) === true) {
            $data->security['token'] = $exception->token;
        }

        if ($debug === true) {
            $data->meta['code']     = $exception->getCode();
            $data->meta['file']     = $exception->getFile();
            $data->meta['line']     = $exception->getLine();
            $data->meta['previous'] = object_to_array($exception->getPrevious());
        }

        return $data;
    }

    /**
     * Sends a HTTP Response to client using front controller.
     * Is used in case of core errors which can't be processed through system.
     *
     * @param string $code    The code used for response e.g. 404 ...
     * @param string $message The message for response
     * @param bool   $json    TRUE to send as JSON response, FALSE to send plain status
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
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


    protected function sendJsonResponse($e)
    {
        /* @var $front DoozR_Controller_Front */
        $front = DoozR_Controller_Front::getInstance();

        /* @var $response DoozR_Response_Web */
        $response = $front->getResponse();

        $response->sendJson(
            $e,
            null,
            null,
            false,
            false,
            true,
            $e->code
        );
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

    /**
     * Setter for object.
     *
     * @param string $object The object of current request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setObject($object)
    {
        $this->object = $object;
    }

    /**
     * Setter for object.
     *
     * @param string $object The object of current request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function object($object)
    {
        $this->setObject($object);
        return $this;
    }

    /**
     * Getter for object.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The object is set, otherwise NULL
     * @access protected
     */
    protected function getObject()
    {
        return $this->object;
    }

    /**
     * Setter for route.
     *
     * @param string $route The route of current request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * Setter for route.
     *
     * @param string $route The route of current request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function route($route)
    {
        $this->setRoute($route);
        return $this;
    }

    /**
     * Getter for route.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The route if set, otherwise NULL
     * @access protected
     */
    protected function getRoute()
    {
        return $this->route;
    }

    /**
     * Setter for translation.
     *
     * @param array $translation The translation of current request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setTranslation(array $translation)
    {
        $this->translation = $translation;
    }

    /**
     * Setter for translation.
     *
     * @param array $translation The translation of current request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function translation(array $translation)
    {
        $this->setTranslation($translation);
        return $this;
    }

    /**
     * Getter for translation.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array|null Translation as array if set, otherwise NULL
     * @access protected
     */
    protected function getTranslation()
    {
        return $this->translation;
    }

    /**
     * Setter for action.
     *
     * @param string $action The current action
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Setter for action.
     *
     * @param string $action The current action
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function action($action)
    {
        $this->setAction($action);
        return $this;
    }

    /**
     * Getter for action.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The action if set, otherwise NULL
     * @access protected
     */
    protected function getAction()
    {
        return $this->action;
    }

    /**
     * Setter for configuration.
     *
     * @param DoozR_Config_Interface $configuration Instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setConfiguration($configuration)
    {
        $this->config = $configuration;
    }

    /**
     * Setter for configuration.
     *
     * @param DoozR_Config_Interface $configuration Instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function configuration($configuration)
    {
        $this->setConfiguration($configuration);
        return $this;
    }

    /**
     * Getter for configuration.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Config_Interface|null DoozR_Config_Interface if set, otherwise NULL
     * @access protected
     */
    protected function getConfiguration()
    {
        return $this->config;
    }

    /**
     * Setter for logger.
     *
     * @param DoozR_Logger_Interface $logger Instance of logger
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setLogger(DoozR_Logger_Interface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Setter for logger.
     *
     * @param DoozR_Logger_Interface $logger Instance of logger
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function logger(DoozR_Logger_Interface $logger)
    {
        $this->setLogger($logger);
        return $this;
    }

    /**
     * Getter for logger.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Logger_Interface|null DoozR_Logger_Interface if set, otherwise NULL
     * @access protected
     */
    protected function getLogger()
    {
        return $this->logger;
    }

    /**
     * Setter for filesystem.
     *
     * @param DoozR_Filesystem_Service $filesystem The filesystem service instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setFilesystem(DoozR_Filesystem_Service $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Setter for filesystem.
     *
     * @param DoozR_Filesystem_Service $filesystem The filesystem service instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function filesystem(DoozR_Filesystem_Service $filesystem)
    {
        $this->setFilesystem($filesystem);
        return $this;
    }

    /**
     * Getter for filesystem.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Filesystem_Service|null DoozR_Filesystem_Service if set, otherwise NULL
     * @access protected
     */
    protected function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * Setter for cache.
     *
     * @param DoozR_Cache_Service $cache Instance of DoozR cache service
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setCache(DoozR_Cache_Service $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Setter for cache.
     *
     * @param DoozR_Cache_Service $cache Instance of DoozR cache service
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function cache(DoozR_Cache_Service $cache)
    {
        $this->setCache($cache);
        return $this;
    }

    /**
     * Getter for cache.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Cache_Service|null DoozR_Cache_Service if set, otherwise NULL
     * @access protected
     */
    protected function getCache()
    {
        return $this->cache;
    }

    /**
     * Setter for connector.
     *
     * @param DoozR_Base_Connector_Interface $connector The connector instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setConnector(DoozR_Base_Connector_Interface $connector = null)
    {
        $this->connector = $connector;
    }

    /**
     * Setter for connector.
     *
     * @param DoozR_Base_Connector_Interface $connector The connector instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function connector(DoozR_Base_Connector_Interface $connector)
    {
        $this->setConnector($connector);
        return $this;
    }

    /**
     * Getter for connector.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Base_Connector_Interface|null DoozR_Base_Connector_Interface if set, otherwise NULL
     * @access protected
     */
    protected function getConnector()
    {
        return $this->connector;
    }

    /**
     * Setter for model.
     *
     * @param DoozR_Base_Model_Interface $model The model
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setModel(DoozR_Base_Model_Interface $model = null)
    {
        $this->model = $model;
    }

    /**
     * Setter for model.
     *
     * @param DoozR_Base_Model_Interface $model The model
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function model($model)
    {
        $this->setModel($model);
        return $this;
    }

    /**
     * Getter for model.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Base_Model_Interface|null DoozR_Base_Model_Interface if set, otherwise NULL
     * @access protected
     */
    protected function getModel()
    {
        return $this->model;
    }

    /**
     * Setter for view.
     *
     * @param DoozR_Base_View_Interface $view The view
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setView(DoozR_Base_View_Interface $view = null)
    {
        $this->view = $view;
    }

    /**
     * Setter for view.
     *
     * @param DoozR_Base_View_Interface $view The view
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function view(DoozR_Base_View_Interface $view)
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
