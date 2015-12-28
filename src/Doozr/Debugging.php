<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Debugging
 *
 * Debugging.php - Enables debugging by configuring PHP's error debugging
 * level and some other settings.
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
 * @package    Doozr_Kernel
 * @subpackage Doozr_Kernel_Debugging
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Class/Singleton/Strict.php';

use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

/**
 * Doozr - Debugging
 *
 * Enables debugging by configuring PHP's error debugging level and some other settings.
 *
 * @category   Doozr
 * @package    Doozr_Kernel
 * @subpackage Doozr_Kernel_Debugging
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
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
    protected $installed = false;

    /**
     * Instance of Doozr_Logging
     *
     * @var Doozr_Logging
     * @access protected
     */
    protected $logger;

    /**
     * CLI state.
     * TRUE = is Cli Environment | FALSE = not
     * Important for installing correct Whoops Handler
     *
     * @var bool
     * @access protected
     */
    protected $cli = false;

    /**
     * Instance of Whoops Error Handler
     *
     * @var \Whoops\Run
     * @access protected
     */
    protected $whoops;

    /**
     * History for previously found values.
     * Required to be able to unhook the systems debugging setup.
     * We are able to install and uninstall in one execution.
     *
     * @var array
     * @access protected
     */
    protected $history = [];

    /**
     * The ini values we operate on/with.
     *
     * @var array
     * @access protected
     */
    protected $iniKeys = [
        'error_reporting',
        'display_errors',
        'display_startup_errors',
        'track_errors',
        'log_errors',
        'html_errors',
    ];

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param Doozr_Logging_Interface $logger       Instance of Doozr_Logging
     * @param bool                    $cli          TRUE if Cli environment (other handlers), FALSE if not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access protected
     */
    protected function __construct(
        Doozr_Logging_Interface $logger,
                                $cli
    ) {
        $this
            ->logger($logger)
            ->cli($cli)
            ->install()
            ->getLogger()
                ->debug('Debugging Component installed.');
    }

    /*------------------------------------------------------------------------------------------------------------------
    | SETTER, GETTER, ISSER & HASSER
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for iniKeys.
     *
     * @param array $iniKeys The ini keys to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setIniKeys(array $iniKeys)
    {
        $this->iniKeys = $iniKeys;
    }

    /**
     * Fluent: Setter for iniKeys.
     *
     * @param array $iniKeys The ini keys to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function iniKeys(array $iniKeys)
    {
        $this->setIniKeys($iniKeys);

        // Chaining
        return $this;
    }

    /**
     * Getter for iniKeys.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array|null iniKeys if set, otherwise NULL
     * @access protected
     */
    protected function getIniKeys()
    {
        return $this->iniKeys;
    }

    /**
     * Setter for history.
     *
     * @param string $key   Key to use
     * @param mixed  $value Value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setHistory($key, $value = null)
    {
        $this->history[$key] = $value;
    }

    /**
     * Fluent: Setter for history.
     *
     * @param string $key   Key to use
     * @param mixed  $value Value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function history($key, $value = null)
    {
        $this->setHistory($key, $value);

        // Chaining
        return $this;
    }

    /**
     * Getter for history.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The value if set, otherwise NULL
     * @access protected
     */
    protected function getHistory($key = null)
    {
        return (true === isset($this->history[$key])) ? $this->history[$key] : null;
    }

    /**
     * Setter for whoops.
     *
     * @param Run $whoops The whoops to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setWhoops($whoops)
    {
        $this->whoops = $whoops;
    }

    /**
     * Fluent: Setter for whoops.
     *
     * @param Run $whoops The whoops to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function whoops(Run $whoops = null)
    {
        $this->setWhoops($whoops);

        return $this;
    }

    /**
     * Getter for whoops.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Run Instance of Whoops
     * @access protected
     */
    protected function getWhoops()
    {
        return $this->whoops;
    }

    /**
     * Setter for installed.
     *
     * @param bool $installed Value of installed.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setInstalled($installed)
    {
        $this->installed = $installed;
    }

    /**
     * Fluent: Setter for installed.
     *
     * @param bool $installed Value of installed.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function installed($installed)
    {
        $this->setInstalled($installed);

        return $this;
    }

    /**
     * Getter for installed.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if installed, otherwise FALSE
     * @access protected
     */
    protected function getInstalled()
    {
        return $this->installed;
    }

    /**
     * Isser for installed.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if installed, otherwise FALSE
     * @access protected
     */
    protected function isInstalled()
    {
        return (true === $this->getInstalled());
    }

    /**
     * Setter for logger.
     *
     * @param Doozr_Logging_Interface $logger The logger to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setLogger(Doozr_Logging_Interface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Fluent: Setter for logger.
     *
     * @param Doozr_Logging_Interface $logger The logger to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function logger(Doozr_Logging_Interface $logger)
    {
        $this->setLogger($logger);

        return $this;
    }

    /**
     * Getter for logger.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Logging Instance of logger
     * @access protected
     */
    protected function getLogger()
    {
        return $this->logger;
    }

    /**
     * Setter for cli.
     *
     * @param bool $cli The CLI status
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setCli($cli)
    {
        $this->cli = $cli;
    }

    /**
     * Fluent: Setter for cli.
     *
     * @param bool $cli The CLI status
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function cli($cli)
    {
        $this->setCli($cli);

        return $this;
    }

    /**
     * Getter for cli.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean Instance for chaining
     * @access protected
     */
    protected function getCli()
    {
        return $this->cli;
    }

    /**
     * Returns cli status.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if CLI, otherwise FALSE
     * @access protected
     */
    protected function isCli()
    {
        return (true === $this->getCli());
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Dumps active debugging state of the current OS/System.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function dumpState()
    {
        // Store active error reporting
        foreach ($this->getIniKeys() as $key) {
            $this->setHistory($key, ini_get($key));
        }

        // For chaining
        return $this;
    }

    /**
     * Restores a state from array input.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function restoreState()
    {
        // Restores state
        foreach ($this->getIniKeys() as $key) {
            ini_set($key, $this->getHistory($key));
        }

        // For chaining
        return $this;
    }

    /**
     * Enables debugging by making the system verbose and by enabling PHP to fetch all errors, notice and so on.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     * @throws
     */
    public function install()
    {
        // Check if not installed ...
        if (true !== $this->isInstalled()) {
            try {
                $this
                    ->dumpState()                                   // Backup current state
                    ->installed(true)                               // 1st mark that it installed
                    ->makeVerbose()                                 // Enable the complete output of PHP's errors
                    ->whoops(new Run())                             // Create a Whoops instance
                    ->installWhoops()                               // Install Whoops Exception Handler
                    ->getLogger()
                        ->debug('Debugging tools installed.');

            } catch (Doozr_Debugging_Exception $exception) {

                $this
                    ->installed(false)
                    ->uninstallWhoops()
                    ->restoreState();

                throw new Doozr_Debugging_Exception(
                    sprintf('Debugging could not be installed! Error: %s', $exception->getMessage())
                );
            }

        } else {
            throw new Doozr_Debugging_Exception(
                'Debugging is already installed!'
            );
        }

        // Enable chaining
        return $this;
    }

    /**
     * Uninstalls debugging handler.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     * @throws Doozr_Debugging_Exception
     */
    public function uninstall()
    {
        if (true === $this->isInstalled()) {
            try {
                $this
                    ->installed(false)
                    ->uninstallWhoops()
                    ->whoops(null)
                    ->restoreState()
                    ->getLogger()
                        ->debug('Debugging tools uninstalled.');

            } catch (Doozr_Debugging_Exception $exception) {
                throw new Doozr_Debugging_Exception(
                    sprintf('Debugging could not be uninstalled! Error: %s', $exception->getMessage())
                );
            }

        } else {
            throw new Doozr_Debugging_Exception(
                'Debugging could not be uninstalled. It\'s not installed!'
            );
        }
    }

    /**
     * Installs whoops exception handler.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function installWhoops()
    {
        // Configure the page handler of Whoops
        if (true === $this->isCli()) {
            // Text for cli
            $exceptionHandler = new PlainTextHandler();

        } else {
            // Otherwise the pretty one
            $exceptionHandler = new PrettyPageHandler();
            $constants        = get_defined_constants();

            $exceptionHandler->setPageTitle('Doozr');

            // Extract Doozr Constants as debugging information
            $data = [];
            foreach($constants as $key => $value) {
                if ('DOOZR_' === substr($key, 0, 6)) {
                    $data[$key] = (true === is_bool($value)) ? ((true === $value) ? 'TRUE' : 'FALSE') : $value;
                }
            }
            ksort($data);

            $exceptionHandler->addDataTable('Doozr Environment', $data);
        }

        $this->getWhoops()->pushHandler($exceptionHandler);
        $this->getWhoops()->register();

        // Chaining
        return $this;
    }

    /**
     * Uninstalls whoops exception handler.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function uninstallWhoops()
    {
        $this->getWhoops()->unregister();

        // Chaining
        return $this;
    }

    /**
     * Makes PHP verbose to get at much debugging output as possible.
     *
     * @param int   $errorReporting Maximum error reporting value
     * @param integer $iniValue       Value for ini settings
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function makeVerbose($errorReporting = PHP_INT_MAX, $iniValue = 1)
    {
        // Set error reporting to maximum int ( = all bits set)
        error_reporting($errorReporting);

        foreach ($this->getIniKeys() as $key) {
            ini_set($key, $iniValue);
        }

        // Chaining
        return $this;
    }
}
