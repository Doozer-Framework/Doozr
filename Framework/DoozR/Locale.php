<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Locale
 *
 * Locale.php - Locale bootstrap of the DoozR Framework
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
 * @subpackage DoozR_Core_Locale
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Class/Singleton.php';

/**
 * DoozR Locale
 *
 * Locale bootstrap of the DoozR Framework
 *
 * @category   DoozR
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Locale
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Locale extends DoozR_Base_Class_Singleton
{
    /**
     * holds instance of config
     *
     * @var object
     * @access private
     */
    private $_config;

    /**
     * holds instance of logger
     *
     * @var object
     * @access private
     */
    private $_logger;


    /**
     * constructor
     *
     * This method is the constructor
     *
     * @param object $config The config instance
     * @param object $logger The logger instance
     *
     * @return  void
     * @access  protected
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @DoozRInject DoozR_Config,DoozR_Logger
     */
    protected function __construct($config, $logger)
    {
        $this->_config = $config;
        $this->_logger = $logger;

        // retrieve timezone from config
        $timezone = $this->_config->locale->timezone();

        // setup
        $this->_setTimezone($timezone);
    }

    /**
     * sets the default timezone
     *
     * This method is intend to set the default timezone
     * (e.g. to prevent E_NOTICE from mktime() + time())
     *
     * @param string $timezone The timezone to set
     *
     * @return  boolean TRUE if timezone could be set, otherwise FALSE
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _setTimezone($timezone = 'Europe/Berlin')
    {
        return date_default_timezone_set($timezone);
    }

    /**
     * returns the current locale setup
     *
     * This method is intend to return the current locale setup.
     *
     * @return  array Current locale configuration
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getActiveSetup()
    {
        return array(
            'charset'  => $this->_config->locale->charset(),
            'encoding' => $this->_config->locale->encoding(),
            'language' => $this->_config->locale->language(),
            'locale'   => $this->_config->locale->locale()
        );
    }
}

?>
