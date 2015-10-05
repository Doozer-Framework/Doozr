<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Tool - Cli
 *
 * console - A console PHP script for managing the Doozr Framework installation.
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
 * @package    Doozr_Tool
 * @subpackage Doozr_Tool_Cli
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once 'Doozr/Tool/Abstract.php';
include_once 'Doozr/Tool/Webserver.php';

/**
 * Doozr - Tool - Cli
 *
 * A console PHP script for managing the Doozr Framework installation.
 *
 * @category   Doozr
 * @package    Doozr_Tool
 * @subpackage Doozr_Tool_Cli
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Tool_Cli extends Doozr_Tool_Abstract
{
    /*------------------------------------------------------------------------------------------------------------------
    | Internal helper
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Start the command processing.
     *
     * @param string $injectedCommand An optional injected (and override) command.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|boolean A result in any form.
     * @access protected
     */
    protected function execute($injectedCommand = null)
    {
        $error  = null;
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

        // Check for passed commands ...
        foreach ($longs as $name => $value) {
            if ($value !== false && strlen($value) > 0) {
                switch ($name) {
                    case 'webserver':
                        new Doozr_Tool_Webserver(
                            $this->getFlags(),
                            $this->getName(),
                            $this->getVersion(),
                            array(
                                $name => $this->getFlagConfiguration($name)
                            ),
                            $value
                        );
                        exit;
                        break;

                    case 'cache':
                        new Doozr_Tool_Cache(
                            $this->getFlags(),
                            $this->getName(),
                            $this->getVersion(),
                            array(
                                $name => $this->getFlagConfiguration($name)
                            ),
                            $value
                        );
                        exit;
                        break;
                }
            } elseif ($value !== false && strlen($value) === 0) {
                $error = sprintf('Empty command. Please tell me what i should to do with "%s"', $name);

            }
        }

        // Show the Doozr header
        $this->showDoozrBanner();

        // Default here is nothing to do just show help
        $this->showHelp($error);

        return true;
    }

    /**
     * Show Doozr banner.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     * @static
     */
    protected static function showDoozrBanner()
    {
        \cli\line('%N%n%y');
        \cli\line('  _____     ______     ______     ______     ______');
        \cli\line(' /\  __-.  /\  __ \   /\  __ \   /\___  \   /\  == \\');
        \cli\line(' \ \ \/\ \ \ \ \/\ \  \ \ \/\ \  \/_/  /__  \ \  __<');
        \cli\line('  \ \____-  \ \_____\  \ \_____\   /\_____\  \ \_\ \_\\');
        \cli\line('   \/____/   \/_____/   \/_____/   \/_____/   \/_/ /_/');
        \cli\line('%N%n');
    }
}
