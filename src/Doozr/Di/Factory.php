<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Di - Factory.
 *
 * Factory.php - The Di factory is responsible for creating instances configured through
 * a recipe only. Everything else is prepared by other parts of the Di library. But the
 * recipe is standardized and can be produced by any source. Instances/values (so called
 * Ingredients) required to be injected somewhere are part of the recipe and so all
 * requirements to produce instances are fulfilled.
 *
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
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Registry/Interface.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Di/Constants.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Di/Dependency.php';

/**
 * Doozr - Di - Factory.
 *
 * The Di factory is responsible for creating instances configured through
 * a recipe only. Everything else is prepared by other parts of the Di library. But the
 * recipe is standardized and can be produced by any source. Instances/values (so called
 * Ingredients) required to be injected somewhere are part of the recipe and so all
 * requirements to produce instances are fulfilled.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @link       https://github.com/clickalicious/Di
 * @final
 */
final class Doozr_Di_Factory
{
    /**
     * Reflection-class-instance of the current class.
     *
     * @var ReflectionClass
     */
    protected $reflection;

    /**
     * Whether the current class is instantiable.
     *
     * @var bool
     */
    protected $instantiable;

    /**
     * An registry interface compatible store for loading and storing instances.
     *
     * @var Doozr_Registry
     */
    protected $registry;

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param Doozr_Registry_Interface $registry An registry instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __construct(Doozr_Registry_Interface $registry)
    {
        $this
            ->registry($registry);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Builds an instance from passed recipe (key:value) and returns it.
     *
     * @param array $recipe    Recipe to build instance from.
     * @param array $arguments Arguments to pass to instance on creation.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return object New instance built from recipe.
     */
    public function build($recipe, array $arguments = [])
    {
        // Create Reflection Instance of passed className
        $this
            ->reflection(new ReflectionClass($recipe['className']));

        // Create an instance and return it to caller
        return $this->instantiate($recipe, $arguments);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for registry.
     *
     * @param Doozr_Registry_Interface $registry The registry to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setRegistry(Doozr_Registry_Interface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Fluent: Setter for registry.
     *
     * @param Doozr_Registry_Interface $registry The registry to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function registry(Doozr_Registry_Interface $registry)
    {
        $this->setRegistry($registry);

        return $this;
    }

    /**
     * Getter for registry.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Registry Registry if set, otherwise NULL
     */
    protected function getRegistry()
    {
        return $this->registry;
    }

    /**
     * Setter for instantiable.
     *
     * @param bool $instantiable The instantiable to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setInstantiable($instantiable)
    {
        $this->instantiable = $instantiable;
    }

    /**
     * Fluent: Setter for instantiable.
     *
     * @param bool $instantiable The instantiable to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function instantiable($instantiable)
    {
        $this->setInstantiable($instantiable);

        return $this;
    }

    /**
     * Getter for instantiable.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool Instantiable if set, otherwise NULL
     */
    protected function getInstantiable()
    {
        return $this->instantiable;
    }

    /**
     * Setter for reflection.
     *
     * @param ReflectionClass $reflection The reflection to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setReflection(ReflectionClass $reflection)
    {
        $this->reflection = $reflection;
    }

    /**
     * Fluent: Setter for reflection.
     *
     * @param ReflectionClass $reflection The reflection to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function reflection(ReflectionClass $reflection)
    {
        $this->setReflection($reflection);

        return $this;
    }

    /**
     * Getter for reflection.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return ReflectionClass The reflection class instance
     */
    protected function getReflection()
    {
        return $this->reflection;
    }

    /**
     * Returns an instance of a passed className.
     *
     * This method is intend to construct an instance of a given class and pass the given (optional) arguments
     * to the constructor. This method looks really ugly and i know this of course. But this way is a tradeoff
     * between functionality and speed optimization.
     *
     * @param string|array $className Name of the class to instantiate or an array containing [class => constructor].
     * @param array        $arguments Arguments to pass to constructor.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return object Instance of class with passed className
     *
     * @throws Doozr_Di_Exception
     */
    protected function construct($className, array $arguments = [])
    {
        // Check for static call (like getInstance or some other singleton)
        if (true === is_array($className)) {

            // Arguments require different handling ...
            if (count($arguments) > 0) {
                return call_user_func_array($className, $arguments);
            } else {
                return call_user_func($className);
            }
        } else {
            $countArguments = count($arguments);

            // Normal instantiation ... but a bit different for speedup.
            // Looks weired but its the fastest way!
            switch ($countArguments) {
                case 0:
                    return new $className();

                case 1:
                    return new $className(
                        $arguments[0]
                    );

                case 2:
                    return new $className(
                        $arguments[0],
                        $arguments[1]
                    );

                case 3:
                    return new $className(
                        $arguments[0],
                        $arguments[1],
                        $arguments[2]
                    );

                case 4:
                    return new $className(
                        $arguments[0],
                        $arguments[1],
                        $arguments[2],
                        $arguments[3]
                    );

                case 5:
                    return new $className(
                        $arguments[0],
                        $arguments[1],
                        $arguments[2],
                        $arguments[3],
                        $arguments[4]
                    );

                case 6:
                    return new $className(
                        $arguments[0],
                        $arguments[1],
                        $arguments[2],
                        $arguments[3],
                        $arguments[4],
                        $arguments[5]
                    );

                case 7:
                    return new $className(
                        $arguments[0],
                        $arguments[1],
                        $arguments[2],
                        $arguments[3],
                        $arguments[4],
                        $arguments[5],
                        $arguments[6]
                    );

                case 8:
                    return new $className(
                        $arguments[0],
                        $arguments[1],
                        $arguments[2],
                        $arguments[3],
                        $arguments[4],
                        $arguments[5],
                        $arguments[6],
                        $arguments[7]
                    );

                case 9:
                    return new $className(
                        $arguments[0],
                        $arguments[1],
                        $arguments[2],
                        $arguments[3],
                        $arguments[4],
                        $arguments[5],
                        $arguments[6],
                        $arguments[7],
                        $arguments[8]
                    );

                case 10:
                    return new $className(
                        $arguments[0],
                        $arguments[1],
                        $arguments[2],
                        $arguments[3],
                        $arguments[4],
                        $arguments[5],
                        $arguments[6],
                        $arguments[7],
                        $arguments[8],
                        $arguments[9]
                    );

                case 11:
                    return new $className(
                        $arguments[0],
                        $arguments[1],
                        $arguments[2],
                        $arguments[3],
                        $arguments[4],
                        $arguments[5],
                        $arguments[6],
                        $arguments[7],
                        $arguments[8],
                        $arguments[9],
                        $arguments[10]
                    );

                case 12:
                    return new $className(
                        $arguments[0],
                        $arguments[1],
                        $arguments[2],
                        $arguments[3],
                        $arguments[4],
                        $arguments[5],
                        $arguments[6],
                        $arguments[7],
                        $arguments[8],
                        $arguments[9],
                        $arguments[10],
                        $arguments[11]
                    );

                case 13:
                    return new $className(
                        $arguments[0],
                        $arguments[1],
                        $arguments[2],
                        $arguments[3],
                        $arguments[4],
                        $arguments[5],
                        $arguments[6],
                        $arguments[7],
                        $arguments[8],
                        $arguments[9],
                        $arguments[10],
                        $arguments[11],
                        $arguments[12]
                    );

                case 14:
                    return new $className(
                        $arguments[0],
                        $arguments[1],
                        $arguments[2],
                        $arguments[3],
                        $arguments[4],
                        $arguments[5],
                        $arguments[6],
                        $arguments[7],
                        $arguments[8],
                        $arguments[9],
                        $arguments[10],
                        $arguments[11],
                        $arguments[12],
                        $arguments[13]
                    );

                case 15:
                    return new $className(
                        $arguments[0],
                        $arguments[1],
                        $arguments[2],
                        $arguments[3],
                        $arguments[4],
                        $arguments[5],
                        $arguments[6],
                        $arguments[7],
                        $arguments[8],
                        $arguments[9],
                        $arguments[10],
                        $arguments[11],
                        $arguments[12],
                        $arguments[13],
                        $arguments[14]
                    );

                case 16:
                    return new $className(
                        $arguments[0],
                        $arguments[1],
                        $arguments[2],
                        $arguments[3],
                        $arguments[4],
                        $arguments[5],
                        $arguments[6],
                        $arguments[7],
                        $arguments[8],
                        $arguments[9],
                        $arguments[10],
                        $arguments[11],
                        $arguments[12],
                        $arguments[13],
                        $arguments[14],
                        $arguments[15]
                    );

                case 17:
                    return new $className(
                        $arguments[0],
                        $arguments[1],
                        $arguments[2],
                        $arguments[3],
                        $arguments[4],
                        $arguments[5],
                        $arguments[6],
                        $arguments[7],
                        $arguments[8],
                        $arguments[9],
                        $arguments[10],
                        $arguments[11],
                        $arguments[12],
                        $arguments[13],
                        $arguments[14],
                        $arguments[15],
                        $arguments[16]
                    );

                default:

                    // Try to run with lib from composer?
                    dump('Bum');die;

                    throw new Doozr_Di_Exception(
                        sprintf(
                            'Too much arguments passed to "%s". This method can getMetaComponents up to 17 arguments. You passed '.
                            '"%s". Please reduce arguments passed to constructor.',
                            __METHOD__,
                            $countArguments
                        )
                    );
                    break;
            }
        }
    }

    /**
     * Prepares the "arguments" in recipe either by creating them or merging runtime arguments in.
     *
     * @param array $recipe    The recipe to prepare arguments for/in.
     * @param array $arguments The optional runtime arguments to add.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The resulting and prepared recipe.
     */
    protected function prepareArguments(array $recipe, array $arguments)
    {
        if (true === isset($recipe['arguments'])) {
            $recipe['arguments'] = array_merge($recipe['arguments'], $arguments);
        } else {
            $recipe['arguments'] = $arguments;
        }

        return $recipe;
    }

    /**
     * Instantiates a class including it dependencies.
     *
     * This method is intend to instantiate a class and pass the required dependencies to it.
     * The dependencies are pre-configured and passed to this method as $recipe. The className is
     * the name of the class to instantiate and arguments is an (optional) array of arguments
     * which are passed to the class as additional arguments when instantiating.
     *
     * @param array $recipe    The recipe for instantiating (contains array of depencies, arguments, ...)
     * @param array $arguments Arguments to pass to instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return object The new created instance
     *
     * @throws Doozr_Di_Exception
     */
    protected function instantiate($recipe, array $arguments = [])
    {
        // Merge arguments with existing?
        $recipe = $this->prepareArguments($recipe, $arguments);

        // Get skeleton for storing different types of injections
        $injections = $this->initInjectionMapSkeleton();

        // Does it have dependencies?
        if (true === isset($recipe['dependencies']) && null !== $recipe['dependencies']) {

            // Iterate over config
            foreach ($recipe['dependencies'] as $dependency) {

                /* @var $dependency Doozr_Di_Dependency */
                // Create instance if no instance exists ...
                if (null === $dependency->getInstance()) {

                    // Check if dependency is just linked!
                    if (true === $dependency->hasLink()) {

                        // Look in registry for instance ...
                        if (null === $instance = $this->getRegistry()->get($dependency->getLink())) {
                            // ... alternatively try to build one :)
                            $instance = $this->getRegistry()->getContainer()->build($dependency->getLink());
                        }

                        if (null === $instance) {
                            throw new Doozr_Di_Exception(
                                sprintf(
                                    'Link to "%s" could not be resolved/satisfied by registry instance of type "%s".',
                                    $dependency->getLink(),
                                    get_class($this->getRegistry())
                                )
                            );
                        }

                        $dependency->setInstance($instance);
                    } else {

                        // Check basic requirements :D it's just the className!
                        if (null === $className = $dependency->getClassName()) {
                            throw new Doozr_Di_Exception(
                                sprintf(
                                    'Property "className" not set! If you are not using "link" then you need to '.
                                    'define the class to inject via "className".'
                                )
                            );
                        }

                        // Check if the constructor is known to us ...
                        if (null === $constructor = $dependency->getConstructor()) {
                            $constructor = self::parseConstructor(new \ReflectionClass($className));
                            $dependency->setConstructor($constructor);
                        }

                        // Create instance via this class ;)
                        $instance = $this->instantiate(
                            [
                                'className'   => $className,
                                'constructor' => $constructor,
                                'arguments'   => $dependency->getArguments(),
                            ]
                        );

                        // Store the instance
                        $dependency->setInstance($instance);
                    }
                }

                // Store position for injection if type = configuration
                if (null === $dependency->getPosition()) {
                    $position = (isset($injections[$dependency->getType()]))
                        ? count($injections[$dependency->getType()]) + 1
                        : 1;
                    $dependency->setPosition($position);
                }

                $injections[$dependency->getType()][] = [
                    'instance' => $dependency->getInstance(),
                    'position' => $dependency->getPosition(),
                    'target'   => $dependency->getTarget(),
                ];
            }
        }

        // Process injections, create instance and return it
        return $this->createInstance($recipe['className'], $recipe['constructor'], $arguments, $injections);
    }

    /**
     * Creates an instance of a class and returns it.
     *
     * This method is intend to instantiate a class and pass the required dependencies to it.
     * The dependencies are pre-configured and passed to this method as $recipe. The className is
     * the name of the class to instantiate and arguments is an (optional) array of arguments
     * which are passed to the class as additional arguments when instantiating.
     *
     * @param string $className   Name of the class being instantiated
     * @param string $constructor Name of constructor method (only required if not default = e.g. using Singleton)
     * @param array  $arguments   Arguments to pass to constructor when creating instance of $className
     * @param array  $injections  Injections to execute on instantiation process and via setter or property injections
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return object The new created instance.
     */
    protected function createInstance($className, $constructor = null, array $arguments = [], array $injections = [])
    {
        // Check for required dependency injections ...
        if (count($injections[Doozr_Di_Constants::INJECTION_TYPE_CONSTRUCTOR]) > 0) {
            // Get injections for constructor
            $constructorInjections = $this->parseInjections(
                Doozr_Di_Constants::INJECTION_TYPE_CONSTRUCTOR,
                $injections
            );

            // Process injections for constructor
            if (null !== $constructorInjections) {
                $arguments = $this->mergeArguments($constructorInjections, $arguments);
            }
        }

        // Get instance - for no dependency calls too
        $instance = $this->constructorInjection($className, $arguments, $constructor);

        // process only if $injections exists
        if (count($injections[Doozr_Di_Constants::INJECTION_TYPE_METHOD]) > 0) {

            // Get injections for methods
            $methodInjections = $this->parseInjections(
                Doozr_Di_Constants::INJECTION_TYPE_METHOD,
                $injections
            );

            if (null !== $methodInjections) {
                $this->methodInjection($instance, $methodInjections);
            }
        }

        // process only if $injections exists
        if (count($injections[Doozr_Di_Constants::INJECTION_TYPE_PROPERTY]) > 0) {

            // Get injections for property
            $propertyInjections = $this->parseInjections(
                Doozr_Di_Constants::INJECTION_TYPE_PROPERTY,
                $injections
            );

            // process injections for constructor
            if (null !== $propertyInjections) {
                $this->propertyInjection($instance, $propertyInjections);
            }
        }

        return $instance;
    }

    /**
     * Returns an instance with injected dependencies.
     *
     * This method is intend to return an instance of the given class. It injects
     * the required dependencies into constructor on instantiation.
     *
     * @param string $className   Name of the class being instantiated
     * @param array  $arguments   Arguments to pass to constructor when creating instance of $className
     * @param string $constructor Constructor used as priority 1 when not directly instantiable.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return object The new created instance
     */
    protected function constructorInjection($className, array $arguments = [], $constructor = null)
    {
        // Check for passed constructor or retrieve it
        if (null === $constructor) {
            $constructor = self::parseConstructor($this->getReflection());
        }

        // If not the default (__constructor) it must be static and so turn into array
        if (Doozr_Di_Constants::CONSTRUCTOR_METHOD !== $constructor) {
            $className = [$className, $constructor];
        }

        return $this->construct($className, $arguments);
    }

    /**
     * Returns constructor of  a class by ReflectionClass instance.
     * No matter if singleton or default class.
     *
     * @param ReflectionClass $reflectionClass A reflection class instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string Constructor parsed or detected
     *
     * @static
     */
    protected static function parseConstructor(\ReflectionClass $reflectionClass)
    {
        // Check if constructor is our common and well known __construct
        if (true === $reflectionClass->isInstantiable()) {
            $constructor = Doozr_Di_Constants::CONSTRUCTOR_METHOD;
        } else {
            // Only in other cases we need to parse ...
            $constructorCandidates = $reflectionClass->getMethods(ReflectionMethod::IS_STATIC);

            // Assume default singleton constructor method name
            $constructor = Doozr_Di_Constants::CONSTRUCTOR_METHOD_SINGLETON;

            $lastProcessedFileName = null;

            // iterate over static methods and check for instantiation
            foreach ($constructorCandidates as $constructorCandidate) {
                /* @var ReflectionMethod $constructorCandidate */
                $fileName = $constructorCandidate->getFileName();

                if ($lastProcessedFileName !== $fileName) {
                    $sourcecode            = file($fileName);
                    $lastProcessedFileName = $fileName;
                }

                // Start Extract method source
                $start            = $constructorCandidate->getStartLine() + 1;
                $end              = $constructorCandidate->getEndLine()   - 1;
                $methodSourcecode = '';

                // Concat sourcecode lines
                for ($i = $start; $i < $end; ++$i) {
                    $methodSourcecode .= $sourcecode[$i];
                }
                // End Extract method source

                // Check for instantiation code ... possibly the constructor
                if (
                    strpos(
                        $methodSourcecode,
                        'n'.'e'.'w'.' self('
                    ) ||
                    strpos(
                        $methodSourcecode,
                        'new '.$reflectionClass->getName().'('
                    )
                ) {
                    $constructor = $constructorCandidate->name;
                    break;
                }
            }
        }

        return $constructor;
    }

    /**
     * Injects given dependencies through methods static & non-static.
     *
     * @param string &$instance  The instance to inject dependencies to
     * @param array  $injections The dependencies to inject as array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function methodInjection(&$instance, array $injections)
    {
        foreach ($injections as $injection) {
            $instance->{$injection['signature']}($injection['argument']);
        }
    }

    /**
     * Injects given dependencies through properties.
     *
     * @param string &$instance  The instance to inject dependencies to
     * @param array  $injections The dependencies to inject as array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function propertyInjection(&$instance, array $injections)
    {
        foreach ($injections as $injection) {
            $instance->{$injection['signature']} = $injection['argument'];
        }
    }

    /**
     * Parses out the requested type of injection from list of injections.
     *
     * @param string $type       The type of injection (can be of: constructor, method, property)
     * @param array  $injections The dependencies to parse from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed NULL if no dependencies found, otherwise ARRAY containing the dependencies
     */
    protected function parseInjections($type, array $injections)
    {
        // assume no result
        $result = null;

        if (!empty($injections[$type])) {
            $result = [];

            switch ($type) {
            case Doozr_Di_Constants::INJECTION_TYPE_PROPERTY:
            case Doozr_Di_Constants::INJECTION_TYPE_METHOD:
                foreach ($injections[$type] as $recipe) {
                    $result[] = [
                        'signature' => $recipe['target'],
                        'argument'  => $recipe['instance'],
                    ];
                }
                break;
            default:
                // Break intentionally omitted
            case Doozr_Di_Constants::INJECTION_TYPE_CONSTRUCTOR:
                return $injections[$type];
                break;
            }
        }

        // return result
        return $result;
    }

    /**
     * Merges given constructor injections and arguments for constructor.
     *
     * @param array $injections Injection arguments to pass to constructor
     * @param array $arguments  Additional arguments to pass to constructor
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The merged result ready to pass to targets constructor
     */
    protected function mergeArguments(array $injections, array $arguments)
    {
        // Get total count of arguments
        $numberOfArguments = count($injections) + count($arguments);

        // Prepare an array with null values for given count
        $result = array_fill(0, $numberOfArguments, null);

        // Get count of injections for performance reasons outside loop
        $countInjections = count($injections);

        // Iterate the injections and position them in result
        for ($i = 0; $i < $countInjections; ++$i) {
            if (null !== $injections[$i]['position']) {
                $position = $injections[$i]['position'] - 1;
            } else {
                $position = $i;
            }

            $result[$position] = $injections[$i]['instance'];
        }

        // Iterate over remaining arguments and fill in the holes
        foreach ($result as $key => $value) {
            if ($result[$key] === null) {
                $result[$key] = array_shift($arguments);
            }
        }

        return $result;
    }

    /**
     * Creates and returns an empty array for the three types of injections.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array For the three types of injections
     */
    protected function initInjectionMapSkeleton()
    {
        return [
            Doozr_Di_Constants::INJECTION_TYPE_CONSTRUCTOR => [],
            Doozr_Di_Constants::INJECTION_TYPE_METHOD      => [],
            Doozr_Di_Constants::INJECTION_TYPE_PROPERTY    => [],
        ];
    }
}
