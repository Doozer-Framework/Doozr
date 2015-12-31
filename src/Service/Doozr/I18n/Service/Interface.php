<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - I18n - Service.
 *
 * Interface.php - This interface is a contract for dependencies
 * outside e.g.
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

/**
 * Doozr - I18n - Service.
 *
 * Interface.php - This interface is a contract for dependencies
 * outside e.g.
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
interface Doozr_I18n_Service_Interface extends PHPTAL_TranslationService
{
    /*------------------------------------------------------------------------------------------------------------------
     | PUBLIC API
     +----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the available locales defined in config.
     *
     * @return array An array containing the available locales with numerical index
     */
    public function getAvailableLocales();

    /**
     * Setter for available locales.
     *
     * @param array $locales Locales to set
     */
    public function setAvailableLocales(array $locales);

    /**
     * Setter for active locale.
     *
     * @param string $locale The locale to set as active
     *
     * @return bool TRUE on success, otherwise FALSE
     *
     * @throws Doozr_I18n_Service_Exception
     */
    public function setActiveLocale($locale);

    /**
     * Returns the currently active locale.
     *
     * @return string|null The active locale if set, otherwise NULL
     */
    public function getActiveLocale();

    /**
     * This method is intend to return the current active locale.
     *
     * @return string The active locale
     */
    public function getClientPreferredLocale();

    /**
     * Returns an instance of the locale detector.
     *
     * @return Doozr_I18n_Service_Detector Instance of the locale detector
     */
    public function getDetector();

    /**
     * This method is intend to return the instance of the locale-detector.
     *
     * @param string $type   The type of the formatter to return
     * @param string $locale The locale to use for formatter
     *
     * @return Doozr_I18n_Service_Localize_Abstract The instance of the locale-detector
     *
     * @throws Doozr_I18n_Service_Exception
     */
    public function getLocalizer($type = self::LOCALIZER_DEFAULT, $locale = null);

    /**
     * Returns an translator instance for passed locale.
     *
     * @param string $locale   The locale to return translator for
     * @param string $encoding The encoding of the translation
     *
     * @return Doozr_I18n_Service_Translator An instance of the locale-detector
     *
     * @throws Doozr_I18n_Service_Exception
     */
    public function getTranslator($locale = null, $encoding = null);

    /**
     * Returns the current active encoding.
     *
     * @return string Currently active encoding (e.g. "UTF-8") ...
     */
    public function getEncoding();

    /**
     * Setter for variable.
     *
     * @param string $key   The key/name of the variable
     * @param string $value The value of the variable
     */
    public function setVariable($key, $value);

    /**
     * Fluent: Setter for variable.
     *
     * @param string $key   The key/name of the variable
     * @param string $value The value of the variable
     *
     * @return $this Instance for chaining
     */
    public function variable($key, $value);

    /**
     * Getter for variable.
     *
     * @return string|null The value of the variable, NULL
     */
    public function getVariable($key);

    /**
     * Returns all defined variables.
     *
     * @return array Name of defined variables
     */
    public function getDefinedVariables();

    /**
     * Getter for variables.
     *
     * @return array Name of defined variables
     */
    public function getVariables();

    /**
     * Magic: Setter for variable.
     *
     * @param string $key   The key/name of the variable
     * @param string $value The value of the variable
     *
     * @return array Name of defined variables
     */
    public function __set($key, $value);

    /**
     * Replaces a string with another one in passed string.
     *
     * @param string $pattern The pattern to be used for detection
     * @param string $string  String to work with
     * @param string $replace Replacement string
     *
     * @return string The resulting string
     */
    public function replaceVariables($pattern, $string, $replace);

    /*------------------------------------------------------------------------------------------------------------------
    | Public Interface/API (Tools)
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Checks for validity of a passed locale string.
     *
     * @param string $locale A locale to check for validity (e.g. "de", "de-AT", "en-us", ...)
     *
     * @return bool TRUE if valid, otherwise FALSE
     */
    public function isValidLocale($locale = '');
}
