<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Logger - Abstract
 *
 * Abstract.php - Abstract-Logger base for logger of the DoozR framework
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2014, Benjamin Carl - All rights reserved.
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
 * @package    DoozR_Logger
 * @subpackage DoozR_Logger_Abstract
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 *             http://tools.ietf.org/html/rfc5424
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Class.php';

/**
 * DoozR - Logger - Abstract
 *
 * Abstract-Logger base for logger of the DoozR framework
 *
 * @category   DoozR
 * @package    DoozR_Logger
 * @subpackage DoozR_Logger_Abstract
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 *             http://tools.ietf.org/html/rfc5424
 */
abstract class DoozR_Logger_Abstract extends DoozR_Base_Class
    implements
    Countable
{
    /**
     * The name of this logger
     *
     * @var string
     * @access protected
     */
    protected $name = 'NAME_NOT_DEFINED';

    /**
     * The version of this logger
     *
     * @var string
     * @access protected
     */
    protected $version = 'Git: $Id$';

    /**
     * The content of the current log call
     * and only this content. if you need access to the complete
     * collection of log-entries have a look at $collection
     * Enter description here ...
     *
     * @var array
     * @access protected
     */
    protected $content = array();

    /**
     * The raw content of the current log call
     *
     * @var array
     * @access protected
     */
    protected $contentRaw = array();

    /**
     * The content of all logger call's (collection).
     * if you need only the last call's content have a look at
     * $content.
     *
     * @var array
     * @access protected
     */
    protected $collection = array();

    /**
     * The raw log-content collection
     *
     * @var array
     * @access protected
     */
    protected $collectionRaw = array();

    /**
     * The instance of the datetime module required for time/date calculations
     *
     * @var DoozR_Datetime_Service
     * @access protected
     */
    protected $dateTime;

    /**
     * Clean log content
     *
     * holds the clean content to log. without any special chars (e.g. for use in firePHP-logger)
     *
     * @var string
     * @access protected
     */
    protected $logClean = '';

    /**
     * The line separator
     *
     * holds the line separator which is used to seperate single log entries (e.g. a row of - or *)
     *
     * @var string
     * @access protected
     */
    protected $lineSeparator;

    /**
     * The line break char
     *
     * holds the line break char (e.g. default = \n possible = <br /> or anything else)
     *
     * @var string
     * @access protected
     */
    protected $lineBreak = "\n";

    /**
     * The line width
     *
     * holds the line width. following our coding standards - the line width default = 120 char
     *
     * @var integer
     * @access protected
     */
    protected $lineWidth = 120;

    /**
     * The level specific for this logger
     *
     * @var integer
     * @access private
     */
    protected $level;

    /**
     * Contains the fingerprint of the client used as UId
     *
     * @var string
     * @access protected
     */
    protected $fingerprint;

    /**
     * The current date
     *
     * @var string
     * @access protected
     */
    protected $date;

    /**
     * The translation from [type to level]
     *
     * @example: 'emergency' => 0 means that emergency is level 0
     *           as 'debug' is level 7
     *
     * @var integer
     * @access const
     */
    protected $availableLogtypes = array(
        'emergency' => 7,   // 0,
        'alert'     => 6,   // 1,
        'critical'  => 5,   // 2,
        'error'     => 4,   // 3,
        'warning'   => 3,   // 4,
        'notice'    => 2,   // 5,
        'info'      => 1,   // 6,
        'debug'     => 0,   // 7,
    );


    /**
     * This method is the constructor and responsible for building the instance.
     *
     * @param integer      $level       The loglevel of the logger extending this class
     * @param string       $fingerprint The fingerprint of the client
     * @param DoozR_Config $config      The configuration instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __construct(DoozR_Datetime_Service $datetime, $level = null, $fingerprint = null)
    {
        // store config
        $this->dateTime = $datetime;

        // set date
        $this->setDate(
            $this->dateTime->getDate('c')
        );

        // set level to upper bound (max) if not passed
        $this->setLevel(
            ($level !== null) ? $level : 0
        );

        // store fingerprint
        $this->setFingerprint(
            ($fingerprint !== null) ? $fingerprint : $this->generateFingerprint()
        );

        // set line seperator
        $this->setLineSeparator(
            str_repeat('-', $this->lineWidth)
        );
    }

    /*------------------------------------------------------------------------------------------------------------------
    | API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Logs a given message/content
     *
     * This method is intend to log a message.
     *
     * @param string $type        The level/type of the log entry
     * @param string $message     The message to log
     * @param array  $context     The context with variables used for interpolation
     * @param string $time        The time to use for logging
     * @param string $fingerprint The fingerprint to use/set
     * @param string $separator   The separator to use/set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     * @throws DoozR_Logger_InvalidArgumentException
     */
    public function log(
        $type,
        $message,
        array $context = array(),
        $time          = null,
        $fingerprint   = null,
        $separator     = null
    ) {
        // prevent misuse
        if (!array_key_exists($type, $this->availableLogtypes)) {
            throw new DoozR_Logger_InvalidArgumentException('Invalid log type: '.$type.' passed to '.__METHOD__);
        }

        /*
        echo 'Level by type: '.$this->getLevelByType($type).'<br />';
        echo 'Type: '.$type.'<br />';
        echo '$this->level: '.$this->level.'<br />';
        echo '$this->name: '.$this->getName(). '<br />';
        */

        // check if we should log this
        if ($this->getLevelByType($type) >= $this->level) {

            // get given log content (array / object) as string
            $message = $this->interpolate(
                $this->string($message),
                $context
            );

            // message
            $message = wordwrap($message, $this->lineWidth, $this->lineBreak, true);

            // log date time
            $time = ($time !== null) ?
                $time :
                $this->date.' ['.$this->dateTime->getMicrotimeDiff($_SERVER['REQUEST_TIME']).']';

            // add fingerprint of client (to identify logs from same client)
            $fingerprint = ($fingerprint !== null) ? $fingerprint : '['.$this->fingerprint.']';

            // format separator
            $separator = ($separator !== null) ? $separator : $this->getLineSeparator();

            // this is one accumulated log-entry
            $logEntry = array(
                'type'        => $type,
                'message'     => $message,
                'context'     => serialize($context),
                'time'        => $time,
                'fingerprint' => $fingerprint,
                'separator'   => $separator
            );

            // store the log-entry as whole string = one string
            $this->concat(
                implode(' ', $logEntry)
            );

            // store the array as
            $this->concatRaw(
                $logEntry
            );

            // write log content append
            $this->output();
        }

        // return always success
        return true;
    }

    /**
     * Returns the collection of the logger
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The collection
     * @access public
     */
    public function getCollection($asArray = false)
    {
        return $this->collection;
    }

    /**
     * Returns the raw collection of the logger
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The raw collection
     * @access public
     */
    public function getCollectionRaw()
    {
        return $this->collectionRaw;
    }

    /**
     * Returns the content of the logger
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The content
     * @access public
     */
    public final function getContent($asArray = false)
    {
        return $this->content;
    }

    /**
     * Returns the raw content of the logger
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The raw content
     * @access public
     */
    public function getContentRaw()
    {
        return $this->contentRaw;
    }

    /**
     * Clears the collections - Both the raw and the normal one.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public final function clearCollection()
    {
        $reset = array(
            &$this->collection,
            &$this->collectionRaw,
        );

        foreach ($reset as &$property) {
            $property = array();
        }
    }

    /**
     * Clears the contents - Both the raw and the normal one.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public final function clearContent()
    {
        $reset = array(
            &$this->content,
            &$this->contentRaw,
        );

        foreach ($reset as &$property) {
            $property = array();
        }
    }

    /**
     * Clears the whole log contents - The collection and the content.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public final function clear()
    {
        $this->clearContent();
        $this->clearCollection();
    }

    /*------------------------------------------------------------------------------------------------------------------
    | GETTER & SETTER
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for fingerprint.
     *
     * @param string $fingerprint The fingerprint to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setFingerprint($fingerprint)
    {
        $this->fingerprint = $fingerprint;
    }

    /**
     * Getter for fingerprint.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Fingerprint
     * @access public
     */
    public function getFingerprint()
    {
        return $this->fingerprint;
    }

    /**
     * Setter for lineSeparator.
     *
     * @param string $lineSeparator The lineSeparator to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setLineSeparator($lineSeparator)
    {
        $this->lineSeparator = $lineSeparator;
    }

    /**
     * Getter for lineSeparator.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string lineSeparator
     * @access protected
     */
    protected function getLineSeparator()
    {
        return $this->lineSeparator;
    }

    /**
     * Setter for level.
     *
     * @param integer $level The level to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * Getter for level.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer level
     * @access public
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Setter for date.
     *
     * @param string $date The date to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Getter for date.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string date
     * @access public
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Setter for name.
     *
     * @param string $name The name to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Getter for name.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The name of logger that extends this abstract class
     * @access public
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Setter for version.
     *
     * @param string $version The version to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Getter for version.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The version of the logger-class that extends this abstract class
     * @access public
     */
    public function getVersion()
    {
        return preg_replace('/\D/', '', $this->version);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL HELPER / TOOLS
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Generates a fingerprint
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Generated fingerprint
     * @access protected
     */
    protected function generateFingerprint()
    {
        return sha1(serialize($_SERVER));
    }

    /**
     * adds the defined line-separator to log-content
     *
     * This method is intend to add the defined line-separator to log-content.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function separate()
    {
        $this->concat($this->lineSeparator.$this->lineBreak);
    }

    /**
     * Returns the level (integer) for a passed type.
     *
     * This method is intend to translate a given log-type to its level.
     *
     * @param string $type The type to translate as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The corresponding level
     * @access protected
     */
    protected function getLevelByType($type)
    {
        $level = 0;

        if (isset($this->availableLogtypes[$type])) {
            $level = $this->availableLogtypes[$type];
        }

        return $level;
    }

    /**
     * Returns the color hex-code for a passed type.
     *
     * @param string $type The type to return color hex-code for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The corresponding color hex-code
     * @access protected
     */
    protected function getColorByType($type)
    {
        switch ($type) {
            case 'emergency':
            case 'alert':
            case 'critical';
            case 'error':
            case 'warning':
            case 'notice':
                return '#EF4A4A';
                break;

            case 'info':
            case 'debug':
                return '#EEEEEE';
                break;

            default:
                return '#7CFC00';
                break;
        }
    }

    /**
     * Takes the passed content and return it as string.
     * This method instrumentalizes the var_export PHP function.
     *
     * @param mixed|array|object $content The content to convert
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string serialized The result as string
     * @access protected
     */
    protected function string($content)
    {
        // check if not is string ...
        if (!is_string($content)) {
            // ... make string of it
            $content = var_export($content, true);
        }

        // and return it
        return $content;
    }

    /**
     * Adds the passed content to content & collection.
     *
     * @param string $logentry The content to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function concat($logentry)
    {
        // finally append to current log-content
        $this->content[] = $logentry;

        // and to the collection
        $this->collection[] = $logentry;
    }

    /**
     * concats the given raw log-content to curent raw log-content array
     *
     * This method is intend to concat the given raw log-content to curent raw log-content array.
     *
     * @param string $what   The type to store
     * @param string $string The content to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function concatRaw(array $array)
    {
        $this->contentRaw[] = $array;

        // and to the collection
        $this->collectionRaw[] = $array;
    }

    /**
     * Formats the passed string with the passed type and the line-break
     * to a complete log-line.
     *
     * @param string  $content   The string to be formatted
     * @param string  $type      The type to be formatted
     * @param boolean $lineBreak TRUE to use defined line break, FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The formatted string to log
     * @access protected
     */
    protected function format($string = '', $type = '', $lineBreak = false)
    {
        // holds the formatted log entry
        $formatted = $string;

        // format only if value is passed
        if (isset($string[1])) {
            $formatted = ' '.(strlen($type) ? $type.':' : '').' '.$string.(($lineBreak) ? $this->lineBreak : '');
        }

        // return the formatted log-content
        return $formatted;
    }

    /**
     * Basic output method which takes the current content and displays
     * it via pre().
     *
     * @param string $color The color of the output as hexadecimal string representation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function output($color = '#7CFC00')
    {
        // first get content to local var
        $content = $this->getContentRaw();

        // iterate log content
        foreach ($content as $logEntry) {

            // get color
            $color = $this->getColorByType($logEntry['type']);

            // show the message
            pre(
                $logEntry['time'].' '.
                '['.$logEntry['type'].'] '.
                $logEntry['fingerprint'].' '.
                $logEntry['message'].
                $this->getLineSeparator(),
                false,
                $color
            );
        }

        // so we can clear the existing log
        $this->clear();
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PSR-3 Interface
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Interpolates context values into the message placeholders.
     *
     * @param string $message The message to log
     * @param array  $context The context (e.g. template variables)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    protected function interpolate($message, array $context = array())
    {
        // build a replacement array with braces around the context keys
        $replace = array();

        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

    /**
     * System is unusable.
     *
     * @param string $message The message to log
     * @param array  $context The context (e.g. template variables)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function emergency($message, array $context = array())
    {
        return $this->log(DoozR_Logger_Constant::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message The message to log
     * @param array  $context The context (e.g. template variables)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function alert($message, array $context = array())
    {
        return $this->log(DoozR_Logger_Constant::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message The message to log
     * @param array  $context The context (e.g. template variables)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function critical($message, array $context = array())
    {
        return $this->log(DoozR_Logger_Constant::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message The message to log
     * @param array  $context The context (e.g. template variables)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function error($message, array $context = array())
    {
        return $this->log(DoozR_Logger_Constant::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message The message to log
     * @param array  $context The context (e.g. template variables)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function warning($message, array $context = array())
    {
        return $this->log(DoozR_Logger_Constant::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message The message to log
     * @param array  $context The context (e.g. template variables)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function notice($message, array $context = array())
    {
        return $this->log(DoozR_Logger_Constant::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     * Example: User logs in, SQL logs.
     *
     * @param string $message The message to log
     * @param array  $context The context (e.g. template variables)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function info($message, array $context = array())
    {
        return $this->log(DoozR_Logger_Constant::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message The message to log
     * @param array  $context The context (e.g. template variables)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function debug($message, array $context = array())
    {
        return $this->log(DoozR_Logger_Constant::DEBUG, $message, $context);
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Fulfill Countable
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the count of elements in registry
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function count()
    {
        return count($this->content);
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Abstract Requirements
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Dispatches a new route to this logger (e.g. for use as new filename).
     *
     * @param string $name The name of the route to dispatch
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean True on success, otherwise false
     * @access public
     */
    abstract public function route($name);
}
