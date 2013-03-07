<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Model Route Generator
 *
 * RouteGenerator.php - Route-Generator for routes of libs.
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
 * @package    DoozR_Model
 * @subpackage DoozR_Model_Doodi
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

/**
 * DoozR Model Route Generator
 *
 * Route-Generator for routes of libs.
 *
 * @category   DoozR
 * @package    DoozR_Model
 * @subpackage DoozR_Model_Doodi
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class Route_Generator
{
    /**
     * type for static methods
     *
     * @var integer
     * @access const
     */
    const METHOD_TYPE_STATIC        = 1;

    /**
     * type for static non-static methods
     *
     * @var integer
     * @access const
     */
    const METHOD_TYPE_NONSTATIC     = 2;


    /**
     * constructor
     *
     * This method is the constructor
     *
     * @param string  $name           The name of the Lib/Orm/Framework to create route for
     * @param string  $root           The path to DoozR
     * @param string  $path           The path to lib (relative from $root)
     * @param string  $file           Name of the file to create route in
     * @param boolean $routeNonStatic TRUE = route all methods || FALSE (default) route only static
     * @param boolean $recursive      TRUE to parse subfolder recursive as well, otherwise FALSE to do not
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function __construct(
        $name,
        $root = DOOZR_DOCUMENT_ROOT,
        $path = '',
        $file = 'Route.php',
        $routeNonStatic = false,
        $recursive = true
    ) {
        // collect all files from path + recursive directories if $recursive is true
        $files = $this->_collectFiles($root, $path, $recursive);

        // parse out the classes contained in collected files
        $classes = $this->_parseClasses($files);

        // create route from previously collected classes
        $route = $this->_createRoute($classes, $routeNonStatic);

        // write route to file
        $success = $this->_writeRouteToFile($name, $route, $root, $path, $file);

        // last unicorn
        if (!$success) {
            throw new Exception(
                'Everything seems to be fine but file could not be created???'
            );
        }
    }

    /**
     * collects all PHP files from a given path
     *
     * This method is intend to collect all PHP-files from a given path
     *
     * @param string  $root      The root folder to use
     * @param string  $path      The path to collect files in
     * @param boolean $recursive TRUE to collect recursive or FALSE to do not
     *
     * @return  void
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _collectFiles($root, $path = '', $recursive = true)
    {
        // $
        $result = array();

        // combine values to absolute path
        $path = $root.$path;

        // check permissions
        if (!is_readable($path) || !($pathHandle = opendir($path))) {
            throw new Exception(
                'Can\'t read directory "'.$path.'". Check permissions and path.'
			);
        }

        // get files
        while ($file = readdir($pathHandle)) {
            // skip . + ..
            if ($file == '.' || $file == '..') {
                continue;
            }

            // complete path and file
            $file = $path.$file;

            // if is directory and recursive true
            if (is_dir($file) && $recursive) {
                $result = array_merge($result, $this->_collectFiles($file.'/', '', $recursive));
                continue;
            }

            if (substr($file, -4, 4) == '.php') {
                // store found files
                $result[] = $file;
            }
        }

        // close handler
        closedir($pathHandle);

        // flush the disk state cache
        clearstatcache();

        // return the result of op
        return $result;
    }

    /**
     * parses all classes in a given PHP-file
     *
     * This method is intend to parse all classes in a given PHP-file
     *
     * @param array $files An array containng absolute paths to one or more file(s)
     *
     * @return  void
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _parseClasses(array $files)
    {
        // assume empty result
        $classesByFile = array();

        // iterate over files and search for classes in every
        foreach ($files as $file) {
            // get source of file
            $source = file_get_contents($file);

            // parse out classes
            $classes = $this->_parseClassesFromSource($source);

            // store in result
            $classesByFile[$file] = $classes;
        }

        // return result
        return $classesByFile;
    }

    /**
     * creates a route class from array of classes
     *
     * This method is intend to dynamically create a PHP-routing-class by a given array of classes.
     *
     * @param array   $classes        The classes to create route class for
     * @param boolean $routeNonStatic TRUE to read the non-static methods as well
     *
     * @return  void
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _createRoute(array $classes, $routeNonStatic = false)
    {
        // assume empty result
        $route = array();

        // iterate over found files/classes
        foreach ($classes as $file => $classesInFile) {
            // include the file
            include_once $file;

            // iterate over classes
            foreach ($classesInFile as $class) {
                // reflect
                $reflection = new ReflectionClass($class);

                // get all static + public methods
                $methods = $reflection->getMethods();

                // iterate methods
                foreach ($methods as $methodObject) {
                    // get required information
                    $method = $methodObject->getName();

                    // exclude all non-accessible-from-outside and magic methods from processing
                    if ($methodObject->isPublic()
                        && !$methodObject->isAbstract()
                        && !$methodObject->isDestructor()
                        && (substr($method, 0, 2) != '__')
                    ) {
                        // get additional data
                        $class = $methodObject->getDeclaringClass()->name;

                        // static?
                        if ($methodObject->isStatic()) {
                            // meta information
                            $meta = $methodObject->export($methodObject->getDeclaringClass()->name, $method, true);

                            $type = self::METHOD_TYPE_STATIC;
                            $isConstructor = $this->_checkForConstructor($meta, $class);

                        } elseif ($routeNonStatic === true) {
                            $type = self::METHOD_TYPE_STATIC;
                            $isConstructor = $methodObject->isConstructor();

                        }

                        // check already exists -> if so then need new logic
                        if (isset($existingRoute[$methodObject])) {
                            throw new Exception(
            					'Error! Key: "'.$method.'" does already exist: "'.
                                var_export($existingRoute[$method], true).
                                '". Do we need a new decorator interface?! Seems so :('
                            );
                        }

                        // proceed ...
                        $route[$this->_getMethodHash($class, $method)] = $this->_prepareMethodData(
                            $class,
                            $method,
                            $type,
                            $isConstructor
                        );
                    }
                }
            }
        }

        // return result of process
        return $route;
    }

    /**
     * checks if a given method creates/returns an instance of the class in any way
     *
     * This method is intend to check if a given method creates/returns an instance of the class in any way.
     *
     * @param object $meta  The metadata from reflection
     * @param string $class The name of the class as string
     *
     * @return  boolean TRUE if the method is a constructor, otherwise FALSE
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _checkForConstructor($meta, $class)
    {
        $start = strpos($meta, '@@');
        $end   = strpos($meta, "\n", $start) - 2;

        $meta = substr($meta, $start+3, $end - $start);
        $meta = str_replace(' - ', '-', $meta);
        $meta = explode(' ', $meta);
        $meta[1] = explode('-', $meta[1]);

        $source = file($meta[0]);

        $result = false;

        for ($i = $meta[1][0]; $i <= $meta[1][1]; ++$i) {
            // check
            if (stristr($source[$i], 'new self')
                || stristr($source[$i], 'new '.$class)
                || stristr($source[$i], 'new $called')
            ) {
                $result = true;
            }
        }

        unset($source, $meta);

        return $result;
    }

    /**
     * prepares data of a method for adding it to routes
     *
     * This method is intend to prepare data of a method for adding it to routes.
     *
     * @param string  $class       The class which contains the method
     * @param string  $method      The method name
     * @param string  $type        The type of the method (static or non-static)
     * @param boolean $constructor TRUE if method is constructor, FALSE if not
     *
     * @return  array The array with the prepared data
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _prepareMethodData($class, $method, $type = self::METHOD_TYPE_STATIC, $constructor = false)
    {
        return array(
    		'class'       => $class,
        	'method'      => $method,
            'type'        => ($type == self::METHOD_TYPE_STATIC) ? 'static' : 'instance',
            'constructor' => $constructor
        );
    }

    /**
     * returns unique-Id (hash) for a class-method
     *
     * This method is intend to return a unique-Id (hash) for a class-method.
     *
     * @param string $class  The class which contains the method
     * @param string $method The name of the method
     *
     * @return  array The array with the prepared data
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _getMethodHash($class, $method)
    {
        return md5($class.$method);
    }

    /**
     * returns all classes found in given source as array
     *
     * This method is intend to return all classes found in given source as array.
     *
     * @param string $source The source to search classes in
     *
     * @return  array The array with the prepared data
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _parseClassesFromSource($source = '')
    {
        // assume empty result
        $classes = array();

        // retrieve tokens from source
        $tokens = token_get_all($source);
        $count = count($tokens);

        // iterate over tokens and collect classes
        for ($i = 2; $i < $count; ++$i) {
            if ($tokens[$i - 2][0] == T_CLASS
                && $tokens[$i - 1][0] == T_WHITESPACE
                && $tokens[$i][0] == T_STRING
            ) {
                $className = $tokens[$i][1];
                $classes[] = $className;
            }
        }

        return $classes;
    }

    /**
     * writes a new route to a new file
     *
     * This method is intend to write a new route to a new file.
     *
     * @param string  $name               The name of the Lib (used for Classname)
     * @param string  $route              The route to write down to file
     * @param string  $root               The root directory
     * @param string  $path               The path relative to root (used for Classname too!)
     * @param string  $file               The name of the file to create
     * @param boolean $overwrite          TRUE to overwrite an existing route, false to do not
     * @param string  $namespaceSeparator The namespace separator
     * @param string  $translateNamespace The translation for namespace separator
     *
     * @return  boolean TRUE on success, otherwise FALSE
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _writeRouteToFile(
        $name,
        $route,
        $root,
        $path,
        $file,
        $overwrite = false,
        $namespaceSeparator = '/',
        $translateNamespace = '_'
	) {
        // is folder writable
        if (!is_writable($root.$path)) {
            throw new Exception(
                'Can\'t write file: "'.$file.'" to directory: "'.$path.'". Check permissions and path.'
			);
        }

        // does file already exist? -> only manually delete!
        if (file_exists($root.$path.$file) && !$overwrite) {
            throw new Exception(
                'Can\'t write file "'.$root.$path.$file.'". File already exists. Please remove file first.'
			);
        }

        // build classname
        $classname = $path.'Route';

        if ($translateNamespace) {
            $classname = str_replace($namespaceSeparator, $translateNamespace, $classname);
        }

        // get total count of routes
        $routes = count($route);
        $i = 0;

        // begin of file
        $phpcode  = '<'.'?'."php\n\nfinal class ".$classname."\n{";
        $phpcode .=  "\n\t";
        $phpcode .=  'public $matrix = array(';

        // create content iterative
        foreach ($route as $uid => $currentRoute) {
            ++$i;
            $phpcode .= "\n\t";
            $phpcode .= "\t'$uid' => array(";
            $phpcode .= "\n\t\t\t'class'       => '".$currentRoute['class']."',";
            $phpcode .= "\n\t\t\t'method'      => '".$currentRoute['method']."',";
            $phpcode .= "\n\t\t\t'type'        => '".$currentRoute['type']."',";
            $phpcode .= "\n\t\t\t'constructor' => ".(($currentRoute['constructor']) ? 'true' : 'false').",";
            $phpcode .= "\n\t\t\t'instance'    => ".'null';
            $phpcode .= "\n\t\t)";
            ($i < $routes) ? $phpcode .= ',' : null;
        }

        // eof
        $phpcode .= "\n\t);";
        $phpcode .= "\n}\n";
        $phpcode .= "\n";
        $phpcode .= '?>';
        $phpcode .= "\n";

        // put content to file
        return (file_put_contents($root.$path.'Route.php', $phpcode) !== false);
    }

}

?>
