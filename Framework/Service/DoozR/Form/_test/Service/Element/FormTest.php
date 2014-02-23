<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Unit-Test
 *
 * FormTest.php - Test for Form
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
 * @subpackage DoozR_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once 'PHPUnit/Autoload.php';
require_once 'DoozR/Bootstrap.php';

/**
 * DoozR - Unit-Test
 *
 * Test for Service
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * The Form Element Test Subject
     *
     * @var DoozR_Form_Service_Element_Form
     * @access protected
     */
    protected static $form;

    protected static $name = 'PHPUnit';


    public static function setUpBeforeClass()
    {
        self::$form = new DoozR_Form_Service_Element_Form();
    }

    public static function tearDownAfterClass()
    {
        self::$form = null;
    }

    /**
     * SETUP
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setUp()
    {
        #$this-
    }

    /**
     * TEARDOWN
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function tearDown()
    {
        // unset
    }


    /**
     * Test: Is the Service "Form" loadable?
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function testInit()
    {
        // test if loaded service class is correct
        $this->assertEquals('DoozR_Form_Service_Element_Form', get_class(self::$form));

        //$mock = $this->getMock('DoozR_Form_Service');
        //$this->assertTrue($mock instanceof DoozR_Form_Service);
    }

    public function testInitiallyValid()
    {
        $this->assertTrue(self::$form->isValid());
    }


    public function testSetAndGetName()
    {
        $this->assertNotEquals(self::$name, self::$form->getName());
        $this->assertEquals('', self::$form->getName());
        self::$form->setName(self::$name);
        $this->assertEquals(self::$name, self::$form->getName());
    }


    public function testSetAndGetEncodingType()
    {
        $this->assertNull(self::$form->getEncodingType());
        self::$form->setEncodingType(
            DoozR_Form_Service_Constant::ENCODING_TYPE_DEFAULT
        );
        $this->assertEquals(
            DoozR_Form_Service_Constant::ENCODING_TYPE_DEFAULT,
            self::$form->getEncodingType()
        );
    }

    /**

    public function testSetAndGetStore()
    {
        $store = new DoozR_Form_Service_Store_Unit();

        $this->assertEquals('DoozR_Form_Service_Store_Unit', get_class($store));
        $this->assertNull(self::$form->getStore());
        $this->assertTrue(self::$form->setStore($store));
    }

    public function testSetAndGetRenderer()
    {
        $renderer = new DoozR_Form_Service_Renderer_Unit();

        $this->assertEquals('DoozR_Form_Service_Renderer_Unit', get_class($renderer));
        $this->assertNull(self::$form->getRenderer());
        $this->assertTrue(self::$form->setRenderer($renderer));
    }

    public function testSetAndGetArguments()
    {
        $this->assertNull(self::$form->getArguments());
    }

    public function testIsSubmitted()
    {
        $this->assertFalse(self::$form->isSubmitted());
    }

    public function testIsValid()
    {
        $this->assertTrue(self::$form->isValid());
    }

    public function testIsFinished()
    {
        $this->assertFalse(self::$form->isFinished());
    }

    public function testSetAndGetStep()
    {
        $testStep = 3;
        $this->assertEquals(1, self::$form->getStep());
        $this->assertTrue(self::$form->setStep($testStep));
        // returns still 1 cause the form wasn't submitted
        $this->assertEquals(1, self::$form->getStep());
    }

    public function testSetAndGetSteps()
    {
        $testSteps = 2;
        $this->assertEquals(DoozR_Form_Service_Constant::STEP_DEFAULT_LAST, self::$form->getSteps());
        $this->assertTrue(self::$form->setSteps($testSteps));
        $this->assertEquals($testSteps, self::$form->getSteps());
    }

    public function testSetAndGetI18n()
    {
        $registry = DoozR_Registry::getInstance();
        $i18n     = DoozR_Loader_Serviceloader::load('i18n', $registry->config, 'de');

        $this->assertNull(self::$form->getI18n());
        $this->assertTrue(self::$form->setI18n($i18n));
        $this->assertInstanceOf('DoozR_I18n_Service', self::$form->getI18n());
        $this->assertSame($i18n, self::$form->getI18n());
    }

    public function testRender()
    {
        //render
        #$this->assertExc
    }
    */
}
