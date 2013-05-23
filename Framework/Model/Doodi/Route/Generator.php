<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doodi - Route - Generator
 *
 * Generator.php - Generator for routes and proxy classes for new Libs to use
 * with Doodi.
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
 * Doodi - Route - Generator
 *
 * Generator for routes and proxy classes for new Libs to use with Doodi.
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
class Doodi_Route_Generator
{
    /**
     * Basic replacements for templates
     *
     * @var array
     * @access private
     * @static
     */
    private static $_templateVariables = array();

    /**
     * The configuration of the current session
     *
     * @var array
     * @access private
     */
    private $_config = array();

    /**
     * The collection of files found while scanning input library
     *
     * @var array
     * @access private
     */
    private $_files;

    /**
     * The collection of classes found while scanning input files
     *
     * @var array
     * @access private
     */
    private $_classes;

    /**
     * type for static methods
     *
     * @var integer
     * @access const
     */
    const METHOD_TYPE_STATIC = 1;

    /**
     * type for static non-static methods
     *
     * @var integer
     * @access const
     */
    const METHOD_TYPE_NONSTATIC = 2;

    /**
     * The name of the layering framework
     *
     * @var string
     * @access const
     */
    const NAME = 'Doodi';

    /**
     * Default separator for our target namespace (Foo_Bar)
     *
     * @var string
     * @access const
     */
    const DEFAULT_NAMESPACE_SEPARATOR = '_';

    /**
     * Default separator for directories (Foo/Bar)
     *
     * @var string
     * @access const
     */
    const DEFAULT_DIRECTORY_SEPARATOR = DIRECTORY_SEPARATOR;

    /**
     * Default pattern for RegExp operations:
     * Split by Underscore "_"
     *
     * @var string
     * @access const
     */
    const SPLIT_BY_UNDERSCORE = '/([\_])/';

    /**
     * Default pattern for RegExp operations:
     * Split by Camelcase "fooBar"
     *
     * @var string
     * @access const
     */
    const SPLIT_BY_CAMELCASE  = '/(?=[A-Z])/';


    /*******************************************************************************************************************
     * PUBLIC API
     ******************************************************************************************************************/

    /**
     * This method is the constructor
     *
     * @param string  $name                   The name of the proxy
     * @param string  $pathLibrary            The path to the library (relative to $pathDocumentRoot)
     * @param string  $pathOutput             The path where to store the proxies (relative to $pathDocumentRoot)
     * @param mixed   $excludePattern         An optional RegExp pattern used for excluding elements on class level
     * @param mixed   $bootstrap              An optional bootstrapping script if required to parse classes of library
     * @param string  $directorySeparator     The directory separator for current OS
     * @param string  $namespaceSeparator     The namespace separator used for concatenating classname elements
     * @param booelan $scanRecursive          TRUE to scan directory recursive, FALSE to scan only first level
     * @param boolean $routeAllMethods        TRUE to route all methods || FALSE (default) route only static
     * @param string  $pathDocumentRoot       DoozR's document root (the root folder)
     * @param string  $filenameRoute          The filename of the route
     * @param string  $filenameBootstrap      The name of the Bootstrap.php
     * @param string  $filenameTransformation The filename for transformation matrix
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __construct(
        $name,
        $oxm,
        $pathLibrary,
        $pathOutput,
        $bootstrap = null,
        $excludePattern = null,
        $namespacePattern = self::SPLIT_BY_UNDERSCORE,
        $directorySeparator = self::DEFAULT_DIRECTORY_SEPARATOR,
        $namespaceSeparator = self::DEFAULT_NAMESPACE_SEPARATOR,
        $scanRecursive = true,
        $routeAllMethods = false,
        $pathDocumentRoot = DOOZR_DOCUMENT_ROOT
    ) {
        // add passed path' to include path for processing
        set_include_path(
            get_include_path().PATH_SEPARATOR.realpath($pathDocumentRoot.$pathLibrary)
        );

        // get bootstrapper if passed -> required to operate
        if ($bootstrap) {
            include_once $pathDocumentRoot.$pathLibrary.$oxm.$directorySeparator.$bootstrap;
        }

        // store information
        $this->set('name', $name);
        $this->set('oxm', $oxm);
        $this->set('pathLibrary', $pathLibrary.$oxm.DIRECTORY_SEPARATOR);
        $this->set('pathOutput', $pathOutput);
        $this->set('excludePattern', '/'.$excludePattern.'/i');
        $this->set('namespacePattern', $namespacePattern);
        $this->set('directorySeparator', $directorySeparator);
        $this->set('namespaceSeparator', $namespaceSeparator);
        $this->set('scanRecursive', $scanRecursive);
        $this->set('routeAllMethods', $routeAllMethods);
        $this->set('pathDocumentRoot', $pathDocumentRoot);

        // chaining support
        return $this;
    }

    /**
     * Start processing input. We don't require any further information.
     * This method is the endpoint of chaining support and executes the stack.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if the whole run was successful, otherwise FALSE
     * @access public
     * @throws Exception
     */
    public function run()
    {
        // collect all files from path + recursive directories if $scanRecursive is true
        $this->_files = $this->_collectFiles(
            $this->get('pathDocumentRoot').$this->get('pathLibrary'),
            $this->get('scanRecursive')
        );

        // parse out the classes contained in collected files
        $this->_classes = $this->_parseClasses($this->_files, $this->get('excludePattern'));

        // retrieve success state for all operations
        $success = (
            // create proxies
            $this->_createProxies(
                $this->get('name'),
                $this->get('pathOutput'),
                $this->_classes,
                $this->get('pathDocumentRoot'),
                $this->get('directorySeparator'),
                $this->get('namespaceSeparator'),
                $this->get('namespacePattern')
            ) &&

            // create route
            $this->_createRoute(
                $this->get('name'),
                $this->get('pathOutput'),
                $this->_classes,
                $this->get('pathDocumentRoot'),
                $this->get('directorySeparator'),
                $this->get('namespaceSeparator'),
                $this->get('namespacePattern')
            ) &&

            // create Bootstrapper
            $this->_createBootstrap(
                $this->get('name'),
                $this->get('pathOutput'),
                $this->_files,
                $this->get('pathDocumentRoot')
            ) &&

            // create Transformation matrix file
            $this->_createTransform()
        );

        // if not successful => Rollback + Exception!
        if (!$success) {
            throw new Exception(
                'Proxy could not be created! I\'ve tried to rollback the changes.'
            );
        }

        return $success;
    }

    /**
     * This method is intend to set a config-variable and its value.
     *
     * @param string $variable The variable name
     * @param string $value    The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doodi_Route_Generator The current instance for chaining
     * @access public
     */
    public function set($variable, $value = null)
    {
        $this->_config[$variable] = $value;
        return $this;
    }

    /**
     * This method is intend to get the value from a config-variable.
     *
     * @param string $variable The variable name
     * @param string $value    The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The value of the config-variable
     * @access public
     * @throws Exception
     */
    public function get($variable)
    {
        if (!isset($this->_config[$variable])) {
            throw new Exception('Config entry: "'.$variable.'" does not exist!');
        }

        return $this->_config[$variable];
    }

    /*******************************************************************************************************************
     * BOOTSTRAP
     ******************************************************************************************************************/

    /**
     * This method creates the Bootstrap(.php)
     * Bootstrap.php contains the originals library Bootstrapper code
     * if this could be parsed + and our Bootstrapping code which adds
     * the proxies and library classes to DoozR's autoloading registry
     * in priorized order.

     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access private
     */
    private function _createBootstrap(
        $name,
        $pathOutput,
        array $files,
        $pathDocumentRoot
    ) {
        // assume we will work with empty
        $content = '';

        // output filename
        $filename = 'Bootstrap.php';

        // bootstrapper files we recognize
        $bootstrapperFiles = array(
            'bootstrapper.php',
            'Bootstrapper.php',
            'bootstrap.php',
            'Bootstrap.php'
        );

        // iterate list of files to look for bootsrapper(s)
        foreach ($files as $fileAndPath) {
            $file = basename($fileAndPath);
            if (in_array($file, $bootstrapperFiles)) {
                $content .= "\n".file_get_contents($fileAndPath);
            }
        }

        $filename = $this->_getAbsolutePath($pathOutput).$name.DIRECTORY_SEPARATOR.$filename;

        // now we have the content of the bootstrapper
        file_put_contents($filename, $content);

        // success
        return true;
    }

    /*******************************************************************************************************************
     * TRANSFORMATION
     ******************************************************************************************************************/

    /**
     * This method creates the Transformation(.php)
     * Bootstrap.php contains the originals library Bootstrapper code
     * if this could be parsed + and our Bootstrapping code which adds
     * the proxies and library classes to DoozR's autoloading registry
     * in priorized order.

     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access private
     */
    private function _createTransform()
    {
        $filename = 'Transform.php';
        return true;
    }

    /*******************************************************************************************************************
     * PROXY
     ******************************************************************************************************************/

    private function _getTargetClassname($name, $classname, $pattern, $separator)
    {
        // storage for filtered result
        $targetClassname = array();

        // split current classname by same pattern as before
        $classnameParts = preg_split($pattern, $classname);

        // iterate and filter out base namespace by default
        foreach ($classnameParts as $classnamePart) {
            if ($classnamePart != $this->get('oxm')) {
                $targetClassname[] = $classnamePart;
            }
        }

        // combine = construct classname and return
        return self::NAME.$separator.$name.$separator.implode($separator, $targetClassname);
    }

    /**
     * This method creates the proxy classes and writes them to filesystem.
     *
     * @param string $name               The name of the proxy
     * @param string $pathOutput         The path where to put the result
     * @param array  $collection         The collection of classes
     * @param string $pathDocumentRoot   The document root path
     * @param string $directorySeparator The directory separator
     * @param string $namespaceSeparator The namespace separator
     * @param string $namespacePattern   The namespace pattern
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The code of the template
     * @access private
     */
    private function _createProxies(
        $name,
        $pathOutput,
        array $collection,
        $pathDocumentRoot,
        $directorySeparator,
        $namespaceSeparator,
        $namespacePattern
    ) {
        // pre setup
        $temporaryFileContent = '';
        $templateDocblock     = $this->_loadTemplate('Docblock', $directorySeparator);
        $templateClass        = $this->_loadTemplate('Class', $directorySeparator);
        $outputFolder         = $pathDocumentRoot.$pathOutput.$name.$directorySeparator;

        // setup runtime template variables
        self::$_templateVariables['YEAR'] = date('Y');
        self::$_templateVariables['name'] = $name;

        // relative output path
        $outputFolderRelative = str_replace($pathDocumentRoot, '', $outputFolder);

        // iterate over collected files ...
        foreach ($collection as $file => $classes) {
            // ... then on over each of its classes
            foreach ($classes as $classname) {

                // get some additional and important information!
                $reflection = new ReflectionClass($classname);

                // get all static + public methods
                $abstract = $reflection->isAbstract();
                $final    = $reflection->isFinal();

                if ($abstract === true) {
                    $classType = 'abstract class';
                } elseif ($final === true) {
                    $classType = 'final class';
                } else {
                    $classType = 'class';
                }

                $targetClassname = $this->_getTargetClassname(
                    $name,
                    $classname,
                    $namespacePattern,
                    $namespaceSeparator
                );

                self::$_templateVariables['classname']       = $classname;
                self::$_templateVariables['class-type']      = $classType;
                self::$_templateVariables['splitted-name']   = implode(' - ', preg_split($namespacePattern, $classname));
                self::$_templateVariables['require']         = "\n".'require_once DOOZR_DOCUMENT_ROOT.\''.$this->_linuxPath((str_replace($pathDocumentRoot, '', $file))).'\';';
                self::$_templateVariables['filename']        = basename($file);
                self::$_templateVariables['bootstrap']       = "\n".'include_once DOOZR_DOCUMENT_ROOT.\''.$this->_linuxPath($outputFolderRelative).'Bootstrap.php\';'."\n";
                self::$_templateVariables['classname-doodi'] = $targetClassname;

                $temporaryFileContent = $this->_parseTemplate($templateDocblock, self::$_templateVariables);
                self::$_templateVariables['docblock.tpl']    = $temporaryFileContent;
                $temporaryFileContent = $this->_parseTemplate($templateClass, self::$_templateVariables);

                $classname = $targetClassname; //$this->_convertToTargetNamespace($classname, $namespacePattern);

                $filename = str_replace($namespaceSeparator, $directorySeparator, str_replace(self::NAME.$namespaceSeparator.$name.$namespaceSeparator, '', $classname)).'.php';
                $filename = $pathDocumentRoot.'Model'.$directorySeparator.self::NAME.$directorySeparator.$name.$directorySeparator.$filename;

                // get real path
                $directoryName = dirname($filename);

                // create folder if not exist
                if (!is_dir($directoryName)) {
                    mkdir($directoryName, 0777, true);
                }

                file_put_contents($filename, $temporaryFileContent);
            }
        }

        return true;
    }

    /*******************************************************************************************************************
     * ROUTE
     ******************************************************************************************************************/

    /**
     * This method takes a collection of routing information and writes the route to a file.
     *
     * @param string  $name               The name of the proxy
     * @param array   $collection         The collection of classes
     * @param string  $pathDocumentRoot   The document root
     * @param string  $path               The path where to write the resulting route
     * @param string  $file               The name of the file to create
     * @param string  $directorySeparator The namespace separator
     * @param string  $namespaceSeparator The translation for namespace separator
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access private
     */
    private function _createRoute(
        $name,
        $pathOutput,
        array $collection,
        $pathDocumentRoot,
        $directorySeparator,
        $namespaceSeparator,
        $namespacePattern
    ) {
        // pre setup
        $filename = 'Route.php';

        // convert passed collection to required route information
        $collection = $this->_collectInformation($collection, $this->get('routeAllMethods'));

        // build classname of Route
        $classname = self::NAME.$namespaceSeparator.$name.$namespaceSeparator.'Route';

        $temporaryFileContent = '';
        $pathOutput           = $pathDocumentRoot.$pathOutput.$name.$directorySeparator;

        // check for permissions
        $this->_checkFileAndFolderPermissions($pathOutput, array($pathOutput.$filename));

        // prepare template variables
        self::$_templateVariables['classname'] = $classname;

        // load templates
        $route        = $this->_loadTemplate('Route', $directorySeparator);
        $routeElement = $this->_loadTemplate('Routeelement', $directorySeparator);
        $docblock     = $this->_loadTemplate('Docblock', $directorySeparator);

        // iterate route elements and build route-array
        foreach ($collection as $uid => $currentRoute) {
            self::$_templateVariables['uid'] = $uid;
            self::$_templateVariables['classname-routeelement'] = $currentRoute['class'];
            self::$_templateVariables['method'] = $currentRoute['method'];
            self::$_templateVariables['type'] = $currentRoute['type'];
            self::$_templateVariables['is-constructor'] = ($currentRoute['constructor']) ? 'true' : 'false';

            $temporaryFileContent .= $this->_parseTemplate($routeElement, self::$_templateVariables);
        }

        self::$_templateVariables['route']              = $temporaryFileContent;
        self::$_templateVariables['date']               = date('Y-m-d H:i:s');
        self::$_templateVariables['generator-filename'] = basename(__FILE__);
        self::$_templateVariables['filename']           = $filename;
        self::$_templateVariables['classname-route']    = $classname;
        self::$_templateVariables['splitted-name']      = str_replace($namespaceSeparator, ' - ', $classname);
        self::$_templateVariables['require']            = '';
        self::$_templateVariables['bootstrap']          = '';

        $temporaryFileContent = $this->_parseTemplate($docblock, self::$_templateVariables);
        self::$_templateVariables['docblock.tpl'] = $temporaryFileContent;

        $temporaryFileContent = $this->_parseTemplate($route, self::$_templateVariables);

        // put content to file
        return (file_put_contents($pathOutput.$filename, $temporaryFileContent) !== false);
    }

    /*******************************************************************************************************************
     * TEMPLATES
     ******************************************************************************************************************/

    /**
     * Loads a template file from filesystem and returns it content.
     *
     * @param string $templateName       The name of the template (file without extension)
     * @param string $directorySeparator The current OS' directory separator
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The code of the template
     * @access private
     */
    private function _loadTemplate($templateName, $directorySeparator)
    {
        $templateFilename = ucfirst(strtolower($templateName)).'.tpl';
        $templatePath     = dirname(__FILE__).$directorySeparator.'Template'.$directorySeparator;
        $templateCode     = file_get_contents($templatePath.$templateFilename);
        return $templateCode;
    }

    /**
     * Parses the variables from template and insert its replacements
     *
     * @param string $template The template as string
     * @param string $scope    The scope variables for replacements
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The parsed/processed code of the template
     * @access private
     */
    private function _parseTemplate($template, $scope)
    {
        foreach ($scope as $from => $to) {
            $template = str_replace('{{'.$from.'}}', $to, $template);
        }

        return $template;
    }

    /*******************************************************************************************************************
     * TOOLS
     ******************************************************************************************************************/

    /**
     * This method is intend to dynamically create a PHP-routing-class by a given array of classes.
     *
     * @param array   $classes         The classes to create route class for
     * @param boolean $routeAllMethods TRUE to read the non-static methods as well
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _collectInformation(array $classes, $routeAllMethods = false)
    {
        // assume empty result
        $route = array();

        // assume type is non static
        $type = self::METHOD_TYPE_NONSTATIC;

        // assume this method is not true
        $isConstructor = false;

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

                        } elseif ($routeAllMethods === true) {
                            $type = self::METHOD_TYPE_STATIC;
                            $isConstructor = $methodObject->isConstructor();

                        }

                        // check already exists -> if so then need new logic
                        if (isset($existingRoute[$methodObject])) {
                            throw new Exception(
                                'Error! Key: "'.$method.'" does already exist: "'.
                                var_export($existingRoute[$method], true).
                                '". Maybe we need a new decorator interface!'
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
     * This method converts the slashes in a passed path
     * to linux style slashes (/)
     *
     * @param string $path The path to convert slashes in
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The correct formatted path
     * @access private
     */
    private function _linuxPath($path)
    {
        return str_replace('\\', '/', $path);
    }

    /**
     * This method checks for correct file and folder permissions
     *
     * @param string  $path      The path to check/create
     * @param array   $filenames A single/or list of file(s) to check for write access
     * @param boolean $overwrite TRUE to weaker check write access (overwrite allowed)
     * @param boolean $create    TRUE to create ...
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The array with the prepared data
     * @access private
     */
    private function _checkFileAndFolderPermissions(
        $path,
        array $filenames = array(),
        $overwrite = false,
        $create = true
    ) {
        // try to create output folder if not exist
        if (!file_exists($path) && $create === true) {
            if (!mkdir($path)) {
                throw new Exception(
                    'Can\'t create directory: "'.$path.'". Check permissions and path.'
                );
            }
        }

        // is folder writable
        if (!is_writable($path)) {
            throw new Exception(
                'Can\'t write file to directory: "'.$path.'". Check permissions and path.'
            );
        }

        // does file already exist? -> only manually delete!
        foreach ($filenames as $filename) {
            if (file_exists($filename) && !$overwrite) {
                throw new Exception(
                    'Can\'t write file "'.$filename.'". File already exists. Please remove file first.'
                );
            }
        }
    }

    /**
     * This method converts a passed identifier (e.g. classname)
     * to Doodi's default namespace
     *
     * @param string $identifier         The identifier to convert to target namespace
     * @param string $namespacePattern   The pattern to split input by
     * @param string $namespaceSeparator The namespace separator to insert
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The converted input string
     * @access private
     */
    private function _convertToTargetNamespace($identifier, $namespacePattern, $namespaceSeparator = '_')
    {
        $identifierParts = preg_split($namespacePattern, $identifier);
        $result = implode($namespaceSeparator, $identifierParts);

        return $result;
    }

    /**
     * This method returns the absolute path with passed relative path attached.
     *
     * @param string  $relativePath  The relative path to combine with absolute path
     * @param boolean $trailingSlash TRUE to add a trailing slash to path, FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The absolute path
     * @access private
     */
    private function _getAbsolutePath($relativePath, $trailingSlash = true)
    {
        $path = $this->get('pathDocumentRoot').$relativePath;

        if ($trailingSlash === true && substr($path, -1, 1) !== self::DEFAULT_DIRECTORY_SEPARATOR) {
            $path .= self::DEFAULT_DIRECTORY_SEPARATOR;
        }

        return $path;
    }

    /**
     * This method is intend to collect all PHP-files from passed path
     *
     * @param string  $path      The path to collect files in
     * @param boolean $recursive TRUE to collect recursive or FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _collectFiles($path = '/', $recursive = true)
    {
        $result = array();

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
                $result = array_merge($result, $this->_collectFiles($file.'/', $recursive));
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
     * This method is intend to parse an array of files for PHP-classes
     *
     * @param array  $files          An array containng absolute path(s) to one or more file(s)
     * @param string $excludePattern An optional pattern to exlcude classes by pattern (e.g. to block Test classes)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _parseClasses(array $files, $excludePattern = null)
    {
        // assume empty result
        $classesByFile = array();

        // iterate over files and search for classes in every
        foreach ($files as $file) {
            // get source of file
            $source = file_get_contents($file);

            // parse out classes
            $classes = $this->_parseClassesFromSource($source);

            foreach ($classes as $index => $classname) {
                if (preg_match_all($excludePattern, $classname) > 0) {
                    unset($classes[$index]);
                }
            }

            // add only non empty to result
            if (count($classes) > 0) {
                $classesByFile[$file] = $classes;
            }
        }

        // return result
        return $classesByFile;
    }

    /**
     * This method is intend to check if a given method creates/returns an instance of the class in any way.
     *
     * @param object $meta  The metadata from reflection
     * @param string $class The name of the class as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if the method is a constructor, otherwise FALSE
     * @access private
     */
    private function _checkForConstructor($meta, $class)
    {
        $start = strpos($meta, '@@');
        $end   = strpos($meta, "\n", $start) - 2;

        /*
        $meta = substr($meta, $start+3, $end - $start);
        $meta = str_replace(' - ', '-', $meta);
        $meta = explode(' ', $meta);
        $meta[1] = explode('-', $meta[1]);
        */

        $meta = substr($meta, $start+3, $end - $start);
        $meta = str_replace(' - ', '-', $meta);
        $file = trim(substr($meta, 0, strrpos($meta, '.php')+4));
        $meta = str_replace($file.' ', '', $meta);
        $meta = str_replace('&#10;', '', $meta);
        $meta = array($file, explode('-', $meta));

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
     * This method is intend to prepare data of a method for adding it to routes.
     *
     * @param string  $class       The class which contains the method
     * @param string  $method      The method name
     * @param string  $type        The type of the method (static or non-static)
     * @param boolean $constructor TRUE if method is constructor, FALSE if not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The array with the prepared data
     * @access private
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
     * This method is intend to return a unique-Id (hash) for a class-method.
     *
     * @param string $class  The class which contains the method
     * @param string $method The name of the method
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The array with the prepared data
     * @access private
     */
    private function _getMethodHash($class, $method)
    {
        return md5($class.$method);
    }

    /**
     * This method is intend to return all classes found in given source as array.
     *
     * @param string $source The source to search classes in
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The array with the prepared data
     * @access private
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
}

?>
