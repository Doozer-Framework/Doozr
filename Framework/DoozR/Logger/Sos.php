<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Logger for SOS
 *
 * Sos.php - This logger implementation is intend to log to SOS (Socket-Output-Server) from
 * Powerflashers [http://www.sos.powerflasher.com/developer-tools/sosmax/download/]
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
 * @subpackage DoozR_Logger_Sos
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

/**
 * Logger for SOS
 *
 * This logger implementation is intend to log to SOS (Socket-Output-Server) from
 * Powerflashers [http://www.sos.powerflasher.com/developer-tools/sosmax/download/]
 *
 * @category   DoozR
 * @package    DoozR_Logger
 * @subpackage DoozR_Logger_Sos
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        Alogger.class.php, ILogger.class.php
 * @since      -
 */
final class DoozR_Logger_Sos extends DoozR_Logger_Abstract implements DoozR_Logger_Interface
{
    /**
     * Name of this logger
     *
     * @var string
     * @access private
     */
    protected $name = 'Sos';

    /**
     * Version of this logger
     *
     * @var string
     * @access protected
     */
    protected $version = '$Rev: 1266 $';


    /*******************************************************************************************************************
     * // BEGIN SOS-LOGGER SPECIFIC VARIABLES
     ******************************************************************************************************************/

    /**
     * holds the sos-logtype of the last logger-call
     *
     * @var string
     * @access private
     */
    private $_sosLogtype;

    /**
     * the host to log to
     *
     * @var string
     * @access private
     */
    private $_host;

    /**
     * the port on the host
     *
     * @var integer
     * @access private
     */
    private $_port;

    /**
     * the socket holding the connection
     *
     * @var object
     * @access private
     */
    private $_socket = null;

    /*******************************************************************************************************************
     * \\ END SOS-LOGGER SPECIFIC VARIABLES
     ******************************************************************************************************************/


    /**
     * constructs the class
     *
     * This method is the constructor and responsible for building the instance.
     *
     * @param integer $level The level to use for this logger
     *
     * @return  void
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    protected function __construct($level, $fingerprint, $config)
    {
        // call parents constructor
        parent::__construct($level, $fingerprint);

        // level
        $this->level = $level;

        // set line seperator
        $this->lineSeparator = '';

        // store config
        self::$config = $config;

        // retrieve logging target (host + port)
        $this->_setupConnection();

        // open socket connection here one time
        $this->_connect();
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
        $this->_sosLogtype = $this->_convertToSosLogtype($type);

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
     * override for parent::inject()
     *
     * This method is intend to convert the logtype from raw-collection to the
     * sos-logtype needed for this loggerimplementation. after converting the
     * call is dipatched to parent.
     *
     * @param array $rawCollection The raw collection with log-information
     *
     * @return mixed The parent-call result
     *
     * @see Framework/Core/Controller/Logger/ALogger::inject()
     */
    protected function inject(array $rawCollection)
    {
        // grab the type for conversion to sos-type
        $this->_sosLogtype = $this->_convertToSosLogtype($rawCollection[0]['type']);

        // and dispatch call to parent to process it
        return parent::inject($rawCollection);
    }

    /**
     * setup the connection(-data)
     *
     * this method sets up the connection data
     *
     * @return  void
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _setupConnection()
    {
        // get all logger configs from core-config
        $configurations = self::$config->logging->logger();

        //
        foreach ($configurations as $configuration) {
            if ($configuration->name == 'sos') {
                break;
            } else {
                $configuration = null;
            }
        }

        // check result
        if (!$configuration) {
            throw new DoozR_Exception(
                'Error while trying to read configuration for SOS-Logger. Please make sure that configuration exists.'
            );
        }

        // check for automatic detect IP for SOS-Logger-Client
        if ($configuration->host == 'automatic') {
            // get current clients ip as client for logger
            $configuration->host = $_SERVER['REMOTE_ADDR'];
        }

        // store the host to log to
        $this->_host = $configuration->host;

        // store the port on the host to log to
        $this->_port = $configuration->port;
    }

    /**
     * connects the logger to the defined host (at defined port)
     *
     * This method is intend to connect the logger to a defined client (host/ip) on a defined port.
     *
     * @return  boolean True on success
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @throws  DoozR_Exception
     */
    private function _connect()
    {
        // pre-assume
        $errorNumber = 0;
        $errorMessage = '';

        // try to open socket to configured host on configured port
        $this->_socket = @fsockopen($this->_host, $this->_port, $errorNumber, $errorMessage, 1);

        // if not successful - exception
        if (!$this->_socket) {
            throw new DoozR_Exception(
                'Error while opening socket to host: '.$this->_host.' on port: '.$this->_port.' '.
                'fsockopen() reports Error-Number: '.$errorNumber.' Error-Message: '.$errorMessage
            );
        }

        // return success
        return true;
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
    private function _convertToSosLogtype($type)
    {
        // format log for sos
        switch ($type) {
        case 'LOG':
            return 'TRACE';
            break;
        case 'UNCLASSIFIED':
            return 'INFO';
            break;
        case 'NOTICE':
            return 'WARN';
            break;
        case 'WARNING':
            return 'ERROR';
            break;
        case 'ERROR':
            return 'FATAL';
            break;
        default:
            return 'TRACE';
            break;
        }
    }

    /**
     * sos-logger specific output override
     *
     * This method is intend to write data to a XML-Socket (SOS Logger)
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
        $content = $this->getContent();

        // so we can clear the existing log
        $this->clear();

        // strip out special chars - for XML socket transfer!
        $content = $this->_stripSpecialChars($content);

        // insert sos type
        $content = "!SOS<showMessage key='".$this->_sosLogtype."'>".
                   str_replace("<", "&lt;", $content)."</showMessage>\n";

        // try to send to socket connection
        if (!is_null($this->_socket)) {
            try {
                if (!@fwrite($this->_socket, $content.chr(0))) {
                    throw new ELogger_SOS_SocketWriteFailed($this->_socket, $content);
                }
            }
            catch(ELogger_SOS_SocketWriteFailed $e) {
                DoozR_Core::coreError($e->getMessage(), $e->getCode());
            }
            catch(Exception $e) {
                DoozR_Core::coreError($e->getMessage(), $e->getCode());
            }
        }
    }

    /**
     * strips special-/control-chars out of the given string to
     * ensure that the log-content (data) can be send to sos-logger
     *
     * @param string $content The content to strip special-/control-chars from
     *
     * @return  string The processed/resulting string
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _stripSpecialChars($content)
    {
        // strip chars
        $content = str_replace("&", "&amp;", $content);
        $content = str_replace("<", "&lt;", $content);
        $content = str_replace(">", "&gt;", $content);

        // return processed result
        return $content;
    }

    /**
     * get called on garbage collecting (object destroyed)
     *
     * destruct method - gets called when instance of this class get
     * collected from garbage collector. Logs destroying to logger.
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @version Method available since Release 1.0.0
     * @since   1.0
     */
    public function __destruct()
    {
        // close sos-loggers xml socket
        if ($this->_socket !== false) {
            fclose($this->_socket);
            $this->_socket = null;
        }
    }
}

?>
