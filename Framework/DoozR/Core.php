<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Core
 *
 * Core.php - Core class of the DoozR Framework
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
 * @subpackage DoozR_Core_Class
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Core/Interface.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Core/Exception.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Class/Singleton.php';

// all base stuff
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Logger.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Path.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Config.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Registry.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Encoding.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Locale.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Debug.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Security.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Controller/Front.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Controller/Back.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Model.php';

/**
 * DoozR - Core
 *
 * Core class of the DoozR Framework
 *
 * @category   DoozR
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Class
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
final class DoozR_Core extends DoozR_Base_Class_Singleton implements DoozR_Interface
{
    /**
     * The version of DoozR (automatic setted by git)
     * !DO NOT MODIFY MANUALLY!
     *
     * @var string Contains the SVN-Version string
     * @access public
     * @static
     */
    public static $version = 'Git: $Id$';

    /**
     * Instance of DoozR_Path
     *
     * @var DoozR_Path
     * @access private
     * @static
     */
    private static $_path;

    /**
     * Instance of DoozR_Config
     *
     * @var DoozR_Config
     * @access private
     * @static
     */
    private static $_config;

    /**
     * Instance of DoozR_Debug
     *
     * @var DoozR_Debug
     * @access private
     * @static
     */
    private static $_debug;

    /**
     * Instance of DoozR_Logger
     *
     * @var DoozR_Logger
     * @access private
     * @static
     */
    private static $_logger;

    /**
     * Instance of DoozR_Encoding
     *
     * @var DoozR_Encoding
     * @access private
     * @static
     */
    private static $_encoding;

    /**
     * Instance of DoozR_Locale
     *
     * @var DoozR_Locale
     * @access private
     * @static
     */
    private static $_locale;

    /**
     * Instance of DoozR_Registry
     *
     * @var DoozR_Registry
     * @access private
     * @static
     */
    private static $_registry;

    /**
     * Instance of DoozR_Front
     *
     * @var DoozR_Front
     * @access private
     * @static
     */
    private static $_front;

    /**
     * Instance of DoozR_Back
     *
     * @var DoozR_Back
     * @access private
     * @static
     */
    private static $_back;

    /**
     * Instance of DoozR_Model
     *
     * @var DoozR_Model
     * @access private
     * @static
     */
    private static $_model;

    /**
     * Instance of DoozR_Security
     *
     * @var DoozR_Security
     * @access private
     * @static
     */
    private static $_security;

    /**
     * Contains the starttime (core instanciated) for measurements
     *
     * @var float
     * @access public
     * @static
     */
    public static $starttime = 0;

    /**
     * Contains the execution-time of core (core is ready to use) for measurements
     *
     * @var float
     * @access public
     * @static
     */
    public static $coreExecutionTime = 0;

    /**
     * An instance of module datetime
     *
     * @var DoozR_Datetime_Module
     * @access private
     * @static
     */
    private static $_dateTime;

    /**
     * Contains the default configuration container used by DoozR (Core especially)
     *
     * @var string
     * @access const
     */
    const DEFAULT_CONFIG_CONTAINER = 'Json';


    /*******************************************************************************************************************
     * // BEGIN PRIVATE/PROTECED METHODS
     ******************************************************************************************************************/

    /**
     * constructor
     *
     * This method is the constructor of the core class.
     *
     * @param array $runtimeConfiguration Override-configuration which overrides app- and core-configuration
     *
     * @return void
     * @access protected
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function __construct(array $runtimeConfiguration = array())
    {
        // start stopwatch
        self::_startTimer();

        // run internal bootstrapper process
        self::bootstrap(true, $runtimeConfiguration);

        // stop timer and store execution-time
        self::_stopTimer();
    }

    /**
     * Starts timer
     *
     * This method is intend to start the timer for measurement.
     *
     * @param boolean $includeWalltime TRUE to start timer including time from routing (absolute request time)
     *
     * @return void
     * @access private
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @static
     */
    private static function _startTimer($includeWalltime = true)
    {
        // include time from bootstrapping + routing?
        if ($includeWalltime) {
            self::$starttime = $_SERVER['REQUEST_TIME'];
        } else {
            self::$starttime = microtime();
        }
    }

    /**
     * bootstrapping of the core classes
     *
     * This method is intend to start the bootstrapping process. It enables you to rerun the whole
     * bootstrapping process from outside by implementing this method as public. So you are able
     * to unit-test your application with a fresh bootstrapped core on each run.
     *
     * @param boolean $rerun                TRUE to rerun the bootstrapper, otherwise FALSE to keep state
     * @param array   $runtimeConfiguration The runtime configuration to pass to config (ovveride configuration)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @static
     */
    public static function bootstrap($rerun = true, array $runtimeConfiguration = array())
    {
        // check if rerun is given (e.g. to support unit-testing on each run with fresh bootstrap!)
        // @see: http://it-republik.de/php/news/Die-Framework-Falle-und-Wege-daraus-059217.html
        if ($rerun) {
            // init registry
            self::_initRegistry();

            // init logging
            self::_initLogger();

            // log bootstrapping
            self::$_logger->log('Bootstrapping of DoozR (v '.self::getVersion(true).')');

            // init path-manager
            self::_initPath();

            // parse configuration
            self::_initConfig($runtimeConfiguration);

            // configure logging
            self::_configureLogging();

            // check locale configuation and configure environment
            self::_initEncoding();

            // check locale configuation and configure environment
            self::_initLocale();

            // initialize debug setup + hooks (error-/exception-handler)
            self::_initDebug();

            // init security layer
            self::_initSecurity();

            // init front-controller
            self::_initFrontController();

            // init back-controller
            self::_initBackController();

            // init model (database layer)
            self::_initModel();

            // init default modules
            self::_initModules();
        }
    }

    /**
     * Initializes the registry
     *
     * This method is intend to initialize the registry of the DoozR Framework. The registry itself
     * is intend to store the instances mainly used by core classes like DoozR_Path, DoozR_Config,
     * DoozR_Logger and this instances are always accessible by its name after the underscore (_ - written lowercase)
     * e.g. DoozR_Logger will be available like this $registry->logger, DoozR_Config like $registry->config
     * and so on.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     * @static
     */
    private static function _initRegistry()
    {
        self::$_registry = DoozR_Registry::getInstance();
    }

    /**
     * Initializes the logger
     *
     * This method is intend to initialize the logger-manager of the DoozR Framework. The first initialized logger
     * is of type collecting. So it collects all entries as long as the config isn't parsed and the real
     * configured loggers are attached.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     * @static
     */
    private static function _initLogger()
    {
        // get collecting logger with max log-level till logger subsystem is ready
        self::$_logger = DoozR_Logger::getInstance();

        // store in registry
        self::$_registry->logger = self::$_logger;
    }

    /**
     * Initializes the path
     *
     * This method is intend to initialize the path-manager of the DoozR Framework. The path-manager returns
     * always the correct path to predefined parts of the framework and it is also cappable of combining paths
     * in correct slashed writing.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     * @static
     */
    private static function _initPath()
    {
        // get path manager
        self::$_path = DoozR_Path::getInstance(DOOZR_DOCUMENT_ROOT);

        // store in registry
        self::$_registry->path = self::$_path;
    }

    /**
     * Initializes the configuration
     *
     * This method is intend to initialize and prepare the config used for running the framework and the app.
     *
     * @param mixed $runtimeConfiguration The runtime configuration passed as ARRAY or OBJECT
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     * @static
     */
    private static function _initConfig($runtimeConfiguration)
    {
        // first get master config
        self::$_config = DoozR_Config::getInstance(
            self::$_path,
            self::$_logger,
            self::DEFAULT_CONFIG_CONTAINER,
            true
        );

        // read config from: DoozR
        self::$_config->read(
            self::$_path->get('config').'.config'
        );

        // read config from: Application
        self::$_config->read(
            self::$_path->get('app', 'Data\Private\Config\.config')
        );

        // check for runtime configuration
        if (count($runtimeConfiguration)) {
            // read config from: Runtime
            self::$_config->read(
                $runtimeConfiguration
            );
        }

        // store config (manager) in registry
        self::$_registry->config = self::$_config;
    }

    /**
     * configures the logging
     *
     * This method is intend configure the logging. It attaches the real configured loggers from config and removes
     * the collecting logger. This method also injects the collected entries into the new attached loggers.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     * @static
     */
    private static function _configureLogging()
    {
        // check if logging enabled
        if (self::$_config->logging->enabled()) {

            // set default level from config
            self::$_logger->setDefaultLoglevel(self::$_config->logging->level());

            // get logger from config
            $loggers = self::$_config->logging->logger();

            // iterate and attach to subsystem
            foreach ($loggers as $logger) {
                self::$_logger->attach(
                    $logger->name,
                    (isset($logger->level)) ? $logger->level : null,
                    self::$_registry->config,
                    (isset($logger->header)) ? $logger->header : false
                );
            }

            // detach the attached collecting logger (not longer needed!)
            self::$_logger->detach('collecting');

        } else {
            // disable logging (+ dispatching ...)
            self::$_logger->disable();
        }
    }

    /**
     * Initializes the encoding
     *
     * This method is intend to initialize the encoding used internal and external (e.g. output)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     * @static
     */
    private static function _initEncoding()
    {
        self::$_encoding = DoozR_Encoding::getInstance(self::$_config, self::$_logger);

        // setup + store encoding in registry
        self::$_registry->encoding = self::$_encoding;
    }

    /**
     * Initializes locale
     *
     * This method is intend to initialize the locale.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     * @see    http://de.wikipedia.org/wiki/Locale
     * @static
     */
    private static function _initLocale()
    {
        self::$_locale = DoozR_Locale::getInstance(self::$_config, self::$_logger);

        // setup + store locale in registry
        self::$_registry->locale = self::$_locale;
    }

    /**
     * manage debug settings
     *
     * This method is intend to configure the debug-behavior of PHP. I tries to runtime patch php.ini-settings
     * (ini_set) for error_reporting, display_errors, log_errors. If debug is enabled, the highest possible reporting
     * level (inlcuding E_STRICT) is set. It also logs a warning-level message - if safe-mode is detected and setup
     * can't be done.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     * @static
     */
    private static function _initDebug()
    {
        // get debug manager
        self::$_debug = DoozR_Debug::getInstance(self::$_logger, self::$_config->debug->enabled());

        // store in registry
        self::$_registry->debug = self::$_debug;
    }

    /**
     * manage security settings
     *
     * This method is intend to manage security related setting and instanciate DoozR_Security which
     * protects the framework and handles security related operations like en- / decryption ...
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     * @static
     */
    private static function _initSecurity()
    {
        // get security manager
        self::$_security = DoozR_Security::getInstance(self::$_config, self::$_logger);

        // store in registry
        self::$_registry->security = self::$_security;
    }

    /**
     * Initializes front-controller
     *
     * This method is intend to initialize the front-controller. The front-controller
     * is mainly responsible for retrieving data from and sending data to the client.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private static function _initFrontController()
    {
        // get instance of front controller
        self::$_front = DoozR_Controller_Front::getInstance(self::$_config, self::$_logger);

        // store in registry
        self::$_registry->front = self::$_front;
    }

    /**
     * Initializes back-controller
     *
     * This method is intend to initialize the back-controller. The back-controller
     * is mainly responsible for managing access to the MVC/MVP part and used as interface
     * to model as well.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     * @static
     */
    private static function _initBackController()
    {
        // get instance of back controller
        self::$_back = DoozR_Controller_Back::getInstance(self::$_config, self::$_logger);

        // store in registry
        self::$_registry->back = self::$_back;
    }

    /**
     * Initializes the model layer
     *
     * This method is intend to initialize the model layer. It provides access to a database through a
     * ORM (Object-Relational-Mapper) like Doctrine, ... or a ODM (like PHPillow)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     * @static
     */
    private static function _initModel()
    {
        // build decorator config
        $decoratorConfig = array(
            'name'      => self::$_config->database->lib(),
            'translate' => self::$_config->database->oxm(),
            'path'      => self::$_path->get('model', 'Lib\\'.self::$_config->database->oxm().'\\'),
            'bootstrap' => self::$_config->database->bootstrap(),
            'route'     => self::$_config->database->route(),
            'docroot'   => self::$_config->database->docroot()
        );

        // get instance of model (is decorator!)
        self::$_model = DoozR_Model::getInstance($decoratorConfig, self::$_path, self::$_config, self::$_logger);

        // store in registry
        self::$_registry->model = self::$_model;
    }

    /**
     * Initializes the default modules
     *
     * This method is intend to initialize the default modules for current running-mode.
     * Running mode depends on used interface. It can be either CLI (Console) or WEB (Browser).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     * @static
     */
    private static function _initModules()
    {
        // get mode app currently runs in
        $runningMode = self::$_front->getRunningMode();

        // get default modules for mode
        $modules = self::$_config->base->modules->{$runningMode}();

        foreach ($modules as $module) {
            self::$_registry->{$module} = DoozR_Loader_Moduleloader::load($module);
        }
    }

    /**
     * Stops timer
     *
     * This method is intend to stop the timer for measurements and log the core-execution time.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     * @static
     */
    private static function _stopTimer()
    {
        // calculate and store core execution time
        self::$coreExecutionTime = self::_getDateTime()->getMicrotimeDiff(self::$starttime);

        // log core execution time
        self::$_logger->log('Core execution-time: '.self::$coreExecutionTime.' seconds');
    }

    /**
     * triggers an core-error
     *
     * this method triggers an error (delegate as exception). in default it throws an
     * E_USER_CORE_EXCEPTION if $fatal set to true it will throw an E_USER_CORE_FATAL_EXCEPTION
     *
     * @param string $error The error-message to throw as core-error
     * @param bool   $fatal The type of core-error - if set to true the error becomes FATAL
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean False always
     * @access private
     * @static
     */
    private static function _coreError($error, $fatal = true)
    {
        // check for error type (simple core error | fatal error = execution stops!)
        if ($fatal) {
            $type = E_USER_CORE_FATAL_EXCEPTION;
        } else {
            $type = E_USER_CORE_EXCEPTION;
        }

        // throw the core error as exception
        throw new DoozR_Core_Exception(
            $error,
            $type
        );

        // return FALSE so we can use the result of this method as return value for caller
        return false;
    }

    /**
     * Returns instance of Datetime module
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Datetime_Module An instance of module Datetime
     * @access private
     * @static
     */
    private static function _getDateTime()
    {
        if (!self::$_dateTime) {
            self::$_dateTime = DoozR_Loader_Moduleloader::load('datetime');
        }

        return self::$_dateTime;
    }

    /*******************************************************************************************************************
     * \\ END PRIVATE/PROTECTED METHODS
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN PUBLIC API
     ******************************************************************************************************************/

    /**
     * external interface for throwing core-exception(s)/error(s)
     *
     * This method is intend as external interface for throwing core-exception(s)/error(s).
     *
     * @param string  $error The error-message
     * @param boolean $fatal Controls if the execution should be stopped (fatal = true)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean FALSE if error successfully processed otherwise TRUE
     * @access public
     * @static
     */
    public static function coreError($error, $fatal = true)
    {
        return self::_coreError($error, $fatal);
    }

    /**
     * returns the starttime of core execution
     *
     * This method is intend to return the starttime of core execution.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return float Microtime
     * @access public
     */
    public static function getCoreStarttime()
    {
        return self::$starttime;
    }

    /**
     * returns the total-time of core execution
     *
     * This method is intend to return the total-time of core execution.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return float Microtime
     * @access public
     */
    public static function getCoreExecutiontime()
    {
        return self::$coreExecutionTime;
    }

    /**
     * returns the version of current used DoozR-installation (core)
     *
     * This method is intend to return the version of DoozR.
     *
     * @param boolean $justRevision If set to true the method returns the revision as integer, otherwise full rev-string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The version of DoozR
     * @access public
     */
    public static function getVersion($justRevision = false)
    {
        if (!$justRevision) {
            return self::$version;
        } else {
            // etxract the version from svn-Id
            preg_match('/\d+/', self::$version, $version);
            //return $version[0];
            return 123;
        }
    }

    /*******************************************************************************************************************
     * \\ END PUBLIC API
     ******************************************************************************************************************/

    /**
     * called on class-destruction
     *
     * This method gets calles on class desctruct.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __destruct()
    {
        /*
        // log request serving time
        self::$_logger->log(
            'Request cycle completed in: '.self::_getDateTime()->getMicrotimeDiff(self::$starttime).' seconds'
        );

        // save session
        session_write_close();
        */
    }
}

?>
