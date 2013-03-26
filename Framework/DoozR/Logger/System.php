<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Logger for logging to OS' system log
 *
 * System.php - This logger-implementation is intend to log to the OS' logging
 * subsystem
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
 * @subpackage DoozR_Logger_System
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
 * Logger for logging to OS' system log
 *
 * This logger-implementation is intend to log to the OS' logging subsystem
 *
 * @category   DoozR
 * @package    DoozR_Logger
 * @subpackage DoozR_Logger_System
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        Alogger.class.php, ILogger.class.php
 * @since      -
 */
final class DoozR_Logger_System extends DoozR_Logger_Abstract implements DoozR_Logger_Interface
{
    /**
     * the name of this logger
     *
     * @var string
     * @access protected
     */
    protected $name = 'System';

    /**
     * the version of this logger
     *
     * @var string
     * @access protected
     */
    protected $version = '$Rev$';

    /*******************************************************************************************************************
     * // BEGIN SYSTEM-LOGGER SPECIFIC VARIABLES
     ******************************************************************************************************************/

    /**
     * The source of the message
     *
     * @var integer
     * @access private
     */
    private $_source;

    /*******************************************************************************************************************
     * \\ END FILE-LOGGER SPECIFIC VARIABLES
     ******************************************************************************************************************/

    /**
     * This method is the constructor and responsible for building the instance.
     *
     * @param integer $level The level to use for this logger
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    protected function __construct($level, $fingerprint)
    {
        // call parents constructor
        parent::__construct($level, $fingerprint);

        // store level
        $this->level = $level;
    }

    /**
     * This method is intend to add the defined line-separator to log-content.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function separate()
    {
        // do nothing to seperate in system logger
        return true;
    }

    /**
     * This method is intend to write data to a defined pipe like STDOUT, a file, browser ...
     * It should be overriden in concrete implementation.
     *
     * @param string $color The color of the ouput as hexadecimal string reprensentation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function output($color = '#7CFC00')
    {
        /*
         Constant Description
        LOG_EMERG   system is unusable
        LOG_ALERT   action must be taken immediately
        LOG_CRIT    critical conditions
        LOG_ERR     error conditions
        LOG_WARNING warning conditions
        LOG_NOTICE  normal, but significant, condition
        LOG_INFO    informational message
        LOG_DEBUG   debug-level message
        */

        $flags = LOG_PID;
        $type  = LOG_INFO;

        // log entries must follow standard UTC timezone settings
        putenv('TZ=UTC');

        if ($this->contentType === 'ERROR') {
            $flags = $flags | LOG_PERROR;
            $type  = LOG_ERR;
        }

        // connect to syslog
        openlog($_SERVER['REQUEST_URI'], $flags, LOG_DAEMON);

        // first get content to local var
        $content = $this->getContent();

        // so we can clear the existing log
        $this->clear();

        // log to syslog
        syslog($type, $content);

        // close connection to syslog
        closelog();
    }

    /**
     * Dispatches a new route to this logger (e.g. for use as new filename).
     *
     * @param string $name The name of the route to dispatch
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean True on success, otherwise false
     * @access public
     */
    public function route($name)
    {
        // set new logile-name
        return $this->_source = $name;
    }
}

?>
