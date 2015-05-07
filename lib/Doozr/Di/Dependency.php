<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Di - Dependency
 *
 * Dependency.php - Dependency representation. Instances of this class representing a dependency with a configuration.
 *
 * PHP versions 5.4
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
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
 * @category   Doozr
 * @package    Doozr_Di
 * @subpackage Doozr_Di_Dependency
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Di
 */

/**
 * Doozr - Di - Dependency
 *
 * Dependency representation. Instances of this class representing a dependency with a configuration.
 *
 * @category   Doozr
 * @package    Doozr_Di
 * @subpackage Doozr_Di_Dependency
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link       https://github.com/clickalicious/Di
 */
class Doozr_Di_Dependency
    implements
    ArrayAccess
{
    /**
     * The name of the class of a single dependency
     *
     * @var string
     * @access protected
     */
    protected $classname;

    /**
     * An existing instance to use instead of creating a new one
     *
     * @var object
     * @access protected
     */
    protected $instance;

    /**
     * The arguments which are passed to the constructor of $classname
     * when creating a new instance.
     *
     * @var array
     * @access protected
     */
    protected $arguments;

    /**
     * The constructor for creating fresh instances of the dependency(class).
     *
     * @var string
     * @access protected
     */
    protected $constructor;

    /**
     * The configuration of this dependency.
     * Contains type of injection and the value
     * (eg. type = method, value = setFoo)
     *
     * @var array
     * @access protected
     */
    protected $configuration;

    /**
     * The identifier eg. used for wiring
     *
     * @var string
     * @access protected
     */
    protected $identifier;

    /**
     * Dependency type: constructor
     * Constructor injection
     *
     * @var string
     * @access public
     * @const
     */
    const TYPE_CONSTRUCTOR = 'constructor';

    /**
     * Dependency type: method
     * Method injection
     *
     * @var string
     * @access public
     * @const
     */
    const TYPE_METHOD = 'method';

    /**
     * Dependency type: constructor
     * Constructor injection
     *
     * @var string
     * @access public
     * @const
     */
    const TYPE_PROPERTY = 'property';


    /**
     * Constructor
     *
     * This method is the constructor.
     *
     * @param string|null $classname The name of the class (the dependency)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     */
    public function __construct($classname = null)
    {
        $this
            ->classname($classname);
    }


    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for classname.
     *
     * @param string $classname The name of the class to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setClassname($classname)
    {
        $this->classname = $classname;
    }

    /**
     * Setter for classname.
     *
     * @param string $classname The name of the class to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function classname($classname)
    {
        $this->setClassname($classname);
        return $this;
    }


    /**
     * Returns the name of the class
     *
     * This method is intend to return the name of the class.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The name of the dependency class
     * @access public
     */
    public function getClassname()
    {
        return $this->classname;
    }

    /**
     * Setter for instance.
     *
     * @param object $instance The instance to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;
    }

    /**
     * Setter for instance.
     *
     * @param object $instance The instance to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this instance for chaining
     * @access public
     */
    public function instance($instance)
    {
        $this->setInstance($instance);
        return $this;
    }

    /**
     * Returns the instance of the class
     *
     * This method is intend to return the instance of the class.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object The instance of the dependency class
     * @access public
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * Setter for identifier.
     *
     * @param string $identifier The identifier to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * Setter for identifier.
     *
     * @param string $identifier The identifier to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function identifier($identifier)
    {
        $this->setIdentifier($identifier);
        return $this;
    }

    /**
     * Returns the identifier of the current instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The identifier of the instance
     * @access public
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Setter for arguments.
     *
     * @param array $arguments The arguments to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * Setter for arguments.
     *
     * @param array $arguments The arguments to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function arguments(array $arguments)
    {
        $this->setArguments($arguments);
        return $this;
    }

    /**
     * Returns the arguments of the class.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Array containing arguments if set, otherwise NULL
     * @access public
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Returns TRUE if this dependency has arguments, otherwise FALSE.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if this dependency has arguments, otherwise FALSE
     * @access public
     */
    public function hasArguments()
    {
        return isset($this->arguments);
    }

    /**
     * Sets the constructor of the dependency class.
     *
     * @param string $constructor The signature of the constructor
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setConstructor($constructor)
    {
        $this->constructor = $constructor;
    }

    /**
     * Sets the constructor of the dependency class.
     *
     * @param string $constructor The signature of the constructor
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function constructor($constructor)
    {
        $this->setConstructor($constructor);
        return $this;
    }

    /**
     * Returns the constructor of the dependency class.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed String containing the signature of the constructor if set, otherwise NULL
     * @access public
     */
    public function getConstructor()
    {
        return $this->constructor;
    }

    /**
     * Returns TRUE if this dependency has a custom constructor, otherwise FALSE.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if this dependency has arguments, otherwise FALSE
     * @access public
     */
    public function hasConstructor()
    {
        return isset($this->constructor);
    }

    /**
     * Sets the configuration of the class.
     *
     * @param array $configuration The configuration to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Sets the configuration of the class.
     *
     * @param array $configuration The configuration to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function configuration(array $configuration)
    {
        $this->setConfiguration($configuration);
        return $this;
    }

    /**
     * Returns the configuration of the class
     *
     * This method is intend to return the configuration of the class.
     * If not set the default return value is returned. The default is
     * type => constructor.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Array containing arguments if set, otherwise NULL
     * @access public
     */
    public function getConfiguration()
    {
        return (!$this->configuration)
            ? array('type' => 'constructor')
            : $this->configuration;
    }

    /**
     * Returns the current dependency as array.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The dependency setup
     * @access public
     */
    public function asArray()
    {
        return array(
            'classname'     => $this->classname,
            'instance'      => $this->instance,
            'arguments'     => $this->arguments,
            'configuration' => $this->configuration
        );
    }

    /**
     * Creates a random unique Id for this instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The random and unique Id
     * @access public
     */
    public function getRandomId()
    {
        return sha1(serialize($this) . microtime());
    }

    /*------------------------------------------------------------------------------------------------------------------
    | MAGIC
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * magic __toString
     *
     * This method return the name of the dependency-class
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The name of the dependency-class
     * @access public
     */
    public function __toString()
    {
        return $this->classname;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | ARRAY ACCESS
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Implements offsetExists
     *
     * @param string $offset The offset to check
     *
     * @return boolean TRUE if offset is set, otherwise FALSE
     */
    public function offsetExists($offset)
    {
        return isset($this->{$offset});
    }

    /**
     * Implements offsetGet
     *
     * @param string $offset The offset to return
     *
     * @return mixed The data from offset
     */
    public function offsetGet($offset)
    {
        return $this->{$offset};
    }

    /**
     * Implements offsetSet
     *
     * @param string $offset The offset to set
     * @param mixed  $value  The value to set
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->classname = $value;
        } else {
            $this->{$offset} = $value;
        }
    }

    /**
     * Implements offsetUnset
     *
     * @param string $offset The offset to unset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->{$offset});
    }
}
