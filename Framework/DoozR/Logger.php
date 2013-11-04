<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Logger - Composite
 *
 * Logger.php - This logger is the composite for accessing the logging-
 * subsystem of the DoozR-Framework. This logger is the main entry point
 * for all log-content. This logger takes any log and dispatch this to
 * the attached loggers.
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
 * @subpackage DoozR_Logger_Composite
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Logger/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Logger/Interface.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Logger/Constant.php';

/**
 * DoozR - Logger - Composite
 *
 * This logger is the composite for accessing the logging-
 * subsystem of the DoozR-Framework. This logger is the main entry point
 * for all log-content. This logger takes any log and dispatch this to
 * the attached loggers.
 *
 * @category   DoozR
 * @package    DoozR_Logger
 * @subpackage DoozR_Logger_Composite
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        Abstract.php, Interface.php
 */
final class DoozR_Logger extends DoozR_Logger_Abstract
    implements
    DoozR_Logger_Interface,
    SplSubject,
    ArrayAccess,
    Iterator,
    Countable
{
    /**
     * The observer storage.
     *
     * @var SplObjectStorage
     * @access protected
     */
    protected $observer;

    /**
     * The default log level from config (once set)
     *
     * @var integer
     * @access protected
     */
    protected $defaultLoglevel;

    /**
     * Name of this logger
     *
     * @var string
     * @access protected
     */
    protected $name = 'Composite';

    /**
     * Version of this logger
     *
     * @var string
     * @access protected
     */
    protected $version = 'Git: $Id$';

    /**
     * The position of the iterator for iterating
     * elements.
     *
     * @var integer
     * @access protected
     */
    protected $position = 0;


    /**
     * Constructor.
     *
     * @param DoozR_Datetime_Service $datetime    Instance of Datetime-Service for date-operations
     * @param integer|null           $level       The logger level | if not passed the max is set
     * @param string|null            $fingerprint The fingerprint of the current client|will be
     *                                            generated if not passed
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __construct(DoozR_Datetime_Service $datetime = null, $level = null, $fingerprint = null)
    {
        // instanciate the SplObjectStorage
        $this->observer = new SplObjectStorage();

        // and then call the original constructor
        parent::__construct($datetime, $level, $fingerprint);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Dispatches a passed message and all arguments to attached loggers.
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
     */
    public function log(
        $type,
        $message,
        array $context = array(),
        $time          = null,
        $fingerprint   = null,
        $separator     = null
    ) {
        // call parents log just as normal => so content, raw ... gets filled
        parent::log($type, $message, $context, $time, $fingerprint, $separator);

        // and now the tricky hook -> notify all observers about the log-event
        $this->notify('log');

        // return always success
        return true;
    }

    /**
     * Removes all attached loggers. Optionally it removes
     * all contents as well.
     *
     * @param boolean $clearContents TRUE to remove all content from logger,
     *                               FALSE to keep it
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function detachAll($clearContents = false)
    {
        $this->observer = new SplObjectStorage();

        if ($clearContents === true) {
            $this->clear();
        }
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Getter & Setter
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns a logger by its name
     *
     * @param string $name The name of the logger
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Logger_Interface|null The logger if exist, otherwise NULL
     * @access public
     */
    public function getLogger($name)
    {
        foreach ($this->observer as $logger) {
            if (strtolower($logger->getName()) === strtolower($name)) {
                return $logger;
            }
        }

        return null;
    }

    /**
     * This method is intend to set the default log-level.
     *
     * @param integer $level The log-level to set as standard
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setDefaultLoglevel($level)
    {
        $this->defaultLoglevel = $level;
    }

    /**
     * This method is intend to return the default log-level.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The default log-level
     * @access public
     */
    public function getDefaultLoglevel()
    {
        return $this->defaultLoglevel;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Fulfill SplSubject
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Attaches an observer instance.
     *
     * @param SplObserver $observer The observer to attach
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function attach(SplObserver $observer)
    {
        $this->observer->attach($observer);
    }

    /**
     * Detaches an observer instance.
     *
     * @param SplObserver $observer The observer to detach
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function detach(SplObserver $observer)
    {
        $this->observer->detach($observer);
    }

    /**
     * Notifies the attached observers about changes.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function notify($event = null)
    {
        foreach ($this->observer as $observer) {
            $observer->update($this, $event);
        }

        $this->clear();
        #$this->clearContent();
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Internal Tools & Helpers
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Suppress the output.
     * This logger implementation is Composite and does not echo any
     * log-content it just dispatch the stuff to its attached observers.
     *
     * @param string $color The color of output text as hex-value string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE as dummy return value
     * @access protected
     */
    protected function output($color = '#7CFC00')
    {
        // return true -> cause not needed for collecting logger
        return true;
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Fulfill ArrayAccess
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the TRUE if the passed offset exists otherwise FALSE
     *
     * @param mixed $offset The offset to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function offsetExists($offset)
    {
        foreach ($this->observer as $observer) {
            if (strtolower($observer->getName()) === strtolower($offset)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the value for the passed offset
     *
     * @param mixed $offset The offset to return value for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function offsetGet($offset)
    {
        foreach ($this->observer as $observer) {
            if (strtolower($observer->getName()) === strtolower($offset)) {
                return $observer;
            }
        }

        return null;
    }

    /**
     * Sets the value for the passed offset
     *
     * @param integer $offset The offset to set value for
     * @param mixed   $value  The value to write
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function offsetSet($offset, $value)
    {
        foreach ($this->observer as $observer) {
            if (strtolower($observer->getName()) === strtolower($offset)) {
                $this->observer->offsetSet($value);
            }
        }
    }

    /**
     * Unsets an offset
     *
     * @param mixed $offset The offset to unset
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function offsetUnset($offset)
    {
        foreach ($this->observer as $observer) {
            if (strtolower($observer->getName()) === strtolower($offset)) {
                $this->observer->detach($observer);
            }
        }

        return null;
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Fulfill Iterator
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Rewinds the position to 0
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Checks if current position is still valid
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function valid()
    {
        return $this->position < count($this->observer);
    }

    /**
     * Returns the key for the current position
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Returns the current element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function current()
    {
        $offset = 0;

        foreach ($this->observer as $observer) {
            if ($offset === $this->position) {
                return $observer;
            }
            ++$offset;
        }
    }

    /**
     * Goes to next element
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the operation
     * @access public
     */
    public function next()
    {
        $this->position++;
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
        return count($this->observer);
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Fulfill Abstract Requirements
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Dispatches a new route to this logger (e.g. for use as new filename).
     *
     * @param string $name The name of the route to dispatch
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function route($name)
    {
        /**
         * This logger does not need to be re-routed
         */
    }
}
