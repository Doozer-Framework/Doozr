<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - I18n - Service - Localize - Datetime
 *
 * Datetime.php - Datetime formatter
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
 * DoozR - I18n - Service - Localize - Datetime
 *
 * Datetime formatter
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
class DoozR_I18n_Service_Localize_Datetime extends DoozR_I18n_Service_Localize_Abstract
{
    /**
     * holds the active timeformat
     * 0 = standard format
     * 1 = iso date
     * 2 = swatch date
     *
     * @var int
     * @access private
     */
    private $_timeset;

    /**
     * the localized name representation of the timesets
     *
     * @var array
     * @access private
     */
    private static $_timesetNames;

    /**
     * the exisiting month in correct order
     *
     * @var array
     * @access private
     */
    private $_month = array(
        'january',
        'february',
        'march',
        'april',
        'may',
        'june',
        'july',
        'august',
        'september',
        'october',
        'november',
        'december'
    );

    /**
     * the exisiting days in correct order
     *
     * @var array
     * @access private
     */
    private $_day = array(
        'sunday',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday'
    );

    /*------------------------------------------------------------------------------------------------------------------
    | BEGIN PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to check if a given timecode is valid.
     *
     * @param int $timecode The timecode to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if valid, otherwise FALSE
     * @access public
     */
    public function isValidTimeCode($timecode = 0)
    {
        return ($timecode > 0 && $timecode < 2);
    }

    /**
     * This method is intend to check if a string is a valid iso date.
     *
     * @param string $date The date to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if valid, otherwise FALSE
     * @access public
     */
    public function isValidIsoDate($date)
    {
        return (preg_match('(^\d{4}-\d{2}-\d{2}$)', $date) > 0);
    }

    /**
     * This method is intend to check if a string is a valid iso date.
     *
     * @param string $date The date to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if valid, otherwise FALSE
     * @access public
     */
    public function isValidIsoDatetime($date)
    {
        return (preg_match('(^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$)', $date) > 0);
    }

    /**
     * This method is intend to check if timestamp is valid.
     *
     * @param int $timestamp The timestamp to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if valid, otherwise FALSE
     * @access public
     */
    public function isValidUnixTimestamp($timestamp)
    {
        return (preg_match('(^\d{1,10}$)', $timestamp) > 0);
    }

    /**
     * This method is intend to convert an iso-date to unix-timestamp.
     *
     * @param string $date The date to convert
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The resulting unix-timestamp
     * @access public
     */
    public function isoDateToUnixTimestamp($date = '1900-01-01')
    {
        return strtotime($date);
    }

    /**
     * This method is intend to convert an iso-datetime to unix-timestamp.
     *
     * @param string $date The datetime to convert
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The resulting unix-timestamp
     * @access public
     */
    public function isoDatetimeToUnixTimestamp($date = '1900-01-01 00:00:00')
    {
        return strtotime($date);
    }

    /**
     * converts a timestamp to an iso-date
     *
     * This method is intend to convert a timestamp to an iso-date.
     *
     * @param int $timestamp The timestamp to convert to an iso-date
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The resulting iso-date
     * @access public
     */
    public function unixTimestampToIsodate($timestamp = 0)
    {
        return date('Y-m-d', $timestamp);
    }

    /**
     * This method is intend to convert a timestamp to an iso-time.
     *
     * @param int $timestamp The timestamp to convert to an iso-time
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The resulting iso-time
     * @access public
     */
    public function unixTimestampToIsoTime($timestamp = 0)
    {
        return date('H:i:s', $timestamp);
    }

    /**
     * This method is intend to convert a timestamp to an iso-datetime.
     *
     * @param int $timestamp The timestamp to convert to an iso-datetime
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The resulting iso-datetime
     * @access public
     */
    public function unixTimestampToIsoDatetime($timestamp = 0)
    {
        return date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * This method is intend to convert a timestamp to a short-date.
     *
     * @param int $timestamp The timestamp to convert
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The resulting short-date
     * @access public
     */
    public function shortDate($timestamp = 0)
    {
        return $this->_formatDate($timestamp, $this->configL10n->datetime->short_date());
    }

    /**
     * converts a timestamp to a middle-date
     *
     * This method is intend to convert a timestamp to a middle-date.
     *
     * @param int $timestamp The timestamp to convert
     *
     * @return  string The resulting middle-date
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function middleDate($timestamp = 0)
    {
        return $this->_formatDate($timestamp, $this->configL10n->datetime->middle_date());
    }

    /**
     * This method is intend to convert a timestamp to a long-date.
     *
     * @param int $timestamp The timestamp to convert
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The resulting long-date
     * @access public
     */
    public function longDate($timestamp = 0)
    {
        return $this->_formatDate($timestamp, $this->configL10n->datetime->long_date());
    }

    /**
     * This method is intend to convert a timestamp to short-time.
     *
     * @param int $timestamp The timestamp to convert
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The resulting short-time
     * @access public
     */
    public function shorttime($timestamp = 0)
    {
        return $this->_formatTime($timestamp, $this->configL10n->datetime->short_time());
    }

    /**
     * This method is intend to convert a timestamp to middle-time.
     *
     * @param int $timestamp The timestamp to convert
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The resulting middle-time
     * @access public
     */
    public function middleTime($timestamp = 0)
    {
        return $this->_formatTime($timestamp, $this->configL10n->datetime->middle_time());
    }

    /**
     * This method is intend to convert a timestamp to long-time.
     *
     * @param int $timestamp The timestamp to convert
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The resulting long-time
     * @access public
     */
    public function longTime($timestamp = 0)
    {
        return $this->_formatTime($timestamp, $this->configL10n->datetime->long_time());
    }

    /**
     * This method is intend to convert a timestamp to short-Datetime.
     *
     * @param int $timestamp The timestamp to convert
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The resulting short-Datetime
     * @access public
     */
    public function shortDateTime($timestamp = 0)
    {
        return $this->_formatDatetime($timestamp, $this->configL10n->datetime->short_datetime());
    }

    /**
     * This method is intend to convert a timestamp to middle-Datetime.
     *
     * @param int $timestamp The timestamp to convert
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The resulting middle-Datetime
     * @access public
     */
    public function middleDateTime($timestamp = 0)
    {
        return $this->_formatDatetime($timestamp, $this->configL10n->datetime->middle_datetime());
    }

    /**
     * This method is intend to convert a timestamp to long-Datetime.
     *
     * @param int $timestamp The timestamp to convert
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The resulting long-Datetime
     * @access public
     */
    public function longDateTime($timestamp = 0)
    {
        return $this->_formatDatetime($timestamp, $this->configL10n->datetime->long_datetime());
    }

    /**
     * This method is intend to convert a timestamp to localized (I10n) month name.
     *
     * @param int $timestamp The timestamp to convert
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The resulting month name
     * @access public
     */
    public function monthname($timestamp = 0)
    {
        // get month from timestamp
        $month = (int)date('n', $timestamp) - 1;

        // return translated (localized) month-name
        return $this->translator->_($this->_month[$month]);
    }

    /**
     * This method is intend to convert a timestamp to localized (I10n) day name.
     *
     * @param int $timestamp The timestamp to convert
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The resulting day name
     * @access public
     */
    public function dayname($timestamp = 0)
    {
        $day = (int)date('w', $timestamp);

        return $this->getConfig()->datetime->{$this->_day[$day]}();
        //return $this->translator->_($this->_day[$day]);
    }

    /**
     * This method is intend to return a list of available timesets.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array List/collection of available timesets
     * @access public
     */
    public function getAvailableTimesets()
    {
        // build array with localized names
        if (!isset(self::$_timesetNames)) {
            self::$_timesetNames = array(
                $this->translator->_('standard_time'),
                $this->translator->_('swatch_time'),
                $this->translator->_('ISO_time')
            );
        }

        // return the names of the timesets
        return self::$_timesetNames;
    }

    /**
     * This method is intend to return the active timeset.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The currently active timeset
     * @access public
     */
    public function getTimeset()
    {
        $timesets = $this->getAvailableTimesets();

        return $timesets[$this->_timeset];
    }

    /**
     * This method is intend to set the active timeset.
     *
     * @param int $timeset The timeset to set active
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function setTimeset($timeset = 0)
    {
        return ($this->_timeset = $timeset);
    }

    /**
     * This method is intend to convert a timestamp to swatch-time.
     *
     * @param int $timestamp The timestamp to convert to swatch-time
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Beats of swatch-time for given timestamp
     * @access public
     */
    public function swatchTime($timestamp = 0)
    {
        return date('B', $timestamp);
    }

    /**
     * This method is intend to convert a timestamp to swatch-date.
     *
     * @param int $timestamp The timestamp to convert to swatch-time
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Swatch-date for given timestamp
     * @access public
     */
    public function swatchDate($timestamp = 0)
    {
        return '@d' . Date('d.m.y', $timestamp);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | BEGIN TOOLS + HELPER
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to act as format-dispatcher.
     *
     * @param int $timestamp The timestamp to format
     * @param string  $format    The format to use for formatting input
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Result of request
     * @access private
     */
    private function _formatDate($timestamp = 0, $format = '')
    {
        switch ($this->_timeset) {
        case 1:
            // swatch date
        return $this->swatchDate($timestamp) . ' � ' . $this->swatchTime($timestamp);
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

    /**
     * This method is intend to act as format-dispatcher.
     *
     * @param int $timestamp The timestamp to format
     * @param string  $format    The format to use for formatting input
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Result of request
     * @access private
     */
    private function _formatTime($timestamp = 0, $format = '')
    {
        switch ($this->_timeset) {
        case 1:
            // swatch date
        return $this->swatchTime($timestamp);
        break;
        case 2:
            // iso date
        return $this->unixTimestampToISOtime($timestamp);
        break;
        default:
            // default time
        return date($this->_dateFilter($format, $timestamp), $timestamp);
        break;
        }
    }

    /**
     * This method is intend to act as format-dispatcher.
     *
     * @param int $timestamp The timestamp to format
     * @param string  $format    The format to use for formatting input
     *
     * @return mixed Result of request
     * @access private
     */
    private function _formatDatetime($timestamp = 0, $format = '')
    {
        switch ($this->_timeset) {
        case 1:
            // swatch date
        return $this->swatchDate($timestamp).' � '.$this->swatchTime($timestamp);
        break;
        case 2:
            // iso date
        return $this->unixTimestampToISOdatetime($timestamp);
        break;
        default:
            // default time
        return date($this->_dateFilter($format, $timestamp), $timestamp);
        break;
        }
    }

    /**
     * This method is intend to encode a datestring.
     *
     * @param string $string The string to encode
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The encoded string
     * @access private
     */
    private function _encodeDate($string = '')
    {
        // maybe has to be rewritten for multibyte...
        $length = strlen($string);
        $encodedString = '';

        for ($i = 0; $i < $length; $i++) {
            $encodedString .= '\\' . $string[$i];
        }

        // return encoded string
        return $encodedString;
    }

    /**
     * This method is intend to encode a datestring.
     *
     * @param string  $format    The format to use for formatting timestamp
     * @param int $timestamp The timestamp to convert/format
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Result of request
     * @access private
     */
    private function _dateFilter($format = '', $timestamp = 0)
    {
        // check if replace needed - return direct if not
        if (!preg_match('(monthname|dayname|hour)', $format)) {
            return $format;
        }

        // replace existing placeholder - monthname_short
        $format = mb_eregi_replace(
            'monthname_short',
            $this->_encodeDate(mb_substr($this->monthName($timestamp), 0, 3)),
            $format
        );

        // replace existing placeholder - dayname_short
        $format = mb_eregi_replace(
            'dayname_short',
            $this->_encodeDate(mb_substr($this->dayName($timestamp), 0, 2)),
            $format
        );

        // replace existing placeholder - dayname
        $format = mb_eregi_replace(
            'dayname',
            $this->_encodeDate($this->dayName($timestamp)),
            $format
        );

        // replace existing placeholder - monthname
        $format = mb_eregi_replace(
            'monthname',
            $this->_encodeDate($this->monthName($timestamp)),
            $format
        );

        // replace existing placeholder - hour
        $format = mb_eregi_replace(
            'hour',
            $this->_encodeDate($this->translator->_('hour')),
            $format
        );

        // and return processed result
        return $format;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | BEGIN MAIN CONTROL METHODS (CONSTRUCTOR AND INIT)
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to act as constructor.
     *
     * @param DoozR_Registry_Interface      $registry   The DoozR_Registry instance
     * @param string                        $locale     The locale this instance is working with
     * @param string                        $namespace  The active namespace of this format-class
     * @param object                        $configI18n An instance of DoozR_Config_Ini holding the I18n-config
     * @param object                        $configL10n An instance of DoozR_Config_Ini holding the I10n-config (locale)
     * @param DoozR_I18n_Service_Translator $translator An instance of a translator (for locale)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_I18n_Service_Localize_Datetime Instance of this class
     * @access public
     */
    public function __construct(
        DoozR_Registry_Interface $registry        = null,
        $locale                                   = null,
        $namespace                                = null,
        $configI18n                               = null, // Config of DoozR (main .config) including section "I18n"
        $configL10n                               = null, // Config I18n/L10n configuration of the current active locale
        DoozR_I18n_Service_Translator $translator = null
    ) {
        // Set type of format-class
        $this->type = 'Datetime';

        // Store the default and active timeset
        $this->_timeset = $configL10n->datetime->default_timeset;


        // Call parents constructor
        parent::__construct($registry, $locale, $namespace, $configI18n, $configL10n, $translator);
    }
}
