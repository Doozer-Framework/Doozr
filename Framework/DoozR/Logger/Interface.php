<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Logger Interface
 *
 * Interface.php - Logger-Interface-Class of the DoozR-Framework
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
 * @subpackage DoozR_Logger_Interface
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

/**
 * DoozR Logger-Interface
 *
 * Logger-Interface-Class of the DoozR-Framework.
 *
 * @category   DoozR
 * @package    DoozR_Logger
 * @subpackage DoozR_Logger_Interface
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
interface DoozR_Logger_Interface
{
    /**
     * signature for logging of messages
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
     * @return  boolean True if successful, otherwise false
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
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
    );


    /**
     * signature for getVersion
     *
     * This method is intend to return the loggers version.
     *
     * @return  string The version of the logger-class that use this interface
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getVersion();


    /**
     * signature for getContent
     *
     * getContent is responsible for returning the current log-content to caller
     *
     * @param boolean $returnArray True to retrieve array, false to retrieve string
     *
     * @return  mixed string if $returnArray false, otherwise array
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getContent($returnArray = false);


    /**
     * signature for getCollection
     *
     * getCollection is responsible for returning the complete collection of log-content to caller
     *
     * @param boolean $returnArray True to retrieve array, false to retrieve string
     *
     * @return  mixed string if $returnArray false, otherwise array
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getCollection($returnArray = false, $returnRaw = false);


    /**
     * clears (deletes/resets) the current log-content (and collection if $clearCollection set to true)
     *
     * This method is intend to clear (delete/reset) the current log-content
     * (and collection if $clearCollection set to true).
     *
     * @param boolean $clearCollection True to clear the collection too, otherwise false to keep the collection
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function clear($clearCollection = false);


    /**
     * signature for getInstance
     *
     * This method is the interface define for getInstance (singleton-pattern)
     *
     * @param integer $level The specific level for logger implementing this interface
     *
     * @return  object instance of this class
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    //public static function getInstance($level = 1);
}

?>
