<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Locale
 *
 * Locale.php - Locale bootstrap of the Doozr Framework
 *
 * PHP versions 5.5
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
 *
 * Copyright (c) 2005 - 2016, Benjamin Carl - All rights reserved.
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
 * @package    Doozr_Kernel
 * @subpackage Doozr_Kernel_Locale
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Class/Singleton.php';

/**
 * Doozr - Locale
 *
 * Locale bootstrap of the Doozr Framework
 *
 * @category   Doozr
 * @package    Doozr_Kernel
 * @subpackage Doozr_Kernel_Locale
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Locale extends Doozr_Base_Class_Singleton
{
    /**
     * Instance of configuration
     *
     * @var Doozr_Configuration
     * @access protected
     */
    protected $config;

    /**
     * Instance of logging
     *
     * @var Doozr_Logging
     * @access protected
     */
    protected $logger;

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param Doozr_Configuration $config The configuration instance
     * @param Doozr_Logging       $logger The logging instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \Doozr_Locale
     * @access protected
     */
    protected function __construct(Doozr_Configuration $config, Doozr_Logging $logger)
    {
        $this->config = $config;
        $this->logger = $logger;

        // retrieve timezone from configuration
        $timezone = $this->config->kernel->localization->timezone;

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
     * @return bool TRUE if timezone could be set, otherwise FALSE
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
        $localizationSetup = $this->config->kernel->localization;

        return array(
            'charset'  => $localizationSetup->charset,
            'encoding' => $localizationSetup->encoding,
            'language' => $localizationSetup->language,
            'locale'   => $localizationSetup->locale,
        );
    }
}
