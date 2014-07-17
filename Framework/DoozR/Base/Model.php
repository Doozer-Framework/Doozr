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
class DoozR_Base_Model extends DoozR_Base_Model_Observer
{
    /**
     * holds data for CRUD operation(s)
     *
     * @var mixed
     * @access protected
     */
    protected $data;

    /**
     * contains the complete request
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
     * contains the translation for reading request
     *
     * @var array
     * @access protected
     */
    protected $translation;

    /**
     * Contains an instance of the module DoozR_Cache_Service
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
     * Constructor of this class
     *
     * @param array               $request         The whole request as processed by "Route"
     * @param array               $translation     The translation required to read the request
     * @param array               $originalRequest The original untouched request
     * @param DoozR_Cache_Service $cache           An instance of DoozR_Cache_Service
     * @param DoozR_Config        $configuration   The main configuration
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Base_Model
     * @access public
     */
    public function __construct(
        array               $request,
        array               $translation,
        array               $originalRequest,
        DoozR_Cache_Service $cache,
        DoozR_Config        $configuration
    ) {
        // store
        $this->request         = $request;
        $this->translation     = $translation;
        $this->originalRequest = $originalRequest;
        $this->cache           = $cache;

        $this->setConfiguration($configuration);

        // check for __tearup - Method (it's DoozR's __construct-like magic-method)
        if ($this->hasMethod('__tearup') && is_callable(array($this, '__tearup'))) {
            $this->__tearup($request, $translation);
        }
    }

    /**
     * This method (container) is intend to return the data for a requested mode.
     *
     * @param boolean $force TRUE to force retrieval fresh data, otherwise FALSE to use already retrieved [default]
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The data for the mode requested
     * @access public
     */
    public function getData($force = false)
    {
        if ($this->data === null || $force === true) {
            // custom generic overload solution
            if (method_exists($this, '__data')) {
                $arguments = func_get_args();

                if (count($arguments) > 0) {
                    call_user_func_array(array($this, '__data'), $arguments);
                } else {
                    call_user_func(array($this, '__data'));
                }
            }
        }

        return $this->data;
    }

    /**
     * This method (container) is intend to set the data for a requested mode.
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
}
