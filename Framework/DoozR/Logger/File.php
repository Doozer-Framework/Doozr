<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Logger - File
 *
 * File.php - This logger-implementation is intend to log to the filesystem.
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
 * @subpackage DoozR_Logger_File
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Logger/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Logger/Interface.php';

/**
 * DoozR - Logger - File
 *
 * This logger-implementation is intend to log to the filesystem.
 *
 * @category   DoozR
 * @package    DoozR_Logger
 * @subpackage DoozR_Logger_File
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
final class DoozR_Logger_File extends DoozR_Logger_Abstract implements DoozR_Logger_Interface
{
    /**
     * the name of this logger
     *
     * @var string
     * @access protected
     */
    protected $name = 'File';

    /**
     * the version of this logger
     *
     * @var string
     * @access protected
     */
    protected $version = '$Rev$';

    /*******************************************************************************************************************
     * // BEGIN FILE-LOGGER SPECIFIC VARIABLES
     ******************************************************************************************************************/

    /**
     * the file we log to
     *
     * @var string
     * @access private
     */
    private $_logfile;

    /**
     * use persistence filehandle for log-operation(s)?
     * true to keep the handle opened for the length of each request
     * false to reopen the file (retrieving a handle) for each log entry
     *
     * @var boolean
     * @access private
     */
    private $_persistent = true;

    /**
     * holds the status of "overwrite" or "append" mode
     * True to append log to file, false to overwrite
     *
     * @var boolean
     * @access private
     */
    private $_append = true;

    /**
     * holds the instance of module filesystem
     *
     * @var object
     * @access private
     */
    private $_filesystem = null;

    /**
     * holds the instance of the path-manager
     *
     * @var object
     * @access protected
     */
    private $_path;


    /*******************************************************************************************************************
     * \\ END FILE-LOGGER SPECIFIC VARIABLES
     ******************************************************************************************************************/

    /**
     * This method is the constructor and responsible for building the instance.
     *
     * @param integer $level The level to use for this logger
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    protected function __construct($level, $fingerprint)
    {
        // call parents constructor
        parent::__construct($level, $fingerprint);

        // get instance of path manager
        $this->_path = DoozR_Path::getInstance(DOOZR_DOCUMENT_ROOT);

        // store level
        $this->level = $level;

        // set logfile
        $this->_setLogfile($_SERVER['PHP_SELF']);

        // get module filesystem
        $this->_filesystem = DoozR_Loader_Serviceloader::load('filesystem');
    }

    /**
     * This method is intend to add the defined line-separator to log-content.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function separate()
    {
        // do nothing to seperate in file logger
        return true;
    }

    /**
     * This method is intend to write data to a defined pipe like STDOUT, a file, browser ...
     * It should be overriden in concrete implementation.
     *
     * @param string $color The color of the ouput as hexadecimal string reprensentation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function output($color = '#7CFC00')
    {
        // first get content to local var
        $content = $this->getContent().$this->lineBreak;

        // so we can clear the existing log
        $this->clear();

        // use persistent write to write content to file?
        if ($this->_persistent) {
            $this->_filesystem->pwrite($this->_logfile, $content, $this->_append);
        } else {
            $this->_filesystem->write($this->_logfile, $content, $this->_append);
        }
    }

    /**
     * Dispatches a new route to this logger (e.g. for use as new filename).
     *
     * @param string $name The name of the route to dispatch
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean True on success, otherwise false
     * @access public
     */
    public function route($name)
    {
        // set new logile-name
        return $this->_setLogfile($name);
    }

    /**
     * Setter for file to log to
     *
     * @param string $logfile The filename (including path) to log the content to
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean True on success otherwise false
     * @access private
     */
    private function _setLogfile($logfile)
    {
        // create logfilename
        $logfile = $this->_path->get('log', basename($logfile).'.txt');

        // and set + return result of set
        return ($this->_logfile = $logfile);
    }
}

?>
