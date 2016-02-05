<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Path.
 *
 * Path.php - This is the Path-Manager of Doozr and it is intend for retrieving and setting paths.
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
 * @link       http://clickalicious.github.com/Doozr/
 */
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Class/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Path/Interface.php';

/**
 * Doozr - Path.
 *
 * This is the Path-Manager of Doozr and it is intend for retrieving and setting paths.
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
class Doozr_Path extends Doozr_Base_Class_Singleton
    implements
    Doozr_Path_Interface
{
    /**
     * The frameworks default paths.
     *
     * @var array
     * @static
     */
    protected static $path = [];

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Add a path to PHP's include paths.
     *
     * @param string $path The path which should be added to include path's
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @static
     */
    public static function addIncludePath($path)
    {
        set_include_path(get_include_path().self::buildPath($path));
    }

    /**
     * Removes a path from PHP's include paths.
     *
     * @param string $path The path which should be removed from include path's
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @static
     */
    public static function removeIncludePath($path)
    {
        set_include_path(str_replace($path.PATH_SEPARATOR, '', get_include_path()));
    }

    /**
     * Returns path by identifier (e.g. 'temp').
     *
     * @param string $identifier    Path which should be returned
     * @param string $add           Extension to the path requested
     * @param bool   $trailingSlash TRUE to add a trailing slash, FALSE to do not (default)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The path requested
     * @static
     */
    public function get($identifier, $add = '', $trailingSlash = false)
    {
        // Prepare identifier for internal use (remove prefix!)
        $identifier = str_replace('doozr_', '', strtolower($identifier));

        // Throw an exception if identifier not known
        if (false === isset(self::$path[$identifier])) {
            throw new Doozr_Path_Exception(
                sprintf('There is no path entry for identifier: "%s"', $identifier)
            );
        }

        // Check for additional path add-on
        if (strlen($add)) {
            // if defined we add the correct slashed path
            $add = self::fixPath($add);
        }

        return self::$path[$identifier].$add.(($trailingSlash) ? DIRECTORY_SEPARATOR : '');
    }

    /**
     * Register an path from external - maybe created at runtime.
     *
     * @param string $identifier Name of the path which should be set
     * @param string $path       Path which should be set
     * @param bool   $force      TRUE to force overwrite of already existing identifier, FALSE to prevent from overwrite
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     * @static
     *
     * @throws Doozr_Exception
     */
    public function set($identifier, $path, $force = false)
    {
        // Check if already exist and prevent overwrite if not force
        if ((true === isset(self::$path[$identifier])) && ((null !== self::$path[$identifier]) && (false === $force))) {
            throw new Doozr_Exception(
                sprintf(
                    'Path with identifier "%s" already defined! Set $force to TRUE to overwrite it.',
                    $identifier
                )
            );
        }

        // Create/update path
        self::$path[$identifier] = self::fixPath($path);

        return true;
    }

    /**
     * Fix slashes with "wrong" direction in a path and returns it.
     *
     * @param string $path The path to correct slashes in
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The path with corrected slash-direction
     * @static
     */
    public static function fixPath($path)
    {
        // Detect which direction of slash is "wrong"
        switch (DIRECTORY_SEPARATOR) {
            case '\\':
                $wrongDirection = '/';
                break;

            case '/':
            default:
                $wrongDirection = '\\';
                break;
        }

        if (false !== stristr($path, $wrongDirection)) {
            $path = str_replace($wrongDirection, DIRECTORY_SEPARATOR, $path);

            // In case of mixed slashes we maybe need to cleanup
            if (stristr($path, '\\\\')) {
                $path = str_replace('\\\\', '\\', $path);
            } elseif (stristr($path, '//')) {
                $path = str_replace('//', '/', $path);
            }
        }

        // Return corrected path
        return $path;
    }

    /**
     * This method is intend to merge two path-settings under consideration of ../ (n-times).
     *
     * @param string $pathBase  The path used as base
     * @param string $pathMerge The path used as extension to $pathBase
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The corrected new merged path
     */
    public function mergePath($pathBase, $pathMerge = '')
    {
        // correct the input-path settings
        $pathBase  = self::fixPath($pathBase);
        $pathMerge = self::fixPath($pathMerge);

        // holds the new constructed path
        $newPath = '';

        // define replace patterns
        $pattern_1 = '..'.DIRECTORY_SEPARATOR;

        //check for pattern_1 = '../'
        if (stristr($pathMerge, $pattern_1)) {
            // count pattern matches
            $patternCount = substr_count($pathMerge, $pattern_1);

            // now we remove the .. from e.g. ../abc/
            $pathMerge = str_replace($pattern_1, '', $pathMerge);

            // if we remove ../ from pathMerge then we need to
            // replace the last folder from pathBase
            $tmpPathBase = explode(DIRECTORY_SEPARATOR, $pathBase);

            // remove empty end-of-array (eoa)
            if (empty($tmpPathBase[count($tmpPathBase) - 1])) {
                array_pop($tmpPathBase);
            }

            // remove n elements (representation of ../ found in pathMerge)
            for ($i = 0; $i < $patternCount; ++$i) {
                array_pop($tmpPathBase);
            }

            // put path onto the remaining elements!
            array_push($tmpPathBase, $pathMerge);

            $newPath = implode(DIRECTORY_SEPARATOR, $tmpPathBase).
                       ((substr($pathMerge, -1, 1) != DIRECTORY_SEPARATOR) ? DIRECTORY_SEPARATOR : '');
        } else {
            $newPath = $pathBase;
        }

        // return new constructed path
        return $newPath;
    }

    /**
     * Converts a given service name to its path representation.
     *
     * @param string $serviceName The name of the service to retrieve the path for
     * @param string $scope       The namespace to use for building path to service
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The path requested
     * @static
     *
     * @deprecated
     */
    public static function serviceToPath($serviceName, $scope = DOOZR_NAMESPACE)
    {
        $service = ucfirst(str_replace('_', DIRECTORY_SEPARATOR, $serviceName));

        return self::fixPath(
            self::$path['service'].$scope.DIRECTORY_SEPARATOR.$service.DIRECTORY_SEPARATOR
        );
    }

    /**
     * Nice magic access to stored path's.
     *
     * @param string $method The method name
     * @param $arguments $arguments One argument to pass by would be a new path to be set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed Result depending on operation
     *
     * @throws Doozr_Path_Exception
     */
    public function __call($method, $arguments)
    {
        $method = str_split_camelcase($method);

        if (count($method) === 2) {
            $identifier = strtolower($method[1]);

            if (isset(self::$path[$identifier]) === false) {
                throw new Doozr_Path_Exception(
                    sprintf('Path "%s" does not exist!', $identifier)
                );
            } else {
                if (strtolower($method[0]) === 'get') {
                    $result = self::$path[$identifier];
                } else {
                    $result = self::$path[$identifier] = (isset($arguments[0])) ? $arguments[0] : null;
                }

                return $result;
            }
        }
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param string $pathToRoot        The path to Doozr (DOOZR_DOCUMENT_ROOT)
     * @param string $pathToApplication The path to Application's root directory
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return \Doozr_Path
     */
    protected function __construct($pathToRoot = null, $pathToApplication = null)
    {
        $this
            ->init($pathToRoot, $pathToApplication);
    }

    /**
     * initializes all required operations.
     *
     * This method is intend to begin retrieving the current document root of Doozr. Afterwards
     * it retrieves the path to the Application and setup all required default and include paths.
     *
     * @param string|null $pathToRoot        Path to Doozr (DOOZR_DOCUMENT_ROOT)
     * @param string|null $pathToApplication Path to applications root directory
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function init($pathToRoot, $pathToApplication)
    {
        // Check for root folder runtimeEnvironment
        // null = try to detect root | true = use const DOOZR_DOCUMENT_ROOT | ELSE = take path from parameter
        switch ($pathToRoot) {
            case null:
                $pathToRoot = str_replace(
                    str_replace('_', DIRECTORY_SEPARATOR, __CLASS__).'.php',
                    '',
                    __FILE__
                );
                break;

            case 'DOOZR':
                // break intentionally omitted
            default:
                $pathToRoot = DOOZR_DOCUMENT_ROOT;
                break;
        }

        // Retrieve path to application
        if (null === $pathToApplication) {
            $pathToApplication = $this->retrievePathToApplication();
        }

        // Init important paths from framework and app, setup include paths to speedup PHPs lookups
        $this
            ->initPaths($pathToRoot, $pathToApplication)
            ->configureIncludePaths();
    }

    /**
     * Returns path to the application.
     *
     * This method is intend to return the path to the application. If it is not set
     * yet it creates the path by assuming that the Application is one level up from
     * DOOZR_DOCUMENT_ROOT
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string Path to Application
     */
    protected function retrievePathToApplication()
    {
        // If path to app was defined before return this (priority 1)
        if (false === defined('DOOZR_APP_ROOT')) {
            if (false !== $environment = getenv('DOOZR_APP_ROOT')) {
                // assume that path to application is like the default environment (one folder up)
                $path = $this->mergePath($environment, 'app/');
            } else {
                // assume that path to application is like the default environment (one folder up)
                $path = $this->mergePath(DOOZR_DOCUMENT_ROOT, '../app/');
            }

            // we need a constant of the path as counterpart to DOOZR_DOCUMENT_ROOT
            define('DOOZR_APP_ROOT', $path);
        }

        // Always use constant
        return DOOZR_APP_ROOT;
    }

    /**
     * Returns the path n levels up from input.
     *
     * @param string $path                  Input path
     * @param int    $level                 Count of levels to move up
     * @param bool   $preserveTrailingSlash TRUE to preserve trailing slash if exist, FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string Path n level up
     */
    protected function up($path, $level = 1, $preserveTrailingSlash = false)
    {
        $postfix = '';

        if (substr($path, -1, 1) === DIRECTORY_SEPARATOR) {
            ++$level;
            if ($preserveTrailingSlash) {
                $postfix = DIRECTORY_SEPARATOR;
            }
        }

        $path = explode(DIRECTORY_SEPARATOR, $path);
        $path = array_slice($path, 0, count($path) - $level);

        return implode(DIRECTORY_SEPARATOR, $path).$postfix;
    }

    /**
     * Configures the default paths of Doozr & Application.
     *
     * @param string $pathToRoot        The path to Doozr (DOOZR_DOCUMENT_ROOT)
     * @param string $pathToApplication The path to applications root directory
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function initPaths($pathToRoot, $pathToApplication)
    {
        // get absolute root
        $root = $this->up($pathToRoot, 1, true);

        self::$path['document_root'] = $root;
        self::$path['core']          = $this->combine($root, ['src', 'Doozr']);
        self::$path['framework']     = $this->combine($root, ['src']);
        self::$path['app']           = $pathToApplication;
        self::$path['model']         = $this->combine($root, ['src', 'Model']);
        self::$path['service']       = $this->combine($root, ['src', 'Service']);
        self::$path['controller']    = $this->combine($root, ['src', 'Doozr', 'Controller']);
        self::$path['data']          = $this->combine($root, ['src', 'Data']);
        self::$path['data_private']  = $this->combine($root, ['src', 'Data', 'Private']);
        self::$path['auth']          = $this->combine($root, ['src', 'Data', 'Private', 'Auth']);
        self::$path['cache']         = DOOZR_DIRECTORY_TEMP;
        self::$path['config']        = $this->combine($root, ['src', 'Data', 'Private', 'Config']);
        self::$path['font']          = $this->combine($root, ['src', 'Data', 'Private', 'Font']);
        self::$path['log']           = DOOZR_DIRECTORY_TEMP;
        self::$path['temp']          = DOOZR_DIRECTORY_TEMP;
        self::$path['data_public']   = $this->combine($pathToApplication, ['Data', 'Public']);
        self::$path['www']           = $this->combine($pathToApplication, ['Data', 'Public', 'www']);
        self::$path['upload']        = $this->combine($pathToApplication, ['Data', 'Private', 'Upload']);
        self::$path['localisation']  = $this->combine($pathToApplication, ['Data', 'Private', 'Locale']);

        return $this;
    }

    /**
     * This method is intend to return a combined path based on input.
     *
     * @param string   $base          The base path for combine operation
     * @param string[] $relativePaths The path parts as array to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The new combined path
     */
    protected function combine($base = '', array $relativePaths = [])
    {
        foreach ($relativePaths as $relativePath) {
            $base .= $relativePath.DIRECTORY_SEPARATOR;
        }

        return $base;
    }

    /**
     * Configures the include paths of PHP.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function configureIncludePaths()
    {
        // get ini include path and split it to an array
        $iniIncludePaths = explode(PATH_SEPARATOR, ini_get('include_path'));

        // default entry
        $includePathsDoozr = '.';

        // build Doozr include paths
        foreach (self::$path as $path) {
            $includePathsDoozr .= self::buildPath($path);
        }

        if (!empty($iniIncludePaths)) {
            foreach ($iniIncludePaths as $iniIncludePath) {
                // If '.' or existing path -> do not attach twice
                if (in_array($iniIncludePath, self::$path) || trim($iniIncludePath) == '.') {
                    continue;
                }

                $includePathsDoozr .= self::buildPath($iniIncludePath);
            }
        }

        // Now try to set the ini value include_path
        ini_set('include_path', $includePathsDoozr);

        return $this;
    }

    /**
     * Build valid include path.
     *
     * @param string $path The path which should be formatted as include-path
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string with the correct include path
     * @static
     */
    protected static function buildPath($path)
    {
        return PATH_SEPARATOR.$path;
    }
}
