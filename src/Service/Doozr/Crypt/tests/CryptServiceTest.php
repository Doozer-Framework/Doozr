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
 */
class CryptServiceTest extends Doozr_Base_Service_Test_Abstract
{
    /**
     * Private key fixture for encryption.
     *
     * @var string
     */
    protected static $privateKey;

    /**
     * Prepares setup for Tests of "Crypt".
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setUp()
    {
        self::$serviceName = 'Crypt';

        parent::setUp();

        /*
        $faker = Faker\Factory::create(
            $this->convertLocale(self::$registry->getConfiguration()->i18n->default->locale)
        );

        dump($faker->password(16,16));
        dump($faker->password(32,32));
        dump($faker->password(64,64));
        dump($faker->realText($faker->numberBetween(100,200)));

        die;
        dump(self::$privateKey);
        */
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
    public function testEncryption()
    {
        // Assertion(s)
        $this->assertTrue(true);
    }
}
