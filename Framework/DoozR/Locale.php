<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Locale
 *
 * Locale.php - Locale bootstrap of the DoozR Framework
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2014, Benjamin Carl - All rights reserved.
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
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Class/Singleton.php';

/**
 * DoozR - Locale
 *
 * Locale bootstrap of the DoozR Framework
 *
 * @category   DoozR
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Locale
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Locale extends DoozR_Base_Class_Singleton
{
    /**
     * Instance of config
     *
     * @var DoozR_Config
     * @access protected
     */
    protected $config;

    /**
     * Instance of logger
     *
     * @var DoozR_Logger
     * @access protected
     */
    protected $logger;


    /**
     * Constructor.
     *
     * @param DoozR_Config $config The config instance
     * @param DoozR_Logger $logger The logger instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Locale
     * @access protected
     */
    protected function __construct(DoozR_Config $config, DoozR_Logger $logger)
    {
        $this->config = $config;
        $this->logger = $logger;

        // retrieve timezone from config
        $timezone = $this->config->locale->timezone;

        // setup
        $this->setTimezone($timezone);
    }

    /**
     * Sets the default timezone
     *
     * This method is intend to set the default timezone
     * (e.g. to prevent E_NOTICE from mktime() + time())
     *
     * @param string $timezone The timezone to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if timezone could be set, otherwise FALSE
     * @access protected
     */
    protected function setTimezone($timezone = 'Europe/Berlin')
    {
        return date_default_timezone_set($timezone);
    }

    /**
     * Returns the current locale setup
     *
     * This method is intend to return the current locale setup.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Current locale configuration
     * @access public
     */
    public function getActiveSetup()
    {
        return array(
            'charset'  => $this->config->locale->charset,
            'encoding' => $this->config->locale->encoding,
            'language' => $this->config->locale->language,
            'locale'   => $this->config->locale->locale,
        );
    }
}
