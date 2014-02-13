<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - I18n - Service
 *
 * Service.php - I18n Service for internationalization and localization.
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

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Service/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/I18n/Service/Detector.php';
require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/I18n/Service/Translator.php';
require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Template/Service/Lib/PHPTAL/PHPTAL/TranslationService.php';

/**
 * DoozR - I18n - Service
 *
 * I18n Service for internationalization and localization.
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_I18n
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @service    Singleton
 * @inject     DoozR_Registry:DoozR_Registry identifier:getInstance type:constructor position:1
 */
class DoozR_I18n_Service extends DoozR_Base_Service_Singleton implements PHPTAL_TranslationService
{
    /**
     * The encoding
     *
     * @var string
     * @access private
     */
    private $_encoding = 'UTF-8';

    /**
     * Contains the current active locale
     *
     * @var mixed
     * @access private
     */
    private $_activeLocale;

    /**
     * This services autoloader id if init, otherwise NULL
     *
     * @var string
     * @access private
     * @static
     */
    private static $_autoloader;

    /**
     * Contains instance of Service Config (used for reading INI-Files)
     *
     * @var DoozR_Config_Service
     * @access private
     * @static
     */
    private static $_config;

    /**
     * holds the config instances indexed by locale
     *
     * @var array
     * @access private
     * @static
     */
    private static $_configurationByLocale = array();

    /**
     * Default formatter of I18n service
     *
     * @var string
     * @access public
     * @const
     */
    const FORMAT_DEFAULT = 'String';

    /**
     * Formatter for Strings (Bad-Word replacement, Highlighting, ...)
     *
     * @var string
     * @access public
     * @const
     */
    const FORMAT_STRING = 'String';

    /**
     * Formatter for Currencies (Thousands-Separator, Decimal-Dot, Currency-Sign + position ...)
     *
     * @var string
     * @access public
     * @const
     */
    const FORMAT_CURRENCY = 'Currency';

    /**
     * Formatter for Datetime values (Year-Month-Day date position, start of week [Sun/Mondays] ...)
     *
     * @var string
     * @access public
     * @const
     */
    const FORMAT_DATETIME = 'Datetime';

    /**
     * Formatter for Measure values meter, kilometer, miles, inches (...)
     *
     * @var string
     * @access public
     * @const
     */
    const FORMAT_MEASURE = 'Measure';

    /**
     * Formatter for Numbers (...)
     *
     * @var string
     * @access public
     * @const
     */
    const FORMAT_NUMBER = 'Number';

    /**
     * The translator singleton for templates
     *
     * @var DoozR_I18n_Service_Translator
     * @access private
     * @static
     */
    private static $_templateTranslator;

    /**
     * The name of the L10n file.
     *
     * @var string
     * @access public
     * @const
     */
    const FILE_NAME_L10N = 'L10n';

    /**
     * The extension of the config file
     *
     * @var string
     * @access public
     * @const
     */
    const FILE_EXTENSION_L10N = 'ini';


    protected $autoloader = true;


    /*------------------------------------------------------------------------------------------------------------------
     | TEARUP
     +----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor for services.
     *
     * This method is a replacement for __construct
     * PLEASE DO NOT USE __construct() - make always use of __tearup()!
     *
     * @param DoozR_Config_Interface $config An instance of a config compliant config reader
     * @param string|null            $locale A locale (de, at-de, ...) OR NULL to use autodetection
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __tearup(DoozR_Config_Interface $config, $locale = null)
    {
        // check if requirements fulfilled
        self::_checkRequirements();

        // store config passed to this instance
        self::$_config = $config;

        // if no locale was passed then we try to read the prefered locale from client
        if ($locale === null) {
            $locale = $this->getClientPreferedLocale();
        }

        // store the given locale
        $this->_activeLocale = $locale;
    }

    /*------------------------------------------------------------------------------------------------------------------
     | BEGIN PUBLIC INTERFACES
     +----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the available locales defined in config.
     *
     * This method is intend to retunr all locales defined in configuration.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array An array containing the available locales with numerical index
     * @access public
     */
    public function getAvailableLocales()
    {
        return self::$_config->i18n->defaults->available();
    }

    /**
     * @param $locales
     *
     * @return mixed
     */
    public function setAvailableLocales($locales)
    {
        return self::$_config->i18n->defaults->available($locales);
    }

    /**
     * Sets the active locale.
     *
     * This method is intend to set the passed locale as active one.
     *
     * @param string $locale The locale to set as active
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     * @throws DoozR_I18n_Service_Exception
     */
    public function setActiveLocale($locale)
    {
        // check if locale is valid
        if (!$this->isValidLocale($locale)) {
            throw new DoozR_I18n_Service_Exception(
                'EXCEPTION_MODULE_DOOZR_I18N'
            );
        }

        $result = $this->_activeLocale = $locale;
        return $result;
    }

    /**
     * Returns the currently active locale.
     *
     * This method is intend to return the currently active locale.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The active locale if set, otherwise NULL
     * @access public
     */
    public function getActiveLocale()
    {
        return $this->_activeLocale;
    }

    /**
     * Initializes the template translator.
     *
     * This method is intend to initialize the template translator.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access private
     */
    private function _initTemplateTranslator()
    {
        if (!self::$_templateTranslator) {
            self::$_templateTranslator = $this->getTranslator();

            self::$_templateTranslator->setNamespace(
                self::$_config->i18n->defaults->namespace()
            );
        }
    }

    /**
     * This method is intend to return the current active locale.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The active locale
     * @access public
     */
    public function getClientPreferedLocale()
    {
        if ($this->_activeLocale !== null) {
            $locale = $this->_activeLocale;

        } else {
            // get detector
            $detector = $this->getDetector();

            // detect user's prefered locale
            $detector->detect();

            // get the locale from detector
            $locale = $detector->getLocale();
        }

        // return the active locale
        return $locale;
    }

    /**
     * Returns an instance of the locale detector.
     *
     * This method is intend to return an instance of the locale detector. The locale detector
     * can be used to detect the clients prefered locale.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_I18n_Service_Detector Instance of the locale detector
     * @access public
     */
    public function getDetector()
    {
        return DoozR_I18n_Service_Detector::getInstance(self::$_config, $this->registry);
    }

    /**
     * This method is intend to return the instance of the locale-detector.
     *
     * @param string $type   The type of the formatter to return
     * @param string $locale The locale to use for formatter
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_I18n_Service_Format_Abstract The instance of the locale-detector
     * @access public
     * @throws DoozR_I18n_Service_Exception
     */
    public function getLocalizer($type = self::FORMAT_DEFAULT, $locale = null)
    {
        // if no locale was passed use the active one
        if ($locale === null) {
            $locale = $this->_activeLocale;
        }

        // retrieve valid input
        $input = $this->_validateInput($locale);

        // convert type to required format
        $type = ucfirst(strtolower($type));

        // check for redirect -> !
        if ($input['redirect']) {
            // return $this->getFormatter($type, $this->getClientPreferedLocale());
            return $this->getLocalizer($type, $input['redirect']);

        } else {
            return $this->instanciate(
                'DoozR_I18n_Service_Localize_'.$type,
                array(
                    $this->registry,
                    $input['locale'],
                    null,
                    self::$_config,
                    $input['config'],
                    $this->getTranslator($input['locale'])
                )
            );
        }
    }

    /**
     * Returns an translator instance for passed locale.
     *
     * This method is intend to return a new translator instance for
     * locale passed to it..
     *
     * @param string $locale The locale to return translator for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_I18n_Service_Translator An instance of the locale-detector
     * @access public
     */
    public function getTranslator($locale = null)
    {
        // if no locale was passed use the active one
        if ($locale === null) {
            $locale = $this->_activeLocale;
        }

        // retrieve valid input
        $input = $this->_validateInput($locale);

        // check for redirect
        if (isset($input['redirect'])) {
            $translator = $this->getTranslator($input['redirect']);

        } else {
            $translator = new DoozR_I18n_Service_Translator($locale, self::$_config, $input['config']);

        }

        return $translator;
    }

    /**
     * Installs gettext like shortcuts _() __() ___()
     *
     * @return bool|mixed
     * @throws DoozR_I18n_Service_Exception
     */
    public function install()
    {
        $result = false;

        if (extension_loaded('gettext')) {
            throw new DoozR_I18n_Service_Exception(
                'Installation stopped! Please deinstall gettext extension if you want to use I18n service with '.
                'shortcut functionality _() | __() | ___()'
            );

        } else {
            $result = include_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/I18n/Service/Install.php';
        }

        return $result;
    }

    /*------------------------------------------------------------------------------------------------------------------
     | PHPTAL Interface fulfillment
     +----------------------------------------------------------------------------------------------------------------*/

    /**
     * PHPTAL:
     * Sets the language for translation.
     *
     * This method is intend to set the language used for translation.
     * In our I18n service its normally done by calling setActiveLocale()
     * which is instrumentalized in this mehtod.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean|string Locale which was set on success, otherwise FALSE
     * @access public
     * @see PHPTAL_TranslationService::setLanguage()
     */
    public function setLanguage()
    {
        // assume false result
        $result = false;

        // get a translator instance
        $this->_initTemplateTranslator();

        // get valid locales from arguments
        $locales = func_get_args();
        $locale  = isset($locales[0]) ? $locales[0] : null;

        if ($locale !== null) {
            $result = $this->setActiveLocale($locale);
        }

        return $result;
    }

    /**
     * PHPTAL will inform translation service what encoding page uses.
     * Output of translate() must be in this encoding.
     */
    /**
     * PHPTAL will inform translation service what encoding page uses.
     * Output of translate() must be in this encoding.
     *
     * @param string $encoding The encoding as string e.g. ...
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function setEncoding($encoding)
    {
        $this->_initTemplateTranslator();

        $this->_encoding = $encoding;

        return true;
    }

    /**
     * Returns the current active encoding
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Currently active encoding (e.g. "UTF-8") ...
     * @access public
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * Sets the domain used for translation.
     *
     * This method is intend to set the domain used for translations (if different parts of application are translated
     * in different files. This is not for language selection). In our I18n service its normally done by calling
     * setNamespace() which is instrumentalized by this method.
     *
     * @param string $domain The domain to use
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     * @see PHPTAL_TranslationService::useDomain()
     */
    public function useDomain($domain)
    {
        $this->_initTemplateTranslator();

        self::$_templateTranslator->setNamespace($domain);

        return true;
    }

    /**
     * Set the XHTML-escaped value of a variable used in translation key.
     *
     * You should use it to replace all ${key}s with values in translated strings.
     *
     * @param string $key           The name of the variable    // key
     * @param string $value_escaped XHTML markup                \\ value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     * @see PHPTAL_TranslationService::setVar()
     */
    public function setVar($key, $value_escaped)
    {
        $this->_initTemplateTranslator();

        #if ($value_escaped === true) {
            self::$_templateTranslator->{$key} = htmlentities($value_escaped);
        #} else {
        #    self::$_templateTranslator->{$key} = $value_escaped;
        #}

        return true;
    }

    /**
     * Translate a gettext key and interpolate variables.
     *
     * @param string $key        translation key, e.g. "hello ${username}!"
     * @param string $htmlescape if true, you should HTML-escape translated string. You should never HTML-escape
     *                           interpolated variables.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The translation for passed key on success, otherwise passed key
     * @access public
     * @see PHPTAL_TranslationService::translate()
     */
    public function translate($key, $htmlescape = true)
    {
        // assume value is empty
        $value = '';

        $this->_initTemplateTranslator();

        #$lookup = str_replace(' ', '_', strtolower($key));

        if ($htmlescape === true) {
            $value = self::$_templateTranslator->__($key);
        } else {
            $value = self::$_templateTranslator->_($key);
        }

        return (strlen($value) && $value !== $key) ? $value : $key;
    }

    /*------------------------------------------------------------------------------------------------------------------
     | BEGIN TOOLS + HELPER
     +----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to validate the input locale and return data
     * required for running service
     *
     * @param string $locale The locale to prepare data for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The prepared data
     * @access private
     */
    private function _validateInput($locale = null)
    {
        // check for valid locale
        if ($locale && !$this->isValidLocale($locale)) {
            throw new DoozR_I18n_Service_Exception('Invalid locale: '.$locale);
        }

        // get concrete locale
        $locale = ($locale !== null) ? $locale : $this->getClientPreferedLocale();

        $config = $this->_getL10nConfigurationByLocale($locale);

        // check for redirect of current locale (e.g. from "en-gb" -> "en")
        try {
            $redirectLocale = $config->redirect->target();
            $this->_activeLocale = $redirectLocale;

        } catch (DoozR_Config_Service_Exception $e) {
            $redirectLocale = null;

        }

        // return a valid set of locale, redirect locale and the config
        return array(
            'locale'   => $locale,
            'redirect' => $redirectLocale,
            'config'   => $config
        );
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Configuration/Management
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the Localization configuration-file (L10n) for passed locale.
     *
     * This method returns the L10n configuraton from configuration file for passed
     * locale if exist. Otherwise an DoozR_I18n_Service_Exception is thrown.
     *
     * @param string $locale The locale to return configuration for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Service_Config The instance of the configreader holding the object representation
     *                                    of L10n configuration
     * @access public
     * @throws DoozR_I18n_Service_Exception
     */
    private function _getL10nConfigurationByLocale($locale)
    {
        // check if already a config parser exist
        if (isset(self::$_configurationByLocale[$locale])) {
            $config = self::$_configurationByLocale[$locale];

        } else {
            $config = DoozR_Loader_Serviceloader::load('Config', 'Ini');
            $path   = $this->registry->path;
            $file   = $path->get(
                'app',
                'Data/Private/I18n/' . $locale . '/' . self::FILE_NAME_L10N . '.' . self::FILE_EXTENSION_L10N
            );

            $config->read($file);

            self::$_configurationByLocale[$locale] = $config;
        }

        return $config;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Public Interface/API (Tools)
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Checks for validity of a passed locale string.
     *
     * @param string $locale A locale to check for validity (e.g. "de", "de-AT", "en-us", ...)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if valid, otherwise FALSE
     * @access public
     */
    public function isValidLocale($locale = '')
    {
        return (preg_match('(^([a-zA-Z]{2})((_|-)[a-zA-Z]{2})?$)', $locale) > 0) ? true : false;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Tools & Helper
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Checks for fulfilled requirements of I18n service.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if requirements fulfilled, otherwise FALSE
     * @access private
     * @static
     * @throws DoozR_I18n_Service_Exception
     */
    private static function _checkRequirements()
    {
        // check if extension mbstring => Multibyte String is installed and usable
        if (!extension_loaded('mbstring')) {
            // Error: multibyte-string extension not installed!
            throw new DoozR_I18n_Service_Exception(
                'Error while checking requirements. "mbstring"-extension could not be found. Please install and '.
                'enable the extension in the "php.ini"'
            );
        }

        return true;
    }
}
