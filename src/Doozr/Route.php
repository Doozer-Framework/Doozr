<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Route.
 *
 * Route.php - Responsible for matching routes (collected from both -> .routes.json.json configuration in
 * Doozr's main configuration as well as the applications configuration and annotations from applications presenters)
 * against the current request-URI.
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
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Class.php';

use Doctrine\Common\Annotations\AnnotationReader;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid;

/**
 * Doozr - Route.
 *
 * Responsible for matching routes (collected from both -> .routes.json.json configuration in
 * Doozr's main configuration as well as the applications configuration and annotations from applications presenters)
 * against the current request-URI.
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
 * @final
 */
final class Doozr_Route extends Doozr_Base_Class
{
    /**
     * The UUID of the active route (request url).
     *
     * @var string
     */
    protected $uuid;

    /**
     * Status of cache.
     *
     * @var bool
     */
    protected $cacheEnabled;

    /**
     * An annotation reader instance.
     *
     * @var AnnotationReader
     * @static
     */
    protected static $annotationReader;

    /**
     * Instance of Dispatcher class.
     *
     * @var Doozr_Route_Resolver
     */
    protected $dispatcher;

    /**
     * The current active route prefilled with defaults. The minimum length of a route is 2 nodes which
     * represents an object and its action. But through .config.json you are free to define a custom route
     * pattern and so up to n-nodes-routes (e.g. /foo/bar/foo/bar/...).
     *
     * @var array
     */
    protected $activeRoute = [
        self::DEFAULT_PRESENTER,
        self::DEFAULT_ACTION,
    ];

    /**
     * The scope used to separate routing data.
     *
     * @var string
     */
    protected $scope;

    /**
     * The route collection.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * The default object.
     *
     * @var string
     */
    const DEFAULT_PRESENTER = 'index';

    /**
     * The default action.
     *
     * @var string
     */
    const DEFAULT_ACTION = 'index';

    /**
     * Scope used for caching routes and routing metadata.
     *
     * @var string
     */
    const SCOPE_CACHE = 'cache.routes';

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param Doozr_Registry $registry Registry of Doozr
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __construct(Doozr_Registry $registry)
    {
        $this
            ->registry($registry)
            ->cacheEnabled($registry->getParameter('doozr.kernel.caching'))
            ->scope(DOOZR_NAMESPACE_FLAT.'.'.self::SCOPE_CACHE);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Routes the passed state to a controller:action and returns the request.
     *
     * @param Request $request The request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Request The request enriched with routing information (route).
     *
     * @throws \Doozr_Route_Exception
     */
    public function route(Request $request)
    {
        // Map the request states URL to a route (presenter:action)
        $uri = ''.$request->getUri();

        $route = $this
            ->uuid(
                $this->calculateUuid($uri)
            )
            ->mapToRoute(
                $request, $this->retrieveRoutes()
            );

        // Check for 404 and 405 -> can and should be caught here
        switch ($route[0]) {
            case Doozr_Route_Resolver::NOT_FOUND:
                throw new Doozr_Route_Exception(
                    sprintf('Route %s not found', $request->getUri()->getPath()),
                    Doozr_Http::NOT_FOUND
                );
                break;

            case Doozr_Route_Resolver::METHOD_NOT_ALLOWED:
                throw new Doozr_Route_Exception(
                    sprintf('Method no allowed. Allowed method(s): %s', $route[1]),
                    Doozr_Http::METHOD_NOT_ALLOWED
                );
                break;

            case Doozr_Route_Resolver::FOUND:
                // Store result of dispatch process - we wil use this later as identifier for status response dispatch
                $request = $request->withAttribute(
                    'route', new Doozr_Request_Route_State($route[1][0], $route[1][1])
                );

                if (true === isset($route[2])) {
                    $request = $request->withQueryParams($route[2]);
                }
                break;
        }

        return $request;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | SETTER, GETTER, ADDER, REMOVER, ISSER & HASSER
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for routes.
     *
     * @param string $routes The routes
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
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
     *
     * @return $this Instance for chaining
     */
    protected function routes($routes)
    {
        $this->setRoutes($routes);

        return $this;
    }

    /**
     * Getter for routes.
     *
     * @param string $method Optional method argument to return only routes for a specific method/verb.
     * @param array  $routes Routes to use for lookup
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array Collection of stored routes
     *
     * @throws Doozr_Route_Exception
     */
    protected function getRoutes($method = null, array $routes = [])
    {
        if (null !== $method) {
            if (false === isset($routes[$method])) {
                throw new Doozr_Route_Exception(
                    sprintf(
                        'No routes for HTTP-method "%s" found.',
                        $method
                    ),
                    Doozr_Http::METHOD_NOT_ALLOWED
                );
            }
            $routes = $routes[$method];
        }

        return $routes;
    }

    /**
     * Setter for uuid.
     *
     * @param string $uuid The uuid
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
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
     *
     * @return $this Instance for chaining
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
     *
     * @return string
     */
    protected function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Setter for cacheEnabled.
     *
     * @param bool $cacheEnabled TRUE|FALSE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
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
     *
     * @return $this Instance for chaining
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
     *
     * @return bool TRUE = cache enabled, FALSE = cache disabled
     */
    protected function getCacheEnabled()
    {
        return $this->cacheEnabled;
    }

    /**
     * Isser for cacheEnabled.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE = cache enabled, FALSE = cache disabled
     */
    protected function isCacheEnabled()
    {
        return $this->getCacheEnabled();
    }

    /**
     * Setter for scope.
     *
     * @param string $scope The scope
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setScope($scope)
    {
        $this->scope = $scope;
    }

    /**
     * Setter for scope.
     *
     * @param string $scope The scope
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function scope($scope)
    {
        $this->setScope($scope);

        return $this;
    }

    /**
     * Getter for scope.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The namspace if set, otherwise NULL
     */
    protected function getScope()
    {
        return $this->scope;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Maps the request to a route (combination of presenter:action).
     *
     * @param Request $request The request to map against
     * @param array   $routes  The routes to map against as collection
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Request The request enriched with route.
     *
     * @throws Doozr_Route_Exception
     */
    protected function mapToRoute(Request $request, array $routes = [])
    {
        $method = $request->getMethod();

        if (false === isset($routes[$method])) {
            throw new Doozr_Route_Exception(
                Doozr_Http::REASONPHRASE_METHOD_NOT_ALLOWED,
                Doozr_Http::METHOD_NOT_ALLOWED
            );
        }

        // Dispatch via wrapped Fastroute
        $resolver = new Doozr_Route_Resolver(
            $routes[$method],
            $method
        );

        return $resolver->resolve($method, $request->getUri()->getPath());
    }

    /**
     * Returns the routes (URL => class:method) from presenters in filesystem and configuration.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array Of routes retrieved
     */
    protected function retrieveRoutes()
    {
        $routes = null;

        // 1st of all we'll try to fetch routes from cache if enabled
        if (true === $this->isCacheEnabled()) {
            try {
                $routes = $this->getRegistry()->getCache()->read(
                    $this->getUuid(),
                    $this->getScope()
                );
            } catch (Doozr_Cache_Service_Exception $e) {
                $routes = null;
            }
        }

        // If cache either disabled or routes could not be fetched -> retrieve them right now ...
        if (null === $routes) {
            $routesFromPresenters    = $this->getRoutesFromPresenters();
            $routesFromConfiguration = $this->getRoutesFromConfiguration(
                (array) $this->getRegistry()->getConfiguration()->kernel->transmission->routing->routes
            );

            $routes = array_merge_recursive($routesFromPresenters, $routesFromConfiguration);

            // Cache if enabled ...
            if (true === $this->getCacheEnabled()) {
                // Store result after reading from presenter(s) and config and so on ...
                $this->getRegistry()->getCache()->create(
                    $this->getUuid(),
                    $routes,
                    null,
                    $this->getScope()
                );
            }
        }

        // Return routes either from cache or fresh retrieved ...
        return $routes;
    }

    /**
     * Calculates a UUID for a passed string.
     *
     * @param string $input The input to calculate the UUID for.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The UUID
     */
    protected function calculateUuid($input)
    {
        try {
            // Generate a version 5 (name-based and hashed with SHA1) UUID object
            $uuid5 = Uuid::uuid5(Uuid::NAMESPACE_DNS, $input);
            $uuid  = $uuid5->toString();
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
     *
     * @return object The new instance
     */
    protected function objectToObject($instance, $classname = 'stdClass')
    {
        // Some "hack" - known to work in general across all PHP versions
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
     *
     * @return array Routes from file ordered and ready for further use
     */
    protected function parseRoutesFromFile($filename)
    {
        // Assume empty result
        $routes            = [];
        $actionsWithRoutes = [];

        // Check if file exists ...
        if (true === $this->getRegistry()->getFilesystem()->readable($filename)) {
            include $filename;

            $content = file_get_contents($filename);
            $matches = preg_match('/class\s([A-Za-z\_]+)/', $content, $classes);

            // Process all found classes ...
            if ($matches > 0) {

                // Use our selection from result
                $classname = $classes[1];

                // Lookup for namespace ...
                $namespaceCount = preg_match('#^namespace\s+(.+?);$#sm', $content, $namespace);

                // Extract namespace of file/class if found ...
                if ($namespaceCount > 0) {
                    $namespace = $namespace[1].'\\';
                } else {
                    $namespace = '';
                }

                // Combine everything to a full qualified classname ...
                $fullQualifiedClassname = $namespace.$classname;

                $reflection = new ReflectionClass($fullQualifiedClassname);

                // Extract the methods (potentially an ...Action())
                $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

                foreach ($methods as $key => $reflection) {
                    if (preg_match('/(Action)$/ui', $reflection->name)) {
                        if (isset($actionsWithRoutes[$filename]) === false) {
                            $actionsWithRoutes[$filename] = [];
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
     *
     * @return array Ordered routes
     */
    protected function sortRoutes(array $routesByFile)
    {
        $sortedRoutes = [];

        // Now we need to organize this stuff a bit
        if (count($routesByFile) > 0) {
            foreach ($routesByFile as $file => $routesByAction) {
                foreach ($routesByAction as $action => $routes) {
                    /* @var \Doozr\Route\Annotation\Route $route */
                    foreach ($routes as $route) {
                        $methods = $route->getMethods();

                        foreach ($methods as $method) {
                            if (isset($sortedRoutes[$method]) === false) {
                                $sortedRoutes[$method] = [];
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
     *
     * @return AnnotationReader Instance of annotation reader
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
     *
     * @return string The classname
     */
    protected function getPresenterClassByFilename($filename)
    {
        return 'Presenter_'.str_replace('.php', '', basename($filename));
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
     *
     * @return string The name of the presenter
     */
    protected function getPresenterByClassname($classname)
    {
        return strtolower(explode('_', $classname)[1]);
    }

    /**
     * Retrieves routes from configuration in same format as from presenters.
     *
     * @param array $configuration The configuration to parse.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The prepared/processed result
     */
    protected function getRoutesFromConfiguration(array $configuration)
    {
        // Result
        $routes = [];

        // Convert routes to same context as from presenter
        foreach ($configuration as $route => $config) {

            // Check for method and set DEFAULT to GET if not set
            if (isset($config->methods) && null !== $config->methods) {
                $methods = explode(',', $config->methods);
            } else {
                $methods = [Doozr_Http::REQUEST_METHOD_GET];
            }

            foreach ($methods as $method) {
                if (false === isset($routes[$method])) {
                    $routes[$method] = [];
                }
                $config->route           = $route;
                $routes[$method][$route] = $config;
            }
        }

        return $routes;
    }

    /**
     * Parses all routes from all presenters (annotations).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array Routes parsed from presenters.
     */
    protected function getRoutesFromPresenters()
    {
        // Assume no routes
        $routes = [];

        $directoryIterator = new RecursiveDirectoryIterator(DOOZR_APP_ROOT.'App/Presenter');
        $iteratorIterator  = new RecursiveIteratorIterator($directoryIterator);
        $regexIterator     = new RegexIterator($iteratorIterator, '/.*\.php/i', RecursiveRegexIterator::GET_MATCH);

        // Iterate over result
        foreach ($regexIterator as $file) {
            // Found a ".php" file in presenter folder ... try to extract routes
            $routesByMethod = self::parseRoutesFromFile($file[0]);

            foreach ($routesByMethod as $method => $routesForMethod) {
                if (isset($routes[$method]) === false) {
                    $routes[$method] = [];
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
     *
     * @return string[] The resulting array containing two nodes "object" and "action" of target route
     */
    protected function buildRoutingProfile($node1 = null, $node2 = null)
    {
        return [
            ($node1 !== null) ? $node1 : self::DEFAULT_PRESENTER,
            ($node2 !== null) ? $node2 : self::DEFAULT_ACTION,
        ];
    }
}
