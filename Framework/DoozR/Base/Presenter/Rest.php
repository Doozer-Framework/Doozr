<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Base - Presenter - Rest
 *
 * Rest.php - Base class for presenter-layers from MV(C|P) with REST support
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
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Presenter
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Presenter.php';

/**
 * DoozR - Base Presenter
 *
 * Base Presenter of the DoozR Framework.
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Presenter
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Base_Presenter_Rest extends DoozR_Base_Presenter
{
    /**
     * The rest service
     *
     * @var DoozR_Rest_Service
     * @access protected
     */
    protected $rest;

    /**
     * The API request object
     * containing ALL relevant information about the REST call
     * (Arguments for POST,PUT,... must still be taken from their representing classes).
     *
     * @var DoozR_Request_Api
     * @access protected
     */
    protected $requestObject;

    /**
     * Root node of API (default = /api/)
     *
     * @var string
     * @access protected
     */
    protected $rootNode = '/api/';

    /**
     * The current route setup as tree representation
     *
     * @var array
     * @access protected
     */
    protected $routeTree;

    /**
     * Routes collection of REST API
     *
     * @var array
     * @access protected
     */
    protected $routes = array();


    /**
     * Constructor.
     *
     * @param array                  $request         The whole request as processed by "Route"
     * @param array                  $translation     The translation required to read the request
     * @param array                  $originalRequest The original untouched request
     * @param DoozR_Config_Interface $config          The DoozR main config instance
     * @param DoozR_Base_Model       $model           The model to communicate with backend (db)
     * @param DoozR_Base_View        $view            The view to display results
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Base_Presenter_Rest
     * @access public
     */
    public function __construct(
        array                  $request,
        array                  $translation,
        array                  $originalRequest,
        DoozR_Config_Interface $config          = null,
        DoozR_Base_Model       $model           = null,
        DoozR_Base_View        $view            = null
    ) {
        // Init REST layer/service => only difference to DoozR_Base_Presenter
        $this->rest = DoozR_Loader_Serviceloader::load('rest', $originalRequest, count($request));

        // get request object (standard notation), object + method
        $this->setRequestObject($this->rest->getRequest());

        // forward call
        parent::__construct($request, $translation, $originalRequest, $config, $model, $view);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to demonstrate how data could be automatic be displayed.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean True if successful, otherwise false
     * @access public
     * @throws DoozR_Base_Presenter_Rest_Exception
     */
    public function Main()
    {
        // Get REAL action (hey dude u know this is the Main() API entry like the main.cpp ;)
        $resource = $this->requestObject->get($this->rootNode . '{{resource}}', function ($resource) {
                return $resource;
            }
        );

        // Try to dispatch to action or fail with exception if action does not exist
        if (is_callable(array($this, $resource))) {

            // Setup the routes (subrouting)
            $this->{$resource}();
            return $this->run();

        } else {
            throw new DoozR_Base_Presenter_Rest_Exception(
                'The resource "' . $resource . '" seems unknown to me. I never heard about it before :('
            );
        }
    }

    /**
     * Setter for rest
     *
     * @param DoozR_Rest_Service $rest A rest service instance to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Rest_Service The rest service instance
     * @access public
     */
    public function setRest(DoozR_Rest_Service $rest)
    {
        return $this->rest = $rest;
    }

    /**
     * Getter for rest
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Rest_Service The rest service instance
     * @access public
     */
    public function getRest()
    {
        return $this->rest;
    }

    /**
     * Setter for request object
     *
     * @param DoozR_Request_Api $requestObject The request object to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setRequestObject($requestObject)
    {
        $this->requestObject = $requestObject;
    }

    /**
     * Getter for request object
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Request_Api The request object
     * @access protected
     */
    protected function getRequestObject()
    {
        return $this->requestObject;
    }

    /**
     * Registers a new route
     *
     * @param string                           $route  The route
     * @param DoozR_Base_Presenter_Rest_Config $config The config
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function registerRoute($route, DoozR_Base_Presenter_Rest_Config $config)
    {
        $this->routes[$route] = $config;
    }

    /**
     * Registers a new route
     *
     * @param array $routes A collection of routes to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function registerRoutes(array $routes)
    {
        foreach ($routes as $route => $config) {
            $this->registerRoute($route, $config);
        }
    }

    /**
     * Setter for route tree representation
     *
     * @param array $routeTree The route tree
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setRouteTree(array $routeTree)
    {
        $this->routeTree = $routeTree;
    }

    /**
     * Getter for route tree
     *
     * @return array The route tree
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function getRouteTree()
    {
        return $this->routeTree;
    }

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
     * @return DoozR_Base_Presenter_Rest_Config|false The config if route could be revsolved,
     *                                                false if route could not be resolved
     * @access protected
     * @throws DoozR_Base_Presenter_Rest_Exception
     */
    protected function getRouteByUrl($url)
    {
        // prepare URL for lookup
        $url = str_replace($this->rootNode, '', $url);

        // extract nodes from clean URL
        $nodes = explode('/', $url);

        // Local copy for diving into ...
        $routeTree = $this->routeTree;

        // The ids extracted from passed URL
        $ids = array();

        // The route reverse created
        $route = array();

        $countNodes = count($nodes);

        // For automagically extracting {{id}} to id (uid of a recordset/dataset)
        $uid = null;

        // Lookup route
        for ($i = 0; $i < $countNodes; ++$i) {

            // Is regular route way/node ?
            if (is_array($routeTree) && isset($routeTree[$nodes[$i]])) {
                $routeTree = $routeTree[$nodes[$i]];

            } elseif (preg_match('/{{(.*)}}/i', key($routeTree), $variable) > 0) {
                // maybe its a variable node value
                $nodes[$i] = '{{' . $variable[1] . '}}';

                $magicUid = strtolower($variable[1]);
                if ($magicUid === 'id' || $magicUid === 'uid') {
                    $uid = $variable[1];
                }

                $ids[]     = $nodes[$i];
                $routeTree = $routeTree[$nodes[$i]];

            } else {
                throw new DoozR_Base_Presenter_Rest_Exception(
                    'Route for URL "' . $url . '" seems wrong. It could not be resolved.'
                );
            }

            $route[] = $nodes[$i];

            if ($i === ($countNodes - 1)) {
                if (is_object($routeTree) === true) {
                    // Inject Ids for reverse lookup
                    /* @var $routeTree DoozR_Base_Presenter_Rest_Config */
                    $routeTree
                        ->id($uid)
                        ->ids($ids)
                        ->url($url)
                        ->route($route)
                        ->rootNode($this->rootNode);

                } else {
                    // In this case we ended up before we got config!
                    throw new DoozR_Base_Presenter_Rest_Exception(
                        'Route for URL "' . $url . '" seems incomplete.'
                    );
                }
            }
        }

        return $routeTree;
    }



    protected function run()
    {
        // COULD START HERE....
        // PUT THIS CODE INTO BASE CLASS AS RUN() !!!!!
        $this->setRouteTree(
            explodeTree($this->routes, '/')
        );

        $routeConfig = $this->getRouteByUrl($this->requestObject->getUrl());

        // check if verb is allowed
        if ($routeConfig->isAllowed($this->requestObject->getMethod()) === false) {
            $this->getResponse()->sendHttpStatus(
                405,
                null,
                true,
                $this->getRequestObject()->getMethod()
            );
            exit;
        }

        # VALIDATE INPUT ARGUMENTS
        $message = 'Missing required argument(s): ';
        $valid = true;

        // ... and iterate them to find missing elements
        foreach ($routeConfig->getRequired() as $requiredArgument => $requiredValue) {

            // Can the required value be retrieved from GET, POST, ...
            if (!isset($this->getRequestObject()->getArguments()->{$requiredArgument})) {
                $valid = false;
                $message .= $requiredArgument;
                if ($requiredValue !== null) {
                    $message .= '(required Value: "' . var_export($requiredValue, true) . '")';
                }
                $message .= ', ';
            }
        }

        // If not valid (argument missing => tell it the user!
        if ($valid === false) {
            // send HTTP-Header "Not-Acceptable" for missing argument + message
            $this->getResponse()->sendHttpStatus(
                406,
                null,
                true,
                substr($message, 0, strlen($message) - 2)
            );
            exit;
        }

        # DATEN READY MACHEN!
        // Retrieve data for context Screen from Model by defined default interface "getData()"
        $data = $this->model->getData(
            $this->getRequestObject(),
            $routeConfig
        );

        // set data here within this instance cause VIEW and MODEL are attached as Observer to this Subject.
        $this->setData($data);

        // Chaining required?
        return $this;
    }

    /**
     * Returns the response object for sending header(s).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Response_Cli|DoozR_Response_Httpd|DoozR_Response_Web
     * @access protected
     * @deprecated
     */
    protected function getResponse()
    {
        // get registry
        $registry = DoozR_Registry::getInstance();

        // get response
        /* @var $response DoozR_Response_Web */
        return $registry->front->getResponse();
    }
}
