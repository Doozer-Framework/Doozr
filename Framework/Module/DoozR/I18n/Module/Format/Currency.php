<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Module I18n
 *
 * Currency.php - Currency formatter
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

require_once DOOZR_DOCUMENT_ROOT.'Module/DoozR/I18n/Module/Base/Format.php';

/**
 * DoozR Module I18n
 *
 * Currency.php - Currency formatter
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
class DoozR_I18n_Module_Format_Currency extends DoozR_I18n_Module_Base_Format
{
    /*******************************************************************************************************************
     * // BEGIN PUBLIC INTERFACES
     ******************************************************************************************************************/

    /**
     * formats a given value as correct currency
     *
     * This method is intend to format a given value as correct currency.
     *
     * @param string  $value          The value to format as currency
     * @param boolean $notation       Defines which notation is shown - can be either null, long, short, symbol
     * @param string  $country        The countrycode of the country of the current processed currency
     * @param string  $encoding       The encoding use to display the currency - null, html, ascii, unicode (ansi)
     * @param string  $symbolPosition set to "before" to show symbols on the left, or to "after" to show on right side
     *
     * @return  string The correct formatted currency
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function format(
        $value,
        $notation = null,
        $country = null,
        $encoding = null,
        $symbolPosition = null
    ) {
        // get country
        $country = (!$country) ? $this->locale : $country;

        // format the given value
        $formatted = number_format(
            $value,
            $this->configI10n->currency->minor_unit(),
            $this->configI10n->currency->decimal_point(),
            $this->configI10n->currency->thousands_seperator()
        );

        // is value = major (1) or minor (0)
        $type = ($value < 1) ? 'minor' : 'major';

        // check for position override
        if (!$symbolPosition) {
            $symbolPosition = $this->configI10n->currency->symbol_position();
        }

        // if notation set overwrite it with the concrete notation
        if ($notation) {
            // get notation from currency-table
            $encoding = ($notation == 'symbol' && $encoding) ? '_'.strtoupper($encoding) : '';

            // get notation from translator
            $notation = $this->translator->_($country.'_'.$type.'_'.$notation.$encoding);

            // spacing between curreny-symbol and value
            $notationSpace = $this->configI10n->currency->notation_space();

            // check where to add the symbol ...
            if ($symbolPosition == 'l') {
                $formatted = $notation.$notationSpace.$formatted;
            } else {
                $formatted = $formatted.$notationSpace.$notation;
            }
        }

        // return formatted (currency) result
        return $formatted;
    }

    /**
     * returns the currency-code for the current active locale
     *
     * This method is intend to return the currency-code for the current active locale.
     *
     * @return  integer The currency-code
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getCurrencyCode()
    {
        // return currency code of current active locale
        return $this->configI10n->currency->code();
    }

    /*******************************************************************************************************************
     * \\ END PUBLIC INTERFACES
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN TOOLS + HELPER
     ******************************************************************************************************************/

    /**
     * format dispatcher
     *
     * This method is intend to act as format-dispatcher.
     *
     * @param integer $timestamp The timestamp to format
     * @param string  $format    The format to use for formatting input
     *
     * @return  mixed Result of request
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _formatDate($timestamp = 0, $format = '')
    {
        switch ($this->_timeset) {
        case 1:
            // swatch date
        return $this->swatchDate($timestamp) . ' – ' . $this->swatchTime($timestamp);
        break;
        case 2:
            // iso date
        return $this->unixTimestampToIsoDatetime($timestamp);
        break;
        default:
            // default time
        return date($this->_dateFilter($format, $timestamp), $timestamp);
        break;
        }
    }

    /*******************************************************************************************************************
     * \\ BEGIN TOOLS + HELPER
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN MAIN CONTROL METHODS (CONSTRUCTOR AND INIT)
     ******************************************************************************************************************/

    /**
     * constructor
     *
     * This method is intend to act as constructor.
     *
     * @param DoozR_Registry $registry   The DoozR_Registry instance
     * @param string         $locale     The locale this instance is working with
     * @param string         $namespace  The active namespace of this format-class
     * @param object         $configI18n An instance of DoozR_Config_Ini holding the I18n-config
     * @param object         $configI10n An instance of DoozR_Config_Ini holding the I10n-config (for locale)
     * @param object         $translator An instance of a translator (for locale)
     *
     * @return  object Instance of this class
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function __construct(
        $registry = null,
        $locale = null,
        $namespace = null,
        $configI18n = null,
        $configI10n = null,
        $translator = null
    ) {
        // set type of format-class
        $this->type = 'Currency';

        // call parents construtor
        parent::__construct($registry, $locale, $namespace, $configI18n, $configI10n, $translator);
    }

    /*******************************************************************************************************************
     * \\ END MAIN CONTROL METHODS (CONSTRUCTOR AND INIT)
     ******************************************************************************************************************/
}

?>
