<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service - Handler - TokenHandler.
 *
 * TokenHandler.php - Handler for token operations like handling the token
 * behavior configure when reaching illegal state.
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

use Clickalicious\Rng\Generator as RandomNumberGenerator;

/**
 * Doozr - Form - Service - Handler - TokenHandler.
 *
 * Handler for token operations like handling the token
 * behavior configure when reaching illegal state.
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
class Doozr_Form_Service_Handler_TokenHandler extends Doozr_Base_Class
    implements Doozr_Form_Service_Handler_Interface
{
    /**
     * Deny access behavior.
     * Blocks access to page before delivered and respond with HTTP status 401 (Unauthorized).
     *
     * @var int
     */
    const TOKEN_BEHAVIOR_DENY = 1;

    /**
     * Ignore.
     * No matter if valid or invalid - the token is just getting ignored.
     *
     * @var int
     */
    const TOKEN_BEHAVIOR_IGNORE = 2;

    /**
     * Allow but invalidate.
     * Allow but invalidate data for security reasons.
     *
     * @Security
     *
     * @var int
     */
    const TOKEN_BEHAVIOR_INVALIDATE = 3;

    /**
     * Token default behavior.
     *
     * @var int
     */
    const TOKEN_BEHAVIOR_DEFAULT = self::TOKEN_BEHAVIOR_IGNORE;

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Handler entry for handling an token state and behavior passed in.
     * Validation is explicitly not part of this handler. Validation is configurable
     * and so up to your imagination and we don't want to stop that.
     *
     * @param int  $invalidTokenBehavior Behavior to apply.
     * @param bool $isValid              State of token (either valid = TRUE, or invalid = FALSE).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if handled, FALSE => requires invalidation of data stored in pool outside!
     */
    public function handleToken($invalidTokenBehavior, $isValid)
    {
        $status = true;

        // If we encounter an error in token relation we need to pass this task to token handler.
        if (true !== $isValid) {
            $status = $this->handleInvalidToken($invalidTokenBehavior);
        }

        return $status;
    }

    /**
     * Generates a token based on time and random input (salt).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string Generated token
     */
    public function generateToken()
    {
        // Generate token from unique input
        $generator = new RandomNumberGenerator();
        $time      = microtime(true);
        $salt      = $generator->generate();

        return sha1($time.$salt);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Handles invalid tokens by passed behavior.
     *
     * @param int $tokenBehavior Behavior for invalid token
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     */
    protected function handleInvalidToken($tokenBehavior = self::TOKEN_BEHAVIOR_DEFAULT)
    {
        switch ($tokenBehavior) {
            case self::TOKEN_BEHAVIOR_IGNORE:
                $status = true;
                break;

            case self::TOKEN_BEHAVIOR_INVALIDATE:
                $status = false;
                break;

            case self::TOKEN_BEHAVIOR_DENY:
            default:
                // Headers ...
                $headers = [
                    'HTTP/1.0 400 Bad Request',
                    sprintf('WWW-Authenticate: x-doozr-form-service-token realm="%s"', $this->generateToken()),
                ];

                // Need to be sure to be able to send header, otherwise we need to stop operation
                if (false === headers_sent($file, $line)) {
                    foreach ($headers as $header) {
                        header($header);
                    }
                }
                exit;
                break;
        }

        return $status;
    }
}
