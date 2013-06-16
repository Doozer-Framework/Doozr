<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Request Parameter
 *
 * Parameter.php - This class is used as replacement for PHP's Globals. It includes
 * the Iterator- and ArrayAccess-Interface to keep original Globals functionality.
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
 * @package    DoozR_Request
 * @subpackage DoozR_Request_Parameter
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id: 2371f412cc495e00089668f915aaa2bcaaf13bd1 $
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Class.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Request/Value.php';

/**
 * DoozR Request Parameter
 *
 * This class is used as replacement for PHP's Globals. It includes the Iterator-
 * and ArrayAccess-Interface to keep original Globals functionality.
 *
 * @category   DoozR
 * @package    DoozR_Request
 * @subpackage DoozR_Request_Parameter
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id: 2371f412cc495e00089668f915aaa2bcaaf13bd1 $
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Request_Parameter extends DoozR_Base_Class implements Iterator, ArrayAccess
{
    /**
     * holds the parameter(s)
     *
     * @var array
     * @access private
     */
    private $_parameter = array();

    /**
     * holds the original input this can be either $_GET, $_POST,
     * $_COOKIE, $_SESSION, $_REQUEST, $_SERVER ...
     *
     * @var array
     * @access private
     */
    private $_input;

    /**
     * Contains the name of the original global PHP variable
     * (e.g. _GET)
     *
     * @var string
     * @access private
     */
    private $_target;

    /**
     * holds the position of iterator
     *
     * @var integer
     * @access private
     */
    private $_iteratorPosition;

    /**
     * holds the key of iterator
     *
     * @var string
     * @access private
     */
    private $_interatorKey;


    /**
     * constructs the class
     *
     * constructor builds the class
     *
     * @param mixed $global String (name of a global-array) or (global)-Array to parse
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object An instance of this class
     * @access public
     */
    public function __construct($global = null)
    {
        if (is_string($global)) {
            // start processing input
            $this->_processInput($global);
        }

        // default
        return false;
    }

    /**
     * transforms input to object
     *
     * This method is intend to transform the input to an object.
     *
     * @param mixed $global The input (string or array) to transform to object
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _processInput($global)
    {
        // check if parameter is of type string
        if (is_array($global)) {
            // we retrieved the array to parse
            $this->_input = $global;

        } else {
            // store the array we working on as string
            $this->_target = $global;

            // *** IMPORTANT ***
            // we need to reference the global array's like $_GET and $_POST like
            // you see in the following lines (we can reference $_GET in the normal way, but trying to get
            // $_GET referenced at runtime with $$ fails)
            //global $$global;
            //$this->_input = $$global;
            $this->_input = $GLOBALS[$global];
        }

        // transform our global input from array to object (with ArrayAccess-Interface)
        $this->_parameter = $this->_transformToObject($this->_input);
    }

    /*******************************************************************************************************************
     * // BEGIN INTERFACE Iterator METHODS
     ******************************************************************************************************************/

    /**
     * re-sets the iterator position to 0
     *
     * re-sets the iterator position to 0
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function rewind()
    {
        $this->_iteratorPosition = 0;
        $this->_interatorKey = null;
    }

    /**
     * validates the position of the iterator
     *
     * validates the position of the iterator
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean True if valid, otherwise false
     * @access public
     */
    public function valid()
    {
        return ($this->_iteratorPosition < count($this->_parameter));
    }

    /**
     * returns the current position of iterator
     *
     * returns the current position of iterator
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer Current position of the iterator
     * @access public
     */
    public function key()
    {
        $i = 0;
        foreach ($this->_parameter as $key => $value) {
            if ($i == $this->_iteratorPosition) {
                return $key;
            }
            ++$i;
        }
    }

    /**
     * returns the current value
     *
     * returns the current value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The value
     * @access public
     */
    public function current()
    {
        $i = 0;
        foreach ($this->_parameter as $key => $value) {
            if ($i == $this->_iteratorPosition) {
                return $value;
            }
            ++$i;
        }
    }

    /**
     * increase the position of iterator (+1)
     *
     * increase the position of iterator (+1)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function next()
    {
        $this->_iteratorPosition++;
    }

    /*******************************************************************************************************************
     * \\ END INTERFACE Iterator METHODS
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN INTERFACE ArrayAcces METHODS
     ******************************************************************************************************************/

    /**
     * setter/interface implementation for e.g. $_GET['foo'] = 'bar'
     *
     * setter/interface implementation for e.g. $_GET['foo'] = 'bar'
     *
     * @param string $offset The offset (key) to set
     * @param mixed  $value  The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function offsetSet($offset, $value)
    {
        // check if offset already exist
        if (isset($this->_parameter[$offset])) {
            // if exist => patch existing entry
            $this->_parameter[$offset]->set($value);
            $this->_parameter[$offset]->setRaw($value);
        } else {
            // otheriwse => create new entry
            $this->_parameter[$offset] = new Request_Value($value);
        }

        // success
        return true;
    }

    /**
     * getter/interface implementation for isset() e.g. isset($_GET['foo'])
     *
     * getter/interface implementation for isset() e.g. isset($_GET['foo'])
     *
     * @param string $offset The offset (key) to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function offsetExists($offset)
    {
        return isset($this->_parameter[$offset]);
    }

    /**
     * setter/interface implementation for unset() e.g. unset($_GET['foo'])
     *
     * setter/interface implementation for unset() e.g. unset($_GET['foo'])
     *
     * @param string $offset The offset (key) to unset
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function offsetUnset($offset)
    {
        unset($this->_parameter[$offset]);
    }

    /**
     * getter/interface implementation for e.g. echo $_GET['foo']
     *
     * getter/interface implementation for e.g. echo $_GET['foo']
     *
     * @param string $offset The offset (key) to return
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed MIXED value of requested key if set, otherwise NULL
     * @access public
     */
    public function offsetGet($offset)
    {
        return isset($this->_parameter[$offset]) ? $this->_parameter[$offset]->getRaw() : null;
    }

    /*******************************************************************************************************************
     * \\ END INTERFACE ArrayAcces METHODS
     ******************************************************************************************************************/

    /**
     * Transforms values from a given key/value array to objects of DoozR_Request_Value
     *
     * This method is intend to transform values from a given key/value array to
     * objects of DoozR_Request_Value.
     *
     * @param array $input The input to transform to object
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array A transformed array containing values of type DoozR_Request_Value instead of values.
     * @access private
     */
    private function _transformToObject($input)
    {
        // transform only once (!REMARK! do not use typehint due the fact
        // that this method can be called twice and would exit the execution
        // if an invalid (already converted) value is passed!
        if (is_array($input)) {
            // parse input contents
            foreach ($input as $key => $value) {
                // transform each key/value-pair from array to object of type DoozR_Request_Value
                $input[$key] = new DoozR_Request_Value($value);
            }
        }

        // return input with replaced values
        return $input;
    }

    /**
     * shortcut to request-params
     *
     * this is a shortcut to allmost every (public-)method DoozR offers
     *
     * @param string $method    the name of the method called
     * @param array  $parameter the parameter of the method call
     *
     * @return mixed depends on input!
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access magic
     */
    public function __call($method, $parameter)
    {
        if (isset($this->_parameter[$method])) {
            if (isset($parameter) && isset($parameter[0]) && $parameter[0] === true) {
                // return RAW
                return $this->_parameter[$method]->getRaw();
            } else {
                // simply return value
                return $this->_parameter[$method]->get();
            }
        }

        // if not defined ($method = param e.g. $_GET['foo'])
        return null;
    }

    /**
     * __magic hook - An interface to access parameter as property
     *
     * __magic hook - An interface to access parameter as property
     *
     * @param string $propertyName The name of the property requested
     *
     * @return mixed NULL if the parameter isn't defined otherwise the parameter
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access magic
     */
    public function __get($propertyName)
    {
        // check if passed argument is a valid global post/get/... parameter:value
        if (isset($this->_parameter[$propertyName])) {
            // simply return value
            //return $this->_parameter[$propertyName]->get();
            return $this->_parameter[$propertyName];
        }

        /*
        // if parameter (property) not defined
        $trace = debug_backtrace();

        // trigger error
        trigger_error(
            'Undefined property via __get(): '.$propertyName.
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE
        );
        */

        return null;
    }

    /**
     * isset() transformation function for proxying isset() checks on
     * properties of the instance to _parameter[].
     *
     * @param string $propertyName The property to check
     *
     * @return boolean TRUE if property is set, otherwise FALSE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access magic
     */
    public function __isset($propertyName)
    {
        return (isset($this->_parameter[$propertyName]));
    }

    /**
     * returns the value for the requested parameter
     *
     * returns the value for the requested parameter if it exists, otherwise NULL
     *
     * @param string $parameter The parameter to return the value for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The value for the parameter if parameter exist, otherwise NULL
     * @access public
     */
    public function get($parameter = null)
    {
        if (!is_null($parameter)) {
            // check if parameter is defined/set
            if (isset($this->_parameter[$parameter])) {
                // if parameter is defined then we return it
                return $this->_parameter[$parameter];
            }
        }

        // otherwise we return NULL not FALSE!
        return null;
    }

    /**
     * returns all defined parameter (the whole array)
     *
     * this method returns all defined parameters! the whole array of Request_Value('s)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array
     * @access public
     */
    public function getAll()
    {
        return $this->_parameter;
    }

    /**
     * returns the parameter of this class as string
     *
     * This method is intend to return the parameter of this class as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The defined parameter-name and -value
     * @access public
     */
    public function __toString()
    {
        return var_export($this->_parameter, true);
    }

    /**
     * returns the original array
     *
     * This method is intend to return the original input array.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The original input
     * @access public
     */
    public function getArray()
    {
        return $this->_input;
    }

    /**
     * if serialize is called - we need to re-transform the class vars to a $_GET like array.
     * Some other applications e.g. XHProf want the information exactly as such an array.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The original input
     * @access public
     */
    public function __sleep()
    {
        return $this->_input;
    }

    /**
     * Restores the original global
     *
     * This method is intend to restore the original global before
     * the class is destructed. So
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The original restored global
     * @access public
     */
    public function __destruct()
    {
        // restore original array from php here cause on gc otherwise boom ...
        $GLOBALS[$this->_target] = $this->_input;
    }
}

?>
