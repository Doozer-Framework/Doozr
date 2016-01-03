<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Base - Model - Driver.
 *
 * Driver.php - Base driver for OxM integration.
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
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Class.php';

/**
 * Doozr - Base - Model - Driver.
 *
 * Base driver for OxM integration.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Base_Model_Driver extends Doozr_Base_Class
{
    /**
     * Generic input validation by passed configuration.
     *
     * @param array $data     Data to validate
     * @param array $required Required fields in data array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if all required fields can be found, otherwise FALSE
     * @static
     */
    protected static function validate(array $data = [], array $required = [])
    {
        return true === in_array($required, $data);
    }

    /**
     * Pre-Install hook.
     * Called right before the real install() call - to provide a way to modify data right before install() call.
     *
     * @param mixed $data The data passed to install() in next step
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed The modified data
     * @static
     */
    public static function preInstall($data = null)
    {
        // Called right before initialization ...
        return $data;
    }

    /**
     * Post-Install hook.
     *
     * @param mixed $eventData Meta data of installation (result)
     * @param mixed $data      Data passed to install() routine
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool
     * @static
     */
    public static function postInstall($eventData, $data = null)
    {
        // Called right before initialization ...
        return $eventData;
    }
}
