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
 * @package    DoozR_Logger
 * @subpackage DoozR_Logger_Abstract
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Class/Singleton/Strict.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Loader/Serviceloader.php';

/**
 * DoozR - Logger - Abstract
 *
 * Abstract-Logger base for logger of the DoozR framework
 *
 * @category   DoozR
 * @package    DoozR_Logger
 * @subpackage DoozR_Logger_Abstract
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
abstract class DoozR_Logger_Abstract extends DoozR_Base_Class_Singleton_Strict
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
    protected $version = '$Rev$';

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
     * The last calls log-type
     *
     * @var string
     * @access protected
     */
    protected $contentType;

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
     * The instance of the datetime module required for time/date calculations
     *
     * @var object
     * @access protected
     */
    protected $dateTime;

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
     * An instance of DoozR_Config
     *
     * @var DoozR_Config
     * @access protected
     * @static
     */
    protected static $config;

    /**
     * The translation from [type to level]
     *
     * @var integer
     * @access const
     */
    protected $logtypes = array(
        'UNCLASSIFIED' => 9, // the unclassified php messages
        'FINE'         => 8, // java like logging
        'FINER'        => 8,
        'FINEST'       => 8,
        'TRACE'        => 7, // trace + logger messages
        'DEBUG'        => 7,
        'INFO'         => 7,
        'LOG'          => 7,
        'IMPORTANT'    => 3,
        'WARN'         => 2, // warnings
        'WARNING'      => 2,
        'EXCEPTION'    => 1, // concrete errors
        'ERROR'        => 1,
        'FATAL'        => 1,
        'SEVERE'       => 1
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
     * @access protected
     */
    protected function __construct($level = 1, $fingerprint = '', $config = null)
    {
        // get datetime module
        $this->dateTime = DoozR_Loader_Serviceloader::load('datetime');

        // set date
        $this->date = $this->dateTime->getDate('c');

        // store config
        self::$config = $config;

        // store fingerprint
        $this->fingerprint = $fingerprint;

        // set line seperator
        $this->lineSeparator = str_repeat('-', $this->lineWidth);
    }

    /**
     * logs a given message/content
     *
     * This method is intend to log a message.
     *
     * @param string $content  The content/text/message to log
     * @param string $type     The type of the log-entry
     * @param string $file     The filename of the file from where the log entry comes
     * @param string $line     The linenumber from where log-entry comes
     * @param string $class    The classname from the class where the log-entry comes
     * @param string $method   The methodname from the method where the log-entry comes
     * @param string $function The functionname from the function where the log-entry comes
     * @param string $optional The optional content/param to log
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function log(
        $content = '',
        $type = 'LOG',
        $file = false,
        $line = false,
        $class = false,
        $method = false,
        $function = false,
        $optional = false
    ) {
        // check if we should log this
        if ($this->typeToLevel($type) <= $this->level) {
            // store the type
            $this->contentType = $type;

            // add raw content for collection
            $this->concatRaw('type', $type);

            // get given log content (array / object) as string
            $content = $this->string($content);
            $content = wordwrap($content, $this->lineWidth, $this->lineBreak, true);
            $this->concatRaw('content', $content);

            // log date time
            $time = $this->date.' ['.$this->dateTime->getMicrotimeDiff($_SERVER['REQUEST_TIME']).']';
            $this->concat($time);
            $this->concatRaw('time', $time);

            // add fingerprint of client (to identify logs from same client)
            $fingerprint = '['.$this->fingerprint.']';
            $this->concat($fingerprint);
            $this->concatRaw('fingerprint', $fingerprint);

            // add main log-content (information)
            $this->concat('['.$type.'] '.$content);

            // add file if given
            $this->concat($file, 'File');
            $this->concatRaw('file', $file);

            // add line if given
            $this->concat($line, 'Line');
            $this->concatRaw('line', $line);

            // add class if given
            $this->concat($class, 'Class');
            $this->concatRaw('class', $class);

            // add method if given
            $this->concat($method, 'Method');
            $this->concatRaw('method', $method);

            // add function if given
            $this->concat($function, 'Function');
            $this->concatRaw('function', $function);

            // add optional information if given
            $this->concat($optional, 'Optional');
            $this->concatRaw('optional', $optional);

            // add separator
            $this->separate();

            // store raw content to in raw collection
            $this->storeRaw();

            // write log content append
            $this->output();
        }

        // return always success
        return true;
    }

    /**
     * stores the current raw log-content
     *
     * This method is intend to store the current raw log-content in raw log-content collection.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function storeRaw()
    {
        // store
        $this->collectionRaw[] = $this->contentRaw;

        // reset/clear
        $this->contentRaw = array();
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
    protected function concatRaw($what, $string)
    {
        $this->contentRaw[$what] = $string;
    }

    /**
     * injects log-content from an other logger (e.g. collecting logger) to the concrete
     * logger extending this abstract class
     *
     * This method is intend to inject log-content from an other logger (e.g. collecting logger) to the concrete
     * logger extending this abstract class.
     *
     * @param array $rawCollection The raw collection with log-information
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean Always true
     * @access protected
     */
    protected function inject(array $rawCollection)
    {
        // iterate over raw collection and process
        foreach ($rawCollection as $entry) {
            // check if we should log this
            if ($this->typeToLevel($entry['type']) <= $this->level) {

                // iterate over log content and concat content
                foreach ($entry as $key => $value) {
                    $this->concatRaw($key, $value);
                }

                // inject last type
                $this->contentType = $entry['type'];

                // inject time
                $this->concat($entry['time']);

                // inject fingerprint
                $this->concat($entry['fingerprint']);

                // add main log-content (information)
                $this->concat('['.$entry['type'].'] '.$entry['content']);

                // inject file if given
                $this->concat($entry['file'], 'File');

                // inject line if given
                $this->concat($entry['line'], 'Line');

                // inject class if given
                $this->concat($entry['class'], 'Class');

                // inject method if given
                $this->concat($entry['method'], 'Method');

                // inject function if given
                $this->concat($entry['function'], 'Function');

                // inject optional information if given
                $this->concat($entry['optional'], 'Optional');

                // add separator
                $this->separate();

                // store raw content to in raw collection
                $this->storeRaw();

                // write log content append
                $this->output();
            }
        }

        // success
        return true;
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
     * converts/translates a given type to its level
     *
     * This method is intend to translate a given log-type to its level.
     *
     * @param string $type The type to translate as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The corresponding level
     * @access protected
     */
    protected function typeToLevel($type)
    {
        if (isset($this->logtypes[$type])) {
            return $this->logtypes[$type];
        } else {
            return '0';
        }
    }

    /**
     * converts/translates a given type to its corresponding color
     *
     * This method is intend to convert/translate a given type to its corresponding color.
     *
     * @param string $type The type to translate as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The corresponding color
     * @access protected
     */
    protected function typeToColor($type)
    {
        switch ($type) {
        case 'EXCEPTION':
        case 'ERROR':
        case 'FATAL':
        case 'SEVERE':
        return '#EF4A4A';
        break;

        default:
        return '#7CFC00';
        break;
        }
    }

    /**
     * returns the logger-name
     *
     * This method is intend to return the logger-name.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The name of logger that extends this abstract class
     * @access public
     * @final
     */
    public final function getName()
    {
        return $this->name;
    }

    /**
     * returns the log-content of the last logger-call
     *
     * This method is intend to return the log-content of the last logger-call.
     *
     * @param boolean $returnArray TRUE to retrieve array, false to retrieve string
     * @param boolean $lineBreak   TRUE to break each part of the single log-entry with a line break
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed string if $returnArray false, otherwise array
     * @access public
     * @final
     */
    public final function getContent($returnArray = false, $lineBreak = false)
    {
        // return as string?
        if (!$returnArray) {
            return implode(($lineBreak) ? $this->lineBreak : '', $this->content);
        }

        // return the content as array
        return $this->content;
    }

    /**
     * returns the complete collection of log-content as string or array
     *
     * This method is intend to return the complete collection of log-content as string or array.
     *
     * @param boolean $returnArray True to retrieve array, false to retrieve string
     * @param boolean $returnRaw   True to retrieve the raw content -> not preformatted
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed string if $returnArray false, otherwise array
     * @access public
     */
    public function getCollection($returnArray = false, $returnRaw = false)
    {
        if (!$returnRaw) {
            $content = $this->collection;
        } else {
            $content = $this->collectionRaw;
        }

        // return as string?
        if (!$returnArray) {
            return implode($this->lineBreak, $content);
        }

        // return the content as array
        return $content;
    }

    /**
     * returns the logger's version
     *
     * This method is intend to return the loggers version.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The version of the logger-class that extends this abstract class
     * @access public
     * @final
     */
    public final function getVersion()
    {
        return preg_replace('/\D/', '', $this->version);
    }
    /**
     * concats a given string including it's type to current log-content
     *
     * This method is intend to concat a given string including it's type to current log-content.
     *
     * @param string $content The content to concat
     * @param string $type    The type to concat
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     * @final
     */
    protected final function concat($content = '', $type = false)
    {
        // check for type
        if ($type) {
            $content = $this->format($content, $type);
        }

        // log only if content not empty
        if ($content) {
            // finally append to current log-content
            $this->content[] = $content;

            // and to the collection
            $this->collection[] = $content;
        }
    }

    /**
     * clears (deletes/resets) the current log-content (and collection if $clearCollection set to true)
     *
     * This method is intend to clear (delete/reset) the current log-content
     * (and collection if $clearCollection set to true).
     *
     * @param boolean $clearCollection True to clear the collection too, otherwise false to keep the collection
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @final
     */
    public final function clear($clearCollection = false)
    {
        // check if collection also should be cleared
        if ($clearCollection) {
            // then clear collection
            $this->collection = array();
        }

        // and clear current content always
        $this->content = array();
    }


    /**
     * formats a logging parameter and it's value to a loggable string
     *
     * This method is intend to format a given parameter and it's value to a loggable string.
     *
     * @param string  $content   The content to format
     * @param string  $type      The type to format
     * @param boolean $lineBreak TRUE to use defined line break, FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The formatted string to log
     * @access protected
     */
    protected function format($content = '', $type = '', $lineBreak = false)
    {
        // holds the formatted log entry
        $formatted = '';

        // format only if value if gven
        if (strlen($content)) {
            $formatted = ' '.(strlen($type) ? $type.':' : '').' '.$content.(($lineBreak) ? $this->lineBreak : '');
        }

        // return the formatted log-content
        return $formatted;
    }

    /**
     * abstract output container method
     *
     * This method is intend to write data to a defined pipe like STDOUT, a file, browser ...
     * It should be overriden in concrete implementation.
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
        $content = $this->getContent();

        // so we can clear the existing log
        $this->clear();

        // and echo out the fetched content
        pre($content, false, $color);
    }

    /**
     * serialize array|object for logger
     *
     * serializes (array|object to string) for logger to make them loggable
     *
     * @param mixed $content array|object to convert to string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string serialized array|object if correct type to convert given, otherwise false
     * @access public
     */
    public function string($content)
    {
        // check if not is string ...
        if (!is_string($content)) {
            // ... make string of it
            $content = var_export($content, true);
        }

        // and return it
        return $content;
    }
}
