<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - I18n - Service - Format - Abstract
 *
 * Abstract.php - Abstract base class for formatter of the I18n module
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
 * @package    DoozR_Service
 * @subpackage DoozR_Service_I18n
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Class.php';

/**
 * DoozR - I18n - Service - Format - Abstract
 *
 * Abstract base class for formatter of the I18n module
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_I18n
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
abstract class DoozR_I18n_Service_Format_Abstract extends DoozR_Base_Class
{
    /**
     * the type of the format-class
     *
     * @var string
     * @access protected
     */
    protected $type;

    /**
     * The DoozR_Registry instance containing core objects
     *
     * @var DoozR_Registry
     * @access protected
     */
    protected $registry;

    /**
     * locale of format-class
     *
     * @var string
     * @access protected
     */
    protected $locale;

    /**
     * namespace of format-class
     *
     * @var string
     * @access protected
     */
    protected $namespace;

    /**
     * configuration of format-class
     *
     * @var array
     * @access protected
     */
    protected $configI18n;

    /**
     * configuration of format-class
     *
     * @var array
     * @access protected
     */
    protected $configL10n;

    /**
     * translator instance for active locale
     *
     * @var object
     * @access protected
     */
    protected $translator;

    /**
     * the localized config of Format_String
     *
     * @var object
     * @access protected
     */
    protected $config;


    /*------------------------------------------------------------------------------------------------------------------
     | BEGIN TOOLS + HELPER
     +----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to create the bad-word table
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    public function getConfig()
    {
        // check if we must load localized config (I10n) was already retrieved
        if (!$this->config) {
            // get config manager of DoozR-Framework
            include_once DOOZR_DOCUMENT_ROOT.'DoozR/Config/Container/Ini.php';

            // configuration-file
            $configurationFile = DOOZR_APP_ROOT.'Data/Private/I18n/'.$this->locale.'/Localization/'.$this->type.'.ini';

            // get configreader
            $config = DoozR_Loader_Serviceloader::load('Config', 'Ini');

            // read config
            $this->config = $config->read($configurationFile);
        }

        // return the configuration
        return $this->config;
    }

    /*------------------------------------------------------------------------------------------------------------------
     | BEGIN MAIN CONTROL METHODS (CONSTRUCTOR AND INIT)
     +----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to act as constructor.
     *
     * @param DoozR_Registry_Interface $registry   The registry
     * @param string                   $locale     The locale this instance is working with
     * @param string                   $namespace  The active namespace of this format-class
     * @param object                   $configI18n An instance of DoozR_Config_Ini holding the I18n-config
     * @param object                   $configL10n An instance of DoozR_Config_Ini holding the I10n-config (for locale)
     * @param object                   $translator An instance of a translator (for locale)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_ Instance of this class
     * @access public
     */
    public function __construct(
        DoozR_Registry_Interface $registry = null,
        $locale                            = null,
        $namespace                         = null,
        $configI18n                        = null,
        $configL10n                        = null,
        $translator                        = null
    ) {
        // store registry
        $this->registry = $registry;

        // store configuration
        $this->locale = $locale;

        // store configuration
        $this->namespace = $namespace;

        // store configuration I18n
        $this->configI18n = $configI18n;

        // store configuration I10n
        $this->configL10n = $configL10n;

        // store translator
        $this->translator = $translator;
    }
}
