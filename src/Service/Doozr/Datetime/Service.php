<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Datetime - Service
 *
 * Service.php - Datetime Service-Mainclass
 *
 * PHP versions 5.5
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
 *
 * Copyright (c) 2005 - 2016, Benjamin Carl - All rights reserved.
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
 * @package    Doozr_Service
 * @subpackage Doozr_Service_Datetime
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Service/Multiple.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Service/Interface.php';

use Doozr\Loader\Serviceloader\Annotation\Inject;

/**
 * Doozr - Datetime - Service
 *
 * Datetime Service-Mainclass
 *
 * @category   Doozr
 * @package    Doozr_Service
 * @subpackage Doozr_Service_Datetime
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 * @Inject(
 *     link   = "doozr.registry",
 *     type   = "constructor",
 *     target = "getInstance"
 * )
 */
class Doozr_Datetime_Service extends Doozr_Base_Service_Multiple
{
    /**
     * Holds the DateTime-Instance of this instance
     *
     * @var object
     * @access protected
     */
    protected $dateTime;

    /**
     * Replacement for __construct
     *
     * This method is intend as replacement for __construct
     * PLEASE DO NOT USE __construct() - make always use of __tearup()!
     *
     * @return  void
     * @access  public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __tearup()
    {
        $this->dateTime = new DateTime();
    }

    /**
     * This method is intend to update/set the/a new Date/Time.
     *
     * @param string $date The date/time to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setDate($date = null)
    {
        if (!$date) {
            $date = date('d.m.Y H:i:s', time());
        }

        // format date
        $date = $this->formatDate($date);

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
        $this->dateTime->setTimestamp($date);
    }

    /**
     * This method is intend to return the current date.
     *
     * @param string $format The format to return (same as in PHP's date())
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The date in requested format
     * @access public
     */
    public function getDate($format = 'd.m.Y')
    {
        return date($format, $this->dateTime->getTimestamp());
    }

    /**
     * This method is intend to return the current time.
     *
     * @param string $format The format to return (same as in PHP's date())
     *
     * @return  string The time in requested format
     * @access  public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function getTime($format = 'H:i:s')
    {
        return date($format, $this->dateTime->getTimestamp());
    }

    /**
     * This method is intend to return the current seconds.
     *
     * @param mixed $timestamp The timestamp to use as date/time-base
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer Seconds
     * @access public
     */
    public function getSecond($timestamp = false)
    {
        if (!$timestamp) {
            $timestamp = $this->dateTime->getTimestamp();
        }

        return (int) date('s', $timestamp);
    }

    /**
     * This method is intend to return the current minutes.
     *
     * @param mixed $timestamp The timestamp to use as date/time-base
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Minutes
     * @access public
     */
    public function getMinute($timestamp = false)
    {
        if (!$timestamp) {
            $timestamp = $this->dateTime->getTimestamp();
        }

        return date('i', $timestamp);
    }

    /**
     * This method is intend to return the current hour.
     *
     * @param mixed $timestamp The timestamp to use as date/time-base
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Hour
     * @access public
     */
    public function getHour($timestamp = false)
    {
        if (!$timestamp) {
            $timestamp = $this->dateTime->getTimestamp();
        }

        return date('G', $timestamp);
    }

    /**
     * This method is intend to return the current day.
     *
     * @param mixed $timestamp The timestamp to use as date/time-base
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Day
     * @access public
     */
    public function getDay($timestamp = false)
    {
        if (!$timestamp) {
            $timestamp = $this->dateTime->getTimestamp();
        }

        return date('j', $timestamp);
    }

    /**
     * This method is intend to return the current week.
     *
     * @param mixed $timestamp The timestamp to use as date/time-base
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer Week
     * @access public
     */
    public function getWeek($timestamp = false)
    {
        if (!$timestamp) {
            $timestamp = $this->dateTime->getTimestamp();
        }

        return (int) date('W', $timestamp);
    }

    /**
     * This method is intend to return the current weekday.
     *
     * @param mixed $timestamp The timestamp to use as date/time-base
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Weekday
     * @access public
     */
    public function getWeekday($timestamp = false)
    {
        if (!$timestamp) {
            $timestamp = $this->dateTime->getTimestamp();
        }

        return date('w', $timestamp);
    }

    /**
     * This method is intend to return the current month.
     *
     * @param mixed $timestamp The timestamp to use as date/time-base
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Month
     * @access public
     */
    public function getMonth($timestamp = false)
    {
        if (!$timestamp) {
            $timestamp = $this->dateTime->getTimestamp();
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
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Year
     * @access public
     */
    public function getYear($timestamp = false)
    {
        if (!$timestamp) {
            $timestamp = $this->dateTime->getTimestamp();
        }

        return date('Y', $timestamp);
    }

    /**
     * This method is intend to return the current MySql-DateTime.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The requested value
     * @access public
     */
    public function getMySqlDateTime()
    {
        return $this->format('Y-m-d H:i:s');
    }

    /**
     * returns current Din5008-DateTime
     *
     * This method is intend to return the current Din5008-DateTime.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The requested value
     * @access public
     */
    public function getDin5008DateTime()
    {
        return $this->format('d.m.Y H:i:s');
    }

    /**
     * returns current Din5008-Date
     *
     * This method is intend to return the current Din5008-Date.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The requested value
     * @access public
     */
    public function getDin5008Date()
    {
        return $this->format('d.m.Y');
    }

    /**
     * returns current MySql-Date and Time = NULL
     *
     * This method is intend to return the current MySql-Date and Time = NULL.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The requested value
     * @access public
     */
    public function getMySqlDateNull()
    {
        return $this->format('Y-m-d 00:00:00');
    }

    /**
     * returns current MySql-Date
     *
     * This method is intend to return the current MySql-Date.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The requested value
     * @access public
     */
    public function getMySQLDate()
    {
        return $this->format('Y-m-d');
    }

    /**
     * returns current MySql-Compact-Date
     *
     * This method is intend to return the current MySql-Compact-Date.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The requested value
     * @access public
     */
    public function getMySQLCompactDate()
    {
        return $this->format('Ymd');
    }

    /**
     * checks if current Date/Time is NULL
     *
     * This method is intend to check if current Date/Time is NULL.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if NULL, otherwise FALSE
     * @access public
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
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The requested value
     * @access public
     */
    public function getTimeStamp()
    {
        return $this->dateTime->getTimestamp();
    }

    /**
     * returns current Date as MySql Datetime
     *
     * This method is intend to return current Date as MySql Datetime.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The requested value
     * @access public
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
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The requested value
     * @access public
     */
    public function getCurrentDin5008DateTime()
    {
        $now = new self;
        return $now->getDin5008DateTime();
    }

    /**
     * Returns current Date as DIN 5008 Date
     *
     * This method is intend to return current Date as DIN 5008 Date.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The requested value
     * @access public
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
     * @param int $count  The count to use for operation
     * @param bool $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased seconds
     * @access public
     */
    public function addSecond($count = 1, $update = true)
    {
        return $this->add($count, 'second', $update);
    }

    /**
     * subtracts seconds to current date (or new DateTime-Instance)
     *
     * This method is intend to add seconds to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of seconds - if FALSE then
     * a fresh DateTime-Instance is returned with increased seconds.
     *
     * @param int $count  The count to use for operation
     * @param bool $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased seconds
     * @access public
     */
    public function subtractSecond($count = 1, $update = true)
    {
        return $this->subtract($count, 'second', $update);
    }

    /**
     * adds minutes to current date (or new DateTime-Instance)
     *
     * This method is intend to add minutes to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of minutes - if FALSE then
     * a fresh DateTime-Instance is returned with increased minutes.
     *
     * @param int $count  The count to use for operation
     * @param bool $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased minutes
     * @access public
     */
    public function addMinute($count = 1, $update = true)
    {
        return $this->add($count, 'minutes', $update);
    }

    /**
     * subtracts minutes to current date (or new DateTime-Instance)
     *
     * This method is intend to add minutes to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of minutes - if FALSE then
     * a fresh DateTime-Instance is returned with increased minutes.
     *
     * @param int $count  The count to use for operation
     * @param bool $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased minutes
     * @access public
     */
    public function subtractMinute($count = 1, $update = true)
    {
        return $this->subtract($count, 'minute', $update);
    }

    /**
     * adds hours to current date (or new DateTime-Instance)
     *
     * This method is intend to add hours to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of hours - if FALSE then
     * a fresh DateTime-Instance is returned with increased hours.
     *
     * @param int $count  The count to use for operation
     * @param bool $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased hours
     * @access public
     */
    public function addHour($count = 1, $update = true)
    {
        return $this->add($count, 'hour', $update);
    }

    /**
     * subtracts hours to current date (or new DateTime-Instance)
     *
     * This method is intend to add hours to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of hours - if FALSE then
     * a fresh DateTime-Instance is returned with increased hours.
     *
     * @param int $count  The count to use for operation
     * @param bool $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased hours
     * @access public
     */
    public function subtractHour($count = 1, $update = true)
    {
        return $this->subtract($count, 'hour', $update);
    }

    /**
     * adds days to current date (or new DateTime-Instance)
     *
     * This method is intend to add days to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of days - if FALSE then
     * a fresh DateTime-Instance is returned with increased days.
     *
     * @param int $count  The count to use for operation
     * @param bool $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased days
     * @access public
     */
    public function addDay($count = 1, $update = true)
    {
        return $this->add($count, 'day', $update);
    }

    /**
     * subtracts days to current date (or new DateTime-Instance)
     *
     * This method is intend to add days to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of days - if FALSE then
     * a fresh DateTime-Instance is returned with increased days.
     *
     * @param int $count  The count to use for operation
     * @param bool $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased days
     * @access public
     */
    public function subtractDay($count = 1, $update = true)
    {
        return $this->subtract($count, 'day', $update);
    }

    /**
     * adds week to current date (or new DateTime-Instance)
     *
     * This method is intend to add week to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of week - if FALSE then
     * a fresh DateTime-Instance is returned with increased week.
     *
     * @param int $count  The count to use for operation
     * @param bool $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased week
     * @access public
     */
    public function addWeek($count = 1, $update = true)
    {
        return $this->add($count, 'week', $update);
    }

    /**
     * subtracts weeks to current date (or new DateTime-Instance)
     *
     * This method is intend to add weeks to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of weeks - if FALSE then
     * a fresh DateTime-Instance is returned with increased weeks.
     *
     * @param int $count  The count to use for operation
     * @param bool $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased weeks
     * @access public
     */
    public function subtractWeek($count = 1, $update = true)
    {
        return $this->subtract($count, 'week', $update);
    }

    /**
     * adds month to current date (or new DateTime-Instance)
     *
     * This method is intend to add month to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of month - if FALSE then
     * a fresh DateTime-Instance is returned with increased month.
     *
     * @param int $count  The count to use for operation
     * @param bool $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased month
     * @access public
     */
    public function addMonth($count = 1, $update = true)
    {
        return $this->add($count, 'month', $update);
    }

    /**
     * subtracts month to current date (or new DateTime-Instance)
     *
     * This method is intend to add month to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of month - if FALSE then
     * a fresh DateTime-Instance is returned with increased month.
     *
     * @param int $count  The count to use for operation
     * @param bool $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased month
     * @access public
     */
    public function subtractMonth($count = 1, $update = true)
    {
        return $this->subtract($count, 'month', $update);
    }

    /**
     * adds years to current date (or new DateTime-Instance)
     *
     * This method is intend to add years to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of years - if FALSE then
     * a fresh DateTime-Instance is returned with increased years.
     *
     * @param int $count  The count to use for operation
     * @param bool $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased years
     * @access public
     */
    public function addYear($count = 1, $update = true)
    {
        return $this->add($count, 'year', $update);
    }

    /**
     * subtracts years to current date (or new DateTime-Instance)
     *
     * This method is intend to add years to the current date. If $update is set to TRUE
     * the current Date/Time will be increased by given amount of years - if FALSE then
     * a fresh DateTime-Instance is returned with increased years.
     *
     * @param int $count  The count to use for operation
     * @param bool $update TRUE to update the current DateTime, FALSE to return fresh instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed TRUE on success (update), otherwise fresh DateTime-Instance with increased years
     * @access public
     */
    public function subtractYear($count = 1, $update = true)
    {
        return $this->subtract($count, 'year', $update);
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
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Result of diff in unit given
     * @access public
     */
    public function getDiff($date, $unit = 'd')
    {
        $date = $this->formatDate($date);

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
        $diff = $this->dateTime->diff($dateTime);

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
     * @param int $lastBusinessDay Defines the last business-day of the week (Monday = 1, ...,  Friday = 5 ...)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Result as DateTime-Instance
     * @access  public
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
     * @param float      $microtimeStart The microtime-value as start
     * @param float|bool $microtimeEnd   The microtime-value as end
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return float The difference
     * @access public
     */
    public function getMicrotimeDiff($microtimeStart, $microtimeEnd = false)
    {
        $microtimeEnd = (!$microtimeEnd) ? microtime() : $microtimeEnd;

        list($microtimeStart_dec, $microtimeStart_sec) = explode(' ', $microtimeStart);
        list($microtimeEnd_dec, $microtimeEnd_sec) = explode(' ', $microtimeEnd);

        return (float)sprintf(
            '%0.12f',
            ($microtimeEnd_sec - $microtimeStart_sec + $microtimeEnd_dec - $microtimeStart_dec)
        );
    }

    /**
     * converts a MySQL Datetime Value to a PHP Timestamp (UNIX Timestamp)
     *
     * This method is intend to convert a MySQL Datetime Value to a PHP Timestamp (UNIX Timestamp).
     *
     * @param string $datetime The MySQL Datetime value to convert.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer Timestamp
     * @access public
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
     * @param int $count  The amount to add
     * @param string  $unit   The unit to use for calculation
     * @param bool $update TRUE to upate current date/time with new value, FALSE to return fresh instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed TRUE/FALSE as result of update, otherwise fresh DateTime-Instance
     * @access protected
     */
    protected function add($count, $unit = 'day', $update = true)
    {
        return $this->dateCalculation($count, $unit, $update, '+');
    }

    /**
     * subtracts some value to current date/time
     *
     * This method is intend to subtract some value to current date/time.
     *
     * @param int $count  The amount to subtract
     * @param string  $unit   The unit to use for calculation
     * @param bool $update TRUE to upate current date/time with new value, FALSE to return fresh instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed TRUE/FALSE as result of update, otherwise fresh DateTime-Instance
     * @access protected
     */
    protected function subtract($count, $unit = 'day', $update = true)
    {
        return $this->dateCalculation($count, $unit, $update, '-');
    }

    /**
     * main date/time calculation method
     *
     * This method is intend to operate on date/time.
     *
     * @param int $count     The amount to subtract
     * @param string  $unit      The unit to use for calculation
     * @param bool $update    TRUE to upate current date/time with new value, FALSE to return fresh instance
     * @param string  $operation "+" to add/incread, "-" to subtract/decrease
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed TRUE/FALSE as result of update, otherwise fresh DateTime-Instance
     * @access protected
     */
    protected function dateCalculation($count, $unit, $update, $operation = '+')
    {
        $timestamp = strtotime(
            date(
                'd.m.Y H:i:s',
                strtotime(
                    $this->getDate('Y-m-d H:i:s')
                )
            ) . " ".$operation.$count." ".$unit
        );

        if ($update) {
            return $this->dateTime->setTimestamp($timestamp);
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
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Date/Time formatted
     * @access protected
     */
    protected function format($format = 'd.m.Y H:i:s')
    {
        return date($format, $this->dateTime->getTimestamp());
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
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Date/Time formatted
     * @access protected
     */
    protected function formatDate($date, $format = 'H/i/s/m/d/Y', $separator = '/')
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
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer UNIX-Timestamp
     * @access protected
     */
    protected function _getTimestamp(
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
