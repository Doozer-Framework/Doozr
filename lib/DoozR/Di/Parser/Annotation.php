<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Di - Annotation Parser
 *
 * Annotation.php - Annotation Parser of the Di-Framework
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - Di - The Dependency Injection Framework
 *
 * Copyright (c) 2012, Benjamin Carl - All rights reserved.
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
 * @category   Di
 * @package    DoozR_Di
 * @subpackage DoozR_Di_Parser_Annotation
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Di
 */

require_once DI_PATH_LIB_DI . 'Parser/Abstract.php';
require_once DI_PATH_LIB_DI . 'Parser/Interface.php';
require_once DI_PATH_LIB_DI . 'Exception.php';

use Doctrine\Common\Annotations\AnnotationReader;

/**
 * DoozR - Di - Annotation Parser
 *
 * Annotation Parser of the Di-Framework
 *
 * @category   Di
 * @package    DoozR_Di
 * @subpackage DoozR_Di_Parser_Annotation
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link       https://github.com/clickalicious/Di
 */
class DoozR_Di_Parser_Annotation extends DoozR_Di_Parser_Abstract implements DoozR_Di_Parser_Interface
{
    /**
     * An annotation reader instance.
     *
     * @var AnnotationReader
     * @access protected
     * @static
     */
    protected static $annotationReader;

    /**
     * The pattern used to identify our annotations
     *
     * @var string
     * @access const
     */
    const BASE_PATTERN = 'inject';

    /**
     * The range to parse from:
     * EVERYTHING
     *
     * @var int
     * @access public
     */
    const RANGE_EVERYTHING = 1;

    /**
     * The range to parse from:
     * CLASS = Only from class docblock
     *
     * @var int
     * @access public
     */
    const RANGE_CLASS = 2;

    /**
     * The range to parse from:
     * METHODS = Only from methods docblocks
     *
     * @var int
     * @access public
     */
    const RANGE_METHODS = 3;

    /**
     * The range to parse from:
     * PROPERTIES = Only from class properties docblocks
     *
     * @var int
     * @access public
     */
    const RANGE_PROPERTIES = 4;

    /**
     * The range to parse from:
     * SINGLE = Only from single elements docblock
     *
     * @var int
     * @access public
     */
    const RANGE_SINGLE_ELEMENT = 5;


    /**
     * Parses the annotations out of input and return it as array
     *
     * This method is intend to build an array of options for each of the commands that were matched.
     * This options array is readable/similar to a dependency map item. This method requires only
     * argument one ($identifier) to work properly.
     *
     * @param int $range The range to parse from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Containing the dependencies build from annotations
     * @access public
     * @throws DoozR_Di_Exception
     */
    public function parse($range = self::RANGE_EVERYTHING)
    {
        // check if all requirements are fulfilled
        if (!$this->requirementsFulfilled()) {
            throw new DoozR_Di_Exception(
                'Error parsing annotations. Requirements not fulfilled. Please set input to parse annotations from.'
            );
        }

        // prepare input
        $input = $this->getInput();

        // check if class is already in scope
        if (!class_exists($input['class'])) {
            if (!isset($input['file'])) {
                throw new DoozR_Di_Exception(
                    'Error parsing dependencies from class. Class not found in scope and no "file" defined!'
                );
            }

            $this->loadFile($input['file']);
        }

        // create a reflection instance of the class
        $reflection = new ReflectionClass($input['class']);

        // parse annotation(s) from reflection and return result
        $this->lastResult = $this->parseFromReflectionByRange($reflection, $range);

        return $this->lastResult;
    }

    /**
     * Checks if the string has a Inject command
     *
     * This method is intend to check if a given string contains a Inject command.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Containing the dependencies build from annotations
     * @access public
     */
    public function hasCommand()
    {
        return (isset($this->data['matched'])) ? ($this->data['matched'] > 0) : false;
    }

    /**
     * Returns the count of Inject commands
     *
     * This method is intend to return the count of Inject commands from last
     * parsing.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The count of commands found
     * @access public
     */
    public function numberOfCommands()
    {
        /*
        return (isset($this->data['matches'][1]))
            ? count($this->data['matches'][1])
            : 0;
        */
        return (is_array($this->lastResult)) ? count($this->lastResult) : 0;
    }

    /**
     * Checks if the requirements are fulfilled
     *
     * This method is intend to check if the requirements are fulfilled.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if requirements fulfilled, otherwise FALSE
     * @access public
     * @static
     */
    public function requirementsFulfilled()
    {
        return ($this->input !== null);
    }

    /**
     * Parses the dependencies from a given reflection for defined range and optional method or property.
     *
     * @param ReflectionClass $reflection The reflection instance to parse from
     * @param int $range The range to parse from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array An raw array containing the dependencies indexed@access private
     * @access protected
     * @throws DoozR_Di_Exception
     */
    protected function parseFromReflectionByRange(ReflectionClass $reflection, $range)
    {
        $dependencies = array();

        switch ($range) {
            case self::RANGE_CLASS:
                $dependencies = array_merge($dependencies, $this->parseFromClassComment($reflection));
                break;

            case self::RANGE_METHODS:
                $dependencies = array_merge($dependencies, $this->parseFromClassMethods($reflection));
                break;

            case self::RANGE_PROPERTIES:
                $dependencies = array_merge($dependencies, $this->parseFromClassProperties($reflection));
                break;

            case self::RANGE_SINGLE_ELEMENT:
                throw new DoozR_Di_Exception(
                    'Parsing from single element not implemented yet!'
                );
                break;

            default:
            case self::RANGE_EVERYTHING:
                $dependencies = array_merge($dependencies, $this->parseFromClassComment($reflection));
                $dependencies = array_merge($dependencies, $this->parseFromClassMethods($reflection));
                $dependencies = array_merge($dependencies, $this->parseFromClassProperties($reflection));
                break;
        }

        return $dependencies;
    }

    /**
     * Parses the annotations out of input and return it as array.
     * This options array is readable/similar to a dependency map item.
     *
     * @param string $sourcecode The sourcecode to parse annotations from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Containing the dependencies parsed from annotations found
     * @access protected
     */
    protected function getAnnotationFromSource($sourcecode)
    {
        // parse annotations out of source
        $this->data['matched'] = preg_match_all(
            '/@'.self::BASE_PATTERN.'(.*?)(\n|$)/i',
            $sourcecode,
            $this->data['matches']
        );

        // assume empty result
        $result = array();

        // check for command
        if ($this->hasCommand()) {

            // iterate over matches
            foreach ($this->data['matches'][1] as $command) {

                // trim whitespaces
                $command = trim($command);

                // get default dependencies (skeleton)
                $tmp = $this->getDefaultSekeleton();

                // split whole command into single arguments
                $arguments = explode(' ', $command);

                // store identifier
                if (stristr($arguments[0], ':')) {
                    $identifier = explode(':', $arguments[0]);
                    $tmp['class']      = $identifier[0];
                    $tmp['identifier'] = $identifier[1];
                } else {
                    $tmp['identifier'] = $arguments[0];
                }

                $countArguments = count($arguments);

                if ($countArguments > 1) {
                    for ($i = 1; $i < $countArguments; ++$i) {
                        $keyValuePair = explode(':', $arguments[$i]);

                        if (count($keyValuePair) == 2) {
                            $tmp[$keyValuePair[0]] = $keyValuePair[1];
                        } else {
                            $tmp['type'] = $keyValuePair[0];
                        }
                    }
                }

                $result[] = $tmp;
            }
        }

        return $result;
    }

    /**
     * Getter for AnnotationReader with lazy instantiate.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return AnnotationReader Instance of annotation reader
     * @access protected
     */
    protected static function getAnnotationReader()
    {
        if (self::$annotationReader === null) {
            self::$annotationReader = new AnnotationReader();
        }

        return self::$annotationReader;
    }

    /**
     * Parses the dependencies from a given reflection out of the class' comment.
     *
     * @param ReflectionClass $reflection The reflection instance to parse from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array An raw array containing the dependencies indexed
     * @access protected
     */
    protected function parseFromClassComment(ReflectionClass $reflection)
    {
        $result = array();

        $dependencies = self::getAnnotationReader()->getClassAnnotations($reflection);

        foreach ($dependencies as $key => $dependency) {
            if ($dependency->type === 'constructor' && $dependency->identifier !== '__construct') {
                $dependency->constructor = $dependency->identifier;
            } else {
                $dependency->constructor = '__construct';
            }

            if (!isset($result[$dependency->identifier])) {
                $result[$dependency->identifier] = array();
            }

            $result[$dependency->identifier][] = object_to_array($dependency);
        }

        return $result;

        /*
        $dependencies = $this->getAnnotationFromSource($reflection->getDocComment());

        $type        = isset($dependencies[0]['type']) ? $dependencies[0]['type'] : null;
        $identifier  = isset($dependencies[0]['identifier']) ? $dependencies[0]['identifier'] : null;

        if ($type === 'constructor' && $identifier !== '__construct') {
            $constructor = $identifier;
        } else {
            $constructor = '__construct';
        }
        return array($constructor => $dependencies);
        */
    }

    /**
     * Parses the dependencies from a given reflection out of the class' methods.
     *
     * @param ReflectionClass $reflection The reflection instance to parse from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array An raw array containing the dependencies indexed
     * @access private
     */
    protected function parseFromClassMethods(ReflectionClass $reflection)
    {
        $result = array();

        // get dependencies from method comment
        $reflectionMethods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        /* @var $reflectionMethod ReflectionMethod */
        foreach ($reflectionMethods as $reflectionMethod) {
            $tmpDependency = $this->getAnnotationFromSource($reflectionMethod->getDocComment());

            if ($tmpDependency) {
                $result[$reflectionMethod->getName()] = $tmpDependency;
            }
        }

        return $result;
    }

    /**
     * Parses the dependencies from a given reflection out of the class' properties.
     *
     * @param ReflectionClass $reflection The reflection instance to parse from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array An raw array containing the dependencies indexed
     * @access protected
     */
    protected function parseFromClassProperties(ReflectionClass $reflection)
    {
        $result = array();

        // get dependencies from property comment
        $reflectionProperties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($reflectionProperties as $reflectionProperty) {
            $tmpDependency = $this->getAnnotationFromSource($reflectionProperty->getDocComment());

            if ($tmpDependency) {
                $result[$reflectionProperty->getName()] = $tmpDependency;
            }
        }

        return $result;
    }
}