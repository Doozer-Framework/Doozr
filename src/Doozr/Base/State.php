<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* Doozr - Base - State
*
* State.php - Base class for state representations. Based on the Data Transfer Object Pattern:
* @link https://en.wikipedia.org/wiki/Data_transfer_object
*
* PHP versions 5.5
*
* LICENSE:
* Doozr - The lightweight PHP-Framework for high-performance websites
*
* Copyright (c) 2005 - 2016, Benjamin Carl - All rights reserved.
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
*   must display the following acknowledgment: This product includes software
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
* @category   Doozr
* @package    Doozr_Base
* @subpackage Doozr_Base_State
* @author     Benjamin Carl <opensource@clickalicious.de>
* @copyright  2005 - 2016 Benjamin Carl
* @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
* @version    Git: $Id$
* @link       http://clickalicious.github.com/Doozr/
*/

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Class.php';

/**
 * Doozr - Base - State
 *
 * Base class for state representations
 *
 * @category   Doozr
 * @package    Doozr_Base
 * @subpackage Doozr_Base_State
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
abstract class Doozr_Base_State extends Doozr_Base_Class
{
    /**
     * History to trace changes in flow.
     *
     * @var array
     * @access protected
     */
    protected $history = [];

    /**
     * Adds a history entry to collection.
     *
     * @param string $method    The methods name
     * @param array  $arguments The methods argument
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Request_State Instance for chaining
     * @access protected
     */
    protected function addHistory($method, $arguments)
    {
        if (!isset($this->history[$method])) {
            $this->history[$method] = [];
        }

        $this->history[$method][] = $arguments;

        return $this;
    }

    /**
     * Setter for history.
     *
     * @param array $history The history data to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setHistory(array $history)
    {
        $this->history = $history;
    }

    /**
     * Setter for history.
     *
     * @param array $history The history data to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function history(array $history)
    {
        $this->setHistory($history);

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

    /*------------------------------------------------------------------------------------------------------------------
    | IMMUTABLE STATE EXPORT
    +-----------------------------------------------------------------------------------------------------------------*/

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
