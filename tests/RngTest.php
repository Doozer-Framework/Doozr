<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Rng
 *
 * RngTest.php - Unit tests for rng functionality.
 *
 *
 * PHP versions 5.3
 *
 * LICENSE:
 * Rng - Random number generator for PHP
 *
 * Copyright (c) 2014 - 2015, Benjamin Carl
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice, this
 * list of conditions and the following disclaimer.
 *
 * - Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *
 * - Neither the name of Rng nor the names of its
 * contributors may be used to endorse or promote products derived from
 * this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * Please feel free to contact us via e-mail: opensource@clickalicious.de
 *
 * @category   Clickalicious
 * @package    Clickalicious_Rng
 * @subpackage Clickalicious_Rng_Tests
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2015 Benjamin Carl
 * @license    http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Rng
 */

require_once CLICKALICIOUS_RNG_BASE_PATH . 'Clickalicious/Rng/Generator.php';

use \Clickalicious\Rng\Generator;

/**
 * Rng
 *
 * Unit tests for client functionality.
 *
 * @category   Clickalicious
 * @package    Clickalicious_Rng
 * @subpackage Clickalicious_Rng_Tests
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2015 Benjamin Carl
 * @license    http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Rng
 */
class RngTest extends PHPUnit_Framework_TestCase
{
    /**
     * Prepare some stuff.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setUp()
    {
        #$this->generator = new Generator();
    }

    /**
     * Test: Trigger and handle SERVER ERROR.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testValidMode()
    {
        $this->assertInstanceOf(
            'Clickalicious\Rng\Generator',
            new Generator(null, Generator::MODE_PHP_MERSENNE_TWISTER)
        );
    }

    /**
     * Test: Trigger and handle SERVER ERROR.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     * @expectedException \Clickalicious\Rng\Exception
     */
    public function testInvalidMode()
    {
        $this->generator = new Generator(null, PHP_INT_MAX);
    }

    /**
     * Cleanup after single test. Remove the key created for tests.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function tearDown()
    {
    }
}
