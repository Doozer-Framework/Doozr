<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Service - I18n - Test
 *
 * I18nServiceTest.php - Tests for Service instance of DoozR I18n Service.
 *
 * PHP versions 5.4
 *
 * LICENSE:
 * DoozR - The lightweight PHP-Framework for high-performance websites
 *
 * Copyright (c) 2005 - 2015, Benjamin Carl - All rights reserved.
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
 * @subpackage DoozR_Service_I18n
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Service/Test/Abstract.php';

/**
 * DoozR - Service - I18n - Test
 *
 * Tests for Service instance of DoozR I18n Service.
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_I18n
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class I18nServiceTest extends DoozR_Base_Service_Test_Abstract
{
    /**
     * Data required for running this test(s)
     *
     * @var array
     * @access protected
     */
    protected static $fixtures = array(
        'locale' => array(
            'default'   => 'en-us',
            'valid'     => 'en-us',
            'invalid'   => 'de-11111de-de-de',
            'available' => array(
                'ar',
                'de',
                'de-at',
                'en',
                'en-gb',
                'en-us',
                'es',
                'fr',
                'it',
                'ru',
            ),
        ),
        'formatter' => array(
            'Currency',
            'Datetime',
            'Measure',
            'Number',
            'String',
        ),
    );

    /**
     * Prepares setup for Tests of "I18n"
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setUp()
    {
        self::$serviceName = 'I18n';
        parent::setUp();

        // Load service
        self::$service = DoozR_Loader_Serviceloader::load(self::$serviceName, self::$registry->getConfig());
    }

    /**
     * Tests if the default locale is returned correctly if no
     * locale was passed to I18n service instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function testDefaultLocale()
    {
        $this->assertEquals(self::$fixtures['locale']['default'], self::$service->getActiveLocale());
    }

    /**
     * Tests if it is possible to set a locale
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function testSetLocale()
    {
        $locale = self::$fixtures['locale']['valid'];

        self::$service->setActiveLocale($locale);
        $this->assertEquals($locale, self::$service->getActiveLocale());
    }

    /**
     * Tests if the service throws an exception if someone tries to set
     * an invalid locale
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     *
     * @expectedException DoozR_I18n_Service_Exception
     */
    public function testSetInvalidLocale()
    {
        $locale = self::$fixtures['locale']['invalid'];

        self::$service->setActiveLocale($locale);
    }

    /**
     * Tests if the service returns an valid detector instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function testDetector()
    {
        $this->assertInstanceOf('DoozR_I18n_Service_Detector', self::$service->getDetector());
    }

    /**
     * Tests if the service returns the correct detected locale
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function testGetClientPreferredLocale()
    {
        $locale = self::$fixtures['locale']['valid'];

        $this->assertEquals($locale, self::$service->getClientPreferredLocale());
    }

    /**
     * Tests if the service returns the correct currency formatter
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function testGetCurrencyLocalizer()
    {
        $locale = self::$fixtures['locale']['valid'];
        self::$service->setActiveLocale($locale);

        /* @var DoozR_I18n_Service_Localize_Currency $currency */
        $currency = self::$service->getLocalizer('Currency');

        $this->assertInstanceOf(
            'DoozR_I18n_Service_Localize_Currency',
            $currency
        );

        $this->assertEquals($locale, $currency->getLocale());
    }

    /**
     * Tests if the service returns the correct datetime formatter
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function testGetDatetimeLocalizer()
    {
        $locale = self::$fixtures['locale']['valid'];
        self::$service->setActiveLocale($locale);

        /* @var DoozR_I18n_Service_Localize_Datetime $datetime */
        $datetime = self::$service->getLocalizer('Datetime');

        $this->assertInstanceOf(
            'DoozR_I18n_Service_Localize_Datetime',
            $datetime
        );

        $this->assertEquals($locale, $datetime->getLocale());
    }

    /**
     * Tests if the service returns the correct measure formatter
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function testGetMeasureLocalizer()
    {
        $locale = self::$fixtures['locale']['valid'];
        self::$service->setActiveLocale($locale);

        /* @var DoozR_I18n_Service_Localize_Measure $measure */
        $measure = self::$service->getLocalizer('Measure');

        $this->assertInstanceOf(
            'DoozR_I18n_Service_Localize_Measure',
            $measure
        );

        $this->assertEquals($locale, $measure->getLocale());
    }

    /**
     * Tests if the service returns the correct number formatter
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function testGetNumberLocalizer()
    {
        $locale = self::$fixtures['locale']['valid'];
        self::$service->setActiveLocale($locale);

        /* @var DoozR_I18n_Service_Localize_Number $number */
        $number = self::$service->getLocalizer('Number');

        $this->assertInstanceOf(
            'DoozR_I18n_Service_Localize_Number',
            $number
        );

        $this->assertEquals($locale, $number->getLocale());
    }

    /**
     * Tests if the service returns the correct string formatter
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function testGetStringLocalizer()
    {
        $locale = self::$fixtures['locale']['valid'];
        self::$service->setActiveLocale($locale);

        /* @var DoozR_I18n_Service_Localize_String $string */
        $string = self::$service->getLocalizer('String');

        $this->assertInstanceOf(
            'DoozR_I18n_Service_Localize_String',
            $string
        );

        $this->assertEquals($locale, $string->getLocale());
    }


    public function testGetLocalizerWithRedirectLocale()
    {
        #$locale = 'en-gb';
        #self::$service->setActiveLocale($locale);

        /* @var DoozR_I18n_Service_Localize_String $string */
        #$string = self::$service->getLocalizer('String');

        #$this->assertInstanceOf(
        #    'DoozR_I18n_Service_Localize_String',
        #    $string
        #);

        #$this->assertEquals('en', $string->getLocale());
    }


    public function testGetAvailableLocales()
    {
        $this->assertEquals(
            object_to_array(self::$service->getAvailableLocales()),
            self::$fixtures['locale']['available']
        );
    }

    public function testSetAvailableLocales()
    {
        $locales = self::$fixtures['locale']['available'];
        $locales[] = 'nl';

        $this->assertEquals(
            $locales, self::$service->setAvailableLocales($locales)
        );

        $this->assertEquals(
            object_to_array(self::$service->getAvailableLocales()),
            $locales
        );
    }

    public function testSetAndGetEncoding()
    {
        $encoding = 'ISO-8859-1';

        $this->assertTrue(self::$service->setEncoding($encoding));
        $this->assertEquals($encoding, self::$service->getEncoding());
    }

    public function testUseDomain()
    {
        $domain = 'foo';

        $this->assertEquals(array($domain), self::$service->useDomain($domain));
    }

    public function testSetVar()
    {
        $key   = 'Foo';
        $value = 'Bar';

        $this->assertTrue(self::$service->setVar($key, $value));
    }

    public function testTranslate()
    {
        $key = 'Foo <p>Bar</p>';
        $this->assertEquals($key, self::$service->translate($key, false));
        $this->assertEquals('Foo <p>Bar</p>', self::$service->translate($key, true));
    }

    public function testSetLanguage()
    {
        $language = self::$fixtures['locale']['valid'];
        $this->assertTrue(self::$service->setLanguage($language));
    }

    /**
     * @covers DoozR_I18n_Service::install()
     */
    public function testInstall()
    {
        try {
            $result = self::$service->install();
            $this->assertTrue($result);

        } catch (Exception $e) {
            $this->assertInstanceOf('DoozR_I18n_Service_Exception', $e);

        }
    }
}
