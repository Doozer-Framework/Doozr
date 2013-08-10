<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Logger for Firephp
 *
 * Firephp.php - This logger-implementation is intend to log to the FirePHP addon of
 * Mozilla Firefox
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
 * @package    DoozR_Logger
 * @subpackage DoozR_Logger_Firephp
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        Alogger.class.php, ILogger.class.php
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Logger/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Logger/Interface.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Logger/Lib/Firephp/FirePHP.class.php';

/**
 * Logger for Firephp
 *
 * This logger-implementation is intend to log to the FirePHP addon of Mozilla Firefox
 *
 * @category   DoozR
 * @package    DoozR_Logger
 * @subpackage DoozR_Logger_Firephp
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        Alogger.class.php, ILogger.class.php
 * @since      -
 */
final class DoozR_Logger_Firephp extends DoozR_Logger_Abstract implements DoozR_Logger_Interface
{
    /**
     * the name of this logger
     *
     * @var string
     * @access protected
     */
    protected $name = 'Firephp';

    /**
     * the version of this logger
     *
     * @var string
     * @access protected
     */
    protected $version = '$Rev$';

    /*******************************************************************************************************************
     * // BEGIN FIREPHP-LOGGER SPECIFIC VARIABLES
     ******************************************************************************************************************/

    /**
     * the instance of firePHP
     *
     * @var object
     * @access private
     */
    private $_firePHP;

    /**
     * holds the FirePHP-logtype of the last logger-call
     *
     * @var string
     * @access private
     */
    private $_firePhpLogtype;

    /*******************************************************************************************************************
     * \\ END FIREPHP-LOGGER SPECIFIC VARIABLES
     ******************************************************************************************************************/

    /**
     * constructs the class
     *
     * This method is the constructor and responsible for building the instance.
     *
     * @param integer $level       The level to use for this logger
     * @param string  $fingerprint Fingerprint of client used to identify clients log entries
     *
     * @return  void
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    protected function __construct($level, $fingerprint)
    {
        // call parents constructor
        parent::__construct($level, $fingerprint);

        // store level
        $this->level = $level;

        // set line seperator
        $this->lineSeparator = '';

        // get firePHP instance
        $this->_firePHP = FirePHP::getInstance(true);
    }

    /**
     * logs a given message/content
     *
     * This method is intend to log a message.
     *
     * @param string $content  The content/text/message to log
     * @param string $type     The type of the log-entry
     * @param string $file     The filename of the file from where the log entry comes
     * @param string $line     The linenumber from where log-entry comes
     * @param string $class    The classname from the class where the log-entry comes
     * @param string $method   The methodname from the method where the log-entry comes
     * @param string $function The functionname from the function where the log-entry comes
     * @param string $optional The optional content/param to log
     *
     * @return  boolean True if successful, otherwise false
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function log(
        $content = '',
        $type = 'LOG',
        $file = false,
        $line = false,
        $class = false,
        $method = false,
        $function = false,
        $optional = false
    ) {
        // grab the type for conversion to sos-type
        $this->_firePhpLogtype = $this->_convertToFirePhpLogtype($type);

        // and dispatch call to parent to process it
        parent::log(
            $content,
            $type,
            $file,
            $line,
            $class,
            $method,
            $function,
            $optional
        );
    }

    /**
     * converts the default log type to sos type
     *
     * This method is intend to convert the default log type to sos-logtype.
     *
     * @param string $type The type of the log-message to convert
     *
     * @return  string The converted log-type
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _convertToFirePhpLogtype($type)
    {
        // format log for FirePHP
        switch ($type) {
        case 'ERROR':
            return FirePHP::ERROR;
            break;
        case 'UNCLASSIFIED':
            return FirePHP::INFO;
            break;
        case 'NOTICE':
            return FirePHP::INFO;
            break;
        case 'WARNING':
            return FirePHP::WARN;
            break;
        case 'LOG':
            // break intentionally ommited
        default:
            return FirePHP::LOG;
            break;
        }
    }

    /**
     * abstract output container method
     *
     * This method is intend to write data to a defined pipe like STDOUT, a file, browser ...
     * It should be overriden in concrete implementation.
     *
     * @param string $color The color of the ouput as hexadecimal string reprensentation
     *
     * @return  void
     * @access  protected
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    protected function output($color = '#7CFC00')
    {
        // first get content to local var
        $content = $this->_stripSpecialChars($this->getContent()).$this->lineBreak;

        // so we can clear the existing log
        $this->clear();

        // check if output possible ->
        if (headers_sent($filename, $linenum)) {
            throw new DoozR_Exception(
                'Headers already sent from file: '.$filename.' on line: '.$linenum
            );

        } else {
            // send log
			$this->_firePHP->fb($content, $this->_firePhpLogtype);
        }
    }

    /**
     * strips out special chars from content
     *
     * This method is intend to strip out special characters from content
     *
     * @param string $content The content to process
     *
     * @return  string Content with stripped characters
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _stripSpecialChars($content)
    {
        $content = str_replace("&", "&amp;", $content);
        $content = str_replace("<", "&lt;", $content);
        $content = str_replace(">", "&gt;", $content);

        return $content;
    }
}
