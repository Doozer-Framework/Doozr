<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - I18n - Service - Test
 *
 * I18nServiceTest.php - Tests for Service instance of Doozr I18n Service.
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
 * Doozr - I18n - Service - Test
 *
 * Tests for Service instance of Doozr I18n Service.
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
 * @property   Doozr_I18n_Service $service
 */
class I18nServiceTest extends Doozr_Base_Service_Test_Abstract
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
     * Test: If the default locale is returned correctly if no locale was passed to I18n service instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function testRetrievingExpectedDefaultLocale()
    {
        // Assertion(s)
        $this->assertEquals(Resource_Fixture::LOCALE_DEFAULT, self::$service->getLocale());
    }

    /**
     * Test: If it is possible to set a locale.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function testSettingACustomLocale()
    {
        // Prepare
        $locale = Resource_Fixture::LOCALE_VALID;
        self::$service->setLocale($locale);

        // Assertion(s)
        $this->assertEquals($locale, self::$service->getLocale());
    }

    /**
     * Test: If the service throws an exception if someone tries to set an invalid locale.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @expectedException Doozr_I18n_Service_Exception
     */
    public function testTryingToSetAnInvalidCustomLocale()
    {
        // Prepare
        $locale = Resource_Fixture::LOCALE_INVALID;

        // Assertion(s)
        self::$service->setLocale($locale);
    }

    /**
     * Test: If the service returns an valid detector instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function testGettingDetectorInstanceFromService()
    {
        // Assertion(s)
        $this->assertInstanceOf('Doozr_I18n_Service_Detector', self::$service->getDetector());
    }

    /**
     * Test: If the service returns the correct detected locale.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function testRetrievingClientsPreferredLocale()
    {
        // Prepare
        $locale = Resource_Fixture::LOCALE_VALID;

        // Assertion(s)
        $this->assertEquals($locale, self::$service->getClientPreferredLocale());
    }

    /**
     * Test: If the service returns the correct currency localizer.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function testGettingCurrencyLocalizerFromService()
    {
        $locale = Resource_Fixture::LOCALE_VALID;
        self::$service->setLocale($locale);

        /* @var Doozr_I18n_Service_Localize_Currency $currency */
        $currency = self::$service->getLocalizer('Currency');

        $this->assertInstanceOf(
            'Doozr_I18n_Service_Localize_Currency',
            $currency
        );

        $this->assertEquals($locale, $currency->getLocale());
    }

    /**
     * Test: If the service returns the correct datetime localizer.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function testGettingDatetimeLocalizerFromService()
    {
        $locale = Resource_Fixture::LOCALE_VALID;
        self::$service->setLocale($locale);

        /* @var Doozr_I18n_Service_Localize_Datetime $datetime */
        $datetime = self::$service->getLocalizer('Datetime');

        $this->assertInstanceOf(
            'Doozr_I18n_Service_Localize_Datetime',
            $datetime
        );

        $this->assertEquals($locale, $datetime->getLocale());
    }

    /**
     * Test: If the service returns the correct measure localizer.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function testGettingMeasureLocalizerFromService()
    {
        $locale = Resource_Fixture::LOCALE_VALID;
        self::$service->setLocale($locale);

        /* @var Doozr_I18n_Service_Localize_Measure $measure */
        $measure = self::$service->getLocalizer('Measure');

        $this->assertInstanceOf(
            'Doozr_I18n_Service_Localize_Measure',
            $measure
        );

        $this->assertEquals($locale, $measure->getLocale());
    }

    /**
     * Test: If the service returns the correct number localizer.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function testGettingNumberLocalizerFromService()
    {
        $locale = Resource_Fixture::LOCALE_VALID;
        self::$service->setLocale($locale);

        /* @var Doozr_I18n_Service_Localize_Number $number */
        $number = self::$service->getLocalizer('Number');

        $this->assertInstanceOf(
            'Doozr_I18n_Service_Localize_Number',
            $number
        );

        $this->assertEquals($locale, $number->getLocale());
    }

    /**
     * Test: If the number localizer returns correct formatted numbers.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @depends testGettingNumberLocalizerFromService
     */
    public function testLocalizeNumber()
    {
        $locale = Resource_Fixture::LOCALE_VALID;
        self::$service->setLocale($locale);

        /* @var Doozr_I18n_Service_Localize_Number $number */
        $number = self::$service->getLocalizer('Number', 'en-us');

        $value = $number->number(23.55, true);
        $this->assertEquals('23.55', $value);

        $value = $number->number(1233423.55, true);
        $this->assertEquals('1,233,423.55', $value);

        /* @var Doozr_I18n_Service_Localize_Number $number */
        $number = self::$service->getLocalizer('Number', $locale);

        $value = $number->number(23.55, true);
        $this->assertEquals('23,55', $value);

        $value = $number->number(1233423.55, true);
        $this->assertEquals('1.233.423,55', $value);
    }

    /**
     * Test: If the service returns the correct string localizer.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function testGettingStringLocalizerFromService()
    {
        $locale = Resource_Fixture::LOCALE_VALID;
        self::$service->setLocale($locale);

        /* @var Doozr_I18n_Service_Localize_String $string */
        $string = self::$service->getLocalizer('String');

        $this->assertInstanceOf(
            'Doozr_I18n_Service_Localize_String',
            $string
        );

        $this->assertEquals($locale, $string->getLocale());
    }

    /**
     * Test: If the service returns the correct string localizer for redirect locale.
     *
     * @example If the locale detected is "en-gb" which is redirected to "en-us" then all localizer will be redirected
     *          too! This test ensures that it works as expected.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function testGettingLocalizerForARedirectLocale()
    {
        $locale = 'en-gb';
        self::$service->setLocale($locale);

        /* @var Doozr_I18n_Service_Localize_String $string */
        $string = self::$service->getLocalizer('String');

        $this->assertInstanceOf(
            'Doozr_I18n_Service_Localize_String',
            $string
        );

        $this->assertEquals('en-us', $string->getLocale());
    }

    /**
     * Test: If the service returns the correct string localizer for redirect locale.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function testGettingAvailableLocales()
    {
        $this->assertEquals(
            Resource_Fixture::$localesAvailable,
            object_to_array(self::$service->getAvailableLocales())
        );
    }

    /**
     * Test: Add an locale to collection of available locales.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @depends testGettingAvailableLocales
     */
    public function testSettingAvailableLocales()
    {
        $locales = Resource_Fixture::$localesAvailable;
        $locales[] = 'nl';

        // Set the value we're going to test in next step
        self::$service->setAvailableLocales($locales);

        $this->assertEquals(
            object_to_array(self::$service->getAvailableLocales()),
            $locales
        );
    }

    /**
     * Test: Set an encoding and retrieve it back.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @requires OS Linux
     */
    public function testSettingAndGettingAnCustomEncoding()
    {
        $encoding = Doozr_I18n_Service::ENCODING_ISO_8859_1;
        self::$service->setEncoding($encoding);
        $this->assertEquals($encoding, self::$service->getEncoding());

        $encoding = Doozr_I18n_Service::ENCODING_UTF_8;
        self::$service->setEncoding($encoding);
        $this->assertEquals($encoding, self::$service->getEncoding());
    }

    /**
     * Test: Set a domain.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @requires OS Linux
     */
    public function testUsingDomainFromPhptalInterfaceImplementation()
    {
        // Prepare
        $domain = 'default';

        // Assertion(s)
        $this->assertEquals([$domain], self::$service->useDomain($domain));
    }

    /**
     * Test: If setting a key value pair works.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @requires OS Linux
     */
    public function testSettingVarFromPhptalInterfaceImplementation()
    {
        // Prepare
        $key = 'Foo';
        $value = 'Bar';

        // Assertion(s)
        $this->assertTrue(self::$service->setVar($key, $value));
    }

    /**
     * Test: If translation works as expected. One time with html encoding one time without.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @requires OS Linux
     */
    public function testTranslatingAnKeyToDefaultLocaleEnUs()
    {
        // Prepare
        $key = 'Foo <p>Bar</p>';

        // Assertion(s)
        $this->assertEquals($key, self::$service->translate($key, false));
        $this->assertEquals(htmlentities($key), self::$service->translate($key, true));
    }

    /**
     * Test: If setting language works.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @requires OS Linux
     */
    public function testSettingLanguageFromPhptalInterfaceImplementation()
    {
        $language = Resource_Fixture::LOCALE_VALID;
        $this->assertTrue(self::$service->setLanguage($language));
    }

    public function testGetOtherInterface()
    {
        #$this->assertTrue(false);
    }
}
