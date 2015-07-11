<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Debugging
 *
 * Debugging.php - Configures some of PHP's runtime parameters for debugging.
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
 * @package    Doozr_Kernel
 * @subpackage Doozr_Kernel_Debugging
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Class/Singleton/Strict.php';

use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\PlainTextHandler;

/**
 * Doozr - Debugging
 *
 * Configures PHP for debugging. Configures some of PHP's runtime parameters.
 *
 * @category   Doozr
 * @package    Doozr_Kernel
 * @subpackage Doozr_Kernel_Debugging
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Debugging extends Doozr_Base_Class_Singleton_Strict
{
    /**
     * The debugging state (TRUE = enabled / FALSE = disabled)
     *
     * @var bool
     * @access protected
     */
    protected $enabled = false;

    /**
     * Instance of Doozr_Logging
     *
     * @var Doozr_Logging
     * @access protected
     */
    protected $logger;

    /**
     * The PHP Version Doozr is running on
     *
     * @var float
     * @access protected
     */
    protected $phpVersion;

    /**
     * CLI state.
     * TRUE = is Cli Environment | FALSE = not
     * Important for installing correct Whoops Handler
     *
     * @var bool
     * @access protected
     */
    protected $isCli = false;

    /**
     * Minimum error value (to disable)
     *
     * @var int
     * @access protected
     */
    protected $errorMinimum = 0;

    /**
     * Maximum error value (to enable very verbose)
     *
     * @var int
     * @access protected
     */
    protected $errorMaximum;

    /**
     * Instance of Whoops Error Handler
     *
     * @var \Whoops\Run
     * @access protected
     */
    protected $whoops;


    /**
     * Constructor.
     *
     * @param Doozr_Logging_Interface $logger       Instance of Doozr_Logging
     * @param bool                    $enabled      Defines it debugging mode is enabled or not
     * @param float                   $phpVersion   Active PHP version Doozr running on
     * @param bool                    $isCli        TRUE if Cli environment (other handlers), FALSE if not
     * @param int                     $errorMaximum Maximum error integer for PHP
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \Doozr_Debugging
     * @access protected
     */
    protected function __construct(
        Doozr_Logging_Interface $logger,
                                $enabled,
                                $phpVersion,
                                $isCli,
                                $errorMaximum
    ) {
        $this
            ->logger($logger)
            ->phpVersion($phpVersion)
            ->isCli($isCli)
            ->errorMaximum($errorMaximum)
            ->whoops(new Run());

        // Log debugging state
        $this->logger->debug(
            'Debugging Component ' . ((true === $enabled) ? 'en' : 'dis') . 'abled.'
        );

        // Check for initial trigger
        if (true === $enabled) {
            $this->enable();

        } else {
            $this->disable();
        }
    }



    protected function setLogger(Doozr_Logging_Interface $logger)
    {
        $this->logger = $logger;
    }

    protected function logger(Doozr_Logging_Interface $logger)
    {
        $this->setLogger($logger);
        return $this;
    }

    protected function getLogger()
    {
        return $this->logger;
    }


    protected function setPhpVersion($phpVersion)
    {
        $this->phpVersion = $phpVersion;
    }

    /**
     * @param $phpVersion
     *
     * @return $this
     */
    protected function phpVersion($phpVersion)
    {
        $this->setPhpVersion($phpVersion);

        return $this;
    }

    protected function getPhpVersion()
    {
        return $this->phpVersion;
    }


    protected function setIsCli($isCli)
    {
        $this->isCli = $isCli;
    }

    protected function isCli($isCli)
    {
        $this->setIsCli($isCli);

        return $this;
    }

    protected function getIsCli()
    {
        return $this->isCli;
    }


    protected function setErrorMaximum($errorMaximum)
    {
        $this->errorMaximum = $errorMaximum;
    }

    protected function errorMaximum($errorMaximum)
    {
        $this->setErrorMaximum($errorMaximum);
        return $this;
    }

    protected function getErrorMaximum()
    {
        return $this->errorMaximum;
    }

    protected function setWhoops($whoops)
    {
        $this->whoops = $whoops;
    }

    protected function whoops($whoops)
    {
        $this->setWhoops($whoops);
        return $this;
    }

    protected function getWhoops()
    {
        return $this->whoops;
    }


    /**
     * Enables debugging.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @throws Doozr_Debugging_Exception
     */
    public function enable()
    {
        if (true === $this->makeVerbose()) {
            $this->enabled = true;
            $this->installWhoops();
            $this->logger->debug('Debugging tools installed.');

        } else {
            $this->enabled = false;

            throw new Doozr_Debugging_Exception(
                'Debugging could not be enabled! Your system seems not configurable at runtime.'
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
     * @throws Doozr_Debugging_Exception
     */
    public function disable()
    {
        if (true === $this->makeSilent()) {
            $this->enabled = false;
            $this->uninstallWhoops();
            $this->logger->debug('Debugging successfully disabled.');

        } else {
            $this->enabled = true;

            throw new Doozr_Debugging_Exception(
                'Debugging could not be disabled!'
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
        // Configure the page handler of Whoops
        if (true === $this->isCli) {
            // Text for cli
            $exceptionHandler = new PlainTextHandler();

        } else {
            // Otherwise the pretty one
            $exceptionHandler = new PrettyPageHandler();

            // Add some Doozr specific ingredients
            $exceptionHandler->setPageTitle('Doozr');

            $constants = get_defined_constants();

            // Extract Doozr Constants as debugging information
            $data = [];
            foreach($constants as $key => $value) {
                if ('DOOZR_' === substr($key, 0, 6)) {
                    $data[$key] = (true === is_bool($value)) ? ((true === $value) ? 'TRUE' : 'FALSE') : $value;
                }
            }
            ksort($data);

            $exceptionHandler->addDataTable("Doozr runtime environment", $data);
        }

        $this->whoops->pushHandler($exceptionHandler);
        $this->whoops->register();
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
        $this->whoops->unregister();
    }

    /**
     * Makes PHP verbose to get at much debugging output as possible.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if debugging was successfully enabled, otherwise FALSE
     * @access protected
     */
    protected function makeVerbose()
    {
        // Set error reporting to maximum output
        error_reporting($this->errorMaximum);

        // ini_set() can only be used if php version is >= 5.3 (cause from PHP 5.3 safemode
        // is deprecated and from PHP 5.4 it is removed) or if safemode is Off.
        if ($this->phpVersion >= 5.3 || !ini_get('safemode')) {
            ini_set('display_errors',         1);
            ini_set('display_startup_errors', 1);
            ini_set('track_errors',           1);
            ini_set('log_errors',             1);
            ini_set('html_errors',            1);
            $result = true;

        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * Makes PHP silent to get less output.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if debugging could be disabled, otherwise FALSE
     * @access protected
     */
    protected function makeSilent()
    {
        // if debugging-mode is disabled we must hide all errors to prevent the app from information leakage.
        // set error_reporting to null (0) to hide PHP's reports
        error_reporting($this->errorMinimum);

        // ini_set() can only be used if php version is >= 5.3 (cause from PHP 5.3 safemode
        // is deprecated and from PHP 5.4 it is removed) or if safemode is Off.
        if ($this->phpVersion >= 5.3 || !ini_get('safemode')) {
            ini_set('display_errors',         0);
            ini_set('display_startup_errors', 0);
            ini_set('track_errors',           0);
            ini_set('log_errors',             1);
            ini_set('html_errors',            0);
            $result = true;

        } else {
            $result = false;
        }

        return $result;
    }
}
