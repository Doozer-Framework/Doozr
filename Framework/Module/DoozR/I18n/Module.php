<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Module I18n
 *
 * Module.php - I18n Module for internationalization and localization support
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
 * @package    DoozR_Module
 * @subpackage DoozR_Module_I18n
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Module/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT.'Module/DoozR/I18n/Module/Translator.php';

/**
 * DoozR Module I18n
 *
 * I18n Module for internationalization and localization support for your project.
 *
 * @category   DoozR
 * @package    DoozR_Module
 * @subpackage DoozR_Module_I18n
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 * @DoozRType  Singleton
 */
class DoozR_I18n_Module extends DoozR_Base_Module_Singleton
{
    /**
     * Contains the current active locale
     *
     * @var mixed
     * @access private
     */
    private $_activeLocale;

    /**
     * Contains instance of Module Configreader (used for reading INI-Files)
     *
     * @var DoozR_Configreader_Module
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


    const FORMATTER_DEFAULT = 'String';


    /*******************************************************************************************************************
     * // TEARUP
     ******************************************************************************************************************/

    /**
     * replacement for __construct
     *
     * This method is intend as replacement for __construct
     * PLEASE DO NOT USE __construct() - make always use of __tearup()!
     *
     * @param string                 $locale       A valid locale (de, at-de, ...) to bind this instance to
     * @param DoozR_Config_Interface $configreader An instance of a configreader compliant config reader
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function __tearup($locale, DoozR_Config_Interface $configreader)
    {
        // store configreader
        self::$_configreader = $configreader;

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
     * @return  boolean TRUE on success, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @throws  DoozR_Exception_Module
     */
    public function setActiveLocale($locale)
    {
        // check if locale is valid
        if (!$this->isValidLocaleCode($locale)) {
            throw new DoozR_Exception_Module(
                'EXCEPTION_MODULE_DOOZR_I18N'
            );
        }

        // set locale
        return ($this->_activeLocale = $locale);
    }

    /**
     * returns the current active locale
     *
     * This method is intend to return the current active locale.
     *
     * @return  string The active locale
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getActiveLocale()
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
     * returns the detector
     *
     * This method is intend to return the instance of the locale-detector.
     *
     * @return  object The instance of the locale-detector
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getDetector()
    {
        /* TODO: Di */
        // get detector-class
        return DoozR_I18n_Module_Detector::getInstance(self::$_configreader);
    }

    /**
     * returns the detector
     *
     * This method is intend to return the instance of the locale-detector.
     *
     * @param string $type   The type of the formatter to return
     * @param string $locale The locale to use for formatter
     *
     * @return  object The instance of the locale-detector
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @throws  DoozR_I18n_Module_Exception
     */
    public function getFormatter($type = self::FORMATTER_DEFAULT, $locale = null)
    {
        // retrieve valid input
        $input = $this->_validateInput($locale);

        if ($input['redirect']) {
            return $this->getFormatter($type, $this->getActiveLocale());

        } else {
            return $this->instanciate(
                'DoozR_I18n_Module_Format_'.$type,
                array(
                    $this->registry,
                    $input['locale'],
                    null,
                    self::$_configreader,
                    $input['configreader'],
                    null
                ),
                'getInstance'
            );
        }
    }

    /**
     * returns a new translator instance
     *
     * This method is intend to return a new translator instance.
     *
     * @param string $locale The locale to use
     *
     * @return  DoozR_I18n_Module_Translator The instance of the locale-detector
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getTranslator($locale = null)
    {
        // retrieve valid input
        $input = $this->_validateInput($locale);

        if ($locale === null) {
            $locale = $this->_activeLocale;
        }

        if ($input['redirect']) {
            return $this->getTranslator($locale);

        } else {
            return new DoozR_I18n_Module_Translator($locale, self::$_configreader, $input['configreader']);
        }
    }

    /**
     * checks if a given locale-code is valid
     *
     * This method is intend to check if all requirements are fulfilled.
     *
     * @param string $code de, de-AT, en-us ...
     *
     * @return  boolean TRUE if valid, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function isValidLocaleCode($code = '')
    {
        return (preg_match('(^([a-zA-Z]{2})((_|-)[a-zA-Z]{2})?$)', $code) > 0) ? true : false;
    }

    /*******************************************************************************************************************
     * // BEGIN TOOLS + HELPER
     ******************************************************************************************************************/

    /**
     * validates input locale and return data required for running module
     *
     * This method is intend to validate the input locale and return data
     * required for running module
     *
     * @param string $locale The locale to prepare data for
     *
     * @return  array The prepared data
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _validateInput($locale = null)
    {
        // check for valid locale
        if ($locale && !$this->isValidLocaleCode($locale)) {
            throw new DoozR_I18n_Module_Exception('Invalid locale: '.$locale);
        }

        $redirectLocale = null;

        // get concrete locale
        $locale = ($locale) ? $locale : $this->getActiveLocale();

        // check if already a config parser exist
        if (isset(self::$_configreaderLocales[$locale])) {
            $configreader = self::$_configreaderLocales[$locale];

        } else {
            $configreader = DoozR_Loader_Moduleloader::load('Configreader', array('Ini'));
            $configreader->read($this->registry->path->get('app', 'Data/Private/I18n/'.$locale.'/L10n.ini'));
            self::$_configreaderLocales[$locale] = $configreader;
        }

        // check for redirect of current locale (e.g. from "en-gb" -> "en")
        try {
            $redirectLocale = $configreader->redirect->target();
            $this->_activeLocale = $redirectLocale;

        } catch (DoozR_Configreader_Module_Exception $e) {
            $redirectLocale = null;

        }

        return array(
            'locale'       => $locale,
            'redirect'     => $redirectLocale,
            'configreader' => $configreader
        );
    }

    /**
     * checks if all requirements are fulfilled
     *
     * This method is intend to check if all requirements are fulfilled.
     *
     * @return  object Instance of this class
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @throws  DoozR_Exception_Module
     * @static
     */
    private static function _checkRequirements()
    {
        // check if extension mbstring => Multibyte String is installed and usable
        if (!extension_loaded('mbstring')) {
            // Error: multibyte-string extension not installed!
            throw new DoozR_Exception_Module(
                'Error while checking requirements. "mbstring"-extension could not be found.'
            );
        }

        // success everything's fine!
        return true;
    }
}

?>
