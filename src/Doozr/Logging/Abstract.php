<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Logging - Abstract.
 *
 * Abstract.php - Abstract-Logging base for logging of the Doozr framework
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
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 *             http://tools.ietf.org/html/rfc5424
 */
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Class.php';

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Doozr - Logging - Abstract.
 *
 * Abstract-Logging base for logging of the Doozr framework
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 *             http://tools.ietf.org/html/rfc5424
 */
abstract class Doozr_Logging_Abstract extends Doozr_Base_Class
    implements
    Countable,
    LoggerInterface
{
    // Default capabilities from library
    use LoggerAwareTrait;

    /**
     * The name of this logging.
     *
     * @var string
     */
    protected $name = 'NAME_NOT_DEFINED';

    /**
     * The version of this logging.
     *
     * @var string
     */
    protected $version = 'Git: $Id$';

    /**
     * The content of the current log call
     * and only this content. if you need access to the complete
     * collection of log-entries have a look at $collection
     * Enter description here ...
     *
     * @var array
     */
    protected $content = [];

    /**
     * The raw content of the current log call.
     *
     * @var array
     */
    protected $contentRaw = [];

    /**
     * The content of all logging call's (collection).
     * if you need only the last call's content have a look at
     * $content.
     *
     * @var array
     */
    protected $collection = [];

    /**
     * The raw log-content collection.
     *
     * @var array
     */
    protected $collectionRaw = [];

    /**
     * The instance of the datetime module required for time/date calculations.
     *
     * @var Doozr_Datetime_Service
     */
    protected $dateTime;

    /**
     * Clean log content.
     *
     * holds the clean content to log. without any special chars (e.g. for use in firePHP-logging)
     *
     * @var string
     */
    protected $logClean = '';

    /**
     * The line separator.
     *
     * holds the line separator which is used to seperate single log entries (e.g. a row of - or *)
     *
     * @var string
     */
    protected $lineSeparator;

    /**
     * The line break char.
     *
     * holds the line break char (e.g. default = \n possible = <br /> or anything else)
     *
     * @var string
     */
    protected $lineBreak = PHP_EOL;

    /**
     * The line width.
     *
     * holds the line width. following our coding standards - the line width default = 120 char
     *
     * @var int
     */
    protected $lineWidth = 120;

    /**
     * The level specific for this logging.
     *
     * @var int
     */
    protected $level;

    /**
     * Contains the fingerprint of the client used as UId.
     *
     * @var string
     */
    protected $fingerprint;

    /**
     * The current date.
     *
     * @var string
     */
    protected $date;

    /**
     * The translation from [type to level].
     *
     * @example: 'emergency' => 0 means that emergency is level 0
     *           as 'debug' is level 7
     *
     * @var int[]
     */
    protected $availableLogtypes = [
        'emergency' => 7,   // 0,
        'alert'     => 6,   // 1,
        'critical'  => 5,   // 2,
        'error'     => 4,   // 3,
        'warning'   => 3,   // 4,
        'notice'    => 2,   // 5,
        'info'      => 1,   // 6,
        'debug'     => 0,   // 7,
    ];

    /**
     * Archive collection for log entries.
     *
     * @var array
     */
    protected $archive = [];

    /**
     * Controls wether the output should be triggered automatically after each log() call (true)
     * or manually (false).
     *
     * @var bool
     */
    protected $automaticOutput = true;

    /**
     * Constructor.
     *
     * @param null|Doozr_Datetime_Service $datetime    Instance of date/time service
     * @param int                         $level       The log-level of the logging extending this class
     * @param string                      $fingerprint The fingerprint of the client
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return \Doozr_Logging_Abstract
     */
    public function __construct(Doozr_Datetime_Service $datetime, $level = null, $fingerprint = null)
    {
        // Store configuration
        $this->dateTime = $datetime;

        // Set date
        $this->setDate(
            $this->dateTime->getDate('c')
        );

        // Set level to upper bound (max) if not passed
        $this->setLevel(
            ($level !== null) ? $level : 0
        );

        // Store fingerprint
        $this->setFingerprint(
            ($fingerprint !== null) ? $fingerprint : $this->generateFingerprint()
        );

        // Set line separator
        $this->setLineSeparator(
            str_repeat('-', $this->lineWidth)
        );
    }

    /*------------------------------------------------------------------------------------------------------------------
    | API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Logs a given message/content.
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
     *
     * @return bool TRUE on success, otherwise FALSE
     *
     * @throws Doozr_Logging_InvalidArgumentException
     */
    public function log(
              $type,
              $message,
        array $context = [],
              $time = null,
              $fingerprint = null,
              $separator = null
    ) {
        // Prevent misuse
        if (false === array_key_exists($type, $this->availableLogtypes)) {
            throw new Doozr_Logging_InvalidArgumentException(
                sprintf('Invalid log type "%s" passed to "%s".',  $type, __METHOD__)
            );
        }

        // Check if we should log this
        if ($this->getLevelByType($type) >= $this->level) {

            // Get given log content (array / object) as string
            $message = $this->interpolate($this->string($message), $context);

            // Message
            $message = wordwrap($message, $this->lineWidth, $this->lineBreak, true);

            // Log date time
            $time = ($time !== null) ?
                $time :
                $this->date.' ['.$this->dateTime->getMicrotimeDiff($_SERVER['REQUEST_TIME']).']';

            // Add fingerprint of client (to identify logs from same client)
            $fingerprint = ($fingerprint !== null) ? $fingerprint : '['.$this->fingerprint.']';

            // Format separator
            $separator = ($separator !== null) ? $separator : $this->getLineSeparator();

            // this is one accumulated log-entry
            $logEntry = [
                'type'        => $type,
                'message'     => $message,
                'context'     => serialize($context),
                'time'        => $time,
                'fingerprint' => $fingerprint,
                'separator'   => $separator,
            ];

            // Store the log-entry as whole string = one string
            $this->concat(
                implode(' ', $logEntry)
            );

            // Store the array as
            $this->concatRaw(
                $logEntry
            );

            if (true === $this->automaticOutput) {
                $this->output();
            }
        }

        // return always success
        return true;
    }

    /**
     * Returns the collection of the logging.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Returns the raw collection of the logging.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The raw collection
     */
    public function getCollectionRaw()
    {
        return $this->collectionRaw;
    }

    /**
     * Returns the content of the logging.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The content
     */
    final public function getContent()
    {
        return $this->content;
    }

    /**
     * Returns the raw content of the logging.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The raw content
     */
    public function getContentRaw()
    {
        return $this->contentRaw;
    }

    /**
     * Clears the collections - Both the raw and the normal one.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    final public function clearCollection()
    {
        $reset = [
            &$this->collection,
            &$this->collectionRaw,
        ];

        foreach ($reset as &$property) {
            $property = [];
        }
    }

    /**
     * Clears the contents - Both the raw and the normal one.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    final public function clearContent()
    {
        $reset = [
            &$this->content,
            &$this->contentRaw,
        ];

        foreach ($reset as &$property) {
            $property = [];
        }
    }

    /**
     * Clears the whole log contents - The collection and the content.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    final public function clear()
    {
        //$this->history($this->getContentRaw());
        $this->clearContent();
        $this->clearCollection();
    }

    /**
     * Archives an entry indexed by hash.
     *
     * @param string $hash  The hash used as identifier
     * @param mixed  $entry The entry to archive
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setArchive($hash, $entry)
    {
        $this->archive[$hash] = $entry;
    }

    /**
     * Fluent proxy for setArchive().
     *
     * @param string $hash  The hash used as identifier
     * @param mixed  $entry The entry to archive
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function archive($hash, $entry)
    {
        $this->setArchive($hash, $entry);

        return $this;
    }

    /**
     * Getter for archive.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The archive collection
     */
    public function getArchive()
    {
        return $this->archive;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | SETTER, GETTER, ADDER, REMOVER, ISSER & HASSER
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for fingerprint.
     *
     * @param string $fingerprint The fingerprint to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setFingerprint($fingerprint)
    {
        $this->fingerprint = $fingerprint;
    }

    /**
     * Getter for fingerprint.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string Fingerprint
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
     */
    public function setLineSeparator($lineSeparator)
    {
        $this->lineSeparator = $lineSeparator;
    }

    /**
     * Getter for lineSeparator.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string lineSeparator
     */
    protected function getLineSeparator()
    {
        return $this->lineSeparator;
    }

    /**
     * Setter for level.
     *
     * @param int $level The level to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * Getter for level.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int level
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
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Getter for date.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string date
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
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Getter for name.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The name of logging that extends this abstract class
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
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Getter for version.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The version of the logging-class that extends this abstract class
     */
    public function getVersion()
    {
        return preg_replace('/\D/', '', $this->version);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL HELPER / TOOLS
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Generates a fingerprint.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string Generated fingerprint
     */
    protected function generateFingerprint()
    {
        return sha1(serialize($_SERVER));
    }

    /**
     * adds the defined line-separator to log-content.
     *
     * This method is intend to add the defined line-separator to log-content.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function separate()
    {
        $this->concat($this->lineSeparator.$this->lineBreak);
    }

    /**
     * Returns the level (integer) for a passed type.
     *
     * This method is intend to encrypt a given log-type to its level.
     *
     * @param string $type The type to encrypt as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int The corresponding level
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
     *
     * @return string The corresponding color hex-code
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
     * This method use var_export PHP function.
     *
     * @param string $content The content to convert
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string serialized The result as string
     */
    protected function string($content)
    {
        // Check if not is string ...
        if (false === is_string($content)) {
            // ... and make string of it.
            $content = var_export($content, true);
        }

        return $content;
    }

    /**
     * Adds the passed content to content & collection.
     *
     * @param string $logEntry The content to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function concat($logEntry)
    {
        // finally append to current log-content
        $this->content[] = $logEntry;

        // and to the collection
        $this->collection[] = $logEntry;
    }

    /**
     * Concat the given raw log-content to current raw log-content array.
     *
     * @param array $array The array to concat
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function concatRaw(array $array)
    {
        $this->contentRaw[] = $array;

        // and to the collection
        $this->collectionRaw[] = $array;
    }

    /**
     * Formats the passed string with the passed type and the line-break to a complete log-line.
     *
     * @param string $string    The string to be formatted
     * @param string $type      The type to be formatted
     * @param bool   $lineBreak TRUE to use defined line break, FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The formatted string to log
     */
    protected function format($string = '', $type = '', $lineBreak = false)
    {
        // Formatted log entry
        $formatted = $string;

        // Format only if value is passed
        if (isset($string[1])) {
            $formatted = ' '.(strlen($type) ? $type.':' : '').' '.$string.
                (($lineBreak) ? $this->lineBreak : '');
        }

        // return the formatted log-content
        return $formatted;
    }

    /**
     * Basic output method which takes the current content and displays it via pre().
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function output()
    {
        // first get content to local var
        $content = $this->getContentRaw();

        // iterate log content
        foreach ($content as $logEntry) {
            // Get color
            $color = $this->getColorByType($logEntry['type']);

            // Show the message
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

        // So we can clear the existing log
        $this->clear();
    }

    /**
     * Interpolates context values into the message placeholders.
     *
     * @param string $message The message to log
     * @param array  $context The context (e.g. template variables)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string TRUE on success, otherwise FALSE
     */
    protected function interpolate($message, array $context = [])
    {
        // build a replacement array with braces around the context keys
        $replace = [];

        foreach ($context as $key => $val) {
            $replace['{'.$key.'}'] = $val;
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PSR-3 Interface
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * System is unusable.
     *
     * @param string $message The message to log
     * @param array  $context The context (e.g. template variables)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance of this class for chaining
     */
    public function emergency($message, array $context = [])
    {
        $this->log(Doozr_Logging_Constant::EMERGENCY, $message, $context);

        return $this;
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
     *
     * @return $this Instance of this class for chaining
     */
    public function alert($message, array $context = [])
    {
        $this->log(Doozr_Logging_Constant::ALERT, $message, $context);

        return $this;
    }

    /**
     * Critical conditions.
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message The message to log
     * @param array  $context The context (e.g. template variables)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance of this class for chaining
     */
    public function critical($message, array $context = [])
    {
        $this->log(Doozr_Logging_Constant::CRITICAL, $message, $context);

        return $this;
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message The message to log
     * @param array  $context The context (e.g. template variables)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance of this class for chaining
     */
    public function error($message, array $context = [])
    {
        $this->log(Doozr_Logging_Constant::ERROR, $message, $context);

        return $this;
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
     *
     * @return $this Instance of this class for chaining
     */
    public function warning($message, array $context = [])
    {
        $this->log(Doozr_Logging_Constant::WARNING, $message, $context);

        return $this;
    }

    /**
     * Normal but significant events.
     *
     * @param string $message The message to log
     * @param array  $context The context (e.g. template variables)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance of this class for chaining
     */
    public function notice($message, array $context = [])
    {
        $this->log(Doozr_Logging_Constant::NOTICE, $message, $context);

        return $this;
    }

    /**
     * Interesting events.
     * Example: User logs in, SQL logs.
     *
     * @param string $message The message to log
     * @param array  $context The context (e.g. template variables)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance of this class for chaining
     */
    public function info($message, array $context = [])
    {
        $this->log(Doozr_Logging_Constant::INFO, $message, $context);

        return $this;
    }

    /**
     * Detailed debug information.
     *
     * @param string $message The message to log
     * @param array  $context The context (e.g. template variables)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance of this class for chaining
     */
    public function debug($message, array $context = [])
    {
        $this->log(Doozr_Logging_Constant::DEBUG, $message, $context);

        return $this;
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Fulfill Countable
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the count of elements in registry.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int The result of the operation
     */
    public function count()
    {
        return count($this->content);
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Abstract Requirements
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Dispatches a new route to this logging (e.g. for use as new filename).
     *
     * @param string $name The name of the route to dispatch
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool True on success, otherwise false
     */
    abstract public function route($name);
}
