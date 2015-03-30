<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Tool - Abstract
 *
 * Abstract.php - The abstract base for CLI Tools.
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
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
 * @category   DoozR
 * @package    DoozR_Tool
 * @subpackage DoozR_Tool_Abstract
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once 'DoozR/Base/Class.php';

use \donatj\Flags;

/**
 * DoozR - Tool - Abstract
 *
 * The abstract base for CLI Tools.
 *
 * @category   DoozR
 * @package    DoozR_Tool
 * @subpackage DoozR_Tool_Abstract
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
abstract class DoozR_Tool_Abstract extends DoozR_Base_Class
{
    /**
     * The name of the software using this CLI interface.
     * Just used for output environment information.
     *
     * @var string
     * @access protected
     */
    protected $name;

    /**
     * The version of the software using this CLI interface.
     * Just used for output environment information.
     *
     * @var string
     * @access protected
     */
    protected $version;

    /**
     * The configuration of flags supported for this CLI.
     *
     * @var donatj\Flags
     * @access protected
     */
    protected $flags;

    /**
     * Collection of long flags is filled after parse().
     *
     * @var array
     * @access protected
     */
    protected $longs;

    /**
     * Collection of short flags is filled after parse().
     *
     * @var array
     * @access protected
     */
    protected $shorts;

    /**
     * The configuration used for supported flags.
     *
     * @var array
     * @access protected
     */
    protected $flagConfiguration = array();


    /**
     * Constructor.
     *
     * @param Flags  $flags             The Flags instance for handling arguments from CLI.
     * @param string $name              The name of this CLI
     * @param string $version           The version of this CLI
     * @param array  $flagConfiguration Configuration for the flags
     * @param null   $injectCommand
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Tool_Abstract
     * @access public
     */
    public function __construct(
        Flags $flags,
        $name = DOOZR_NAME,
        $version = DOOZR_VERSION,
        array $flagConfiguration = array(),
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
     * Configures.
     *
     * @param array $configuration The configuration for commands
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining.
     * @access protected
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
     * @return $this Instance for chaining.
     * @access protected
     */
    protected function parse()
    {
        // Try to parse and fail on any exception ...
        try {
            $this->getFlags()->parse();

            // Extract parsed arguments
            $this
                ->longs($this->getFlags()->longs())
                ->shorts($this->getFlags()->shorts());

        } catch(donatj\Exceptions\InvalidFlagParamException $e) {
            $this->showHelp($e->getMessage());

        } catch (donatj\Exceptions\InvalidFlagTypeException $e) {
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
     * @return string The colorized string
     * @access protected
     */
    protected function colorize($string, $color)
    {
        return \cli\Colors::colorize('%N%n' . $color . $string . '%N%n');
    }

    /**
     * Shows help screen with available commands on demand or on error.
     *
     * @param string $error The error to show additionally.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function showHelp($error = null)
    {
        if ($error !== null) {
            $this->showError($error);
        }

        $message = 'Available commands:';
        echo PHP_EOL . PHP_EOL . $message . PHP_EOL . $this->getFlags()->getDefaults() . PHP_EOL;
        exit;
    }

    /**
     * Displays an error message with red background and white color.
     *
     * @param string $error The error message as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function showError($error)
    {
        echo PHP_EOL . $this->colorize($error, '%1');
    }

    /**
     * Displays an success message with green background and white color.
     *
     * @param string $success The success message as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function showSuccess($success)
    {
        echo PHP_EOL . $this->colorize($success, '%2');
    }

    /**
     * Shows version of this CLI.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function showVersion()
    {
        echo PHP_EOL . $this->colorize($this->getName(), '%y') . ' - ' . $this->colorize('Version', '%g') .
             ': ' . $this->getVersion() . PHP_EOL;
        exit;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Setter & Getter
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for name.
     *
     * @param string $name The name of CLI.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
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
     * @return $this Instance for chaining.
     * @access protected
     */
    protected function name($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Setter for name.
     *
     * @param string $name The name of CLI.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining.
     * @access protected
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
     * @return void
     * @access protected
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
     * @return $this Instance for chaining
     * @access protected
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
     * @return string The version
     * @access protected
     */
    protected function getVersion()
    {
        return $this->version;
    }

    /**
     * Setter for flags.
     *
     * @param string $flags The flags of CLI.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setFlags($flags)
    {
        $this->flags = $flags;
    }

    /**
     * Setter for flags.
     *
     * @param string $flags The flags of CLI.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function flags($flags)
    {
        $this->flags = $flags;
        return $this;
    }

    /**
     * Getter for flags.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return donatj\Flags The flags
     * @access protected
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
     * @return void
     * @access protected
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
     * @return $this Instance for chaining
     * @access protected
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
     * @return array Longs
     * @access protected
     */
    protected function getLongs()
    {
        return $this->longs;
    }

    /**
     * Setter for shorts.
     *
     * @param array $longs The longs to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setShorts(array $shorts)
    {
        $this->shorts = $shorts;
    }

    /**
     * Setter for shorts.
     *
     * @param array $longs The longs to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
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
     * @return array shorts
     * @access protected
     */
    protected function getShorts()
    {
        return $this->shorts;
    }

    /**
     * Setter for flagConfiguration.
     *
     * @param string $flagConfiguration The flagConfiguration of CLI.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setFlagConfiguration($flagConfiguration)
    {
        $this->flagConfiguration = $flagConfiguration;
    }

    /**
     * Setter for flagConfiguration.
     *
     * @param string $flagConfiguration The flagConfiguration of CLI.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function flagConfiguration($flagConfiguration)
    {
        $this->flagConfiguration = $flagConfiguration;
        return $this;
    }

    /**
     * Getter for flagConfiguration.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The flagConfiguration
     * @access protected
     */
    protected function getFlagConfiguration($key = null)
    {
        if ($key !== null) {
            $result = (isset($this->flagConfiguration[$key])) ? $this->flagConfiguration[$key] : null;
        } else {
            $result = $this->flagConfiguration;
        }

        return $result;
    }
}
