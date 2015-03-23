<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Clickalicious\Rng;

/**
 * Rng
 *
 * Generator.php - Random number generator for PHP
 * Fallback mechanism implementation based on current best practice.
 *
 *
 * PHP versions 5.3
 *
 * LICENSE:
 * Rng - Random number generator for PHP
 *
 * Copyright (c) 2015, Benjamin Carl
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
 * @subpackage Clickalicious_Rng_Generator
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2015 Benjamin Carl
 * @license    http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Rng
 */

require_once 'Exception.php';

use Clickalicious\Rng\Exception;

/**
 * Rng
 *
 * Random number generator for PHP with fallback mechanism implementation
 * based on current best practice.
 *
 * @category   Clickalicious
 * @package    Clickalicious_Rng
 * @subpackage Clickalicious_Rng_Generator
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2015 Benjamin Carl
 * @license    http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Rng
 */
class Generator
{
    /**
     * The seed for the RNG.
     * Static to prevent double seeding.
     *
     * @var null
     * @access protected
     */
    protected $seed;

    /**
     * The cryptographic quality switch.
     * Used in OpenSSL random byte generator for example.
     *
     * @var bool
     * @access protected
     */
    protected $crypto;

    /**
     * The active mode. Default set by constructor.
     *
     * @var int
     * @access protected
     */
    protected $mode;

    /**
     * The valid modes for validation.
     *
     * @var array
     * @access protected
     * @static
     */
    protected static $validModes = array(
        self::MODE_PHP_DEFAULT,
        self::MODE_PHP_MERSENNE_TWISTER,
        self::MODE_MCRYPT,
        self::MODE_OPEN_SSL
    );

    /**
     * PHP's default RNG
     * (e.g. srand() + rand())
     *
     * @var int
     * @access public
     * const
     * @see http://php.net/manual/de/function.srand.php
     *      http://php.net/manual/de/function.rand.php
     */
    const MODE_PHP_DEFAULT = 1;

    /**
     * Mersenne Twister Mode
     * (e.g. mt_srand() + mt_rand())
     *
     * @var int
     * @access public
     * const
     * @see http://de.wikipedia.org/wiki/Mersenne-Twister
     *      http://php.net/manual/de/function.mt-srand.php
     *      http://php.net/manual/de/function.mt-rand.php
     */
    const MODE_PHP_MERSENNE_TWISTER = 2;

    /**
     * MCRYPT based PHP /dev/urandom based PRNG implementation.
     *
     * @var int
     * @access public
     * const
     * @see http://php.net/manual/de/intro.mcrypt.php
     *      http://mcrypt.sourceforge.net/
     */
    const MODE_MCRYPT = 4;

    /**
     * OpenSSL based PHP PRNG implementation.
     *
     * @var int
     * @access public
     * const
     * @see http://php.net/manual/de/function.openssl-random-pseudo-bytes.php
     */
    const MODE_OPEN_SSL = 8;

    /**
     * Name of the extension "mcrypt" for better readability.
     *
     * @var string
     * @access public
     * @const
     */
    const EXTENSION_MCRYPT = 'mcrypt';


    /**
     * Constructor.
     *
     * @param int      $mode   The mode used for generating random numbers.
     *                         Default is MCRYPT as the currently best practice for generating random numbers
     * @param int|null $seed   The optional seed used for randomizer init
     * @param bool     $crypto TRUE (default) to enable cryptographic crypto (pseudo) randomness
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     */
    public function __construct(
        $mode   = self::MODE_OPEN_SSL,
        $seed   = null,
        $crypto = true
    ) {
        $this
            ->crypto($crypto)
            ->mode($mode);

        // Only seed if seed passed -> no longer required (since PHP 4.2.0)
        if ($seed !== null) {
            $this->seed($seed);
        }
    }

    /**
     * Generates and returns a (pseudo) random number.
     *
     * @param int $rangeMinimum The minimum value of range
     * @param int $rangeMaximum The maximum value of range
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     * @return int The generated (pseudo) random number
     */
    public function generate($rangeMinimum = 0, $rangeMaximum = PHP_INT_MAX)
    {
        switch ($this->getMode()) {

            case self::MODE_OPEN_SSL:
                $randomValue = $this->genericRand($rangeMinimum, $rangeMaximum, self::MODE_OPEN_SSL);
                break;

            case self::MODE_MCRYPT:
                $randomValue = $this->genericRand($rangeMinimum, $rangeMaximum, self::MODE_MCRYPT);
                break;

            case self::MODE_PHP_MERSENNE_TWISTER:
                $randomValue = $this->mtRand($rangeMinimum, $rangeMaximum);
                break;

            case self::MODE_PHP_DEFAULT:
                $randomValue = $this->rand($rangeMinimum, $rangeMaximum);
                break;
        }

        return $randomValue;
    }

    /**
     * Generate the seed from microtime.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     * @return int The seed value
     */
    public function generateSeed()
    {
        list ($usec, $sec) = explode(' ', microtime());
        return (int)($sec + strrev($usec * 1000000)) + 13;
    }

    /**
     * "rand" based randomize.
     *
     * @param int $rangeMinimum The minimum range border for randomizer
     * @param int $rangeMaximum The maximum range border for randomizer
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access protected
     * @return int From *closed* interval [$min, $max]
     */
    protected function rand($rangeMinimum, $rangeMaximum)
    {
        return rand($rangeMinimum, $rangeMaximum);
    }

    /**
     * "mt_rand" based randomize.
     *
     * @param int $rangeMinimum The minimum range border for randomizer
     * @param int $rangeMaximum The maximum range border for randomizer
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access protected
     * @return int From *closed* interval [$min, $max]
     */
    protected function mtRand($rangeMinimum, $rangeMaximum)
    {
        return mt_rand($rangeMinimum, $rangeMaximum);
    }

    /**
     * "mcrypt" based equivalent to rand & mt_rand but better randomness.
     *
     * @param int $rangeMinimum The minimum range border for randomizer
     * @param int $rangeMaximum The maximum range border for randomizer
     * @param int $source       The source of the random bytes (OpenSSL, MCrypt, ...)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return int From *closed* interval [$min, $max]
     * @access protected
     * @throws \Clickalicious\Rng\Exception
     */
    protected function genericRand(
        $rangeMinimum,
        $rangeMaximum,
        $source = self::MODE_MCRYPT
    ) {
        $diff = $rangeMaximum - ($rangeMinimum + 1);

        if ($diff > PHP_INT_MAX) {
            throw new Exception('Bad range');
        }

        // The largest *multiple* of diff less than our sample
        $ceiling = floor(PHP_INT_MAX / $diff) * $diff;

        do {
            switch ($source) {
                case self::MODE_MCRYPT:
                    $bytes = $this->getRandomBytesFromMcrypt(PHP_INT_SIZE);
                    break;

                case self::MODE_OPEN_SSL:
                default:
                    $bytes = $this->getRandomBytesFromOpenSSL(PHP_INT_SIZE, $this->getCrypto());
                    break;
            }

            // Check for error
            if (false === $bytes || PHP_INT_SIZE !== strlen($bytes)) {
                throw new Exception(
                    sprintf(
                        'Failed to read %s bytes from %s.',
                        PHP_INT_SIZE,
                        ($source === self::MODE_MCRYPT) ? 'MCrypt' : 'OpenSSL'
                    )
                );
            }

            if (PHP_INT_SIZE === 8) {
                // 64-bit versions
                list($higher, $lower) = array_values(unpack('N2', $bytes));
                $val = $higher << 32 | $lower;

            } else {
                // 32-bit versions
                $val = unpack('Nint', $bytes);
            }

            $val = $val['int'] & PHP_INT_MAX;

        } while ($val > $ceiling);

        // In the unlikely case our sample is bigger than largest multiple, just do over until it’s not any more.
        // Perfectly even sampling in our 0<output<diff domain is mathematically impossible unless the total number of
        // *valid* inputs is an exact multiple of diff.
        return $val % $diff + $rangeMinimum;
    }

    /**
     * Returns random bytes from MCrypt.
     *
     * @param int $numberOfBytes The number of bytes to read and return
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access protected
     * @return string The random bytes
     * @throws \Clickalicious\Rng\Exception
     */
    protected function getRandomBytesFromOpenSSL($numberOfBytes, $cryptographic)
    {
        return openssl_random_pseudo_bytes($numberOfBytes, $cryptographic);
    }

    /**
     * Returns random bytes from MCrypt.
     *
     * @param int $numberOfBytes The number of bytes to read and return
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access protected
     * @return string The random bytes
     * @throws \Clickalicious\Rng\Exception
     */
    protected function getRandomBytesFromMcrypt($numberOfBytes)
    {
        return mcrypt_create_iv($numberOfBytes, MCRYPT_DEV_URANDOM);
    }

    /**
     * Checks if requirements for mode are fulfilled.
     *
     * @param int $mode The mode to check requirements for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access protected
     * @return bool TRUE on success, otherwise FALSE
     * @throws \Clickalicious\Rng\Exception
     */
    protected function checkRequirements($mode)
    {
        if (in_array($mode, self::$validModes, true) !== true) {
            throw new Exception(
                sprintf('Unsupported mode "%s"', $mode)
            );
        }

        switch ($mode) {
            case self::MODE_MCRYPT:
                if (extension_loaded(self::EXTENSION_MCRYPT) !== true) {
                    throw new Exception(
                        sprintf('Extension "%s" not loaded but required!', self::EXTENSION_MCRYPT)
                    );
                }
                break;

            case self::MODE_OPEN_SSL:
            case self::MODE_PHP_DEFAULT:
            case self::MODE_PHP_MERSENNE_TWISTER:
            default:
                // Intentionally omitted cause not required - listed here for code quality and readability
                break;
        }

        return true;
    }

    /**
     * Setter for mode.
     *
     * @param int $mode The mode to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     * @return void
     */
    public function setMode($mode)
    {
        // Check for requirements depending on mode
        if ($this->checkRequirements($mode) === true) {
            $this->mode = $mode;
        }
    }

    /**
     * Fluent setter for mode.
     *
     * @param int $mode The mode to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     * @return $this Instance for chaining
     */
    public function mode($mode)
    {
        $this->setMode($mode);
        return $this;
    }

    /**
     * Getter for mode.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     * @return int The active mode
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Setter for seed.
     *
     * @param int $seed The seed value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     * @return void
     */
    public function setSeed($seed)
    {
        // We need to call different methods depending on chosen source
        switch ($this->getMode()) {

            case self::MODE_PHP_MERSENNE_TWISTER:
                mt_srand($seed);
                break;

            case self::MODE_PHP_DEFAULT:
                srand($seed);
                break;

            case self::MODE_MCRYPT:
            case self::MODE_OPEN_SSL:
            default:
                // Intentionally left blank
                break;
        }

        $this->seed = $seed;
    }

    /**
     * Setter for seed.
     *
     * @param int $seed The seed value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     * @return $this Instance for chaining
     */
    public function seed($seed)
    {
        if (is_int($seed) !== true) {
            throw new Exception(
                sprintf('The type of the seed value "%s" need to be int. You passed a(n) "%s".', $seed, gettype($seed))
            );
        }

        $this->setSeed($seed);
        return $this;
    }

    /**
     * Getter for seed.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     * @return int|null The seed value if set, otherwise FALSE
     */
    public function getSeed()
    {
        return $this->seed;
    }

    /**
     * Setter for crypto.
     *
     * @param bool $crypto TRUE to set cryptographic flag, FALSE to disable
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     * @return void
     */
    protected function setCrypto($crypto)
    {
        $this->crypto = $crypto;
    }

    /**
     * Setter for crypto.
     *
     * @param bool $crypto TRUE to set cryptographic flag, FALSE to disable
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     * @return $this Instance for chaining
     */
    protected function crypto($crypto)
    {
        $this->setCrypto($crypto);
        return $this;
    }

    /**
     * Getter for crypto.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     * @return bool|null The crypto flag if set, otherwise NULL
     */
    protected function getCrypto()
    {
        return $this->crypto;
    }
}
