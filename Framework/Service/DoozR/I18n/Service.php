<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - I18n - Service
 *
 * Service.php - I18n Service for internationalization and localization support
 * for your project.
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
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Service/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/I18n/Service/Translator.php';
require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Template/Service/Lib/PHPTAL/PHPTAL/TranslationService.php';

/**
 * DoozR - I18n - Service
 *
 * I18n Service for internationalization and localization support for your project.
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_I18n
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 * @DoozRType  Singleton
 */
class DoozR_I18n_Service extends DoozR_Base_Service_Singleton implements PHPTAL_TranslationService
{
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
     * Contains instance of Service Configreader (used for reading INI-Files)
     *
     * @var DoozR_Configreader_Service
     * @access private
     * @static
     */
    private static $_configreader;

    /**
     * holds the configreader instances indexed by locale
     *
     * @var array
     * @access private
     * @static
     */
    private static $_configreaderLocales = array();

    /**
     * The default formatter of I18n module
     *
     * @var string
     * @access public
     */
    const FORMATTER_DEFAULT = 'String';

    /**
     * The translator singleton for templates
     *
     * @var DoozR_I18n_Service_Translator
     * @access private
     * @static
     */
    private static $_templateTranslator;


    /*******************************************************************************************************************
     * // TEARUP
     ******************************************************************************************************************/

    /**
     * replacement for __construct
     *
     * This method is intend as replacement for __construct
     * PLEASE DO NOT USE __construct() - make always use of __tearup()!
     *
     * @param DoozR_Config_Interface $configreader An instance of a configreader compliant config reader
     * @param string                 $locale       A valid locale (de, at-de, ...) to bind this instance to
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __tearup(DoozR_Config_Interface $configreader, $locale = null)
    {
        // make services custom autoloader ready
        self::_initAutoloader();

        // store configreader passed to this instance
        self::$_configreader = $configreader;

        // if no locale was passed then we try to read the prefered locale from client
        if (!$locale) {
            $locale = $this->getClientPreferedLocale();
        }

        // store the given locale
        $this->_activeLocale = $locale;

        // check if requirements fulfilled
        self::_checkRequirements();
    }

    /*******************************************************************************************************************
     * // BEGIN PUBLIC INTERFACES
     ******************************************************************************************************************/

    /**
     * sets the given locale as active
     *
     * This method is intend to set the given locale as active.
     *
     * @param string $locale The locale to set as active
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     * @throws DoozR_Exception_Service
     */
    public function setActiveLocale($locale)
    {
        // check if locale is valid
        if (!$this->isValidLocaleCode($locale)) {
            throw new DoozR_Exception_Service(
                'EXCEPTION_MODULE_DOOZR_I18N'
            );
        }

        // set locale
        return ($this->_activeLocale = $locale);
    }

    /**
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
            self::$_templateTranslator->setNamespace('default');
        }
    }

    /**
     * This method is intend to set the language.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     * @see PHPTAL_TranslationService::setLanguage()
     */
    public function setLanguage()
    {
        // get a translator instance
        $this->_initTemplateTranslator();

        // get valid locales from arguments
        $locale = func_get_args();

        if ($locale) {
            return $this->setActiveLocale($locale);
        }

        return false;
    }

    /**
     * This method is intend to set the language.
     *
     * @param string $encoding The encoding to set as active
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     * @see PHPTAL_TranslationService::useDomain()
     */
    public function setEncoding($encoding)
    {
        //$this->_initTemplateTranslator();
        //pre('setEncoding(): '.$encoding);
    }

    /**
     * This method is intend to set the domain.
     *
     * @param string $domain The domain to set as active
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
    }

    /**
     * This method is intend to set the domain.
     *
     * @param string $key           The key of the var
     * @param string $value_escaped The escaped value for the passed key
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     * @see PHPTAL_TranslationService::setVar()
     */
    public function setVar($key, $value_escaped)
    {
        $this->_initTemplateTranslator();

        if ($value_escaped === true) {
            self::$_templateTranslator->{$key} = htmlentities($value_escaped);
        } else {
            self::$_templateTranslator->{$key} = $value_escaped;
        }
    }

    /**
     * This method is intend to set the domain.
     *
     * @param string $key        The key to translate
     * @param string $htmlescape TRUE to escape chars, FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The translation for passed key on success, otherwise passed key
     * @access public
     * @see PHPTAL_TranslationService::translate()
     */
    public function translate($key, $htmlescape = true)
    {
        $this->_initTemplateTranslator();

        $value  = '';
        $lookup = str_replace(' ', '_', strtolower($key));

        if ($htmlescape === true) {
            $value = self::$_templateTranslator->__($lookup);
        } else {
            $value = self::$_templateTranslator->_($lookup);
        }

        return (strlen($value) && $value !== $lookup) ? $value : $key;
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
        if ($this->_activeLocale) {
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
     * This method is intend to return the instance of the locale-detector.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object The instance of the locale-detector
     * @access public
     */
    public function getDetector()
    {
        /* TODO: Di */
        // get detector-class
        return DoozR_I18n_Service_Detector::getInstance(self::$_configreader);
    }

    /**
     * This method is intend to return the instance of the locale-detector.
     *
     * @param string $type   The type of the formatter to return
     * @param string $locale The locale to use for formatter
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object The instance of the locale-detector
     * @access public
     * @throws DoozR_I18n_Service_Exception
     */
    public function getFormatter($type = self::FORMATTER_DEFAULT, $locale = null)
    {
        // retrieve valid input
        $input = $this->_validateInput($locale);

        $type = ucfirst(strtolower($type));

        if ($input['redirect']) {
            return $this->getFormatter($type, $this->getClientPreferedLocale());

        } else {
            return $this->instanciate(
                'DoozR_I18n_Service_Format_'.$type,
                array(
                    $this->registry,
                    $input['locale'],
                    null,
                    self::$_configreader,
                    $input['configreader'],
                    $this->getTranslator()
                ),
                'getInstance'
            );
        }
    }

    /**
     * This method is intend to return a new translator instance.
     *
     * @param string $locale The locale to use
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_I18n_Service_Translator The instance of the locale-detector
     * @access public
     */
    public function getTranslator($locale = null)
    {
        // retrieve valid input
        $input = $this->_validateInput($locale);

        if ($locale === null) {
            $locale = $this->_activeLocale;
        }

        if (isset($input['redirect'])) {
            $r = $this->getTranslator($input['redirect']);
        } else {
            $r = new DoozR_I18n_Service_Translator($locale, self::$_configreader, $input['configreader']);
        }

        return $r;
    }

    /**
     * This method is intend to check if all requirements are fulfilled.
     *
     * @param string $code de, de-AT, en-us ...
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if valid, otherwise FALSE
     * @access public
     */
    public function isValidLocaleCode($code = '')
    {
        return (preg_match('(^([a-zA-Z]{2})((_|-)[a-zA-Z]{2})?$)', $code) > 0) ? true : false;
    }

    /*******************************************************************************************************************
     * // BEGIN TOOLS + HELPER
     ******************************************************************************************************************/

    /**
     * This method is intend to validate the input locale and return data
     * required for running module
     *
     * @param string $locale The locale to prepare data for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The prepared data
     * @access private
     */
    private static function _initAutoloader()
    {
        if (!self::$_autoloader) {
            // register services custom autoloader
            $autoloaderService = new DoozR_Loader_Autoloader_Spl_Config();
            $autoloaderService
                ->setNamespace('DoozR_I18n')
                ->setNamespaceSeparator('_')
                ->addExtension('php')
                ->setPath(DOOZR_DOCUMENT_ROOT.'Service')
                ->setDescription('DoozR\'s Ii8n module autoloader');

            // add to SPL through facade
            self::$_autoloader = DoozR_Loader_Autoloader_Spl_Facade::attach(
                $autoloaderService
            );
        }
    }

    /**
     * This method is intend to validate the input locale and return data
     * required for running module
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
        if ($locale && !$this->isValidLocaleCode($locale)) {
            throw new DoozR_I18n_Service_Exception('Invalid locale: '.$locale);
        }

        $redirectLocale = null;

        // get concrete locale
        $locale = ($locale) ? $locale : $this->getClientPreferedLocale();

        // check if already a config parser exist
        if (isset(self::$_configreaderLocales[$locale])) {
            $configreader = self::$_configreaderLocales[$locale];

        } else {
            $configreader = DoozR_Loader_Serviceloader::load('Configreader', array('Ini'));
            $configreader->read($this->registry->path->get('app', 'Data/Private/I18n/'.$locale.'/L10n.ini'));
            self::$_configreaderLocales[$locale] = $configreader;
        }

        // check for redirect of current locale (e.g. from "en-gb" -> "en")
        try {
            $redirectLocale = $configreader->redirect->target();
            $this->_activeLocale = $redirectLocale;

        } catch (DoozR_Configreader_Service_Exception $e) {
            $redirectLocale = null;

        }

        return array(
            'locale'       => $locale,
            'redirect'     => $redirectLocale,
            'configreader' => $configreader
        );
    }

    /**
     * This method is intend to check if all requirements are fulfilled.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object Instance of this class
     * @access private
     * @throws DoozR_Exception_Service
     * @static
     */
    private static function _checkRequirements()
    {
        // check if extension mbstring => Multibyte String is installed and usable
        if (!extension_loaded('mbstring')) {
            // Error: multibyte-string extension not installed!
            throw new DoozR_Exception_Service(
                'Error while checking requirements. "mbstring"-extension could not be found.'
            );
        }

        // success everything's fine!
        return true;
    }
}

?>
