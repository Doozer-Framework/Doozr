<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Debug
 *
 * Debug.php - Debug Manager - configures PHP dynamic in debug-mode and setup hooks
 * on important parts.
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
 * @subpackage DoozR_Core_Debug
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Class/Singleton/Strict.php';

/**
 * DoozR Debug
 *
 * Debug Manager - configures PHP dynamic in debug-mode and setup hooks
 * on important parts.
 *
 * @category   DoozR
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Debug
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Debug extends DoozR_Base_Class_Singleton_Strict
{
    /**
     * holds the information (status) of debug-mode (true
     * = enabled / false = disabled)
     *
     * @var boolean
     * @access private
     */
    private $_enabled = false;

    /**
     * holds an instance of logger
     *
     * @var object
     * @access private
     */
    private $_logger;


    /**
     * constructs the class
     *
     * constructor builds the class
     *
     * @param object  $logger  An instance of DoozR_Logger
     * @param boolean $enabled Defines it debug mode is enabled or not
     *
     * @return  object Instance of this class
     * @access  protected
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    protected function __construct($logger, $enabled = false)
    {
        // store instances
        $this->_logger = $logger;

        // log debug state
        $this->_logger->log('Debug-Manager - debug-mode enabled = '.var_export($enabled, true));

        // check for initial trigger
        if ($enabled) {
            $this->enable();
        } else {
            $this->disable();
        }
    }

    /**
     * enables debugging
     *
     * This method is intend to enable debugging.
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function enable()
    {
        if ($this->_enable()) {
            $this->_enabled = true;
            $this->_logger->log('Debug-mode successfully enabled.');
        } else {
            $this->_enabled = false;
            throw new Exception('Debug-mode could not be enabled!');
        }
    }

    /**
     * disables debugging
     *
     * This method is intend to disable debugging.
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function disable()
    {
        if ($this->_disable()) {
            $this->_enabled = false;
            $this->_logger->log('Debug-mode successfully disabled!');
        } else {
            $this->_enabled = true;
            throw new Exception('Debug-mode could not be disabled!');
        }
    }

    /**
     * responsible for enabling debugging
     *
     * This method is responsible for enabling debugging.
     *
     * @return  boolean True if debug was successfully enabled, otherwise false
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    private function _enable()
    {
        // debug is enabled! now we try to set error handling!
        // safe_mode must be enabled (in PHP < 5.3) or PHP-version must be >= 5.3
        if (DOOZR_PHP_VERSION < 5.3 || !ini_get('safe_mode')) {
            // set error reporting to maximum output
            error_reporting(DOOZR_PHP_ERROR_MAX);
            ini_set('error_reporting', DOOZR_PHP_ERROR_MAX);

            // don't display errors
            ini_set('display_errors', 'Off');
            //ini_set('display_errors', 'On');
            //ini_set('display_startup_errors', 1);
            //ini_set('html_errors', 0);

            // don't log errors - we log them through custom logger(s)
            ini_set('log_errors', 'Off');

            // and return -> success
            return true;
        } else {
            $log =  'Safe mode is enabled! So error_reporting + error_handling could\'nt be set at runtime!';
            $log .= 'Please ensure that you have configured error handling in your php.ini';

            $this->_logger->log($log, 'WARNING');

            // and return -> no success
            return false;
        }
    }

    /**
     * responsible for disabling debugging
     *
     * This method is responsible for disabling debugging.
     *
     * @return  boolean True if debug was successfully disabled, otherwise false
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    private function _disable()
    {
        // if debugging mode is false hide all errors!
        // set error_reporting to null (0) to hide PHP's reports
        error_reporting(0);

        // to ensure that it works set to ini too
        // TODO: check if really needed
        ini_set('error_reporting', 0);
        ini_set('display_errors', 'Off');
        //ini_set('display_startup_errors', 0);
        //ini_set('html_errors', 1);

        // TODO: check if really needed
        // enable PHP's logging of error's?!
        ini_set('log_errors', 'On');

        // and return -> success
        return true;
    }
}

?>
