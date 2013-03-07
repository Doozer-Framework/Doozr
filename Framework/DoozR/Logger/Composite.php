<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Logger Composite
 *
 * Composite.php - This logger dispatches all log-entries to registered loggers
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
 * @subpackage DoozR_Logger_Composite
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        Abstract.php, Interface.php
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Logger/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Logger/Interface.php';

/**
 * Logger Composite
 *
 * This logger dispatches all log-entries to registered loggers
 *
 * @category   DoozR
 * @package    DoozR_Logger
 * @subpackage DoozR_Logger_Composite
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        Abstract.php, Interface.php
 * @since      -
 */
final class DoozR_Logger_Composite extends DoozR_Logger_Abstract implements DoozR_Logger_Interface
{
    /**
     * name of this logger
     *
     * @var string
     * @access protected
     */
    protected $name = 'Logger-Composite';

    /**
     * the version of this logger
     *
     * @var string
     * @access protected
     */
    protected $version = '$Rev$';

    /**
     * holds the logger retrieved from Config.ini.php
     *
     * @var array
     * @access private
     */
    private $_logger = array();

    /**
     * holds the current status of logging (true if logging enabled otherwise false).
     *
     * @var boolean
     * @access private
     */
    private $_enabled = false;


    /**
     * constructs the class
     *
     * constructor builds the class
     *
     * @param integer $level The level to use in general
     *
     * @return  object instance of this class
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    protected function __construct($level = 1)
    {
        // call parents constructor
        parent::__construct($level);

        // holds the log-level for this logger
        $this->level = $level;

        // get config-setting for logging enabled (true|false)
        //$this->_enabled = DoozR_Core::config()->get('LOGGING.ENABLED');
        $this->_enabled = true;

        // process config for logger and factory only if logging is activated
        if ($this->_enabled) {
            // get list of active loggers from config
            //$logger = DoozR_Core::config()->get('LOGGING.LOGGER');
            $logger = null; //array(file:9,client:2);


            // factor logger - if at minum one exist/found
            if (strlen($logger)) {
                $this->_factory($logger);
            }
        }
    }

    /**
     * injects raw logdata into all attached loggers
     *
     * This method is intend to inject the raw logdata into all attached loggers.
     *
     * @param array $rawCollection The raw collection of log-data
     *
     * @return  boolean True if succesful otherwise false
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function inject(array $rawCollection)
    {
        // assume that result is true
        $result = true;

        // iterate over attached logger and inject log-content
        foreach ($this->_logger as $key => $value) {
            $result &= $this->_logger[$key]->inject($rawCollection);
        }

        // return the result
        return $result;
    }

    /**
     * returns defined logger as array
     *
     * This method is intend to return the current defined logger as array.
     *
     * @return  array Defined logger(s) as array
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getLogger()
    {
        return $this->_logger;
    }


    /**
     * sets the status of logging-enabled
     *
     * This method is intend to set the status of logging (true | false)
     *
     * @param boolean $enabled status of logging-enabled
     *
     * @return  void
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @static
     */
    public static function setEnabled($enabled = false)
    {
        $this->_enabled = $enabled;
    }


    /**
     * sorting of loggers
     *
     * sorts the loggers defined in config to log to HEADER-based
     * loggers like firePHP first
     *
     * @return  void
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @static
     */
    private function _sortLogger()
    {
        // empty tmp array
        $loggerSorted = array();

        // check if firePHP logger is enabled
        if (isset($this->_logger['firephp'])) {
            $loggerSorted[] = $this->_logger['firephp'];

            foreach ($this->_logger as $key => $value) {
                if ($key != 'firephp') {
                    $loggerSorted[] = $this->_logger[$key];
                }
            }

            // remap internal array
            $this->_logger = $loggerSorted;
        }
    }

    /**
     * logs a message to all appended loggers
     *
     * This method is intend to log a message to all appended loggers.
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
        $content  = '',
        $type     = 'LOG',
        $file     = false,
        $line     = false,
        $class    = false,
        $method   = false,
        $function = false,
        $optional = false
    ) {
        // check if debug enabled - otherwise log to dev/null
        if ($this->_enabled) {
            // dispatch log-call to all real logger
            foreach ($this->_logger as $logger) {
                if ($logger->getName() != 'Firephp' || !defined('DISABLE_FIREPHP')) {
                    $log = $logger->log($content, $type, $file, $line, $class, $method, $function, $optional);
                }
            }
        }

        // success
        return true;
    }

    /**
     * dispatches a new route to all defined loggers
     *
     * dispatches a new route to all defined logger (e.g. for use as new file
     * name [file-logger]). e.g. to prevent that (if DoozR-MVC is active) file-logger
     * logs everything to the same file(-name).
     *
     * @param string $routeName The name of the route to dispatch
     *
     * @return  boolean True always
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function route($routeName = null)
    {
        // check if debug enabled - otherwise log to dev/null
        if ($this->_enabled === true && !is_null($routeName)) {
            // dispatch log-call to all real logger
            foreach ($this->_logger as $logger) {
                // only try to add route if route()-method exists
                if (method_exists($logger, 'route')) {
                    $logger->route($routeName);
                }
            }
        }

        // success
        return true;
    }

    /**
     * logger factory
     *
     * creates and stores instances of loggers
     *
     * @param string $logger the logger config from Config.ini.php
     *
     * @return  void
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @static
     */
    private function _factory($logger)
    {
        // retrieve instances of logger
        $this->_logger = $this->_parseLogger($logger);

        // resort the logger (bring firePHP to front if exist - firePHP works with HEADER!!!)
        $this->_sortLogger();
    }

    /**
     * parses logger(s) and instantiate them
     *
     * This method is intend to parse defined logger(s) and instantiate them.
     *
     * @param string $logger list of logger-names to instantiate
     *
     * @return  array An array of instances of defined loggers
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @static
     */
    private function _parseLogger($logger)
    {
        // convert list of logger to array
        $logger = explode(',', mb_strtolower($logger));

        // temp array of logger-instances
        $loggerInstances = array();

        // get default logginglevel
        $defaultLoglevel = DoozR_Core::config()->get('LOGGING.LEVEL');

        // get loggers as defined! in users order!
        foreach ($logger as $currentLogger) {
            // check for an individual level?
            if (mb_stristr($currentLogger, ':')) {
                $loggerConfig = explode(':', $currentLogger);
            } else {
                // else default log level = 1
                $loggerConfig = array(
                    $currentLogger,
                    $defaultLoglevel
                );
            }

            // prepare config-settings of current logger
            $name = ucfirst($loggerConfig[0]);
            $loglevel = $loggerConfig[1];

            // load the required file (name + path)
            include_once DOOZR_DOCUMENT_ROOT.'Core/Controller/Logger/'.$name.'/Logger'.$name.'.class.php';

            // instanciate the logger
            $loggerInstances[$name] = call_user_func('Logger_'.$name.'::getInstance', $loglevel);
        }

        // return array of logger-instances
        return $loggerInstances;
    }

    /**
     * output method (not needed for collecting logger)
     *
     * This method is intend to write data to a defined pipe like STDOUT, a file, browser ...
     * It should be overriden in concrete implementation.
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
    }
}

?>
