<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Response - Resolver
 *
 * Resolver.php - Response resolver. Returns a response by request (route from request-state to MVP).
 *
 * PHP versions 5.5
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
 * @package    Doozr_Request
 * @subpackage Doozr_Response_Resolver
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Http.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Doozr - Request - Dispatcher
 *
 * Request dispatcher for dispatching route from request state to MVP.
 *
 * @category   Doozr
 * @package    Doozr_Request
 * @subpackage Doozr_Response_Resolver
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Response_Resolver extends Doozr_Base_Class
{
    /**
     * Presenter class for active route.
     *
     * @var string
     * @access protected
     */
    protected $classname;

    /**
     * Method (Action) for active route.
     *
     * @var string
     * @access protected
     */
    protected $method;

    /**
     * The active route.
     *
     * @var array
     * @access protected
     */
    protected $route;

    /**
     * The directory separator
     * shortcut to DIRECTORY_SEPARATOR
     *
     * @var string
     * @access protected
     */
    protected $separator = DIRECTORY_SEPARATOR;

    /**
     * Instance of model
     *
     * @var object
     * @access protected
     */
    protected $model;

    /**
     * Instance of view
     *
     * @var object
     * @access protected
     */
    protected $view;

    /**
     * Presenter instance
     *
     * @var Doozr_Base_Presenter
     * @access protected
     */
    protected $presenter;

    /**
     * Response
     *
     * @var Doozr_Response_Interface
     * @access protected
     */
    protected $response;

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param Doozr_Registry $registry Registry containing all kernel components
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     */
    public function __construct(
        Doozr_Registry $registry
    ) {
        $this
            ->registry($registry);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Marshalling everything for running MVP (run()) by request.
     *
     * @param Request  $request  The request to marshall from.
     * @param Response $response The response to use as base.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function resolve(Request $request, Response $response)
    {
        // Ensure to put *route* in before ;) - here.
        $route        = $request->getAttribute('route');
        $target       = $route->getPresenter();
        $requestState = $request->export();

        $this
            ->response($response)
            ->route($route)
            ->classname($target)
            ->action($route->getAction())
            ->initMvp($target, $this->getRegistry(), $requestState);

        return $this->run();
    }

    /**
     * Dispatches the request to the backend layers. This can be "Model" "View" "Presenter" in MVP runtimeEnvironment
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \Doozr_Response_Interface|Doozr_Response_Web
     * @access public
     * @throws \Doozr_Route_Exception
     */
    public function run()
    {
        // The MVP process is here ...
        $response  = $this->getResponse();
        $presenter = $this->getPresenter();
        $action    = $this->getAction();
        $view      = $this->getView();

        // Use inofficial standard "xAction()"
        $method = $action . 'Action';

        // Validate that request mapped to a route (foo:bar) can be executed by Doozr
        if (true === $httpStatus = $this->validateRequest($presenter, $method)) {

            // If we have a view attach it
            if (null !== $view && $view instanceof Doozr_Base_View) {
                $presenter->attach($view);
            }

            // Call the requested Action on requested Presenter (Presenter:Action)
            $data = $presenter->{$method}();

            // Create a response body with write access
            $responseBody = new Doozr_Response_Body('php://memory', 'w');
            $responseBody->write($data[Doozr_Base_Presenter::IDENTIFIER_VIEW]);

            $response = $response->withBody($responseBody);
            $response = $response->withStatus(Doozr_Http::OK);

        } else {
            // So if the Status is not TRUE (successful) it contains an integer for HTTP Response :)
            switch ($httpStatus) {
                case Doozr_Http::BAD_REQUEST:
                    $message = sprintf(
                        'No Presenter to execute route ("%s"). Sure it exists?',
                        $this->getRoute()
                    );
                    break;

                case Doozr_Http::NOT_FOUND:
                default:
                    $message = sprintf(
                        'Method "%s()" of class "%s" not callable. Sure it exists and it\'s public?',
                        $method,
                        'Presenter_' . ucfirst($this->getClassname())
                    );
                    break;
            }

            throw new Doozr_Route_Exception(
                $message,
                $httpStatus
            );
        }

        // Return all data returned and fetched in data
        return $response;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | SETTER & GETTER, ISSER & HASSER
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for classname.
     *
     * @param string $classname The classname for presenter of current request.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setClassname($classname)
    {
        $this->classname = $classname;
    }

    /**
     * Fluent: Setter for classname.
     *
     * @param string $classname The classname for presenter of current request.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function classname($classname)
    {
        $this->setClassname($classname);

        return $this;
    }

    /**
     * Getter for classname.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The classname if set, otherwise NULL
     * @access protected
     */
    protected function getClassname()
    {
        return $this->classname;
    }

    /**
     * Setter for route.
     *
     * @param Doozr_Request_Route_State $route The route of current request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setRoute(Doozr_Request_Route_State $route)
    {
        $this->route = $route;
    }

    /**
     * Setter for route.
     *
     * @param Doozr_Request_Route_State $route The route of current request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function route(Doozr_Request_Route_State $route)
    {
        $this->setRoute($route);

        return $this;
    }

    /**
     * Getter for route.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Request_Route_State The route if set, otherwise NULL
     * @access protected
     */
    protected function getRoute()
    {
        return $this->route;
    }

    /**
     * Setter for action.
     *
     * @param string $method The current action
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setAction($method)
    {
        $this->method = $method;
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
        return $this->method;
    }

    /**
     * Setter for presenter.
     *
     * @param Doozr_Base_Presenter_Interface $presenter The presenter instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setPresenter(Doozr_Base_Presenter_Interface $presenter = null)
    {
        $this->presenter = $presenter;
    }

    /**
     * Setter for presenter.
     *
     * @param Doozr_Base_Presenter_Interface $presenter The presenter instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function presenter(Doozr_Base_Presenter_Interface $presenter)
    {
        $this->setPresenter($presenter);

        return $this;
    }

    /**
     * Getter for presenter.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Base_Presenter|null Doozr_Base_Presenter_Interface if set, otherwise NULL
     * @access protected
     */
    protected function getPresenter()
    {
        return $this->presenter;
    }

    /**
     * Setter for model.
     *
     * @param Doozr_Base_Model_Interface $model The model
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setModel(Doozr_Base_Model_Interface $model = null)
    {
        $this->model = $model;
    }

    /**
     * Setter for model.
     *
     * @param Doozr_Base_Model_Interface $model The model
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
     * @return Doozr_Base_Model_Interface|null Doozr_Base_Model_Interface if set, otherwise NULL
     * @access protected
     */
    protected function getModel()
    {
        return $this->model;
    }

    /**
     * Setter for view.
     *
     * @param Doozr_Base_View_Interface $view The view
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setView(Doozr_Base_View_Interface $view = null)
    {
        $this->view = $view;
    }

    /**
     * Fluent: Setter for view.
     *
     * @param Doozr_Base_View_Interface $view The view
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function view(Doozr_Base_View_Interface $view)
    {
        $this->setView($view);

        return $this;
    }

    /**
     * Getter for view.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Base_View_Interface|null The view if set, otherwise NULL
     * @access protected
     */
    protected function getView()
    {
        return $this->view;
    }

    /**
     * Setter for response.
     *
     * @param Doozr_Response_Interface $response The response
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setResponse(Doozr_Response_Interface $response = null)
    {
        $this->response = $response;
    }

    /**
     * Fluent: Setter for response.
     *
     * @param Doozr_Response_Interface $response The response
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function response(Doozr_Response_Interface $response)
    {
        $this->setResponse($response);
        return $this;
    }

    /**
     * Getter for response.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Response_Interface|null The response if set, otherwise NULL
     * @access protected
     */
    protected function getResponse()
    {
        return $this->response;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Initializes the MVP layer by creating instances of Model, View & Presenter.
     *
     * @param string               $target       The target for MVP (name of class)
     * @param \Doozr_Registry      $registry     Instance of Doozr registry to inject
     * @param \Doozr_Request_State $requestState The request state to inject NOT REQUEST! only state cause no transform!
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function initMvp($target, Doozr_Registry $registry, Doozr_Request_State $requestState)
    {
        // Try to get model instance
        $model = $this->modelFactory(
            $target,
            [
                $registry,
                $requestState,
            ]
        );

        // Try to get a view instance
        $view = $this->viewFactory(
            $target,
            [
                $registry,
                $requestState,
                $registry->getParameter('doozr.kernel.caching'),
            ]
        );

        // Try to get presenter with model (view
        $presenter = $this->presenterFactory(
            $target,
            [
                $registry,
                $requestState,
                $model,
            ]
        );

        $this->setModel($model);
        $this->setView($view);
        $this->setPresenter($presenter);
    }

    /**
     * Validates the existing request data. A request needs at least a presenter-instance
     * (Presenter) and an entry point (e.g. Main()) to be valid.
     *
     * @param string $instance The name of the presenter class (Presenter, Controller)
     * @param string $method   The name of the method
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool|integer TRUE if request is valid, otherwise HTTP-Error like 400 ...
     * @access protected
     */
    protected function validateRequest($instance, $method)
    {
        // Assume valid
        $validity = true;

        if (false === is_object($instance) || !$instance instanceof Doozr_Base_Presenter) {
            // No presenter instance = Bad Request = 400
            $validity = Doozr_Http::BAD_REQUEST;

        } elseif (false === method_exists($instance, $method) || false === is_callable(array($instance, $method))) {
            // No action (method) to call on existing presenter = Not Found = 404
            $validity = Doozr_Http::NOT_FOUND;
        }

        return $validity;
    }

    /**
     * Returns the model layer.
     *
     * @param string     $target    The name of the current model.
     * @param null|array $arguments The optional arguments to pass to model.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Base_Model
     * @access protected
     */
    protected function modelFactory($target, $arguments = null)
    {
        return $this->layerFactory($target, 'Model', $arguments);
    }

    /**
     * Returns the view layer.
     *
     * @param string     $target    The name of the current view.
     * @param null|array $arguments The optional arguments to pass to view.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Base_View
     * @access protected
     */
    protected function viewFactory($target, $arguments = null)
    {
        return $this->layerFactory($target, 'View', $arguments);
    }

    /**
     * Returns the presenter layer.
     *
     * @param string     $target    The name of the current presenter.
     * @param null|array $arguments The optional arguments to pass to presenter.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Base_Presenter_Interface
     * @access protected
     */
    protected function presenterFactory($target, $arguments = null)
    {
        return $this->layerFactory($target, 'Presenter', $arguments);
    }

    /**
     * Creates and returns an instance of a layer (can be either Model|View|Presenter).
     *
     * @param string $request   The resource requested
     * @param string $layer     The part/layer of the MVP structure to instantiate and return
     * @param array  $arguments An array of Parameters to append at instantiation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Base_Presenter|Doozr_Base_Model|Doozr_Base_View
     * @access protected
     */
    protected function layerFactory($request, $layer, $arguments = null)
    {
        // Assume instance won't be created
        $instance = null;

        // Build classname
        $classname = $layer . '_' . ucfirst($request);

        // Build location (path + filename)
        $classFileAndPath = $this->getRegistry()->getParameter('doozr.app.root') .
                            str_replace('_', $this->separator, $classname) . '.php';

        // Check if requested layer file exists
        if ($this->getRegistry()->getFilesystem()->exists($classFileAndPath)) {
            include_once $classFileAndPath;
            $instance = self::instantiate($classname, $arguments);
        }

        return $instance;
    }
}
