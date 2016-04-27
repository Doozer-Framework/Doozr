<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Bootstrap.
 *
 * Bootstrap.php - Bootstrapping important operations at startup of Doozr.
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
        $root = $_SERVER['DOCUMENT_ROOT'];
        $path = '';

        for ($i = count($partial) - 1; $i > -1; --$i) {
            $path = $s.$partial[$i].$path;

            if (realpath($root.$path) === __FILE__) {
                $path = $root.$path;
                $path = ($s === '\\')
                    ? str_replace('/', '\\', $path)
                    : str_replace('\\', '/', $path);
                define('__FILE_LINK__', $path);

                break;
            }
        }

        if (false === defined('__FILE_LINK__')) {
            define('__FILE_LINK__', __FILE__);
        }

        // retrieve absolute path to Doozr - make it our new document root -> by file link
        $documentRoot = str_replace('Doozr'.$s.'Bootstrap.php', '', __FILE_LINK__);
    }

    // Finally we store as constant for further use
    define('DOOZR_DOCUMENT_ROOT', $documentRoot);
}

/*----------------------------------------------------------------------------------------------------------------------
| LOAD KERNEL
+---------------------------------------------------------------------------------------------------------------------*/

require_once DOOZR_DOCUMENT_ROOT.'Doozr/Kernel/App.php';

/*----------------------------------------------------------------------------------------------------------------------
| CHECK FOR PASSED APP PATH
+---------------------------------------------------------------------------------------------------------------------*/

// First we check for configured path to application DOOZR_APP_ROOT
if (false === defined('DOOZR_APP_ROOT')) {

    // Then for environment variable
    if (false === $appRoot = getenv('DOOZR_APP_ROOT')) {

        // Priority #1: App-Root by Document-Root
        if (false === $defaultAppRoot = realpath($_SERVER['DOCUMENT_ROOT'].$s.'..'.$s.'app')) {

            // Priority #2: App-Root by Doozr Document-Root
            $defaultAppRoot = realpath(DOOZR_DOCUMENT_ROOT.'../app');
        }

        $appRoot = ($defaultAppRoot !== false) ? $defaultAppRoot : '';
        $appRoot = rtrim($appRoot, $s).$s;
    }

    // Finally store a constant for further use
    define('DOOZR_APP_ROOT', $appRoot);
}

/*----------------------------------------------------------------------------------------------------------------------
| PATH FOR ALL TEMPORARY STUFF (FILESYSTEM)
+---------------------------------------------------------------------------------------------------------------------*/
define('DOOZR_DIRECTORY_TEMP', sys_get_temp_dir().DIRECTORY_SEPARATOR);

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

// First we check for defined constant DOOZR_CACHING_CONTAINER ...
if (false === defined('DOOZR_CACHING_CONTAINER')) {

    // Then for environment variable
    if (false === $doozrCacheContainer = getenv('DOOZR_CACHING_CONTAINER')) {

        // Default = Filesystem
        $doozrCacheContainer = 'filesystem';
    }

    define('DOOZR_CACHING_CONTAINER', $doozrCacheContainer);
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

    define('DOOZR_LOGGING', (bool) $doozrLogging);
}

/*----------------------------------------------------------------------------------------------------------------------
| DEBUGGING
+---------------------------------------------------------------------------------------------------------------------*/

// First we check for defined constant DOOZR_DEBUGGING ...
if (false === defined('DOOZR_DEBUGGING')) {

    // Then for environment variable
    if (false === $doozrDebugging = getenv('DOOZR_DEBUGGING')) {

        // Default by app environment
        if (DOOZR_APP_ENVIRONMENT === Doozr_Kernel::APP_ENVIRONMENT_DEVELOPMENT) {
            $doozrDebugging = true;
        }
    }

    define('DOOZR_DEBUGGING', (bool) $doozrDebugging);
}

/*----------------------------------------------------------------------------------------------------------------------
| CACHING
+---------------------------------------------------------------------------------------------------------------------*/

// First we check for defined constant DOOZR_CACHING ...
if (false === defined('DOOZR_CACHING')) {

    // Then for environment variable
    if (false === $doozrCaching = getenv('DOOZR_CACHING')) {

        // Default by app environment
        if (DOOZR_APP_ENVIRONMENT === Doozr_Kernel::APP_ENVIRONMENT_DEVELOPMENT ||
            DOOZR_APP_ENVIRONMENT === Doozr_Kernel::APP_ENVIRONMENT_TESTING
        ) {
            $doozrCaching = false;
        } else {
            $doozrCaching = !DOOZR_DEBUGGING;
        }
    }

    define('DOOZR_CACHING', (bool) $doozrCaching);
}

/*----------------------------------------------------------------------------------------------------------------------
| PROFILING
+---------------------------------------------------------------------------------------------------------------------*/

// First we check for defined constant DOOZR_CACHING ...
if (false === defined('DOOZR_PROFILING')) {

    // Then for environment variable
    if (false === $doozrProfiling = getenv('DOOZR_PROFILING')) {

        // Default by app environment
        if (DOOZR_APP_ENVIRONMENT === Doozr_Kernel::APP_ENVIRONMENT_DEVELOPMENT) {
            $doozrProfiling = true;
        } else {
            $doozrProfiling = DOOZR_DEBUGGING;
        }
    }

    define('DOOZR_PROFILING', (bool) $doozrProfiling);
}

/*----------------------------------------------------------------------------------------------------------------------
| APPLICATIONS NAMESPACE
+---------------------------------------------------------------------------------------------------------------------*/

// First we check for defined constant DOOZR_APP_NAMESPACE ...
if (false === defined('DOOZR_APP_NAMESPACE')) {

    // Then for environment variable
    if (false === $doozrAppNamespace = getenv('DOOZR_APP_NAMESPACE')) {

        // Default = App\*
        $doozrAppNamespace = 'App';
    }

    define('DOOZR_APP_NAMESPACE', $doozrAppNamespace);
}

/*----------------------------------------------------------------------------------------------------------------------
| COMPOSER INTEGRATION
+---------------------------------------------------------------------------------------------------------------------*/

// Try to include composer's autoloader to make all the composer stuff easy available
if (false === composer_running()) {
    include_once DOOZR_DOCUMENT_ROOT.'../vendor/autoload.php';
}

/*----------------------------------------------------------------------------------------------------------------------
| DOOZR RUNTIME GLOBAL CONSTANTS
+---------------------------------------------------------------------------------------------------------------------*/

define('DOOZR_PHP_VERSION',    floatval(PHP_VERSION));
define('DOOZR_PHP_ERROR_MAX',  PHP_INT_MAX);
define('DOOZR_OS',             strtoupper(PHP_OS));
define('DOOZR_WINDOWS',        (substr(DOOZR_OS, 0, 3) === 'WIN') && DIRECTORY_SEPARATOR !== '/');
define('DOOZR_UNIX',           (DIRECTORY_SEPARATOR === '/' && DOOZR_WINDOWS === false));
define('DOOZR_SECURE_HASH',    (DOOZR_PHP_VERSION > 5.11));
define('DOOZR_SAPI',           php_sapi_name());
define('DOOZR_VERSION',        '$Id$');
define('DOOZR_NAME',           'Doozr');
define('DOOZR_NAMESPACE',      'Doozr');
define('DOOZR_NAMESPACE_FLAT', 'doozr');

/*----------------------------------------------------------------------------------------------------------------------
| EXTEND PHP's FUNCTIONALITY
+---------------------------------------------------------------------------------------------------------------------*/

require_once DOOZR_DOCUMENT_ROOT.'Doozr/Extend.php';

/*----------------------------------------------------------------------------------------------------------------------
| AUTOLOADING (SPL)
+---------------------------------------------------------------------------------------------------------------------*/

// SPL facade files configuration + facade itself
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Loader/Autoloader/Spl/Configuration.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Loader/Autoloader/Spl/Facade.php';

// now configure a new autoloader spl configuration
$autoloaderDoozr = new Doozr_Loader_Autoloader_Spl_Config();
$autoloaderDoozr
    ->_namespace('Doozr')
    ->namespaceSeparator('_')
    ->addExtension('php')
    ->path(substr(DOOZR_DOCUMENT_ROOT, 0, -1))
    ->description('Doozr\'s autoloader for loading classes of Doozr below "src/".')
    ->priority(0);

/*
 * Autoloader for Doozr - Services (native)
 */
$autoloaderService = new Doozr_Loader_Autoloader_Spl_Config();
$autoloaderService
    ->_namespace('Doozr')
    ->namespaceSeparator('_')
    ->addExtension('php')
    ->path(DOOZR_DOCUMENT_ROOT.'Service')
    ->description('Doozr\'s autoloader for loading Services of Doozr below "src/Service".')
    ->priority(1);

/*
 * The facade itself is auto instanciating singleton within the
 * register method if not already instantiated! So don't worry
 * just call the register() method pass a configuration and everything
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

// Install error handler which is used in case that DOOZR_APP_ENVIRONMENT is not development (= make use of Whoops)
if (Doozr_Kernel::APP_ENVIRONMENT_TESTING !== DOOZR_APP_ENVIRONMENT) {

    // ERROR-HANDLER: register error-handler
    require_once DOOZR_DOCUMENT_ROOT.'Doozr/Handler/Error.php';

    // Set own error_handler
    set_error_handler(
        array(
            'Doozr_Handler_Error',
            'handle',
        )
    );

    // Hook for theoretically "unhandable error(s)" like E_PARSE (smart-hack)
    register_shutdown_function(
        array(
            'Doozr_Handler_Error',
            'handleUnhandable',
        )
    );

    // EXCEPTION-HANDLER: register exception-handler
    require_once DOOZR_DOCUMENT_ROOT.'Doozr/Handler/Exception.php';

    // Set own exception_handler
    set_exception_handler(
        array(
            'Doozr_Handler_Exception',
            'handle',
        )
    );
}

/*----------------------------------------------------------------------------------------------------------------------
| HELPER
+---------------------------------------------------------------------------------------------------------------------*/

/**
 * Detects composer in global scope.
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 *
 * @return bool TRUE if composer is active, otherwise FALSE
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
 *
 * @return string The runtime environment the Kernel is running in
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
