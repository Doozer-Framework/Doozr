<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - I18n - Service
 *
 * Service.php - I18n Service for internationalization and localization.
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
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Service/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT.'Service/Doozr/I18n/Service/Detector.php';
require_once DOOZR_DOCUMENT_ROOT.'Service/Doozr/I18n/Service/Translator.php';
require_once DOOZR_DOCUMENT_ROOT.'Service/Doozr/I18n/Service/Interface.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Service/Interface.php';

use Doozr\Loader\Serviceloader\Annotation\Inject;

/**
 * Doozr - I18n - Service
 *
 * I18n Service for internationalization and localization.
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
 * @Inject(
 *     link   = "doozr.registry",
 *     type   = "constructor",
 *     target = "getInstance"
 * )
 */
class Doozr_I18n_Service extends Doozr_Base_Service_Singleton
    implements
    Doozr_Base_Service_Interface,
    Doozr_I18n_Service_Interface
{
    /**
     * The encoding.
     *
     * @var string
     */
    protected $encoding;

    /**
     * The variables for I18n.
     *
     * @var array
     */
    protected $variables = [];

    /**
     * Contains the current active locale.
     *
     * @var mixed
     */
    protected $locale;

    /**
     * Contains instance of Service Config (used for reading INI-Files).
     *
     * @var Doozr_Configuration
     * @static
     */
    protected static $config;

    /**
     * holds the config instances indexed by locale.
     *
     * @var array
     * @static
     */
    protected static $configurationByLocale = [];

    /**
     * The translator singleton for templates.
     *
     * @var Doozr_I18n_Service_Translator
     * @static
     */
    protected static $templateTranslator;

    /**
     * The name of the L10n file.
     *
     * @var string
     */
    const FILE_NAME_L10N = 'L10n';

    /**
     * The extension of the config file.
     *
     * @var string
     */
    const FILE_EXTENSION_L10N = 'ini';

    /**
     * Common known character encodings.
     *
     * @var string
     */
    const ENCODING_UTF_1       = 'UTF-1';
    const ENCODING_UTF_5       = 'UTF-5';
    const ENCODING_UTF_6       = 'UTF-6';
    const ENCODING_UTF_7       = 'UTF-7';
    const ENCODING_UTF_8       = 'UTF-8';
    const ENCODING_UTF_9       = 'UTF-9';
    const ENCODING_UTF_16      = 'UTF-16';
    const ENCODING_UTF_18      = 'UTF-18';
    const ENCODING_UTF_32      = 'UTF-32';
    const ENCODING_ISO_8859_1  = 'ISO-8859-1';       // west european
    const ENCODING_ISO_8859_2  = 'ISO-8859-2';       // middle european
    const ENCODING_ISO_8859_3  = 'ISO-8859-3';       // south european
    const ENCODING_ISO_8859_4  = 'ISO-8859-4';       // north european
    const ENCODING_ISO_8859_5  = 'ISO-8859-5';       // cyrillic
    const ENCODING_ISO_8859_6  = 'ISO-8859-6';       // arabic
    const ENCODING_ISO_8859_7  = 'ISO-8859-7';       // greece
    const ENCODING_ISO_8859_8  = 'ISO-8859-8';       // hebrew
    const ENCODING_ISO_8859_9  = 'ISO-8859-9';       // turkish
    const ENCODING_ISO_8859_10 = 'ISO-8859-10';     // nordic
    const ENCODING_ISO_8859_11 = 'ISO-8859-11';     // thai
    const ENCODING_ISO_8859_13 = 'ISO-8859-13';     // baltic
    const ENCODING_ISO_8859_14 = 'ISO-8859-14';     // celtic
    const ENCODING_ISO_8859_15 = 'ISO-8859-15';     // west european
    const ENCODING_ISO_8859_16 = 'ISO-8859-16';     // south european

    /**
     * Different types of localizer existing in project.
     *
     * @var string
     */
    const LOCALIZER_CURRENCY = 'CURRENCY';
    const LOCALIZER_DATETIME = 'DATETIME';
    const LOCALIZER_MEASURE  = 'MEASURE';
    const LOCALIZER_NUMBER   = 'NUMBER';
    const LOCALIZER_STRING   = 'STRING';

    /**
     * Default formatter of I18n service.
     *
     * @var string
     */
    const LOCALIZER_DEFAULT = self::LOCALIZER_STRING;

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Service entry point.
     *
     * This method is a replacement for __construct
     * PLEASE DO NOT USE __construct() - make always use of __tearup()!
     *
     * @param string|null $locale   A locale (de, at-de, ...) OR NULL to use autodetection
     * @param string|null $encoding An optional encoding used for setting/translating in locale/translator
     *                              also used when setting locale in gettext interface (e.g. en_US.UTF-8)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success
     */
    public function __tearup($locale = null, $encoding = self::ENCODING_UTF_8)
    {
        // Check if requirements fulfilled
        self::checkRequirements();

        // Store config passed to this instance
        self::$config = self::getRegistry()->getConfiguration();

        // If no locale was passed then we try to read the preferred locale from client
        if (null === $locale) {
            $locale = $this->getClientPreferredLocale();
        }

        $this->setLocale($locale);
        $this->setEncoding($encoding);

        // Must return true
        return true;
    }

    /*------------------------------------------------------------------------------------------------------------------
     | PUBLIC API
     +----------------------------------------------------------------------------------------------------------------*/

    /**
     * Checks for validity of a passed locale string.
     *
     * @param string $locale A locale to check for validity (e.g. "de", "de-AT", "en-us", ...)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if valid, otherwise FALSE
     */
    public function isValidLocale($locale = '')
    {
        return (preg_match('(^([a-zA-Z]{2})((_|-)[a-zA-Z]{2})?$)', $locale) > 0) ? true : false;
    }

    /**
     * Returns the available locales defined in config.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array An array containing the available locales with numerical index
     */
    public function getAvailableLocales()
    {
        return object_to_array(self::$config->i18n->default->available);
    }

    /**
     * Setter for available locales.
     *
     * @param array $locales Locales to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setAvailableLocales(array $locales)
    {
        self::$config->i18n->default->available = array_to_object($locales);
    }

    /**
     * Setter for active locale.
     *
     * @param string $locale The locale to set as active
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     *
     * @throws Doozr_I18n_Service_Exception
     */
    public function setLocale($locale)
    {
        $result = true;

        // Check if locale is valid
        if (!$this->isValidLocale($locale)) {
            throw new Doozr_I18n_Service_Exception(
                sprintf('The locale "%s" is not valid.', $locale)
            );
        }

        $this->locale = $locale;

        return $result;
    }

    /**
     * Returns the currently active locale.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string|null The active locale if set, otherwise NULL
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * This method is intend to return the current active locale.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The active locale
     */
    public function getClientPreferredLocale()
    {
        if (null !== $this->locale) {
            $locale = $this->locale;

        } else {
            // Get detector
            $detector = $this->getDetector();

            // Detect user's preferred locale
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
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_I18n_Service_Detector Instance of the locale detector
     */
    public function getDetector()
    {
        return Doozr_I18n_Service_Detector::getInstance(self::$config, self::getRegistry());
    }

    /**
     * This method is intend to return the instance of the locale-detector.
     *
     * @param string $type   The type of the formatter to return
     * @param string $locale The locale to use for formatter
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_I18n_Service_Localize_Abstract The instance of the locale-detector
     *
     * @throws Doozr_I18n_Service_Exception
     */
    public function getLocalizer($type = self::LOCALIZER_DEFAULT, $locale = null)
    {
        // If no locale was passed use the active one
        if ($locale === null) {
            $locale = $this->locale;
        }

        // Retrieve valid input
        $input = $this->validateInput($locale);

        // Convert type to required format
        $type = ucfirst(strtolower($type));

        // Check for redirect -> !
        if (true === isset($input['redirect']) && null !== $input['redirect']) {
            return $this->getLocalizer($type, $input['redirect']);
        } else {
            return self::instantiate(
                'Doozr_I18n_Service_Localize_'.$type,
                [
                    self::getRegistry(),
                    $input['locale'],
                    null,
                    self::$config,
                    $input['config'],
                    $this->getTranslator($input['locale']),
                ]
            );
        }
    }

    /**
     * Returns an translator instance for passed locale.
     *
     * @param string $locale   The locale to return translator for
     * @param string $encoding The encoding of the translation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_I18n_Service_Translator An instance of the locale-detector
     *
     * @throws Doozr_I18n_Service_Exception
     */
    public function getTranslator($locale = null, $encoding = null)
    {
        // if no locale was passed use the active one
        if ($locale === null) {
            $locale = $this->getLocale();
        }

        // Check if locale is available on system
        $availableLocales = $this->getAvailableLocales();

        if (false === in_array($locale, $availableLocales)) {
            throw new Doozr_I18n_Service_Exception(
                sprintf('The locale "%s" is not available by configuration.', $locale)
            );
        }

        // if no encoding was passed use the active one
        if ($encoding === null) {
            $encoding = $this->getEncoding();
        }

        // retrieve valid input
        $input = $this->validateInput($locale);

        // check for redirect
        if (isset($input['redirect'])) {
            $translator = $this->getTranslator($input['redirect'], $encoding);
        } else {
            $translator = new Doozr_I18n_Service_Translator($locale, $encoding, self::$config, $input['config']);
        }

        return $translator;
    }

    /*------------------------------------------------------------------------------------------------------------------
     | FULFILL Doozr_I18n_Service_Interface
     +----------------------------------------------------------------------------------------------------------------*/

    /**
     * PHPTAL:
     * Sets the language for translation.
     *
     * In our I18n service its normally done by calling setLocale()
     * which is instrumentalized in this method.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool|string Locale which was set on success, otherwise FALSE
     *
     * @see PHPTAL_TranslationService::setLanguage()
     */
    public function setLanguage()
    {
        // assume false result
        $result = false;

        // get a translator instance
        $this->initTemplateTranslator();

        // get valid locales from arguments
        $locales = func_get_args();
        $locale  = isset($locales[0]) ? $locales[0] : null;

        if ($locale !== null) {
            $result = $this->setLocale($locale);
        }

        return $result;
    }

    /**
     * PHPTAL:
     * Will inform translation service what encoding page uses.
     * Output of translate() must be in this encoding.
     *
     * @param string $encoding The encoding as string e.g. ...
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;

        $this->initTemplateTranslator();

        return true;
    }

    /**
     * Returns the current active encoding.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string Currently active encoding (e.g. "UTF-8") ...
     */
    public function getEncoding()
    {
        return $this->encoding;
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
     *
     * @return string The domain as array capsuled
     *
     * @see PHPTAL_TranslationService::useDomain()
     */
    public function useDomain($domain)
    {
        $this->initTemplateTranslator();

        self::$templateTranslator->setNamespace($domain);

        return [$domain];
    }

    public function addDomain($domain)
    {
        $this->initTemplateTranslator();

        self::$templateTranslator->addNamespace($domain);

        return [$domain];
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
     *
     * @return bool TRUE on success, otherwise FALSE
     *
     * @see PHPTAL_TranslationService::setVar()
     */
    public function setVar($key, $value_escaped)
    {
        $this->initTemplateTranslator();

        $this->setVariable($key, $value_escaped);

        return true;
    }

    /**
     * Setter for variable.
     *
     * @param string $key   The key/name of the variable
     * @param string $value The value of the variable
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setVariable($key, $value)
    {
        $this->variables[$key] = $value;
    }

    /**
     * Fluent: Setter for variable.
     *
     * @param string $key   The key/name of the variable
     * @param string $value The value of the variable
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function variable($key, $value)
    {
        $this->setVariable($key, $value);

        return $this;
    }

    /**
     * Getter for variable.
     *
     * @param string $key The key to return.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string|null The value of the variable, NULL
     */
    public function getVariable($key)
    {
        return $this->variables[$key];
    }

    /**
     * Returns all defined variables.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array Name of defined variables
     */
    public function getDefinedVariables()
    {
        return array_keys($this->variables);
    }

    /**
     * Getter for variables.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array Name of defined variables
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Magic: Setter for variable.
     *
     * @param string $key   The key/name of the variable
     * @param string $value The value of the variable
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array Name of defined variables
     */
    public function __set($key, $value)
    {
        $this->setVariable($key, $value);
    }

    /**
     * Replaces a string with another one in passed string.
     *
     * @param string $pattern The pattern to be used for detection
     * @param string $string  String to work with
     * @param string $replace Replacement string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The resulting string
     */
    public function replaceVariables($pattern, $string, $replace)
    {
        $count = preg_match($pattern, $string, $result);

        for ($i = 0; $i < $count; $i += 2) {
            $string = str_replace($result[$i], $this->getVariable($result[$i + 1]), $string);
        }

        return $string;
    }

    /**
     * Translate a key (e.g. gettext) and interpolate variables.
     *
     * @param string     $key        Translation key, e.g. "hello ${username}!" or a simple one "hello_message_user"
     * @param bool       $htmlEscape TRUE to HTML-escape translated string. Never HTML-escape interpolated variables
     * @param array|null $arguments  Arguments to pass with translation.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The translation for passed key on success, otherwise passed key
     *
     * @see PHPTAL_TranslationService::translate()
     */
    public function translate($key, $htmlEscape = true, array $arguments = null)
    {
        // Init the translator instance first. We need to call this stupid on each
        $this->initTemplateTranslator();

        /*
         * @todo Bugfix : here everything gets encoded and this return trash or whatsoever
         */
        if (true === $htmlEscape) {
            $value = self::$templateTranslator->__($key, $arguments);
        } else {
            $value = self::$templateTranslator->_($key, $arguments);
        }

        // Replace the contained placeholder keys with the content of these variables:
        $value = $this->replaceVariables(
            '/\${(.*[A-Za-z0-9])}/',
            $value,
            $this->getVariables()
        );

        return (strlen($value) && $value !== $key) ? $value : $key;
    }

    /*------------------------------------------------------------------------------------------------------------------
     | TOOLS + HELPER
     +----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to validation the input locale and return data
     * required for running service.
     *
     * @param string $locale The locale to prepare data for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The prepared data
     *
     * @throws Doozr_I18n_Service_Exception
     */
    protected function validateInput($locale = null)
    {
        // check for valid locale
        if ($locale && !$this->isValidLocale($locale)) {
            throw new Doozr_I18n_Service_Exception(
                sprintf('Invalid locale: %s', $locale)
            );
        }

        // get concrete locale
        $locale = ($locale !== null) ? $locale : $this->getClientPreferredLocale();

        $config = $this->getL10nConfigurationByLocale($locale);

        // check for redirect of current locale (e.g. from "en-gb" -> "en")
        try {
            $redirectLocale = $config->redirect->target;
            $this->locale   = $redirectLocale;
        } catch (Whoops\Exception\ErrorException $e) {
            $redirectLocale = null;
        } catch (Exception $e) {
            $redirectLocale = null;
        }

        // return a valid set of locale, redirect locale and the config
        return [
            'locale'   => $locale,
            'redirect' => $redirectLocale,
            'config'   => $config,
        ];
    }

    /**
     * Initializes the template translator.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     */
    protected function initTemplateTranslator()
    {
        if (!self::$templateTranslator) {
            self::$templateTranslator = $this->getTranslator();
        }
    }

    /**
     * Checks for fulfilled requirements of I18n service.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if requirements fulfilled, otherwise FALSE
     * @static
     *
     * @throws Doozr_I18n_Service_Exception
     */
    protected static function checkRequirements()
    {
        // check if extension mbstring => Multibyte String is installed and usable
        if (!extension_loaded('mbstring')) {
            // Error: multibyte-string extension not installed!
            throw new Doozr_I18n_Service_Exception(
                'Error while checking requirements. "mbstring"-extension could not be found. Please install and '.
                'enable the extension in the "php.ini"'
            );
        }

        return true;
    }

    /**
     * Returns the Localization configuration-file (L10n) for passed locale.
     *
     * This method returns the L10n configuration from configuration file for passed
     * locale if exist. Otherwise an Doozr_I18n_Service_Exception is thrown.
     *
     * @param string $locale The locale to return configuration for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return object The instance of the configreader holding the object representation of L10n configuration
     *
     * @throws Doozr_I18n_Service_Exception
     */
    protected function getL10nConfigurationByLocale($locale)
    {
        // Check if already a config parser exist
        if (true === isset(self::$configurationByLocale[$locale])) {
            $config = self::$configurationByLocale[$locale];
        } else {
            $filename = self::getRegistry()->getPath()->get(
                'app',
                'Data/Private/I18n/'.$locale.'/'.self::FILE_NAME_L10N.'.'.self::FILE_EXTENSION_L10N
            );

            // Read
            $config = $this->getConfigurationReader();
            $config->read($filename);

            self::$configurationByLocale[$locale] = $config;
        }

        return $config;
    }

    /**
     * Returns an instance of Doozr's internal Config-Reader for reading INI-Configurations.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Configuration_Reader_Ini
     *
     * @throws Doozr_Di_Exception
     */
    protected function getConfigurationReader()
    {
        // Add required dependencies
        self::$registry->getContainer()->getMap()->wire(
            [
                'doozr.filesystem.service' => Doozr_Loader_Serviceloader::load('filesystem'),
                'doozr.cache.service'      => Doozr_Loader_Serviceloader::load(
                    'cache',
                    DOOZR_CACHING_CONTAINER,
                    DOOZR_NAMESPACE_FLAT.'.cache.i18n',
                    [],
                    DOOZR_UNIX,
                    DOOZR_CACHING
                ),
            ]
        );

        /* @var Doozr_Configuration_Reader_Ini $config */
        $config = self::$registry->getContainer()->build(
            'doozr.configuration.reader.ini',
            [
                DOOZR_CACHING,
            ]
        );

        return $config;
    }
}
