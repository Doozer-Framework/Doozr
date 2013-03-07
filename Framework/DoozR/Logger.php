<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Logger
 *
 * Logger.php - This logger is the composite for accessing the logging-subsystem
 * of the DoozR-Framework
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
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Logger
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
 * Logger
 *
 * This logger is the composite for accessing the logging-subsystem of the DoozR-Framework
 *
 * @category   DoozR
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Logger
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        Abstract.php, Interface.php
 * @since      -
 */
final class DoozR_Logger extends DoozR_Logger_Abstract implements DoozR_Logger_Interface
{
    /**
     * name of this logger
     *
     * @var string
     * @access protected
     */
    protected $name = 'Composite';

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
    private $_enabled = true;

    /**
     * holds the default log level from config (once set)
     *
     * @var integer
     * @access private
     */
    private $_defaultLoglevel;

    private static $_clientFingerprint;

    /**
     * maximum loglevel
     *
     * @var integer
     * @access const
     */
    const LOG_LEVEL_MAX = 9;


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
    protected function __construct($level = null)
    {
        // call parents constructor
        parent::__construct($level);

        // holds the log-level for this logger
        $this->level = ($level) ? $level : self::LOG_LEVEL_MAX;

        // temporary set default loglevel to current level
        $this->_defaultLoglevel = $this->level;

        // get fingerprint of client as UId
        self::$_clientFingerprint = checksum($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']);

        // attach default collecting logger till subsystem is configurable
        $this->attach('collecting', self::LOG_LEVEL_MAX, null, false);
    }

    /**
     * sets the default log-level
     *
     * This method is intend to set the default log-level.
     *
     * @param integer $level The log-level to set as standard
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function setDefaultLoglevel($level)
    {
        $this->_defaultLoglevel = $level;
    }

    /**
     * returns the default log-level
     *
     * This method is intend to return the default log-level.
     *
     * @return  integer The default log-level
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getDefaultLoglevel()
    {
        return $this->_defaultLoglevel;
    }

    /**
     * attachs a logger to the logger-subsystem
     *
     * This method is intend to attach a logger to the logger-subsystem.
     *
     * @param mixed   $logger The name of the logger
     * @param mixed   $level  The log-level of the logger
     * @param mixed   $config An instance of DoozR_Config
     * @param boolean $header TRUE to reorder the list (required if type = HTTP-Header-Logger)
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function attach($logger, $level = false, $config = null, $header = true)
    {
        // is config string (our defaults) or is it custom array config
        if (!is_array($logger)) {
            $logger = array(
                'name'  => $logger,
                'level' => $level
            );
        }

        // return hash of created logger instance
        return $this->_factory($logger, $config, $header);
    }

    /**
     * detaches a logger from the logger-subsystem
     *
     * This method is intend to detach a logger from the logger-subsystem.
     *
     * @param string  $name   The name of the logger to remove
     * @param boolean $inject Affects 'collecting': Injects the collected logs to attached logger if TRUE
     *
     * @return  boolean TRUE if logger was removed, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function detach($name, $inject = true)
    {
        // check for automatic collection dispatching/injection
        if ($name == 'collecting' && $inject) {
            $this->inject($this->getCollection(true, true));
        }

        // remove logger from array and store
        $this->_logger = array_remove_value($this->_logger, $name);

        // return success
        return true;
    }

    /**
     * logger factory
     *
     * creates and stores instances of loggers
     *
     * @param array   $setup  The config of logger
     * @param mixed   $config An instance of DoozR_Config, or NULL
     * @param boolean $header TRUE if logger is header based, otherwise FALSE
     *
     * @return  void
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @static
     */
    private function _factory(array $setup, $config, $header)
    {
        // level set?
        if (!isset($setup['level']) || !$setup['level'] ) {
            $setup['level'] = $this->getDefaultLoglevel();
        }

        if (!isset($setup['class'])) {
            $setup['class'] = __CLASS__.'_'.ucfirst($setup['name']);
        }

        if (!isset($setup['file'])) {
            $setup['file'] = DOOZR_DOCUMENT_ROOT.str_replace('_', DIRECTORY_SEPARATOR, $setup['class']).'.php';
        }

        if (!isset($setup['constructor'])) {
            $setup['constructor'] = 'getInstance';
        }

        // get file
        include_once $setup['file'];

        // get instance of the given logger and store
        $loggerInstance = $this->instanciate(
            $setup['class'],
            array($setup['level'], self::$_clientFingerprint, $config),
            $setup['constructor']
        );

        // some of the loggers (e.g. firePHP which works with HTTP-Header) need to be dispatched
        // before an output by an other logger is generated
        if ($header) {
            $this->_logger = array_merge(array($loggerInstance), $this->_logger);

        } else {
            $this->_logger[$setup['name']] = $loggerInstance;

        }

        // return id/hash of logger
        return md5($setup['name']);
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

            // instanciate the logger
            $loggerInstances[$name] = call_user_func('Logger_'.$name.'::getInstance', $loglevel);
        }

        // return array of logger-instances
        return $loggerInstances;
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
        foreach ($this->_logger as $name => $logger) {
            // inject into all but collecting
            if ($name != 'collecting') {
                $result &= $this->_logger[$name]->inject($rawCollection);
            }
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
     * returns true if current logger is collecting, otherwise false
     *
     * This method is intend to return TRUE if current logger is collecting, otherwise FALSE.
     *
     * @return  boolean TRUE if current logger is collecting, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function isCollecting()
    {
        return (count($this->_logger) == 1 && isset($this->_logger['collecting']));
    }

    /**
     * sets the status of logging to enabled
     *
     * This method is intend to set the status of logging to enabled.
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function enable()
    {
        $this->_enabled = true;
    }

    /**
     * sets the status of logging to disabled
     *
     * This method is intend to set the status of logging to disabled.
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function disable()
    {
        $this->_enabled = false;
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
     * returns the complete collection of log-content as string or array
     *
     * This method is intend to return the complete collection of log-content as string or array.
     *
     * @param boolean $returnArray True to retrieve array, false to retrieve string
     * @param boolean $returnRaw   True to retrieve the raw content -> not preformatted
     *
     * @return  mixed string if $returnArray false, otherwise array
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getCollection($returnArray = false, $returnRaw = false)
    {
        return $this->_logger['collecting']->getCollection($returnArray, $returnRaw);
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
        if ($this->_enabled === true && $routeName) {
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
     * output method (not needed for collecting logger)
     *
     * This method is intend to write data to a defined pipe like STDOUT, a file, browser ...
     * It should be overriden in concrete implementation.
     *
     * @param string $color The color used as font-color for output
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
