<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Tool - Webserver
 *
 * Webserver.php - Management tool for internal webserver.
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
 * @package    Doozr_Tool
 * @subpackage Doozr_Tool_Webserver
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once 'Doozr/Tool/Abstract.php';

use donatj\Flags;

/**
 * Doozr - Tool - Webserver
 *
 * Management tool for internal webserver.
 *
 * @category   Doozr
 * @package    Doozr_Tool
 * @subpackage Doozr_Tool_Webserver
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Tool_Webserver extends Doozr_Tool_Abstract
{
    /**
     * The pipes used for communicate with internal webserver
     *
     * @var array
     * @access protected
     */
    protected $pipes;

    /**
     * The server proces.
     *
     * @var resource
     * @access protected
     */
    protected $server;

    /**
     * The commandline.
     *
     * @var string
     * @access protected
     */
    protected $commandLine = '';

    /**
     * Command start.
     *
     * @var string
     * @access public
     */
    const COMMAND_START = 'start';

    /**
     * Command stop.
     *
     * @var string
     * @access public
     */
    const COMMAND_STOP = 'stop';

    /**
     * Command restart.
     *
     * @var string
     * @access public
     */
    const COMMAND_RESTART = 'restart';

    /**
     * Command status.
     *
     * @var string
     * @access public
     */
    const COMMAND_STATUS = 'status';

    /**
     * Interface for "localhost"
     *
     * @var string
     * @access public
     */
    const INTERFACE_LOCALHOST = 'localhost';

    /**
     * Interface for "localip"
     *
     * @var string
     * @access public
     */
    const INTERFACE_LOCALIP = '127.0.0.1';

    /**
     * Interface for "all interfaces"
     *
     * @var string
     * @access public
     */
    const INTERFACE_ALL = '0.0.0.0';

    /**
     * The default port to listen on.
     *
     * @var string
     * @access public
     */
    const DEFAULT_PORT = 80;

    /**
     * The default directory to serve from.
     *
     * @var string
     * @access public
     */
    const DEFAULT_DOCUMENT_ROOT = '.';

    /**
     * The default router script.
     *
     * @var string
     * @access public
     */
    const DEFAULT_ROOTER = '';

    /*------------------------------------------------------------------------------------------------------------------
    | Internal helper
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Start the command processing.
     *
     * @param string $injectedCommand An optional injected (and overide) command.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed A result in any form.
     * @access protected
     * @throws Doozr_Exception
     */
    protected function execute($injectedCommand = null)
    {
        $longs  = $this->getLongs();
        $shorts = $this->getShorts();

        // First check for help requested as long or short
        if ((isset($longs['help']) && $longs['help'] === true) || (isset($shorts['h']) && $shorts['h'] === 1)) {
            $this->showHelp();
        }

        // Default command
        // First check for help requested as long or short
        if ((isset($longs['version']) && $longs['version'] === true) || (isset($shorts['v']) && $shorts['v'] === 1)) {
            $this->showVersion();
        }

        $argumentBag = [];

        // Check for passed commands ...
        foreach ($longs as $name => $value) {
            if ($value !== false && strlen($value) > 0) {
                $argumentBag[$name] = $value;
            }
        }

        if ($injectedCommand !== null) {
            $result = $this->dispatchCommand($injectedCommand, $argumentBag);

        } else {
            throw new Doozr_Exception(
                'Not implemented!'
            );
        }

        // Default here is nothing to do just show help
        $this->showHelp();

        return true;

        return $result;
    }

    /**
     * Takes an command and call its handler.
     *
     * @param string $command     A command.
     * @param array  $argumentBag A collection of arguments.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function dispatchCommand($command, array $argumentBag = [])
    {
        $result = false;

        switch ($command) {

            case self::COMMAND_START:
                $result = $this->start(
                    $argumentBag['interface'],
                    $argumentBag['port'],
                    $argumentBag['docroot'],
                    isset($argumentBag['router']) ? ' ' . $argumentBag['router'] : ''
                );
                break;

            case self::COMMAND_STOP:
                $result = self::COMMAND_STOP;
                break;

            case self::COMMAND_RESTART:
                $result = self::COMMAND_RESTART;
                break;

            case self::COMMAND_STATUS:
                $result = self::COMMAND_STATUS;
                break;

            default:
                $command = null;
                break;
        }

        return $result;
    }

    /**
     * Returns the running status for a passed resourcehandle.
     *
     * @param resource $process The resource to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if running, otherwise FALSE
     * @access protected
     */
    protected function isRunning($process)
    {
        if (is_resource($process)) {
            $status = proc_get_status($process);
            return $status['running'];

        } else {
            return false;
        }
    }

    /**
     * Starts PHP's internal webserver and give us some control of it.
     *
     * @param string $interface    The interface to listen on (e.g. 0.0.0.0 or 127.0.0.1 or localhost ...)
     * @param string $port         The port to listen on (e.g. HTTP default 80)
     * @param string $documentRoot The document root to serve from
     * @param string $router       The router script (replacement for mod_rewrite)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed A result in any form.
     * @access protected
     */
    protected function  start(
        $interface    = self::INTERFACE_LOCALHOST,
        $port         = self::DEFAULT_PORT,
        $documentRoot = self::DEFAULT_DOCUMENT_ROOT,
        $router       = self::DEFAULT_ROOTER
    ) {
        // The command to execute
        $command = sprintf('php -S %s:%s -t %s%s 2>&1', $interface, $port, $documentRoot, $router);

        // The descriptor specs / pipe definition
        $descriptorspec = array(
            0 => array('pipe', 'r'),         // STDIN  => Pipe
            1 => array('pipe', 'w'),         // STDOUT => Pipe
            2 => array('pipe', 'w', 'a'),    // STDERR => Pipe
        );

        // Start our built-in web server
        $this->server = proc_open(
            $command,
            $descriptorspec,
            $this->pipes,
            $documentRoot
        );

        // Check for running server ...
        if ($this->isRunning($this->server) === false) {
            $this->showError('Failed to start test web server!');

        } else {
            $status = proc_get_status($this->server);
            echo $this->colorize('Server running ', '%g') .
                 sprintf('[PID: %s] ', $status['pid']) .
                 $this->colorize(
                    sprintf(
                        '%s:%s %s %s',
                        $interface,
                        $port,
                        $documentRoot,
                        $router
                    ) . ' ',
                    '%y'
                 ) . PHP_EOL;
            echo 'Press Ctrl + C to stop ...' . PHP_EOL;
            #file_put_contents(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'doozr-webserver.pid', $status['pid']);

            // Check if running in a loop
            while ($this->isRunning($this->server) !== false) {
                echo $this->fetchStreams($this->pipes);
            }
        }
    }

    /**
     * Fetch the streams new input.
     *
     * @param array $pipes The pipes to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The colorized result
     * @access protected
     */
    protected function fetchStreams(array $pipes)
    {
        $output  = $this->colorize(stream_get_contents($pipes[2]), '%r');
        $output .= $this->colorize(stream_get_contents($pipes[1]), '%g');

        return $output;
    }

    /**
     * Hook on destroy to cleanly close open handles.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function __destruct()
    {
        foreach ($this->pipes AS $pipe) {
            fclose($pipe);
        }

        if ($this->isRunning($this->server)) {
            proc_terminate($this->server, 2);
        }
    }
}

