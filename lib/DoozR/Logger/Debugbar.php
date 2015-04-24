<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Logger - Debugbar
 *
 * Debugbar.php - This logger is a bridge to PHP Debug Bar's logging system (
 *   http://phpdebugbar.com/docs/base-collectors.html#messages
 * ) we decided to use this logger - which can be combined with all the other
 * loggers - to bring the logging functionality of PHP Debug Bar to the devs.
 * So when developing Doozr you can simply call $logger->log('Foo', LEVEL);
 * and be sure if logging level config is OK the log entry will also appear in
 * debug bar.
 *
 * PHP versions 5.4
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
 * @category   Doozr
 * @package    Doozr_Logger
 * @subpackage Doozr_Logger_Debugbar
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Logger/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Logger/Interface.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Logger/PsrInterface.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Logger/Constant.php';

/**
 * Doozr - Logger - Debugbar
 *
 * This logger is a bridge to PHP Debug Bar's logging system (
 *   http://phpdebugbar.com/docs/base-collectors.html#messages
 * ) we decided to use this logger - which can be combined with all the other
 * loggers - to bring the logging functionality of PHP Debug Bar to the devs.
 * So when developing Doozr you can simply call $logger->log('Foo', LEVEL);
 * and be sure if logging level config is OK the log entry will also appear in
 * debug bar.
 *
 * @category   Doozr
 * @package    Doozr_Logger
 * @subpackage Doozr_Logger_Debugbar
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Logger_Debugbar extends Doozr_Logger_Abstract implements
    Doozr_Logger_Interface,
    Doozr_Logger_PsrInterface,
    SplObserver
{
    /**
     * Name of this logger
     *
     * @var string
     * @access protected
     */
    protected $name = 'Debugbar';

    /**
     * Version of this logger
     *
     * @var string
     * @access protected
     */
    protected $version = '$Id$';

    /**
     * The identifier of this instance
     *
     * @var string
     * @access protected
     */
    protected $identifier = '';


    /**
     * Constructor.
     *
     * @param Doozr_Datetime_Service $datetime
     * @param int $level The loglevel of the logger extending this class
     * @param string $fingerprint The fingerprint of the client
     * @internal param Doozr_Config $config The configuration instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \Doozr_Logger_Debugbar
     * @access public
     */
    public function __construct(Doozr_Datetime_Service $datetime, $level = null, $fingerprint = null)
    {
        // call parents constructor
        parent::__construct($datetime, $level, $fingerprint);
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
        $this->identifier = $name;
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
                /* @var Doozr_Logger $subject */
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

            // build the log-line
            $content = $logEntry['time'].' '.
                       '['.$logEntry['type'].'] '.
                       $logEntry['fingerprint'].' '.
                       $logEntry['message'].
                       $this->lineBreak.$this->getLineSeparator().$this->lineBreak;
        }

        // so we can clear the existing log
        $this->clearContent();
    }

}
