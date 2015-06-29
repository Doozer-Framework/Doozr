<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Bootstrap
 *
 * Bootstrap.php - The Bootstrapper of the Doozr-Framework.
 * Delegates important operations at startup of Doozr.
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
 * @subpackage Doozr_Kernel_Bootstrap
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

/*----------------------------------------------------------------------------------------------------------------------
| PATCHING CONSTANT WITH MICROTIME FOR MEASUREMENTS
+---------------------------------------------------------------------------------------------------------------------*/

$_SERVER['REQUEST_TIME'] = microtime();

/*----------------------------------------------------------------------------------------------------------------------
| PATHFINDER
+---------------------------------------------------------------------------------------------------------------------*/

// systems directory separator
$s = DIRECTORY_SEPARATOR;

// First we check for defined constant DOOZR_DOCUMENT_ROOT ...
if (false === defined('DOOZR_DOCUMENT_ROOT')) {

    // Then for environment variable
    if (false === $documentRoot = getenv('DOOZR_DOCUMENT_ROOT')) {

        // Retrieve path to file without! resolving possible symlinks
        $partial = explode($s, __FILE__);
        $root    = $_SERVER['DOCUMENT_ROOT'];
        $path    = '';

        for ($i = count($partial) - 1; $i > -1; --$i) {
            $path = $s . $partial[$i] . $path;

            if (realpath($root.$path) === __FILE__) {
                $path = $root.$path;
                $path = ($s === '\\')
                    ? str_replace('/', '\\', $path)
                    : str_replace('\\', '/', $path);
                define('__FILE_LINK__', $path);

                break;
            }
        }

        if (!defined('__FILE_LINK__')) {
            define('__FILE_LINK__', __FILE__);
        }

        // retrieve absolute path to Doozr - make it our new document root -> by file link
        $documentRoot = str_replace('Doozr' . $s . 'Bootstrap.php', '', __FILE_LINK__);
    }

    // Finally we store as constant for further use
    define('DOOZR_DOCUMENT_ROOT', $documentRoot);
}

/*----------------------------------------------------------------------------------------------------------------------
| LOAD KERNEL
+---------------------------------------------------------------------------------------------------------------------*/

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Kernel/App.php';

/*----------------------------------------------------------------------------------------------------------------------
| CHECK FOR PASSED APP PATH
+---------------------------------------------------------------------------------------------------------------------*/

// First we check for configured path to application DOOZR_APP_ROOT
if (false === defined('DOOZR_APP_ROOT')) {

    // Then for environment variable
    if (false === $appRoot = getenv('DOOZR_APP_ROOT')) {

        // Priority #1: App-Root by Document-Root
        if (false === $defaultAppRoot = realpath($_SERVER['DOCUMENT_ROOT'] . $s . '..' . $s . 'app')) {

            // Priority #2: App-Root by Doozr Document-Root
            $defaultAppRoot = realpath(DOOZR_DOCUMENT_ROOT . '../app');
        }

        $appRoot = ($defaultAppRoot !== false) ? $defaultAppRoot : '';
        $appRoot = rtrim($appRoot, $s) . $s;
    }

    // Finally store a constant for further use
    define('DOOZR_APP_ROOT', $appRoot);
}

/*----------------------------------------------------------------------------------------------------------------------
| PATH FOR ALL TEMPORARY STUFF (FILESYSTEM)
+---------------------------------------------------------------------------------------------------------------------*/
define('DOOZR_SYSTEM_TEMP', sys_get_temp_dir() . DIRECTORY_SEPARATOR);

/*----------------------------------------------------------------------------------------------------------------------
| RUNTIME ENVIRONMENT
+---------------------------------------------------------------------------------------------------------------------*/

// First we check for defined constant DOOZR_RUNTIME_ENVIRONMENT ...
if (false === defined('DOOZR_RUNTIME_ENVIRONMENT')) {

    // Then for environment variable
    if (false === $runtimeEnvironment = getenv('DOOZR_RUNTIME_ENVIRONMENT')) {

        // Retrieve by detecting
        $runtimeEnvironment = detectRuntimeEnvironment();
    }

    define('DOOZR_RUNTIME_ENVIRONMENT', $runtimeEnvironment);
}

/*----------------------------------------------------------------------------------------------------------------------
| CACHE CONTAINER
+---------------------------------------------------------------------------------------------------------------------*/

// First we check for defined constant DOOZR_CACHE_CONTAINER ...
if (false === defined('DOOZR_CACHE_CONTAINER')) {

    // Then for environment variable
    if (false === $doozrCacheContainer = getenv('DOOZR_CACHE_CONTAINER')) {

        // Default = Filesystem
        $doozrCacheContainer = 'filesystem';
    }

    define('DOOZR_CACHE_CONTAINER', $doozrCacheContainer);
}

/*----------------------------------------------------------------------------------------------------------------------
| LOGGING
+---------------------------------------------------------------------------------------------------------------------*/

// First we check for defined constant DOOZR_LOGGING ...
if (false === defined('DOOZR_LOGGING')) {

    // Then for environment variable ...
    if (false === $doozrLogging = getenv('DOOZR_LOGGING')) {

        // Default by app environment
        if (DOOZR_APP_ENVIRONMENT !== Doozr_Kernel::APP_ENVIRONMENT_TESTING) {
            $doozrLogging = true;
        }
    }

    define('DOOZR_LOGGING', (bool)$doozrLogging);
}

/*----------------------------------------------------------------------------------------------------------------------
| DEBUGGING
+---------------------------------------------------------------------------------------------------------------------*/

// First we check for defined constant DOOZR_LOGGING ...
if (false === defined('DOOZR_DEBUGGING')) {

    // Then for environment variable
    if (false === $doozrDebugging = getenv('DOOZR_DEBUGGING')) {

        // Default by app environment
        if (DOOZR_APP_ENVIRONMENT  === Doozr_Kernel::APP_ENVIRONMENT_DEVELOPMENT) {
            $doozrDebugging = true;
        }
    }

    define('DOOZR_DEBUGGING', $doozrDebugging);
}

/*----------------------------------------------------------------------------------------------------------------------
| CACHING
+---------------------------------------------------------------------------------------------------------------------*/

// First we check for defined constant DOOZR_LOGGING ...
if (false === defined('DOOZR_CACHING')) {

    // Then for environment variable
    if (false === $doozrCaching = getenv('DOOZR_CACHING')) {

        // Default by app environment
        if (DOOZR_APP_ENVIRONMENT  === Doozr_Kernel::APP_ENVIRONMENT_DEVELOPMENT) {
            $doozrCaching = false;
        } else {
            $doozrCaching = !DOOZR_DEBUGGING;
        }
    }

    define('DOOZR_CACHING', (bool)$doozrCaching);
}

/*----------------------------------------------------------------------------------------------------------------------
| COMPOSER INTEGRATION
+---------------------------------------------------------------------------------------------------------------------*/

// Try to include composer's autoloader to make all the composer stuff easy available
if (false === composer_running()) {
    include_once DOOZR_DOCUMENT_ROOT.'../vendor/autoload.php';
}

/*----------------------------------------------------------------------------------------------------------------------
| EXTEND PHP's FUNCTIONALITY + LOAD PHP 5.3 EMULATOR-FUNCTIONS FOR PHP < 5.3
+---------------------------------------------------------------------------------------------------------------------*/

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Extend.php';

/*----------------------------------------------------------------------------------------------------------------------
| AUTOLOADING (SPL)
+---------------------------------------------------------------------------------------------------------------------*/

// SPL facade files config + facade itself
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Loader/Autoloader/Spl/Configuration.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Loader/Autoloader/Spl/Facade.php';

// now configure a new autoloader spl config
$autoloaderDoozr = new Doozr_Loader_Autoloader_Spl_Config();
$autoloaderDoozr
    ->setNamespace('Doozr')
    ->setNamespaceSeparator('_')
    ->addExtension('php')
    ->setPath(substr(DOOZR_DOCUMENT_ROOT, 0, -1))
    ->setDescription('Doozr\'s autoloader for loading classes of Doozr below "lib/".')
    ->setPriority(0);

/**
 * Autoloader for Doozr - Services (native)
 */
$autoloaderService = new Doozr_Loader_Autoloader_Spl_Config();
$autoloaderService
    ->setNamespace('Doozr')
    ->setNamespaceSeparator('_')
    ->addExtension('php')
    ->setPath(DOOZR_DOCUMENT_ROOT . 'Service')
    ->setDescription('Doozr\'s autoloader for loading Services of Doozr below "lib/Service".')
    ->setPriority(1);

/**
 * The facade itself is auto instanciating singleton within the
 * register method if not already instantiated! So don't worry
 * just call the register() method pass a config and everything
 * is handled magically (:
 */
Doozr_Loader_Autoloader_Spl_Facade::attach(
    array(
        $autoloaderDoozr,
        $autoloaderService,
    )
);

/*----------------------------------------------------------------------------------------------------------------------
 | ERROR & EXCEPTION-HANDLING (HOOK)
 ---------------------------------------------------------------------------------------------------------------------*/

// We install the generic handler here! This one is used if not development runtimeEnvironment is enabled
// ERROR-HANDLER: register error-handler
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Handler/Error.php';

// Set the own exception_handler
set_error_handler(
    array(
        'Doozr_Handler_Error',
        'handle'
    )
);

// Hook for theoretically "unhandable error(s)" like E_PARSE (smart-hack)
register_shutdown_function(
    array(
        'Doozr_Handler_Error',
        'handleUnhandable'
    )
);

// EXCEPTION-HANDLER: register exception-handler
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Handler/Exception.php';

// Set own exception_handler
set_exception_handler(
    array(
        'Doozr_Handler_Exception',
        'handle'
    )
);

/*----------------------------------------------------------------------------------------------------------------------
| HELPER
+---------------------------------------------------------------------------------------------------------------------*/

/**
 * Detects composer in global scope
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return bool TRUE if composer is active, otherwise FALSE
 * @access public
 */
function composer_running()
{
    $result = false;
    $classes = get_declared_classes();
    natsort($classes);
    foreach ($classes as $class) {
        if (stristr($class, 'ComposerAutoloaderInit')) {
            $result = true;
            break;
        }
    }

    return $result;
}

/**
 * Returns the runtime environment by PHP SAPI.
 *
 * PHP has a lot of known SAPIs and we need just to distinguish between Cli, Web and Httpd (Running on PHP's
 * internal webserver). Some of PHP's known SAPIs:
 * aolserver, apache, apache2filter, apache2handler, caudium, cgi (until PHP 5.3), cgi-fcgi, cli, continuity,
 * embed, isapi, litespeed, milter, nsapi, phttpd, pi3web, roxen, thttpd, tux und webjames ...
 *
 * @param string $sapi The SAPI of PHP
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return string The runtime environment the Kernel is running in
 * @access public
 */
function detectRuntimeEnvironment($sapi = PHP_SAPI)
{
    // Assume default running runtimeEnvironment
    $environment = Doozr_Kernel::RUNTIME_ENVIRONMENT_WEB;

    // Detect running runtimeEnvironment through php functionality
    switch ($sapi) {
        case 'cli':
            $environment = Doozr_Kernel::RUNTIME_ENVIRONMENT_CLI;
            break;

        case 'cli-server':
            $environment = Doozr_Kernel::RUNTIME_ENVIRONMENT_HTTPD;
            break;
    }

    return $environment;
}
