<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Base - Presenter.
 *
 * Presenter.php - Base class for presenters
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
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Presenter/Subject.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Presenter/Interface.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Http.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Route/Annotation/Route.php';

use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Doozr - Base - Presenter.
 *
 * Base class for presenters
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
class Doozr_Base_Presenter extends Doozr_Base_Presenter_Subject implements
    Doozr_Base_Presenter_Interface
{
    /**
     * Data for CRUD operation(s).
     *
     * @var mixed
     */
    protected $data;

    /**
     * Instance of model for communication.
     *
     * @var Doozr_Base_Model
     */
    protected $model;

    /**
     * The main configuration.
     *
     * @var Doozr_Configuration
     */
    protected $configuration;

    /**
     * Type of presenter.
     *
     * @var string
     */
    protected $type = 'Presenter';

    /**
     * Complete route.
     *
     * @var Doozr_Request_Route_State
     */
    protected $route;

    /**
     * The request.
     *
     * @var Request
     */
    protected $request;

    /**
     * This array contains the required arguments
     * to run a specific action in a specific context.
     *
     * @var array
     */
    protected $required = [];

    /**
     * Allowed request types to execute against this
     * presenter.
     *
     * @var array
     */
    protected $allowed = [];

    /**
     * The count of root nodes.
     *
     * @var int
     */
    protected $nodes;

    /**
     * The ids of the route.
     *
     * @var array
     */
    protected $ids;

    /**
     * The URL of the route.
     *
     * @var string
     */
    protected $url;

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param Doozr_Registry   $registry Instance of Doozr_Registry containing all core components
     * @param Request          $request  Whole request as state
     * @param Doozr_Base_Model $model    Model to communicate with backend (db)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @throws Doozr_Base_Presenter_Exception
     */
    public function __construct(
        Doozr_Registry   $registry,
        Request          $request,
        Doozr_Base_Model $model = null
    ) {
        // Store instances for further use ...
        $this
            ->registry($registry)
            ->request($request)
            ->route($request->getAttribute('route'))
            ->model($model)
            ->configuration($registry->getConfiguration());


        // Check if an app is configured -> enable autoloading for it automagically
        if (false !== isset($this->getConfiguration()->app)) {
            $this->registerAutoloader(
                $this->getConfiguration()->get('app')
            );
        }

        // Important! => call parents constructor so SplObjectStorage is created!
        parent::__construct(
            new Doozr_Request_State()
        );

        // Check for __tearup - Method (it's Doozr's __construct-like magic-method)
        /*
        if ($this->hasMethod('__tearup') && is_callable([$this, '__tearup'])) {
            $result = $this->__tearup($this->getRoute());

            if ($result !== true) {
                throw new Doozr_Base_Presenter_Exception(
                    '__tearup() must (if set) return TRUE. __tearup() executed and it returned: '.
                    var_export($result, true)
                );
            }
        }*/
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method (container) is intend to set the data for a requested mode.
     *
     * @param mixed $data The data (array preferred) to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed The data from processing
     */
    public function setData($data)
    {
        $this->data = $data;

        // Notify observers about new data
        return $this->notify();
    }

    /**
     * Setter for data.
     *
     * @param mixed $data The data to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function data($data)
    {
        $this->setData($data);

        return $this;
    }

    /**
     * This method (container) is intend to return the data for a requested mode.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed The data for the mode requested
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * This method is intend to call the teardown method of a model if exist.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed The result of magic destruct replacement
     */
    public function __destruct()
    {
        return $this->__teardown();
    }

    /*------------------------------------------------------------------------------------------------------------------
    | MAGIC METHODS
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * __construct replacement for Presenter.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool Must return TRUE in case of success, otherwise FALSE
     */
    protected function __tearup()
    {
        return true;
    }

    /**
     * __destruct replacement for Presenter.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool Must return TRUE in case of success, otherwise FALSE
     */
    protected function __teardown()
    {
        return true;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/


    public function setType($type)
    {
        $this->type = $type;
    }

    public function type($type)
    {
        $this->setType($type);

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * Setter for request.
     *
     * @param Request $request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Fluent: Setter for request.
     *
     * @param Request $request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function request($request)
    {
        $this->setRequest($request);

        return $this;
    }

    /**
     * Getter for request.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Request Request
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * Setter for route.
     *
     * @param Doozr_Request_Route_State $route The route
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
     * @param Doozr_Request_Route_State $route The route
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
     * @return Doozr_Request_Route_State The route
     */
    protected function getRoute()
    {
        return $this->route;
    }

    /**
     * Setter for model.
     *
     * @param Doozr_Base_Model $model The model to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setModel(Doozr_Base_Model $model = null)
    {
        $this->model = $model;
    }

    /**
     * @param Doozr_Base_Model $model
     *
     * @return $this
     */
    protected function model(Doozr_Base_Model $model = null)
    {
        $this->setModel($model);

        return $this;
    }

    /**
     * Getter for model.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Base_Model The model if set, otherwise NULL
     */
    protected function getModel()
    {
        return $this->model;
    }

    /**
     * Sets the ids of the route.
     *
     * @param array $ids The ids of the route
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Base_Presenter
     */
    protected function setIds(array $ids)
    {
        $this->ids = $ids;
    }

    /**
     * Sets the ids of the route.
     *
     * @param array $ids The ids of the route
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Base_Presenter
     */
    protected function ids(array $ids)
    {
        $this->setIds($ids);

        return $this;
    }

    /**
     * Returns the ids of the route.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The ids of the route
     */
    protected function getIds()
    {
        return $this->ids;
    }

    /**
     * Setter for configuration.
     *
     * @param Doozr_Configuration_Interface $configuration The configuation object
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setConfiguration(Doozr_Configuration_Interface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Setter for configuration.
     *
     * @param Doozr_Configuration_Interface $configuration The configuation object
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function configuration(Doozr_Configuration_Interface $configuration)
    {
        $this->setConfiguration($configuration);

        return $this;
    }

    /**
     * Getter for configuration.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Configuration The configuration stored
     */
    protected function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Sets the count of root nodes for request.
     *
     * @param int $countOfRootNodes The count of root nodes
     *
     * @example if request is /foo/bar/1234 and the root node count
     *          is 2 then all operations will use /foo and /bar as
     *          root.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setNodes($countOfRootNodes)
    {
        $this->nodes = $countOfRootNodes;
    }

    /**
     * Sets the count of root nodes for request.
     *
     * @param int $countOfRootNodes The count of root nodes
     *
     * @example if request is /foo/bar/1234 and the root node count
     *          is 2 then all operations will use /foo and /bar as
     *          root.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Base_Presenter
     */
    protected function nodes($countOfRootNodes)
    {
        $this->setNodes($countOfRootNodes);

        return $this;
    }

    /**
     * Returns the count of root nodes.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int The count of root nodes
     */
    protected function getNodes()
    {
        return $this->nodes;
    }

    /**
     * Registers an autoloader instance SPL with highest priority for loading classes of the app.
     *
     * @param object $app The app configuration object
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function registerAutoloader($app)
    {
        // now configure a new autoloader spl config
        $autoloaderApp = new Doozr_Loader_Autoloader_Spl_Config();
        $autoloaderApp
            ->_namespace($app->namespace)
            ->namespaceSeparator('_')
            ->addExtension('php')
            ->path(substr($app->path, 0, -1))
            ->description('Autoloader for App classes with namespace: "'.$app->namespace.'"')
            ->priority(0);

        Doozr_Loader_Autoloader_Spl_Facade::attach([$autoloaderApp]);
    }

    /**
     * Create of Crud.
     *
     * @param mixed $data The data for create
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     */
    protected function create($data = null)
    {
        if ($this->hasMethod('__create') && is_callable([$this, '__create'])) {
            return $this->__create($data);
        }

        // notify observers about new data
        $this->notify();
    }

    /**
     * Read of cRud.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed Data on success, otherwise null
     */
    protected function read()
    {
        if ($this->hasMethod('__read') && is_callable([$this, '__read'])) {
            return $this->__read();
        }

        // notify observers about new data
        $this->notify();
    }

    /**
     * Update of crUd.
     *
     * @param mixed $data The data for update
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed Data on success, otherwise null
     */
    public function update($data = null)
    {
        if ($this->hasMethod('__update') && is_callable([$this, '__update'])) {
            return $this->__update($data);
        }

        // notify observers about new data
        $this->notify();
    }

    /**
     * Delete of cruD.
     *
     * @param mixed $data The data for delete
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     */
    protected function delete($data = null)
    {
        if ($this->hasMethod('__delete') && is_callable([$this, '__delete'])) {
            return $this->__delete($data);
        }

        // notify observers about new data
        $this->notify();
    }

    /**
     * Adds a HTTP-method (verb like GET, HEAD, PUT, POST ...) to the list
     * of allowed methods for this presenter.
     *
     * @param string|array $methods The HTTP Method which is allowed as string or multiple methods via array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @link   http://tools.ietf.org/html/rfc1945#page-30
     *
     * @return $this Instance for chaining
     */
    protected function allow($methods)
    {
        if (is_array($methods) === false) {
            $methods = [$methods];
        }

        foreach ($methods as $method) {
            if (!in_array($method, $this->allowed)) {
                $this->allowed[] = strtoupper($method);
            }
        }

        // chaining
        return $this;
    }

    /**
     * Checks if passed method (HTTP verb) is allowed.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @param string $method The HTTP Method which should be checked
     *
     * @return bool TRUE if passed method is allowed, otherwise FALSE
     */
    protected function allowed($method)
    {
        return in_array($method, $this->allowed);
    }

    /**
     * Checks if passed method (HTTP verb) is allowed.
     *
     * @param string $method The HTTP Method which should be checked
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if passed method is allowed, otherwise FALSE
     */
    protected function isAllowed($method)
    {
        return $this->allowed($method);
    }

    /**
     * This method is intend to store a single item (argument as string)
     * or a list of items (array with arguments as string) required to
     * run the presenter (or parts of model/view).
     *
     * @param        $argument
     * @param string $scope    Scope (Action) for which the argument is required (* = wildcard = all)
     * @param string $method   Method (HTTP verb) to bind the requirement to
     *
     * @internal param mixed $variable A single argument required to execute the presenter or an array of arguments
     *
     * @author   Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Base_Presenter True if everything wents fine, otherwise false
     */
    protected function required($argument, $scope = 'Index', $method = Doozr_Http::REQUEST_METHOD_GET)
    {
        // prepare storage on method/verb level
        if (false === isset($this->required[$method])) {
            $this->required[$method] = [];
        }

        // prepare storage on scope level
        if (false === isset($this->required[$method][$scope])) {
            $this->required[$method][$scope] = [];
        }

        // convert input to array if not an array
        if (false === is_array($argument)) {
            $argument = [$argument => null];
        }

        // store the combined values for automatic requirement management
        $this->required[$method][$scope][] = $argument;

        // success
        return $this;
    }

    /**
     * Returns TRUE if a passed arguments is required by presenter, FALSE if not.
     *
     * @param string $argument The argument to check
     * @param string $scope    The scope used for lookup
     * @param string $method   The method (HTTP verb) to use for lookup
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if required, otherwise FALSE
     */
    protected function isRequired($argument, $scope = 'Index', $method = Doozr_Http::REQUEST_METHOD_GET)
    {
        // Prepare storage on method/verb level
        if (false === isset($this->required[$method])) {
            return false;
        }

        // Prepare storage on scope level
        if (false === isset($this->required[$method][$scope])) {
            return false;
        }

        // Convert input to array if not an array
        if (false === is_array($argument)) {
            $argument = [$argument];
        }

        // Iterate the passed input to build ordered (scope) rules
        foreach ($argument as $requiredVariable) {
            pre($requiredVariable);
        }

        // Success
        return true;
    }

    /**
     * Returns all required fields of presenter.
     *
     * @param string $scope  The scope used for lookup
     * @param string $method The method (HTTP verb) to use for lookup
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array List of required fields
     */
    protected function getRequired($scope = 'Index', $method = Doozr_Http::REQUEST_METHOD_GET)
    {
        // prepare storage on method/verb level
        if (!isset($this->required[$method])) {
            return [];
        }

        // prepare storage on scope level
        if (!isset($this->required[$method][$scope])) {
            return [];
        }

        return $this->required[$method][$scope];
    }

    /**
     * Sets the URL of the route.
     *
     * @param string $url The URL to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Base_Presenter
     */
    protected function url($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Runs/executes all operations. Should be overwritten by
     * child on demand.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Base_Presenter
     */
    protected function run()
    {
        // runs all the stuff required to setup the API service
        return $this;
    }
}
