<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Logger - Collecting
 *
 * Collecting.php - This logger collects log-entries and hold them until the
 * logger-subsystem is finally ready for (real) logging.
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
 * @subpackage DoozR_Logger_Collecting
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        Abstract.php, Interface.php
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Logger/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Logger/Interface.php';

/**
 * DoozR - Logger - Collecting
 *
 * This logger collects log-entries and hold them until the logger-subsystem is finally
 * ready for (real) logging.
 *
 * @category   DoozR
 * @package    DoozR_Logger
 * @subpackage DoozR_Logger_Collecting
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        Abstract.php, Interface.php
 * @since      -
 */
final class DoozR_Logger_Collecting extends DoozR_Logger_Abstract implements DoozR_Logger_Interface
{
    /**
     * Name of this logger
     *
     * @var string
     * @access private
     */
    protected $name = 'Collecting';

    /**
     * Version of this logger
     *
     * @var string
     * @access protected
     */
    protected $version = '$Rev$';


    /**
     * This method act as constructor
     *
     * @param integer $level       The loglevel to use for this instance
     * @param string  $fingerprint The fingerprint of the client
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    protected function __construct($level, $fingerprint)
    {
        // call parents constructor
        parent::__construct($level, $fingerprint);

        // holds the log-level for this logger
        $this->level = $level;
    }

    /**
     * Output log content
     *
     * This method is intend to write data to a defined pipe like STDOUT, a file, browser ...
     * It should be overriden in concrete implementation.
     *
     * @param string $color The color of output text as hex-value string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE as dummy return value (empty method are expensive!)
     * @access protected
     */
    protected function output($color = '#7CFC00')
    {
        // dummmy return true -> cause not needed for collecting logger
        return true;
    }
}
