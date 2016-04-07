<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - I18n - Service - TranslatorTest
 *
 * TranslatorTest.php - Tests for Translator of the Doozr I18n Service.
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
require_once DOOZR_DOCUMENT_ROOT.'Service/Doozr/I18n/tests/Resource/Fixture.php';

/**
 * Doozr - I18n - Service - TranslatorTest
 *
 * Tests for Translator of the Doozr I18n Service.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
class TranslatorTest extends Doozr_Base_Service_Test_Abstract
{
    /**
     * Prepares setup for Tests of "I18n".
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setUp()
    {
        self::$serviceName = 'I18n';
        parent::setUp();
    }

    /**
     * Test: If the service returns the correct translator.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function testGettingTranslatorInstanceFromService()
    {
        // Prepare
        $locale = Resource_Fixture::LOCALE_VALID;
        self::$service->setLocale($locale);

        $translator = self::$service->getTranslator();

        $this->assertInstanceOf(
            'Doozr_I18n_Service_Translator',
            $translator
        );

        // Assertion(s)
        $this->assertEquals($locale, $translator->getLocale());
    }

    /**
     * Test: If the service returns the correct translator if a locale with redirect was passed.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function testGettingTranslatorForRedirectLocale()
    {
        // Prepare
        $translator = self::$service->getTranslator('en-gb');

        $this->assertInstanceOf(
            'Doozr_I18n_Service_Translator',
            $translator
        );

        // Assertion(s)
        $this->assertEquals('en-us', $translator->getLocale());
    }

    /**
     * Test: That a passed string isn't altered by translator if the string isn't translated yet.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @requires OS Linux
     */
    public function testTranslatorDoesNotAlterMissingTranslation()
    {
        // Prepare
        $locale = Resource_Fixture::LOCALE_VALID;
        self::$service->setLocale($locale);

        $input = Resource_Fixture::KEY_MISSING;

        $translator = self::$service->getTranslator();
        $translator->setNamespace('default');

        // Assertion(s)
        $this->assertEquals($input, $translator->_($input));
    }

    /**
     * Test: If the try to translate a string without setting a namespace first will throw an exception as warning for
     * the developer.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @expectedException Doozr_I18n_Service_Exception
     */
    public function testTranslatingWithoutNamespaceThrowsException()
    {
        // Prepare
        $locale = Resource_Fixture::LOCALE_VALID;
        self::$service->setLocale($locale);
        $translator = self::$service->getTranslator();
        $translator->_('hour');
    }

    /**
     * Test: If simple translation will be successful.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @requires OS Linux
     */
    public function testTranslatingTheKeyYes()
    {
        // Prepare
        $locale = Resource_Fixture::LOCALE_VALID;
        self::$service->setLocale($locale);

        $translator = self::$service->getTranslator();
        $translator->setNamespace('default');

        // Assertion(s)
        $this->assertEquals('Ja', $translator->_('Yes'));
    }

    /**
     * Test: If simple translation will be successful.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @requires OS Linux
     */
    public function testTranslatingTheKeyNo()
    {
        // Prepare
        $locale = Resource_Fixture::LOCALE_VALID;
        self::$service->setLocale($locale);

        $translator = self::$service->getTranslator();
        $translator->setNamespace('default');

        // Assertion(s)
        $this->assertEquals('Nein', $translator->_('No'));
    }

    /**
     * Test: If a more complex translation will be successful.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @requires OS Linux
     */
    public function testTranslatingAKeyWithValuesInserted()
    {
        // Prepare
        $locale = Resource_Fixture::LOCALE_VALID;
        self::$service->setLocale($locale);

        /* @var Doozr_I18n_Service_Translator $translator*/
        $translator = self::$service->getTranslator();
        $translator->setNamespace('default');
        $translation = $translator->_('x_books_in_my_y_shelves', [5, 23]);

        // Assertion(s)
        $this->assertContains('5', $translation);
        $this->assertContains('23', $translation);
        $this->assertNotContains('666', $translation);
    }
}
