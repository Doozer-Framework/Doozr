<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Tool - Abstract.
 *
 * Abstract.php - The abstract base for CLI Tools.
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
require_once 'Doozr/Base/Class.php';

use donatj\Flags;
use donatj\Exceptions\InvalidFlagParamException;
use donatj\Exceptions\InvalidFlagTypeException;

/**
 * Doozr - Tool - Abstract.
 *
 * The abstract base for CLI Tools.
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
abstract class Doozr_Tool_Abstract extends Doozr_Base_Class
{
    /**
     * The name of the software using this CLI interface.
     * Just used for output environment information.
     *
     * @var string
     */
    protected $name;

    /**
     * The version of the software using this CLI interface.
     * Just used for output environment information.
     *
     * @var string
     */
    protected $version;

    /**
     * The configuration of flags supported for this CLI.
     *
     * @var Flags
     */
    protected $flags;

    /**
     * Collection of long flags is filled after parse().
     *
     * @var array
     */
    protected $longs;

    /**
     * Collection of short flags is filled after parse().
     *
     * @var array
     */
    protected $shorts;

    /**
     * Collection of arguments passed with the flags.
     *
     * @var array
     */
    protected $arguments;

    /**
     * The configuration used for supported flags.
     *
     * @var array
     */
    protected $flagConfiguration = [];

    /**
     * Separator for arguments.
     *
     * @var string
     */
    const ARGUMENT_SEPARATOR = ':';

    /**
     * Constructor.
     *
     * @param Flags  $flags             Flags instance for handling arguments from CLI.
     * @param string $name              Name of CLI
     * @param string $version           Version of this CLI
     * @param array  $flagConfiguration Configuration for the flags
     * @param null   $injectCommand     Injected command
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __construct(
        Flags $flags,
              $name = DOOZR_NAME,
              $version = DOOZR_VERSION,
        array $flagConfiguration = [],
              $injectCommand = null
    ) {
        // For tools like PS ...
        cli_set_process_title($name);

        $this
            ->name($name)
            ->version($version)
            ->flags($flags)
            ->flagConfiguration($flagConfiguration)
            ->configure($flagConfiguration)
            ->parse()
            ->execute($injectCommand);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Internal helper
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Start the command processing.
     *
     * @param string $injectedCommand An optional injected (and override) command.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed A result in any form.
     */
    protected function execute($injectedCommand = null)
    {
        return;
    }

    /**
     * Configures.
     *
     * @param array $configuration The configuration for commands
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining.
     */
    protected function configure(array $configuration)
    {
        // Force colors
        \cli\Colors::enable();

        foreach ($configuration as $key => $values) {
            if ($values['type'] === 'short') {
                $this->getFlags()->{$values['type']}($key, $values['info']);
            } else {
                $this->getFlags()->{$values['type']}($key, $values['value'], $values['info']);
            }
        }

        return $this;
    }

    /**
     * Parses the arguments from CLI.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining.
     */
    protected function parse()
    {
        // Try to parse and fail on any exception ...
        try {
            $this->getFlags()->parse();

            // Extract parsed arguments
            $this
                ->longs($this->getFlags()->longs())
                ->shorts($this->getFlags()->shorts())
                ->arguments($this->getFlags()->args());
        } catch (InvalidFlagParamException $e) {
            $this->showHelp($e->getMessage());
        } catch (InvalidFlagTypeException $e) {
            $this->showHelp($e->getMessage());
        }

        return $this;
    }

    /**
     * Colorizes a string with a given color.
     *
     * @param string $string The string to colorize
     * @param string $color  The color
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The colorized string
     */
    protected function colorize($string, $color)
    {
        return \cli\Colors::colorize('%N%n'.$color.$string.'%N%n');
    }

    /**
     * Shows help screen with available commands on demand or on error.
     *
     * @param string $error The error to show additionally.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function showHelp($error = null)
    {
        if ($error !== null) {
            $this->showError($error);
        }

        $message = 'Available commands:';
        echo PHP_EOL.PHP_EOL.$message.PHP_EOL.$this->getFlags()->getDefaults().PHP_EOL;
        exit;
    }

    /**
     * Displays an error message with red background and white color.
     *
     * @param string $error The error message as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function showError($error)
    {
        echo PHP_EOL.$this->colorize($error, '%1');
    }

    /**
     * Displays an success message with green background and white color.
     *
     * @param string $success The success message as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function showSuccess($success)
    {
        echo PHP_EOL.$this->colorize($success, '%2');
    }

    /**
     * Shows version of this CLI.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function showVersion()
    {
        echo PHP_EOL.$this->colorize($this->getName(), '%y').' - '.$this->colorize('Version', '%g').
             ': '.$this->getVersion().PHP_EOL;
        exit;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | SETTER & GETTER
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for name.
     *
     * @param string $name The name of CLI.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Setter for name.
     *
     * @param string $name The name of CLI.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining.
     */
    protected function name($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Getter for name.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string $name The name of CLI.
     */
    protected function getName()
    {
        return $this->name;
    }

    /**
     * Setter for version.
     *
     * @param string $version The version of CLI.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Setter for version.
     *
     * @param string $version The version of CLI.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function version($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Getter for version.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The version
     */
    protected function getVersion()
    {
        return $this->version;
    }

    /**
     * Setter for flags.
     *
     * @param Flags $flags The flags of CLI.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setFlags(Flags $flags)
    {
        $this->flags = $flags;
    }

    /**
     * Setter for flags.
     *
     * @param Flags $flags The flags of CLI.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function flags(Flags $flags)
    {
        $this->flags = $flags;

        return $this;
    }

    /**
     * Getter for flags.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Flags The flags
     */
    protected function getFlags()
    {
        return $this->flags;
    }

    /**
     * Setter for longs.
     *
     * @param array $longs The longs to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setLongs(array $longs)
    {
        $this->longs = $longs;
    }

    /**
     * Setter for longs.
     *
     * @param array $longs The longs to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function longs(array $longs)
    {
        $this->setLongs($longs);

        return $this;
    }

    /**
     * Getter for longs.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array Longs
     */
    protected function getLongs()
    {
        return $this->longs;
    }

    /**
     * Setter for shorts.
     *
     * @param array $shorts The shorts to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setShorts(array $shorts)
    {
        $this->shorts = $shorts;
    }

    /**
     * Setter for shorts.
     *
     * @param array $shorts The shorts to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function shorts(array $shorts)
    {
        $this->setShorts($shorts);

        return $this;
    }

    /**
     * Getter for shorts.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array shorts
     */
    protected function getShorts()
    {
        return $this->shorts;
    }

    /**
     * Setter for arguments.
     *
     * @param array $arguments The arguments to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * Fluent: Setter for arguments.
     *
     * @param array $arguments The arguments to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function arguments(array $arguments)
    {
        $this->setArguments($arguments);

        return $this;
    }

    /**
     * Getter for arguments.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array Arguments
     */
    protected function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Setter for flagConfiguration.
     *
     * @param array $flagConfiguration The flagConfiguration of CLI.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setFlagConfiguration(array $flagConfiguration)
    {
        $this->flagConfiguration = $flagConfiguration;
    }

    /**
     * Setter for flagConfiguration.
     *
     * @param array $flagConfiguration The flagConfiguration of CLI.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function flagConfiguration(array $flagConfiguration)
    {
        $this->flagConfiguration = $flagConfiguration;

        return $this;
    }

    /**
     * Getter for flagConfiguration.
     *
     * @param string|null $key Single key to return flagConfiguration for, or NULL to return whole configuration
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The flagConfiguration
     */
    protected function getFlagConfiguration($key = null)
    {
        if (null !== $key) {
            $result = (isset($this->flagConfiguration[$key])) ? $this->flagConfiguration[$key] : null;

        } else {
            $result = $this->flagConfiguration;
        }

        return $result;
    }
}
