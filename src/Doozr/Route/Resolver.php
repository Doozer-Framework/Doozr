<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Route - Dispatcher
 *
 * Resolver.php - Route dispatcher for dispatching route from request state to MVP.
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
 * @package    Doozr_Route
 * @subpackage Doozr_Route_Resolver
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Route/Collector.php';

use FastRoute\Dispatcher\GroupCountBased;

/**
 * Doozr - Route - Dispatcher
 *
 * Route dispatcher for dispatching route from request state to MVP.
 *
 * @category   Doozr
 * @package    Doozr_Route
 * @subpackage Doozr_Route_Resolver
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Route_Resolver extends GroupCountBased
{
    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param array  $routes  The routes to use for dispatch
     * @param string $method  The HTTP method to dispatch for
     * @param array  $options The optional additional options to pass
     *
     * @todo Refactor: Cleanup the init stuff. More abstraction and encapsulation in Doozr classes!
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     */
    public function __construct(array $routes = [], $method = Doozr_Http::REQUEST_METHOD_GET, array $options = [])
    {
        // Combine options from runtime with fixed configuration ...
        $options += [
            'routeParser'   => 'FastRoute\\RouteParser\\Std',
            'dataGenerator' => 'FastRoute\\DataGenerator\\GroupCountBased',
            'dispatcher'    => 'FastRoute\\Dispatcher\\GroupCountBased',
        ];

        // Get Route collector!
        $routeCollector = new Doozr_Route_Collector(
            new $options['routeParser'], new $options['dataGenerator']
        );

        foreach ($routes as $route => $config) {
            $routeCollector->addRoute($method, $route, array($config->presenter, $config->action));
        }

        parent::__construct($routeCollector->getData());
    }

    /**
     * Wrapper for using resolve() instead of dispatch().
     *
     * @param string $httpMethod The HTTP Method to route
     * @param string $uri        The URI to route
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The result as array?
     * @access public
     */
    public function resolve($httpMethod, $uri)
    {
        return parent::dispatch($httpMethod, $uri);
    }
}
