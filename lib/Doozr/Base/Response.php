<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Base - Response
 *
 * Response.php - Base class for responses
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
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Response
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

/**
 * Doozr - Base - Response
 *
 * Base class for responses
 *
 * @category   Doozr
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Response
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Base_Response extends Doozr_Base_State_Container
{
    /**
     * The TYPE of the Response (can be either WEB or CLI)
     *
     * @var string
     * @access protected
     */
    protected static $type;

    /**
     * The registry
     *
     * @var Doozr_Registry
     * @access protected
     */
    protected $registry;

    /**
     * holds an instance/handle on logger
     *
     * @var object
     * @access protected
     */
    protected $logger;

    /**
     * contains instance of config
     *
     * @var object
     * @access protected
     */
    protected $config;


    /**
     * Constructor.
     *
     * @param Doozr_Configuration|object $config An instance of config
     * @param Doozr_Logging|object $logger An instance of config
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \Doozr_Base_Response
     * @access public
     */
    public function __construct(Doozr_Configuration $config, Doozr_Logging $logger)
    {
        // get a handle on logger
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Returns the type of current request (web OR cli) as string
     *
     * @return string type of current request CLI or WEB (returns lowercase!)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access  public
     */
    public static function getType()
    {
        return self::$type;
    }
}
