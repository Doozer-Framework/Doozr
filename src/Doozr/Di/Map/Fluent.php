<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Di - Map - Fluent.
 *
 * Fluent.php - Fluent map class of Di.
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
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       https://github.com/clickalicious/Di
 */
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Di/Map.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Di/Map/Interface.php';

/**
 * Doozr - Di - Map - Fluent.
 *
 * Fluent map class of Di.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @link       https://github.com/clickalicious/Di
 */
class Doozr_Di_Map_Fluent extends Doozr_Di_Map
    implements
    Doozr_Di_Map_Interface
{
    /**
     * Current active className to add dependencies for.
     *
     * @var string
     */
    protected $className;

    /**
     * Last active className.
     *
     * @var string
     */
    protected $lastClassName;

    /**
     * Base dependency object.
     *
     * @var Doozr_Di_Dependency
     */
    protected $dependency;

    /**
     * Current active dependency.
     *
     * @var Doozr_Di_Dependency
     */
    protected $currentDependency;

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param Doozr_Di_Collection $collection Doozr_Di_Collection to collect dependencies in.
     * @param Doozr_Di_Dependency $dependency Doozr_Di_Dependency base object for cloning dependencies from.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __construct(
        Doozr_Di_Collection $collection,
        Doozr_Di_Dependency $dependency
    ) {
        $this
            ->collection($collection)
            ->dependency($dependency);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Empty container method to keep the interface consistent with other Doozr_Di_Map_* classes.
     *
     * @param mixed $source The source to generate from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Map_Fluent Current instance for chaining method calls
     */
    public function generate($source = null)
    {
        // Empty container method for interface fulfillment (Doozr_Di_Map_Static, Doozr_Di_Map_Fluent)
        return $this;
    }

    /**
     * Setter for the name of the class which has dependencies.
     *
     * This method is intend to set the name of the class which has dependencies.
     *
     * @param string $className   The name of the class which has dependencies
     * @param mixed  $arguments   The arguments to pass to constructor of class
     * @param mixed  $constructor The constructor of the class
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Map_Fluent Instance of this class (for method chaining)
     */
    public function className($className, $arguments = null, $constructor = null)
    {
        // flush maybe exisiting content
        $this->flush();

        // store className
        $this->className = $className;

        // add arguments if given
        if (!is_null($arguments) && is_array($arguments)) {
            $this->getCollection()->setArguments($className, $arguments);
        }

        // add constructor if given
        if (!is_null($constructor)) {
            $this->getCollection()->setConstructor($className, $constructor);
        }

        // fluent interface
        return $this;
    }

    /**
     * Setter for the name of the dependency class.
     *
     * This method is intend to set the name of the dependency class.
     *
     * @param string $className The name of the dependency class
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Map_Fluent Instance of this class (for method chaining)
     */
    public function dependsOn($className)
    {
        // store last created temporary dependency
        if ($this->currentDependency) {
            $this->flush();
        }

        /* @var $this->currentDependency Doozr_Di_Dependency */
        $this->currentDependency = clone $this->dependency;

        $this->currentDependency->setClassName($className);

        return $this;
    }

    /**
     * Setter for the target of the dependency class.
     *
     * This method is intend to set the target of the dependency class.
     *
     * @param string $target The target of the dependency class
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Map_Fluent Instance of this class (for method chaining)
     */
    public function target($target)
    {
        /* @var $this->currentDependency Doozr_Di_Dependency */
        $this->currentDependency->setTarget($target);

        // fluent interface
        return $this;
    }

    /**
     * Setter for the target of the dependency class.
     *
     * This method is intend to set the target of the dependency class.
     *
     * @param string $target The target of the dependency class
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Map_Fluent Instance of this class (for method chaining)
     */
    public function id($target)
    {
        if (null === $this->currentDependency) {
            throw new Doozr_Form_Service_Exception(
                sprintf('Please call className() before trying to set an Id via %s', __METHOD__)
            );
        }

        return $this->target($target);
    }

    /**
     * Setter for the instance of the dependency class.
     *
     * This method is intend to set the instance of the dependency class.
     *
     * @param mixed $instance The instance to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Map_Fluent Instance of this class (for method chaining)
     */
    public function instance($instance)
    {
        /* @var $this->currentDependency Doozr_Di_Dependency */
        $this->currentDependency->setInstance($instance);

        // fluent interface
        return $this;
    }

    /**
     * Setter for the arguments of the dependency class.
     *
     * This method is intend to set the arguments of the dependency class.
     *
     * @param array $arguments The arguments to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance of this class (for method chaining)
     */
    public function arguments(array $arguments)
    {
        /* @var $this->currentDependency Doozr_Di_Dependency */
        $this->currentDependency->setArguments($arguments);

        return $this;
    }

    /**
     * Stores the name of the last processed class.
     *
     * This method is required for the fluent interface.
     *
     * @param string $className The name of the last processed class
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setLastProcessedClass($className)
    {
        $this->lastClassName = $className;
    }

    /**
     * Returns the name of the last processed class.
     *
     * This method is intend to return the name of the last processed class
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The name of the last processed class
     */
    public function getLastProcessedClass()
    {
        return $this->lastClassName;
    }

    /**
     * Shortcut to Doozr_Di_Map::wire().
     *
     * @param int   $mode   The mode to use for wiring
     * @param array $matrix The wire matrix containing instances to wire
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Map_Fluent Instance of this class (for method chaining)
     */
    public function wire(array $matrix = [], $mode = Doozr_Di_Constants::WIRE_MODE_AUTOMATIC)
    {
        // flush maybe existing temporary content
        $this->flush();

        parent::wire($matrix, $mode);

        return $this;
    }

    /**
     * Setter for the configuration of the dependency class.
     *
     * This method is intend to set the configuration of the dependency class.
     *
     * @param bool $returnContainer TRUE to return container instance, FALSE to return map instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Container Instance of the container for active scope
     */
    public function store($returnContainer = true)
    {
        // reset state
        $this->reset();

        // store this map to container
        $this->container->setMap($this);

        if ($returnContainer === true) {
            return $this->container;
        } else {
            // fluent interface
            return $this;
        }
    }

    /**
     * Shortcut for build().
     *
     * This method is intend to act as a shortcut to build().
     *
     * @param array  $arguments The arguments to pass to build()
     * @param string $className The (optional) name of the class to build instance of
     * @param bool   $wire      TRUE to automatic wire instances, otherwise FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Map_Fluent Instance of this class (for method chaining)
     */
    public function build($arguments = null, $className = null, $wire = true)
    {
        $className = ($className) ? $className : $this->className;

        /*
        if (true === $wire) {
            $this->wire(Doozr_Di_Constants::WIRE_MODE_AUTOMATIC);
        }*/

        #return $this->store()->build($className, $arguments);
    }

    /**
     * Resets the state of this instance to default.
     * This method is intend to set the configuration of the dependency class.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function reset()
    {
        $this->className  = null;
        $this->dependency = null;

        parent::reset();
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PROTECTED
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Flushes the content.
     *
     * This method is intend to flush the temporary content.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function flush()
    {
        $this->lastClassName = $this->className;

        if ($this->currentDependency !== null) {
            $this->getCollection()->addDependency(
                $this->id,
                $this->className,
                $this->constructor,
                $this->currentDependency);
        }

        $this->currentDependency = null;
    }
}
