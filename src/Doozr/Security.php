<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Security.
 *
 * Security.php - Access to private/public keys for security operations of
 * the Doozr Framework.
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
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Class/Singleton.php';

use Psr\Log\LoggerInterface;

/**
 * Doozr Security.
 *
 * Access to private/public keys for security operations of the Doozr Framework.
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
class Doozr_Security extends Doozr_Base_Class_Singleton
{
    /**
     * Global Doozr Doozr_Configuration.
     *
     * @var Doozr_Configuration_Interface
     */
    protected $configuration;

    /**
     * Logging subsystem.
     *
     * @var LoggerInterface
     */
    protected $logging;

    /**
     * Private key used for non critical de-/encryption.
     *
     * @var string
     */
    private $privateKey;

    /**
     * Public key used for non critical de-/encryption.
     *
     * @var string
     */
    private $publicKey;

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param Doozr_Configuration_Interface $configuration Global configuration of Doozr
     * @param LoggerInterface               $logging       Logging subsystem
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function __construct(Doozr_Configuration_Interface $configuration, LoggerInterface $logging)
    {
        $this
            ->configuration($configuration)
            ->logging($logging)
            ->privateKey(
                substr($this->getSystemFingerprint(), -16, 32) // 256 Bit cut down for Doozr_Crypt_Service_Container_Interface compatibility
            );
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns a unique fingerprint for current system running Doozr Kernel.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string Unique fingerprint
     */
    public function getSystemFingerprint()
    {
        return sha1(serialize(php_uname()));
    }

    /**
     * Setter for public key.
     *
     * @param string $publicKey Public key to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setPublicKey($publicKey)
    {
        $this->publicKey = $publicKey;
    }

    /**
     * Fluent: Setter for public key.
     *
     * @param string $publicKey Public key to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function publicKey($publicKey)
    {
        $this->setPublicKey($publicKey);

        return $this;
    }

    /**
     * Getter for public key.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string|null Public key if set, otherwise NULL
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * Setter for private key.
     *
     * @param string $privateKey Public key to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;
    }

    /**
     * Fluent: Setter for private key.
     *
     * @param string $privateKey Public key to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function privateKey($privateKey)
    {
        $this->setPrivateKey($privateKey);

        return $this;
    }

    /**
     * Getter for private key.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string|null Public key if set, otherwise NULL
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for configuration.
     *
     * @param Doozr_Configuration_Interface $configuration Configuration to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setConfiguration(Doozr_Configuration_Interface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Fluent: Setter for configuration.
     *
     * @param Doozr_Configuration_Interface $configuration Configuration to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function configuration(Doozr_Configuration_Interface $configuration)
    {
        $this->setConfiguration($configuration);

        return $this;
    }

    /**
     * Getter for configuration.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Configuration_Interface|null Instance if set, otherwise NULL
     */
    protected function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Setter for logging.
     *
     * @param LoggerInterface $logging Logging to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setLogging(LoggerInterface $logging)
    {
        $this->logging = $logging;
    }

    /**
     * Fluent: Setter for logging.
     *
     * @param LoggerInterface $logging Logging to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function logging(LoggerInterface $logging)
    {
        $this->setLogging($logging);

        return $this;
    }

    /**
     * Getter for logging.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return LoggerInterface|null Logger instance if set, otherwise NULL
     */
    protected function getLogging()
    {
        return $this->logging;
    }
}
