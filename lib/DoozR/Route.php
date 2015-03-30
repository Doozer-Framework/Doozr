<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Route
 *
 * Route.php - This class is responsible for matching routes (collected from
 * both -> .routes configuration in DoozR's main configuration as well as the
 * applications configuration and annotations from applications presenters)
 * against the current request-URI.
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
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Route
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/State/Container.php';

use Doctrine\Common\Annotations\AnnotationReader;

/**
 * DoozR - Route
 *
 * This class is responsible for matching routes (collected from
 * both -> .routes configuration in DoozR's main configuration as well as the
 * applications configuration and annotations from applications presenters)
 * against the current request-URI.
 *
 * @category   DoozR
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Route
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @final
 */
final class DoozR_Route extends DoozR_Base_State_Container
{
    /**
     * The UUID of the active route (request url)
     *
     * @var string
     * @access protected
     * @static
     */
    protected static $uuid;

    /**
     * Instance of cache service
     *
     * @var DoozR_Cache_Service
     * @access protected
     * @static
     */
    protected static $cacheService;

    /**
     * Status of cache enable (TRUE|FALSE)
     *
     * @var bool
     * @access protected
     * @static
     */
    protected static $cache;

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
     * @var DoozR_Request_State
     * @access protected
     */
    protected static $requestState;

    /**
     * The routes read from different sources and ready
     * to dispatch.
     *
     * @var array
     * @access protected
     * @static
     */
    protected static $routes;

    /**
     * The default routes to fill up missing parts passed to redirect
     * mechanism. The user does not need to know the default parts cause
     * they will be filled automagically so he only needs to put in the
     * required source and target minimum.
     *
     * @var array
     * @access protected
     * @static
     */
    /*
    protected static $defaults = array(
        'model' => array(
            self::DEFAULT_OBJECT,
            self::DEFAULT_ACTION
        ),
        'view' => array(
            self::DEFAULT_OBJECT,
            self::DEFAULT_ACTION
        ),
        'presenter' => array(
            self::DEFAULT_OBJECT,
            self::DEFAULT_ACTION
        )
    );
    */

    /**
     * The current active route prefilled with defaults.
     * The minimum length of a route is 2 nodes which
     * represents an object and its action. But through
     * .config you are free to define a custom route pattern
     * and so up to n-nodes-routes (e.g. /foo/bar/foo/bar/...)
     *
     * @var array
     * @access protected
     * @static
     */
    protected static $activeRoute = array(
        self::DEFAULT_OBJECT,
        self::DEFAULT_ACTION
    );

    /**
     * The request in its plain form, stored for further
     * access.
     *
     * @var array
     * @access protected
     * @static
     */
    protected static $request;

    /**
     * The translation matrix for passed requests. The translation
     * is done so that you can handle the route in your applications
     * code by exactly those translations (e.g. /foo/bar/ will become
     * available in the application through the variables $object and
     * $action by default pattern).
     *
     * @var array
     * @access protected
     * @static
     */
    #protected static $translationMatrix;

    /**
     * The registry of the DoozR Framework for accessing
     * base objects.
     *
     * @var DoozR_Registry
     * @access protected
     * @static
     */
    protected static $registryInstance;

    /**
     * The namespace used to separate routing data.
     *
     * @var string
     * @access protected
     * @static
     */
    protected static $namespace;

    /**
     * The default object.
     *
     * @var string
     * @access const
     */
    const DEFAULT_OBJECT = 'index';

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


    /*------------------------------------------------------------------------------------------------------------------
    | BEGIN PUBLIC METHODS
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method initializes the routing process and detects the route for running this request-URI on.
     *
     * @param DoozR_Registry_Interface   $registry     The registry of DoozR.
     * @param DoozR_Base_State_Interface $requestState The request state instance for retrieving all request data
     * @param DoozR_Cache_Service        $cacheService Instance of cache service to improve performance with caching
     * @param bool                       $cache        TRUE to enable caching, FALSE to disable
     * @param bool                       $autorun      TRUE to automatic run/execute the route
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     * @static
     */
    public static function init(
        DoozR_Registry_Interface   $registry,
        DoozR_Base_State_Interface $requestState,
        DoozR_Cache_Service        $cacheService  = null,
                                   $cache         = false,
                                   $autorun       = true
    ) {
        // Assume we do not run so the result cant be true -> so its false
        $result = false;

        self::$registryInstance = $registry;
        self::$requestState     = $requestState;
        self::$cacheService     = $cacheService;
        self::$cache            = $cache;
        self::$namespace        = DOOZR_NAMESPACE_FLAT . '.' . self::NAMESPACE_CACHE;

        // Autorun (enabled in default config)
        if ($autorun !== false) {
            $result = self::run();
        }

        return $result;
    }

    /**
     * This method runs or executes the preset configuration. This runner
     * was separated from init() to enable the user/application to manually
     * or automatically intercept the execution between the route detection
     * of DoozR and the execution of it.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     * @static
     */
    public static function run()
    {
        // With caching we need unique identifier
        self::$uuid = md5(self::$requestState->getUrl());

        // Check for cache enabled
        if (self::$cache === true) {
            // Try to fetch routes from cache
            try {
                self::$routes = self::$cacheService->read(self::$uuid, self::$namespace);

            } catch (DoozR_Cache_Service_Exception $e) {
                // Intentionally left blank
            }
        }

        // If cache disabled or routes could not be fetched -> prepare them ...
        if (self::$routes === null) {
            // If we reach here we must parse the routes first ...
            $routesFromPresenter = self::getRoutesFromPresenters();
            $routesFromConfig    = self::$registryInstance->getConfig()->routes;
            $routes              = array();

            // Convert routes to same context as from presenter
            foreach ($routesFromConfig as $route => $config) {

                // Check for method and set DEFAULT to GET if not set
                if (isset($config->methods) && $config->methods !== null) {
                    $methods = explode(',', $config->methods);
                } else {
                    $methods = array('GET');
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

            self::$routes = array_merge_recursive($routes, $routesFromPresenter);

            // Cache if enabled ...
            if (self::$cache === true) {
                // Store result after reading from presenter(s) and config and so on ...
                self::$cacheService->create(self::$uuid, self::$routes, null, self::$namespace);
            }
        }


        // Feed dispatcher with routes
        $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
            foreach (self::$routes as $verb => $routes) {
                foreach ($routes as $route => $config) {
                    $r->addRoute($verb, $route, array($config->presenter, $config->action));
                }
            }
        });

        $error = null;

        // Dispatch route ...
        $routeInfo = $dispatcher->dispatch(self::$requestState->getMethod(), self::$requestState->getUrl());
        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                $error = array(
                    'number'  => 404,
                    'message' => 'Route %s not found',
                    'context' => self::$requestState->getUrl(),
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
                self::$requestState->setActiveRoute($routeInfo[1], $routeInfo[2]);
                break;
        }

        /**
         * Check for automatic call DoozR's MVP-structure
         * If automatic dispatch isn't enabled then the dev need to call run() on Back-Controller manually.
         * The validation of the request state content (405?, 404?, is part of the run() method:
         *   is part of back-controllers domain !!!
         */
        if (self::$registryInstance->getConfig()->base->pattern->enabled) {
            self::$registryInstance->getBack()->run(
                self::$requestState,
                $error
            );
        }
    }

    /*------------------------------------------------------------------------------------------------------------------
    | BEGIN PRIVATE/PROTECTED METHODS
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Parses the routes from filename.
     *
     * @param string $filename The filename to parse routes from.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Routes from file ordered and ready for further use
     * @access protected
     * @static
     */
    protected static function parseRoutesFromFile($filename)
    {
        // Assume empty result
        $routes = array();

        // Check if file exists ...
        if (true === self::$registryInstance->getFilesystem()->is_readable($filename)) {
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
                            self::getAnnotationReader()->getMethodAnnotations($reflection);
                    }
                }
            }

            $routes = self::sortRoutes($actionsWithRoutes);
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
     * @static
     */
    protected static function sortRoutes(array $routesByFile)
    {
        $sortedRoutes = array();

        // Now we need to organize this stuff a bit
        if (count($routesByFile) > 0) {
            foreach ($routesByFile as $file => $routesByAction) {
                foreach ($routesByAction as $action => $routes) {
                    /* @var \DoozR\Route\Annotation\Route $route */
                    foreach ($routes as $route) {
                        $methods = $route->getMethods();

                        foreach ($methods as $method) {
                            if (isset($sortedRoutes[$method]) === false) {
                                $sortedRoutes[$method] = array();
                            }

                            // Inject detected presenter if not already redirected to another one by annotation
                            if ($route->getPresenter() === null) {
                                $route->setPresenter(
                                    self::getPresenterByClassname(self::getPresenterClassByFilename($file))
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
     * @static
     */
    protected static function getAnnotationReader()
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
     * @static
     */
    protected static function getPresenterClassByFilename($filename)
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
     * @static
     */
    protected static function getPresenterByClassname($classname)
    {
        return strtolower(explode('_', $classname)[1]);
    }

    /**
     * Parses all routes from all presenters (annotations).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Routes parsed from presenters.
     * @access protected
     * @static
     */
    protected static function getRoutesFromPresenters()
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

                $routes[$method] = array_merge($routes[$method], $routesForMethod);
            }
        }

        // Return routes found ...
        return $routes;
    }

    /**
     * This method translates the current request to a valid route.
     *
     * @param array $request     The current request
     * @param array $translation The translation of the request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Translated request
     * @access protected
     * @static
     */
    /*
    protected static function translate(array $request, array $translation)
    {
        // the translated result
        $result = array();

        // Get count of parts used in current pattern outside the following loop -> performance
        $countParts = count($translation);

        // parse the parts
        for ($i = 0; $i < $countParts; ++$i) {
            if (isset($request[$i]) && !is_array($request[$i])) {
                $$translation[$i] = strtolower($request[$i]);
            } else {
                $$translation[$i] = self::DEFAULT_ACTION;
            }

            // a list for dispatch (name => value - pairs)
            $result[$translation[$i]] = $$translation[$i];
        }

        // return translated input
        return $result;
    }
    */

    /**
     * This method fills a passed route with default values
     * for missing values to fulfill at least our required
     * two node condition /object/action/
     *
     * @param array $route The current route
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The resulting array containing "object" and "action" of target route
     * @access protected
     * @static
     */
    /*
    protected static function fillUp(array $route)
    {
        // the filled up route
        $result = array();

        // the count of nodes (e.g. X/Y/Z ..)
        $countNodes = count($route);

        // case1: at least object + action is set
        if ($countNodes > 1) {
            // at least "object" and "action" found -> don't worry be happy
            $result = $route;

        } elseif ($countNodes === 1) {
            // case 2: only object set
            $result[0] = $route[0];
            $result[1] = self::DEFAULT_ACTION;

        } else {
            // case 3: nothin was set
            $result[0] = self::DEFAULT_OBJECT;
            $result[1] = self::DEFAULT_ACTION;
        }

        return $result;
    }
    */

    /**
     * This method takes an existing route and redirects as arguments
     * and checks the passed route for redirects matching the whole
     * current route or parts of it.
     *
     * @param array $route          The current active route
     * @param array $routeRedirects All possible redirects of the application
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The resulting array containing "object" and "action" of target route
     * @access protected
     * @static
     */
    /*
    protected static function redirect(array $route, array $routeRedirects = array())
    {
        // check if redirect is required
        if (count($routeRedirects)) {
            $route = self::fillUp($route[1]);

            // check for existing route redirects for "object" & "action"
            if (isset($routeRedirects[$route[0]][$route[1]])) {
                $node  = $routeRedirects[$route[0]][$route[1]];
                $route = array_merge($node, array_slice($route, 2));

            } elseif (isset($routeRedirects[$route[0]])) {
                $node = $routeRedirects[$route[0]];

                // check if a redirect array[x,y,z...] is defined on those level
                if (isset($node[0])) {
                    $route = array_merge($node, array_slice($route, 1));
                }
            }
        }

        // return the target route
        return $route;
    }
    */

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
     * @static
     */
    protected static function buildRoutingProfile($node1 = null, $node2 = null)
    {
        return array(
            ($node1 !== null) ? $node1 : self::DEFAULT_OBJECT,
            ($node2 !== null) ? $node2 : self::DEFAULT_ACTION
        );
    }
}
