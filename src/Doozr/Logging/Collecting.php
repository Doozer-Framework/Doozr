<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Logging - Collecting
 *
 * Collecting.php - This logging collects log-entries and hold them until the
 * logging-subsystem is finally ready for (real) logging.
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
 * @package    Doozr_Logging
 * @subpackage Doozr_Logging_Collecting
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 * @see        Abstract.php, Interface.php
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Logging/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Logging/Interface.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Logging/Constant.php';

use Psr\Log\LoggerInterface;

/**
 * Doozr - Logging - Collecting
 *
 * This logging collects log-entries and hold them until the
 * logging-subsystem is finally ready for (real) logging.
 *
 * @category   Doozr
 * @package    Doozr_Logging
 * @subpackage Doozr_Logging_Collecting
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 * @final
 */
final class Doozr_Logging_Collecting extends Doozr_Logging_Abstract
    implements
    Doozr_Logging_Interface,
    LoggerInterface,
    SplObserver
{
    /**
     * Name of this logging
     *
     * @var string
     * @access private
     */
    protected $name = 'Collecting';

    /**
     * Version of this logging
     *
     * @var string
     * @access protected
     */
    protected $version = 'Git: $Id$';

    /*------------------------------------------------------------------------------------------------------------------
    | Fulfill SplObserver
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * SplOberserver
     *
     * @param SplSubject  $subject The subject containing the data
     * @param null|string $event   The event triggered (optional)
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
     * Prevent content from being echoed or something like this.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE as dummy return value (empty method are expensive!)
     * @access protected
     */
    protected function output()
    {
        // Dummy return true -> cause not needed for collecting logging
        return true;
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Fulfill Abstract Requirements
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Dispatches a new route to this logging (e.g. for use as new filename).
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
         * This logging does not need to be re-routed
         */
    }
}
