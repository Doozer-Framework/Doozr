<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - I18n - Service - Localize - Number.
 *
 * Number.php - This localizer is responsible to localize (L10N) values of type number.
 *
 * PHP versions 5.5
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
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
require_once DOOZR_DOCUMENT_ROOT.'Service/Doozr/I18n/Service/Localize/Abstract.php';

/**
 * Doozr - I18n - Service - Localize - Number.
 *
 * This localizer is responsible to localize (L10N) values of type number.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_I18n_Service_Localize_Number extends Doozr_I18n_Service_Localize_Abstract
{
    /**
     * Type of the current localizer.
     *
     * @var string
     * @access protected
     */
    protected $type = Doozr_I18n_Service::LOCALIZER_NUMBER;

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Formats value as percentage value.
     *
     * @param string $value      The value to format as percentage value
     * @param bool   $showSymbol TRUE to show %-symbol, FALSE to hide
     * @param string $spacer     The spacer placed between value and symbol
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string Formatted percentage value
     */
    public function percent($value, $showSymbol = false, $spacer = ' ')
    {
        if (true === $showSymbol) {
            $formatted = $spacer.'%';

        } else {
            $formatted = '';
        }

        // Format given value
        return number_format(
            $value,
            $this->getConfiguration()->number->minor_unit,
            $this->getConfiguration()->number->decimal_point,
            $this->getConfiguration()->number->thousands_separator
        ) . $formatted;
    }

    /**
     * Formats a value/number to formatted string value.
     *
     * @param string $value                 Value to format as number
     * @param bool   $floatingPointNotation Whether
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string Formatted value
     */
    public function number($value, $floatingPointNotation = false)
    {
        return number_format(
            $value,
            (true === $floatingPointNotation) ? $this->getConfiguration()->number->minor_unit : 0,
            $this->getConfiguration()->number->decimal_point,
            $this->getConfiguration()->number->thousands_separator
        );
    }
}
