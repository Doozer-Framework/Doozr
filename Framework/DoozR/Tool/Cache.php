<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Tool - Cache
 *
 * Cache.php - Cache tool for internal webserver.
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
 * @package    DoozR_Tool
 * @subpackage DoozR_Tool_Cache
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once 'DoozR/Tool/Abstract.php';

use \donatj\Flags;

/**
 * DoozR - Tool - Cache
 *
 * Cache tool for internal webserver.
 *
 * @category   DoozR
 * @package    DoozR_Tool
 * @subpackage DoozR_Tool_Cache
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Tool_Cache extends DoozR_Tool_Abstract
{
   /**
     * Command purge.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_CLEAR = 'purge';

    /**
     * Scope everything
     *
     * @var string
     * @access public
     * @const
     */
    const SCOPE_EVERYTHING = '*';


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
     * @throws DoozR_Exception
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

        $argumentBag = array();

        // Check for passed commands ...
        foreach ($longs as $name => $value) {
            if ($value !== false && strlen($value) > 0) {
                $argumentBag[$name] = $value;
            }
        }

        if ($injectedCommand !== null) {
            $result = $this->dispatchCommand($injectedCommand, $argumentBag);

        } else {
            throw new DoozR_Exception(
                'Not implemented!'
            );
        }

        if ($result === false) {
            echo $this->colorize($injectedCommand . ' failed!' . PHP_EOL, '%r');
            $this->showHelp();

        } else {
            echo $this->colorize($injectedCommand . ' successful!' . PHP_EOL, '%g');
        }

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
    protected function dispatchCommand($command, array $argumentBag = array())
    {
        $result    = false;
        $arguments = null;

        // Multicommand?
        $arguments = explode(':', $command);

        if (count($arguments) > 1) {
            $command = array_shift($arguments);
        } else {
            $arguments = array(self::SCOPE_EVERYTHING);
        }

        switch ($command) {

            case self::COMMAND_CLEAR:
                $result = $this->purge(
                    $arguments,
                    $argumentBag
                );
                break;

            case self::COMMAND_WARMUP:
                $result = $this->warmup(
                    $arguments,
                    $argumentBag
                );
                break;

            default:
                $command = null;
                break;
        }

        return $result;
    }

    /**
     * Purges content from cache.
     *
     * @param string $scope The scope to purge content for.
     * @param array $argumentBag A bag of arguments from CLI
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed A result in any form.
     * @access protected
     * @throws DoozR_Exception
     */
    protected function purge(
              $scope       = self::SCOPE_EVERYTHING,
        array $argumentBag = array()
    ) {
        // We need to detect the cache container of DoozR or fallback to default
        if ($container = getenv('DOOZR_CACHE_CONTAINER') === false) {
            $container = (defined('DOOZR_CACHE_CONTAINER') === true) ?
                DOOZR_CACHE_CONTAINER :
                DoozR_Cache_Service::CONTAINER_FILESYSTEM;
        }

        // Build namespace for cache
        $namespace = DOOZR_NAMESPACE_FLAT . '.cache';

        /* @var DoozR_Cache_Service $cache */
        $cache = DoozR_Loader_Serviceloader::load('cache', $container, $namespace, array(), DOOZR_UNIX);

        $scope = implode('.', $scope);

        switch ($scope) {
            case '*':
                // We can purge simply everything from our namespace!
                try {
                    $cache->runGarbageCollection($namespace, -1);

                } catch (Exception $e) {
                    var_dump($e);
                    die;
                }

                break;

            case 'routes':
                throw new DoozR_Exception('Not implemented yet!');
                break;

            case 'configs':
                throw new DoozR_Exception('Not implemented yet!');
                break;

            case 'services':
                throw new DoozR_Exception('Purging Not implemented yet!');
                break;
        }

        return true;
    }

    /**
     * Prepares content for the cache = warmup.
     *
     * @param string $scope The scope to warmup content for.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed A result in any form.
     * @access protected
     */
    protected function warmup(
        $scope       = self::SCOPE_EVERYTHING,
        array $argumentBag = array()
    ) {
        var_dump($scope);
        die;
    }
}

