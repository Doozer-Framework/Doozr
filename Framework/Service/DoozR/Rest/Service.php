<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Rest - Service
 *
 * Rest.php - ...
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
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Rest
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Service/Multiple.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Request/Api.php';

/**
 * DoozR - Rest - Service
 *
 * ...
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Rest
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 * @throws     DoozR_Rest_Service_Exception
 * @DoozRType  Multiple
 * @DiInject   DoozR_Registry:DoozR_Registry identifier:__construct type:constructor position:1
 */
class DoozR_Rest_Service extends DoozR_Base_Service_Multiple
{
    /**
     * Request object to use with DoozR Rest or Soap
     *
     * @var object
     * @access private
     */
    private $_requestObject;

    /**
     * This method is intend to act as constructor.
     *
     * @param array   $request        The original request
     * @param integer $countRootNodes The count of root nodes (e.g. 2 on /Foo/Bar/Demo/Screen/ means
     *                                that /Foo/Bar/ will be taken as root and ripped)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __tearup(array $request = array(), $countRootNodes = 2)
    {
        // if no custom request data/config is passed ...
        if (empty($request)) {
            // ... use defeault
            $request = array(
                'port'   => $_SERVER['SERVER_PORT'],
                'ip'     => gethostbyname($_SERVER['SERVER_NAME']),
                'domain' => $_SERVER['SERVER_NAME'],
                'ssl'    => is_ssl()
            );
        }

        // get hands on request object
        $requestObject = $this->registry->front->getRequest();

        // extract real API request
        // so at this very specific and no longer generic routing way we
        // can be sure that the following works
        $this->_requestObject = new DoozR_Request_Api();

        $this->_requestObject->set(
            array(
                'resource'  => array_slice($request, $countRootNodes),
                'method'    => $requestObject->getMethod(),
                'arguments' => $requestObject->arguments,
                'request'   => $request,
                'url'       => $requestObject->getUrl()
            )
        );

        /*
        $this->_requestObject->resource  = array_slice($request, $countRootNodes);
        $this->_requestObject->method    = $requestObject->getMethod();
        $this->_requestObject->arguments = $requestObject->arguments;
        $this->_requestObject->request   = $request;
        $this->_requestObject->url       = $requestObject->getUrl();
        */
    }

    /**
     * Returns the current request object
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function getRequestObject()
    {
        return $this->_requestObject;
    }

    /**
     * This method is intend to cleanup on class destruct.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __teardown()
    {
        /* */
    }
}
