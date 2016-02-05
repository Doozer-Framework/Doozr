<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Crypt - Service.
 *
 * Service.php - En- / Decryption Service for Doozr Framework.
 *
 * AES Cipher Library - Based on Federal Information Processing
 * Standards Publication 197 - 26th November 2001 -
 * Text cipher class - This class is using AES crypt algorithm
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
 * @author     Marcin F. Wisniowski <marcin.wisniowski@mfw.pl>
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Service/Multiple/Facade.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Service/Interface.php';

use Doozr\Loader\Serviceloader\Annotation\Inject;

/**
 * Doozr - Crypt - Service.
 *
 * AES Cipher Library - Based on Federal Information Processing Standards Publication
 * 197 - 26th November 2001 - Text cipher class - This class is using AES crypt algoritm
 *
 * @category   Doozr
 *
 * @author     Marcin F. Wisniowski <marcin.wisniowski@mfw.pl>
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 * @Inject(
 *     link   = "doozr.registry",
 *     type   = "constructor",
 *     target = "getInstance"
 * )
 */
class Doozr_Crypt_Service extends Doozr_Base_Service_Multiple_Facade
    implements
    Doozr_Base_Service_Interface
{
    /**
     * Cipher container instance.
     *
     * @var Doozr_Crypt_Service_Container_Interface
     */
    protected $container;

    /**
     * Cipher used for encryption.
     *
     * @var string
     */
    protected $cipher;

    /**
     * Valid container/ciphers.
     *
     * @var string[]
     */
    protected $cipherAvailable;

    /**
     * Encoding for encrypted strings.
     *
     * @var string
     */
    protected $encoding;

    /**
     * Valid encodings including the encode + decode function reference.
     *
     * @var string[]
     */
    protected $encodingAvailable;

    /**
     * Mapping from encoding to encode & decode functions.
     *
     * @var array[]
     */
    protected $encodingMapping;

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor replacement.
     *
     * @param string $cipher   Cipher (container) for en-/decryption
     * @param string $encoding Encoding used for output
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __tearup(
        $cipher = null,
        $encoding = null
    ) {
        // Check for defaults from configuration
        if (null === $cipher) {
            $cipher = $this->getRegistry()->getConfiguration()->crypt->cipher->default;
        }

        if (null === $encoding) {
            $encoding = $this->getRegistry()->getConfiguration()->crypt->encoding->default;
        }

        $cipherAvailable   = object_to_array($this->getRegistry()->getConfiguration()->crypt->cipher->available);
        $encodingAvailable = object_to_array($this->getRegistry()->getConfiguration()->crypt->encoding->available);
        $encodingMapping   = object_to_array($this->getRegistry()->getConfiguration()->crypt->encoding->mapping);

        $this
            ->cipherAvailable($cipherAvailable)
            ->encodingAvailable($encodingAvailable)
            ->encodingMapping($encodingMapping)
            ->cipher($cipher)
            ->encoding($encoding);
    }

    /*------------------------------------------------------------------------------------------------------------------
     | PUBLIC API
     +----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to encrypt a given string with a given key or default key.
     *
     * @param string $data   The data to encrypt
     * @param mixed  $key    The key to use for encryption
     * @param bool   $encode TRUE to encode the data, otherwise FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The encrypted $content
     */
    public function encrypt($data, $key = null, $encode = true)
    {
        if (null !== $key) {
            $this->getContainer()->setKey($key);
        }

        $data = $this->getContainer()->encrypt($data);

        if (true === $encode) {
            $data = $this->encode($data);
        }

        return $data;
    }

    /**
     * Decrypts an encrypted string.
     *
     * @param string $data   The data to decrypt
     * @param mixed  $key    The key to use for decryption
     * @param bool   $decode TRUE to decode the data, otherwise FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The decrypted string
     */
    public function decrypt($data, $key = null, $decode = true)
    {
        if (null !== $key) {
            $this->getContainer()->setKey($key);
        }

        if (true === $decode) {
            $data = $this->decode($data);
        }

        $data = $this->getContainer()->decrypt($data);

        return $data;
    }

    /*------------------------------------------------------------------------------------------------------------------
     | INTERNAL API
     +----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for container.
     *
     * @param Doozr_Crypt_Service_Container_Interface $container The container instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * Fluent: Setter for container.
     *
     * @param Doozr_Crypt_Service_Container_Interface $container The container instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function container($container)
    {
        $this->setContainer($container);

        return $this;
    }

    /**
     * Getter for container.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return \Doozr_Crypt_Service_Container_Interface
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * Setter for cipher.
     *
     * @param string $cipher Cipher to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setCipher($cipher)
    {
        if (false === in_array($cipher, $this->getCipherAvailable())) {
            throw new Doozr_Crypt_Service_Exception(
                sprintf(
                    'Cipher "%s" not supported. Choose from: "%s".',
                    $cipher,
                    var_export($this->getCipherAvailable(), true)
                )
            );
        }

        // Init once or on change
        if ($cipher !== $this->getCipher()) {
            // Create a new instance of cipher's container
            $this
                ->container($this->containerFactory($cipher))
                ->cipher;

            self::setRealObject(
                $this->getContainer()
            );
        }
    }

    /**
     * fluent: Setter for cipher.
     *
     * @param string $cipher Cipher to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function cipher($cipher)
    {
        $this->setCipher($cipher);

        return $this;
    }

    /**
     * Getter for cipher.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The cipher
     */
    protected function getCipher()
    {
        return $this->cipher;
    }

    /**
     * Setter for encoding.
     *
     * @param string $encoding The encoding to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setEncoding($encoding)
    {
        if (false === in_array($encoding, $this->getEncodingAvailable())) {
            throw new Doozr_Crypt_Service_Exception(
                sprintf(
                    'Encoding "%s" not supported. Choose from: "%s".',
                    $encoding,
                    var_export($this->getEncodingAvailable(), true)
                )
            );
        }

        $this->encoding = $encoding;
    }

    /**
     * Fluent: Setter for encoding.
     *
     * @param string $encoding The encoding used for encrypted strings (better for transport)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this
     */
    protected function encoding($encoding)
    {
        $this->setEncoding($encoding);

        return $this;
    }

    /**
     * Getter for encoding.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string Encoding set
     */
    protected function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Setter for encodingAvailable.
     *
     * @param array $encodingAvailable The encodingAvailable to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setEncodingAvailable(array $encodingAvailable)
    {
        $this->encodingAvailable = $encodingAvailable;
    }

    /**
     * Fluent: Setter for encodingAvailable.
     *
     * @param array $encodingAvailable The encodingAvailable to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this
     */
    protected function encodingAvailable(array $encodingAvailable)
    {
        $this->setEncodingAvailable($encodingAvailable);

        return $this;
    }

    /**
     * Getter for encodingAvailable.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The encodingAvailable set
     */
    protected function getEncodingAvailable()
    {
        return $this->encodingAvailable;
    }

    /**
     * Setter for encodingMapping.
     *
     * @param array $encodingMapping The encodingMapping to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setEncodingMapping(array $encodingMapping)
    {
        $this->encodingMapping = $encodingMapping;
    }

    /**
     * Fluent: Setter for encodingMapping.
     *
     * @param array $encodingMapping The encodingMapping to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this
     */
    protected function encodingMapping(array $encodingMapping)
    {
        $this->setEncodingMapping($encodingMapping);

        return $this;
    }

    /**
     * Getter for encodingMapping.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The encodingMapping set
     */
    protected function getEncodingMapping()
    {
        return $this->encodingMapping;
    }

    /**
     * Setter for valid ciphers.
     *
     * @param array $cipherAvailable Valid ciphers to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setCipherAvailable(array $cipherAvailable)
    {
        $this->cipherAvailable = $cipherAvailable;
    }

    /**
     * Fluent: Setter for valid ciphers.
     *
     * @param array $cipherAvailable Valid ciphers to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function cipherAvailable(array $cipherAvailable)
    {
        $this->setCipherAvailable($cipherAvailable);

        return $this;
    }

    /**
     * Getter for valid ciphers.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array Valid ciphers set
     */
    protected function getCipherAvailable()
    {
        return $this->cipherAvailable;
    }

    /**
     * Encodes data.
     *
     * @param string $data The data to encode
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string Encoded data
     */
    protected function encode($data)
    {
        $function = $this->getEncodingMapping()[$this->getEncoding()][__FUNCTION__];

        return call_user_func($function, $data);
    }

    /**
     * Decodes data.
     *
     * @param string $data The data to decode
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string Decoded data
     */
    protected function decode($data)
    {
        $function = $this->getEncodingMapping()[$this->getEncoding()][__FUNCTION__];

        return call_user_func($function, $data);
    }

    /**
     * This method is intend to act as factory for container.
     *
     * @param string $container        The container to create
     * @param array  $containerOptions The configuration/options for the container
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Crypt_Service_Container_Interface Container instance
     *
     * @throws Doozr_Crypt_Service_Exception
     */
    protected function containerFactory($container, array $containerOptions = [])
    {
        $container = ucfirst(strtolower($container));
        $className = __CLASS__.'_Container_'.$container;
        $file      = $this->getRegistry()->getPath()->get('service').
                     str_replace('_', DIRECTORY_SEPARATOR, $className).'.php';

        // Check if file exists
        // @codeCoverageIgnoreStart
        if (false === file_exists($file)) {
            throw new Doozr_Crypt_Service_Exception(
                sprintf('Container "%s" is not loadable. File "%s" does not exist!', $container, $file)
            );
        }
        // @codeCoverageIgnoreEnd

        // Include the file for the class
        include_once $file;

        // Any options set?
        return self::instantiate($className, $containerOptions);
    }
}
