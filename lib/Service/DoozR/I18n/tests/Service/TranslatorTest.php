<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Service - I18n - Test
 *
 * TranslatorTest.php - Tests for Translator of the DoozR I18n Service.
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
 * Tests for Translator of the DoozR I18n Service.
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_I18n
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link       http://clickalicious.github.com/DoozR/
 */
class TranslatorTest extends DoozR_Base_Service_Test_Abstract
{
    /**
     * Data required for running this test(s)
     *
     * @var array
     * @access protected
     */
    protected static $fixtures = array(
        'locale' => array(
            'default'   => 'de',
            'valid'     => 'de',
            'invalid'   => 'de-11111de-de-de',
            'available' => array(
                'ar',
                'de',
                'de-at',
                'en',
                'en-gb',
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
        'translation' => array(
            'missing' => 'This is a not translated string.'
        ),
    );


    /**
     * Prepares setup for Tests of "I18n"
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setUp()
    {
        self::$serviceName = 'I18n';
        parent::setUp();

        // Load service
        self::$service = DoozR_Loader_Serviceloader::load(self::$serviceName, self::$registry->getConfig());
    }

    /**
     * Tests if the service returns the correct translator
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function testGetTranslator()
    {
        $locale = self::$fixtures['locale']['valid'];
        self::$service->setActiveLocale($locale);

        $translator = self::$service->getTranslator();

        $this->assertInstanceOf(
            'DoozR_I18n_Service_Translator',
            $translator
        );

        $this->assertEquals($locale, $translator->getLocale());
    }

    /**
     * Tests if the service returns the correct translator
     * if a locale with redirect was passed.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function testGetTranslatorForRedirectLocale()
    {
        $translator = self::$service->getTranslator('en-gb');

        $this->assertInstanceOf(
            'DoozR_I18n_Service_Translator',
            $translator
        );

        $this->assertEquals('en', $translator->getLocale());
    }

    /**
     * Tests that a passed string isn't altered by translator
     * if the string isn't translated yet
     *
     */
    public function testTranslatorDoesNotAlterMissingTranslation()
    {
        $locale = self::$fixtures['locale']['valid'];
        self::$service->setActiveLocale($locale);

        $input = self::$fixtures['translation']['missing'];

        $translator = self::$service->getTranslator();
        $translator->setNamespace('default');

        $this->assertEquals($input, $translator->_($input));
    }

    /**
     * Tests if the try to translate a string without setting
     * a namespace first will throw an exception as warning for
     * the developer
     *
     * @expectedException DoozR_I18n_Service_Exception
     */
    public function testTranslationWithoutNamespaceThrowsException()
    {
        $locale = self::$fixtures['locale']['valid'];
        self::$service->setActiveLocale($locale);
        $translator = self::$service->getTranslator();
        $translationShouldFail = $translator->_('hour');
    }

    /**
     * Tests if simple translation will be successful
     */
    public function testTranslate()
    {
        $locale = self::$fixtures['locale']['valid'];
        self::$service->setActiveLocale($locale);

        $translator = self::$service->getTranslator();
        $translator->setNamespace('default');

        $this->assertEquals('Ja', $translator->_('Yes'));
    }

    /**
     * Tests if a more complex translation will be successful
     */
    public function testTranslateWithArguments()
    {
        $locale = self::$fixtures['locale']['valid'];
        self::$service->setActiveLocale($locale);

        $translator = self::$service->getTranslator();
        $translator->setNamespace('default');

        $translation = $translator->_('x_books_in_my_y_shelves', array(5, 23));

        $this->assertContains('5', $translation);
        $this->assertContains('23', $translation);
        $this->assertNotContains('666', $translation);
    }
}
