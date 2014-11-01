<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Base - Model
 *
 * Model.php - Base class for model-layers from MV(C|P)
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
 * @subpackage DoozR_Base_Model
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Model/Observer.php';
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Model/Interface.php';

/**
 * DoozR Base Model
 *
 * Base class for model-layers from MV(C|P)
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Model
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Base_Model extends DoozR_Base_Model_Observer implements DoozR_Base_Model_Interface
{
    /**
     * Data for CRUD operation(s)
     *
     * @var mixed
     * @access protected
     */
    protected $data;

    /**
     * The complete request
     *
     * @var array
     * @access protected
     */
    protected $request;

    /**
     * The original untouched request
     *
     * @var array
     * @access protected
     */
    protected $originalRequest;

    /**
     * The request state object of DoozR
     *
     * @var DoozR_Request_State
     * @access protected
     */
    protected $requestState;

    /**
     * Translation for reading request
     *
     * @var array
     * @access protected
     */
    protected $translation;

    /**
     * Instance of the DoozR_Cache_Service
     *
     * @var DoozR_Cache_Service
     * @access protected
     */
    protected $cache;

    /**
     * The main configuration for use in model environment.
     *
     * @var DoozR_Config
     * @access protected
     */
    protected $configuration;


    /**
     * Constructor.
     *
     * @param DoozR_Registry             $registry      DoozR_Registry containing all core components
     * @param DoozR_Base_State_Interface $requestState  Whole request as state
     * @param array                      $request       Whole request as processed by "Route"
     * @param array                      $translation   Translation required to read the request
     * @param DoozR_Cache_Service        $cache         Instance of DoozR_Cache_Service
     * @param DoozR_Config               $configuration Main configuration
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Base_Model
     * @access public
     * @throws DoozR_Base_Model_Exception
     */
    public function __construct(
        DoozR_Registry             $registry,
        DoozR_Base_State_Interface $requestState,
        array                      $request,
        array                      $translation,
        DoozR_Cache_Service        $cache,
        DoozR_Config               $configuration
    ) {
        // Store all passed instances
        $this
            ->registry($registry)
            ->request($request)
            ->translation($translation)
            ->originalRequest($requestState->getRequest())
            ->requestState($requestState)
            ->cache($cache)
            ->configuration($configuration);

        // Check for __tearup - Method (it's DoozR's __construct-like magic-method)
        if ($this->hasMethod('__tearup') && is_callable(array($this, '__tearup'))) {
            $result = $this->__tearup($request, $translation);

            if ($result !== true) {
                throw new DoozR_Base_Model_Exception(
                    '__tearup() must (if set) return TRUE. __tearup() executed and it returned: ' .
                    var_export($result, true)
                );
            }
        }
    }

    /**
     * Setter for request.
     *
     * @param array $request The request to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setRequest(array $request)
    {
        $this->request = $request;
    }

    /**
     * Setter for request.
     *
     * @param array $request The request to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function request(array $request)
    {
        $this->setRequest($request);
        return $this;
    }

    /**
     * Getter for request.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array|null The request stored, otherwise NULL
     * @access protected
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * Setter for translation.
     *
     * @param array $translation The translation to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setTranslation(array $translation)
    {
        $this->translation = $translation;
    }

    /**
     * Setter for translation.
     *
     * @param array $translation The translation to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function translation(array $translation)
    {
        $this->setTranslation($translation);
        return $this;
    }

    /**
     * Getter for translation.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array|null The translation stored, otherwise NULL
     * @access protected
     */
    protected function getTranslation()
    {
        return $this->translation;
    }

    /**
     * Setter for originalRequest.
     *
     * @param mixed $originalRequest The originalRequest to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setOriginalRequest($originalRequest)
    {
        $this->originalRequest = $originalRequest;
    }

    /**
     * Setter for originalRequest.
     *
     * @param mixed $originalRequest The originalRequest to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function originalRequest($originalRequest)
    {
        $this->setOriginalRequest($originalRequest);
        return $this;
    }

    /**
     * Getter for originalRequest.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed|null The originalRequest stored, otherwise NULL
     * @access protected
     */
    protected function getOriginalRequest()
    {
        return $this->originalRequest;
    }

    /**
     * Setter for requestState.
     *
     * @param DoozR_Request_State $requestState The request state to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setRequestState($requestState)
    {
        $this->requestState = $requestState;
    }

    /**
     * Setter for requestState.
     *
     * @param DoozR_Request_State $requestState The request state to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function requestState($requestState)
    {
        $this->setRequestState($requestState);
        return $this;
    }

    /**
     * Getter for requestState.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Request_State|null The requestState stored, otherwise NULL
     * @access protected
     */
    protected function getRequestState()
    {
        return $this->requestState;
    }

    /**
     * Setter for cache.
     *
     * @param DoozR_Cache_Service $cache The cache service instance to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setCache(DoozR_Cache_Service $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Setter for cache.
     *
     * @param DoozR_Cache_Service $cache The cache service instance to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function cache(DoozR_Cache_Service $cache)
    {
        $this->setCache($cache);
        return $this;
    }

    /**
     * Getter for cache.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Cache_Service|null The cache service instance stored, otherwise NULL
     * @access protected
     */
    protected function getCache()
    {
        return $this->cache;
    }

    /**
     * This method (container) is intend to set the data for a requested runtimeEnvironment.
     *
     * @param mixed $data The data (array prefered) to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Setter for data with fluent API support for chaining calls to this class.
     *
     * @param mixed $data The data to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Base_Model $this Instance for chaining
     * @access public
     */
    public function data($data)
    {
        $this->setData($data);
        return $this;
    }

    /**
     * This method (container) is intend to return the data for a requested runtimeEnvironment.
     *
     * @param bool $force TRUE to force retrieval fresh data, otherwise FALSE to use already retrieved [default]
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The data for the runtimeEnvironment requested
     * @access public
     */
    public function getData($force = false)
    {
        if ($this->data === null || $force === true) {

            // Get action
            $action = $this->getRequestState()->getActiveRoute()[$this->getRequestState()->getTranslationMatrix()[1]];

            // Assume no method to call
            $method = false;

            // Check for concrete method call
            if (method_exists($this, '__data' . ucfirst($action))) {
                // Concrete integration ...
                $method = '__data' . ucfirst($action);

            } elseif (method_exists($this, '__data')) {
                // custom generic overload solution
                $method = '__data';
            }

            // Call if method detected ...
            if ($method !== false) {
                $arguments = func_get_args();

                if (count($arguments) > 0) {
                    call_user_func_array(array($this, $method), $arguments);

                } else {
                    call_user_func(array($this, $method));

                }
            }
        }

        return $this->data;
    }

    /**
     * Setter for configuration.
     *
     * @param DoozR_Config_Interface $configuration The configuation object
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    protected function setConfiguration(DoozR_Config_Interface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Setter for configuration with fluent API support for chaining calls to this class.
     *
     * @param DoozR_Config_Interface $configuration The
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    protected function configuration(DoozR_Config_Interface $configuration)
    {
        $this->setConfiguration($configuration);
        return $this;
    }

    /**
     * Getter for configuration.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Config_Interface The configuration stored
     * @access public
     */
    protected function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Escapes values from bad stuff but only simple
     *
     * @param string $string String to escape
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Escaped input
     * @access public
     */
    public function escape($string)
    {
        $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        $string = str_replace('{{', '', $string);
        $string = str_replace('}}', '', $string);
        return htmlentities($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Create of Crud
     *
     * @param mixed $data The data for create
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access protected
     */
    protected function create($data = null)
    {
        $method = '__' . str_replace(__CLASS__ . '::', '', __METHOD__);

        if ($this->hasMethod($method) && is_callable(array($this, $method))) {
            $arguments = func_get_args();
            if (empty($arguments)) {
                $result = $this->{$method}();
            } else {
                $result = call_user_func_array(array($this, $method), $arguments);
            }

            return $result;
        }
    }

    /**
     * Read of cRud
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Data on success, otherwise null
     * @access protected
     */
    protected function read()
    {
        $method = '__' . str_replace(__CLASS__ . '::', '', __METHOD__);

        if ($this->hasMethod($method) && is_callable(array($this, $method))) {
            $arguments = func_get_args();
            if (empty($arguments)) {
                $result = $this->{$method}();
            } else {
                $result = call_user_func_array(array($this, $method), $arguments);
            }

            return $result;
        }
    }

    /**
     * Delete of cruD
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access protected
     */
    protected function delete()
    {
        $method = '__' . str_replace(__CLASS__ . '::', '', __METHOD__);

        if ($this->hasMethod($method) && is_callable(array($this, $method))) {
            $arguments = func_get_args();
            if (empty($arguments)) {
                $result = $this->{$method}();
            } else {
                $result = call_user_func_array(array($this, $method), $arguments);
            }

            return $result;
        }
    }

    /**
     * Returns an array containing a flat structure for a breadcrumb navigation
     *
     * @param $url The URL used to extract breadcrumb from
     *
     * @author Benjamin Carl <benjamin.carl@clickalicious.de>
     * @return array The resulting breadcrumb structure
     * @access protected
     */
    protected function getBreadcrumbByUrl($url, $home = 'Home')
    {
        $nodes      = explode('/', $url);
        $countNodes = count($nodes);

        $breadcrumb = array();
        $root       = '';

        for ($i = 0; $i < $countNodes; ++$i) {
            $node = ($i === 0) ? $home : $nodes[$i];
            $breadcrumb[] = array(
                'href'   => ($i === 0) ? '/' : ($root . '/' . $node),
                'text'   => $node,
                'active' => ($i === ($countNodes - 1)),
                'class'  => ($i === ($countNodes - 1)) ? 'active' : null,
                'id'     => ($i === ($countNodes - 1)) ? 'breadcrumb' : null,
            );

            $root .= ($i > 0) ? ('/' . $node) : '';
        }

        return $breadcrumb;
    }

    /**
     * This method is intend to call the teardown method of a model if exist
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __destruct()
    {
        // check for __tearup - Method (it's DoozR's __construct-like magic-method)
        if ($this->hasMethod('__teardown') && is_callable(array($this, '__teardown'))) {
            $this->__teardown();
        }
    }
}
