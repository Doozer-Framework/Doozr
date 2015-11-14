<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Di - Dependency
 *
 * Dependency.php - Dependency recipe representation. This class contains all information
 * to create instances and handle them within the Di Library.
 *
 * PHP versions 5.5
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
 * @package    Doozr_Di
 * @subpackage Doozr_Di_Dependency
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Di
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Constants.php';

/**
 * Doozr - Di - Dependency
 *
 * Dependency recipe representation. This class contains all information to create instances
 * and handle them within the Di Library.
 *
 * @example
 * id              string       doozr.foo.bar   ASCII
 * classname       string       Doozr_Registry  ASCII
 * instance        null|object
 * arguments       null|array
 * constructor     null|string
 * type            string       constructor     ASCII
 * target          string       __construct     ASCII
 * position        string       1
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
     * Id of this dependency instance.
     *
     * @var string
     * @access public
     */
    public $id;

    /**
     * Name of the class.
     *
     * @var string
     * @access public
     */
    public $classname;

    /**
     * Instance to use (instead of creating a new ones on request).
     * If instance is wired then each request for a Di Id like foo.bar.baz
     * will then return the wired one. If null then a fresh (or singleton)
     * instance will be returned.
     *
     * @var object
     * @access public
     */
    public $instance;

    /**
     * The arguments passed to the constructor of $classname when creating a new instance.
     *
     * @var array
     * @access public
     */
    public $arguments;

    /**
     * The constructor of $classname (Speeds up creating fresh instances).
     *
     * @var string
     * @access public
     */
    public $constructor;

    /**
     * The type of this dependency. Contains type of injection.
     * Defaults to "constructor".
     *
     * @var string
     * @access public
     */
    public $type = Doozr_Di_Constants::INJECTION_TYPE_CONSTRUCTOR;

    /**
     * The target used in combination with
     *
     * @var string
     * @access public
     */
    public $target;

    /**
     * The position where to inject this dependency into
     *
     * @var int
     * @access public
     */
    public $position;

    /**
     * The link to another existing Instance/Id.
     * This will make instance become a reference.
     *
     * @var string
     * @access public
     */
    public $link;

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param array $recipe The recipe of this dependency.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     */
    public function __construct(array $recipe = null)
    {
        if (null !== $recipe) {
            $this->import($recipe);
        }
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Generic import for recipes.
     *
     * @param array $recipe The recipe to import.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @throws Doozr_Di_Exception
     */
    public function import(array $recipe)
    {
        foreach ($recipe as $key => $value) {
            $method = 'set'.ucfirst($key);

            if (false === is_callable([$this, $method])) {
                throw new Doozr_Di_Exception(
                    sprintf('Property not supported: "%s"', $key)
                );
            }

            $this->{$method}($value);
        }
    }

    /**
     * Setter for id.
     *
     * @param string $id The id of the dependency.
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
     * Setter for id.
     *
     * @param string $id The id of the dependency.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function id($id)
    {
        $this->setId($id);

        return $this;
    }

    /**
     * Getter for Id.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The Id if set, otherwise NULL
     * @access public
     */
    public function getId()
    {
        return $this->id;
    }

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
     * @return $this Instance for chaining
     * @access public
     */
    public function instance($instance)
    {
        $this->setInstance($instance);

        return $this;
    }

    /**
     * Getter for instance.
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
     * Setter for arguments.
     *
     * @param string $arguments The arguments to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * Setter for arguments.
     *
     * @param array $arguments The arguments to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function arguments($arguments)
    {
        $this->setArguments($arguments);

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
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Returns TRUE if this dependency has arguments, otherwise FALSE.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if this dependency has arguments, otherwise FALSE
     * @access public
     */
    public function hasArguments()
    {
        return (true === isset($this->arguments) && count($this->arguments) > 0);
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
     * @return $this Instance for chaining
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
     * @return string String containing the signature of the constructor if set, otherwise NULL
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
     * @return bool TRUE if this dependency has arguments, otherwise FALSE
     * @access public
     */
    public function hasConstructor()
    {
        return isset($this->constructor);
    }

    /**
     * Setter for type.
     *
     * @param string $type The type to set.
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
     * @param string $type The type to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function type($type)
    {
        $this->setType($type);

        return $this;
    }

    /**
     * Getter for type.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The type if set, otherwise NULL
     * @access public
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Setter for target.
     *
     * @param string $target The target to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * Setter for target.
     *
     * @param string $target The target to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function target($target)
    {
        $this->setTarget($target);

        return $this;
    }

    /**
     * Returns the target of the current instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The target of the instance
     * @access public
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Setter for position.
     *
     * @param int $position The position to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * Setter for position.
     *
     * @param int $position The position to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function position($position)
    {
        $this->setPosition($position);

        return $this;
    }

    /**
     * Getter for position.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The position if set, otherwise NULL
     * @access public
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Setter for link.
     *
     * @param string $link The link of the dependency.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string
     * @access public
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * Setter for link.
     *
     * @param string $link The link of the dependency.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function link($link)
    {
        $this->setLink($link);

        return $this;
    }

    /**
     * Getter for Link.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The Link if set, otherwise NULL
     * @access public
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Returns state either dependency has link.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if dependency has link, otherwise FALSE
     * @access public
     */
    public function hasLink()
    {
        return (null !== $this->getLink());
    }

    /**
     * Returns the current dependency as array.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The dependency recipe
     * @access public
     */
    public function asArray()
    {
        return [
            'id'        => $this->getId(),
            'classname' => $this->getClassname(),
            'instance'  => $this->getInstance(),
            'arguments' => $this->getArguments(),
            'type'      => $this->getType(),
            'target'    => $this->getTarget(),
            'position'  => $this->getPosition(),
        ];
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
     * @return bool TRUE if offset is set, otherwise FALSE
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
        if (true === is_null($offset)) {
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
