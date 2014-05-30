<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Unit-Test
 *
 * HttpServiceTest.php - Test for Service
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2014, Benjamin Carl - All rights reserved.
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
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
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
 * @subpackage DoozR_Service_Http
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class HttpServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Contains the service instance for testing
     *
     * @var DoozR_Http_Service
     * @access protected
     */
    protected $service;

    // data for connection
    const PROTOCOL = 'http';
    const HOST     = 'google.de';
    const PORT     = 80;


    /**
     * SETUP
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setUp()
    {
        // Instanciate DoozR -> this will manage some base setup
        DoozR_Core::getInstance();

        $this->init();
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
        $this->service = null;
    }

    /**
     * Initialize the Service: Http
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function init()
    {
        // load the service with default Service-Loader
        $this->service = DoozR_Loader_Serviceloader::load('Http');
    }

    /**
     * Test: Is the Service "Http" loadable?
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function testLoadable()
    {
        // test if loaded service class is correct
        $this->assertEquals('DoozR_Http_Service', get_class($this->service));
    }

    public function testSetCredentials()
    {
        $user     = 'John';
        $password = 'Doe';

        // positive behavior
        $result = $this->service->setCredentials($user, $password);
        $this->assertTrue($result);

        $result = $this->service->credentials($user, $password);
        $this->assertInstanceOf('DoozR_Http_Service', $result);

        $credentials = $this->service->getCredentials();
        $this->assertArrayHasKey('user', $credentials);
        $this->assertArrayHasKey('password', $credentials);
        $this->assertEquals($user, $credentials['user']);
        $this->assertEquals($password, $credentials['password']);
    }

    public function testSetAndGetHost()
    {
        $result = $this->service->setHost(self::HOST);
        $this->assertTrue($result);

        $result = $this->service->host(self::HOST);
        $this->assertInstanceOf('DoozR_Http_Service', $result);

        $result = $this->service->getHost();
        $this->assertEquals(self::HOST, $result);
    }

    /**
     * @depends testSetAndGetHost
     */
    public function testSetAndGetPort()
    {
        $result = $this->service->setPort(self::PORT);
        $this->assertTrue($result);

        $result = $this->service->port(self::PORT);
        $this->assertInstanceOf('DoozR_Http_Service', $result);

        $result = $this->service->getPort();
        $this->assertEquals(self::PORT, $result);
    }

    /**
     * @depends testSetAndGetPort
     */
    public function testSetAndGetProtocol()
    {
        $result = $this->service->setProtocol(self::PROTOCOL);
        $this->assertTrue($result);

        $result = $this->service->protocol(self::PROTOCOL);
        $this->assertInstanceOf('DoozR_Http_Service', $result);

        $result = $this->service->getProtocol();
        $this->assertEquals(self::PROTOCOL, $result);
    }
}
