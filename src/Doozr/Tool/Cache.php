<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Tool - Cache
 *
 * Cache.php - Cache tool for internal webserver.
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
 * @package    Doozr_Tool
 * @subpackage Doozr_Tool_Cache
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once 'Doozr/Tool/Abstract.php';

/**
 * Doozr - Tool - Cache
 *
 * Cache tool for internal webserver.
 *
 * @category   Doozr
 * @package    Doozr_Tool
 * @subpackage Doozr_Tool_Cache
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Tool_Cache extends Doozr_Tool_Abstract
{
    /**
     * Valid scopes for cache operations
     *
     * @var string[]
     * @access protected
     */
    protected $validScopes = array(
        self::SCOPE_DOOZR,                  // Everything
        self::SCOPE_DOOZR_CACHE,            // Default namespace
        self::SCOPE_DOOZR_ROUTES,           // Routing Matrix
        self::SCOPE_DOOZR_CONFIG,           // Configuration(s)
        self::SCOPE_DOOZR_I18N,             // Translations
    );

   /**
    * Command purge.
    * Purges all entries for passed scope.
    *
    * @var string
    * @access public
    */
    const COMMAND_PURGE = 'purge';

    /**
     * Command warmup.
     *
     * @var string
     * @access public
     */
    const COMMAND_WARMUP = 'warmup';

    /**
     * Scope everything
     *
     * @var string
     * @access public
     */
    const SCOPE_EVERYTHING    = '*';                         // <= Everything/Generic
    const SCOPE_DOOZR         = 'doozr';                     // <= Doozr root namespace
    const SCOPE_DOOZR_CACHE   = 'doozr.cache';               // <= Default caching namespace of Service Cache
    const SCOPE_DOOZR_CONFIG  = 'doozr.cache.configuration'; // <= Configuration of Doozr
    const SCOPE_DOOZR_ROUTES  = 'doozr.cache.routes';        // <= Routes of the Doozr Installation
    const SCOPE_DOOZR_I18N    = 'doozr.cache.i18n';          // <= Translations

    /*------------------------------------------------------------------------------------------------------------------
    | Internal helper
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Start the command processing.
     *
     * @param string $injectedCommand An optional injected (and override) command.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string A result in any form.
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

        if ($result === false) {
            echo $this->colorize($injectedCommand.' failed!'.PHP_EOL, '%r');
            $this->showHelp();

        } else {
            $foo = $this->colorize($injectedCommand.' (%s element'.(($result != 1) ? 's' : '').'', '%y');
            $bar = $this->colorize($result, '%w');
            $foo = sprintf($foo, $bar);
            echo $foo.$this->colorize(')', '%y');
            echo $this->colorize(' successful!'.PHP_EOL, '%g');
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
     * @return string The result of the command.
     * @access protected
     */
    protected function dispatchCommand($command, array $argumentBag = [])
    {
        $result    = false;
        $arguments = null;

        // Multicommand?
        $arguments = explode(':', $command);

        if (count($arguments) > 1) {
            $command = array_shift($arguments);
        } else {
            // Extract the namespace (scope for action)
            $arguments = $argumentBag['namespace'];
        }

        switch ($command) {
            case self::COMMAND_PURGE:
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
     * @param array $namespaces  The namespace to purge content for.
     * @param array $argumentBag The arguments bag
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return int The number of elements purged
     * @access protected
     * @throws Doozr_Exception
     */
    protected function purge(
        array $namespaces  = [self::SCOPE_EVERYTHING],
        array $argumentBag = []
    ) {
        $result = 0;

        // Iterate the namespaces passed ...
        foreach ($namespaces as $namespace) {

            // Build namespace for cache
            if (false === in_array($namespace, $this->validScopes)) {
                throw new Doozr_Exception(
                    sprintf('Scope %s not allowed!', $namespace)
                );
            }

            /* @var Doozr_Cache_Service $cache */
            $cache = Doozr_Loader_Serviceloader::load(
                'cache',
                DOOZR_CACHING_CONTAINER,
                $namespace,
                [],
                DOOZR_UNIX,
                DOOZR_CACHING
            );

            // We can purge simply everything from passed namespace!
            try {
                $result += $cache->garbageCollection($namespace, -1, true);

            } catch (Exception $exception) {
                break;
            }
        }

        return $result;
    }

    /**
     * Prepares content for the cache = warmup.
     *
     * @param string $namespace   The namespace to warmup content for.
     * @param array  $argumentBag The arguments bag
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed A result in any form.
     * @access protected
     */
    protected function warmup(
              $namespace   = self::SCOPE_EVERYTHING,
        array $argumentBag = []
    ) {
        dump(__METHOD__);
        die;
    }
}

