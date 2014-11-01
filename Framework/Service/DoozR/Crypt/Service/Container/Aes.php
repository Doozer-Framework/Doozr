<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Service Crypt Container AES
 *
 * Aes.php - AES-Encryption-Container of the Crypt Service.
 * Supports AES-128, AES-192 and AES-256 cause the length is
 * defined by key length
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
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Crypt/Service/Container.php';
require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Crypt/Service/Container/Interface.php';

/**
 * DoozR Service Crypt Container AES
 *
 * AES-Encryption-Container of the Crypt Service.
 *
 * Based on code from:
 *  Author: Cody Phillips
 *  Company: Phillips Data
 *  Website: www.phpaes.com, www.phillipsdata.com
 *  File: AES.class.php
 *  October 1, 2007
 *
 *  This software is sold as-is without any warranties, expressed or implied,
 *  including but not limited to performance and/or merchantability. No
 *  warranty of fitness for a particular purpose is offered. This script can
 *  be used on as many servers as needed, as long as the servers are owned
 *  by the purchaser. (Contact us if you want to distribute it as part of
 *  another project) The purchaser cannot modify, rewrite, edit, or change any
 *  of this code and then resell it, which would be copyright infringement.
 *  This code can be modified for personal use only.
 *  Comments, Questions? Contact the author at cody [at] wshost [dot] net
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Crypt
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @service    Multiple
 */
class DoozR_Crypt_Service_Container_Aes
extends DoozR_Crypt_Service_Container implements DoozR_Crypt_Service_Container_Interface
{
    /**
     * The number of 32-bit words comprising the plaintext and
     * columns comrising the state matrix of an AES cipher.
     *
     * @var int
     * @access private
     * @static
     */
    private static $_nb = 4;

    /**
     * The number of 32-bit words comprising the cipher key in
     * this AES cipher.
     *
     * @var int
     * @access private
     */
    private $_nk;

    /**
     * The number of rounds in this AES cipher.
     *
     * @var int
     * @access private
     */
    private $_rounds;

    /**
     * The key schedule in this AES cipher.
     *
     * @var int
     * @access private
     */
    private $_word;

    /**
     * The state matrix in this AES cipher with Nb
     * columns and 4 rows
     *
     * @var array
     * @access private
     */
    private $_state;

    /**
     * Determines the lenght of key z
     *
     * @var int
     * @access private
     */
    private $keyLength;


    /**
     * The S-Box substitution table.
     *
     * @var array
     * @access private
     * @static
     */
    private static $_sBox = array(
        0x63, 0x7c, 0x77, 0x7b, 0xf2, 0x6b, 0x6f, 0xc5,
        0x30, 0x01, 0x67, 0x2b, 0xfe, 0xd7, 0xab, 0x76,
        0xca, 0x82, 0xc9, 0x7d, 0xfa, 0x59, 0x47, 0xf0,
        0xad, 0xd4, 0xa2, 0xaf, 0x9c, 0xa4, 0x72, 0xc0,
        0xb7, 0xfd, 0x93, 0x26, 0x36, 0x3f, 0xf7, 0xcc,
        0x34, 0xa5, 0xe5, 0xf1, 0x71, 0xd8, 0x31, 0x15,
        0x04, 0xc7, 0x23, 0xc3, 0x18, 0x96, 0x05, 0x9a,
        0x07, 0x12, 0x80, 0xe2, 0xeb, 0x27, 0xb2, 0x75,
        0x09, 0x83, 0x2c, 0x1a, 0x1b, 0x6e, 0x5a, 0xa0,
        0x52, 0x3b, 0xd6, 0xb3, 0x29, 0xe3, 0x2f, 0x84,
        0x53, 0xd1, 0x00, 0xed, 0x20, 0xfc, 0xb1, 0x5b,
        0x6a, 0xcb, 0xbe, 0x39, 0x4a, 0x4c, 0x58, 0xcf,
        0xd0, 0xef, 0xaa, 0xfb, 0x43, 0x4d, 0x33, 0x85,
        0x45, 0xf9, 0x02, 0x7f, 0x50, 0x3c, 0x9f, 0xa8,
        0x51, 0xa3, 0x40, 0x8f, 0x92, 0x9d, 0x38, 0xf5,
        0xbc, 0xb6, 0xda, 0x21, 0x10, 0xff, 0xf3, 0xd2,
        0xcd, 0x0c, 0x13, 0xec, 0x5f, 0x97, 0x44, 0x17,
        0xc4, 0xa7, 0x7e, 0x3d, 0x64, 0x5d, 0x19, 0x73,
        0x60, 0x81, 0x4f, 0xdc, 0x22, 0x2a, 0x90, 0x88,
        0x46, 0xee, 0xb8, 0x14, 0xde, 0x5e, 0x0b, 0xdb,
        0xe0, 0x32, 0x3a, 0x0a, 0x49, 0x06, 0x24, 0x5c,
        0xc2, 0xd3, 0xac, 0x62, 0x91, 0x95, 0xe4, 0x79,
        0xe7, 0xc8, 0x37, 0x6d, 0x8d, 0xd5, 0x4e, 0xa9,
        0x6c, 0x56, 0xf4, 0xea, 0x65, 0x7a, 0xae, 0x08,
        0xba, 0x78, 0x25, 0x2e, 0x1c, 0xa6, 0xb4, 0xc6,
        0xe8, 0xdd, 0x74, 0x1f, 0x4b, 0xbd, 0x8b, 0x8a,
        0x70, 0x3e, 0xb5, 0x66, 0x48, 0x03, 0xf6, 0x0e,
        0x61, 0x35, 0x57, 0xb9, 0x86, 0xc1, 0x1d, 0x9e,
        0xe1, 0xf8, 0x98, 0x11, 0x69, 0xd9, 0x8e, 0x94,
        0x9b, 0x1e, 0x87, 0xe9, 0xce, 0x55, 0x28, 0xdf,
        0x8c, 0xa1, 0x89, 0x0d, 0xbf, 0xe6, 0x42, 0x68,
        0x41, 0x99, 0x2d, 0x0f, 0xb0, 0x54, 0xbb, 0x16
    );

    /**
     * The inverse S-Box substitution table.
     *
     * @var array
     * @access private
     * @static
     */
    private static $_invSBox = array(
        0x52, 0x09, 0x6a, 0xd5, 0x30, 0x36, 0xa5, 0x38,
        0xbf, 0x40, 0xa3, 0x9e, 0x81, 0xf3, 0xd7, 0xfb,
        0x7c, 0xe3, 0x39, 0x82, 0x9b, 0x2f, 0xff, 0x87,
        0x34, 0x8e, 0x43, 0x44, 0xc4, 0xde, 0xe9, 0xcb,
        0x54, 0x7b, 0x94, 0x32, 0xa6, 0xc2, 0x23, 0x3d,
        0xee, 0x4c, 0x95, 0x0b, 0x42, 0xfa, 0xc3, 0x4e,
        0x08, 0x2e, 0xa1, 0x66, 0x28, 0xd9, 0x24, 0xb2,
        0x76, 0x5b, 0xa2, 0x49, 0x6d, 0x8b, 0xd1, 0x25,
        0x72, 0xf8, 0xf6, 0x64, 0x86, 0x68, 0x98, 0x16,
        0xd4, 0xa4, 0x5c, 0xcc, 0x5d, 0x65, 0xb6, 0x92,
        0x6c, 0x70, 0x48, 0x50, 0xfd, 0xed, 0xb9, 0xda,
        0x5e, 0x15, 0x46, 0x57, 0xa7, 0x8d, 0x9d, 0x84,
        0x90, 0xd8, 0xab, 0x00, 0x8c, 0xbc, 0xd3, 0x0a,
        0xf7, 0xe4, 0x58, 0x05, 0xb8, 0xb3, 0x45, 0x06,
        0xd0, 0x2c, 0x1e, 0x8f, 0xca, 0x3f, 0x0f, 0x02,
        0xc1, 0xaf, 0xbd, 0x03, 0x01, 0x13, 0x8a, 0x6b,
        0x3a, 0x91, 0x11, 0x41, 0x4f, 0x67, 0xdc, 0xea,
        0x97, 0xf2, 0xcf, 0xce, 0xf0, 0xb4, 0xe6, 0x73,
        0x96, 0xac, 0x74, 0x22, 0xe7, 0xad, 0x35, 0x85,
        0xe2, 0xf9, 0x37, 0xe8, 0x1c, 0x75, 0xdf, 0x6e,
        0x47, 0xf1, 0x1a, 0x71, 0x1d, 0x29, 0xc5, 0x89,
        0x6f, 0xb7, 0x62, 0x0e, 0xaa, 0x18, 0xbe, 0x1b,
        0xfc, 0x56, 0x3e, 0x4b, 0xc6, 0xd2, 0x79, 0x20,
        0x9a, 0xdb, 0xc0, 0xfe, 0x78, 0xcd, 0x5a, 0xf4,
        0x1f, 0xdd, 0xa8, 0x33, 0x88, 0x07, 0xc7, 0x31,
        0xb1, 0x12, 0x10, 0x59, 0x27, 0x80, 0xec, 0x5f,
        0x60, 0x51, 0x7f, 0xa9, 0x19, 0xb5, 0x4a, 0x0d,
        0x2d, 0xe5, 0x7a, 0x9f, 0x93, 0xc9, 0x9c, 0xef,
        0xa0, 0xe0, 0x3b, 0x4d, 0xae, 0x2a, 0xf5, 0xb0,
        0xc8, 0xeb, 0xbb, 0x3c, 0x83, 0x53, 0x99, 0x61,
        0x17, 0x2b, 0x04, 0x7e, 0xba, 0x77, 0xd6, 0x26,
        0xe1, 0x69, 0x14, 0x63, 0x55, 0x21, 0x0c, 0x7d
    );

    /**
     * Log table based on 0xe5
     *
     * @var array
     * @access private
     * @static
     */
    private static $_ltable = array(
        0x00, 0xff, 0xc8, 0x08, 0x91, 0x10, 0xd0, 0x36,
        0x5a, 0x3e, 0xd8, 0x43, 0x99, 0x77, 0xfe, 0x18,
        0x23, 0x20, 0x07, 0x70, 0xa1, 0x6c, 0x0c, 0x7f,
        0x62, 0x8b, 0x40, 0x46, 0xc7, 0x4b, 0xe0, 0x0e,
        0xeb, 0x16, 0xe8, 0xad, 0xcf, 0xcd, 0x39, 0x53,
        0x6a, 0x27, 0x35, 0x93, 0xd4, 0x4e, 0x48, 0xc3,
        0x2b, 0x79, 0x54, 0x28, 0x09, 0x78, 0x0f, 0x21,
        0x90, 0x87, 0x14, 0x2a, 0xa9, 0x9c, 0xd6, 0x74,
        0xb4, 0x7c, 0xde, 0xed, 0xb1, 0x86, 0x76, 0xa4,
        0x98, 0xe2, 0x96, 0x8f, 0x02, 0x32, 0x1c, 0xc1,
        0x33, 0xee, 0xef, 0x81, 0xfd, 0x30, 0x5c, 0x13,
        0x9d, 0x29, 0x17, 0xc4, 0x11, 0x44, 0x8c, 0x80,
        0xf3, 0x73, 0x42, 0x1e, 0x1d, 0xb5, 0xf0, 0x12,
        0xd1, 0x5b, 0x41, 0xa2, 0xd7, 0x2c, 0xe9, 0xd5,
        0x59, 0xcb, 0x50, 0xa8, 0xdc, 0xfc, 0xf2, 0x56,
        0x72, 0xa6, 0x65, 0x2f, 0x9f, 0x9b, 0x3d, 0xba,
        0x7d, 0xc2, 0x45, 0x82, 0xa7, 0x57, 0xb6, 0xa3,
        0x7a, 0x75, 0x4f, 0xae, 0x3f, 0x37, 0x6d, 0x47,
        0x61, 0xbe, 0xab, 0xd3, 0x5f, 0xb0, 0x58, 0xaf,
        0xca, 0x5e, 0xfa, 0x85, 0xe4, 0x4d, 0x8a, 0x05,
        0xfb, 0x60, 0xb7, 0x7b, 0xb8, 0x26, 0x4a, 0x67,
        0xc6, 0x1a, 0xf8, 0x69, 0x25, 0xb3, 0xdb, 0xbd,
        0x66, 0xdd, 0xf1, 0xd2, 0xdf, 0x03, 0x8d, 0x34,
        0xd9, 0x92, 0x0d, 0x63, 0x55, 0xaa, 0x49, 0xec,
        0xbc, 0x95, 0x3c, 0x84, 0x0b, 0xf5, 0xe6, 0xe7,
        0xe5, 0xac, 0x7e, 0x6e, 0xb9, 0xf9, 0xda, 0x8e,
        0x9a, 0xc9, 0x24, 0xe1, 0x0a, 0x15, 0x6b, 0x3a,
        0xa0, 0x51, 0xf4, 0xea, 0xb2, 0x97, 0x9e, 0x5d,
        0x22, 0x88, 0x94, 0xce, 0x19, 0x01, 0x71, 0x4c,
        0xa5, 0xe3, 0xc5, 0x31, 0xbb, 0xcc, 0x1f, 0x2d,
        0x3b, 0x52, 0x6f, 0xf6, 0x2e, 0x89, 0xf7, 0xc0,
        0x68, 0x1b, 0x64, 0x04, 0x06, 0xbf, 0x83, 0x38
    );

    /**
     * Inverse log table
     *
     * @var array
     * @access private
     * @static
     */
    private static $_atable = array(
        0x01, 0xe5, 0x4c, 0xb5, 0xfb, 0x9f, 0xfc, 0x12,
        0x03, 0x34, 0xd4, 0xc4, 0x16, 0xba, 0x1f, 0x36,
        0x05, 0x5c, 0x67, 0x57, 0x3a, 0xd5, 0x21, 0x5a,
        0x0f, 0xe4, 0xa9, 0xf9, 0x4e, 0x64, 0x63, 0xee,
        0x11, 0x37, 0xe0, 0x10, 0xd2, 0xac, 0xa5, 0x29,
        0x33, 0x59, 0x3b, 0x30, 0x6d, 0xef, 0xf4, 0x7b,
        0x55, 0xeb, 0x4d, 0x50, 0xb7, 0x2a, 0x07, 0x8d,
        0xff, 0x26, 0xd7, 0xf0, 0xc2, 0x7e, 0x09, 0x8c,
        0x1a, 0x6a, 0x62, 0x0b, 0x5d, 0x82, 0x1b, 0x8f,
        0x2e, 0xbe, 0xa6, 0x1d, 0xe7, 0x9d, 0x2d, 0x8a,
        0x72, 0xd9, 0xf1, 0x27, 0x32, 0xbc, 0x77, 0x85,
        0x96, 0x70, 0x08, 0x69, 0x56, 0xdf, 0x99, 0x94,
        0xa1, 0x90, 0x18, 0xbb, 0xfa, 0x7a, 0xb0, 0xa7,
        0xf8, 0xab, 0x28, 0xd6, 0x15, 0x8e, 0xcb, 0xf2,
        0x13, 0xe6, 0x78, 0x61, 0x3f, 0x89, 0x46, 0x0d,
        0x35, 0x31, 0x88, 0xa3, 0x41, 0x80, 0xca, 0x17,
        0x5f, 0x53, 0x83, 0xfe, 0xc3, 0x9b, 0x45, 0x39,
        0xe1, 0xf5, 0x9e, 0x19, 0x5e, 0xb6, 0xcf, 0x4b,
        0x38, 0x04, 0xb9, 0x2b, 0xe2, 0xc1, 0x4a, 0xdd,
        0x48, 0x0c, 0xd0, 0x7d, 0x3d, 0x58, 0xde, 0x7c,
        0xd8, 0x14, 0x6b, 0x87, 0x47, 0xe8, 0x79, 0x84,
        0x73, 0x3c, 0xbd, 0x92, 0xc9, 0x23, 0x8b, 0x97,
        0x95, 0x44, 0xdc, 0xad, 0x40, 0x65, 0x86, 0xa2,
        0xa4, 0xcc, 0x7f, 0xec, 0xc0, 0xaf, 0x91, 0xfd,
        0xf7, 0x4f, 0x81, 0x2f, 0x5b, 0xea, 0xa8, 0x1c,
        0x02, 0xd1, 0x98, 0x71, 0xed, 0x25, 0xe3, 0x24,
        0x06, 0x68, 0xb3, 0x93, 0x2c, 0x6f, 0x3e, 0x6c,
        0x0a, 0xb8, 0xce, 0xae, 0x74, 0xb1, 0x42, 0xb4,
        0x1e, 0xd3, 0x49, 0xe9, 0x9c, 0xc8, 0xc6, 0xc7,
        0x22, 0x6e, 0xdb, 0x20, 0xbf, 0x43, 0x51, 0x52,
        0x66, 0xb2, 0x76, 0x60, 0xda, 0xc5, 0xf3, 0xf6,
        0xaa, 0xcd, 0x9a, 0xa0, 0x75, 0x54, 0x0e, 0x01
    );


    /**
     * Constructor.
     *
     * @param string $key The key to use (optional)
     * @see   setKey()
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Crypt_Service_Container_Aes
     * @access public
     */
    public function __construct($key = null)
    {
        if ($key) {
            $this->setKey($key);
        }
    }


    public function setKey($key)
    {
        $this->_nk = strlen($key)/4;
        $this->_rounds = $this->_nk + self::$_nb + 2;

        if ($this->_nk != 4 && $this->_nk != 6 && $this->_nk != 8)
            die("Key is " . ($this->_nk*32) . " bits long. *not* 128, 192, or 256.");

        $this->_rounds = $this->_nk+self::$_nb+2;
        $this->_word = array(); // Nb*(Nr+1) 32-bit words
        $this->_state = array(array());  // 2-D array of Nb colums and 4 rows

        $this->KeyExpansion($key); // places expanded key in w
    }

    public function getKey()
    {
        return $this;
    }


    /** Encrypts an aribtrary length String.
    *   @params plaintext string
    *   @returns ciphertext string
    *   Whenever possible you should stream your plaintext through the
    *   encryptBlock() function directly, as the amount of time required
    *   to encrypt is linear to the size of the ciphertext.
    **/
    public function encrypt($x)
    {
        $t = ""; // 16-byte block
        $y = ""; // returned cipher text;

        // put a 16-byte block into t
        $xsize = strlen($x);
        for ($i=0; $i<$xsize; $i+=16) {
                for ($j=0; $j<16; $j++) {
                        if (($i+$j)<$xsize) {
                                $t[$j] = $x[$i+$j];
                        }
                        else
                                $t[$j] = chr(0);
                }

                $y .= $this->encryptBlock($t);
        }
        return $y;
    }

    /** Decrypts an aribtrary length String.
    *   @params ciphertext string
    *   @returns plaintext string
    *   Whenever possible you should stream your ciphertext through the
    *   decryptBlock() function directly, as the amount of time required
    *   to decrypt is linear to the size of the ciphertext.
    **/
    public function decrypt($y)
	{
        $t = ""; // 16-byte block
        $x = ""; // returned plain text;

        // put a 16-byte block into t
        $ysize = strlen($y);
        for ($i=0; $i<$ysize; $i+=16) {
                for ($j=0; $j<16; $j++) {
                        if (($i+$j)<$ysize)
                                $t[$j] = $y[$i+$j];
                        else
                                $t[$j] = chr(0);
                }
                $x .= $this->decryptBlock($t);
        }
        return $x;
    }

    /** Encrypts the 16-byte plain text.
    *   @params 16-byte plaintext string
    *   @returns 16-byte ciphertext string
    **/
    public function encryptBlock($x)
	{
        $y = ""; // 16-byte string

        // place input x into the initial state matrix in column order
        for ($i=0; $i<4*self::$_nb; $i++) {
                // we want integerger division for the second index
                $this->_state[$i%4][($i-$i%self::$_nb)/self::$_nb] = ord($x[$i]);
        }

        // add round key
        $this->addRoundKey(0);

        for ($i=1; $i<$this->_rounds; $i++) {
                // substitute bytes
                $this->subBytes();

                // shift rows
                $this->shiftRows();

                // mix columns
                $this->mixColumns();

                // add round key
                $this->addRoundKey($i);
        }

        // substitute bytes
        $this->subBytes();

        // shift rows
        $this->shiftRows();

        // add round key
        $this->addRoundKey($i);

        // place state matrix s into y in column order
        for ($i=0; $i<4*self::$_nb; $i++)
               $y .= chr($this->_state[$i%4][($i-$i%self::$_nb)/self::$_nb]);
        return $y;
    }

    /** Decrypts the 16-byte cipher text.
    *   @params 16-byte ciphertext string
    *   @returns 16-byte plaintext string
    **/
    public function decryptBlock($y)
	{
        $x = ""; // 16-byte string

        // place input y into the initial state matrix in column order
        for ($i=0; $i<4*self::$_nb; $i++)
                $this->_state[$i%4][($i-$i%self::$_nb)/self::$_nb] = ord($y[$i]);

        // add round key
        $this->addRoundKey($this->_rounds);

        for ($i=$this->_rounds-1; $i>0; $i--) {
                // inverse shift rows
                $this->invShiftRows();

                // inverse sub bytes
                $this->invSubBytes();

                // add round key
                $this->addRoundKey($i);

                // inverse mix columns
                $this->invMixColumns();
        }

        // inverse shift rows
        $this->invShiftRows();

        // inverse sub bytes
        $this->invSubBytes();

        // add round key
        $this->addRoundKey($i);

        // place state matrix s into x in column order
        for ($i=0; $i<4*self::$_nb; $i++) {
               // Used to remove filled null characters.
               $x .= ($this->_state[$i%4][($i-$i%self::$_nb)/self::$_nb] == chr(0) ? "" : chr($this->_state[$i%4][($i-$i%self::$_nb)/self::$_nb]));
        }

        return $x;
    }

    public function __destruct()
	{
        unset($this->_word);
        unset($this->_state);
    }

    /** makes a big key out of a small one
    *   @returns void
    **/
    private function KeyExpansion($key)
	{
        // Rcon is the round constant
        static $Rcon = array(
                0x00000000,
                0x01000000,
                0x02000000,
                0x04000000,
                0x08000000,
                0x10000000,
                0x20000000,
                0x40000000,
                0x80000000,
                0x1b000000,
                0x36000000,
                0x6c000000,
                0xd8000000,
                0xab000000,
                0x4d000000,
                0x9a000000,
                0x2f000000
        );

        $temp = 0; // temporary 32-bit word

        // the first Nk words of w are the cipher key z
        for ($i=0; $i<$this->_nk; $i++) {
                $this->_word[$i] = 0;
                // fill an entire word of expanded key w
                // by pushing 4 bytes into the w[i] word
                $this->_word[$i] = ord($key[4*$i]); // add a byte in
                $this->_word[$i] <<= 8; // make room for the next byte
                $this->_word[$i] += ord($key[4*$i+1]);
                $this->_word[$i] <<= 8;
                $this->_word[$i] += ord($key[4*$i+2]);
                $this->_word[$i] <<= 8;
                $this->_word[$i] += ord($key[4*$i+3]);
        }


        for (; $i<self::$_nb*($this->_rounds+1); $i++) {
                $temp = $this->_word[$i-1];

                if ($i%$this->_nk == 0)
                        $temp = $this->subWord($this->rotWord($temp)) ^ $Rcon[$i/$this->_nk];
                else if ($this->_nk > 6 && $i%$this->_nk == 4)
                        $temp = $this->subWord($temp);

                $this->_word[$i] = $this->_word[$i-$this->_nk] ^ $temp;

       	       self::make32BitWord($this->_word[$i]);
        }
    }

    /** adds the key schedule for a round to a state matrix.
    *   @returns void
    **/
    private function addRoundKey($round)
	{
        $temp = "";

        for ($i=0; $i<4; $i++) {
                for ($j=0; $j<self::$_nb; $j++) {
                        // place the i-th byte of the j-th word from expanded key w into temp
                        $temp = $this->_word[$round*self::$_nb+$j] >> (3-$i)*8;
                        // Cast temp from a 32-bit word into an 8-bit byte.
                        $temp %= 256;
                        // Can't do unsigned shifts, so we need to make this temp positive
                        $temp = ($temp < 0 ? (256 + $temp) : $temp);

                        $this->_state[$i][$j] ^= $temp; // xor temp with the byte at location (i,j) of the state
                }
        }
    }

    /** unmixes each column of a state matrix.
    *   @returns void
    **/
    private function invMixColumns()
	{
        $s0 = $s1 = $s2 = $s3= '';

        // There are Nb columns
        for ($i=0; $i<self::$_nb; $i++) {
                $s0 = $this->_state[0][$i]; $s1 = $this->_state[1][$i]; $s2 = $this->_state[2][$i]; $s3 = $this->_state[3][$i];

                $this->_state[0][$i] = $this->mult(0x0e, $s0) ^ $this->mult(0x0b, $s1) ^ $this->mult(0x0d, $s2) ^ $this->mult(0x09, $s3);
                $this->_state[1][$i] = $this->mult(0x09, $s0) ^ $this->mult(0x0e, $s1) ^ $this->mult(0x0b, $s2) ^ $this->mult(0x0d, $s3);
                $this->_state[2][$i] = $this->mult(0x0d, $s0) ^ $this->mult(0x09, $s1) ^ $this->mult(0x0e, $s2) ^ $this->mult(0x0b, $s3);
                $this->_state[3][$i] = $this->mult(0x0b, $s0) ^ $this->mult(0x0d, $s1) ^ $this->mult(0x09, $s2) ^ $this->mult(0x0e, $s3);

        }
    }

    /** applies an inverse cyclic shift to the last 3 rows of a state matrix.
    *   @returns void
    **/
    private function invShiftRows()
	{
        $temp = "";
        for ($i=1; $i<4; $i++) {
                for ($j=0; $j<self::$_nb; $j++)
                        $temp[($i+$j)%self::$_nb] = $this->_state[$i][$j];
                for ($j=0; $j<self::$_nb; $j++)
                        $this->_state[$i][$j] = $temp[$j];
        }
    }

    /** applies inverse S-Box substitution to each byte of a state matrix.
    *   @returns void
    **/
    private function invSubBytes()
	{
        for ($i=0; $i<4; $i++)
                for ($j=0; $j<self::$_nb; $j++)
                        $this->_state[$i][$j] = self::$_invSBox[$this->_state[$i][$j]];
    }

    /** mixes each column of a state matrix.
    *   @returns void
    **/
    private function mixColumns()
	{
        $s0 = $s1 = $s2 = $s3= '';

        // There are Nb columns
        for ($i=0; $i<self::$_nb; $i++) {
                $s0 = $this->_state[0][$i]; $s1 = $this->_state[1][$i]; $s2 = $this->_state[2][$i]; $s3 = $this->_state[3][$i];

                $this->_state[0][$i] = $this->mult(0x02, $s0) ^ $this->mult(0x03, $s1) ^ $this->mult(0x01, $s2) ^ $this->mult(0x01, $s3);
                $this->_state[1][$i] = $this->mult(0x01, $s0) ^ $this->mult(0x02, $s1) ^ $this->mult(0x03, $s2) ^ $this->mult(0x01, $s3);
                $this->_state[2][$i] = $this->mult(0x01, $s0) ^ $this->mult(0x01, $s1) ^ $this->mult(0x02, $s2) ^ $this->mult(0x03, $s3);
                $this->_state[3][$i] = $this->mult(0x03, $s0) ^ $this->mult(0x01, $s1) ^ $this->mult(0x01, $s2) ^ $this->mult(0x02, $s3);
        }
    }

    /** applies a cyclic shift to the last 3 rows of a state matrix.
    *   @returns void
    **/
    private function shiftRows()
	{
        $temp = "";
        for ($i=1; $i<4; $i++) {
                for ($j=0; $j<self::$_nb; $j++)
                        $temp[$j] = $this->_state[$i][($j+$i)%self::$_nb];
                for ($j=0; $j<self::$_nb; $j++)
                        $this->_state[$i][$j] = $temp[$j];
        }
    }

    /** applies S-Box substitution to each byte of a state matrix.
    *   @returns void
    **/
    private function subBytes()
	{
        for ($i=0; $i<4; $i++) {
            for ($j=0; $j<self::$_nb; $j++) {
                $this->_state[$i][$j] = self::$_sBox[$this->_state[$i][$j]];
            }
        }
    }

    /** multiplies two polynomials a(x), b(x) in GF(2^8) modulo the irreducible polynomial m(x) = x^8+x^4+x^3+x+1
    *   @returns 8-bit value
    **/
    private static function mult($a, $b)
	{
		$sum = self::$_ltable[$a] + self::$_ltable[$b];
		$sum %= 255;
		// Get the antilog
		$sum = self::$_atable[$sum];
		return ($a == 0 ? 0 : ($b == 0 ? 0 : $sum));
    }

    /** applies a cyclic permutation to a 4-byte word.
    *   @returns 32-bit int
    **/
    private static function rotWord($w)
	{
		$temp = $w >> 24; // put the first 8-bits into temp
		$w <<= 8; // make room for temp to fill the lower end of the word
		self::make32BitWord($w);
		// Can't do unsigned shifts, so we need to make this temp positive
		$temp = ($temp < 0 ? (256 + $temp) : $temp);
		$w += $temp;

		return $w;
    }

    /** applies S-box substitution to each byte of a 4-byte word.
    *   @returns 32-bit int
    **/
    private static function subWord($w)
	{
		$temp = 0;
		// loop through 4 bytes of a word
		for ($i=0; $i<4; $i++) {
		        $temp = $w >> 24; // put the first 8-bits into temp
		        // Can't do unsigned shifts, so we need to make this temp positive
		        $temp = ($temp < 0 ? (256 + $temp) : $temp);
		        $w <<= 8; // make room for the substituted byte in w;
		        self::make32BitWord($w);
		        $w += self::$_sBox[$temp]; // add the substituted byte back
		}

		self::make32BitWord($w);

		return $w;
    }

    /** reduces a 64-bit word to a 32-bit word
    *   @returns void
    **/
    private static function make32BitWord(&$w)
	{
        // Reduce this 64-bit word to 32-bits on 64-bit machines
        $w &= 0x00000000FFFFFFFF;
    }
}
