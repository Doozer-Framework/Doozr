<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - I18n - Service - Interface - Text.
 *
 * Text.php - Translation-Interface to text
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
 * Doozr - I18n - Service - Interface - Text.
 *
 * Translation-Interface to text
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
class Doozr_I18n_Service_Interface_Text extends Doozr_I18n_Service_Interface_Abstract
    implements
    Doozr_I18n_Service_Interface_Interface
{
    /**
     * Name of the directory containing the LC_MESSAGES directory.
     *
     * @example If the path to LC_MESSAGES is /var/foo/locales/en_US/Text/LC_MESSAGES then the value of
     *          TRANSLATION_FILES_DIRECTORY must be "Text" without the quotation marks.
     *
     * @var string
     */
    const TRANSLATION_FILES_DIRECTORY = 'Text';

    /**
     * Extension of translation files of this interface.
     *
     * @var string
     */
    const TRANSLATION_FILES_EXTENSION = 'mo';

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Looks for the passed looks up the translation of the given combination of values.
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
        // Cause of german umlauts and special chars in identifier we need to use crc as index
        $id = md5($string);

        // Get translation by "string" and the corresponding "key"
        $string = (isset(self::$translationTables[$uuid][$id])) ? self::$translationTables[$uuid][$id] : $string;

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
     * builds a translation-tables for giving locale and namespace.
     *
     * This method is intend to build a translation-tables for giving locale and namespace.
     *
     * @param string $locale     The locale the table get build for
     * @param array  $namespaces The namespace(s) of the table get build for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The final translationtable for given locale + namespace
     */
    protected function buildTranslationtable($locale, array $namespaces)
    {
        // The resulting array
        $result = [];

        // Assume init was done already
        $fresh = false;

        // check if locale was prepared before
        if (!isset(self::$translations[$locale])) {
            self::$translations[$locale] = [];
            $fresh                       = true;
        }

        // Iterate namespaces and parse ...
        foreach ($namespaces as $namespace) {
            // was this namespace in the current locale loaded before
            if (!$fresh && isset(self::$translations[$locale][$namespace])) {
                // we can reuse the existing
                $result = array_merge($result, self::$translations[$locale][$namespace]);
            } else {
                // Load fresh from file
                $translationFile = $this->path.$locale.DIRECTORY_SEPARATOR.self::TRANSLATION_FILES_DIRECTORY.
                                   DIRECTORY_SEPARATOR.$this->normalizeLocale($locale).DIRECTORY_SEPARATOR.
                                   'LC_MESSAGES'.DIRECTORY_SEPARATOR.
                                   $namespace.'.'.self::TRANSLATION_FILES_EXTENSION;

                $result = array_merge($result, $this->parseTranslationfile($translationFile));
            }
        }

        // return the resulting table
        return $result;
    }

    /**
     * Parses a translationfile.
     *
     * This method is intend to parse a translationfile and return the result as array.
     *
     * @param string $filename The name of the file to parse
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The content of the given file
     *
     * @throws Doozr_I18n_Service_Exception
     */
    protected function parseTranslationfile($filename)
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            throw new Doozr_I18n_Service_Exception(
                sprintf('Translationfile: "%s" does not exist or isn\'t readable.', $filename)
            );
        }

        // assume empty resulting array
        $result = [];

        // open read handle to file
        $fileHandle = fopen($filename, 'r');

        // read till end of file (eof)
        while (!feof($fileHandle)) {
            // read current line
            $line = fgets($fileHandle);

            if (!$line === false && strlen(trim($line))) {
                $parts                        = explode('=', $line);
                $result[md5(trim($parts[0]))] = trim($parts[1]);
            }
        }

        // close handle to file
        fclose($fileHandle);

        // return parsed array
        return $result;
    }
}
