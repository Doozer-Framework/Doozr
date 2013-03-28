<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - I18n - Module - Interface - Text
 *
 * Text.php - Translation-Interface to text
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

require_once DOOZR_DOCUMENT_ROOT.'Module/DoozR/I18n/Module/Base/Interface.php';

/**
 * DoozR Module I18n
 *
 * Translation-Interface
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
class DoozR_I18n_Module_Interface_Text extends DoozR_I18n_Module_Base_Interface
{
    /**
     * path to locale files (filesystem)
     * only used in Text and Gettext mode
     *
     * @var string
     * @access private
     */
    private $_path;


    /*******************************************************************************************************************
     * // BEGIN PUBLIC INTERFACES
     ******************************************************************************************************************/

    /**
     * looks up the translation of the given combination of values
     *
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
    public function lookup($string, $key, $arguments = null)
    {
        // cause of german umlauts and special chars in identifier we need to use crc as index
        $id = md5($string);

        // get translation by "string" and the corresponding "key"
        $string = (isset(self::$translationTables[$key][$id])) ? self::$translationTables[$key][$id] : $string;

        if ($arguments !== null) {
            $string = vsprintf($string, $arguments);
        }

        // return the result
        return $string;
    }

    /*******************************************************************************************************************
     * \\ END PUBLIC INTERFACES
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN TOOLS + HELPER
     ******************************************************************************************************************/

    /**
     * builds a translation-tables for giving locale and namespace
     *
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
        // the resulting array
        $result = array();

        // assume init was done already
        $fresh = false;

        // check if locale was prepared before
        if (!isset(self::$translations[$locale])) {
            self::$translations[$locale] = array();
            $fresh = true;
        }

        // iterate over given namespace(s) and parse them
        foreach ($namespaces as $namespace) {
            // was this namespace in the current locale loaded before
            if (!$fresh && isset(self::$translations[$locale][$namespace])) {
                // we can reuse the exisiting
                $result = array_merge($result, self::$translations[$locale][$namespace]);
            } else {
                // load fresh from file
                $translationFile = $this->_path.$locale.DIRECTORY_SEPARATOR.'namespace_'.$namespace.'.inc';
                $result = array_merge($result, $this->_parseTranslationfile($translationFile));
            }
        }

        // return the resulting table
        return $result;
    }

    /**
     * parses a translationfile
     *
     * This method is intend to parse a translationfile and return the result as array.
     *
     * @param string $filename The name of the file to parse
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The content of the given file
     * @access private
     * @throws DoozR_I18n_Module_Exception
     */
    private function _parseTranslationfile($filename)
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            include_once DOOZR_DOCUMENT_ROOT.'Module/DoozR/I18n/Module/Exception.php';
            throw new DoozR_I18n_Module_Exception('Translationfile: '.$filename.' does not exist or isn\'t readable.');
        }

        // assume empty resulting array
        $result = array();

        // open read handle to file
        $fileHandle = fopen($filename, 'r');

        // read till end of file (eof)
        while (!feof($fileHandle)) {
            // read current line
            $line = fgets($fileHandle);

            if (!$line === false && strlen(trim($line))) {
                $parts = explode('=', $line);
                $result[md5(trim($parts[0]))] = trim($parts[1]);
            }
        }

        // close handle to file
        fclose($fileHandle);

        // return parsed array
        return $result;
    }

    /*******************************************************************************************************************
     * \\ END TOOLS + HELPER
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN MAIN CONTROL METHODS (CONSTRUCTOR AND INIT)
     ******************************************************************************************************************/

    /**
     * constructor
     *
     * This method is intend to act as constructor.
     *
     * @param string $config The config for this type of interface
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object Instance of this class
     * @access protected
     */
    protected function __construct(array $config)
    {
        // store the path to
        $this->_path = $config['path'];

        // call parents constructor
        parent::__construct($config);
    }

    /*******************************************************************************************************************
     * \\ END MAIN CONTROL METHODS (CONSTRUCTOR AND INIT)
     ******************************************************************************************************************/
}

?>
