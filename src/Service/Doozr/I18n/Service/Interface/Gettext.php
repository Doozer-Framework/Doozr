<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - I18n - Service - Interface - Gettext
 *
 * Gettext.php - Translation interface to => gettext™
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
require_once DOOZR_DOCUMENT_ROOT.'Service/Doozr/I18n/Service/Interface/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT.'Service/Doozr/I18n/Service/Interface/Interface.php';

/**
 * Doozr - I18n - Service - Interface - Gettext
 *
 * Gettext.php - Translation interface to => gettext™
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
class Doozr_I18n_Service_Interface_Gettext extends Doozr_I18n_Service_Interface_Abstract
    implements
    Doozr_I18n_Service_Interface_Interface
{
    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to look-up the translation of the given combination of values.
     *
     * @param string $string    The string to translate
     * @param string $uuid      The uuid of the translation-table
     * @param mixed  $arguments The arguments for inserting values into translation (vsprintf) or null
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The translation of the input or the input string on failure
     */
    public function lookup($string, $uuid = null, $arguments = null)
    {
        if (is_array($string) === true) {
            $translate = 'FAIL';
        } else {
            $translate = $string;
        }

        // Get translation by "string"
        $string = gettext($translate);

        if ($arguments !== null) {
            $string = vsprintf($string, $arguments);
        }

        // return the result
        return $string;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | TOOLS + HELPER
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to build a translation-tables for giving locale and namespace.
     *
     * @param string $locale     The locale the table get build for
     * @param array  $namespaces The namespace(s) of the table get build for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array Key value based (key[locale] => value[os/system locale])
     */
    protected function buildTranslationtable($locale, array $namespaces)
    {
        // It simply does not make sense in gettext™ to iterate different namespaces! We use the first one
        $namespace = $namespaces[0];

        // Wrap result into array by locale indexed
        return [
            $locale => $this->initI18n(
                $locale,
                $this->getEncoding(),
                $namespace,
                $this->getPath()
            ),
        ];
    }

    /**
     * Initializes gettext™ environment.
     *
     * This method is intend to initialize the gettext™ environment and is responsible
     * for setting all required environment variables and path' to make gettext™ run.
     *
     * @param string $locale    A valid locale e.g "de", "de_DE", "ru_RU" and so on
     * @param string $encoding  A valid encoding like UTF-8, ISO-8859-1 or UTF-16 ...
     * @param string $namespace A valid namespace - often called the domain (name of the *.mo file)
     * @param string $path      A valid path to the *.mo files to make them known to gettext™
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool|array An array with files/namespaces found for current locale, otherwise FALSE on failure
     *
     * @throws Doozr_I18n_Service_Exception
     */
    protected function initI18n($locale, $encoding, $namespace, $path)
    {
        // Make it possible to build a valid locale like: en_US.utf8
        $path           .= $locale.DIRECTORY_SEPARATOR.'Gettext';
        $gettextLocale   = $this->normalizeLocale($locale);                                     // e.g. en-us => en_US
        $gettextLanguage = $this->getLanguageByLocale($gettextLocale);                          // e.g. en_US => en
        $gettextEncoding = $this->normalizeEncoding($encoding);                                 // e.g. UTF-8 => utf8

        // Setup environment variables mainly required by gettext™
        putenv('LANG='.$gettextLanguage);
        putenv('LC_ALL='.$gettextLocale);
        putenv('LC_MESSAGES='.$gettextLocale);

        // We provide the system/OS a prioritized variety of dialects to choose from - for the locale built above
        $fullQualifiedLocales = [
            $gettextLocale.$gettextEncoding,
            $gettextLocale.'.'.$encoding,
            $gettextLocale,
        ];

        $result = setlocale(LC_ALL, $fullQualifiedLocales);

        if ($result === null || $result === false) {
            $locale = var_export($fullQualifiedLocales, true);
            throw new Doozr_I18n_Service_Exception(
                sprintf('The locale "%s" could not be set. Sure the system (OS) supports it?', $locale)
            );
        };

        bind_textdomain_codeset($namespace, $encoding);
        bindtextdomain($namespace, $path);
        textdomain($namespace);
    }

    /**
     * Returns a normalized encoding.
     *
     * @example Will convert an encoding string like "UTF-8" to ".utf8" OR "ISO-8859-1" to ".iso88591" so the locales
     *          can be set correctly.
     *
     * @param string $encoding The encoding to be normalized
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The normalized encoding
     */
    protected function normalizeEncoding($encoding)
    {
        if ($encoding === null) {
            $encoding = '';
        }

        if ($encoding === '') {
            return $encoding;
        }

        return '.'.strtolower(str_replace('-', '', $encoding));
    }

    /**
     * Returns the language from a passed locale.
     *
     * @param string $locale The locale to return language from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The extracted locale
     */
    protected function getLanguageByLocale($locale)
    {
        $language = explode('_', $locale);

        return $language[0];
    }

    /**
     * Checks the requirements of this translator interface.
     * For example it checks if a required extension is loaded or not and so on.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     * @static
     *
     * @throws Doozr_I18n_Service_Exception
     */
    protected static function checkRequirements()
    {
        // Test if gettext™ extension is installed with php
        if (true !== extension_loaded('gettext')) {
            throw new Doozr_I18n_Service_Exception(
                'Error while checking requirements: gettext™ extension not loaded.'
            );
        }

        return true;
    }
}
