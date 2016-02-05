<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Base - Tools.
 *
 * Tools.php - Toolkit collection useful for developers.
 *
 * PHP version 5
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
 * @link       http://clickalicious.github.com/Doozr/
 */
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Development.php';

/**
 * Doozr - Base - Tools.
 *
 * Toolkit collection useful for developers.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Base_Tools extends Doozr_Base_Development
{
    /**
     * Backtrace information.
     *
     * @var array|null
     */
    protected $debugBacktrace;

    /**
     * Path to parent.
     *
     * @var array
     */
    protected $paths = [
        self::PATH_REALPATH => null,
        self::PATH_SYMLINK  => null,
    ];

    /**
     * Filename.
     *
     * @var string
     */
    protected $filenameOfCurrentClass;

    /**
     * Path and filename.
     *
     * @var string
     */
    protected $pathAndFile;

    /**
     * Identifier for realpath.
     *
     * @var string
     */
    const PATH_REALPATH = 'realpath';

    /**
     * Identifier for symlink.
     *
     * @var string
     */
    const PATH_SYMLINK = 'symlink';

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Getter for filenameOfCurrentClass.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array|null Stored filenameOfCurrentClass, or NULL
     */
    public function retrieveFilenameOfCurrentClass()
    {
        // Inline cache [expensive]
        if (null === $this->getFilenameOfCurrentClass()) {

            // Inline cache [expensive]
            if (null === $debugBacktrace = $this->getDebugBacktrace()) {
                $debugBacktrace = debug_backtrace();

                $this->setDebugBacktrace(
                    $debugBacktrace
                );
            }

            // Some security by retrieving path by path
            $this->filenameOfCurrentClass = filename($debugBacktrace[0]['file']);
        }

        return $this->filenameOfCurrentClass;
    }

    /**
     * Returns the path to the parent (extending class) of this class.
     *
     * @param bool $resolveSymlinks TRUE to resolve symlink, otherwise FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The path to parent class
     */
    public function retrievePathToCurrentClass($resolveSymlinks = false)
    {
        $mode = (false === $resolveSymlinks) ? self::PATH_SYMLINK : self::PATH_REALPATH;

        // Inline cache for path retrieval [expensive]
        if (null === $this->paths[$mode]) {

            // Inline cache [expensive]
            if (null === $debugBacktrace = $this->getDebugBacktrace()) {
                $debugBacktrace = debug_backtrace();

                $this->setDebugBacktrace(
                    $debugBacktrace
                );
            }

            $path               = realpath_ext($debugBacktrace[0]['file'], $resolveSymlinks);
            $this->paths[$mode] = dirname($path).DIRECTORY_SEPARATOR;
        }

        return $this->paths[$mode];
    }

    /**
     * Returns the filename and path (combined) of the parent.
     *
     * @param bool $resolveSymlinks TRUE to resolve symlink, otherwise FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The filename and path of the parent class
     */
    public function retrievePathAndFilename($resolveSymlinks = false)
    {
        // Inline cache [expensive]
        if (null === $pathAndFile = $this->getPathAndFile()) {
            $pathAndFile = $this->retrievePathToCurrentClass($resolveSymlinks).
                           $this->retrieveFilenameOfCurrentClass();

            $this->setPathAndFile(
                $pathAndFile
            );
        }

        return $pathAndFile;
    }

    /**
     * Returns whether a method passed as string exists in current class hierarchy.
     *
     * @param string $method Name of the method to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if method exist, otherwise FALSE
     */
    public function hasMethod($method)
    {
        return method_exists(get_class($this), $method);
    }

    /**
     * Call methods from passed instances (generic dynamic calling methods).
     *
     * @param object     $instance   Instance of a class to call method from
     * @param string     $methodName Name of the method to call
     * @param mixed|null $parameter  Parameter as mixed
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed The result of the call
     * @static
     */
    public static function dynamicCall($instance, $methodName, $parameter = null)
    {
        // No params given
        if (null === $parameter) {
            $returnValue = $instance->{$methodName}();
        } else {
            // Pass params to method
            $returnValue = $instance->{$methodName}($parameter);
        }

        return $returnValue;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns an instance fof class requested.
     *
     * @param string $className       Name of the class to load
     * @param array  $arguments       Arguments to pass to loaded class
     * @param string $constructor     Constructor to use for instantiation
     * @param string $includeFilename Filename to include before instantiating the class
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return object Instance of the class ($classname) requested
     */
    protected static function instantiate($className, $arguments = null, $constructor = null, $includeFilename = null)
    {
        // Include file given?
        if (null !== $includeFilename) {
            include_once $includeFilename;
        }

        // create reflection of class
        $reflectionOfClass = new ReflectionClass($className);

        // Check if class is instantiable
        if (false === $reflectionOfClass->isInstantiable()) {

            // if no constructor given for singleton instantiation try to detect
            if (null === $constructor) {
                $constructor = self::parseConstructor($reflectionOfClass);
            }

            // Instantiate ...
            if (null === $arguments) {
                // ... without any arguments
                return call_user_func($className.'::'.$constructor);
            } else {
                // ... with arguments passed by
                if (is_array($arguments)) {
                    return call_user_func_array($className.'::'.$constructor, $arguments);
                } else {
                    return call_user_func($className.'::'.$constructor, $arguments);
                }
            }
        } else {

            // Check for given arguments
            if ($arguments) {

                // check if parameter is already of type array
                if (!is_array($arguments)) {
                    // if not make array
                    $arguments = [$arguments];
                }

                // return instance with parameter}
                return $reflectionOfClass->newInstanceArgs($arguments);
            } else {
                // return instance without parameter
                return $reflectionOfClass->newInstance();
            }
        }
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

            // iterate over static methods and check for instantiation
            foreach ($constructorCandidates as $constructorCandidate) {

                /* @var ReflectionMethod $constructorCandidate */
                $sourcecode = file($constructorCandidate->getFileName());

                $start            = $constructorCandidate->getStartLine() + 1;
                $end              = $constructorCandidate->getEndLine()   - 1;
                $methodSourcecode = '';

                // Concat sourcecode lines
                for ($i = $start; $i < $end; ++$i) {
                    $methodSourcecode .= $sourcecode[$i];
                }

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
     * Setter for debugBacktrace.
     *
     * @param array $debugBacktrace The debugBacktrace to store.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setDebugBacktrace(array $debugBacktrace)
    {
        $this->debugBacktrace = $debugBacktrace;
    }

    /**
     * Fluent: Setter for debugBacktrace.
     *
     * @param array $debugBacktrace The debugBacktrace to store.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function debugBacktrace(array $debugBacktrace)
    {
        $this->setDebugBacktrace($debugBacktrace);

        return $this;
    }

    /**
     * Getter for debugBacktrace.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array|null Stored debugBacktrace, or NULL
     */
    protected function getDebugBacktrace()
    {
        return $this->debugBacktrace;
    }

    /**
     * Setter for filenameOfCurrentClass.
     *
     * @param array $filenameOfCurrentClass The filenameOfCurrentClass to store.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setFilenameOfCurrentClass(array $filenameOfCurrentClass)
    {
        $this->filenameOfCurrentClass = $filenameOfCurrentClass;
    }

    /**
     * Fluent: Setter for filenameOfCurrentClass.
     *
     * @param array $filenameOfCurrentClass The filenameOfCurrentClass to store.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function filenameOfCurrentClass(array $filenameOfCurrentClass)
    {
        $this->setFilenameOfCurrentClass($filenameOfCurrentClass);

        return $this;
    }

    /**
     * Getter for filenameOfCurrentClass.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array|null Stored filenameOfCurrentClass, or NULL
     */
    protected function getFilenameOfCurrentClass()
    {
        return $this->filenameOfCurrentClass;
    }

    /**
     * Setter for pathAndFile.
     *
     * @param string $pathAndFile The pathAndFile to store.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setPathAndFile($pathAndFile)
    {
        $this->pathAndFile = $pathAndFile;
    }

    /**
     * Fluent: Setter for pathAndFile.
     *
     * @param string $pathAndFile The pathAndFile to store.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function pathAndFile($pathAndFile)
    {
        $this->setPathAndFile($pathAndFile);

        return $this;
    }

    /**
     * Getter for pathAndFile.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string|null Stored pathAndFile, or NULL
     */
    protected function getPathAndFile()
    {
        return $this->pathAndFile;
    }
}
