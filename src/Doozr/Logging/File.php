<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Logging - File
 *
 * File.php - This logger logs all passed content to a logfile.
 *
 * PHP versions 5.5
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
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
 * @package    Doozr_Logging
 * @subpackage Doozr_Logging_File
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Logging/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Logging/Interface.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Logging/Constant.php';

use Psr\Log\LoggerInterface;

/**
 * Doozr - Logging - File
 *
 * File.php - This logger logs all passed content to a logfile.
 *
 * @category   Doozr
 * @package    Doozr_Logging
 * @subpackage Doozr_Logging_File
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 * @see        Abstract.php, Interface.php
 */
class Doozr_Logging_File extends Doozr_Logging_Abstract
    implements
    Doozr_Logging_Interface,
    LoggerInterface,
    SplObserver
{
    /**
     * Name of this logger
     *
     * @var string
     * @access protected
     */
    protected $name = 'File';

    /**
     * Version of this logger
     *
     * @var string
     * @access protected
     */
    protected $version = '$Id$';

    /*-----------------------------------------------------------------------------------------------------------------+
    | BEGIN FILE-LOGGER SPECIFIC VARIABLES
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * The file we log to
     *
     * @var string
     * @access protected
     */
    protected $logfile;

    /**
     * use persistence filehandle for log-operation(s)?
     * true to keep the handle opened for the length of each request
     * false to reopen the file (retrieving a handle) for each log entry
     *
     * @var bool
     * @access protected
     */
    protected $persistent = true;

    /**
     * holds the status of "overwrite" or "append" runtimeEnvironment
     * True to append log to file, false to overwrite
     *
     * @var bool
     * @access protected
     */
    protected $append = true;

    /**
     * Instance of filesystem service
     *
     * @var object
     * @access protected
     */
    protected $filesystem = null;

    /**
     * Instance of the path-manager
     *
     * @var object
     * @access protected
     */
    protected $path;


    /**
     * Constructor.
     *
     * @param Doozr_Datetime_Service $datetime    Datetime Service of Doozr
     * @param int                    $level       Loglevel of the logger extending this class
     * @param string                 $fingerprint Fingerprint of the client
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \Doozr_Logging_File
     * @access public
     */
    public function __construct(Doozr_Datetime_Service $datetime, $level = null, $fingerprint = null)
    {
        // call parents constructor
        parent::__construct($datetime, $level, $fingerprint);

        // get registry
        $registry = Doozr_Registry::getInstance();

        // store path-manager
        $this->setPath($registry->path);

        // set logfile-name (+path)
        $this->setLogfile($_SERVER['PHP_SELF']);

        // set filesystem service
        $this->setFilesystem(
            Doozr_Loader_Serviceloader::load('filesystem')
        );
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Setter & Getter
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for path.
     *
     * @param Doozr_Path_Interface $path The path manager
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setPath(Doozr_Path_Interface $path)
    {
        $this->path = $path;
    }

    /**
     * Getter for path.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Path|null The path manager instance if set, otherwise NULL
     * @access public
     */
    public function getPathToClass($resolveSymlinks = false)
    {
        return $this->path;
    }

    /**
     * Setter for logfile.
     *
     * @param string $filename The log-filename
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setLogfile($filename)
    {
        $this->logfile = $this->path->get('log', basename($filename).'.txt');
    }

    /**
     * Getter for logfile.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The name of the logfile if set, otherwise NULL
     * @access public
     */
    public function getLogfile()
    {
        return $this->logfile;
    }

    /**
     * Setter for filesystem.
     *
     * @param Doozr_Base_Service_Interface $filesystem The filesystem instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setFilesystem(Doozr_Filesystem_Service $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Getter for filesystem.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Filesystem_Service|null The Doozr_Filesystem_Service instance if set, otherwise NULL
     * @access public
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * Setter for persistent status
     *
     * @param bool $status TRUE = write persistent, FALSE do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setPersistent($status = true)
    {
        $this->persistent = $status;
    }

    /**
     * Getter for persistent status
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if persistent write is on, otherwise FALSE if not
     * @access public
     */
    public function getPersistent()
    {
        return $this->persistent;
    }

    /**
     * Setter for append status
     *
     * @param bool $status TRUE = append, FALSE do not [overwrite]
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setAppend($status = true)
    {
        $this->append = $status;
    }

    /**
     * Getter for append status
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if append is on, otherwise FALSE if not
     * @access public
     */
    public function getAppend()
    {
        return $this->append;
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
        $this->logfile = $name;
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Fulfill SplObserver
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Update of SplObserver
     *
     * @param SplSubject $subject The subject we work on
     * @param null       $event   The event to process (optional)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function update(SplSubject $subject, $event = null)
    {
        switch ($event) {
            case 'log':
                /* @var Doozr_Logging $subject */
                $logs = $subject->getCollectionRaw();

                foreach ($logs as $log) {
                    $this->log(
                        $log['type'],
                        $log['message'],
                        unserialize($log['context']),
                        $log['time'],
                        $log['fingerprint'],
                        $log['separator']
                    );
                }
                break;
        }
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Internal Tools & Helper
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Output method for writing files.
     * We need this method cause it differs here from abstract default.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function output()
    {
        // first get content to local var
        $content = $this->getContentRaw();

        // iterate log content
        foreach ($content as $logEntry) {

            // build the log-line
            $content = $logEntry['time'].' '.
                '['.$logEntry['type'].'] '.
                $logEntry['fingerprint'].' '.
                $logEntry['message'].
                $this->lineBreak.$this->getLineSeparator().$this->lineBreak;

            // use persistent write to write content to file?
            if ($this->getPersistent() === true) {
                $this->getFilesystem()->pwrite($this->getLogfile(), $content, $this->getAppend());

            } else {
                $this->getFilesystem()->write($this->getLogfile(), $content, $this->getAppend());

            }
        }

        // so we can clear the existing log
        $this->clearContent();
    }

    /**
     * Returns the separator for this very specific logger
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The line separator -> empty in this case
     * @access protected
     */
    protected function getLineSeparator()
    {
        return $this->lineSeparator;
    }

    /**
     * This method is intend to add the defined line-separator to log-content.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success, otherwise FALSE
     * @access protected
     */
    protected function separate()
    {
        // do nothing to seperate in system logger
        return true;
    }
}
