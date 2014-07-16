<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Route
 *
 * Route.php - This class is the heart of DoozR's routing mechanism.
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

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Class.php';

/**
 * DoozR - Route
 *
 * Route.php - This class is the heart of DoozR's routing mechanism.
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
final class DoozR_Route extends DoozR_Base_Class
{
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
    protected static $translationMatrix;

    /**
     * The registry of the DoozR Framework for accessing
     * base objects.
     *
     * @var array
     * @access protected
     * @static
     */
    protected static $registry;

    /**
     * The default object.
     *
     * @var string
     * @access const
     */
    const DEFAULT_OBJECT = 'Index';

    /**
     * The default action.
     *
     * @var string
     * @access const
     */
    const DEFAULT_ACTION = 'Read';


    /*******************************************************************************************************************
     * // BEGIN PUBLIC METHODS
     ******************************************************************************************************************/

    /**
     * This method inititalizes the routing process by taking
     * the most important input as arguments and prepare it for
     * further processing.
     *
     * @param string                   $requestUri The URI used to request the current resource
     * @param string                   $route      The route configuration from .config
     * @param DoozR_Registry_Interface $registry   The registry of DoozR for config ...
     * @param boolean                  $autorun    TRUE to automatic run/execute the route
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     * @static
     */
    public static function init($requestUri, $route, DoozR_Registry_Interface $registry, $autorun = true)
    {
        // store registry
        self::$registry = $registry;

        // now begin generic parsing of pattern
        // get exclude(s) and build exclude-regexp of it/them
        $exclude = regexp($route->exclude, 'exclude');

        // get pattern and inject exclude in pattern
        $pattern = str_replace('{{EXCLUDE}}', $exclude, $route->pattern->pattern);

        // get object(s) + action(s) from request
        $result = preg_match_all($pattern, $requestUri, $request);

        // check for redirect/fillup
        self::$request = self::redirect($request, object_to_array($route->routes));

        // get pattern translation as array from config
        self::$translationMatrix = explode('/', $route->pattern->translation);

        // if autorun is enabled we execute the dispatched route
        if ($autorun === true || true) {
            self::run();
        }
    }

    /**
     * This method runs or executes the presetted configuration. This runner
     * was seperated from init() to enable the user/application to manually
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
        // get translation
        $translated = self::translate(self::$request, self::$translationMatrix);

        // Dispatch the new route to logger-subsystem (e.g. for use as filename in file-logger)
        self::$registry->logger->route(implode('_', $translated));

        // store the route as active route
        self::$activeRoute = $translated;

        // Check for usage of DoozR's MVC-/MVP-structure ...
        if (self::$registry->config->base->pattern->enabled()) {
            // ... dispatch request (MVP/MVC) to DoozR's Back_Controller
            self::$registry->back->dispatch(
                self::$activeRoute,
                self::$request,
                self::$translationMatrix,
                self::$registry->config->base->pattern->type()
            );

        }
    }

    /**
     * This method returns the current active and routet route.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array An ARRAY containing "object" and "action"
     * @access public
     * @static
     */
    public function get()
    {
        return self::$activeRoute;
    }

    /*******************************************************************************************************************
     * \\ END PUBLIC METHODS
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN PRIVATE/PROTECTED METHODS
     ******************************************************************************************************************/

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
    protected static function translate(array $request, array $translation)
    {
        // the translated result
        $result = array();

        // get count of parts used in current pattern
        // outside the following loop -> performance
        $countParts = count($translation);

        // parse the parts
        for ($i = 0; $i < $countParts; ++$i) {

            if (isset($request[$i])) {
                $$translation[$i] = ucfirst(strtolower($request[$i]));
            } else {
                $$translation[$i] = 'Default';
            }

            // a list for dispatch (name => value - pairs)
            $result[$translation[$i]] = $$translation[$i];
        }

        // return translated input
        return $result;
    }

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
    protected static function redirect(array $route, array $routeRedirects = array())
    {
        // check if redirect is required
        if (count($routeRedirects)) {

            $route      = self::fillUp($route[1]);
            $countNodes = count($route);

            // check for existing route redirects for "object" & "action"
            if (isset($routeRedirects[$route[0]][$route[1]])) {
                $node = $routeRedirects[$route[0]][$route[1]];
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

    /*******************************************************************************************************************
     * \\ END PRIVATE/PROTECTED METHODS
     ******************************************************************************************************************/
}
