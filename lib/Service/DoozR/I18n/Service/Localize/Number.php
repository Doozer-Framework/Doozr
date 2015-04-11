<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - I18n - Service - Localize - Number
 *
 * Number.php - Number formatter
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

require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/I18n/Service/Localize/Abstract.php';

/**
 * DoozR - I18n - Service - Localize - Number
 *
 * Localize-Number-Class
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
class DoozR_I18n_Service_Localize_Number extends DoozR_I18n_Service_Localize_Abstract
{
    /*******************************************************************************************************************
     * // BEGIN PUBLIC INTERFACES
     ******************************************************************************************************************/

    /**
     * This method is intend to format a given value as percentage value.
     *
     * @param string  $value      The value to format as percentage value
     * @param bool $showSymbol TRUE to show %-symbol, FALSE to hide
     * @param string  $spacer     The spacer placed between value and symbol
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The formatted percentage value
     * @access public
     */
    public function percent($value, $showSymbol = false, $spacer = ' ')
    {
        // format the given value
        $formatted = number_format(
            $value,
            $this->config->currency->minor_unit(),
            $this->config->currency->decimal_point(),
            $this->config->currency->thousands_seperator()
        );

        if ($showSymbol) {
            $formatted .= $spacer.'%';
        }

        // return formatted (currency) result
        return $formatted;
    }


    /**
     * This method is intend to format a given value as percentage value.
     *
     * @param string  $value                 The value to format as percentage value
     * @param bool $floatingPointNotation Controls if the number should be formatted by floating point notation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The formatted percentage value
     * @access public
     */
    public function number($value, $floatingPointNotation = false)
    {
        // format the given value
        return number_format(
            $value,
            ($floatingPointNotation) ? $this->config->currency->minor_unit() : 0,
            $this->config->currency->decimal_point(),
            $this->config->currency->thousands_seperator()
        );
    }

    /*******************************************************************************************************************
     * \\ END PUBLIC INTERFACES
     ******************************************************************************************************************/
}
