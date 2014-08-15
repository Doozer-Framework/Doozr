<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Bootstrap
 *
 * Bootstrap.php - The Bootstrapper of the DoozR-Framework.
 * Delegates important operations at startup of DoozR.
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2014, Benjamin Carl - All rights reserved.
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
 * @subpackage DoozR_Core_Bootstrap
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
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

// try to get from env => priorized
$documentRoot = getenv('DOOZR_DOCUMENT_ROOT');

// if document root not passed via env:
if ($documentRoot === false) {

    // retrieve path to file without! resolving possible symlinks
    $partial = explode($s, __FILE__);
    $root    = $_SERVER['DOCUMENT_ROOT'];
    $path    = '';

    for ($i = count($partial)-1; $i > -1; --$i) {
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

    if (!defined('__FILE_LINK__')) {
        define('__FILE_LINK__', __FILE__);
    }

    // retrieve absolute path to DoozR - make it our new document root -> by file link
    $documentRoot = str_replace('DoozR'.$s.'Bootstrap.php', '', __FILE_LINK__);
}

// store as constant
define('DOOZR_DOCUMENT_ROOT', $documentRoot);

/*----------------------------------------------------------------------------------------------------------------------
| CHECK FOR PASSED APP PATH
+---------------------------------------------------------------------------------------------------------------------*/

$pathAppRoot = getenv('DOOZR_APP_ROOT');

if ($pathAppRoot !== false) {
    define('DOOZR_APP_ROOT', $pathAppRoot);
}

/*----------------------------------------------------------------------------------------------------------------------
| PATH FOR ALL TEMPORARY STUFF (FILESYSTEM)
+---------------------------------------------------------------------------------------------------------------------*/
define('DOOZR_SYSTEM_TEMP', sys_get_temp_dir() . DIRECTORY_SEPARATOR);

/*----------------------------------------------------------------------------------------------------------------------
| COMPOSER INTEGRATION
+---------------------------------------------------------------------------------------------------------------------*/

// Try to include composer's autoloader to make all the composer stuff easy available
@include_once DOOZR_DOCUMENT_ROOT . '../vendor/autoload.php';

/*----------------------------------------------------------------------------------------------------------------------
| EXTEND PHP's FUNCTIONALITY + LOAD PHP 5.3 EMULATOR-FUNCTIONS FOR PHP < 5.3
+---------------------------------------------------------------------------------------------------------------------*/

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Extend.php';

/*----------------------------------------------------------------------------------------------------------------------
| AUTOLOADING (SPL)
+---------------------------------------------------------------------------------------------------------------------*/

// SPL facade files config + facade itself
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Loader/Autoloader/Spl/Config.php';
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Loader/Autoloader/Spl/Facade.php';

// now configure a new autoloader spl config
$autoloaderDoozR = new DoozR_Loader_Autoloader_Spl_Config();
$autoloaderDoozR
    ->setNamespace('DoozR')
    ->setNamespaceSeparator('_')
    ->addExtension('php')
    ->setPath(substr(DOOZR_DOCUMENT_ROOT, 0, -1))
    ->setDescription('DoozR\'s main autoloader and responsible for loading core classes')
    ->setPriority(0);

/**
 * Autoloader for DoozR - Services (native)
 */
$autoloaderService = new DoozR_Loader_Autoloader_Spl_Config();
$autoloaderService
    ->setNamespace('DoozR')
    ->setNamespaceSeparator('_')
    ->addExtension('php')
    ->setPath(DOOZR_DOCUMENT_ROOT . 'Service')
    ->setDescription('DoozR\'s autoloader responsible for loading services from DoozR\'s namespace')
    ->setPriority(1);

/**
 * The facade itself is auto instanciating singleton within the
 * register method if not already instanciated! So don't worry
 * just call the register() method pass a config and everything
 * is handled magically (:
 */
DoozR_Loader_Autoloader_Spl_Facade::attach(
    array(
        $autoloaderDoozR,
        $autoloaderService,
    )
);

/*----------------------------------------------------------------------------------------------------------------------
 | ERROR & EXCEPTION-HANDLING (HOOK)
 ---------------------------------------------------------------------------------------------------------------------*/

// We install the generic handler here! This one is used if not development mode is enabled

/*----------------------------------------------------------------------------------------------------------------------
| LOAD DoozR's CORE-CLASS
+---------------------------------------------------------------------------------------------------------------------*/

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Core.php';
