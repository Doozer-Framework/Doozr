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
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Request/Parameter.php';

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
final class DoozR_Core extends DoozR_Base_Class_Singleton
    implements
    DoozR_Interface
{
    /**
     * The version of DoozR (automatic setted by git)
     * !DO NOT MODIFY MANUALLY!
     *
     * @var string The GIT SHA1 hash of the last commit
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
     * Instance of Dependeny-Injection map
     *
     * @var DoozR_Di_Map_Static
     * @access private
     * @static
     */
    private static $_map;

    /**
     * The dependency injection container
     *
     * @var DoozR_Di_Container
     * @access private
     * @static
     */
    private static $_container;

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
     * @var DoozR_Datetime_Service
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
     * This method is the constructor of the core class.
     *
     * @param array $runtimeConfiguration Override-configuration which overrides app- and core-configuration
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
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
     * This method is intend to start the timer for measurement.
     *
     * @param boolean $includeWalltime TRUE to start timer including time from routing (absolute request time)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
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
            // prepare container
            self::_initDiContainer();

            // init registry
            self::_initRegistry();

            // init logging
            self::_initLogger();

            // log bootstrapping
            self::$_logger->debug('Bootstrapping of DoozR (v '.self::getVersion(true).')');

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

            // init default services
            self::_initServices();
        }
    }

    /**
     * initialize the Dependency-Injection container and load the map
     * for wiring from a static JSON-representation.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     * @static
     */
    private static function _initDiContainer()
    {
        // simple absolute path bootstrapping for better performance
        require_once DOOZR_DOCUMENT_ROOT.'DoozR/Di/Bootstrap.php';

        // Required classes (files) for static demonstration #3
        require_once DI_PATH_LIB_DI.'Collection.php';
        require_once DI_PATH_LIB_DI.'Importer/Json.php';
        require_once DI_PATH_LIB_DI.'Map/Static.php';
        require_once DI_PATH_LIB_DI.'Factory.php';
        require_once DI_PATH_LIB_DI.'Container.php';

        /**
         * create instances of required classes
         * create instance of Di_Map_Annotation and pass required classes as arguments to constructor
         * The Di-Map builder requires two objects Collection + Importer
         */
        $collection = new DoozR_Di_Collection();
        $importer   = new DoozR_Di_Importer_Json();
        self::$_map = new DoozR_Di_Map_Static($collection, $importer);

        // generate map from static JSON map of DoozR
        self::$_map->generate(DOOZR_DOCUMENT_ROOT.'Data/Private/Config/.dependencies');

        // create
        self::$_container = DoozR_Di_Container::getInstance();
        self::$_container->setFactory(new DoozR_Di_Factory());
    }

    /**
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

        /**
         * join => DI-stuff
         */
        self::$_registry->container        = self::$_container;
        self::$_registry->map              = self::$_map;
        self::$_registry->requestParameter = new DoozR_Request_Parameter();
    }

    /**
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
        // add required dependencies
        self::$_map->wire(
            DoozR_Di_Container::MODE_STATIC,
            array(
                'DoozR_Datetime_Service' => DoozR_Loader_Serviceloader::load('datetime')
            )
        );

        // store map with fresh instances
        self::$_container->setMap(self::$_map);

        // get config reader
        self::$_logger = self::$_container->build('DoozR_Logger');

        /**
         * Now attach the Collecting Logger
         */
        self::$_logger->attach(
            self::$_container->build('DoozR_Logger_Collecting')
        );

        // store in registry
        self::$_registry->logger = self::$_logger;
    }

    /**
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
        // add required dependencies
        self::$_map->wire(
            DoozR_Di_Container::MODE_STATIC,
            array(
                'DoozR_Path'   => self::$_path,
                'DoozR_Logger' => self::$_logger
            )
        );

        // store map with fresh instances
        self::$_container->setMap(self::$_map);

        // get config reader
        self::$_config = self::$_container->build('DoozR_Config', array(self::DEFAULT_CONFIG_CONTAINER, true));

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
        // 1st get collecting logger
        $collectingLogger = self::$_logger->getLogger('collecting');

        // 2nd get existing log content
        $collection = $collectingLogger->getCollectionRaw();

        // 3rd remove collecting logger
        self::$_logger->detachAll(true);

        // check if logging enabled
        if (self::$_config->logging->enabled()) {

            // set default level from config
            self::$_logger->setDefaultLoglevel(self::$_config->logging->level());

            // get logger from config
            $loggers = self::$_config->logging->logger();

            // iterate and attach to subsystem
            foreach ($loggers as $logger) {

                // @todo: DI
                $classname = 'DoozR_Logger_'.ucfirst(strtolower($logger->name));

                $logger = new $classname(
                    DoozR_Loader_Serviceloader::load('datetime'),
                    (isset($logger->level)) ? $logger->level : self::$_logger->getDefaultLoglevel()
                );

                // attach the logger
                self::$_logger->attach($logger);
            }

            foreach ($collection as $key => $entry) {
                self::$_logger->log(
                    $entry['type'],
                    $entry['message'],
                    unserialize($entry['context']),
                    $entry['time'],
                    $entry['fingerprint'],
                    $entry['separator']
                );
            }

        } else {
            // disable logging (+ dispatching ...)
            self::$_logger->disable();
        }
    }

    /**
     * This method is intend to initialize the encoding used internal and external (e.g. output)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     * @static
     */
    private static function _initEncoding()
    {
        // define dependencies by it's identifier
        self::$_map->wire(
            DoozR_Di_Container::MODE_STATIC,
            array(
                'DoozR_Config' => self::$_config,
                'DoozR_Logger' => self::$_logger
            )
        );

        // update map => intentionally this method is used for setting a new map but it
        // does also work for our usecase ... to inject an updated map on each call
        self::$_container->setMap(self::$_map);

        self::$_encoding = self::$_container->build('DoozR_Encoding');

        // setup + store encoding in registry
        self::$_registry->encoding = self::$_encoding;
    }

    /**
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
        self::$_locale = self::$_container->build('DoozR_Locale');

        // setup + store locale in registry
        self::$_registry->locale = self::$_locale;
    }

    /**
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
        self::$_debug = self::$_container->build('DoozR_Debug', array(self::$_config->debug->enabled()));

        // store in registry
        self::$_registry->debug = self::$_debug;
    }

    /**
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
        self::$_security = self::$_container->build('DoozR_Security');

        // store in registry
        self::$_registry->security = self::$_security;
    }

    /**
     * This method is intend to initialize the front-controller. The front-controller
     * is mainly responsible for retrieving data from and sending data to the client.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     * @static
     */
    private static function _initFrontController()
    {
        // get instance of front controller
        self::$_front = self::$_container->build('DoozR_Controller_Front');

        // store in registry
        self::$_registry->front = self::$_front;
    }

    /**
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
        self::$_back = self::$_container->build('DoozR_Controller_Back');

        // store in registry
        self::$_registry->back = self::$_back;
    }

    /**
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
            'name'      => self::$_config->database->proxy(),
            'translate' => self::$_config->database->oxm(),
            'path'      => self::$_path->get('model', 'Lib\\'.self::$_config->database->oxm().'\\'),
            'bootstrap' => self::$_config->database->bootstrap(),
            'route'     => self::$_config->database->route(),
            'docroot'   => self::$_config->database->docroot()
        );

        // define dependencies by it's identifier
        self::$_map->wire(
            DoozR_Di_Container::MODE_STATIC,
            array(
                'DoozR_Path' => self::$_path
            )
        );

        // update existing map with newly added dependencies
        self::$_container->setMap(self::$_map);

        // get instance of model (is decorator!)
        self::$_model = self::$_container->build('DoozR_Model', array($decoratorConfig));

        // store in registry
        self::$_registry->model = self::$_model;
    }

    /**
     * This method is intend to initialize the default services for current running-mode.
     * Running mode depends on used interface. It can be either CLI (Console) or WEB (Browser).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     * @static
     */
    private static function _initServices()
    {
        // get mode app currently runs in
        $runningMode = self::$_front->getRunningMode();

        // get default services for mode
        $services = self::$_config->base->services->{$runningMode}();

        foreach ($services as $service) {
            self::$_registry->{$service} = DoozR_Loader_Serviceloader::load($service);
        }
    }

    /**
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
        self::$_logger->debug('Core execution-time: '.self::$coreExecutionTime.' seconds');
    }

    /**
     * This method triggers an error (delegate as exception). in default it throws an
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
     * @return DoozR_Datetime_Service An instance of module Datetime
     * @access private
     * @static
     */
    private static function _getDateTime()
    {
        if (!self::$_dateTime) {
            self::$_dateTime = DoozR_Loader_Serviceloader::load('datetime');
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
            //preg_match('/\d+/', self::$version, $version);
            return self::$version;
        }
    }

    /**
     * Returns the registry of the current execution
     * prefilled with all the base services of DoozR
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Registry
     * @access public
     */
    public function getRregistry()
    {
        return self::$_registry;
    }

    /*******************************************************************************************************************
     * \\ END PUBLIC API
     ******************************************************************************************************************/

    /**
     * This method gets calles on class desctruct.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __destruct()
    {
        // log request serving time -> but only if logger available!
        if (self::$_logger) {
            self::$_logger->debug(
                'Request cycle completed in: '.self::_getDateTime()->getMicrotimeDiff(self::$starttime).' seconds'
            );

            $memoryUsage = number_format(round(memory_get_peak_usage()/1024/1024, 2), 2);

            // log memory usage
            self::$_logger->debug(
                'Total consumed memory: '.$memoryUsage.' MB'
            );
        }

        // save session
        session_write_close();
    }
}
