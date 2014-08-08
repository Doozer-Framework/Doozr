<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Debug
 *
 * Debug.php - Configures PHP dynamic in debug-mode and setup hooks
 * on important parts.
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
 * @subpackage DoozR_Core_Debug
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Class/Singleton/Strict.php';

use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

/**
 * DoozR - Debug
 *
 * Configures PHP dynamic in debug-mode and setup hooks
 * on important parts.
 *
 * @category   DoozR
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Debug
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Debug extends DoozR_Base_Class_Singleton_Strict
{
    /**
     * The debug-mode state (true = enabled / false = disabled)
     *
     * @var boolean
     * @access protected
     */
    protected $enabled = false;

    /**
     * Instance of logger
     *
     * @var DoozR_Logger
     * @access protected
     */
    protected $logger;


    /**
     * Constructor.
     *
     * @param DoozR_Logger_Interface $logger  An instance of DoozR_Logger
     * @param boolean                $enabled Defines it debug mode is enabled or not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Debug
     * @access protected
     */
    protected function __construct(DoozR_Logger_Interface $logger, $enabled = false)
    {
        // store instances
        $this->logger = $logger;

        // log debug state
        $this->logger->debug(
            'Debug-Manager - debug-mode enabled = ' . strtoupper(var_export($enabled, true))
        );

        // check for initial trigger
        if ($enabled) {
            $this->enable();
        } else {
            $this->disable();
        }
    }

    /**
     * Enables debugging.
     *
     * This method is intend to enable debugging.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @throws DoozR_Debug_Exception
     */
    public function enable()
    {
        if ($this->prepareForDevelopment() === true) {
            $this->enabled = true;
            $this->installWhoops();
            $this->logger->debug('Debug-mode successfully enabled.');

        } else {
            $this->enabled = false;

            throw new DoozR_Debug_Exception(
                'Debug-mode could not be enabled! Your system isn\'t configurable at runtime.'
            );
        }
    }

    /**
     * Disables debugging.
     *
     * This method is intend to disable debugging.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @throws DoozR_Debug_Exception
     */
    public function disable()
    {
        if ($this->prepareForProduction() === true) {
            $this->enabled = false;
            $this->uninstallWhoops();
            $this->logger->debug('Debug-mode successfully disabled!');

        } else {
            $this->enabled = true;

            throw new DoozR_Debug_Exception(
                'Debug-mode could not be disabled!'
            );
        }
    }

    /**
     * Installs whoops exception handler.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function installWhoops()
    {
        $whoops = new Run();

        // Configure the PrettyPageHandler
        $exceptionHandler = new PrettyPageHandler();

        // Add some DoozR specific ingredients
        $exceptionHandler->setPageTitle('DoozR');
        $exceptionHandler->addDataTable("DoozR runtime environment", array(
                "DOOZR_OS"            => DOOZR_OS,
                "DOOZR_PHP_VERSION"   => DOOZR_PHP_VERSION,
                "DOOZR_DOCUMENT_ROOT" => DOOZR_DOCUMENT_ROOT
            )
        );

        $whoops->pushHandler($exceptionHandler);
        $whoops->register();
    }

    /**
     * Uninstalls whoops exception handler.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function uninstallWhoops()
    {
        $whoops = new Run();
        $whoops->unregister();
    }

    /**
     * Enables debugging.
     *
     * This method is to enable debugging.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if debug was successfully enabled, otherwise FALSE
     * @access protected
     */
    protected function prepareForDevelopment()
    {
        // set error reporting to maximum output
        error_reporting(DOOZR_PHP_ERROR_MAX);

        // ini_set() can only be used if php version is >= 5.3 (cause from PHP 5.3 safemode
        // is deprecated and from PHP 5.4 it is removed) or if safemode is Off.
        if (DOOZR_PHP_VERSION >= 5.3 || !ini_get('safemode')) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            ini_set('track_errors', 1);
            ini_set('log_errors', 1);
            ini_set('html_errors', 1);
            $result = true;

        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * Disables debugging.
     *
     * This method is intend to disable debugging.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if debug was successfully disabled, otherwise FALSE
     * @access protected
     */
    protected function prepareForProduction()
    {
        // if debug-mode is disabled we must hide all errors to prevent the app from information leakage.
        // set error_reporting to null (0) to hide PHP's reports
        error_reporting(0);

        // ini_set() can only be used if php version is >= 5.3 (cause from PHP 5.3 safemode
        // is deprecated and from PHP 5.4 it is removed) or if safemode is Off.
        if (DOOZR_PHP_VERSION >= 5.3 || !ini_get('safemode')) {
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
            ini_set('track_errors', 0);
            ini_set('log_errors', 1);
            ini_set('html_errors', 0);

        }

        // and return -> success
        return true;
    }
}
