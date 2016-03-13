<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Base - Presenter - Rest.
 *
 * Rest.php - Base class for presenter-layers from MVP with REST support
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
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Presenter.php';

/**
 * Doozr - Base Presenter.
 *
 * Base Presenter of the Doozr Framework.
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
class Doozr_Base_Presenter_Rest extends Doozr_Base_Presenter
{
    /**
     * The rest service.
     *
     * @var Doozr_Rest_Service
     */
    protected $rest;

    /**
     * Root node of API (default = /api/).
     *
     * @var string
     */
    protected $rootNode = '/api/';

    /**
     * The current route setup as tree representation.
     *
     * @var array
     */
    protected $routeTree;

    /**
     * Routes collection of REST API.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * The separator for the route (URL).
     *
     * @var string
     */
    const ROUTE_SEPARATOR = '/';

    /**
     * HTTP Status default = OK.
     *
     * @var int
     */
    const STATUS_OK_DEFAULT = 200;

    /**
     * HTTP Status 201 = OK for create.
     *
     * @var int
     */
    const STATUS_OK_CREATE = 201;

    /**
     * HTTP Status 204 = OK for delete.
     *
     * @var int
     */
    const STATUS_OK_DELETED = 204;

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param Doozr_Registry                $registry      Instance of Doozr_Registry containing all core components
     * @param Doozr_Base_State_Interface    $requestState  The whole request as processed by "Route"
     * @param array                         $request       The request
     * @param array                         $translation   The translation required to read the request
     * @param Doozr_Configuration_Interface $configuration The Doozr main config instance
     * @param Doozr_Base_Model              $model         The model to communicate with backend (db)
     * @param Doozr_Base_View               $view          The view to display results
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return \Doozr_Base_Presenter_Rest
     */
    public function __construct(
        Doozr_Registry                $registry,
        Doozr_Base_State_Interface    $requestState,
        array                         $request,
        array                         $translation,
        Doozr_Configuration_Interface $configuration = null,
        Doozr_Base_Model              $model = null,
        Doozr_Base_View               $view = null
    ) {
        // We need to hook in here - to make use of this proxy for installing JsonResponseHandler ;)
        /*
        $whoops = new Whoops\Run();
        $jsonErrorHandler = new Whoops\Handler\JsonResponseHandler();
        #$jsonErrorHandler->onlyForAjaxRequests(true);
        $whoops->pushHandler($jsonErrorHandler);
        $whoops->register();
        */

        // Forward (proxy) to parent
        parent::__construct(
            $registry,
            $requestState,
            $request,
            $translation,
            $configuration,
            $model,
            $view
        );
    }

    /**
     * The main entry point.
     *
     * Mainly extracts the resource which was called.
     *
     * @example If you would request something like /api/users/123 and /api is the root node this main would extract
     *          /users/123 as resource called.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @throws Doozr_Base_Presenter_Rest_Exception
     */
    public function Main()
    {
        // Get REAL action (hey dude u know this is the Main() API entry like the main.cpp ;)
        $resource = $this->getStateObject()->get($this->rootNode.'{{resource}}', function ($resource) {
                return $resource;
            }
        );

        // Get method in correct formatting
        $method = strtolower($resource).'Action';

        // Try to dispatch to action or fail with exception if action does not exist
        if (is_callable(array($this, $method))) {
            // Setup the routes (subrouting)
            $this->{$method}();

            // And return the result of the run (executed subrouting!)
            $this->run();
        } else {
            throw new Doozr_Base_Presenter_Rest_Exception(
                'The resource "'.$resource.'" is unknown to me. I never heard about it before.',
                404
            );
        }
    }

    /**
     * Setter for REST.
     *
     * @param Doozr_Rest_Service $rest A rest service instance to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setRest(Doozr_Rest_Service $rest)
    {
        $this->rest = $rest;
    }

    /**
     * Getter for REST.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Rest_Service The rest service instance
     */
    public function getRest()
    {
        return $this->rest;
    }

    /**
     * Registers a new route.
     *
     * @param Doozr_Base_Presenter_Rest_Config $config The config
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function registerRoute(Doozr_Base_Presenter_Rest_Config $config)
    {
        $this->routes[$config->getRoute()] = $config;
    }

    /**
     * Registers a new route.
     *
     * @param array $routes A collection of routes to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function registerRoutes(array $routes)
    {
        foreach ($routes as $route => $config) {
            $this->registerRoute($route, $config);
        }
    }

    /**
     * Setter for route tree representation.
     *
     * @param array $routeTree The route tree
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setRouteTree(array $routeTree)
    {
        $this->routeTree = $routeTree;
    }

    /**
     * Getter for route tree.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The route tree
     */
    protected function getRouteTree()
    {
        return $this->routeTree;
    }

    /**
     * Setter for routes.
     *
     * @param array $routes The routes
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setRoutes(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Getter for routes.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The routes
     */
    protected function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Returns the route matched by URL including config and extracted Ids ...
     * We do only throws exceptions here instead of sending header directives like 404 405 406.
     * This is responsibility of the implementing application cause here too high level.
     *
     * @param string $url The URL to return route for.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Base_Presenter_Rest_Config|false The config if route could be revsolved,
     *                                                false if route could not be resolved
     *
     * @throws Doozr_Base_Presenter_Rest_Exception
     */
    protected function getRouteByUrl($url)
    {
        $url = str_replace($this->rootNode, '', $url);
        $nodes = explode(self::ROUTE_SEPARATOR, $url);
        $routeTree = $this->routeTree;
        $ids = [];
        $route = [];
        $countNodes = count($nodes);
        $uid = null;

        // Lookup route ...
        for ($i = 0; $i < $countNodes; ++$i) {

            // Is regular route way/node ?
            if (is_array($routeTree) && isset($routeTree[$nodes[$i]])) {
                $routeTree = $routeTree[$nodes[$i]];
            } elseif (preg_match('/{{(.*)}}/i', key($routeTree), $variable) > 0) {
                // maybe its a variable node value
                $nodes[$i] = '{{'.$variable[1].'}}';
                $id = $this->extractId($variable[1]);
                if ($id !== null) {
                    $uid = $id;
                }

                $ids[] = $nodes[$i];
                $routeTree = $routeTree[$nodes[$i]];
            } else {
                throw new Doozr_Base_Presenter_Rest_Exception(
                    'Route for URL "'.$url.'" seems wrong. It could not be resolved.',
                    400
                );
            }

            $route[] = $nodes[$i];

            if ($i === ($countNodes - 1)) {
                if (is_object($routeTree) === true) {
                    // Inject Ids for reverse lookup
                    /* @var $routeTree Doozr_Base_Presenter_Rest_Config */
                    $routeTree
                        ->id($uid)
                        ->ids($ids)
                        ->url($url)
                        ->realRoute($route)
                        ->rootNode($this->rootNode);
                } else {
                    // In this case we ended up before we got config!
                    throw new Doozr_Base_Presenter_Rest_Exception(
                        'Route for URL "'.$url.'" seems incomplete.',
                        406
                    );
                }
            }
        }

        return $routeTree;
    }

    /**
     * Returns boolean status if input is Id field.
     *
     * @param string $input The input to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if field is Id field, otherwise FALSE
     */
    protected function isId($input)
    {
        // Assume false result
        $result = false;

        // Make the search easier
        $input = strtolower($input);

        // We got the Id field if the first 2 or the last 2 characters are === id (e.g. IdMessages, MessageId, Id, ...)
        if (substr($input, 0, 2) === 'id' || substr($input, -2, 2) === 'id') {
            $result = true;
        }

        return $result;
    }

    /**
     * Getter for state object.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Request_State
     */
    protected function getStateObject()
    {
        return $this->stateObject;
    }

    /**
     * Executes the configured subroutes if any matches.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @throws Doozr_Base_Presenter_Rest_Exception
     */
    protected function run()
    {
        // First convert via subrouting defined routes to a parsable array tree
        $this->setRouteTree(explodeTree($this->getRoutes(), self::ROUTE_SEPARATOR));

        // Retrieve route config via match by current request URL ;)
        $routeConfig = $this->getRouteByUrl($this->getStateObject()->getUrl());

        // Real processed request method depends on the override header if set. Otherwise normal request type is used
        $headers = $this->getStateObject()->getHeaders();

        // Override set? and is allowed for the resource? Sometimes clients are not able to e.g. tunnel PUT, DELETE ...
        if (isset($headers['X_HTTP_METHOD_OVERRIDE']) === true && $routeConfig->getOverride() === true) {
            $requestMethod = $headers['X_HTTP_METHOD_OVERRIDE'];

            // Inject into object we use as base -> update :)
            $this->getStateObject()->setMethod($requestMethod);
        } else {
            $requestMethod = $this->getStateObject()->getMethod();
        }

        // Request method should not be processed.
        if ($routeConfig->isAllowed($requestMethod) === false) {
            throw new Doozr_Base_Presenter_Rest_Exception(
                'Method "'.$this->getStateObject()->getMethod().'" not allowed.',
                405
            );
        }

        // Missing argument + message
        if ($this->validateInputArguments(
                $routeConfig->getRequired(),
                $this->getStateObject()->getQueryParams()
            ) !== true
        ) {
            $missingArguments = [];

            foreach ($routeConfig->getRequired() as $key => $value) {
                if (array_key_exists($key, $this->getStateObject()->getQueryParams()->getArray()) === false) {
                    $missingArguments[] = $key.(($value !== null) ? ' => '.$value : '');
                }
            }

            throw new Doozr_Base_Presenter_Rest_Exception(
                'Missing required argument'.((count($missingArguments) > 1) ? 's' : '').': '.
                implode(',', $missingArguments),
                406
            );
        }

        // Try to get data and check if authorization required and failed
        $data = $this->getModel()->getData(
            $this->getStateObject(), $routeConfig
        );

        // Retrieve data from model so that VIEW and MODEL are informed (Observer and this here is the Subject)
        $this->setData(
            $data
        );
    }

    /**
     * Checks if all required fields where passed with request.
     *
     * @param array                   $argumentsRequired The arguments required
     * @param Doozr_Request_Arguments $argumentsSent     The arguments send with request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if all required fields where sent, otherwise FALSE
     */
    protected function validateInputArguments(array $argumentsRequired, Doozr_Request_Arguments $argumentsSent)
    {
        $valid = true;
        $requestBody = $this->getStateObject()->getRequestBody();

        // ... and iterate them to find missing elements
        foreach ($argumentsRequired as $requiredArgument => $requiredValue) {
            // Can the required value be retrieved from GET, POST, ...
            if (!isset($argumentsSent->{$requiredArgument}) && (!isset($requestBody->{$requiredArgument}))) {
                $valid = false;
            }
        }

        return $valid;
    }

    /**
     * Returns the response object for sending header(s).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Response_Cli|Doozr_Response_Httpd|Doozr_Response_Web
     */
    protected function getResponse()
    {
        // get registry
        $registry = Doozr_Registry::getInstance();

        // get response
        /* @var $response Doozr_Response_Web */
        return $registry->front->getResponse();
    }
}
