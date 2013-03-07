<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Module I18n
 *
 * Datetime.php - Datetime formatter
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
 * Datetime formatter
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
class DoozR_I18n_Module_Format_Datetime extends DoozR_I18n_Module_Base_Format
{
    /**
     * holds the active timeformat
     * 0 = standard format
     * 1 = iso date
     * 2 = swatch date
     *
     * @var integer
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


    /*******************************************************************************************************************
     * // BEGIN PUBLIC INTERFACES
     ******************************************************************************************************************/

    /**
     * checks if a given timecode is valid
     *
     * This method is intend to check if a given timecode is valid.
     *
     * @param integer $timecode The timecode to check
     *
     * @return  boolean TRUE if valid, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function isValidTimeCode($timecode = 0)
    {
        return ($timecode > 0 && $timecode < 2);
    }

    /**
     * checks if a string is a valid iso date
     *
     * This method is intend to check if a string is a valid iso date.
     *
     * @param string $date The date to check
     *
     * @return  boolean TRUE if valid, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function isValidIsoDate($date)
    {
        return (preg_match('(^\d{4}-\d{2}-\d{2}$)', $date) > 0);
    }

    /**
     * checks if a string is a valid iso date
     *
     * This method is intend to check if a string is a valid iso date.
     *
     * @param string $date The date to check
     *
     * @return  boolean TRUE if valid, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function isValidIsoDatetime($date)
    {
        return (preg_match('(^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$)', $date) > 0);
    }

    /**
     * checks if a timestamp is valid
     *
     * This method is intend to check if timestamp is valid.
     *
     * @param integer $timestamp The timestamp to check
     *
     * @return  boolean TRUE if valid, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function isValidUnixTimestamp($timestamp)
    {
        return (preg_match('(^\d{1,10}$)', $timestamp) > 0);
    }

    /**
     * converts an iso-date to unix-timestamp
     *
     * This method is intend to convert an iso-date to unix-timestamp.
     *
     * @param string $date The date to convert
     *
     * @return  integer The resulting unix-timestamp
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function isoDateToUnixTimestamp($date = '1900-01-01')
    {
        return strtotime($date);
    }

    /**
     * converts an iso-datetime to unix-timestamp
     *
     * This method is intend to convert an iso-datetime to unix-timestamp.
     *
     * @param string $date The datetime to convert
     *
     * @return  integer The resulting unix-timestamp
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
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
     * @param integer $timestamp The timestamp to convert to an iso-date
     *
     * @return  string The resulting iso-date
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function unixTimestampToIsodate($timestamp = 0)
    {
        return date('Y-m-d', $timestamp);
    }

    /**
     * converts a timestamp to an iso-time
     *
     * This method is intend to convert a timestamp to an iso-time.
     *
     * @param integer $timestamp The timestamp to convert to an iso-time
     *
     * @return  string The resulting iso-time
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function unixTimestampToIsoTime($timestamp = 0)
    {
        return date('H:i:s', $timestamp);
    }

    /**
     * converts a timestamp to an iso-datetime
     *
     * This method is intend to convert a timestamp to an iso-datetime.
     *
     * @param integer $timestamp The timestamp to convert to an iso-datetime
     *
     * @return  string The resulting iso-datetime
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function unixTimestampToIsoDatetime($timestamp = 0)
    {
        return date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * converts a timestamp to a short-date
     *
     * This method is intend to convert a timestamp to a short-date.
     *
     * @param integer $timestamp The timestamp to convert
     *
     * @return  string The resulting short-date
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function shortDate($timestamp = 0)
    {
        return $this->_formatDate($timestamp, $this->configI10n->date->short_date());
    }

    /**
     * converts a timestamp to a middle-date
     *
     * This method is intend to convert a timestamp to a middle-date.
     *
     * @param integer $timestamp The timestamp to convert
     *
     * @return  string The resulting middle-date
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function middleDate($timestamp = 0)
    {
        return $this->_formatDate($timestamp, $this->configI10n->date->middle_date());
    }

    /**
     * converts a timestamp to a long-date
     *
     * This method is intend to convert a timestamp to a long-date.
     *
     * @param integer $timestamp The timestamp to convert
     *
     * @return  string The resulting long-date
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function longDate($timestamp = 0)
    {
        return $this->_formatDate($timestamp, $this->configI10n->date->long_date());
    }

    /**
     * converts a timestamp to short-time
     *
     * This method is intend to convert a timestamp to short-time.
     *
     * @param integer $timestamp The timestamp to convert
     *
     * @return  string The resulting short-time
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function shortTime($timestamp = 0)
    {
        return $this->_formatTime($timestamp, $this->configI10n->date->short_time());
    }

    /**
     * converts a timestamp to middle-time
     *
     * This method is intend to convert a timestamp to middle-time.
     *
     * @param integer $timestamp The timestamp to convert
     *
     * @return  string The resulting middle-time
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function middleTime($timestamp = 0)
    {
        return $this->_formatTime($timestamp, $this->configI10n->date->middle_time());
    }

    /**
     * converts a timestamp to long-time
     *
     * This method is intend to convert a timestamp to long-time.
     *
     * @param integer $timestamp The timestamp to convert
     *
     * @return  string The resulting long-time
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function longTime($timestamp = 0)
    {
        return $this->_formatTime($timestamp, $this->configI10n->date->long_time());
    }

    /**
     * converts a timestamp to short-Datetime
     *
     * This method is intend to convert a timestamp to short-Datetime.
     *
     * @param integer $timestamp The timestamp to convert
     *
     * @return  string The resulting short-Datetime
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function shortDateTime($timestamp = 0)
    {
        return $this->_formatDatetime($timestamp, $this->configI10n->date->short_datetime());
    }

    /**
     * converts a timestamp to middle-Datetime
     *
     * This method is intend to convert a timestamp to middle-Datetime.
     *
     * @param integer $timestamp The timestamp to convert
     *
     * @return  string The resulting middle-Datetime
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function middleDateTime($timestamp = 0)
    {
        return $this->_formatDatetime($timestamp, $this->configI10n->date->middle_datetime());
    }

    /**
     * converts a timestamp to long-Datetime
     *
     * This method is intend to convert a timestamp to long-Datetime.
     *
     * @param integer $timestamp The timestamp to convert
     *
     * @return  string The resulting long-Datetime
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function longDateTime($timestamp = 0)
    {
        return $this->_formatDatetime($timestamp, $this->configI10n->date->long_datetime());
    }

    /**
     * converts a timestamp to localized (I10n) month name
     *
     * This method is intend to convert a timestamp to localized (I10n) month name.
     *
     * @param integer $timestamp The timestamp to convert
     *
     * @return  string The resulting month name
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function monthName($timestamp = 0)
    {
        // get month from timestamp
        $month = (int) date('n', $timestamp) - 1;

        // return translated (localized) month-name
        return $this->translator->_($this->_month[$month]);
    }

    /**
     * converts a timestamp to localized (I10n) day name
     *
     * This method is intend to convert a timestamp to localized (I10n) day name.
     *
     * @param integer $timestamp The timestamp to convert
     *
     * @return  string The resulting day name
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function dayName($timestamp = 0)
    {
        $day = (int)date('w', $timestamp);
        return $this->translator->_($this->_day[$day]);
    }

    /**
     * returns a list of available timesets
     *
     * This method is intend to return a list of available timesets.
     *
     * @return  array List/collection of available timesets
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
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
     * returns the active timeset
     *
     * This method is intend to return the active timeset.
     *
     * @return  string The currently active timeset
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getTimeset()
    {
        $timesets = $this->getAvailableTimesets();

        return $timesets[$this->_timeset];
    }

    /**
     * sets the active timeset
     *
     * This method is intend to set the active timeset.
     *
     * @param integer $timeset The timeset to set active
     *
     * @return  boolean TRUE on success, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function setTimeset($timeset = 0)
    {
        return ($this->_timeset = $timeset);
    }

    /**
     * converts a timestamp to swatch-time
     *
     * This method is intend to convert a timestamp to swatch-time.
     *
     * @param integer $timestamp The timestamp to convert to swatch-time
     *
     * @return  string Beats of swatch-time for given timestamp
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function swatchTime($timestamp = 0)
    {
        return date('B', $timestamp);
    }

    /**
     * converts a timestamp to swatch-date
     *
     * This method is intend to convert a timestamp to swatch-date.
     *
     * @param integer $timestamp The timestamp to convert to swatch-time
     *
     * @return  string Swatch-date for given timestamp
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function swatchDate($timestamp = 0)
    {
        return '@d'.date('d.m.y', $timestamp);
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
     */
    private function _formatDatetime($timestamp = 0, $format = '')
    {
        switch ($this->_timeset) {
        case 1:
            // swatch date
        return $this->swatchDate($timestamp).' – '.$this->swatchTime($timestamp);
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
     * encodes a datestring
     *
     * This method is intend to encode a datestring.
     *
     * @param string $string The string to encode
     *
     * @return  string The encoded string
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
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
     * applies a filter
     *
     * This method is intend to encode a datestring.
     *
     * @param string  $format    The format to use for formatting timestamp
     * @param integer $timestamp The timestamp to convert/format
     *
     * @return  mixed Result of request
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
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
        $this->type = 'Datetime';

        // store the default and active timeset
        $this->_timeset = $configI10n->date->default_timeset();

        // call parents construtor
        parent::__construct($registry, $locale, $namespace, $configI18n, $configI10n, $translator);
    }

    /*******************************************************************************************************************
     * \\ END MAIN CONTROL METHODS (CONSTRUCTOR AND INIT)
     ******************************************************************************************************************/
}

?>
