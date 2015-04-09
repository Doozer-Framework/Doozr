<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* DoozR - Base - State
*
* State.php - Base state class for inheritance.
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
* @subpackage DoozR_Base_State
* @author     Benjamin Carl <opensource@clickalicious.de>
* @copyright  2005 - 2015 Benjamin Carl
* @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
* @version    Git: $Id$
* @link       http://clickalicious.github.com/DoozR/
*/

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Class.php';

/**
 * DoozR - Base - State
 *
 * Base state class for inheritance.
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_State
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
abstract class DoozR_Base_State extends DoozR_Base_Class
{
    /**
     * The runtimeEnvironment DoozR runs ins
     *
     * @var string
     * @access protected
     */
    protected $runtimeEnvironment;

    /**
     * History to trace changes in flow.
     *
     * @var array
     * @access protected
     */
    protected $history = array();

    /**
     * The request arguments
     *
     * @var array
     * @access protected
     */
    protected $arguments = array();

    /**
     * The protocol
     *
     * @var string
     * @access protected
     */
    protected $protocol;


    /**
     * Setter for protocol.
     *
     * @param string $protocol The protocol used
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setProtocol($protocol)
    {
        $this->addHistory(__METHOD__, func_get_args());
        $this->protocol = $protocol;
    }

    /**
     * Setter for protocol.
     *
     * @param string $protocol The protocol used
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Request_State Instance for chaining
     * @access public
     */
    public function protocol($protocol)
    {
        $this->setProtocol($protocol);
        return $this;
    }

    /**
     * Getter for protocol.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The protocol
     * @access public
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Setter for runtimeEnvironment.
     *
     * @param string $runtimeEnvironment The runtimeEnvironment DoozR is running in (WEB, CLI, CLI-SERVER)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setRuntimeEnvironment($runtimeEnvironment)
    {
        $this->addHistory(__METHOD__, func_get_args());
        $this->runtimeEnvironment = $runtimeEnvironment;
    }

    /**
     * Setter for runtimeEnvironment.
     *
     * @param string $runtimeEnvironment The runtimeEnvironment DoozR is running in (WEB, CLI, CLI-SERVER)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Request_State Instance for chaining
     * @access public
     */
    public function runtimeEnvironment($runtimeEnvironment)
    {
        $this->setRuntimeEnvironment($runtimeEnvironment);
        return $this;
    }

    /**
     * Getter for runtimeEnvironment.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The runtimeEnvironment DoozR is running in (WEB, CLI, CLI-SERVER)
     * @access public
     */
    public function getRuntimeEnvironment()
    {
        return $this->runtimeEnvironment;
    }

    /**
     * Adds a history entry to collection.
     *
     * @param string $method    The methods name
     * @param array  $arguments The methods argument
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Request_State Instance for chaining
     * @access protected
     */
    protected function addHistory($method, $arguments)
    {
        if (!isset($this->history[$method])) {
            $this->history[$method] = array();
        }

        $this->history[$method][] = $arguments;

        return $this;
    }

    /**
     * Returns the history collection.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The collection of history entries
     * @access public
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * Returns the instance as array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The properties of this instance as array
     * @access public
     */
    public function unwrap()
    {
        return get_object_vars($this);
    }
}
