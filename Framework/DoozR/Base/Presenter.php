<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Base - Presenter
 *
 * Presenter.php - Base class for presenter-layers from MV(C|P)
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2013, Benjamin Carl - All rights reserved.
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
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Presenter/Subject.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Http.php';

/**
 * DoozR - Base Presenter
 *
 * Base Presenter of the DoozR Framework.
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Presenter
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Base_Presenter extends DoozR_Base_Presenter_Subject
{
    /**
     * holds data for CRUD operation(s)
     *
     * @var mixed
     * @access protected
     */
    protected $data;

    /**
     * Contains the instance of model for communication
     *
     * @var DoozR_Base_Model
     * @access protected
     */
    protected $model;

    /**
     * Contains the instance of view for cummunication
     *
     * @var DoozR_Base_View
     * @access protected
     */
    protected $view;

    /**
     * Contains the instance of config
     *
     * @var DoozR_Config
     * @access protected
     */
    protected $config;

    /**
     * contains the complete request
     *
     * @var array
     * @access protected
     */
    protected $request;

    /**
     * This array contains the required arguments
     * to run a specific action in a specific context
     *
     * @var array
     * @access protected
     */
    protected $required = array();

    /**
     * Allowed request types to execute against this
     * presenter
     *
     * @var array
     * @access protected
     */
    protected $allowed = array('get');

    /**
     * The original unmodified request as array
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
     * The count of root nodes
     *
     * @var integer
     * @access private
     */
    private $_nodes;


    /**
     * This method is the constructor of this class.
     *
     * @param array                  $request         The whole request as processed by "Route"
     * @param array                  $translation     The translation required to read the request
     * @param array                  $originalRequest The original untouched request
     * @param DoozR_Config_Interface $config          The DoozR main config instance
     * @param DoozR_Base_Model       $model           The model to communicate with backend (db)
     * @param DoozR_Base_View        $view            The view to display results
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __construct(
        array $request,
        array $translation,
        array $originalRequest,
        DoozR_Config_Interface $config = null,
        DoozR_Base_Model $model = null,
        DoozR_Base_View $view = null
    ) {
        // store
        $this->request         = $request;
        $this->translation     = $translation;
        $this->originalRequest = $originalRequest;
        $this->config          = $config;
        $this->model           = $model;
        $this->view            = $view;

        // important! => call parents constructor so SplObjectStorage is created!
        parent::__construct();

        // check for __tearup - Method (it's DoozR's __construct-like magic-method)
        if ($this->hasMethod('__tearup') && is_callable(array($this, '__tearup'))) {
            $this->__tearup($this->request, $this->translation);
        }
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
     * Returns the current active processed request as array.
     * If passed FALSE ($original = false) this method will
     * return the modified (already rewritten request) when
     * TRUE is passed then this method will return the original
     * request.
     *
     * @param boolean $original TRUE [default] to retrieve the raw request, FALSE to retrieve rewritten request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The request
     * @access public
     */
    public function getRequest($original = true)
    {
        if ($original === true) {
            $request = $this->originalRequest;
        } else {
            $request = $this->request;
        }

        return $request;
    }

    /**
     *
     *
     * @return DoozR_Response_Web|DoozR_Response_Cli|DoozR_Response_Httpd
     */
    protected function getResponse()
    {
        // get registry
        $registry = DoozR_Registry::getInstance();

        // get front-controller and return it
        #->sendText($text)
        return $registry->front->getResponse();
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
        if ($this->hasMethod('__create') && is_callable(array($this, '__create'))) {
            return $this->__create();
        }

        // notify observers about new data
        $this->notify();
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
        if ($this->hasMethod('__read') && is_callable(array($this, '__read'))) {
            return $this->__read();
        }

        // notify observers about new data
        $this->notify();
    }

    /**
     * Update of crUd
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Data on success, otherwise null
     * @access protected
     */
    protected function update()
    {
        if ($this->hasMethod('__update') && is_callable(array($this, '__update'))) {
            return $this->__update();
        }

        // notify observers about new data
        $this->notify();
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
        if ($this->hasMethod('__delete') && is_callable(array($this, '__delete'))) {
            return $this->__delete();
        }

        // notify observers about new data
        $this->notify();
    }

    /**
     * This method (container) is intend to return the data for a requested mode.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The data for the mode requested
     * @access public
     */
    public function getData()
    {
        pred('# ERROR CHECK FOR THIS STRING: oidsafd8fz8sniemxfeio #');
        # CHECK HERE IF WE MUST CALL Model->getData() INSTEAD
        #return $this->data;
    }

    /**
     * This method (container) is intend to set the data for a requested mode.
     *
     * @param mixed $data The data (array prefered) to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean True if everything wents fine, otherwise false
     * @access public
     */
    public function setData($data)
    {
        $this->data = $data;

        // notify observers about new data
        $this->notify();
    }

    /**
     * Adds a HTTP-method (verb like GET, HEAD, PUT, POST ...) to the list
     * of allowed methods for this presenter.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Base_Presenter Instance for chaining
     * @access protected
     */
    protected function allow($method)
    {
        if (!in_array($method, $this->allowed)) {
            $this->allowed[] = $method;
        }

        // chaining
        return $this;
    }

    /**
     * Checks if passed method (HTTP verb) is allowed.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if passed method is allowed, otherwise FALSE
     * @access protected
     */
    protected function allowed($method)
    {
        return in_array($method, $this->allowed);
    }

    /**
     * Checks if passed method (HTTP verb) is allowed.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if passed method is allowed, otherwise FALSE
     * @access protected
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
     * @param mixed  $variable A single argument required to execute the presenter or an array of arguments
     * @param string $scope    The scope (Action) for which the argument is required (* = wildcard = all)
     * @param string $method   The method (HTTP verb) to bind the requirement to
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean True if everything wents fine, otherwise false
     * @access protected
     */
    protected function required($argument, $scope = 'Index', $method = DoozR_Http::REQUEST_METHOD_GET)
    {
        // prepare storage on method/verb level
        if (!isset($this->required[$method])) {
            $this->required[$method] = array();
        }

        // prepare storage on scope level
        if (!isset($this->required[$method][$scope])) {
            $this->required[$method][$scope] = array();
        }

        // convert input to array if not an array
        if (!is_array($argument)) {
            $argument = array($argument);
        }

        // iterate the passed input to build ordered (scope) ruleset
        foreach ($argument as $requiredVariable) {
            // if passed argument does not have a validation set
            if (!is_array($requiredVariable)) {
                // prepare default validation => null
                $requiredVariable = array(
                    $requiredVariable,      // variable identifier
                    null                    // validation
                );
            }

            // store the combined values for automatic requirement management
            $this->required[$method][$scope][] = $requiredVariable;
        }

        // success
        return $this;
    }

    /**
     * Returns TRUE if a passed arguments is required by presenter, FALSE if not
     *
     * @param string $argument The argument to check
     * @param string $scope    The scope used for lookup
     * @param string $method   The method (HTTP verb) to use for lookup
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if required, otherwise FALSE
     * @access protected
     */
    protected function isRequired($argument, $scope = 'Index', $method = DoozR_Http::REQUEST_METHOD_GET)
    {
        // prepare storage on method/verb level
        if (!isset($this->required[$method])) {
            return false;
        }

        // prepare storage on scope level
        if (!isset($this->required[$method][$scope])) {
            return false;
        }

        // convert input to array if not an array
        if (!is_array($argument)) {
            $argument = array($argument);
        }

        // iterate the passed input to build ordered (scope) ruleset
        foreach ($argument as $requiredVariable) {
            pre($requiredVariable);
        }

        // success
        return true;
    }

    /**
     * Returns all required fields of presenter.
     *
     * @param string $scope  The scope used for lookup
     * @param string $method The method (HTTP verb) to use for lookup
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array List of required fields
     * @access protected
     */
    protected function getRequired($scope = 'Index', $method = DoozR_Http::REQUEST_METHOD_GET)
    {
        // prepare storage on method/verb level
        if (!isset($this->required[$method])) {
            return array();
        }

        // prepare storage on scope level
        if (!isset($this->required[$method][$scope])) {
            return array();
        }

        return $this->required[$method][$scope];
    }

    /**
     * Sets the count of root nodes for request.
     *
     * @param integer $countOfRootNodes The count of root nodes
     *
     * @example if request is /foo/bar/1234 and the root node count
     *          is 2 then all operations will use /foo and /bar as
     *          root.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Base_Presenter
     * @access protected
     */
    protected function nodes($countOfRootNodes)
    {
        $this->_nodes = $countOfRootNodes;

        return $this;
    }

    /**
     * Returns the count of root nodes.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The count of root nodes
     * @access protected
     */
    protected function getNodes()
    {
        return $this->_nodes;
    }

    /**
     * Sets the count of root nodes for request.
     *
     * @param integer $countOfRootNodes The count of root nodes
     *
     * @example if request is /foo/bar/1234 and the root node count
     *          is 2 then all operations will use /foo and /bar as
     *          root.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access protected
     */
    protected function setNodes($countOfRootNodes)
    {
        return ($this->_nodes = $countOfRootNodes);
    }

    /**
     * Runs/executes all operations. Should be overwritten by
     * child on demand.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Base_Presenter
     * @access protected
     */
    protected function run()
    {
        // runs all the stuff required to setup the API service
        return $this;
    }
}
