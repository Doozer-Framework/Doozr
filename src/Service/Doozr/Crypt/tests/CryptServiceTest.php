<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Service - Crypt - Test.
 *
 * CryptServiceTest.php - Tests for Service instance of Doozr Crypt Service.
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
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Service/Test/Abstract.php';

/**
 * Doozr - Service - Crypt - Test.
 *
 * Tests for Service instance of Doozr Crypt Service.
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
 *
 * @property   Doozr_Crypt_Service $service
 */
class CryptServiceTest extends Doozr_Base_Service_Test_Abstract
{
    /**
     * Collection of defined keys for iterating them
     * @var array
     */
    protected static $privateKeys = [];

    /**
     * The buffer used for en-/decryption.
     *
     * @var string
     */
    protected static $buffer;

    /**
     * Key strength 128 Bit.
     *
     * @var int
     */
    const KEY_128_BIT = 128;

    /**
     * Key strength 192 Bit.
     *
     * @var int
     */
    const KEY_256_BIT = 192;

    /**
     * Key strength 256 Bit.
     *
     * @var int
     */
    const KEY_512_BIT = 256;

    /**
     * Keys by strength in Bit.
     *
     * @var int[]
     */
    protected static $keysByStrengthBit = [
        self::KEY_128_BIT,
        self::KEY_256_BIT,
        self::KEY_512_BIT
    ];

    /**
     * Invalid cipher to test exception.
     *
     * @var string
     */
    const CIPHER_INVALID = 'DES';

    /**
     * Invalid encoding to test exception.
     *
     * @var string
     */
    const ENCODING_INVALID = 'Base63';

    /**
     * Prepares setup for Tests of "Crypt".
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setUp()
    {
        self::$serviceName = 'Crypt';

        parent::setUp();

        // Get a faker instance with Doozr's default locale
        $faker = Faker\Factory::create(
            $this->convertLocale(self::$registry->getConfiguration()->i18n->default->locale)
        );

        // Iterate defined key strengths, generate keys in different strength and store it
        foreach (self::$keysByStrengthBit as $keyStrengthBits) {
            $keyStrengthBytes = $keyStrengthBits / 8;
            self::$privateKeys[$keyStrengthBits] = $faker->password($keyStrengthBytes, $keyStrengthBytes);
        }

        // Generate a random text for encryption
        self::$buffer = $faker->realText($faker->numberBetween(10, 4096));
    }

    /**
     * Converts a locale from "de-de" format to "de_DE" format.
     *
     * @param string $locale To convert
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string Converted locale
     */
    protected function convertLocale($locale)
    {
        $locale = explode('-', $locale);

        return sprintf('%s_%s', strtolower($locale[0]), strtoupper($locale[1]));
    }

    /**
     * Test: If encryption & decryption works.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function testEncryptionAndDecryption()
    {
        // Iterate key strengths and test en- and decryption with all defined key strengths
        foreach (self::$privateKeys as $keyStrengthBits => $privateKey) {

            // Out clear text
            $text = self::$buffer;

            // Check En- and Decryption
            $this->assertSame($text, self::$service->decrypt(self::$service->encrypt($text, $privateKey), $privateKey));
        }
    }

    /**
     * Test: If instance fails with Exception due to a invalid cipher passed to constructor
     *
     * @expectedException Doozr_Crypt_Service_Exception
     * @expectedExceptionCode 5200
     */
    public function testExceptionOnInvalidCipher()
    {
        Doozr_Loader_Serviceloader::load(self::$serviceName, self::CIPHER_INVALID);
    }

    /**
     * Test: If instance fails with Exception due to a invalid encoding passed to constructor
     *
     * @expectedException Doozr_Crypt_Service_Exception
     * @expectedExceptionCode 5200
     */
    public function testExceptionOnInvalidEncoding()
    {
        Doozr_Loader_Serviceloader::load(self::$serviceName, null, self::ENCODING_INVALID);
    }
}
