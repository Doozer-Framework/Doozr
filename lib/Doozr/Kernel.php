<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Kernel
 *
 * Kernel.php - Kernel of the Doozr Framework.
 *
 * PHP versions 5.5
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
 * @subpackage Doozr_Kernel_Component
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Kernel/Interface.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Kernel/App/Interface.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Class/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Registry.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Collection.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Importer/Json.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Map/Static.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Factory.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/Container.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Loader/Serviceloader.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Logging.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Path.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Configuration.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Encoding.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Locale.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Debugging.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Security.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Model.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Request/Arguments.php';

use DebugBar\StandardDebugBar;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Doozr - Kernel
 *
 * Kernel of the Doozr Framework.
 *
 * @category   Doozr
 * @package    Doozr_Kernel
 * @subpackage Doozr_Kernel_Component
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Kernel extends Doozr_Base_Class_Singleton
    implements
    Doozr_Kernel_Interface,
    Doozr_Kernel_App_Interface
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
     */
    const RUNTIME_ENVIRONMENT_CLI = 'Cli';

    /**
     * WEB running runtimeEnvironment
     *
     * @var string
     * @access public
     */
    const RUNTIME_ENVIRONMENT_WEB = 'Web';

    /**
     * HTTPD running runtimeEnvironment
     *
     * @var string
     * @access public
     */
    const RUNTIME_ENVIRONMENT_HTTPD = 'Httpd';

    /**
     * Applications environment for Testing
     *
     * @var string
     * @access public
     */
    const APP_ENVIRONMENT_TESTING = 'testing';

    /**
     * Applications environment for Production
     *
     * @var string
     * @access public
     */
    const APP_ENVIRONMENT_PRODUCTION = 'production';

    /**
     * Applications environment for Development
     *
     * @var string
     * @access public
     */
    const APP_ENVIRONMENT_DEVELOPMENT = 'development';

    /**
     * Applications environment for Staging
     *
     * @var string
     * @access public
     */
    const APP_ENVIRONMENT_STAGING = 'staging';

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is the constructor of the core class.
     *
     * @param string $appEnvironment     Application environment of the app running this Kernel instance
     * @param string $runtimeEnvironment Runtime environment of Doozr (PHP SAPI)
     * @param bool   $debugging          TRUE to enable debugging, FALSE to disable
     * @param bool   $caching            TRUE to enable caching, FALSE to disable
     * @param bool   $logging            TRUE to enable logging, FALSE to disable
     * @param string $documentRoot       The document root as string
     * @param string $appRoot            The app root as string
     * @param bool   $virtualized        TRUE to run Kernel virtualized, otherwise FALSE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Kernel
     * @access protected
     */
    protected function __construct(
        $appEnvironment,
        $runtimeEnvironment,
        $debugging,
        $caching,
        $logging,
        $documentRoot,
        $appRoot,
        $virtualized
    ) {
        // Start stopwatch
        self::startTimer();

        // Run internal bootstrap process
        self::bootstrap(
            true,
            $appEnvironment,
            $runtimeEnvironment,
            $debugging,
            $caching,
            $logging,
            $documentRoot,
            $appRoot,
            $virtualized
        );

        // Stop timer and store execution-time
        self::stopTimer();
    }

    /**
     * Proxy to getInstance to reduce confusion e.g. when bootstrapping the application.
     *
     * @param string $appEnvironment     Application environment of the app running this Kernel instance
     * @param string $runtimeEnvironment Runtime environment of Doozr (PHP SAPI)
     * @param bool   $debugging          TRUE to enable debugging, FALSE to disable
     * @param bool   $caching            TRUE to enable caching, FALSE to disable
     * @param bool   $logging            TRUE to enable logging, FALSE to disable
     * @param string $documentRoot       The document root as string
     * @param string $appRoot            The app root as string
     * @param bool   $virtualized        TRUE to run Kernel virtualized, otherwise FALSE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Kernel|Doozr_Kernel_App The Doozr Kernel instance
     * @access public
     */
    public static function boot(
        $appEnvironment     = self::APP_ENVIRONMENT_PRODUCTION,
        $runtimeEnvironment = self::RUNTIME_ENVIRONMENT_WEB,
        $debugging          = false,
        $caching            = false,
        $logging            = true,
        $documentRoot       = null,
        $appRoot            = null,
        $virtualized        = false
    ) {
        return Doozr_Kernel::getInstance(
            $appEnvironment,
            $runtimeEnvironment,
            $debugging,
            $caching,
            $logging,
            $documentRoot,
            $appRoot,
            $virtualized
        );
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
        // Include time from bootstrapping + routing?
        if ($includeWalltime) {
            self::$starttime = $_SERVER['REQUEST_TIME'];
        } else {
            self::$starttime = microtime();
        }
    }

    /**
     * Starts the bootstrapping process. It enables you to rerun the whole bootstrapping process from outside
     * by implementing this method as public. So you are able to unit-test your application with a fresh bootstrapped
     * core on each run. Able to  rerun (e.g. to support unit-testing on each run with fresh bootstrap!)
     * @link http://it-republik.de/php/news/Die-Framework-Falle-und-Wege-daraus-059217.html
     *
     * @param bool   $rerun              TRUE to rerun the bootstrap process, FALSE to keep state from last run
     * @param string $appEnvironment     Application environment of the app running this Kernel instance
     * @param string $runtimeEnvironment Runtime environment of Doozr (PHP SAPI)
     * @param bool   $debugging          TRUE to enable debugging, FALSE to disable
     * @param bool   $caching            TRUE to enable caching, FALSE to disable
     * @param bool   $logging            TRUE to enable logging, FALSE to disable
     * @param string $documentRoot       The document root as string
     * @param string $appRoot            The app root as string
     * @param bool   $virtualized        TRUE to run Kernel virtualized, otherwise FALSE
     *
     * @throws Doozr_Kernel_Exception
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @static
     */
    public static function bootstrap(
        $rerun              = true,
        $appEnvironment     = self::APP_ENVIRONMENT_PRODUCTION,
        $runtimeEnvironment = self::RUNTIME_ENVIRONMENT_WEB,
        $debugging          = false,
        $caching            = false,
        $logging            = true,
        $documentRoot       = '',
        $appRoot            = '',
        $virtualized        = false
    ) {
        // Check for requested rerun. Prevent duplicate init. But enable to do.
        if ($rerun === true) {
            // Start init-stack ...
            if (!
                (
                    self::initRegistry(
                        [
                            'doozr.kernel.rerun'               => $rerun,
                            'doozr.kernel.app.environment'     => $appEnvironment,
                            'doozr.kernel.runtime.environment' => $runtimeEnvironment,
                            'doozr.kernel.debugging'           => $debugging,
                            'doozr.kernel.caching'             => $caching,
                            'doozr.kernel.logging'             => $logging,
                            'doozr.kernel.virtualized'         => $virtualized,
                            'doozr.document.root'              => $documentRoot,
                            'doozr.app.root'                   => $appRoot,
                        ]
                    ) &&
                    self::initDependencyInjection() &&
                    self::initFilesystem($virtualized) &&
                    self::initCache() &&
                    self::initLogging() &&
                    self::initPath() &&
                    self::initConfiguration() &&
                    self::configureLogging() &&
                    (
                        self::$registry->getLogger()
                            ->debug(
                                'Runtime environment: ' . $runtimeEnvironment
                            )
                            ->debug(
                                'Bootstrapping of Doozr (v ' . DOOZR_VERSION . ')'
                            )
                    ) &&
                    self::initSystem() &&
                    self::initEncoding() &&
                    self::initLocale() &&
                    self::initDebugging() &&
                    #self::initSecurity() &&
                    #self::initRequest() &&
                    #self::initResponse() &&
                    self::initModel() &&
                    self::initServices()
                )
            ) {
                throw new Doozr_Kernel_Exception(
                    'Error while bootstrapping Doozr Kernel. This should never happen. Stopping execution.'
                );
            }
        }
    }

/*
->debug(
'Bootstrapping of Doozr (v ' . DOOZR_VERSION . ')'
)*/

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
            Doozr_Loader_Serviceloader::load('cache', DOOZR_CACHE_CONTAINER, $namespace, [], DOOZR_UNIX)
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
        /**
         * Create instances of required classes
         * Create instance of Doozr_Di_Map_Static and pass required classes as arguments to constructor
         * The Di-Map builder requires two objects Collection + Importer
         */
        $collection = new Doozr_Di_Collection();
        $importer   = new Doozr_Di_Importer_Json();
        $map        = new Doozr_Di_Map_Static($collection, $importer);

        // Generate map from static JSON map of Doozr
        $map->generate(DOOZR_DOCUMENT_ROOT . 'Data/Private/Config/.map.json');

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
     * @param array $parameters The parameters to store in parameter bag.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success
     * @access protected
     * @static
     */
    protected static function initRegistry(array $parameters = [])
    {
        self::$registry = Doozr_Registry::getInstance($parameters);

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
    protected static function initLogging()
    {
        // Add additional/new dependencies
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
        /* @var Doozr_Cache_Service $cache */
        $cache   = self::getRegistry()->getCache();
        $caching = self::$registry->getParameter('doozr.kernel.caching');

        // Add additional/new dependencies
        self::$registry->getMap()->wire(
            Doozr_Di_Container::MODE_STATIC,
            array(
                'Doozr_Configuration_Reader_Json' => new Doozr_Configuration_Reader_Json(
                    Doozr_Loader_Serviceloader::load('filesystem'),
                    $cache,
                    $caching
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
                $caching
            )
        );

        // Read config of: Doozr - central core configuration from developer
        $config->read(
            self::$registry->getPath()->get('config') . '.config.json'
        );









        /**
         * READ CONFIG OF SERVICES
         */
        /*
        if (true === $caching) {
            // If caching is enabled we try to read service
            #$content = self::$registry->getFilesystem()->('');
            #$virtualFile = self::$registry->getPath()->get('config') . '.service.json';

        } else {

        }
        */

        $pattern = self::$registry->getPath()->get('service') . '*\\*\\.config.json';
        $files   = glob($pattern, GLOB_NOSORT);

        foreach ($files as $file) {
            $config->read($file);
        }
        /**
         * END CONFIG OF SERVICES
         */











        $userlandConfigurationFile = self::$registry->getPath()->get(
            'app',
            'Data\Private\Config\.config.' . self::$registry->getParameter('doozr.kernel.app.environment') . '.json'
        );

        if (
            true === self::$registry->getFilesystem()->exists($userlandConfigurationFile) &&
            true === self::$registry->getFilesystem()->readable($userlandConfigurationFile)
        ) {
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
        if (true === self::$registry->getParameter('doozr.kernel.logging')) {

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
            /* @todo CHECK disable() useful? makes sense? */
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
                'Doozr_Logging'       => self::$registry->getLogger()
            )
        );

        // update map => intentionally this method is used for setting a new map but it
        // does also work for our use-case ... to inject an updated map on each call
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
     * @link   http://de.wikipedia.org/wiki/Locale
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
    protected static function initDebugging()
    {
        if (true === self::$registry->getParameter('doozr.kernel.debugging')) {
            // Get debug manager
            self::$registry->setDebugging(
                self::$registry->getContainer()->build(
                    'Doozr_Debugging',
                    array(
                        self::$registry->getParameter('doozr.kernel.debugging'),
                        DOOZR_PHP_VERSION,
                        (self::RUNTIME_ENVIRONMENT_CLI === DOOZR_RUNTIME_ENVIRONMENT),
                        DOOZR_PHP_ERROR_MAX,
                    )
                )
            );

            $debugbar = new StandardDebugBar();
            $debugbar['time']->startMeasure('request-cycle', 'Request cycle (Doozr)');
            $debugbar->addCollector(
                new DebugBar\DataCollector\ConfigCollector(
                    json_decode(json_encode(self::$registry->getConfiguration()->get()), true)
                )
            );

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
        if (true === self::$registry->getParameter('doozr.kernel.debugging')) {
            self::$registry->getDebugbar()['time']->startMeasure('request-parsing', 'Parsing request');
        }

        self::$registry->setRequest(
            self::$registry->getContainer()->build(
                'Doozr_Request_' . DOOZR_RUNTIME_ENVIRONMENT
            )
        );

        if (true === self::$registry->getParameter('doozr.kernel.debugging')) {
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
        if (true === self::$registry->getParameter('doozr.kernel.debugging')) {
            self::$registry->getDebugbar()['time']->startMeasure('preparing-response', 'Preparing response');
        }

        self::$registry->setResponse(
            self::$registry->getContainer()->build(
                'Doozr_Response_' . DOOZR_RUNTIME_ENVIRONMENT
            )
        );

        if (true === self::$registry->getParameter('doozr.kernel.debugging')) {
            self::$registry->getDebugbar()['time']->stopMeasure('preparing-response');
        }

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
     * @return bool False always
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

    /**
     * Handles a Request to convert it to a Response.
     *
     * When $catch is true, the implementation must catch all exceptions and do its best to convert them to a Response
     * instance.
     *
     * @param Request  $request  Request instance
     * @param Response $response Response instance
     * @param bool     $catch    Whether to catch exceptions or not
     * @param bool     $expose   Whether to expose sensitive information or not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Response A Response instance
     * @access public
     * @throws \Exception When an Exception occurs during processing
     */
    public function handle(
        Request $request,
        Response $response,
        $catch = true,
        $expose = false
    ) {
        try {
            /* @var $router Doozr_Route */
            $router  = self::$registry->getContainer()->build('Doozr_Route');
            $request = $router->route($request);

            /* @var $requestDispatcher Doozr_Request_Dispatcher */
            $requestDispatcher = self::$registry->getContainer()->build('Doozr_Request_Dispatcher');

            // Retrieving response by dispatching "request + route" to request dispatcher
            $response = $requestDispatcher
                ->marshall($request, $response)
                ->run();

        } catch (\Exception $exception) {
            if (true === $catch) {
                $response = $this->buildErrorResponse(
                    $exception->getCode(),
                    $exception->getMessage(),
                    $response,
                    $expose
                );

            } else {
                throw $exception;
            }
        }

        return $response;
    }

    /**
     * Returns a prepared response with error message and HTTP status code set.
     *
     * @param Response $response      The response instance
     * @param int      $statusCode    The status code
     * @param string   $statusMessage The status message
     * @param bool     $expose        Controls whether the real information will be send or the default HTTP one
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Response The modified response instance
     * @access protected
     */
    protected function buildErrorResponse(Response $response, $statusCode, $statusMessage, $expose = false)
    {
        // Normalize for Exceptions from other parts of the app.
        if ($statusCode < 200 || $statusCode > 510) {
            $statusCode = 500;
        }

        $body = new Doozr_Response_Body('php://memory', 'w');

        // Do not expose secret information
        if (false === $expose) {
            $statusMessage = constant('Doozr_Http::REASONPHRASE_' . $statusCode);
        }
        $body->write('<h1>' . $statusMessage . '</h1>');

        return $response
            ->withStatus($statusCode, $statusMessage)
            ->withBody($body);
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
        if (null !== self::$registry->getLogger()) {
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
