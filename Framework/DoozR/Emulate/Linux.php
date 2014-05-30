<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Extend - Emulate - Linux
 *
 * Linux.php - This include extends PHP's functionality by emulating
 *             missing native functions available only on Linux/Unix
 *             -based environments like /dev/urandom and
 *             get_all_headers(). This is done by using native PHP-Code.
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2014, Benjamin Carl - All rights reserved.
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
 * @package    DoozR_Extend
 * @subpackage DoozR_Extend_Emulate_Linux
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

/***********************************************************************************************************************
 * // BEGIN EMULATING LINUX (UNIX) ONLY FUNCTIONALITY FOR OTHER OS'
 **********************************************************************************************************************/

if (!function_exists('getallheaders')) {
    /**
     * getallheaders
     *
     * This method adds getallheader()-Support to Apache and other Webservers
     * on Windows-based OS'. This method originally is only available on Unix/Linux-
     * based OS'. To get all headers it iterates over all Request-Header and prepare
     * them like the getallheaders()-function under Linux/Unix OS'.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array $headers All HTTP Headers of current Request
     * @access public
     */
    function getallheaders()
    {
        // holds the headers to return
        $headers = array();

        // holds the result
        $headerParsed = array();

        // iterate over $_SERVER to parse header from there
        foreach ($_SERVER as $header => $value) {
            if (preg_match('/HTTP_(.+)/i', $header, $headerParsed)) {
                $headers[$headerParsed[1]] = $value;
            }
        }

        // return the result
        return $headers;
    }
}

/***********************************************************************************************************************
 * \\ END
 **********************************************************************************************************************/
