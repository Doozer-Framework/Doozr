<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Datetime - Service
 *
 * Service.php - Datetime Service-Mainclass
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
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Datetime
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Service/Multiple.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Service/Interface.php';

/**
 * DoozR - Datetime - Service
 *
 * Datetime Service-Mainclass
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Datetime
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 * @DoozRType  Multiple
 */
class DoozR_Datetime_Service extends DoozR_Base_Service_Multiple implements DoozR_Base_Service_Interface
{
    /**
     * holds the DateTime-Instance of this instance
     *
     * @var object
     * @access private
     */
    private $_dateTime;


    /**
     * replacement for __construct
     *
     * This method is intend as replacement for __construct
     * PLEASE DO NOT USE __construct() - make always use of __tearup()!
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function __tearup()
    {
        $this->_dateTime = new DateTime();
    }

    /**
     * update date/time
     *
     * This method is intend to update/set the/a new Date/Time.
     *
     * @param string $date The date/time to set
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function setDate($date = null)
    {
        if (!$date) {
            $date = date('d.m.Y H:i:s', time());
        }

        // format date
        $date = $this->_formatDate($date);

        // get timestamp from date
        $date = $this->_getTimestamp(
            $date[0], // hour
            $date[1], // minute
            $date[2], // second
            $date[3], // month
            $date[4], // day
            $date[5]  // year
        );

        // update current date/time
        $this->_dateTime->setTimestamp($date);
    }

    /**
     * returns current date
     *
     * This method is intend to return the current date.
     *
     * @param string $format The format to return (same as in PHP's date())
     *
     * @return  string The date in requested format
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getDate($format = 'd.m.Y')
    {
        return date($format, $this->_dateTime->getTimestamp());
    }

    /**
     * returns current time
     *
     * This method is intend to return the current time.
     *
     * @param string $format The format to return (same as in PHP's date())
     *
     * @return  string The time in requested format
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getTime($format = 'H:i:s')
    {
        return date($format, $this->_dateTime->getTimestamp());
    }

    /**
     * returns current seconds
     *
     * This method is intend to return the current seconds.
     *
     * @param mixed $timestamp The timestamp to use as date/time-base
     *
     * @return  integer Seconds
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getSecond($timestamp = false)
    {
        if (!$timestamp) {
            $timestamp = $this->_dateTime->getTimestamp();
        }

        return (int) date('s', $timestamp);
    }

    /**
     * returns current minutes
     *
     * This method is intend to return the current minutes.
     *
     * @param mixed $timestamp The timestamp to use as date/time-base
     *
     * @return  string Minutes
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getMinute($timestamp = false)
    {
        if (!$timestamp) {
            $timestamp = $this->_dateTime->getTimestamp();
        }

        return date('i', $timestamp);
    }

    /**
     * returns current hour
     *
     * This method is intend to return the current hour.
     *
     * @param mixed $timestamp The timestamp to use as date/time-base
     *
     * @return  string Hour
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getHour($timestamp = false)
    {
        if (!$timestamp) {
            $timestamp = $this->_dateTime->getTimestamp();
        }

        return date('G', $timestamp);
    }

    /**
     * returns current day
     *
     * This method is intend to return the current day.
     *
     * @param mixed $timestamp The timestamp to use as date/time-base
     *
     * @return  string Day
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getDay($timestamp = false)
    {
        if (!$timestamp) {
            $timestamp = $this->_dateTime->getTimestamp();
        }

        return date('j', $timestamp);
    }

    /**
     * returns current week
     *
     * This method is intend to return the current week.
     *
     * @param mixed $timestamp The timestamp to use as date/time-base
     *
     * @return  integer Week
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getWeek($timestamp = false)
    {
        if (!$timestamp) {
            $timestamp = $this->_dateTime->getTimestamp();
        }

        return (int) date('W', $timestamp);
    }

    /**
     * returns current weekday
     *
     * This method is intend to return the current weekday.
     *
     * @param mixed $timestamp The timestamp to use as date/time-base
     *
     * @return  string Weekday
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getWeekday($timestamp = false)
    {
        if (!$timestamp) {
            $timestamp = $this->_dateTime->getTimestamp();
        }

        return date('w', $timestamp);
    }

    /**
     * returns current month
     *
     * This method is intend to return the current month.
     *
     * @param mixed $timestamp The timestamp to use as date/time-base
     *
     * @return  string Month
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getMonth($timestamp = false)
    {
        if (!$timestamp) {
            $timestamp = $this->_dateTime->getTimestamp();
        }

        return date('n', $timestamp);
    }

    /**
     * returns current year
     *
     * This method is intend to return the current year.
     *
     * @param mixed $timestamp The timestamp to use as date/time-base
     *
     * @return  string Year
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getYear($timestamp = false)
    {
        if (!$timestamp) {
            $timestamp = $this->_dateTime->getTimestamp();
        }

        return date('Y', $timestamp);
    }

    /**
     * returns current MySql-DateTime
     *
     * This method is intend to return the current MySql-DateTime.
     *
     * @return  string The requested value
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getMySqlDateTime()
    {
        return $this->_format('Y-m-d H:i:s');
    }

    /**
     * returns current Din5008-DateTime
     *
     * This method is intend to return the current Din5008-DateTime.
     *
     * @return  string The requested value
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getDin5008DateTime()
    {
        return $this->_format('d.m.Y H:i:s');
    }

    /**
     * returns current Din5008-Date
     *
     * This method is intend to return the current Din5008-Date.
     *
     * @return  string The requested value
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getDin5008Date()
    {
        return $this->_format('d.m.Y');
    }

    /**
     * returns current MySql-Date and Time = NULL
     *
     * This method is intend to return the current MySql-Date and Time = NULL.
     *
     * @return  string The requested value
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getMySqlDateNull()
    {
        return $this->_format('Y-m-d 00:00:00');
    }

    /**
     * returns current MySql-Date
     *
     * This method is intend to return the current MySql-Date.
     *
     * @return  string The requested value
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getMySQLDate()
    {
        return $this->_format('Y-m-d');
    }

    /**
     * returns current MySql-Compact-Date
     *
     * This method is intend to return the current MySql-Compact-Date.
     *
     * @return  string The requested value
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getMySQLCompactDate()
    {
        return $this->_format('Ymd');
    }

    /**
     * checks if current Date/Time is NULL
     *
     * This method is intend to check if current Date/Time is NULL.
     *
     * @return  boolean TRUE if NULL, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function isNull()
    {
        return ($this->getMySqlDateTime() == '0000-00-00 00:00:00');
    }

    /**
     * returns current Date as Timestamp
     *
     * This method is intend to return current Date as Timestamp.
     *
     * @return  string The requested value
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getTimeStamp()
    {
        return $this->_dateTime->getTimestamp();
    }

    /**
     * returns current Date as MySql Datetime
     *
     * This method is intend to return current Date as MySql Datetime.
     *
     * @return  string The requested value
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getCurrentMySqlDateTime()
    {
        $now = new self;
        return $now->getMySqlDateTime();
    }

    /**
     * returns current Date as DIN 5008 DateTime
     *
     * This method is intend to return current Date as DIN 5008 DateTime.
     *
     * @return  string The requested value
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getCurrentDin5008DateTime()
    {
        $now = new self;
        return $now->getDin5008DateTime();
    }

    /**
     * returns current Date as DIN 5008 Date
     *
     * This method is intend to return current Date as DIN 5008 Date.
     *
     * @return  string The requested value
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getCurrentDin5008Date()
    {
        $now = new self;
        return $now->getDin5008Date();
    }

    /**
     * adds seconds to current date (or new DateTime-Instance)
     *
     * This method is intend to add seconds to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of seconds - if FALSE then
     * a fresh DateTime-Instance is returned with increased seconds.
     *
     * @param integer $count  The count to use for operation
     * @param boolean $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @return  mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased seconds
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function addSecond($count = 1, $update = true)
    {
        return $this->_add($count, 'second', $update);
    }

    /**
     * subtracts seconds to current date (or new DateTime-Instance)
     *
     * This method is intend to add seconds to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of seconds - if FALSE then
     * a fresh DateTime-Instance is returned with increased seconds.
     *
     * @param integer $count  The count to use for operation
     * @param boolean $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @return  mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased seconds
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function subtractSecond($count = 1, $update = true)
    {
        return $this->_subtract($count, 'second', $update);
    }

    /**
     * adds minutes to current date (or new DateTime-Instance)
     *
     * This method is intend to add minutes to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of minutes - if FALSE then
     * a fresh DateTime-Instance is returned with increased minutes.
     *
     * @param integer $count  The count to use for operation
     * @param boolean $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @return  mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased minutes
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function addMinute($count = 1, $update = true)
    {
        return $this->_add($count, 'minutes', $update);
    }

    /**
     * subtracts minutes to current date (or new DateTime-Instance)
     *
     * This method is intend to add minutes to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of minutes - if FALSE then
     * a fresh DateTime-Instance is returned with increased minutes.
     *
     * @param integer $count  The count to use for operation
     * @param boolean $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @return  mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased minutes
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function subtractMinute($count = 1, $update = true)
    {
        return $this->_subtract($count, 'minute', $update);
    }

    /**
     * adds hours to current date (or new DateTime-Instance)
     *
     * This method is intend to add hours to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of hours - if FALSE then
     * a fresh DateTime-Instance is returned with increased hours.
     *
     * @param integer $count  The count to use for operation
     * @param boolean $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @return  mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased hours
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function addHour($count = 1, $update = true)
    {
        return $this->_add($count, 'hour', $update);
    }

    /**
     * subtracts hours to current date (or new DateTime-Instance)
     *
     * This method is intend to add hours to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of hours - if FALSE then
     * a fresh DateTime-Instance is returned with increased hours.
     *
     * @param integer $count  The count to use for operation
     * @param boolean $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @return  mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased hours
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function subtractHour($count = 1, $update = true)
    {
        return $this->_subtract($count, 'hour', $update);
    }

    /**
     * adds days to current date (or new DateTime-Instance)
     *
     * This method is intend to add days to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of days - if FALSE then
     * a fresh DateTime-Instance is returned with increased days.
     *
     * @param integer $count  The count to use for operation
     * @param boolean $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @return  mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased days
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function addDay($count = 1, $update = true)
    {
        return $this->_add($count, 'day', $update);
    }

    /**
     * subtracts days to current date (or new DateTime-Instance)
     *
     * This method is intend to add days to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of days - if FALSE then
     * a fresh DateTime-Instance is returned with increased days.
     *
     * @param integer $count  The count to use for operation
     * @param boolean $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @return  mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased days
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function subtractDay($count = 1, $update = true)
    {
        return $this->_subtract($count, 'day', $update);
    }

    /**
     * adds week to current date (or new DateTime-Instance)
     *
     * This method is intend to add week to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of week - if FALSE then
     * a fresh DateTime-Instance is returned with increased week.
     *
     * @param integer $count  The count to use for operation
     * @param boolean $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @return  mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased week
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function addWeek($count = 1, $update = true)
    {
        return $this->_add($count, 'week', $update);
    }

    /**
     * subtracts weeks to current date (or new DateTime-Instance)
     *
     * This method is intend to add weeks to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of weeks - if FALSE then
     * a fresh DateTime-Instance is returned with increased weeks.
     *
     * @param integer $count  The count to use for operation
     * @param boolean $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @return  mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased weeks
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function subtractWeek($count = 1, $update = true)
    {
        return $this->_subtract($count, 'week', $update);
    }

    /**
     * adds month to current date (or new DateTime-Instance)
     *
     * This method is intend to add month to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of month - if FALSE then
     * a fresh DateTime-Instance is returned with increased month.
     *
     * @param integer $count  The count to use for operation
     * @param boolean $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @return  mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased month
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function addMonth($count = 1, $update = true)
    {
        return $this->_add($count, 'month', $update);
    }

    /**
     * subtracts month to current date (or new DateTime-Instance)
     *
     * This method is intend to add month to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of month - if FALSE then
     * a fresh DateTime-Instance is returned with increased month.
     *
     * @param integer $count  The count to use for operation
     * @param boolean $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @return  mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased month
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function subtractMonth($count = 1, $update = true)
    {
        return $this->_subtract($count, 'month', $update);
    }

    /**
     * adds years to current date (or new DateTime-Instance)
     *
     * This method is intend to add years to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of years - if FALSE then
     * a fresh DateTime-Instance is returned with increased years.
     *
     * @param integer $count  The count to use for operation
     * @param boolean $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @return  mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased years
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function addYear($count = 1, $update = true)
    {
        return $this->_add($count, 'year', $update);
    }

    /**
     * subtracts years to current date (or new DateTime-Instance)
     *
     * This method is intend to add years to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of years - if FALSE then
     * a fresh DateTime-Instance is returned with increased years.
     *
     * @param integer $count  The count to use for operation
     * @param boolean $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @return  mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased years
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function subtractYear($count = 1, $update = true)
    {
        return $this->_subtract($count, 'year', $update);
    }

    /**
     * calculates the difference (in days, month, years, hours, minutes, seconds ...)
     * between current and given date
     *
     * This method is intend to calculate the difference (in days, month, years, hours, minutes, seconds ...)
     * between current and given date.
     *
     * @param string $date The date used for diff
     * @param string $unit The unit to return
     *
     * @return  string Result of diff in unit given
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getDiff($date, $unit = 'd')
    {
        $date = $this->_formatDate($date);

        $date = $this->_getTimestamp(
            $date[0], // hour
            $date[1], // minute
            $date[2], // second
            $date[3], // month
            $date[4], // day
            $date[5]  // year
        );

        $dateTime = new DateTime();
        $dateTime->setTimestamp($date);
        $diff = $this->_dateTime->diff($dateTime);

        /**
           possible return values
           'y' => 21,
           'm' => 1,
           'd' => 23,
           'h' => 0,
           'i' => 36,
           's' => 59,
           'invert' => 0,
           'days' => 7724,
         */

        return $diff->{strtolower($unit)};
    }

    /**
     * calculates the previous (last) business-day right before the current date
     *
     * This method is intend to calculate the previous (last) business-day right before the current date.
     *
     * @param string $lastBusinessDay Defines the last business-day of the week (Monday = 1, ...,  Friday = 5 ...)
     *
     * @return  string Result as DateTime-Instance
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getPreviousBusinessDay($lastBusinessDay = 5)
    {
        // iterate max 3 loops
        for ($i = 1; $i <= 3; ++$i) {
            // init date with last day - no matter what day this was
            $date = $this->subtractDay($i, false);

            // get previous day as "day of week"
            $weekday = $this->getWeekday($date->getTimestamp());

            if ($weekday > 0 && $weekday <= $lastBusinessDay) {
                return $date;
            }
        }
    }

    /**
     * calculates the difference between the two given microtime(s)
     *
     * This method is intend to calculate the difference between two given microtime(s).
     *
     * @param float $microtimeStart The microtime-value as start
     * @param float $microtimeEnd   The microtime-value as end
     *
     * @return  float
     * @access  public
     * @since   1.0
     */
    public function getMicrotimeDiff($microtimeStart, $microtimeEnd = false)
    {
        $microtimeEnd = (!$microtimeEnd) ? microtime() : $microtimeEnd;

        list($microtimeStart_dec, $microtimeStart_sec) = explode(' ', $microtimeStart);
        list($microtimeEnd_dec, $microtimeEnd_sec) = explode(' ', $microtimeEnd);

        return sprintf("%0.12f", ($microtimeEnd_sec - $microtimeStart_sec + $microtimeEnd_dec - $microtimeStart_dec));
    }

    /**
     * converts a MySQL Datetime Value to a PHP Timestamp (UNIX Timestamp)
     *
     * This method is intend to convert a MySQL Datetime Value to a PHP Timestamp (UNIX Timestamp).
     *
     * @param string $datetime The MySQL Datetime value to convert.
     *
     * @return  integer Timestamp
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function convertMySqlDateTimeToPhpTimestamp($datetime)
    {
        $val  = explode(' ', $datetime);
        $date = explode('-', $val[0]);
        $time = explode(':', $val[1]);

        return mktime((int)$time[0], (int)$time[1], (int)$time[2], (int)$date[1], (int)$date[2], (int)$date[0]);
    }

    /**
     * adds some value to current date/time
     *
     * This method is intend to add some value to current date/time.
     *
     * @param integer $count  The amount to add
     * @param string  $unit   The unit to use for calculation
     * @param boolean $update TRUE to upate current date/time with new value, FALSE to return fresh instance
     *
     * @return  mixed TRUE/FALSE as result of update, otherwise fresh DateTime-Instance
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    private function _add($count, $unit = 'day', $update = true)
    {
        return $this->_dateCalculation($count, $unit, $update, '+');
    }

    /**
     * subtracts some value to current date/time
     *
     * This method is intend to subtract some value to current date/time.
     *
     * @param integer $count  The amount to subtract
     * @param string  $unit   The unit to use for calculation
     * @param boolean $update TRUE to upate current date/time with new value, FALSE to return fresh instance
     *
     * @return  mixed TRUE/FALSE as result of update, otherwise fresh DateTime-Instance
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    private function _subtract($count, $unit = 'day', $update = true)
    {
        return $this->_dateCalculation($count, $unit, $update, '-');
    }

    /**
     * main date/time calculation method
     *
     * This method is intend to operate on date/time.
     *
     * @param integer $count     The amount to subtract
     * @param string  $unit      The unit to use for calculation
     * @param boolean $update    TRUE to upate current date/time with new value, FALSE to return fresh instance
     * @param string  $operation "+" to add/incread, "-" to subtract/decrease
     *
     * @return  mixed TRUE/FALSE as result of update, otherwise fresh DateTime-Instance
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    private function _dateCalculation($count, $unit, $update, $operation = '+')
    {
        $timestamp = strtotime(
            date(
                'd.m.Y H:i:s', strtotime(
                    $this->getDate('Y-m-d H:i:s')
                )
            ) . " ".$operation.$count." ".$unit
        );

        if ($update) {
            return $this->_dateTime->setTimestamp($timestamp);
        } else {
            $dateTime = new DateTime();
            $dateTime->setTimestamp($timestamp);
            return $dateTime;
        }
    }

    /**
     * formats date/time by given format
     *
     * This method is intend to format date/time by a given format (same as in PHP's date())
     *
     * @param string $format The format to use
     *
     * @return  string Date/Time formatted
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    private function _format($format = 'd.m.Y H:i:s')
    {
        return date($format, $this->_dateTime->getTimestamp());
    }

    /**
     * formats a date by given format
     *
     * This method is intend to format a date by a given format (same as in PHP's date())
     *
     * @param string $date      The date to operate on
     * @param string $format    The format to use
     * @param string $separator The separator used in date-format
     *
     * @return  string Date/Time formatted
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    private function _formatDate($date, $format = 'H/i/s/m/d/Y', $separator = '/')
    {
        $date = date($format, strtotime($date));
        return explode($separator, $date);
    }

    /**
     * returns timestamp for standalone date/time-values
     *
     * This method is intend to return timestamp for standalone date/time-values.
     *
     * @param string $hour   The hour
     * @param string $minute The minute
     * @param string $second The second
     * @param string $day    The day
     * @param string $month  The month
     * @param string $year   The year
     *
     * @return  string UNIX-Timestamp
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    private function _getTimestamp(
        $hour,
        $minute,
        $second,
        $day,
        $month,
        $year
    ) {
        return mktime(
            $hour,
            $minute,
            $second,
            $day,
            $month,
            $year
        );
    }
}

?>
