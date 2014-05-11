<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Api - Presenter
 *
 * Api.php - Api Presenter Apinstration
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
 * @package    DoozR_Api
 * @subpackage DoozR_Api_Presenter
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

/**
 * DoozR - Api - Presenter
 *
 * Api Presenter
 *
 * @category   DoozR
 * @package    DoozR_Api
 * @subpackage DoozR_Api_Presenter
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
final class Presenter_Api extends DoozR_Base_Presenter_Rest implements DoozR_Base_Presenter_Interface
{
    /**
     * This method is the replacement for construct. It is called right on construction of
     * the class-instance. It retrieves all arguments 1:1 as passed to constructor.
     *
     * @param array   $request     The original request
     * @param array   $translation The translation to read the request
     * @param integer $nodes       The
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function __tearup(array $request, array $translation, $nodes = 2)
    {
        // setup allowed verbs and define required fields
        $this
            ->nodes($nodes)
            ->allow(DoozR_Request_Web::METHOD_GET)
            ->required(array('id' => 1234), 'user')
            ->run();
    }

    /**
     * This method is the replacement for construct. It is called right on construction of
     * the class-instance. It retrieves all arguments 1:1 as passed to constructor.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function __teardown()
    {
        /*...*/
    }

    /**
     * This method is intend to demonstrate how data could be automatic
     * be displayed.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean True if successful, otherwise false
     * @access public
     */
    public function Main()
    {
        /**
         * @todo: Implement the logic for validation (type based) for input!
         */
        // retrieve data for context Screen from Model by defined default interface "getData()"
        $data = $this->model->getData(
            $this->getRest()->getRequest()
        );

        // set data here within this instance cause VIEW and MODEL are attached as Observer to this Subject.
        $this->setData($data);

        // the result from operation above
        return true;
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
        // get registry
        $registry = DoozR_Registry::getInstance();

        // get response
        $response = $registry->front->getResponse();

        // get request object (standard notation), object + method
        $requestObject = $this->getRest()->getRequest();
        $object        = strtolower($requestObject->getResource()[0]);
        $method        = strtoupper($requestObject->getMethod());

        // check if verb is allowed
        if ($this->isAllowed($method) === false) {
            $response->sendHttpStatus(405, null, true, strtoupper($method));
            exit;
        }

        // get required fields ...
        $requiredArguments = $this->getRequired($object);

        /**
         * The first Argument in an API is the last part in the URI if
         * not an index action. So we take the nodes which are the root,
         * rip them off from the current URI/URL from request and what
         * stays is the argument(s) for the API ...
         */
        // ... and iterate them to find missing elements
        foreach ($requiredArguments as $requiredArgument) {
            foreach ($requiredArgument as $key => $value) {

                if (
                    // Can the required value be retrieved from GET, POST, ...
                    !isset($requestObject->arguments->{$key}) &&
                    // And if not -> can it be retrieved correctly from route?
                    ($id = $requestObject->get('/Api/x/{{' . $key . '}}', function ($id) { return $id; })) === null
                ) {
                    $message = '';

                    foreach ($requiredArgument as $argument => $value) {
                        $message .= 'Missing required argument: ' . $argument . ' => Value: ' . var_export($value, true);
                    }

                    // send HTTP-Header "Not-Acceptable" for missing argument + message
                    $response->sendHttpStatus(406, null, true, $message);
                    exit;
                }
            }
        }

        // runs all the stuff required to setup the API service
        return $this;
    }
}
