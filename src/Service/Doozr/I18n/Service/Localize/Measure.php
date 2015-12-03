<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - I18n - Service - Localize - Measure.
 *
 * Measure.php - Measurement formatter
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
 * Doozr - I18n - Service - Localize - Measure.
 *
 * Measurement formatter
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
class Doozr_I18n_Service_Localize_Measure extends Doozr_I18n_Service_Localize_Abstract
{
    /**
     * Available measuring systems.
     *
     * @var array
     */
    private $_validSystems = [
        'si',
        'uscs',
    ];

    /**
     * Format which is displayed to user.
     *
     * @var string
     */
    private $_displayLocalize;

    /**
     * Default measuring-system.
     *
     * @var string
     */
    private $_defaultMeasureSystem = 'si';

    /**
     * Measuring-system of input.
     *
     * @var string
     */
    private $_input;

    /**
     * Measuring-system of output.
     *
     * @var string
     */
    private $_output;

    /*------------------------------------------------------------------------------------------------------------------
    | MAIN CONTROL METHODS (CONSTRUCTOR AND INIT)
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to act as constructor.
     *
     * @param Doozr_Registry_Interface $registry   The Doozr_Registry instance
     * @param string                   $locale     The locale this instance is working with
     * @param string                   $namespace  The active namespace of this format-class
     * @param \stdClass                $configI18n An instance of Doozr_Config_Ini holding the I18n-config
     * @param \stdClass                $configL10n An instance of Doozr_Config_Ini holding the I10n-config (for locale)
     * @param object                   $translator An instance of a translator (for locale)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __construct(
        Doozr_Registry_Interface $registry = null,
                                 $locale = null,
                                 $namespace = null,
                                 $configI18n = null,
                                 $configL10n = null,
                                 $translator = null
    ) {
        // Set type of format-class
        $this->type = 'Measure';

        // Setup default in- and output format (measure-system)
        $this->setInputMeasureSystem($this->_defaultMeasureSystem);
        $this->setOutputMeasureSystem($this->_defaultMeasureSystem);

        // Call parents constructor
        parent::__construct($registry, $locale, $namespace, $configI18n, $configL10n, $translator);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to set the measure system for input of the current instance. Can be either uscs or si.
     *
     * @param string $system The system used for input
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     */
    public function setInputMeasureSystem($system = null)
    {
        // set if given system is valid
        if ($this->isValidMeasureSystem($system) === true) {
            return ($this->_input = $system);
        }

        // otherwise set default measure-system of I10n
        return ($this->_input = $this->configL10n->measure->measure_system);
    }

    /**
     * This method is intend to return the measure system for input.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The measure system used for input
     */
    public function getInputMeasureSystem()
    {
        return $this->_input;
    }

    /**
     * This method is intend to set the measure system for output of the current instance. Can be either uscs or si.
     *
     * @param string $system The system used for output
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     */
    public function setOutputMeasureSystem($system = null)
    {
        // set if given system is valid
        if ($this->isValidMeasureSystem($system) === true) {
            return ($this->_output = $system);
        }

        // otherwise set default measure-system of I10n
        return ($this->_output = $this->configL10n->measure->measure_system);
    }

    /**
     * This method is intend to return the measure system for output.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The measure system used for output
     */
    public function getOutputMeasureSystem()
    {
        return $this->_output;
    }

    /**
     * This method is intend to return the state of validity for a given measure system.
     *
     * @param string $system A measure system to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if given measure system is valid, otherwise FALSE
     */
    public function isValidMeasureSystem($system = null)
    {
        return in_array($system, $this->_validSystems);
    }

    /**
     * This method is intend to return a list of available measure systems.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array A list (collection) of available measure systems
     */
    public function getAvailableSystems()
    {
        return $this->_validSystems;
    }

    /**
     * This method is intend to check the validity of a choosen format.
     *
     * @param int $countChoices The count of choices maximum possible
     * @param int $format       The format (choice) of user
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int The given input if valid, otherwise 0
     */
    public function validunit($countChoices, $format)
    {
        // checks if format is smaller 0 (invalid) or greater then given count of choices
        if ($format < 0 || $format > $countChoices) {
            // invalid
            return 0;
        }

        // valid
        return $format;
    }

    /**
     * This method is intend to convert a liquid value from the input system to the output system.
     *
     * @param int $input        The input
     * @param int $inputFormat  The format of the input
     * @param int $outputFormat The format to use for output
     *
     * @example
     * (input)$format:
     * 0: ml|min,
     * 1: cl|fldr,
     * 2: dl|floz,
     * 3: l|gi,
     * 4: dal|pt,
     * 5: hl|qt,
     * 6: hl|gal,
     * 7: hl|barrel
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return float The result of the conversion
     */
    public function liquid($input = 0, $inputFormat = 0, $outputFormat = 0)
    {
        $input = (float) $this->_liquidDownsize($input, $inputFormat);
        $format = (int) $this->validunit(7, $outputFormat);

        if ($this->_input == 'si' && $this->_output == 'si') {
            $formats = ['ml', 'cl', 'dl', 'l', 'dal', 'hl', 'hl', 'hl'];
            $div = [1, 10, 100, 1000, 10000, 100000, 100000, 100000];
        } elseif ($this->_input == 'si' && $this->_output == 'uscs') {
            $formats = ['min', 'fldr', 'floz', 'gi', 'pt', 'qt', 'gal', 'barrel'];
            $div = [
                    0.00048133998891762809516053073702394,
                    3.6966912,
                    29.57353,
                    118.29412,
                    473.17647,
                    946.35295,
                    3785.4118,
                    158987.29,
                ];
        } elseif ($this->_input == 'uscs' && $this->_output == 'si') {
            $formats = ['ml', 'cl', 'dl', 'l', 'dal', 'hl', 'hl', 'hl'];
            $div = [2077.5336, 20775.336, 207753.36, 2077533.6, 20775336, 207753360, 207753360, 207753360];
        } elseif ($this->_input == 'uscs' && $this->_output == 'uscs') {
            $formats = ['min', 'fldr', 'floz', 'gi', 'pt', 'qt', 'gal', 'barrel'];
            $div = [1, 7680, 61440, 245760, 983040, 1966080, 7864320, 330301440];
        }

        $this->_displayFormat = (string) $formats[$format];

        return (float) $input / $div[$format];
    }

    /**
     * This method is intend to convert a liquid value from the input system to the output system.
     *
     * @param int $input        The input
     * @param int $inputFormat  The format of the input
     * @param int $outputFormat The format to use for output
     *
     * @example
     * (input/output)$format:
     * 0: mm|in,
     * 1: cm|ft,
     * 2: dm|yd,
     * 3: m|fur,
     * 4: km|mi,
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return float The result of the conversion
     */
    public function linear($input = 0, $inputFormat = 0, $outputFormat = 0)
    {
        $input = (float) $this->_linearDownsize($input, $inputFormat);
        $format = (int) $this->validunit(4, $outputFormat);

        if ($this->_input == 'si' && $this->_output == 'si') {
            $formats = ['mm', 'cm', 'dm', 'm', 'km'];
            $div = [1, 10, 100, 1000, 1000000];
            $output = (float) $input / $div[$format];
        } elseif ($this->_input == 'si' && $this->_output == 'uscs') {
            $formats = ['in', 'ft', 'yd', 'fur', 'mi'];
            $div = [25.4, 304.8, 914.4, 201168, 1609344];
            $output = (float) $input / $div[$format];
        } elseif ($this->_input == 'uscs' && $this->_output == 'si') {
            $formats = ['mm', 'cm', 'dm', 'm', 'km'];
            $div = [25.4, 2.54, 0.254, 0.0254, 0.0000254];
            $output = (float) $input * $div[$format];
        } elseif ($this->_input == 'uscs' && $this->_output == 'uscs') {
            $formats = ['in', 'ft', 'yd', 'fur', 'mi'];
            $div = [1, 12, 36, 7920, 63360];
            $output = (float) $input / $div[$format];
        }

        $this->display_format = (string) $formats[$format];

        return (float) $output;
    }

    /**
     * This method is intend to convert a temperature value from the input system to the output system.
     *
     * @param int $input The input
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return float The result of the conversion
     */
    public function temperature($input = 0)
    {
        $input = (float) $input;

        if ($this->_input == 'si' && $this->_output == 'si') {
            $this->_displayFormat = 'C';
            $output = (float) $input;
        } elseif ($this->_input == 'si' && $this->_output == 'uscs') {
            $this->_displayFormat = 'F';
            $output = (float) ((($input / 5) * 9) + 32);
        } elseif ($this->_input == 'uscs' && $this->_output == 'si') {
            $this->_displayFormat = 'C';
            $output = (float) ((($input - 32) / 9) * 5);
        } elseif ($this->_input == 'uscs' && $this->_output == 'uscs') {
            $this->_displayFormat = 'F';
            $output = (float) $input;
        }

        return (float) $output;
    }

    /**
     * This method is intend to return the localized (translated) unit for the last conversion.
     *
     * @param int $type The type to use for retrieving the unit (1 = short, 2 = long, 3 = abbr long)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The requested unit
     */
    public function unit($type = 3)
    {
        // check if given type is valid
        if ($type < 0 || $type > 3) {
            $type = 0;
        }

        // check for requested type
        if ($type == 1) {
            $unit = $this->translator->_($this->_displayFormat.'_short');
        } elseif ($type == 2) {
            $unit = $this->translator->_($this->_displayFormat.'_long');
        } else {
            $unit = '<abbr title="'.
                $this->translator->_($this->_displayFormat.'_long').'" xml:lang="'.$this->locale.'">'.
                $this->translator->_($this->_displayFormat.'_short').'</abbr>';
        }

        // return complete constructed unit-string
        return $unit;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | TOOLS & HELPER
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to convert a cooking value to teaspoon (is)|teaspoon (uscs).
     *
     * @param int $input  The input to convert
     * @param int $format The format to use for conversion
     *
     * @example
     * (input)$format:
     * 0: teaspoon (is)|teaspoon (uscs),
     * 1: tablespoon (is)|tablespoon (uscs),
     * 2: tablespoon (is)|cup
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return float The converted result
     */
    private function _cookingDownsize($input = 0, $format = 0)
    {
        $format = (int) $this->validunit(2, $format);

        if ($this->_input == 'si') {
            $div = [1, 3, 3];
        } else {
            $div = [1, 3, 48];
        }

        return (float) $input * $div[$format];
    }

    /**
     * This method is intend to convert a capacity value to mm�|cu in.
     *
     * @param int $input  The input to convert
     * @param int $format The format to use for conversion
     *
     * @example
     * (input)$format:
     * 0: mm�|cu in,
     * 1: cm�|cu ft,
     * 2: dm�|cu yd,
     * 3: m�|acre fd,
     * 4: km�|cu mi
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return float The converted result
     */
    private function _capacityDownsize($input = 0, $format = 0)
    {
        $format = (int) $this->validunit(4, $format);

        if ($this->_input == 'si') {
            $div = [1, 1000, 1000000, 1000000000, 1000000000000];
        } else {
            $div = [1, 1728, 46656, 75271680, 254358061056000];
        }

        return (float) $input * $div[$format];
    }

    /**
     * This method is intend to convert a linear value to mm|in.
     *
     * @param int $input  The input to convert
     * @param int $format The format to use for conversion
     *
     * @example
     * (input/output)$format:
     * 0: mm|in,
     * 1: cm|ft,
     * 2: dm|yd,
     * 3: m|fur,
     * 4: km|mi
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return float The converted result
     */
    private function _linearDownsize($input = 0, $format = 0)
    {
        $format = (int) $this->validunit(4, $format);

        if ($this->_input == 'si') {
            $div = [1, 10, 100, 1000, 1000000];
        } else {
            $div = [1, 12, 36, 7920, 63360];
        }

        return (float) $input * $div[$format];
    }

    /**
     * This method is intend to convert a surface value to mm�|sq in.
     *
     * @param int $input  The input to convert
     * @param int $format The format to use for conversion
     *
     * @example
     * (input)$format:
     * 0: mm�|sq in,
     * 1: cm�|sq ft,
     * 2: dm�|sq yd,
     * 3: m�|sq rd,
     * 4: a|acre,
     * 5: ha|sq mi,
     * 6: km�|sq mi
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return float The converted result
     */
    private function _surfaceDownsize($input = 0, $format = 0)
    {
        $format = (int) $this->validunit(6, $format);

        // check direction of converting si <-> uscs
        if ($this->_input == 'si') {
            $div = [1, 100, 10000, 1000000, 100000000, 10000000000, 1000000000000];
        } else {
            $div = [1, 12, 36, 7920, 63360, 63360, 63360];
        }

        return (float) $input * $div[$format];
    }

    /**
     * This method is intend to convert a weight value to mg|grain (no british weights!).
     *
     * @param int $input  The input to convert
     * @param int $format The format to use for conversion
     *
     * @example
     * (input)$format:
     * 0: mg|grain,
     * 1: cg|dr,
     * 2: dg|oz,
     * 3: g|lb,
     * 4: dag|stone,
     * 5: kg|cwt,
     * 6: ton_is|ton_us
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return float The converted result
     */
    private function _weightDownsize($input = 0, $format = 0)
    {
        $format = (int) $this->validunit(6, $format);

        if ($this->_input == 'si') {
            $div = [1, 10, 100, 1000, 10000, 1000000, 1000000000];
        } else {
            $div = [1, 27.34375, 437.5, 7000, 98000, 700000, 14000000];
        }

        return (float) $input * $div[$format];
    }

    /**
     * This method is intend to convert a liquid value to ml|min.
     *
     * @param int $input  The input to convert
     * @param int $format The format to use for conversion
     *
     * @example
     * (input)$format:
     * 0: ml|min,
     * 1: cl|fldr,
     * 2: dl|floz,
     * 3: l|gi,
     * 4: dal|pt,
     * 5: hl|qt,
     * 6: hl|gal,
     * 7: hl|barrel
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return float The converted result
     */
    private function _liquidDownsize($input = 0, $format = 0)
    {
        $format = (int) $this->validunit(7, $format);

        if ($this->_input == 'si') {
            $div = [1, 10, 100, 1000, 10000, 100000, 100000, 100000];
        } else {
            $div = [1, 7680, 61440, 245760, 983040, 1966080, 7864320, 330301440];
        }

        return (float) $input * $div[$format];
    }
}
