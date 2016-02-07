<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service - Test
 *
 * FormServiceTest.php - Tests for Service instance of Doozr Form Service.
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

use Faker\Factory as FakerFactory;

/**
 * Doozr - Form - Service - Test
 *
 * Tests for Service instance of Doozr Form Service.
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
 * @property   Doozr_Form_Service $service
 */
class FormServiceTest extends Doozr_Base_Service_Test_Abstract
{
    /**
     * Faker instance to generate random values for testing
     *
     * @var Faker\Generator
     */
    protected static $faker;

    /**
     * Prepares setup for Tests of "Form".
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setUp()
    {
        self::$serviceName = 'Form';

        /** INIT */
        static $status;

        // Function has already run
        if (null === $status) {
            // Get container instance from registry
            $map = self::$registry->getContainer()->getMap();

            // File containing mappings
            $file = Doozr_Loader_Serviceloader::getServicePath('form').DIRECTORY_SEPARATOR.'.map.json';

            // Generate map from static JSON map of Doozr
            $map->generate($file);

            // Get container instance from registry
            $container = self::$registry->getContainer();

            // Add map to existing maps in container
            $container->addToMap($map);

            // Create container and set factory and map
            $container->getMap()->wire(
                [
                    'doozr.i18n.service'          => Doozr_Loader_Serviceloader::load('i18n'),
                    'doozr.form.service.store'    => new Doozr_Form_Service_Store_UnitTest(),
                    'doozr.form.service.renderer' => new Doozr_Form_Service_Renderer_Html(),
                ]
            );

            self::$registry->setContainer($container);

            $status = 1;
        }

        /** END INIT */

        parent::setUp();

        // Get a faker instance with Doozr's default locale
        self::$faker = FakerFactory::create(
            $this->convertLocale(self::$registry->getConfiguration()->i18n->default->locale)
        );
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
     * Test: If the correct fieldname for token field is returned.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function testFieldnameToken()
    {
        // Test for default fieldname initially set
        $this->assertSame(
            Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_TOKEN,
            self::$service->getFieldnameToken()
        );

        // Generate, set & test getting a custom fieldname
        $fieldname = self::$faker->word();

        // Set random custom fieldname
        self::$service->setFieldnameToken($fieldname);

        // Now check for successful stored value
        $this->assertSame(
            $fieldname,
            self::$service->getFieldnameToken()
        );
    }

    /**
     * Test: If the correct fieldname for submitted field is returned.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function testFieldnameSubmitted()
    {
        // Test for default fieldname initially set
        $this->assertSame(
            Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_SUBMITTED,
            self::$service->getFieldnameSubmitted()
        );

        // Generate, set & test getting a custom fieldname
        $fieldname = self::$faker->word();

        // Set random custom fieldname
        self::$service->setFieldnameSubmitted($fieldname);

        // Now check for successful stored value
        $this->assertSame(
            $fieldname,
            self::$service->getFieldnameSubmitted()
        );
    }

    /**
     * Test: If the correct fieldname for step field is returned.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function testFieldnameStep()
    {
        $this->assertSame(
            Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_STEP,
            self::$service->getFieldnameStep()
        );

        // Generate, set & test getting a custom fieldname
        $fieldname = self::$faker->word();

        // Set random custom fieldname
        self::$service->setFieldnameStep($fieldname);

        // Now check for successful stored value
        $this->assertSame(
            $fieldname,
            self::$service->getFieldnameStep()
        );
    }

    /**
     * Test: If the correct fieldname for steps field is returned.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function testFieldnameSteps()
    {
        $this->assertSame(
            Doozr_Form_Service_Constant::PREFIX.Doozr_Form_Service_Constant::FORM_NAME_FIELD_STEPS,
            self::$service->getFieldnameSteps()
        );

        // Generate, set & test getting a custom fieldname
        $fieldname = self::$faker->word();

        // Set random custom fieldname
        self::$service->setFieldnameSteps($fieldname);

        // Now check for successful stored value
        $this->assertSame(
            $fieldname,
            self::$service->getFieldnameSteps()
        );
    }

    /**
     * Test: If a FormHandler can be retrieved for a custom scope.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function testFormHandler()
    {
        /*
        $request = new Doozr_Request_Cli(
            new Doozr_Request_State()
        );
        dump($request);
        die;
        */

        // Generate, set & test getting a custom fieldname
        $scope = self::$faker->word();
        $formHandler = self::$service->getFormHandler($scope);
        $this->assertInstanceOf('Doozr_Form_Service_FormHandler', $formHandler);
    }
}
