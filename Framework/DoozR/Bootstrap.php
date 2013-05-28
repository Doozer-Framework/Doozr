<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Bootstrapper
 *
 * Bootstrap.php - The Bootstrapper of the DoozR-Framework.
 * Delegates important operations at startup of DoozR.
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
 * @subpackage DoozR_Core_Bootstrap
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

/***********************************************************************************************************************
 * // BEGIN PATCHING CONSTANT WITH MICROTIME FOR MEASUREMENTS
 **********************************************************************************************************************/

$_SERVER['REQUEST_TIME'] = microtime();

/***********************************************************************************************************************
 * \\ END PATCHING CONSTANT WITH MICROTIME FOR MEASUREMENTS
 **********************************************************************************************************************/

/***********************************************************************************************************************
 * // BEGIN PATHFINDER
 **********************************************************************************************************************/

// systems directory separator
$s = DIRECTORY_SEPARATOR;

// retrieve path to file without! resolving possible symlinks
$partial = explode($s,__FILE__);
$root    = $_SERVER['DOCUMENT_ROOT'];
$path    = '';

for ($i = count($partial)-1; $i > -1; --$i) {
    $path = $s.$partial[$i].$path;

    if (realpath($root.$path) === __FILE__) {
        $path = ($s === '\\')
            ? str_replace('/', '\\', $root.$path)
            : str_replace('\\', '/', $root.$path);
        define('__FILE_LINK__', $path);
    }
}

if (!defined('__FILE_LINK__')) {
    define('__FILE_LINK__', __FILE__);
}

// retrieve absolute path to DoozR - make it our new document root -> by file link
define('DOOZR_DOCUMENT_ROOT', str_replace('DoozR'.$s.'Bootstrap.php', '', __FILE_LINK__));

/***********************************************************************************************************************
 * \\ END
 **********************************************************************************************************************/

/***********************************************************************************************************************
 * // BEGIN AUTOLOADING (SPL)
 **********************************************************************************************************************/

// SPL facade files config + facade itself
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Loader/Autoloader/Spl/Config.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Loader/Autoloader/Spl/Facade.php';

// now configure a new autoloader spl config
$autoloaderDoozR = new DoozR_Loader_Autoloader_Spl_Config();
$autoloaderDoozR
    ->setNamespace('DoozR')
    ->setNamespaceSeparator('_')
    ->addExtension('php')
    ->setPath(substr(DOOZR_DOCUMENT_ROOT, 0, -1))
    ->setDescription('DoozR\'s main autoloader and responsible for loading core classes')
    ->setPriority(0);

$autoloaderDoodi = new DoozR_Loader_Autoloader_Spl_Config();
$autoloaderDoodi
    ->setNamespace('Doodi')
    ->setNamespaceSeparator('_')
    ->addExtension('php')
    ->setPath(DOOZR_DOCUMENT_ROOT.'Model')
    ->setDescription('Doodi\'s autoloader responsible for loading Doodi\'s + libs classes')
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
        $autoloaderDoodi
    )
);

/***********************************************************************************************************************
 * \\ END
 **********************************************************************************************************************/

/***********************************************************************************************************************
 * // BEGIN EXTEND PHP's FUNCTIONALITY + LOAD PHP 5.3 EMULATOR-FUNCTIONS FOR PHP < 5.3
 **********************************************************************************************************************/

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Extend.php';

/***********************************************************************************************************************
 * \\ END EXTEND PHP's FUNCTIONALITY + LOAD PHP 5.3 EMULATOR-FUNCTIONS FOR PHP < 5.3
 **********************************************************************************************************************/

/***********************************************************************************************************************
 * // BEGIN ERROR-HANDLING (HOOK)
 **********************************************************************************************************************/

// include required files
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Handler/Error.php';

// register DoozR's Error-Handler
set_error_handler(
    array(
        'DoozR_Handler_Error',
        'handle'
    )
);

// hook for theoretically "unhandable error(s)" like E_PARSE (smart-hack)
register_shutdown_function(
    array(
        'DoozR_Handler_Error',
        'handleUnhandable'
    )
);

/***********************************************************************************************************************
 * \\ END ERROR-HANDLING (HOOK)
 **********************************************************************************************************************/

/***********************************************************************************************************************
 * // BEGIN EXCEPTION-HANDLING (HOOK)
 **********************************************************************************************************************/

// EXCEPTION-HANDLER: register exception-handler (dispatches calls to error-handler)
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Handler/Exception.php';

// set the own exception_handler
set_exception_handler(
    array(
        'DoozR_Handler_Exception',
        'handle'
    )
);

/***********************************************************************************************************************
 * \\ END EXCEPTION-HANDLING (HOOK)
 **********************************************************************************************************************/

/***********************************************************************************************************************
 * // BEGIN LOAD DoozR's CORE-CLASS
 **********************************************************************************************************************/

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Core.php';

/***********************************************************************************************************************
 * \\ END LOAD DoozR's CORE-CLASS
 **********************************************************************************************************************/

?>
