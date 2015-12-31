<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Response - Resolver.
 *
 * Resolver.php - Response resolver. Returns a response by request (route from request-state to MVP).
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
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Http.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Doozr - Request - Dispatcher.
 *
 * Request dispatcher for dispatching route from request state to MVP.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Response_Resolver extends Doozr_Base_Class
{
    /**
     * Presenter class for active route.
     *
     * @var string
     */
    protected $classname;

    /**
     * Method (Action) for active route.
     *
     * @var string
     */
    protected $method;

    /**
     * The active route.
     *
     * @var array
     */
    protected $route;

    /**
     * The directory separator
     * shortcut to DIRECTORY_SEPARATOR.
     *
     * @var string
     */
    protected $separator = DIRECTORY_SEPARATOR;

    /**
     * Instance of model.
     *
     * @var object
     */
    protected $model;

    /**
     * Instance of view.
     *
     * @var object
     */
    protected $view;

    /**
     * Presenter instance.
     *
     * @var Doozr_Base_Presenter
     */
    protected $presenter;

    /**
     * Response.
     *
     * @var Response
     */
    protected $response;

    /**
     * Request state instance.
     *
     * @var Doozr_Request_State
     */
    protected $requestState;

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param Doozr_Registry      $registry     Registry containing all kernel components
     * @param Doozr_Request_State $requestState Request state instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __construct(
        Doozr_Registry      $registry,
        Doozr_Request_State $requestState
    ) {
        $this
            ->registry($registry)
            ->requestState($requestState);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Marshalling everything for running MVP (run()) by request.
     *
     * @param Request  $request  Request to marshall from.
     * @param Response $response Response to use as base.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Response Instance for chaining
     */
    public function resolve(Request $request, Response $response)
    {
        /* @var Doozr_Request_Psr_Interface $request */

        // Ensure to put *route* in before ;) - here.
        $route  = $request->getAttribute('route');
        $target = $route->getPresenter();

        #$requestState = $request->export();
        #dump($requestState);

        // Extract data from request and transfer to a request state
        #$requestState = $this->convertRequestToRequestState($this->getRequestState(), $request);
        #dump($requestState);
        #die;

        $this
            ->response($response)
            ->route($route)
            ->classname($target)
            ->action($route->getAction())
            ->initMvp($target, $this->getRegistry(), $request);

        return $this->run();
    }

    /**
     * Takes values from a Request instance and transfer it to request state
     *
     * @param Request $request
     * @param Doozr_Request_State $requestState
     */
    protected function convertRequestToRequestState(Request $request, Doozr_Request_State $requestState)
    {
        /*
        // Set valid request sources
        $this->setRequestSources(
            $this->emitValidRequestSources(
                DOOZR_RUNTIME_ENVIRONMENT
            )
        );

        // HTTP Version of the request made
        $protocolVersion = explode('/', $_SERVER['SERVER_PROTOCOL']);

        // Store protocol version
        $this->withProtocolVersion(
            (true === isset($protocolVersion[1])) ? $protocolVersion[1] : '1.0'
        );

        // Store headers normalized to prevent System/OS/PHP mismatches
        $headers = $this->normalizeHeaders(getallheaders());
        foreach ($headers as $header => $value) {
            $this->withHeader($header, $value);
        }

        // Receive and store request method (HTTP verb)
        $this->withMethod(
            $this->receiveMethod()
        );

        // Emulate the request in case of PUT ...
        $this->equalizeRequestArguments(
            $this->getMethod(),
            $headers
        );

        // Store cookies
        $this->withCookieParams(
            $_COOKIE
        );

        // Store file uploads ...
        $files = [];
        foreach ($_FILES as $file) {
            $files[] = new Doozr_Request_File($file);
        }
        $this->withUploadedFiles(
            $files
        );

        // Store query params as array
        $queryArguments = [];
        parse_str($_SERVER['QUERY_STRING'], $queryArguments);
        $this->withQueryParams(
            $queryArguments
        );

        // Detect if Ajax and set flag
        $this->withAttribute('isAjax', $this->isAjax());

        // Store body arguments (_POST _PUT ...) as parsed body representation
        $this->withParsedBody(
            $this->receiveArguments($this->getMethod())
        );

        // Set the request target!
        $this->withRequestTarget($this->getUri()->getPath());

        //
        $requestState->withRequestTarget(
            $request->getRequestTarget()
        );
*/
    }



    /**
     * Dispatches the request to the backend layers. This can be "Model" "View" "Presenter".
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Response
     *
     * @throws \Doozr_Route_Exception
     */
    public function run()
    {
        // The MVP process is here ...
        /* @var Psr\Http\Message\ResponseInterface $response */
        $response  = $this->getResponse();
        $presenter = $this->getPresenter();
        $action    = $this->getAction();
        $view      = $this->getView();

        // Use inofficial standard "xAction()"
        $method = $action.'Action';

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
                        'Presenter_'.ucfirst($this->getClassname())
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
     *
     * @return $this Instance for chaining
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
     *
     * @return string The classname if set, otherwise NULL
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
     *
     * @return $this Instance for chaining
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
     *
     * @return Doozr_Request_Route_State The route if set, otherwise NULL
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
     *
     * @return $this Instance for chaining
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
     *
     * @return string The action if set, otherwise NULL
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
     *
     * @return $this Instance for chaining
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
     *
     * @return Doozr_Base_Presenter Doozr_Base_Presenter_Interface if set, otherwise NULL
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
     *
     * @return $this Instance for chaining
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
     *
     * @return Doozr_Base_Model_Interface|null Doozr_Base_Model_Interface if set, otherwise NULL
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
     *
     * @return $this Instance for chaining
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
     *
     * @return SplObserver The view if set, otherwise NULL
     */
    protected function getView()
    {
        return $this->view;
    }

    /**
     * Setter for response.
     *
     * @param Response $response The response
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setResponse(Response $response = null)
    {
        $this->response = $response;
    }

    /**
     * Fluent: Setter for response.
     *
     * @param Response $response The response
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function response(Response $response)
    {
        $this->setResponse($response);

        return $this;
    }

    /**
     * Getter for response.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Response The response if set, otherwise NULL
     */
    protected function getResponse()
    {
        return $this->response;
    }

    /**
     * Fluent setter for requestState.
     *
     * @param Doozr_Request_State $requestState Request state instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function setRequestState(Doozr_Request_State $requestState)
    {
        $this->requestState = $requestState;
    }

    /**
     * Fluent setter for requestState.
     *
     * @param Doozr_Request_State $requestState Request state instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function requestState(Doozr_Request_State $requestState)
    {
        $this->setRequestState($requestState);

        return $this;
    }

    /**
     * Getter for requestState.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Request_State
     */
    protected function getRequestState()
    {
        return $this->requestState;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Initializes the MVP layer by creating instances of Model, View & Presenter.
     *
     * @param string          $target   Target for MVP (name of class)
     * @param \Doozr_Registry $registry Instance of Doozr registry to inject
     * @param Request         $request  Request state to inject NOT REQUEST! only state cause no transform!
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function initMvp($target, Doozr_Registry $registry, Request $request)
    {
        // Try to get model instance
        $model = $this->modelFactory(
            $target,
            [
                $registry,
                $request,
            ]
        );

        // Try to get a view instance
        $view = $this->viewFactory(
            $target,
            [
                $registry,
                $request,
            ]
        );

        // Try to get presenter with model (view
        $presenter = $this->presenterFactory(
            $target,
            [
                $registry,
                $request,
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
     *
     * @return bool|int TRUE if request is valid, otherwise HTTP-Error like 400 ...
     */
    protected function validateRequest($instance, $method)
    {
        // Assume valid
        $validity = true;

        if (false === is_object($instance) || !$instance instanceof Doozr_Base_Presenter) {
            // No presenter instance = Bad Request = 400
            $validity = Doozr_Http::BAD_REQUEST;
        } elseif (false === method_exists($instance, $method) || false === is_callable([$instance, $method])) {
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
     *
     * @return Doozr_Base_Model|null
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
     *
     * @return Doozr_Base_View|null
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
     *
     * @return Doozr_Base_Presenter|null
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
     *
     * @return Doozr_Base_Presenter|Doozr_Base_Model|Doozr_Base_View|null
     */
    protected function layerFactory($request, $layer, $arguments = null)
    {
        // Assume instance won't be created
        $instance = null;

        // Build classname
        $classname = 'App\\'.$layer.'\\'.ucfirst($request);

        // Build location (path + filename)
        $classFileAndPath = $this->getRegistry()->getParameter('doozr.app.root').
                            str_replace('_', $this->separator, $classname).'.php';

        // Check if requested layer file exists
        if ($this->getRegistry()->getFilesystem()->exists($classFileAndPath)) {
            include_once $classFileAndPath;

            /* @var Doozr_Base_Presenter|Doozr_Base_Model|Doozr_Base_View $instance */
            $instance = self::instantiate($classname, $arguments);
        }

        return $instance;
    }
}
