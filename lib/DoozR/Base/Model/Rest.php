<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Base - Model - Rest
 *
 * Rest.php - Base class for model-layers from MV(C|P) with REST support
 *
 * PHP versions 5.4
 *
 * LICENSE:
 * DoozR - The lightweight PHP-Framework for high-performance websites
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
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Model
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Http.php';
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Model.php';

/**
 * DoozR - Base Presenter
 *
 * Base Presenter of the DoozR Framework.
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Model
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Base_Model_Rest extends DoozR_Base_Model
{
    /**
     * Permission level for: Everyone is allowed to access no matter of level, id, role, ...
     * This is required for fetching legal website content for blog ... or logging in!
     *
     * @var int
     * @access public
     * @const
     */
    const PERMISSION_OVERRIDE_ALLOW_ALL = 1;

    /**
     * Status of processing (HTTP-Response code)
     *
     * @var int
     * @access protected
     */
    protected $status = DoozR_Http::STATUS_OK;

    /**
     * The result of the current operation
     *
     * @var bool
     * @access protected
     */
    protected $result = false;

    /**
     * The errors occurred while processing
     *
     * @var array
     * @access protected
     */
    protected $error = array();


    /**
     * Setter for error.
     *
     * @param array $error The collection of error to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setError(array $error)
    {
        $this->error = $error;
    }

    /**
     * Adds an error to the collection of errors.
     *
     * @param string $error The error to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function addError($error)
    {
        $this->error[] = $error;
    }

    /**
     * Removes an error from the collection of errors.
     *
     * @param string $error The error to remove
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function removeError($error)
    {
        $this->setError(
            array_diff($this->getError(), array($error))
        );
    }

    /**
     * Getter for error
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The collection of error
     * @access protected
     */
    protected function getError()
    {
        return $this->error;
    }

    /**
     * Setter for result
     *
     * @param bool $result The result to set TRUE or FALSE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * Setter for result
     *
     * @param bool $result The result to set TRUE or FALSE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function result($result)
    {
        $this->setResult($result);
        return $this;
    }

    /**
     * Getter for result
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean The result
     * @access protected
     */
    protected function getResult()
    {
        return $this->result;
    }

    /**
     * Setter for status
     *
     * @param int $status The HTTP Status of current operation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Setter for status
     *
     * @param int $status The HTTP Status of current operation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function status($status)
    {
        $this->setStatus($status);
        return $this;
    }

    /**
     * Getter for status
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The HTTP Status of current operation
     * @access protected
     */
    protected function getStatus()
    {
        return $this->status;
    }

    /**
     * Authorizes an consumer ACL service object against an provider ACL service
     * object to check if resource is allowed for current consumer...
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if authorized, otherwise FALSE
     * @access protected
     * @throws DoozR_Base_Model_Rest_Exception
     */
    protected function authorize(DoozR_Acl_Service $aclConsumer, DoozR_Acl_Service $aclProvider)
    {
        // Check if login is required and if - if user is logged in ...
        if ($aclProvider->isLoginRequired() === true && $aclConsumer->isLoggedIn() === false) {
            throw new DoozR_Base_Model_Rest_Exception(
                'Authorization required.',
                403
            );

        } elseif ($aclConsumer->isAllowed($aclProvider, DoozR_Acl_Service::ACTION_CREATE) === false) {
            // Not enough rights ...
            throw new DoozR_Base_Model_Rest_Exception(
                'Authorization required.',
                401
            );

        } else {
            $status = true;
        }

        return $status;
    }

    /**
     * Returns the operation for passed in HTTP-verb.
     *
     * @param string $verb The HTTP-verb to return operation for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The operation for passed in verb
     * @access protected
     */
    protected function verbToOperation($verb)
    {
        switch (strtoupper($verb))
        {
            case DoozR_Http::REQUEST_METHOD_POST:
                $operation = 'create';
                break;

            case DoozR_Http::REQUEST_METHOD_GET:
                $operation = 'read';
                break;

            case DoozR_Http::REQUEST_METHOD_PUT:
                $operation = 'update';
                break;

            case DoozR_Http::REQUEST_METHOD_DELETE:
                $operation = 'delete';
                break;

            case DoozR_Http::REQUEST_METHOD_OPTIONS:
                $operation = 'options';
                break;

            case DoozR_Http::REQUEST_METHOD_HEAD:
                $operation = 'meta';
                break;

            default:
                $operation = 'read';
                break;
        }

        return $operation;
    }

    /**
     * Merges arguments retrieved from route with arguments
     * retrieved normally with request.
     *
     * @param array                   $routeArguments   The arguments from route
     * @param DoozR_Request_Arguments $requestArguments The argument from request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The resulting merged arguments array
     * @access protected
     */
    protected function mergeArguments($routeArguments, $requestArguments)
    {
        $input  = array($routeArguments, $requestArguments);
        $output = array();

        foreach ($input as $keyValueStore) {

            foreach ($keyValueStore as $key => $value) {
                $output[$this->escape($key)] = strval($value);
            }
        }

        return $output;
    }

    /**
     * Converts a passed in HTTP-verb and a route to a Model-Method-Name which
     * can be called to process the data.
     *
     * @param string $verb  The HTTP-verb of the request PUT, POST, GET, DELETE ...
     * @param array  $route The route of the current request as array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The resulting method name
     * @access protected
     */
    protected function getMethodByVerbAndRoute($verb, array $route)
    {
        $method = $this->verbToOperation($verb);

        foreach ($route as $node) {
            $method .= ucfirst($this->escape($node));
        }

        return $method;
    }

    /**
     * __data() is the generic __data proxy and is called on each access via getData()
     *
     * @param DoozR_Base_State_Interface       $state         The request state object
     * @param DoozR_Base_Presenter_Rest_Config $configuration The configuration
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     * @throws DoozR_Base_Model_Rest_Exception
     * @throws DoozR_Exception
     */
    protected function __data(DoozR_Base_State_Interface $state, DoozR_Base_Presenter_Rest_Config $configuration)
    {
        // Store state of this model
        /* @var $state DoozR_Request_State */
        $this->setStateObject($state);

        // Extract additional arguments from route
        $model = $this;

        /* @var $model DoozR_Base_Model */
        $resource = $this->getStateObject()
            ->get($configuration->getRootNode() . $configuration->getRoute(), function() use ($model) {
                $result = array();
                foreach (func_get_args() as $argument) {
                    $result[] = $model->escape($argument);
                };

                return $result;
            });

        // Build a key => value store from already extracted identifiers and the recently extracted resource array
        $routeArguments = array_combine($configuration->getIds(), $resource);

        // Get arguments passed with this request
        $requestArguments = $this->getStateObject()->getArguments();

        // Get the identifier of the field/argument containing the real Id (uid) of the resource
        $id = $configuration->getId();
        if ($id !== null) {
            // Check for passed Id for all follow up operations on this resource
            if (array_key_exists('{{' . $id . '}}', $routeArguments)) {
                $id = $routeArguments['{{' . $id . '}}'];

            } elseif (isset($requestArguments->{$id})) {
                $id = $requestArguments->{$id};

            }
        }

        // Combine arguments from "route" with arguments from "request"
        $arguments = $this->mergeArguments($routeArguments, $requestArguments);

        // Check for submitted DOOZR_REQUEST_BODY and extract the structure so we can access the arguments
        if (isset($arguments['DOOZR_REQUEST_BODY']) === true && is_json($arguments['DOOZR_REQUEST_BODY'])) {
            $arguments = object_to_array(json_decode($arguments['DOOZR_REQUEST_BODY']));
        }

        $data = null;

        // Dispatch to resource manager if one is defined...
        $method = $this->getMethodByVerbAndRoute($this->getStateObject()->getMethod(), $configuration->getRealRoute());
        if (is_callable(array($this, $method))) {
            $data = $this->{$method}( $configuration, $arguments, getallheaders(), $id);

        } else {
            // Inform developer about a not resolvable route endpoint and show him/her the methods available as hint!
            $methodsDefined = get_class_methods(__CLASS__);
            throw new DoozR_Base_Model_Rest_Exception(
                'Could not call "' . $method . '" operation method in model "' . __CLASS__ .
                '". Currently defined methods of the model [unordered] are: ' . var_export($methodsDefined, true)
            );
        }

        // So we just need to set our new fresh (or updated) data internally. "protected $data" is returned by
        // getData() to caller :) If the caller follows our strict call api
        $this->setData($data);
    }
}
