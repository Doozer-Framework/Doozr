<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Kernel
 *
 * Kernel.php - Kernel class of the Doozr Framework
 *
 * PHP versions 5.4
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
 *
 * Copyright (c) 2005 - 2015, Benjamin Carl - All rights reserved.
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
 * @category   Doozr
 * @package    Doozr_Kernel
 * @subpackage Doozr_Kernel_Class
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Kernel/Interface.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Class/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Logging.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Path.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Configuration.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Registry.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Encoding.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Locale.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Debugging.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Security.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Controller/Front.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Controller/Back.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Model.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Request/Arguments.php';

use DebugBar\StandardDebugBar;

/**
 * Doozr - Kernel
 *
 * Kernel class of the Doozr Framework
 *
 * @category   Doozr
 * @package    Doozr_Kernel
 * @subpackage Doozr_Kernel_Class
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 * @final
 */
final class Doozr_Kernel extends Doozr_Base_Class_Singleton
    implements
    Doozr_Kernel_Interface
{
    /**
     * Contains the starttime (core instantiated) for measurements
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
     * @var Doozr_Datetime_Service
     * @access protected
     * @static
     */
    protected static $dateTime;

    /**
     * Contains the default configuration container used by Doozr (Kernel especially)
     *
     * @var string
     * @access const
     */
    const DEFAULT_CONFIG_CONTAINER = 'Json';

    /**
     * CLI running runtimeEnvironment
     *
     * @var string
     * @access public
     * @const
     */
    const RUNTIME_ENVIRONMENT_CLI = 'Cli';

    /**
     * WEB running runtimeEnvironment
     *
     * @var string
     * @access public
     * @const
     */
    const RUNTIME_ENVIRONMENT_WEB = 'Web';

    /**
     * HTTPD running runtimeEnvironment
     *
     * @var string
     * @access public
     * @const
     */
    const RUNTIME_ENVIRONMENT_HTTPD = 'Httpd';


    const APP_ENVIRONMENT_TESTING = 'testing';
    const APP_ENVIRONMENT_PRODUCTION = 'production';
    const APP_ENVIRONMENT_DEVELOPMENT = 'development';
    const APP_ENVIRONMENT_STAGING = 'staging';


    /*------------------------------------------------------------------------------------------------------------------
    | PRIVATE/PROTECTED METHODS
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is the constructor of the core class.
     *
     * @param bool $virtualized TRUE to run the Kernel virtualized, otherwise FALSE to run in real mode
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Kernel
     * @access protected
     */
    protected function __construct($virtualized = false)
    {
        // Start stopwatch
        self::startTimer();

        // Run internal bootstrap process
        self::bootstrap(true, $virtualized);

        // Stop timer and store execution-time
        self::stopTimer();
    }

    /**
     * Proxy to getInstance to reduce confusion e.g. when bootstrapping the application.
     *
     * @param bool $virtualized TRUE to signalize Doozr that it is running virtual, default = FALSE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Kernel The core instance
     * @access public
     */
    public static function init($virtualized = false)
    {
        return Doozr_Kernel::getInstance($virtualized);
    }

    /**
     * Starts the timer for measurement.
     *
     * @param bool $includeWalltime TRUE to start timer including time from routing (absolute request time)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     * @static
     */
    protected static function startTimer($includeWalltime = true)
    {
        // include time from bootstrapping + routing?
        if ($includeWalltime) {
            self::$starttime = $_SERVER['REQUEST_TIME'];
        } else {
            self::$starttime = microtime();
        }
    }

    /**
     * Starts the bootstrapping process. It enables you to rerun the whole
     * bootstrapping process from outside by implementing this method as public. So you are able
     * to unit-test your application with a fresh bootstrapped core on each run.
     *
     * @param bool $rerun       TRUE to rerun the bootstrap process, otherwise FALSE to keep state
     * @param bool $virtualized TRUE to run Kernel virtualized, otherwise FALSE
     *
     * @throws Doozr_Exception
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @static
     */
    public static function bootstrap($rerun = true, $virtualized = false)
    {
        // Check if rerun is given (e.g. to support unit-testing on each run with fresh bootstrap!)
        // @see: http://it-republik.de/php/news/Die-Framework-Falle-und-Wege-daraus-059217.html
        if ($rerun === true) {
            // Start init-stack ...
            if (!
                (
                    self::initRegistry() &&
                    self::initDependencyInjection() &&
                    self::initFilesystem($virtualized) &&
                    self::initCache() &&
                    self::initLogger() &&
                    self::initPath() &&
                    self::initConfiguration() &&
                    self::configureLogging() &&
                    (self::$registry->getLogger()->debug('Bootstrapping of Doozr (v ' . DOOZR_VERSION . ')')) &&
                    self::initSystem() &&
                    self::initEncoding() &&
                    self::initLocale() &&
                    self::initDebug() &&
                    self::initSecurity() &&
                    self::initRequest() &&
                    (
                        self::$registry->getLogger()->debug(
                            'Runtime environment: ' . DOOZR_RUNTIME_ENVIRONMENT
                        )
                    ) &&
                    self::initResponse() &&
                    self::initFrontController() &&
                    self::initBackController() &&
                    self::initModel() &&
                    self::initServices()
                )
            ) {
                throw new Doozr_Exception(
                    'Critical error while bootstrapping Doozr. This should never happen. Stopping execution.'
                );
            }
        }
    }

    /**
     * Initializes the filesystem access.
     *
     * @param bool $virtualized TRUE to run filesystem virtualized, otherwise FALSE to run real
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success
     * @access protected
     * @static
     */
    protected static function initFilesystem($virtualized = false)
    {
        // Store filesystem ...
        self::$registry->setFilesystem(
            Doozr_Loader_Serviceloader::load('filesystem', $virtualized)
        );

        // Important for bootstrap result
        return true;
    }

    /**
     * Initializes the cache.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success
     * @access protected
     * @static
     */
    protected static function initCache()
    {
        // Build namespace for cache
        $namespace = DOOZR_NAMESPACE_FLAT . '.cache';

        // Store cache ...
        self::$registry->setCache(
            Doozr_Loader_Serviceloader::load('cache', DOOZR_CACHE_CONTAINER, $namespace, array(), DOOZR_UNIX)
        );

        // Important for bootstrap result
        return true;
    }

    /**
     * Initialize the Dependency-Injection container and load the map for wiring from a static JSON-representation.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success
     * @access protected
     * @static
     */
    protected static function initDependencyInjection()
    {
        // Required classes (files) for static demonstration #3
        require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Collection.php';
        require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Importer/Json.php';
        require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Map/Static.php';
        require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Factory.php';
        require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Container.php';

        /**
         * Create instances of required classes
         * Create instance of Doozr_Di_Map_Static and pass required classes as arguments to constructor
         * The Di-Map builder requires two objects Collection + Importer
         */
        $collection = new Doozr_Di_Collection();
        $importer   = new Doozr_Di_Importer_Json();
        $map        = new Doozr_Di_Map_Static($collection, $importer);

        // Generate map from static JSON map of Doozr
        $map->generate(DOOZR_DOCUMENT_ROOT . 'Data/Private/Config/.dependencies');

        // create
        $container = Doozr_Di_Container::getInstance();
        $container->setFactory(new Doozr_Di_Factory());

        self::$registry->setContainer($container);
        self::$registry->setMap($map);

        // Important for bootstrap result
        return true;
    }

    /**
     * Initializes the registry of the Doozr Framework. The registry itself
     * is intend to store the instances mainly used by core classes like Doozr_Path, Doozr_Configuration,
     * Doozr_Logging and this instances are always accessible by its name after the underscore (_ - written lowercase)
     * e.g. Doozr_Logging will be available like this $registry->logger, Doozr_Configuration like $registry->config
     * and so on.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success
     * @access protected
     * @static
     */
    protected static function initRegistry()
    {
        self::$registry = Doozr_Registry::getInstance();

        // Important for bootstrap result
        return true;
    }

    /**
     * Initializes the logger-manager of the Doozr Framework. The first initialized logger
     * is of type collecting. So it collects all entries as long as the config isn't parsed and the real
     * configured loggers are attached.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success
     * @access protected
     * @static
     */
    protected static function initLogger()
    {
        // Add required dependencies
        self::$registry->getMap()->wire(
            Doozr_Di_Container::MODE_STATIC,
            array(
                'Doozr_Datetime_Service' => Doozr_Loader_Serviceloader::load('datetime')
            )
        );

        // Store map with fresh instances
        self::$registry->getContainer()->setMap(self::$registry->getMap());

        // Get logger
        $logger = self::$registry->getContainer()->build('Doozr_Logging');

        // And attach the Collecting Logger
        $logger->attach(
            self::$registry->getContainer()->build('Doozr_Logging_Collecting')
        );

        self::$registry->setLogger($logger);

        // Important for bootstrap result
        return true;
    }

    /**
     * Initializes the path-manager of the Doozr Framework. The path-manager returns
     * always the correct path to predefined parts of the framework and it is also cappable of combining paths
     * in correct slashed writing.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success
     * @access protected
     * @static
     */
    protected static function initPath()
    {
        self::$registry->setPath(Doozr_Path::getInstance(DOOZR_DOCUMENT_ROOT));

        // Important for bootstrap result
        return true;
    }

    /**
     * Initialize and prepare the config used for running the framework and the app.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success
     * @access protected
     * @static
     */
    protected static function initConfiguration()
    {
        // The cache instance ...
        /* @var Doozr_Cache_Service $cache */
        $cache = self::getRegistry()->getCache();

        // Add required dependencies
        self::$registry->getMap()->wire(
            Doozr_Di_Container::MODE_STATIC,
            array(
                'Doozr_Configuration_Reader_Json' => new Doozr_Configuration_Reader_Json(
                    Doozr_Loader_Serviceloader::load('filesystem'),
                    $cache,
                    true
                ),
                'Doozr_Cache_Service' => $cache,
            )
        );

        // Store map with fresh instances
        self::$registry->getContainer()->setMap(self::$registry->getMap());

        /* @var Doozr_Configuration $config */
        $config = self::$registry->getContainer()->build(
            'Doozr_Configuration',
            array(
                true
            )
        );

        // Read config of: Doozr - central core configuration from developer
        $config->read(
            self::$registry->getPath()->get('config') . '.config.json'
        );


        /**
         * READ CONFIG OF SERVICES
         */
        $path      = self::$registry->getPath()->get('service');
        $directory = new RecursiveDirectoryIterator($path);
        $iterator  = new RecursiveIteratorIterator($directory);

        foreach ($iterator as $info) {
            if ($info->getFilename() === '.config.json') {
                $config->read($info->getPathname());
            }
        }

        /**
         * END CONFIG OF SERVICES
         */


        // Read config of application
        /* @todo HERE BY ENV! */
        $userlandConfigurationFile = self::$registry->getPath()->get('app', 'Data\Private\Config\.config.json');

        if (file_exists($userlandConfigurationFile) && is_readable($userlandConfigurationFile)) {
            $config->read($userlandConfigurationFile);
        }

        // Store config
        self::$registry->setConfiguration($config);

        // Important for bootstrap result
        return true;
    }

    /**
     * Configures logging. It attaches the real configured loggers from config and removes
     * the collecting logger. This method also injects the collected entries into the new attached loggers.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success
     * @access protected
     * @static
     */
    protected static function configureLogging()
    {
        // 1st get collecting logger
        $collectingLogger = self::$registry->getLogger()->getLogger('collecting');

        // 2nd get existing log content
        $collection = $collectingLogger->getCollectionRaw();

        // 3rd remove collecting logger
        self::$registry->getLogger()->detachAll(true);

        // Check if logging enabled ...
        if (true === DOOZR_LOGGING) {
            // Iterate and attach to subsystem
            foreach (self::$registry->getConfiguration()->kernel->logging->logger as $logger) {
                $loggerInstance = self::$registry->getContainer()->build(
                    'Doozr_Logging_' . ucfirst(strtolower($logger->name)),
                    array((isset($logger->level)) ? $logger->level : self::$registry->getLogger()->getDefaultLoglevel())
                );

                // attach the logger
                self::$registry->getLogger()->attach($loggerInstance);
            }

            foreach ($collection as $key => $entry) {
                self::$registry->getLogger()->log(
                    $entry['type'],
                    $entry['message'],
                    unserialize($entry['context']),
                    $entry['time'],
                    $entry['fingerprint'],
                    $entry['separator']
                );
            }

        } else {
            // Disable logging (+ dispatching ...)
            //self::$registry->getLogger()->disable();
            self::$registry->getLogger()->detachAll(true);
        }

        // Important for bootstrap result
        return true;
    }

    /**
     * Initializes the system settings of/for PHP.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success, FALSE on failure
     * @access protected
     * @static
     * @throws Doozr_Kernel_Exception
     */
    protected static function initSystem()
    {
        $result      = true;
        $phpSettings = self::getRegistry()->getConfiguration()->kernel->system->php;

        foreach ($phpSettings as $iniKey => $value) {
            if (false === ($result && ini_set($iniKey, $value))) {
                throw new Doozr_Kernel_Exception(
                    sprintf('Error setting up system. Error while trying to set "%s" (value: "%s")', $iniKey, $value)
                );
            }
        }

        return $result;
    }

    /**
     * Initializes the encoding used internal and external (e.g. output)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success
     * @access protected
     * @static
     */
    protected static function initEncoding()
    {
        // Define dependencies by it's identifier
        self::$registry->getMap()->wire(
            Doozr_Di_Container::MODE_STATIC,
            array(
                'Doozr_Configuration' => self::$registry->getConfiguration(),
                'Doozr_Logging' => self::$registry->getLogger()
            )
        );

        // update map => intentionally this method is used for setting a new map but it
        // does also work for our usecase ... to inject an updated map on each call
        self::$registry->getContainer()->setMap(self::$registry->getMap());

        // Setup + store encoding in registry
        self::$registry->setEncoding(self::$registry->getContainer()->build('Doozr_Encoding'));

        // Important for bootstrap result
        return true;
    }

    /**
     * Initializes the locale.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success.
     * @access protected
     * @see    http://de.wikipedia.org/wiki/Locale
     * @static
     */
    protected static function initLocale()
    {
        self::$registry->setLocale(self::$registry->getContainer()->build('Doozr_Locale'));

        // Important for bootstrap result
        return true;
    }

    /**
     * Configures the debug-behavior of PHP. I tries to runtime patch php.ini-settings
     * (ini_set) for error_reporting, display_errors, log_errors. If debug is enabled, the highest possible reporting
     * level (inlcuding E_STRICT) is set. It also logs a warning-level message - if safe-runtimeEnvironment is detected and setup
     * can't be done.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success
     * @access protected
     * @static
     */
    protected static function initDebug()
    {
        if (true === DOOZR_DEBUGGING) {
            // Get debug manager
            self::$registry->setDebugging(
                self::$registry->getContainer()->build(
                    'Doozr_Debugging',
                    array(
                        DOOZR_DEBUGGING
                    )
                )
            );

            $debugbar = new StandardDebugBar();
            $debugbar['time']->startMeasure('request-cycle', 'Request cycle (Doozr)');
            $debugbar->addCollector(new DebugBar\DataCollector\ConfigCollector(json_decode(json_encode(self::$registry->getConfiguration()->get()), true)));



            self::$registry->setDebugbar(
                $debugbar
            );
        }

        // Important for bootstrap result
        return true;
    }

    /**
     * Manages security related setting and instanciate Doozr_Security which
     * protects the framework and handles security related operations like en- / decryption ...
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success
     * @access protected
     * @static
     */
    protected static function initSecurity()
    {
        // Get security manager
        self::$registry->setSecurity(self::$registry->getContainer()->build('Doozr_Security'));

        // Important for bootstrap result
        return true;
    }

    /**
     * Initialize the request state.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success
     * @access protected
     * @static
     */
    protected static function initRequest()
    {
        if (true === DOOZR_DEBUGGING) {
            self::$registry->getDebugbar()['time']->startMeasure('request-parsing', 'Parsing request');
        }

        // Get instance of request
        self::$registry->setRequest(
            self::$registry->getContainer()->build(
                'Doozr_Request',
                array(DOOZR_RUNTIME_ENVIRONMENT)
            )->export()
        );


        if (true === DOOZR_DEBUGGING) {
            self::$registry->getDebugbar()['time']->stopMeasure('request-parsing');
        }

        // Important for bootstrap result
        return true;
    }

    /**
     * Initialize the response state.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success
     * @access protected
     * @static
     */
    protected static function initResponse()
    {
        if (true === DOOZR_DEBUGGING) {
            self::$registry->getDebugbar()['time']->startMeasure('preparing-response', 'Preparing response');
        }

        /**
         * to be continued. We take action to bring the response in place here for our inner/outer layer state concept.
         * So we would init a reponse state here. The response state can be used and accessed anywhere in the
         * application. You can add header(s), information(s), ... At the end of the request cycle or on an explicit
         * flush() - The response will be send() [you can either call flush() -> will clear state | or send() without
         * any further ops.]
         */
        // Get instance of response
        self::$registry->setResponse(
            self::$registry->getContainer()->build('Doozr_Response')->export()
        );

        if (true === DOOZR_DEBUGGING) {
            self::$registry->getDebugbar()['time']->stopMeasure('preparing-response');
        }

        // Important for bootstrap result
        return true;
    }

    /**
     * Initializes the front-controller. The front-controller
     * is mainly responsible for retrieving data from and sending data to the client.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success
     * @access protected
     * @static
     */
    protected static function initFrontController()
    {
        // add required dependencies
        self::$registry->getMap()->wire(
            Doozr_Di_Container::MODE_STATIC,
            array(
                'Doozr_Request_State'  => self::$registry->getRequest(),
                'Doozr_Response_State' => self::$registry->getResponse(),
            )
        );

        // Store map with fresh instances
        self::$registry->getContainer()->setMap(self::$registry->getMap());

        // Get front controller and store in central registry
        self::$registry->setFront(
            self::$registry->getContainer()->build('Doozr_Controller_Front')
        );

        // Important for bootstrap result
        return true;
    }

    /**
     * Initializes the back-controller. The back-controller
     * is mainly responsible for managing access to the MVP part and used as interface
     * to model as well.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success
     * @access protected
     * @static
     */
    protected static function initBackController()
    {
        // Get instance of back controller
        self::$registry->setBack(
            self::$registry->getContainer()->build('Doozr_Controller_Back', array(
                    Doozr_Loader_Serviceloader::load('filesystem'),
                    self::getRegistry()->getCache()
                )
            )
        );

        // Important for bootstrap result
        return true;
    }

    /**
     * Initializes the model layer. It provides access to a database through a
     * ORM (Object-Relational-Mapper) like Doctrine, ... or a ODM (like PHPillow)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success
     * @access protected
     * @static
     */
    protected static function initModel()
    {
        // Retrieve configuration
        $config = self::$registry->getConfiguration();
        $path   = self::$registry->getPath();

        // Build decorator config ...
        $databaseConfiguration = array(
            'name'      => $config->kernel->model->proxy,
            'translate' => $config->kernel->model->oxm,
            'path'      => $path->get(
                'model', 'Lib\\' . $config->kernel->model->oxm . '\\'
            ),
            'bootstrap' => $config->kernel->model->bootstrap,
            'route'     => $config->kernel->model->route,
            'docroot'   => $config->kernel->model->docroot
        );

        // Define dependencies by it's identifier
        self::$registry->getMap()->wire(
            Doozr_Di_Container::MODE_STATIC,
            array(
                'Doozr_Path' => $path
            )
        );

        // Update existing map with newly added dependencies
        self::$registry->getContainer()->setMap(self::$registry->getMap());
        self::$registry->setModel(self::$registry->getContainer()->build('Doozr_Model', array($databaseConfiguration)));

        // Important for bootstrap result
        return true;
    }

    /**
     * Initializes the default services for current running-runtimeEnvironment.
     * Running runtimeEnvironment depends on used interface. It can be either CLI (Console) or WEB (Browser).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success
     * @access protected
     * @static
     */
    protected static function initServices()
    {
        // Get default services for runtimeEnvironment
        $services = self::$registry->getConfiguration()
            ->kernel
            ->services
            ->{strtolower(DOOZR_RUNTIME_ENVIRONMENT)};

        foreach ($services as $service) {
            self::$registry->{$service} = Doozr_Loader_Serviceloader::load($service);
        }

        // Important for bootstrap result
        return true;
    }

    /**
     * Stops the timer for measurements and log the core-execution time.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     * @static
     */
    protected static function stopTimer()
    {
        // calculate and store core execution time
        self::$coreExecutionTime = self::getDateTime()->getMicrotimeDiff(self::$starttime);

        // log core execution time
        self::$registry->logger->debug('Kernel execution-time: ' . self::$coreExecutionTime . ' seconds');
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
     * @access protected
     * @static
     * @throws Doozr_Kernel_Exception
     */
    protected static function coreError($error, $fatal = true)
    {
        // check for error type (simple core error | fatal error = execution stops!)
        if ($fatal) {
            $type = E_USER_CORE_FATAL_EXCEPTION;
        } else {
            $type = E_USER_CORE_EXCEPTION;
        }

        // throw the core error as exception
        throw new Doozr_Kernel_Exception(
            $error,
            $type
        );

        // Return FALSE so we can use the result of this method as return value for caller
        return false;
    }

    /**
     * Returns instance of Datetime module
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Datetime_Service An instance of module Datetime
     * @access protected
     * @static
     */
    protected static function getDateTime()
    {
        if (!self::$dateTime) {
            self::$dateTime = Doozr_Loader_Serviceloader::load('datetime');
        }

        return self::$dateTime;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the starttime of core execution.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return float Microtime
     * @access public
     */
    public static function getKernelStarttime()
    {
        return self::$starttime;
    }

    /**
     * Returns the total-time of core execution.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return float Microtime
     * @access public
     */
    public static function getKernelExecutiontime()
    {
        return self::$coreExecutionTime;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | MAGIC
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * On destruction of class.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __destruct()
    {
        // Log request serving time -> but only if logger available!
        if (self::$registry->getLogger()) {
            self::$registry->getLogger()->debug(
                'Request cycle completed in: ' . self::getDateTime()->getMicrotimeDiff(self::$starttime) . ' seconds'
            );

            // Log memory usage
            $memoryUsage = number_format(round(memory_get_peak_usage() / 1024 / 1024, 2), 2);
            self::$registry->getLogger()->debug(
                'Total consumed memory: ' . $memoryUsage . ' MB'
            );
        }

        // Save session
        session_write_close();
    }
}
