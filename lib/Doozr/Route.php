<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Route
 *
 * Route.php - This class is responsible for matching routes (collected from
 * both -> .routes configuration in Doozr's main configuration as well as the
 * applications configuration and annotations from applications presenters)
 * against the current request-URI.
 *
 * PHP versions 5.4
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
 * @category   Doozr
 * @package    Doozr_Kernel
 * @subpackage Doozr_Kernel_Route
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Http.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/State/Container.php';

use Doctrine\Common\Annotations\AnnotationReader;
use Rhumsaa\Uuid\Uuid;
use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;

/**
 * Doozr - Route
 *
 * This class is responsible for matching routes (collected from
 * both -> .routes configuration in Doozr's main configuration as well as the
 * applications configuration and annotations from applications presenters)
 * against the current request-URI.
 *
 * @category   Doozr
 * @package    Doozr_Kernel
 * @subpackage Doozr_Kernel_Route
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 * @final
 */
final class Doozr_Route extends Doozr_Base_State_Container
{
    /**
     * The UUID of the active route (request url)
     *
     * @var string
     * @access protected
     */
    protected $uuid;

    /**
     * Status of cache enable (TRUE|FALSE)
     *
     * @var bool
     * @access protected
     */
    protected $cacheEnabled;

    /**
     * An annotation reader instance.
     *
     * @var AnnotationReader
     * @access protected
     * @static
     */
    protected static $annotationReader;

    /**
     * The object our truth is stored in
     *
     * @var Doozr_Request_State
     * @access protected
     */
    protected $requestState;

    /**
     * The routes read from different sources and ready
     * to dispatch.
     *
     * @var array
     * @access protected
     */
    protected $routes;

    /**
     * The current active route prefilled with defaults.
     * The minimum length of a route is 2 nodes which
     * represents an object and its action. But through
     * .config you are free to define a custom route pattern
     * and so up to n-nodes-routes (e.g. /foo/bar/foo/bar/...)
     *
     * @var array
     * @access protected
     */
    protected $activeRoute = array(
        self::DEFAULT_PRESENTER,
        self::DEFAULT_ACTION
    );

    /**
     * The namespace used to separate routing data.
     *
     * @var string
     * @access protected
     */
    protected $namespace;

    /**
     * The default object.
     *
     * @var string
     * @access const
     */
    const DEFAULT_PRESENTER = 'index';

    /**
     * The default action.
     *
     * @var string
     * @access const
     */
    const DEFAULT_ACTION = 'index';

    /**
     * Namespace used for caching routes and routing metadata.
     *
     * @var string
     * @access public
     * @const
     */
    const NAMESPACE_CACHE = 'cache.routes';


    /**
     * Constructor.
     *
     * @param Doozr_Registry $registry The registry of Doozr
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     */
    public function __construct(Doozr_Registry $registry)
    {
        $this
            ->registry($registry)
            ->requestState($registry->getRequest())
            ->cacheEnabled($registry->getConfiguration()->kernel->cache->enabled)
            ->namespace_(DOOZR_NAMESPACE_FLAT . '.' . self::NAMESPACE_CACHE)
            ->run();
    }

    /*------------------------------------------------------------------------------------------------------------------
    | BEGIN PUBLIC METHODS
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method runs or executes the preset configuration. This runner was separated from init() to enable the
     * user/application to manually or automatically intercept the execution between the route detection of Doozr
     * and the execution of it.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function run()
    {
        // With caching we need unique identifier
        $this->setUuid($this->calculateUuid($this->getRequestState()->getUrl()));

        // Check for cache enabled
        if (true === $this->getCacheEnabled()) {
            // Try to fetch routes from cache
            try {
                $this->setRoutes(
                    $this->getRegistry()->getCache()->read(
                        $this->getUuid(),
                        $this->getNamespace()
                    )
                );
            } catch (Doozr_Cache_Service_Exception $e) {
                // Intentionally left blank
            }
        }

        // If cache disabled or routes could not be fetched -> prepare them ...
        if (null === $this->getRoutes()) {
            // If we reach here we must parse the routes first ...
            $routesFromPresenter = $this->getRoutesFromPresenters();
            $routesFromConfig    = $this->getRegistry()->getConfiguration()->kernel->transmission->routing->routes;
            $routes              = array();

            // Convert routes to same context as from presenter
            foreach ($routesFromConfig as $route => $config) {

                // Check for method and set DEFAULT to GET if not set
                if (isset($config->methods) && $config->methods !== null) {
                    $methods = explode(',', $config->methods);
                } else {
                    $methods = array(Doozr_Http::REQUEST_METHOD_GET);
                }

                foreach ($methods as $method) {
                    if (isset($routes[$method]) === false) {
                        $routes[$method] = array();
                    }

                    // Manipulate & extend source config object ...
                    $config->route = $route;

                    // Store it ...
                    $routes[$method][$route] = $config;
                }
            }

            $this->setRoutes(
                array_merge_recursive($routes, $routesFromPresenter)
            );

            // Cache if enabled ...
            if (true === $this->getCacheEnabled()) {
                // Store result after reading from presenter(s) and config and so on ...
                $this->getRegistry()->getCache()->create(
                    $this->getUuid(),
                    $this->getRoutes(),
                    null,
                    $this->getNamespace()
                );
            }
        }

        // Feed dispatcher with routes
        $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
            $routeCollection = $this->getRoutes();
            foreach ($routeCollection as $verb => $routes) {
                foreach ($routes as $route => $config) {
                    $r->addRoute($verb, $route, array($config->presenter, $config->action));
                }
            }
        });

        $error = null;

        // Dispatch route ...
        $routeInfo = $dispatcher->dispatch($this->getRequestState()->getMethod(), $this->getRequestState()->getUrl());
        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                $error = array(
                    'number'  => 404,
                    'message' => 'Route %s not found',
                    'context' => $this->getRequestState()->getUrl(),
                );
                break;

            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $error = array(
                    'number'  => 405,
                    'message' => 'Method no allowed. Allowed method(s): %s',
                    'context' => $routeInfo[1],
                );
                break;

            case FastRoute\Dispatcher::FOUND:
                // Store result of dispatch process - we wil use this later as identifier for status response dispatch
                $this->getRequestState()->setActiveRoute($routeInfo[1], $routeInfo[2]);
                break;
        }

        // Dispatch the request to back controller
        $this->getRegistry()->getBack()->run(
            $this->getRequestState(),
            $error
        );
    }

    /*------------------------------------------------------------------------------------------------------------------
    | BEGIN PRIVATE/PROTECTED METHODS
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for routes.
     *
     * @param string $routes The routes
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setRoutes($routes)
    {
        $this->routes = $routes;
    }

    /**
     * Fluent setter for routes.
     *
     * @param string $routes The routes
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function routes($routes)
    {
        $this->setRoutes($routes);
        return $this;
    }

    /**
     * Getter for routes.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string
     * @access protected
     */
    protected function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Setter for uuid.
     *
     * @param string $uuid The uuid
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Fluent setter for uuid.
     *
     * @param string $uuid The uuid
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function uuid($uuid)
    {
        $this->setUuid($uuid);
        return $this;
    }

    /**
     * Getter for uuid.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string
     * @access protected
     */
    protected function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Setter for requestState.
     *
     * @param \Doozr_Request_State $requestState
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setRequestState(Doozr_Request_State $requestState)
    {
        $this->requestState = $requestState;
    }

    /**
     * Fluent setter for requestState.
     *
     * @param \Doozr_Request_State $requestState
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
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
     * @return \Doozr_Request_State
     * @access protected
     */
    protected function getRequestState()
    {
        return $this->requestState;
    }

    /**
     * Setter for cacheEnabled.
     *
     * @param bool $cacheEnabled TRUE|FALSE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setCacheEnabled($cacheEnabled)
    {
        $this->cacheEnabled = $cacheEnabled;
    }

    /**
     * Fluent setter for cacheEnabled.
     *
     * @param bool $cacheEnabled TRUE|FALSE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function cacheEnabled($cacheEnabled)
    {
        $this->setCacheEnabled($cacheEnabled);
        return $this;
    }

    /**
     * Getter for cacheEnabled.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool $cacheEnabled TRUE|FALSE
     * @access protected
     */
    protected function getCacheEnabled()
    {
        return $this->cacheEnabled;
    }

    /**
     * Setter for namespace.
     *
     * @param string $namespace The namespace
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Setter for namespace.
     *
     * @param string $namespace The namespace
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function namespace_($namespace)
    {
        $this->setNamespace($namespace);
        return $this;
    }

    /**
     * Getter for namespace.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The namspace if set, otherwise NULL
     * @access protected
     */
    protected function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Calculates a UUID for a passed string.
     *
     * @param string $input The input to calculate the UUID for.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The UUID
     * @access protected
     */
    protected function calculateUuid($input)
    {
        try {
            // Generate a version 5 (name-based and hashed with SHA1) UUID object
            $uuid5 = Uuid::uuid5(Uuid::NAMESPACE_DNS, $input);
            $uuid = $uuid5->toString();

        } catch (UnsatisfiedDependencyException $e) {
            $uuid = sha1($input);
        }

        return $uuid;
    }

    /**
     * Converts an object instance to any other instance (stdClass = default).
     *
     * @param object $instance  An instance to convert
     * @param string $classname A classname for the new instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object The new instance
     * @access protected
     */
    protected function objectToObject($instance, $classname = 'stdClass') {
        return unserialize(sprintf(
            'O:%d:"%s"%s',
            strlen($classname),
            $classname,
            strstr(strstr(serialize($instance), '"'), ':')
        ));
    }

    /**
     * Parses the routes from filename.
     *
     * @param string $filename The filename to parse routes from.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Routes from file ordered and ready for further use
     * @access protected
     */
    protected function parseRoutesFromFile($filename)
    {
        // Assume empty result
        $routes = array();

        // Check if file exists ...
        if (true === $this->getRegistry()->getFilesystem()->is_readable($filename)) {
            include $filename;

            $content = file_get_contents($filename);
            $matches = preg_match('/class\s([A-Za-z\_]+)/', $content, $classes);

            if ($matches > 0) {
                // Use our selection from result
                $classname = $classes[1];
                $reflection = new ReflectionClass($classname);

                // Extract the methods (potentially an ...Action())
                $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

                foreach ($methods as $key => $reflection) {
                    if (preg_match('/(Action)$/ui', $reflection->name)) {
                        if (isset($actionsWithRoutes[$filename]) === false) {
                            $actionsWithRoutes[$filename] = array();
                        }

                        $actionsWithRoutes[$filename][$reflection->name] =
                            $this->getAnnotationReader()->getMethodAnnotations($reflection);
                    }
                }
            }

            $routes = $this->sortRoutes($actionsWithRoutes);
        }

        return $routes;
    }

    /**
     * Sort the routes by method and route (remove duplicates).
     *
     * @param array $routesByFile An array containing the routes of a class' actions indexed by filename.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Ordered routes
     * @access protected
     */
    protected function sortRoutes(array $routesByFile)
    {
        $sortedRoutes = array();

        // Now we need to organize this stuff a bit
        if (count($routesByFile) > 0) {
            foreach ($routesByFile as $file => $routesByAction) {
                foreach ($routesByAction as $action => $routes) {
                    /* @var \Doozr\Route\Annotation\Route $route */
                    foreach ($routes as $route) {
                        $methods = $route->getMethods();

                        foreach ($methods as $method) {
                            if (isset($sortedRoutes[$method]) === false) {
                                $sortedRoutes[$method] = array();
                            }

                            // Inject detected presenter if not already redirected to another one by annotation
                            if ($route->getPresenter() === null) {
                                $route->setPresenter(
                                    $this->getPresenterByClassname($this->getPresenterClassByFilename($file))
                                );
                            }

                            // Inject detected action if not already redirected to another one by annotation
                            if ($route->getAction() === null) {
                                $route->setAction(str_replace('Action', '', $action));
                            }

                            $sortedRoutes[$method][$route->getRoute()] = $route;
                        }
                    }
                }
            }
        }

        // Return ordered routes
        return $sortedRoutes;
    }

    /**
     * Getter for AnnotationReader with lazy instantiate.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return AnnotationReader Instance of annotation reader
     * @access protected
     */
    protected function getAnnotationReader()
    {
        if (self::$annotationReader === null) {
            self::$annotationReader = new AnnotationReader();
        }

        return self::$annotationReader;
    }

    /**
     * Returns the classname of a presenter by its filename.
     *
     * @param string $filename The filename to return classname from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The classname
     * @access protected
     */
    protected function getPresenterClassByFilename($filename)
    {
        return 'Presenter_' . str_replace('.php', '', basename($filename));
    }

    /**
     * Returns the name of a a presenter by its classname.
     *
     * @example
     * So if you would pass "Presenter_Index" to it, it would return "Index".
     *
     * @param string $classname The classname to return presenter from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The name of the presenter
     * @access protected
     */
    protected function getPresenterByClassname($classname)
    {
        return strtolower(explode('_', $classname)[1]);
    }

    /**
     * Parses all routes from all presenters (annotations).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Routes parsed from presenters.
     * @access protected
     */
    protected function getRoutesFromPresenters()
    {
        // Assume no routes
        $routes = array();

        $directoryIterator = new RecursiveDirectoryIterator(DOOZR_APP_ROOT . 'Presenter');
        $iteratorIterator  = new RecursiveIteratorIterator($directoryIterator);
        $regexIterator     = new RegexIterator($iteratorIterator, '/.*\.php/i', RecursiveRegexIterator::GET_MATCH);

        // Iterate over result
        foreach ($regexIterator as $file) {
            // Found a ".php" file in presenter folder ... try to extract routes
            $routesByMethod = self::parseRoutesFromFile($file[0]);

            foreach ($routesByMethod as $method => $routesForMethod) {
                if (isset($routes[$method]) === false) {
                    $routes[$method] = array();
                }

                foreach ($routesForMethod as $route => $object) {
                    $routesForMethod[$route] = $this->objectToObject($object);
                }

                $routes[$method] = array_merge($routes[$method], $routesForMethod);
            }
        }

        // Return routes found ...
        return $routes;
    }

    /**
     * Takes two optional arguments and build a basic routing profile.
     * If null is passed the default value is used.
     *
     * @param string $node1 The first node part of the route
     * @param string $node2 The second node part of the route
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The resulting array containing two nodes "object" and "action" of target route
     * @access protected
     */
    protected function buildRoutingProfile($node1 = null, $node2 = null)
    {
        return array(
            ($node1 !== null) ? $node1 : self::DEFAULT_PRESENTER,
            ($node2 !== null) ? $node2 : self::DEFAULT_ACTION
        );
    }
}
