<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Registry
 *
 * Registry.php - Registry of the DoozR Framework
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
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Registry
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Class/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Registry/Interface.php';

/**
 * DoozR Registry
 *
 * Registry of the DoozR Framework
 *
 * @category   DoozR
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Registry
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Registry extends DoozR_Base_Class_Singleton implements DoozR_Registry_Interface
{
	/**
	 * holds registry data
	 *
	 * @var array $data
	 * @access protected
	 */
    protected $_data;


    /**
     * adds value to registry
     *
     * This method is intend to add a new value to the registry.
     *
     * @param string $key   The key/identifier of the value
     * @param mixed  $value The value to store/add
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function __set($key, $value)
    {
        $this->_data[$key] = $value;
    }

    /**
     * returns value from registry
     *
     * This method is intend to return a value from the registry.
     *
     * @param string $key The key/identifier of the value
     *
     * @return  mixed|null
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function __get($key)
    {
        if (isset($this->_data[$key])) {
            return $this->_data[$key];
        } else {
            return null;
        }
    }

    /**
     * checks if a key is set
     *
     * This method is intend to check if a key is set.
     *
     * @param string $key The key/identifier to check
     *
     * @return  boolean TRUE if key is set, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function __isset($key)
    {
        return isset($this->_data[$key]);
    }

    /**
     * removes a key and its value from registry
     *
     * This method is intend to remove a key and its value from registry.
     *
     * @param string $key The key/identifier to remove
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */

    public function __unset($key)
    {
        unset($this->_data[$key]);
    }
}

?>
