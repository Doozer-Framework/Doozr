<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Di - Constants
 *
 * Constants.php - Di constants. Global required values in a container
 * served as constants.
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
 * @package    Doozr_Di
 * @subpackage Doozr_Di_Constants
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Di
 */

/**
 * Doozr - Di - Constants
 *
 * Di constants. Global required values in a container served as constants.
 *
 * @category   Doozr
 * @package    Doozr_Di
 * @subpackage Doozr_Di_Map
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link       https://github.com/clickalicious/Di
 */
class Doozr_Di_Constants
{
    /**
     * The default scope used when handling maps and stuff like that internally.
     * By changing the scope it's possible to separate content.
     *
     * @var string
     * @access public
     * @const
     */
    const DEFAULT_SCOPE = DOOZR_NAMESPACE_FLAT;

    /**
     * Type for "Constructor" injection
     *
     * @var string
     * @access public
     * @const
     */
    const INJECTION_TYPE_CONSTRUCTOR = 'constructor';

    /**
     * Type for "Method" injection
     *
     * @var string
     * @access public
     * @const
     */
    const INJECTION_TYPE_METHOD = 'method';

    /**
     * Type for "Property" injection
     *
     * @var string
     * @access public
     * @const
     */
    const INJECTION_TYPE_PROPERTY = 'property';

   /**
     * Wiring is done manually by you
     *
     * @var int
     * @access public
     * @const
     */
    const WIRE_MODE_MANUAL = 1;

    /**
     * Wiring is done automatically
     *
     * @var int
     * @access public
     * @const
     */
    const WIRE_MODE_AUTOMATIC = 2;

    /**
     * Name of the constructor method.
     *
     * @var string
     * @access public
     * @const
     */
    const CONSTRUCTOR_METHOD = '__construct';

    /**
     * Name of the constructor method of singletons.
     *
     * @var string
     * @access public
     * @const
     */
    const CONSTRUCTOR_METHOD_SINGLETON = 'getInstance';
}
