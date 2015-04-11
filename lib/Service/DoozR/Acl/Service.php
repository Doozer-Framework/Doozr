<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Acl - Service
 *
 * Service.php - Service for ACL
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
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Acl
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Service/Multiple.php';
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Service/Interface.php';

use DoozR\Loader\Serviceloader\Annotation\Inject;

/**
 * DoozR - Acl - Service
 *
 * Service for ACL
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Acl
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @Inject(
 *     class="DoozR_Registry",
 *     identifier="__construct",
 *     type="constructor",
 *     position=1
 * )
 */
class DoozR_Acl_Service extends DoozR_Base_Service_Multiple implements DoozR_Base_Service_Interface
{
    /**
     * The actions supported by this ACL
     *
     * @var array
     * @access protected
     */
    protected $actions = array(
        self::ACTION_CREATE,
        self::ACTION_READ,
        self::ACTION_UPDATE,
        self::ACTION_DELETE
    );

    /**
     * Some state to hold information if user is logged in
     *
     * @var bool
     * @access protected
     */
    protected $loggedin = false;

    /**
     * Tells us if a login is required for the resource this ACL was bound to
     *
     * @var bool
     * @access protected
     */
    protected $loginRequired = false;

    /**
     * The type of this ACL
     *
     * @var int
     * @access protected
     */
    protected $type;

    /**
     * The current FIXED user permissions
     *
     * @var int
     * @access protected
     */
    protected $permissions;

    /**
     * Action create
     *
     * @var string
     * @access public
     * @const
     */
    const ACTION_CREATE = 'create';

    /**
     * Action read
     *
     * @var string
     * @access public
     * @const
     */
    const ACTION_READ = 'read';

    /**
     * Action update
     *
     * @var string
     * @access public
     * @const
     */
    const ACTION_UPDATE = 'update';

    /**
     * Action delete
     *
     * @var string
     * @access public
     * @const
     */
    const ACTION_DELETE = 'delete';

    /**
     * Type for provider (e.g. a REST endpoint or something like this)
     *
     * @var int
     * @access public
     * @const
     */
    const TYPE_PROVIDER = 1;

    /**
     * Type for consumer (e.g. an User or a Group or a Robot)
     *
     * @var int
     * @access public
     * @const
     */
    const TYPE_CONSUMER = 2;


    /**
     * Constructor.
     *
     * @param int $type        The type of this ACL (Consumer | Provider)
     * @param int $permissions The permissions code that will be tested against. It is optional and not needed
     *                             when only using the class to generate a new permissions code.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __tearup($type = self::TYPE_PROVIDER, $permissions = 0)
    {
        $this->setType($type);
        $this->setPermissions($permissions);
    }

    /**
     * Setter for actions.
     *
     * @param array $actions The supported/possible/available actions to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setActions(array $actions = array())
    {
        $this->actions = $actions;
    }

    /**
     * Setter for actions.
     *
     * @param array $actions The supported/possible/available actions to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Acl_Service The current instance for chaining
     * @access public
     */
    public function actions(array $actions = array())
    {
        $this->setActions($actions);
        return $this;
    }

    /**
     * Getter for actions.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The actions
     * @access public
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Setter for type.
     *
     * @param int $type The type of the ACL
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Setter for type.
     *
     * @param int $type The type of the ACL
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Acl_Service The current instance for chaining
     * @access public
     */
    public function type($type = self::TYPE_PROVIDER)
    {
        $this->setType($type);
        return $this;
    }

    /**
     * Getter for type.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The type
     * @access public
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Setter for permissions.
     *
     * @param int $permissions The permissions to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setPermissions($permissions = 0)
    {
        $this->permissions = intval($permissions);
    }

    /**
     * Setter for permissions.
     *
     * @param int $permissions The permissions to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Acl_Service The current instance for chaining
     * @access public
     */
    public function permissions($permissions = 0)
    {
        $this->setPermissions($permissions);
        return $this;
    }

    /**
     * Getter for permissions.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The current permissions as integer representation
     * @access public
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Checks whether the provider can be accessed by this consumer.
     *
     * @param DoozR_Acl_Service $acl    The provider ACL
     * @param string            $action The action to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if is allowed, otherwise FALSE
     * @access public
     * @throws DoozR_Exception_Service
     */
    public function isAllowed(DoozR_Acl_Service $acl, $action)
    {
        if ($this->getType() === self::TYPE_CONSUMER && $acl->getType() === self::TYPE_PROVIDER) {
            return ($acl->hasPermission($action) && $acl->grant($this->getPermissions(), $action));

        } else {
            throw new DoozR_Exception_Service(
                'Type mismatch! Only Consumer ca be allowed to access Provider.'
            );
        }
    }

    /**
     * Checks whether or not the action exists in current configuration.
     *
     * @param string $action The action to be checked
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if exist, otherwise FALSE
     * @access public
     */
    public function hasAction($action)
    {
        return in_array($action, $this->actions);
    }

    /**
     * Checks whether or not the permission as string is allowed by current setup.
     *
     * @param string $action The action to be tested for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if allowed (has permission), otherwise FALSE
     * @access public
     * @throws DoozR_Exception_Service
     */
    public function hasPermission($action)
    {
        if ($this->hasAction($action) === true) {
            $result = $this->grant($this->getPermissions(), $action);

        } else {
            throw new DoozR_Exception_Service('Action "' . $action . '" does not exist!');
            $result = false;
        }

        return $result;
    }

    /**
     * Adds an action to the bitmask property when generating a new permissions code.
     *
     * @param string $action The action that should be allowed.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Acl_Service The current instance for chaining
     * @access public
     */
    public function addPermission($action)
    {
        // Check if permission is already added
        if ($this->hasPermission($action) === false) {
            //
            $this->setPermissions(
                $this->getPermissions() + $this->getKey($action)
            );
        }

        // Chaining API
        return $this;
    }

    /**
     * Removes an action to the bitmask property when generating a new permissions code.
     *
     * @param string $action The action that should not be allowed.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Acl_Service The current instance for chaining
     * @access public
     */
    public function removePermission($action)
    {
        // Permission must be set before we can remove it ...
        if ($this->grant($this->getPermissions(), $action) === true) {
            $this->setPermissions(
               $this->getPermissions() - $this->getKey($action)
            );
        }

        // Chaining API
        return $this;
    }

    /**
     * Checks if the provided action is allowed when using the provided permissions code.
     *
     * @param int $permissions A permissions code to check if access could be granted
     * @param string  $action      The action to test check if granted
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if the action should be allowed, otherwise FALSE
     * @access protected
     */
    protected function grant($permissions, $action)
    {
        $key = $this->getKey($action);
        $grant = (($permissions & $key) == $key);

        return $grant;
    }

    /**
     * Gets the value based on an actions position in the array of possible actions.
     *
     * @param string $action The action to look up
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The position value
     * @access protected
     */
    protected function getKey($action)
    {
        return pow(2, array_search($action, $this->actions));
    }



    public function isLoggedin()
    {
        return $this->getLoggedin();
    }

    public function setLoggedin($status = false)
    {
        $this->loggedin = $status;
    }

    public function getLoggedin()
    {
        $result = false;

        if ($this->loggedin ===true) {
            $result = true;
        }

        return $result;
    }



    public function isLoginRequired()
    {
        return $this->getLoginRequired();
    }

    public function setLoginRequired($status)
    {
        $this->loginRequired = $status;
    }

    public function getLoginRequired()
    {
        return $this->loginRequired;
    }
}
