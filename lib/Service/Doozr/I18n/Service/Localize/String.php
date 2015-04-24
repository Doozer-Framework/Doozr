<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - I18n - Service - Localize - String
 *
 * String.php - String formatter
 *
 * PHP versions 5.4
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
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
 * @category   Doozr
 * @package    Doozr_Service
 * @subpackage Doozr_Service_I18n
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Service/Doozr/I18n/Service/Localize/Abstract.php';

/**
 * Doozr - I18n - Service - Localize - String
 *
 * String formatter
 *
 * @category   Doozr
 * @package    Doozr_Service
 * @subpackage Doozr_Service_I18n
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_I18n_Service_Localize_String extends Doozr_I18n_Service_Localize_Abstract
{
    /**
     * The bad-word-table
     *
     * @var array
     * @access private
     */
    private $_badWordTable;

    /**
     * The special-word-table
     *
     * @var array
     * @access private
     */
    private $_specialWordTable;

    /**
     * The tags for special-word replacement
     *
     * @var array
     * @access private
     */
    private $_tags = array(
        'acronym',
        'dfn',
        'abbr'
    );

    /**
     * The code templates used by highlightSpecialWords()
     *
     * @var array
     * @access private
     */
    private $_templates = array(
        'abbr'    => '<abbr title="{$DESC}">{$WORD}</abbr>',
        'acronym' => '<acronym title="{$DESC}">{$WORD}</acronym>',
        'dfn'     => '<dfn>{$WORD}</dfn> {$DESC}'
    );


    /*------------------------------------------------------------------------------------------------------------------
     | PUBLIC API
     +----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to return a string with highlighted acronyms, definitions and abbreviations.
     *
     * @param string $string The string to highlight words in
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The processed string
     * @access public
     */
    public function highlightSpecialWords($string)
    {
        // read specialword-table if not already read-in
        if ($this->_specialWordTable === null) {
            $this->_createSpecialWordTable();
        }

        // iterate over possible existing tags
        foreach ($this->_tags as $tag) {
            // get words for tags
            $words = $this->_specialWordTable[$tag];

            // iterate over words as word => description
            foreach ($words as $word => $description) {
                // replace it with template content if word found
                if (mb_strstr($string, $word)) {
                    // get template for tag
                    $tpl = $this->_templates[$tag];

                    // create HTML code
                    $html = str_replace('{$WORD}', $word, str_replace('{$DESC}', $description, $tpl));

                    // insert HTML into given string
                    $string = str_replace($word, $html, $string);
                }
            }
        }

        // return processed string result
        return $string;
    }

    /**
     * This method is intend to remove a bad-word from a given string.
     *
     * @param string $string The string to check for containing bad-word
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The replaced value if bad-word found, otherwise the original-input
     * @access public
     */
    public function removeBadWords($string)
    {
        // read badword-table if not already read-in
        if (!$this->_badWordTable) {
            $this->_createBadWordTable();
        }

        // cut value into pieces
        $words = explode(' ', trim($string));

        // get replace character
        try {
            $replacecharacter = $this->getConfig()->words->replacecharacter;
        } catch (Doozr_Config_Service_Exception $e) {
            $replacecharacter = '*';
        }

        // iterate over each word
        foreach ($words as $key => $word) {

            // clean word from special chars
            $word = preg_replace("/[^a-zA-Z0-9]+/", "", $word);

            foreach ($this->_badWordTable as $badWord) {
                if (preg_match('/^' . $badWord . '/im', $word)) {
                    #$words[$key] = str_replace($badWord, str_repeat($replacecharacter, mb_strlen($badWord)), $word);
                    $words[$key] = preg_replace(
                        '/^' . $badWord . '+/im',
                        str_repeat($replacecharacter, mb_strlen($badWord)),
                        $word
                    );
                    break;
                }
            }
        }

        // return the result
        $result = implode(' ', $words);

        return $result;
    }


    /*------------------------------------------------------------------------------------------------------------------
     | TOOLS & HELPER
     +----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to create the special-word table
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _createSpecialWordTable()
    {
        // get l10n config for strings
        $config = $this->getConfig();

        // assume empty special word table
        $this->_specialWordTable = array();

        // iterate over predefined parts
        foreach ($this->_tags as $tag) {
            // create table
            $this->_specialWordTable[$tag] = $this->config->{$tag};
        }
    }

    /**
     * This method is intend to create the bad-word table
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _createBadWordTable()
    {
        // get l10n config for strings
        $config = $this->getConfig();

        // create empty table
        $this->_badWordTable = array();

        // split list of bad words into array ...
        $badWords = explode(',', $config->words->badwords);

        // ... and process
        foreach ($badWords as $badWord) {
            $this->_badWordTable[] = trim($badWord);
        }
    }


    /*------------------------------------------------------------------------------------------------------------------
     | MAIN CONTROL METHODS (CONSTRUCTOR AND INIT)
     +----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to act as constructor.
     *
     * @param Doozr_Registry_Interface $registry The Doozr_Registry instance
     * @param string $locale The locale this instance is working with
     * @param string $namespace The active namespace of this format-class
     * @param object $configI18n An instance of Doozr_Config_Ini holding the I18n-config
     * @param object $configL10n An instance of Doozr_Config_Ini holding the I10n-config (for locale)
     * @param object $translator An instance of a translator (for locale)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     */
    public function __construct(
        Doozr_Registry_Interface $registry = null,
        $locale                            = null,
        $namespace                         = null,
        $configI18n                        = null,
        $configL10n                        = null,
        $translator                        = null
    ) {
        // Set type of format-class
        $this->type = 'String';

        // Call parents constructor
        parent::__construct($registry, $locale, $namespace, $configI18n, $configL10n, $translator);
    }
}
