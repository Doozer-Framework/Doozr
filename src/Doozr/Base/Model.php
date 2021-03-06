<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Base - Model.
 *
 * Model.php - Base class for Models
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
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Model/Observer.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Model/Interface.php';

use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Doozr Base Model.
 *
 * Base class for Models
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
class Doozr_Base_Model extends Doozr_Base_Model_Observer
    implements
    Doozr_Base_Model_Interface
{
    /**
     * Data for CRUD operation(s).
     *
     * @var mixed
     */
    protected $data;

    /**
     * Active/last action.
     *
     * @var string
     */
    protected $action;

    /**
     * Active/last route.
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
     * Instance of the Doozr_Cache_Service.
     *
     * @var Doozr_Cache_Service|CacheItemPoolInterface
     */
    protected $cache;

    /**
     * The main configuration for use in model environment.
     *
     * @var Doozr_Configuration
     */
    protected $configuration;

    /**
     * Constructor.
     *
     * @param Doozr_Registry $registry Doozr registry
     * @param Request        $request  Request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __construct(
        Doozr_Registry $registry,
        Request        $request
    ) {
        // Store all passed instances
        $this
            ->registry($registry)
            ->route($request->getAttribute('route'))
            ->request($request)
            ->cache($registry->getCache())
            ->configuration($registry->getConfiguration());

        // Now tearup the stuff from App scope ...
        $this->__tearup();

        // Ensure parent's constructor is called
        parent::__construct();
    }

    /**
     * Setter for data attribute.
     *
     * @param mixed $data The data
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     */
    public function setData($data)
    {
        $this->data = $data;

        return true;
    }

    /**
     * Fluent: Setter for data attribute.
     *
     * @param mixed $data The data
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
     * Getter for data.
     *
     * @param bool $force TRUE to force retrieval fresh data, otherwise FALSE to use already retrieved [default]
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed The data requested
     */
    public function getData($force = false)
    {
        if ($this->data === null || $force === true) {
            $route  = $this->getRoute();
            $action = $route->getAction();
            $method = false;

            $this->action($action);

            // Check for concrete method call
            if (method_exists($this, '__data'.ucfirst($action))) {
                // Concrete integration ...
                $method = '__data'.ucfirst($action);
            } elseif (method_exists($this, '__data')) {
                // custom generic overload solution
                $method = '__data';
            }

            // Call if method detected ...
            if ($method !== false) {
                $arguments = func_get_args();

                if (count($arguments) > 0) {
                    $result = call_user_func_array([$this, $method], $arguments);
                } else {
                    $result = call_user_func([$this, $method]);
                }

                if (true !== $result) {
                    throw new Doozr_Base_Model_Exception(
                        sprintf(
                            '%s() (if set) MUST return TRUE. %s() is set but it returned: "%s"',
                            $method,
                            $method,
                            var_export($result, true)
                        )
                    );
                }
            }
        }

        return $this->data;
    }

    /**
     * Setter for route.
     *
     * @param Doozr_Request_Route_State $route The route to set
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
     * @param Doozr_Request_Route_State $route The route to set
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
     * @return Doozr_Request_Route_State The route stored, otherwise NULL
     */
    protected function getRoute()
    {
        return $this->route;
    }

    /**
     * Setter for request.
     *
     * @param Request $request The request to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Setter for request.
     *
     * @param Request $request The request to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function request(Request $request)
    {
        $this->setRequest($request);

        return $this;
    }

    /**
     * Getter for request.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Request The request stored, otherwise NULL
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * Setter for cache.
     *
     * @param CacheItemPoolInterface $cache The cache service instance to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setCache(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Setter for cache.
     *
     * @param CacheItemPoolInterface $cache The cache service instance to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function cache(CacheItemPoolInterface $cache)
    {
        $this->setCache($cache);

        return $this;
    }

    /**
     * Getter for cache.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return CacheItemPoolInterface The cache service instance stored, otherwise NULL
     */
    protected function getCache()
    {
        return $this->cache;
    }

    /**
     * Setter for action.
     *
     * @param string $action The action to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Setter for action.
     *
     * @param string $action The action to set
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
     * @return string The last action processed, otherwise NULL in clean state
     */
    protected function getAction()
    {
        return $this->action;
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
     * Setter for configuration with fluent API support for chaining calls to this class.
     *
     * @param Doozr_Configuration_Interface $configuration The
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
     * @return Doozr_Configuration_Interface The configuration stored
     */
    protected function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Escapes values from bad stuff but only simple.
     *
     * @param string $string String to escape
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string Escaped input
     */
    public function escape($string)
    {
        $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        $string = str_replace('{{', '', $string);
        $string = str_replace('}}', '', $string);

        return htmlentities($string, ENT_QUOTES, 'UTF-8');
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
        $method = '__'.str_replace(__CLASS__.'::', '', __METHOD__);

        if ($this->hasMethod($method) && is_callable([$this, $method])) {
            $arguments = func_get_args();
            if (empty($arguments)) {
                $result = $this->{$method}();
            } else {
                $result = call_user_func_array([$this, $method], $arguments);
            }

            return $result;
        }
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
        $method = '__'.str_replace(__CLASS__.'::', '', __METHOD__);

        if ($this->hasMethod($method) && is_callable([$this, $method])) {
            $arguments = func_get_args();
            if (empty($arguments)) {
                $result = $this->{$method}();
            } else {
                $result = call_user_func_array([$this, $method], $arguments);
            }

            return $result;
        }
    }

    /**
     * Delete of cruD.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     */
    protected function delete()
    {
        $method = '__'.str_replace(__CLASS__.'::', '', __METHOD__);

        if ($this->hasMethod($method) && is_callable([$this, $method])) {
            $arguments = func_get_args();
            if (empty($arguments)) {
                $result = $this->{$method}();
            } else {
                $result = call_user_func_array([$this, $method], $arguments);
            }

            return $result;
        }
    }

    /**
     * Returns an array containing a flat structure for a breadcrumb navigation.
     *
     * @param string $url  The URL used to extract breadcrumb from
     * @param string $home The crumb used for "Home" e.g. >> Home
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The resulting breadcrumb structure
     */
    protected function getBreadcrumbByUrl($url, $home = 'Home')
    {
        $nodes      = explode('/', $url);
        $countNodes = count($nodes);

        $breadcrumb = [];
        $root       = '';

        for ($i = 0; $i < $countNodes; ++$i) {
            $node         = ($i === 0) ? $home : $nodes[$i];
            $breadcrumb[] = [
                'href'   => ($i === 0) ? '/' : ($root.'/'.$node),
                'text'   => $node,
                'active' => ($i === ($countNodes - 1)),
                'class'  => ($i === ($countNodes - 1)) ? 'active' : null,
                'id'     => ($i === ($countNodes - 1)) ? 'breadcrumb' : null,
            ];

            $root .= ($i > 0) ? ('/'.$node) : '';
        }

        return $breadcrumb;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | MAGIC METHODS
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to call the teardown method of a model if exist.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __destruct()
    {
        $this->__teardown();
    }

    /**
     * __construct replacement for Model.
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
     * __destruct replacement for Model.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool Must return TRUE in case of success, otherwise FALSE
     */
    protected function __teardown()
    {
        return true;
    }
}
