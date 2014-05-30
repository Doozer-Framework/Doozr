<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Crypt - Service
 *
 * Service.php - En- / Decryption Service for DoozR Framework.
 * This module works with container so different ciphers are supported:
 *
 *     AES:
 *     AES Cipher Library - Based on Federal Information Processing
 *     Standards Publication 197 - 26th November 2001 -
 *     Text cipher class - This class is using AES crypt algoritm
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
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Crypt
 * @author     Marcin F. Wisniowski <marcin.wisniowski@mfw.pl>
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Service/Multiple/Facade.php';

/**
 * DoozR - Crypt - Service
 *
 * AES Cipher Library - Based on Federal Information Processing Standards Publication
 * 197 - 26th November 2001 - Text cipher class - This class is using AES crypt algoritm
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Crypt
 * @author     Marcin F. Wisniowski <marcin.wisniowski@mfw.pl>
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @service    Multiple
 * @inject     DoozR_Registry:DoozR_Registry identifier:__construct type:constructor position:1
 */
class DoozR_Crypt_Service extends DoozR_Base_Service_Multiple_Facade
{
    /**
     * holds the AES-cipher object
     *
     * @var object
     * @access private
     */
    private $_container;

    /**
     * holds the private key
     *
     * @var string
     * @access private
     */
    private $_privateKey;

    /**
     * Contains the name of currently active cipher
     *
     * @var string
     * @access private
     */
    private $_activeCipher;

    /**
     * Contains container encoding
     *
     * @var string
     * @access private
     */
    private $_containerEncoding = 'base64';

    /**
     * Contains the valid encodings including the encode + decode function reference
     *
     * @var array
     * @access private
     */
    private $_validContainerEncodings = array(
        'base64' => array(
            '_encode' => 'base64_encode',
            '_decode' => 'base64_decode'
        ),
        'uuencode' => array(
            '_encode' => 'convert_uuencode',
            '_decode' => 'convert_uudecode'
        )
    );


    /**
     * This method is intend to act as constructor.
     *
     * @param string $cipher   The cipher (container) to use
     * @param string $encoding The encoding used for output
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __tearup($cipher = 'Aes', $encoding = 'base64')
    {
        // setup algorithm
        $this->setCipher($cipher);

        // setup encoding
        $this->setEncoding($encoding);
    }

    /**
     * Sets the active cipher (algorithm)
     *
     * This method is intend to set the active cipher (algorithm).
     *
     * @param string $cipher The (name of) cipher to use
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function setCipher($cipher = 'Aes')
    {
        if ($cipher != $this->_activeCipher) {
            // create a new instance of AES
            $this->_container = $this->_containerFactory($cipher);

            // store cipher as active
            $this->_activeCipher = $cipher;

            self::setRealObject(
                $this->_container
            );

            // successful changed
            return true;
        }

        // no change - no success
        return false;
    }

    /**
     * Returns the active cipher (algorithm)
     *
     * This method is intend to return the active cipher (algorithm).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The active cipher (algortihm)
     * @access public
     */
    public function getCipher()
    {
        return $this->_activeCipher;
    }

    /**
     * This method is intend to set the encoding.
     *
     * @param string $encoding The (name of) encoding to use
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setEncoding($encoding)
    {
        if (in_array($encoding, $this->_validContainerEncodings)) {
            $this->_containerEncoding = $encoding;
        }
    }

    /**
     * Returns the encoding
     *
     * This method is intend to return the active encoding.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The active encoding
     * @access public
     */
    public function getEncoding()
    {
        return $this->_containerEncoding;
    }

    /**
     * This method is intend to encode data.
     *
     * @param string $data The data to encode
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Encoded data
     * @access private
     */
    private function _encode($data)
    {
        $function = $this->_validContainerEncodings[$this->_containerEncoding][__FUNCTION__];
        return call_user_func($function, $data);
    }

    /**
     * This method is intend to decode data.
     *
     * @param string $data The data to decode
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Decoded data
     * @access private
     */
    private function _decode($data)
    {
        $function = $this->_validContainerEncodings[$this->_containerEncoding][__FUNCTION__];
        return call_user_func($function, $data);
    }

    /**
     * This method is intend to act as factory for container.
     *
     * @param string $container        The container to create
     * @param array  $containerOptions The configuration/options for the container
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object Instance of the container
     * @access private
     * @throws DoozR_Exception
     */
    private function _containerFactory($container, array $containerOptions = array())
    {
        $container = ucfirst(strtolower($container));
        $class     = __CLASS__.'_Container_'.$container;
        $file      = $this->registry->path->get('module').str_replace('_', DIRECTORY_SEPARATOR, $class).'.php';

        // check if file exists
        if (!file_exists($file)) {
            throw new DoozR_Exception(
                'Container-File: '.$file.' does not exist!'
            );
        }

        include_once $file;
        return new $class($containerOptions);
    }

    /**
     * This method is intend to encrypt a given string with a given key or default key.
     *
     * @param string  $data   The data to encrypt
     * @param mixed   $key    The key to use for encryption
     * @param boolean $encode TRUE to encode the data, otherwise FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The encrypted $content
     * @access public
     */
    public function encrypt($data, $key = null, $encode = true)
    {
        if ($key) {
            $this->_container->setKey($key);
        }

        $data = $this->_container->encrypt($data);

        if ($encode) {
            $data = $this->_encode($data);
        }

        return $data;
    }

    /**
     * decrypt a crypted string of data
     *
     * @param string  $data   The data to decrypt
     * @param mixed   $key    The key to use for decryption
     * @param boolean $decode TRUE to decode the data, otherwise FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The decrypted $data
     * @access public
     */
    public function decrypt($data, $key = null, $decode = true)
    {
        if ($key) {
            $this->_container->setKey($key);
        }

        if ($decode) {
            $data = $this->_decode(
                $data
            );
        }

        $data = $this->_container->decrypt($data);

        return $data;
    }
}
