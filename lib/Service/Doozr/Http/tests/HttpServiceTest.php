<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Service - Http - Test
 *
 * HttpServiceTest.php - Tests for Service instance of Doozr Http Service.
 *
 * PHP versions 5.5
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
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
 * @category   Doozr
 * @package    Doozr_Service
 * @subpackage Doozr_Service_I18n
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Service/Test/Abstract.php';

/**
 * Doozr - Service - Http - Test
 *
 * Tests for Service instance of Doozr Http Service.
 *
 * @category   Doozr
 * @package    Doozr_Service
 * @subpackage Doozr_Service_Http
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class HttpServiceTest extends Doozr_Base_Service_Test_Abstract
{
    /**
     * Protocol used
     *
     * @var string
     * @access public
     */
    const PROTOCOL = 'http';

    /**
     * Host to connect to for test
     *
     * @var string
     * @access public
     */
    const HOST = 'google.de';

    /**
     * Port used
     *
     * @var int
     * @access public
     */
    const PORT = 80;

    /**
     * Username used
     *
     * @var string
     * @access public
     */
    const USERNAME = 'John';

    /**
     * Password used
     *
     * @var int
     * @access public
     */
    const PASSWORD = 'Doe';

    /**
     * Prepares setup for Tests
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setUp()
    {
        self::$serviceName = 'Http';
        parent::setUp();

        // Load service
        self::$service = Doozr_Loader_Serviceloader::load(self::$serviceName);
    }

    /**
     * Tests if it is possible to set credentials and retrieve them back
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function testSetCredentials()
    {
        // Positive behavior test
        $result = self::$service->setCredentials(self::USERNAME, self::PASSWORD);
        $this->assertTrue($result);

        $result = self::$service->credentials(self::USERNAME, self::PASSWORD);
        $this->assertInstanceOf(self::$serviceClassName, $result);

        $credentials = self::$service->getCredentials();

        $this->assertArrayHasKey('user', $credentials);
        $this->assertArrayHasKey('password', $credentials);

        $this->assertEquals(self::USERNAME, $credentials['user']);
        $this->assertEquals(self::PASSWORD, $credentials['password']);
    }

    /**
     * Tests if it is possible to set a host and retrieve it back
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function testSetAndGetHost()
    {
        $result = self::$service->setHost(self::HOST);
        $this->assertTrue($result);

        $result = self::$service->host(self::HOST);
        $this->assertInstanceOf(self::$serviceClassName, $result);

        $result = self::$service->getHost();
        $this->assertEquals(self::HOST, $result);
    }

    /**
     * Tests if it is possible to set a port and retrieve it back
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function testSetAndGetPort()
    {
        $result = self::$service->setPort(self::PORT);
        $this->assertTrue($result);

        $result = self::$service->port(self::PORT);
        $this->assertInstanceOf(self::$serviceClassName, $result);

        $result = self::$service->getPort();
        $this->assertEquals(self::PORT, $result);
    }

    /**
     * Tests if it is possible to set a protocol and retrieve it back
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function testSetAndGetProtocol()
    {
        $result = self::$service->setProtocol(self::PROTOCOL);
        $this->assertTrue($result);

        $result = self::$service->protocol(self::PROTOCOL);
        $this->assertInstanceOf(self::$serviceClassName, $result);

        $result = self::$service->getProtocol();
        $this->assertEquals(self::PROTOCOL, $result);
    }
}
