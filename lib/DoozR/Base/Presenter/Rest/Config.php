<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Base - Presenter - Rest - Config
 *
 * Config.php - Config class for Rest API
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
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
 * @subpackage DoozR_Base_Presenter
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Request/Web.php';

/**
 * DoozR - Base - Presenter - Rest - Config
 *
 * Config class for Rest API
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Presenter
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Base_Presenter_Rest_Config
{
    /**
     * The Id (unique id) of a recordset/dataset
     * this instance is related to (e.g. for
     * PUT, DELETE, HEAD, OPTONS, GET ...)
     *
     * @var string
     * @access protected
     */
    protected $id;

    /**
     * Instance of ACL service to manage the ACL for this resource
     *
     * @var DoozR_Acl_Service
     * @access protected
     */
    protected $acl;

    /**
     * Count of nodes for this route
     *
     * @var int
     * @access protected
     */
    protected $nodes = 0;

    /**
     * Request methods allowed
     *
     * @var array
     * @access protected
     */
    protected $allow = array(DoozR_Request_Web::METHOD_GET);

    /**
     * The required input arguments for this route
     *
     * @var array
     * @access protected
     */
    protected $required = array();

    /**
     * The URL active
     *
     * @var string
     * @access protected
     */
    protected $url;

    /**
     * The Ids extracted from route
     *
     * @var array
     * @access protected
     */
    protected $ids = array();

    /**
     * The route of this config object
     *
     * @var string
     * @access protected
     */
    protected $route;

    /**
     * The real route of this config object
     *
     * @var array
     * @access protected
     */
    protected $realRoute;

    /**
     * What is the root node of the API?
     *
     * @var string
     * @access protected
     */
    protected $rootNode;

    /**
     * Login required status for this route config.
     *
     * @var bool
     * @access protected
     */
    protected $login = false;

    protected $override = true;


    public function __construct(DoozR_Acl_Service $acl)
    {
        $this->setAcl($acl);
    }


    public function setLogin($status)
    {
        $this->login = $status;
        $this->getAcl()->setLoginRequired($status);
    }

    public function login($status)
    {
        $this->setLogin($status);
        return $this;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setOverride($status)
    {
        $this->override = $status;
    }

    public function override($status)
    {
        $this->setOverride($status);
        return $this;
    }

    public function getOverride()
    {
        return $this->override;
    }



    /**
     * Setter for id
     *
     * @param string $id The id to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Chaining setter for id
     *
     * @param string $id The id to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Base_Presenter_Rest_Config This instance for chaining calls
     * @access public
     */
    public function id($id)
    {
        $this->setId($id);
        return $this;
    }

    /**
     * Getter for id
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The Id
     * @access public
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setter for nodes
     *
     * @param int $nodes The count of nodes for this config
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setNodes($nodes)
    {
        $this->nodes = $nodes;
    }

    /**
     * Chaining setter for nodes
     *
     * @param int $nodes The count of nodes for this config
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Base_Presenter_Rest_Config This instance for chaining calls
     * @access public
     */
    public function nodes($nodes)
    {
        $this->setNodes($nodes);
        return $this;
    }

    /**
     * Getter for nodes
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer Count of nodes
     * @access public
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * Setter for allowed HTTP verbs
     *
     * @param string|array $allow A single HTTP verb or a collection of multiple entries
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setAllow($allow)
    {
        if (!is_array($allow)) {
            $allow = array($allow);
        }

        // Update ACL properties
        foreach ($allow as $verb) {
            $this->getAcl()->addPermission(
                $this->verbToOperation($verb)
            );
        }

        $this->allow = $allow;
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
     * Chaining setter for allow
     *
     * @param string|array $allow A single HTTP verb or a collection of multiple entries
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Base_Presenter_Rest_Config This instance for chaining calls
     * @access public
     */
    public function allow($allow)
    {
        $this->setAllow($allow);
        return $this;
    }

    /**
     * Getter for allowed HTTP verbs
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Allowed HTTP verbs
     * @access public
     */
    public function getAllow()
    {
        return $this->allow;
    }

    /**
     * Checks if a passed verb is allowed for this route object
     *
     * @param string $verb The verb to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return booelan TRUE if verb is allowed, otherwise FALSE
     * @access public
     */
    public function isAllowed($verb)
    {
        return in_array($verb, $this->getAllow());
    }

    /**
     * Setter for required input arguments
     *
     * @param array $required The required values
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setRequired(array $required)
    {
        $this->required = $required;
    }

    /**
     * Chaining setter for required
     *
     * @param array $required The required values
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Base_Presenter_Rest_Config This instance for chaining calls
     * @access public
     */
    public function required($required)
    {
        $this->setRequired($required);
        return $this;
    }

    /**
     * Getter for required input arguments
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Required input arguments
     * @access public
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Setter for URL
     *
     * @param string $url The URL of this config object
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Chaining setter for URL
     *
     * @param string $url The URL of this config object
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Base_Presenter_Rest_Config This instance for chaining calls
     * @access public
     */
    public function url($url)
    {
        $this->setUrl($url);
        return $this;
    }

    /**
     * Getter for URL
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer Count of nodes
     * @access public
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Setter for Ids
     *
     * @param array $ids A collection of extracted Ids for matching
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setIds($ids)
    {
        $this->ids = $ids;
    }

    /**
     * Chaining setter for Ids
     *
     * @param array $ids A collection of extracted Ids for matching
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Base_Presenter_Rest_Config This instance for chaining calls
     * @access public
     */
    public function ids($ids)
    {
        $this->setIds($ids);
        return $this;
    }

    /**
     * Adds an Id
     *
     * @param string $id An Id to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function addId($id)
    {
        $this->ids[] = $id;
    }

    /**
     * Getter for extracted Ids
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array extracted Ids
     * @access public
     */
    public function getIds()
    {
        return $this->ids;
    }

    /**
     * Setter for route
     *
     * @param string $route The route
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * Chaining setter for route
     *
     * @param string $route The route
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Base_Presenter_Rest_Config This instance for chaining calls
     * @access public
     */
    public function route($route)
    {
        $this->setRoute($route);
        return $this;
    }

    /**
     * Getter for route
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The route of this config
     * @access public
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Setter for real route
     *
     * @param array $realRoute The real route
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setRealRoute($realRoute)
    {
        $this->realRoute = $realRoute;
    }

    /**
     * Chaining setter for route
     *
     * @param array $realRoute The real route
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Base_Presenter_Rest_Config This instance for chaining calls
     * @access public
     */
    public function realRoute($realRoute)
    {
        $this->setRealRoute($realRoute);
        return $this;
    }

    /**
     * Getter for real route
     *
     * @param bool $asString TRUE to return route as string, otherwise FALSE to return array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|array The active route as array or as string
     * @access public
     */
    public function getRealRoute($asString = false)
    {
        return ($asString === true) ? implode( '/', $this->realRoute ) : $this->realRoute;
    }

    /**
     * Setter for rootNode
     *
     * @param string $rootNode The rootNode
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setRootNode($rootNode)
    {
        $this->rootNode = $rootNode;
    }

    /**
     * Chaining setter for rootNode
     *
     * @param string $rootNode The rootNode
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Base_Presenter_Rest_Config This instance for chaining calls
     * @access public
     */
    public function rootNode($rootNode)
    {
        $this->setRootNode($rootNode);
        return $this;
    }

    /**
     * Getter for rootNode
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The active rootNode as string
     * @access public
     */
    public function getRootNode()
    {
        return $this->rootNode;
    }

    /**
     * Setter for acl
     *
     * @param DoozR_Acl_Service $acl The acl instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setAcl(DoozR_Acl_Service $acl)
    {
        $this->acl = $acl;
    }

    /**
     * Chaining setter for acl
     *
     * @param DoozR_Acl_Service $acl The acl instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Base_Presenter_Rest_Config This instance for chaining calls
     * @access public
     */
    public function acl(DoozR_Acl_Service $acl)
    {
        $this->setAcl($acl);
        return $this;
    }

    /**
     * Getter for acl
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Acl_Service Acl service instance
     * @access public
     */
    public function getAcl()
    {
        return $this->acl;
    }
}
