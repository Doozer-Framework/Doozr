<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - I18n - Service - Localize - Abstract
 *
 * Abstract.php - Abstract base class for formatter of the I18n service
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
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Class.php';

/**
 * Doozr - I18n - Service - Localize - Abstract
 *
 * Abstract base class for Localizeter of the I18n service
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 *
 * @property   Doozr_Configuration_Hierarchy_I18n      $configI18n
 * @property   Doozr_Configuration_Hierarchy_I18n_L10n $configL10n
 */
abstract class Doozr_I18n_Service_Localize_Abstract extends Doozr_Base_Class
{
    /**
     * Type of the localize class.
     *
     * @var string
     */
    protected $type;

    /**
     * Doozr_Registry instance containing core objects.
     *
     * @var Doozr_Registry
     */
    protected $registry;

    /**
     * Locale of format class.
     *
     * @var string
     */
    protected $locale;

    /**
     * Namespace of format class.
     *
     * @var string
     */
    protected $namespace;

    /**
     * configuration of format class.
     *
     * @var array
     */
    protected $configI18n;

    /**
     * Configuration of format class.
     *
     * @var array
     */
    protected $configL10n;

    /**
     * Translator instance for active locale.
     *
     * @var object
     */
    protected $translator;

    /**
     * Localized config of Localize_String.
     *
     * @var object
     */
    protected $config;

    /*------------------------------------------------------------------------------------------------------------------
    | MAIN CONTROL METHODS (CONSTRUCTOR AND INIT)
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to act as constructor.
     *
     * @param Doozr_Registry_Interface $registry   The registry
     * @param string                   $locale     The locale this instance is working with
     * @param string                   $namespace  The active namespace of this format-class
     * @param object                   $configI18n An instance of Doozr_Config_Ini holding the I18n-config
     * @param object                   $configL10n An instance of Doozr_Config_Ini holding the I10n-config (for locale)
     * @param object                   $translator An instance of a translator (for locale)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __construct(
        Doozr_Registry_Interface $registry = null,
        $locale = null,
        $namespace = null,
        $configI18n = null,
        $configL10n = null,
        $translator = null
    ) {
        $this->registry   = $registry;
        $this->locale     = $locale;
        $this->namespace  = $namespace;
        $this->configI18n = $configI18n;
        $this->configL10n = $configL10n;
        $this->translator = $translator;
    }

    /*------------------------------------------------------------------------------------------------------------------
     | PUBLIC API
     +----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to create the bad-word table.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Configuration_Hierarchy_I18n_L10n
     */
    public function getConfiguration()
    {
        return $this->configL10n;
    }

    /**
     * Returns the active locale.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|string The current locale as string, otherwise NULL
     */
    public function getLocale()
    {
        return $this->locale;
    }
}
