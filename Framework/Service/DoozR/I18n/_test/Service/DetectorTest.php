<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Service - I18n - Test
 *
 * DetectorTest.php - Tests for Detector of the DoozR I18n Service.
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2013, Benjamin Carl - All rights reserved.
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
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

$filename = realpath(dirname(__FILE__) . '../../../../../../DoozR/Bootstrap.php');

if (file_exists($filename)) {
    require_once $filename;
} else {
    require_once '../../../../../DoozR/Bootstrap.php';
}

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Service/Test/Abstract.php';

/**
 * DoozR - Service - I18n - Test
 *
 * Tests for Abstract interface of the DoozR I18n Service.
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_I18n
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link       http://clickalicious.github.com/DoozR/
 */
class DetectorTest extends DoozR_Base_Service_Test_Abstract
{
    /**
     * Data required for running this test(s)
     *
     * @var array
     * @access protected
     */
    protected static $fixtures = array(
        'locale' => array(
            'default' => 'en',
            'valid'   => 'de',
            'invalid' => 'de-11111de-de-de',
        ),
        'translation' => array(
            'missing' => 'This is a not translated string.'
        ),
        'namespace' => array(
            'valid' => 'default',
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
    }

    /**
     * Tests ...
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function testGetDetector()
    {
        $detector = self::$service->getDetector();

        $this->assertInstanceOf('DoozR_I18n_Service_Detector', $detector);
    }

    public function testiIsValidLocaleCode()
    {
        $detector = self::$service->getDetector();

        $this->assertTrue($detector->isValidLocaleCode('de'));
        $this->assertTrue($detector->isValidLocaleCode('en'));
        $this->assertTrue($detector->isValidLocaleCode('en-gb'));
        $this->assertFalse($detector->isValidLocaleCode('fr fr'));
        $this->assertFalse($detector->isValidLocaleCode('f'));
        $this->assertFalse($detector->isValidLocaleCode('foo'));
    }

    public function testDetect()
    {
        $detector = self::$service->getDetector();
        $this->assertTrue($detector->detect());
    }

    public function testGetLocalePreferences()
    {
        $detector = self::$service->getDetector();
        $preferences = $detector->getLocalePreferences();

        $this->assertArrayHasKey('locale', $preferences);
        $this->assertArrayHasKey('language', $preferences);
        $this->assertArrayHasKey('country', $preferences);

        $this->assertEquals('en', $preferences['locale']);
        $this->assertEquals('en', $preferences['language']);
        $this->assertEquals('us', $preferences['country']);
    }
}