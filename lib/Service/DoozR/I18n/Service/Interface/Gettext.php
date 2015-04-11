<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - I18n - Service - Interface - Gettext
 *
 * Gettext.php - Translation interface to => gettext™
 *
 * PHP versions 5.4
 *
 * LICENSE:
 * DoozR - The lightweight PHP-Framework for high-performance websites
 *
 * Copyright (c) 2005 - 2015, Benjamin Carl - All rights reserved.
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
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/I18n/Service/Interface/Abstract.php';

/**
 * DoozR - I18n - Service - Interface - Gettext
 *
 * Gettext.php - Translation interface to => gettext™
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_I18n
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_I18n_Service_Interface_Gettext extends DoozR_I18n_Service_Interface_Abstract
{
    /**
     * Path to locale files (filesystem)
     *
     * @var string
     * @access protected
     */
    protected $path;


    /*------------------------------------------------------------------------------------------------------------------
    | BEGIN PUBLIC INTERFACES
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to look-up the translation of the given combination of values.
     *
     * @param string $string    The string to translate
     * @param string $key       The key (hash) identifier for translation-table
     * @param mixed  $arguments The arguments for inserting values into translation (vsprintf) or null
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The translation of the input or the input string on failure
     * @access public
     */
    public function lookup($string, $key = null, $arguments = null)
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
     * @return array The final translationtable for given locale + namespace
     * @access protected
     */
    protected function buildTranslationtable($locale, array $namespaces)
    {
        // get real path
        $path = realpath($this->path);

        /* @todo: Does not make sense in gettext™ to iterate different namespaces?! */
        // iterate over given namespace(s) and configure environment for them
        foreach ($namespaces as $namespace) {
            $this->initI18n($locale, $this->encoding, $namespace, $path);
        }

        return false;
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
     * @return boolean|array An array with files/namespaces found for current locale, otherwise FALSE on failure
     * @access protected
     * @throws DoozR_I18n_Service_Exception
     */
    protected function initI18n($locale, $encoding, $namespace, $path)
    {
        // Assume success
        $path           .= DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . 'Gettext';
        $gettextEncoding = $this->normalizeEncoding($encoding);
        $gettextLocale   = $this->normalizeLocale($locale);

        putenv('LANG=' . $this->getLanguageByLocale($gettextLocale));

        $fullQualifiedLocale = $gettextLocale . $gettextEncoding;

        $result = setlocale(LC_ALL, $fullQualifiedLocale);
        if ($result === null || $result === false) {
            throw new DoozR_I18n_Service_Exception(
                sprintf('The locale "%s" could not be set. Sure the system (OS) supports it?', $fullQualifiedLocale)
            );
            $result = false;
        };

        bind_textdomain_codeset($namespace, 'UTF-8');
        bindtextdomain($namespace, $path);
        textdomain($namespace);

        return $result;
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
     * @return string The normalized encoding
     * @access protected
     */
    protected function normalizeEncoding($encoding)
    {
        if ($encoding === null) {
            $encoding = '';
        }

        if ($encoding === '') {
            return $encoding;
        }

        return '.' . strtolower(str_replace('-', '', $encoding));
    }

    /**
     * Normalizes a locale to a full qualified locale. For example: If u would pass "de" into it everybody
     * would know - a yeah it's "de_DE" and exactly this is done here.
     *
     * @param string $locale The locale to normalize
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The normalized locale
     * @access protected
     */
    protected function normalizeLocale($locale)
    {
        $locale = explode('-', $locale);

        foreach ($locale as $key => &$part) {
            if ($key > 0) {
                $part = strtoupper($part);
            }
        }

        return implode('_', $locale);
    }

    /**
     * Returns the language from a passed locale.
     *
     * @param string $locale The locale to return language from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The extracted locale
     * @access protected
     */
    protected function getLanguageByLocale($locale)
    {
        $language = explode('_', $locale);
        return $language[0];
    }

    /**
     * Checks the requirements of this translator interface
     *
     * This method is intend to check if all requirements are fulfilled.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access protected
     * @static
     * @throws DoozR_I18n_Service_Exception
     */
    protected static function checkRequirements()
    {
        // test if gettext extension is installed with php
        if (extension_loaded('gettext') !== true) {
            throw new DoozR_I18n_Service_Exception(
                'Error while checking requirements: gettext™ extension not loaded.'
            );
        }

        return true;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | MAIN CONTROL METHODS (CONSTRUCTOR AND INIT)
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param array $config The config for this type of interface
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_I18n_Service_Interface_Gettext Instance of this class
     * @access protected
     */
    protected function __construct(array $config)
    {
        // store the path to
        $this->path = $config['path'];

        // check if requirements fulfilled
        self::checkRequirements();

        // call parents constructor
        parent::__construct($config);
    }
}
