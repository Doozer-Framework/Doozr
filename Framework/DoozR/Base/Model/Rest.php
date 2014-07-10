<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Base - Model - Rest
 *
 * Rest.php - Base class for model-layers from MV(C|P) with REST support
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
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Base_Model_Rest extends DoozR_Base_Model
{
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
     * Build a package for response which is basically an array structure which
     * is very common when converted to JSON.
     *
     * @param            $success TRUE if the response package is a success response, otherwise FALSE
     * @param mixed|null $data    The data to return to client (RAW response)
     * @param array      $error   The error(s) array if success is false
     *
     * @example array(
     *              success => true|false
     *              result  => The data for the client (We can name it the RAW-response)
     *              error   => The error field containing one or more array entries with errors
     *                         (only set if success === false)
     *          );
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The response package
     * @access protected
     */
    protected function packageResponse($success, $data = null, array $error = array())
    {
        if ($success === true) {
            $response = array(
                'success' => $success,
                'result'  => $data,
            );
        } else {
            $response = array(
                'success' => $success,
                'result'  => $data,
                'error'   => $error
            );
        }

        return $response;
    }

    /**
     * __data() is the generic __data proxy and is called on each access via getData()
     *
     * @param DoozR_Request_Api                $request The default request object for APIs
     * @param DoozR_Base_Presenter_Rest_Config $config  The configuration
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     * @throws DoozR_Base_Model_Rest_Exception
     * @throws DoozR_Exception
     */
    protected function __data(DoozR_Request_Api $request, DoozR_Base_Presenter_Rest_Config $config)
    {
        // Extract additional arguments from route
        $model = $this;

        /* @var $model DoozR_Base_Model */
        $resource = $request->get($config->getRootNode() . $config->getRoute(), function() use ($model) {
            $result = array();
            foreach (func_get_args() as $argument) {
                $result[] = $model->escape($argument);
            };

            return $result;
        });

        // Build a key => value store from already extracted identifiers and the recently extracted resource array
        $routeArguments = array_combine($config->getIds(), $resource);

        // Get arguments passed with this request
        $requestArguments = $request->getArguments();

        // Get the identifier of the field/argument containing the real Id (uid) of the resource
        $id = $config->getId();
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

        $data = null;

        // Dispatch to resource manager if one is defined...
        $method = $this->getMethodByVerbAndRoute($request->getMethod(), $config->getRealRoute());
        if (is_callable(array($this, $method))) {
            $data = $this->{$method}($id, $arguments, $config);

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

    /**
     * Observer notification
     *
     * @param SplSubject $subject The subject which notifies this observer (Presenter)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    protected function __update(SplSubject $subject)
    {
        $this->setData($subject->getData());
    }
}
