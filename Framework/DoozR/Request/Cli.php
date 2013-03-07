<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Request - Cli
 *
 * Cli.php - Request-Handler for requests passed through CLI to Front-Controller.
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
 * @package    DoozR_Request
 * @subpackage DoozR_Request_Cli
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Request.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Request/Interface.php';

/**
 * DoozR Request Cli
 *
 * Cli.php - Request Cli - Request-Handler for requests passed through CLI to
 * Front-Controller.
 *
 * @category   DoozR
 * @package    DoozR_Request
 * @subpackage DoozR_Request_Cli
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Request_Cli extends DoozR_Base_Request implements DoozR_Request_Interface
{
    /**
     * holds the type of os-mode for commands
     * e.g. -- for other and / for windows
     *
     * @string
     * @access private
     */
    private $_commandMode;

    /**
     * holds the forced command-mode
     * e.g. -- for other and / for windows
     *
     * @string
     * @access private
     */
    private $_forceCommandMode = null;

    /**
     * holds the list of possible commands seperator
     * e.g. -- for other and / for windows
     *
     * @array
     * @access private
     */
    private $_commandSeps = array(
        'win'   => '/',
        'other' => '--'
    );

    /**
     * holds the current used commands seperator
     *
     * @array
     * @access private
     */
    private $_commandSep;

    /**
     * holds the own type of Request
     *
     * @var string
     * @access const
     */
    const TYPE = 'cli';


    /**
     * constructs the class
     *
     * constructor builds the class
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function __construct()
    {
        // set type - TODO: really needed? seems to be senseless
        self::$type = self::TYPE;

        // init
        $this->_init();

        // which php-globals should be processed?
        // $_SERVER currently excluded due too heavy processing-time
        $this->_requestSources = array(
            'CLI',
            'ENVIRONMENT'
        );

        // get securitylayer functionality (htmlpurifier + phpids)
        // sanitize global arrays, retrieve impacts and maybe cancle the whole request
        parent::__construct();

        // replace globals
        $this->_replacePhpGlobals();
    }

    /**
     * initializes the main-settings
     *
     * initializes the main-settings.
     *
     * @return  boolean True if everything wents fine, otherwise false
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _init()
    {
        // we need the "global" array of arguments
        global $argv, $argc, $_CLI;

        /**
         * check CLI overrides
         */

        // check for force-mode
        for ($i = 0; $i < $argc; ++$i) {
            if (stristr($argv[$i], 'force-mode')) {
                $this->_forceCommandMode = $argv[$i+1];
                break;
            }
        }

        // ceck for forced command-mode
        if (!$this->_forceCommandMode) {
            // detect OS for definition of command-seperator [can be overridden by force-mode LIN|WIN]
            $this->_commandMode = $this->_detectCommandMode();
        } else {
            // set forced mode
            $this->_commandMode = $this->_forceCommandMode;
        }

        // set command-seperator
        $this->_commandSep = (isset($this->_commandSeps[$this->_commandMode])) ?
            $this->_commandSeps[$this->_commandMode] :
            $this->_commandSeps[$this->_detectCommandMode()];

        // transform to make the input OBJECT-ready
        $argumentsPreprocessed = $this->_parseCommandLine($argv, $argc);

        // inject the given arguments as $_SERVER['REQUEST_URI']
        $this->_injectRequestUri($argv, $argc);

        /**
         * create GLOBAL(S)
         */
        // in CLI mode we do not have any prefilled global like $_GET / $_POST
        // so we create one -> $_CLI and we fill also the all-rounder $_REQUEST
        $_CLI = $argumentsPreprocessed;
    }

    /**
     * parse the commandline (cl) and return an ordered list of items
     *
     * This method is intend to parse the commandline and return an ordered list of itmes.
     *
     * @param array   $arguments      The arguments ($argv) to parse
     * @param integer $countArguments The count of arguments
     *
     * @return  array parsed commandline as ordered list
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _parseCommandLine(array $arguments, $countArguments)
    {
        // prefill with static elements
        $result = array(
            'cli_filename' => $arguments[0],
            'command'      => $arguments[1]
        );

        // iterate over remaining arguments
        for ($i = 2; $i < $countArguments; ++$i) {
            // rename for cleaner structure
            $argument = $arguments[$i];

            // lead argument?
            if (strpos($argument, $this->_commandSep) !== false
                && substr($argument, 0, strlen($this->_commandSep)) == $this->_commandSep
            ) {
                // get value of lead argument
                $argument = str_replace($this->_commandSep, '', $argument);
                $argument = $this->_argumentDefaultReplace($argument);
                $result[$argument] = (isset($arguments[$i+1]) && strpos($arguments[$i+1], $this->_commandSep) === false)
                    ? $arguments[$i+1]
                    : null;
            }
        }

        // return the preprocessed array
        return $result;
    }

    /**
     * replaces problematic arguments with names usable as array-index
     *
     * This method is intend to replace problematic arguments with names usable as array-index.
     *
     * @param string $argument The argument to check for replacement
     *
     * @return  string processed argument-name
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _argumentDefaultReplace($argument)
    {
        // check argument for replacements
        if ($argument == '?') {
            $argument = 'help';
        } else {
            // get first char's ascii code
            $firstCharAscii = ord($argument);
            if (($firstCharAscii < 65 || $firstCharAscii > 90)
                && ($firstCharAscii < 97 || $firstCharAscii > 122)
            ) {
                $argument = '_'.$argument;
            }
        }

        // return processed argument
        return $argument;
    }

    /**
     * converts the arguments to a request-uri and stores it in $_SERVER['REQUEST_URI']
     *
     * This method is intend to convert the arguments to a request-uri and stores it in $_SERVER['REQUEST_URI'].
     *
     * @param array   $arguments The arguments to use for building request-uri
     * @param integer $count     The count of arguments to use for building request-uri
     *
     * @return  string processed argument-name
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _injectRequestUri(array $arguments, $count)
    {
        global $_SERVER;

        $requestUri = $arguments[1];

        for ($i = 0; $i < ($count-1); $i=$i+2) {
            $requestUri .= ($i == 0)
                ? ('?'.'cli='.$arguments[0])
                : ('&'.$arguments[$i].'='.(isset($arguments[$i+1]) ? $arguments[$i+1] : ''));
        }

        // store as request-uri in global $_SERVER (used by Route.php)
        $_SERVER['REQUEST_URI'] = $requestUri;
    }

    /**
     * detects command-mode from OS
     *
     * Detects which command-mode (formatting) is used for console mode (CLI).
     *
     * @return  string "win" if windos is detected as OS, otherwise "other".
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _detectCommandMode()
    {
        // get os setting from php
        return (DOOZR_WIN) ? 'win' : 'other';
    }

    /**
     * replaces PHP's globals with DoozR's global objects
     *
     * This method is intend to inject an OOP-Interface into PHP's global
     * array's.
     *
     * @return  boolean True if everything wents fine, otherwise false
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _replacePhpGlobals()
    {
        /**
         * iterate over all defined global-replacements and replace
         * PHP's them with out object
         */
        foreach ($this->_requestSources as $global) {
            switch ($global) {
            case 'CLI':
                global $_CLI;
                global $_REQUEST;
                $_CLI     = $this->CLI;
                $_REQUEST = $this->CLI;
                break;
            }
        }
    }
}

?>
