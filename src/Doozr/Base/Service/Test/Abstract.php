<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Service - I18n - Test.
 *
 * ServiceTest.php - This is the Test-Controller of a Service Test
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

/**
 * Doozr - Service - I18n - Test.
 *
 * This is the Test-Controller of a Service Test
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
abstract class Doozr_Base_Service_Test_Abstract extends PHPUnit_Framework_TestCase
{
    /**
     * The Service instance for testing.
     *
     * @var Doozr_Base_Service_Interface
     */
    protected static $service;

    /**
     * The name of the Service.
     *
     * @var string
     */
    protected static $serviceName;

    /**
     * ClassName of Service.
     *
     * @var string
     */
    protected static $serviceClassName;

    /**
     * The Doozr Kernel instance.
     *
     * @var \Doozr_Kernel_App
     */
    protected static $kernel;

    /**
     * The Doozr Registry.
     *
     * @var Doozr_Registry
     */
    protected static $registry;

    /**
     * Prepares setup for Tests.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setUp()
    {
        /* @var $app Doozr_Kernel_App Get kernel instance */
        self::$kernel = Doozr_Kernel_App::boot(
            DOOZR_APP_ENVIRONMENT,
            DOOZR_RUNTIME_ENVIRONMENT,
            DOOZR_UNIX,
            DOOZR_DEBUGGING,
            DOOZR_CACHING,
            DOOZR_CACHING_CONTAINER,
            DOOZR_LOGGING,
            DOOZR_PROFILING,
            DOOZR_APP_ROOT,
            DOOZR_DIRECTORY_TEMP,
            DOOZR_DOCUMENT_ROOT,
            DOOZR_NAMESPACE,
            DOOZR_NAMESPACE_FLAT
        );

        // Store className
        self::$serviceClassName = 'Doozr_'.self::$serviceName.'_Service';

        // Get registry
        self::$registry = Doozr_Registry::getInstance();

        // Load service
        self::$service = Doozr_Loader_Serviceloader::load(self::$serviceName);
    }

    /**
     * Test: Generic - if the service is loadable and the existing instance matches the required instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function testEnsureServiceIsLoadable()
    {
        $this->assertInstanceOf(self::$serviceClassName, self::$service);
    }

    /**
     * Cleanup after test execution.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function tearDown()
    {
        self::$service = null;
    }
}
