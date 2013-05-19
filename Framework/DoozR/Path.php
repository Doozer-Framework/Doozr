<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Path
 *
 * Path.php - is the Path-Manager of the DoozR-Framework and it is intend for
 * retrieving and setting (maintaining) path's.
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
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Path
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Class/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Path/Interface.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Exception.php';

/**
 * DoozR Path
 *
 * This is the Path-Manager of the DoozR-Framework and it is intend for
 * retrieving and setting (maintaining) path's.
 *
 * @category   DoozR
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Path
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Path extends DoozR_Base_Class_Singleton implements DoozR_Path_Interface
{
    /**
     * singelton-instance-holder
     *
     * null on initialising - used to hold the instance of
     * this class for returning it on a getInstance() call
     * and to prevent multiple instances of the class
     *
     * @var object
     * @access private
     */
    private static $_instance = null;

    /**
     * The frameworks default paths
     *
     * @var array
     * @access private
     * @static
     */
    private static $_path = array();


    /**
     * constructs the class
     *
     * constructor builds the class
     *
     * @param string $pathToRoot        The path to DoozR (DOOZR_DOCUMENT_ROOT)
     * @param string $pathToApplication The path to applications root directory
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    protected function __construct($pathToRoot = null, $pathToApplication = null)
    {
        // init
        $this->_init($pathToRoot, $pathToApplication);
    }

    /**
     * initializes all required operations
     *
     * This method is intend to begin retrieving the current document root of DoozR. Afterwards
     * it retrieves the path to the application and setup all required default and include paths.
     *
     * @param string $pathToRoot        The path to DoozR (DOOZR_DOCUMENT_ROOT)
     * @param string $pathToApplication The path to applications root directory
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _init($pathToRoot, $pathToApplication)
    {
        // check for rootfolder mode
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

        // retrieve path to application
        if (!$pathToApplication) {
            $pathToApplication = $this->_retrievePathToApplication();
        }

        // init all important paths from framework and app
        $this->_initPaths($pathToRoot, $pathToApplication);

        // setup include paths to speedup php lookups
        $this->_initIncludePaths();
    }

    /**
     * returns the path to the application
     *
     * This method is intend to return the path to the application. If it is not set
     * yet it creates the path by assuming that the Application is one level up from
     * DOOZR_DOCUMENT_ROOT
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Path to Application
     * @access private
     */
    private function _retrievePathToApplication()
    {
        // if path to app was defined before return this (prio 1)
        if (!defined('DOOZR_APP_ROOT')) {
            // assume that path to application is like the default environment (one folder up)
            $path = $this->mergePath(DOOZR_DOCUMENT_ROOT, '../App/');

            // we need a constant of the path as counterpart to DOOZR_DOCUMENT_ROOT
            define('DOOZR_APP_ROOT', $path);
        }

        // alway use constant
        return DOOZR_APP_ROOT;
    }

    /**
     * returns the path n levels up from input
     *
     * This method is intend to switch up n level from a given base path.
     *
     * @param string  $path                  The input path
     * @param integer $level                 The count of levels to move up
     * @param boolean $preserveTrailingSlash TRUE to preserve trailing slash if exist, FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Path n level up
     * @access private
     */
    private function _up($path, $level = 1, $preserveTrailingSlash = false)
    {
        $postfix = '';

        if (substr($path, -1, 1) == DIRECTORY_SEPARATOR) {
            ++$level;
            if ($preserveTrailingSlash) {
                $postfix = DIRECTORY_SEPARATOR;
            }
        }

        $path = explode(DIRECTORY_SEPARATOR, $path);
        $path = array_slice($path, 0, count($path)-$level);

        return implode(DIRECTORY_SEPARATOR, $path).$postfix;
    }

    /**
     * setup the default paths of DoozR and Application
     *
     * setup the default paths of DoozR
     *
     * @param string $pathToRoot        The path to DoozR (DOOZR_DOCUMENT_ROOT)
     * @param string $pathToApplication The path to applications root directory
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _initPaths($pathToRoot, $pathToApplication)
    {
        // shortening
        $s = DIRECTORY_SEPARATOR;

        // get absolute root
        $root = $this->_up($pathToRoot, 1, true);

        // real root (without "Framework" directory)
        self::$_path['document_root'] = $root;

        // path to core
        self::$_path['core'] = $this->_combine($root, array('Framework', 'DoozR'));

        // path to framework
        self::$_path['framework'] = $this->_combine($root, array('Framework'));

        // path to app
        self::$_path['app'] = $pathToApplication;

        // path to model
        self::$_path['model'] = $this->_combine($root, array('Framework', 'Model'));

        // path to services
        self::$_path['module'] = $this->_combine($root, array('Framework', 'Service'));

        // path to controller
        self::$_path['controller'] = $this->_combine($root, array('Framework', 'DoozR', 'Controller'));

        // path to data
        self::$_path['data'] = $this->_combine($root, array('Framework', 'Data'));

        // path to data-private
        self::$_path['data_private'] = $this->_combine($root, array('Framework', 'Data', 'Private'));

        // path to auth
        self::$_path['auth'] = $this->_combine($root, array('Framework', 'Data', 'Private', 'Auth'));

        $systemp = sys_get_temp_dir().DIRECTORY_SEPARATOR;

        // path to cache
           //self::$_path['cache'] = $this->_combine($root, array('Framework', 'Data', 'Private', 'Cache'));
        self::$_path['cache'] = $systemp;

        // path to config
        self::$_path['config'] = $this->_combine($root, array('Framework', 'Data', 'Private', 'Config'));

        // path to font
        self::$_path['font'] = $this->_combine($root, array('Framework', 'Data', 'Private', 'Font'));

        // path to log
           //self::$_path['log'] = $this->_combine($root, array('Framework', 'Data', 'Private', 'Log'));
        self::$_path['log'] = $systemp;

        // path to temp
           //self::$_path['temp'] = $this->_combine($root, array('Framework', 'Data', 'Private', 'Temp'));
        self::$_path['temp'] = $systemp;

        // path to data-public (APP)
        self::$_path['data_public'] = $this->_combine($pathToApplication, array('Data', 'Public'));

        // path to data-public (APP)
        self::$_path['www'] = $this->_combine($pathToApplication, array('Data', 'Public', 'www'));

        // path to upload (APP)
        self::$_path['upload'] = $this->_combine($pathToApplication, array('Data', 'Private', 'Upload'));

        // path to localisation (APP)
        self::$_path['localisation'] = $this->_combine($pathToApplication, array('Data', 'Private', 'Locale'));
    }

    /**
     * This method is intend to return a combined path based on input.
     *
     * @param string $base          The base path for combine operation
     * @param array  $relativePaths The path parts as array to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return String The new combined path
     * @access private
     */
    private function _combine($base = '', array $relativePaths = array())
    {
        foreach ($relativePaths as $relativePath) {
            $base .= $relativePath.DIRECTORY_SEPARATOR;
        }

        return $base;
    }

    /**
     * setup the inlcude path's of php
     *
     * setup the inlcude path's of php
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _initIncludePaths()
    {
        // get ini include path and split it to an array
        $iniIncludePaths = explode(PATH_SEPARATOR, ini_get('include_path'));

        // default entry
        $includePathsDoozR = '.';

        // build DoozR include paths
        foreach (self::$_path as $path) {
            $includePathsDoozR .= self::_buildPath($path);
        }

        if (!empty($iniIncludePaths)) {
            foreach ($iniIncludePaths as $iniIncludePath) {
                // if '.' or exisiting path -> do not attach twice
                if (in_array($iniIncludePath, self::$_path) || trim($iniIncludePath) == '.') {
                    continue;
                }

                $includePathsDoozR .= self::_buildPath($iniIncludePath);
            }
        }

        // now try to set the ini value include_path
        ini_set('include_path', $includePathsDoozR);
    }

    /**
     * Build valid include path
     *
     * @param string $path The path which should be formatted as include-path
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string with the correct include path
     * @access private
     * @static
     */
    private static function _buildPath($path)
    {
        return PATH_SEPARATOR.$path;
    }

    /**
     * Add a path to php's include searchpaths
     *
     * @param string $path The path which should be added as include-path
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @static
     */
    public static function addIncludePath($path)
    {
        $path = get_include_path().PATH_SEPARATOR.$path;
        set_include_path($path);
    }

    /**
     * Removes a path from php's include searchpaths
     *
     * @param string $path The path which should be removed from include-paths
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @static
     */
    public static function removeIncludePath($path)
    {
        set_include_path(str_replace($path.PATH_SEPARATOR, '', get_include_path()));
    }

    /**
     * Returns requested path
     *
     * @param string  $identifier    The path which should be returned
     * @param string  $add           An extension to the path requested
     * @param boolean $trailingSlash True to add a trailing slash (false = default)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The path requested
     * @access private
     * @static
     */
    public function get($identifier, $add = '', $trailingSlash = false)
    {
        // prepare which
        $identifier = str_replace('doozr_', '', strtolower($identifier));

        // return if exist - return false if not
        if (isset(self::$_path[$identifier])) {
            // check for additional path add-on
            if (strlen($add)) {
                // if defined we add the correct slashed path
                $add = self::_correctPath($add);
            }
            return self::$_path[$identifier].$add.(($trailingSlash) ? self::$_separator : '');
        } else {
            return false;
        }
    }

    /**
     * Register an path from external - maybe created at runtime
     *
     * @param string  $identifier The name of the path which should be set
     * @param string  $path       The path which should be set
     * @param boolean $force      True to force overwrite of already existing identifier
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean True if successful otherwise false
     * @access private
     * @static
     */
    public function set($identifier, $path, $force = false)
    {
        // check if already exist and prevent overwrite if not force
        if (isset(self::$_path[$identifier]) && !is_null(self::$_path[$identifier]) && !$force) {
            throw new DoozR_Base_Exception(
                'Path with identifier "'.$identifier.'" is already defined! Set $force to TRUE to overwrite it.'
            );
        }

        // create/update path
        self::$_path[$identifier] = $this->correctPath($path);

        // return success
        return true;
    }

    /**
     * This method is intend to correct direction of slashes in given path.
     *
     * @param string $path The path to correct slashes in
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The resulting path
     * @access public
     * @static
     */
    public static function correctPath($path)
    {
        return self::_correctPath($path);
    }

    /**
     * Corrects slashes with "wrong" direction in a path and returns it
     *
     * @param string $path The path to correct slashes in
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The path with corrected slash-direction
     * @access private
     * @static
     */
    private static function _correctPath($path)
    {
        // detect which direction of slash is "wrong"
        switch (DIRECTORY_SEPARATOR) {
        case '/':
            $wrongDirection = '\\';
            break;
        case '\\':
            $wrongDirection = '/';
            break;
        }

        // if wrong directioned slash found -> replace it
        if (stristr($path, $wrongDirection)) {
            $path = str_replace($wrongDirection, DIRECTORY_SEPARATOR, $path);
        }

        // in case of mixed slashes we maybe need to cleanup
        if (stristr($path, '\\\\')) {
            $path = str_replace('\\\\', '\\', $path);
        } elseif (stristr($path, '//')) {
            $path = str_replace('//', '/', $path);
        }

        // return corrected path
        return $path;
    }

    /**
     * This method is intend to merge two path-settings under consideration of ../ (n-times)
     *
     * @param string $pathBase  The path used as base
     * @param string $pathMerge The path used as extension to $pathBase
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The corrected new merged path
     * @access public
     * @static
     */
    public function mergePath($pathBase, $pathMerge = '')
    {
        // correct the input-path settings
        $pathBase = $this->correctPath($pathBase);
        $pathMerge = $this->correctPath($pathMerge);

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
            for ($i = 0; $i < $patternCount; $i++) {
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
     * converts a given module name to a path
     *
     * This method is intend to convert a given module name to a path.
     *
     * @param string $serviceName The name of the module to retrieve the path for
     * @param string $namespace  The namespace to use for building path to module
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The path requested
     * @access public
     * @static
     * @deprecated
     */
    public static function moduleToPath($serviceName, $namespace = 'DoozR')
    {
        $service = ucfirst(str_replace('_', self::$_separator, $serviceName));
        return self::_correctPath(self::$_path['module'].$namespace.self::$_separator.$service.self::$_separator);
    }
}

?>
