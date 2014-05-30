<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Registry
 *
 * Registry.php - Registry of the DoozR framework.
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
 * this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 * - All advertising materials mentioning features or use of this software
 * must display the following acknowledgement: This product includes software
 * developed by Benjamin Carl and other contributors.
 * - Neither the name Benjamin Carl nor the names of other contributors
 * may be used to endorse or promote products derived from this
 * software without specific prior written permission.
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
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Class/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Registry/Interface.php';

/**
 * DoozR - Registry
 *
 * Registry of the DoozR framework.
 *
 * @category   DoozR
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Registry
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Registry extends DoozR_Base_Class_Singleton implements
    DoozR_Registry_Interface,
    ArrayAccess,
    Iterator,
    Countable
{
    /**
     * To be more flexible we use an array for storing properties
     * which are passed via __set and set()
     * key = property-name
     *
     * @var array
     * @access protected
     * @static
     */
    protected static $_lookup = array();

    /**
     * To be more flexible for a reverse lookup
     * key = index (numeric)
     *
     * @var array
     * @access protected
     * @static
     */
    protected static $_reverseLookup = array();

    /**
     * Lookup matrix for implementation of ArrayAccess
     * Those lookup matrix is used to retrieve the relation
     * between an identifier and a numeric index
     *
     * @var array
     * @access protected
     * @static
     */
    protected static $_references = array();

    /**
     * The position of the iterator for iterating
     * elements.
     *
     * @var integer
     * @access protected
     * @static
     */
    protected static $position = 0;

    /**
     * The count of elements precalculated for countable
     * interface.
     *
     * @var integer
     * @access protected
     * @static
     */
    protected static $count = 0;


    /**
     * This method stores an element in the registry under the
     * passed key.
     *
     * @param string $variable   The variable (class, object) to store
     * @param string $identifier The identifier for the stored object, class ...
     *                           If not passed a UUID is calculated and returned
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The identifier for reading the stored variable
     * @access public
     * @static
     */
    public static function set(&$variable, $identifier = null)
    {
        // generate identifier if not passed
        if ($identifier === null) {
            $identifier = sha1(serialize($variable));
        }

        // store the variable as reference
        self::$_references[]            = $variable;
        $index                    = count(self::$_references)-1;
        self::$_lookup[$identifier]     = $index;
        self::$_reverseLookup[$index]   = $identifier;

        // store count of elements
        self::$count = $index+1;

        // return identifier for outer use
        return $identifier;
    }

    /**
     * This method returns a previously stored element from the registry
     *
     * @param string $identifier The identifier of the stored object, class ...
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The stored variable if exist
     * @access public
     * @static
     */
    public static function get($identifier = null)
    {
        $result = null;

        if ($identifier === null) {
            $result = self::$_lookup;

        } else {
            if (isset(self::$_lookup[$identifier])) {
                $result = self::$_references[self::$_lookup[$identifier]];

            } else {
                // simulate PHP's behavior by using custom error
                $message = 'Undefined property: '.__CLASS__.'::'.$identifier;
                $type    = E_USER_NOTICE;
                trigger_error($message, $type);
            }
        }

        return $result;
    }

    /**
     * This method is a shortcut wrapper to set()
     *
     * @param string $identifier The identifier of the property
     * @param mixed  $value      The value to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function __set($identifier, $value)
    {
        return self::set($value, $identifier);
    }

    /**
     * This method is a shortcut wrapper to get()
     *
     * @param string $identifier The identifier of the property
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The value of the property if exist
     * @access public
     */
    public function __get($identifier)
    {
        return self::get($identifier);
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Fulfill ArrayAccess
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the TRUE if the passed offset exists otherwise FALSE
     *
     * @param mixed $offset The offset to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function offsetExists($offset)
    {
        if (!is_int($offset)) {
            $offset = array_search($offset, self::$_reverseLookup);
        }

        return (isset(self::$_references[$offset]));
    }

    /**
     * Returns the value for the passed offset
     *
     * @param mixed $offset The offset to return value for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function offsetGet($offset)
    {
        if (!is_int($offset)) {
            $offset = array_search($offset, self::$_reverseLookup);
        }

        return self::$_references[$offset];
    }

    /**
     * Sets the value for the passed offset
     *
     * @param integer $offset The offset to set value for
     * @param mixed   $value  The value to write
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function offsetSet($offset, $value)
    {
        if (!is_int($offset) && $exist = array_search($offset, self::$_reverseLookup)) {
            $offset = $exist;
        }

        self::$_references[$offset] = $value;
    }

    /**
     * Unsets an offset
     *
     * @param mixed $offset The offset to unset
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function offsetUnset($offset)
    {
        $identifier = self::$_reverseLookup[$offset];
        unset(self::$_lookup[$identifier]);
        unset(self::$_reverseLookup[$identifier]);
        unset(self::$_references[$identifier]);
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Fulfill Iterator
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Rewinds the position to 0
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function rewind()
    {
        self::$position = 0;
    }

    /**
     * Checks if current position is still valid
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function valid()
    {
        return self::$position < count(self::$_references);
    }

    /**
     * Returns the key for the current position
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function key()
    {
        return self::$position;
    }

    /**
     * Returns the current element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function current()
    {
        return self::$_references[self::$position];
    }

    /**
     * Goes to next element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function next()
    {
        self::$position++;
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Fulfill Countable
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the count of elements in registry
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function count()
    {
        return self::$count;
    }
}
