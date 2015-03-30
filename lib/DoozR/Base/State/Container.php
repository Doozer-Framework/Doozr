<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Base - State - Container
 *
 * Container.php - Container for handling storage of state instance.
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
 * DoozR - Base - State - Container
 *
 * Container for handling storage of state instance.
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
class DoozR_Base_State_Container extends DoozR_Base_Class
{
    /**
     * The state object instance
     *
     * @var DoozR_Base_State
     * @access protected
     */
    protected $stateObject;


    /**
     * Constructor.
     *
     * @param DoozR_Base_State_Interface $stateObject The state object instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Base_State_Container
     * @access public
     */
    public function __construct(DoozR_Base_State_Interface $stateObject = null)
    {
        if ($stateObject !== null) {
            $this->setStateObject($stateObject);
        }
    }

    /**
     * Setter for state object.
     *
     * @param DoozR_Base_State_Interface $stateObject The state object instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    protected function setStateObject(DoozR_Base_State_Interface $stateObject)
    {
        $this->stateObject = $stateObject;
    }

    /**
     * Setter for state object.
     *
     * @param DoozR_Base_State_Interface $stateObject The state object instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Base_State_Container
     * @access public
     */
    protected function stateObject(DoozR_Base_State_Interface $stateObject)
    {
        $this->setStateObject($stateObject);
        return $this;
    }

    /**
     * Getter for state object.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Base_State The state object instance
     * @access public
     */
    protected function getStateObject()
    {
        return $this->stateObject;
    }

    /**
     * Exports state.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Base_State The state object instance
     * @access public
     */
    public function export()
    {
        return $this->getStateObject();
    }

    /**
     * Imports state.
     *
     * @param DoozR_Base_State_Interface $stateObject
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function import(DoozR_Base_State_Interface $stateObject)
    {
        $this->setStateObject($stateObject);
    }
}
