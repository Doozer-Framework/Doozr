<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Password - Service
 *
 * Service.php - Password Service of the DoozR Framework.
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
 * @subpackage DoozR_Service_Password
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Service/Multiple/Facade.php';

/**
 * DoozR - Password - Service
 *
 * Service.php - Password Service of the DoozR Framework.
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Password
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Release: @package_version@
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 * @DoozRType  Multiple
 */
class DoozR_Password_Service extends DoozR_Base_Service_Multiple_Facade
{
    /**
     * type of userfriendly (speakable) passwords like
     * "KobuGeMa" or "HaKeLoPi" ...
     *
     * @var integer
     * @access private
     * @static
     */
    const PASSWORD_USERFRIENDLY = 0;

    /**
     * type of userfriendly (speakable) passwords like
     * "KobuGeMa" or "HaKeLoPi" ...
     *
     * @var integer
     * @access private
     * @static
     */
    const PASSWORD_USERFRIENDLY_REMEMBER = 1;

    /**
     * type of alphanum password - all Alpha's lower + upper + all
     * digits
     *
     * @var integer
     * @access private
     * @const
     */
    const PASSWORD_ALPHANUM = 2;

    /**
     * type of alphanum password - all Alpha's lower + upper + all
     * digits + standard special chars like !"�%&/()??'*:; ...
     *
     * @var integer
     * @access private
     * @const
     */
    const PASSWORD_ALPHANUM_SPECIAL = 3;

    /**
     * type of alphanum password - all Alpha's lower + upper + all
     * digits + standard special chars like !"�%&/()??'*:; ...
     * AND special chars like ^�~ ...
     *
     * @var integer
     * @access private
     * @const
     */
    const PASSWORD_ALPHANUM_SPECIAL_HARDCORE = 4;

    /**
     * holds the return type for password - plain
     *
     * @var integer
     * @access private
     * @const
     */
    const RETURN_TYPE_PLAIN = 0;

    /**
     * holds the return type for password - md5
     *
     * @var integer
     * @access private
     * @const
     */
    const RETURN_TYPE_MD5 = 1;

    /**
     * holds the return type for password - Passwordhash
     * Passwordhash is DoozR's and some other major software's
     * hashing framework. More:
     *
     * @var integer
     * @access private
     * @const
     */
    const RETURN_TYPE_PASSWORDHASH = 2;

    /**
     * holds the range of chars (ASC) for userfriendly passes
     *
     * VALID AREAS
     * [default] are the chars which can be used in every "1ST" turn of generation
     * [special] are the chars which can be used in every "2ND" turn of generation
     *
     * SYNTAX:
     * we use a char's ASCII representation e.g. 65 = A
     * you can define an area: e.g. 65-68 [get converted to 65,66,67,68]
     * or you can define an single char e.g. 65 [no need to convert]
     *
     * @var array
     * @access private
     * @static
     */
    private static $_RANGE_USERFRIENDLY = array(
        // A-Z without A,E,I,O,U + a-z without a,e,i,o,u
        'default' => '66-68,70-72,74-78,80-84,86-90,98-100,102-104,106-110,102-104,112-116,118-122',
        // A,E,I,O,U + a,e,i,o,u
        'special' => '65,69,73,79,85,97,101,105,111,117'
    );

    /**
     * holds the range of chars (ASC) for userfriendly rememberal passes
     *
     * VALID AREAS
     * [default] are the chars which can be used in every "1ST" turn of generation
     * [special] are the chars which can be used in every "2ND" turn of generation
     *
     * SYNTAX:
     * we use a char's ASCII representation e.g. 65 = A
     * you can define an area: e.g. 65-68 [get converted to 65,66,67,68]
     * or you can define an single char e.g. 65 [no need to convert]
     *
     * @var array
     * @access private
     * @static
     */
    private static $_RANGE_USERFRIENDLY_REMEMBER = array(
        // A-Z without A,E,I,O,U + a-z without a,e,i,o,u
        'default' => '66,68,70-72,75,77-78,80,82-84',
        // A,E,I,O,U + a,e,i,o,u
        'special' => '97,101,105,111,117'
    );

    /**
     * holds the range of chars (ASC) for alphanum passes
     *
     * VALID AREAS
     * [default] chars which can be used in every turn
     *
     * SYNTAX:
     * we use a char's ASCII representation e.g. 65 = A
     * you can define an area: e.g. 65-68 [get converted to 65,66,67,68]
     * or you can define an single char e.g. 65 [no need to convert]
     *
     * @var array
     * @access private
     * @static
     */
    private static $_RANGE_ALPHANUM = array(
        // A-Z + a-z + 0 - 9
        'default' => '48-57,65-90,97-122'
    );

    /**
     * holds the range of chars (ASC) for alphanum passes
     *
     * VALID AREAS
     * [default] chars which can be used in every turn
     *
     * SYNTAX:
     * we use a char's ASCII representation e.g. 65 = A
     * you can define an area: e.g. 65-68 [get converted to 65,66,67,68]
     * or you can define an single char e.g. 65 [no need to convert]
     *
     * @var array
     * @access private
     * @static
     */
    private static $_RANGE_ALPHANUM_SPECIAL = array(
        // A-Z + a-z + 0 - 9 + !"#$%&()*+,-./:;<=>?@[\]_{}
        'default' => '48-57,65-90,97-122,33-38,40-47,58-64,91,93,95,123,125'
    );

    /**
     * holds the range of chars (ASC) for alphanum passes
     *
     * VALID AREAS
     * [default] chars which can be used in every turn
     *
     * SYNTAX:
     * we use a char's ASCII representation e.g. 65 = A
     * you can define an area: e.g. 65-68 [get converted to 65,66,67,68]
     * or you can define an single char e.g. 65 [no need to convert]
     *
     * @var array
     * @access private
     * @static
     */
    private static $_RANGE_ALPHANUM_SPECIAL_HARDCORE = array(
        // A-Z + a-z + 0 - 9 + !"#$%&()*+,-./:;<=>?@[\]_{} + '^`|~
        'default' => '48-57,65-90,97-122,33-38,40-47,58-64,91,93,95,123,125,39,94,96,124,126'
    );

    /**
     * holds a reference to module passwordhash
     *
     * @var object
     * @access private
     * @static
     */
    private static $_passwordhash = null;


    private $_config;


    /**
     * constructs the class
     *
     * constructor builds the class
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object An instance of this class
     * @access public
     */
    public function __tearup()
    {
        //
        $this->_config = $this->registry->config;

        // construct password matrices
        $this->_initPasswordMatrices();
    }

    /**
     * initializes the password matrices and lex the syntax to usable chars
     *
     * This method is intend to initialize the password matrices and lex the syntax to usable chars.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _initPasswordMatrices()
    {
        // init matrix for userfriendly passwords
        self::$_RANGE_USERFRIENDLY = $this->_matrixLexer(self::$_RANGE_USERFRIENDLY);

        // init matrix for userfriendliest rememberal passwords
        self::$_RANGE_USERFRIENDLY_REMEMBER = $this->_matrixLexer(self::$_RANGE_USERFRIENDLY_REMEMBER);

        // init matrix for alphanumeric passwords
        self::$_RANGE_ALPHANUM = $this->_matrixLexer(self::$_RANGE_ALPHANUM);

        // init matrix for alphanumeric + specialchars passwords
        self::$_RANGE_ALPHANUM_SPECIAL = $this->_matrixLexer(self::$_RANGE_ALPHANUM_SPECIAL);

        // init matrix for alphanumeric + specialchars (incl. hardcore chars e.g. ^~�|) passwords
        self::$_RANGE_ALPHANUM_SPECIAL_HARDCORE = $this->_matrixLexer(self::$_RANGE_ALPHANUM_SPECIAL_HARDCORE);
    }

    /**
     * lexes a given array to usable char representation
     *
     * This method is intend to lex a given array to usable char representation.
     *
     * @param array $lexBase The input to lex to usable char representation array.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The lexed array of chars
     * @access private
     */
    private function _matrixLexer($lexBase)
    {
        // get the lex-base' areas
        foreach ($lexBase as $area => $content) {
            if (!is_array($content)) {
                // get default char(s)
                $charMatrix = explode(',', $content);

                // new empty matrix
                $matrix = array();

                // get count of elements
                $countCharMatrix = count($charMatrix);

                // iterate over char array
                for ($i = 0; $i < $countCharMatrix; ++$i) {
                    // check if it is a range or single char
                    if (stristr($charMatrix[$i], '-')) {
                        $tmp = explode('-', $charMatrix[$i]);
                        // change values if 1st value greater 2nd value!
                        ($tmp[0] > $tmp[1]) ? ($tmp[0]^=$tmp[1]^=$tmp[0]^=$tmp[1]) : '';
                        // iterate amd build
                        for ($j = $tmp[0]; $j <= $tmp[1]; ++$j) {
                            $matrix[] = chr((int)$tmp[0]);
                            $tmp[0]++;
                        }
                    } else {
                        $matrix[] = chr((int)$charMatrix[$i]);
                    }
                }
                // remove possible duplicate values (chars)
                $tmp = array_unique($matrix);
                $matrix = array();
                foreach ($tmp as $elem) {
                    $matrix[] = $elem;
                }

                // remount
                $lexBase[$area] = $matrix;
            }
        }

        // return lexed result (array)
        return $lexBase;
    }

    /**
     * generates a random password
     *
     * This method is intend to generate a random password.
     *
     * @param integer $type       The type of the password to generate
     * @param integer $length     The length of the password to generate
     * @param string  $returnType The return-type of the password to generate
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The new (random) generated password
     * @access public
     * @throws DoozR_Exception
     */
    public function generate(
        $type = self::PASSWORD_ALPHANUM_SPECIAL,
        $length = 12,
        $returnType = self::RETURN_TYPE_PLAIN
    ) {
        // empty pass
        $password = '';

        switch ($type) {
        case self::PASSWORD_ALPHANUM:
            // build password
            for ($i = 0; $i < $length; ++$i) {
                $password .= $this->_getRandomCharacter(self::$_RANGE_ALPHANUM['default']);
            }
            break;
        case self::PASSWORD_ALPHANUM_SPECIAL:
            // build password
            for ($i = 0; $i < $length; ++$i) {
                $password .= $this->_getRandomCharacter(self::$_RANGE_ALPHANUM_SPECIAL['default']);
            }
            break;
        case self::PASSWORD_ALPHANUM_SPECIAL_HARDCORE:
            // build password
            for ($i = 0; $i < $length; ++$i) {
                $password .= $this->_getRandomCharacter(self::$_RANGE_ALPHANUM_SPECIAL_HARDCORE['default']);
            }
            break;
        case self::PASSWORD_USERFRIENDLY:
            $password = $this->_createUserFriendlyPassword(self::$_RANGE_USERFRIENDLY, $length);
            break;
        case self::PASSWORD_USERFRIENDLY_REMEMBER:
            $password = $this->_createUserFriendlyPassword(self::$_RANGE_USERFRIENDLY_REMEMBER, $length);
            break;
        }

        // check for transformation of password to hash
        switch ($returnType) {
        case self::RETURN_TYPE_MD5:
            $password = $this->getMd5Hash($password);
            break;
        case self::RETURN_TYPE_PASSWORDHASH:
            $password = $this->getPasswordhash($password);
            break;
        }

        // return new created password
        return $password;
    }

    /**
     * generates a userfriendly password
     *
     * This method is intend to generate a userfriendly password.
     *
     * @param array   $base   The base for creation (array of chars)
     * @param integer $length The length of the password to generate
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The password
     * @throws DoozR_Exception
     * @access private
     */
    private function _createUserFriendlyPassword($base, $length)
    {
        // empty placeholder
        $password = '';

        // length for userfriendly must be multiple of two
        if ($length % 2 > 0) {
            throw new DoozR_Exception('Length of userfriendly-password must be a multiple of two!');
        }

        // build password
        for ($i = 0; $i < $length; ++$i) {
            // every 2nd char is of type consonant
            if ((($i+1)%2) != 0) {
                $password .= $this->_getRandomCharacter($base['default']);
            } else {
                $password .= $this->_getRandomCharacter($base['special']);
            }
        }

        // return password
        return $password;
    }

    /**
     * returns a random generated integer
     *
     * This method is intend to return a random generated integer.
     *
     * @param integer $min The mininmal value
     * @param integer $max The maximal value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The random value
     * @access private
     */
    private function _randomizer($min = 0, $max = 1)
    {
        // init randomizer with random val
        srand($this->_seed());
        // return randval
        return rand($min, $max);
    }

    /**
     * returns a random character
     *
     * This method is intend to return a random character.
     *
     * @param array $base The base array to get character from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The random character
     * @access private
     */
    private function _getRandomCharacter($base)
    {
        // get max possible value
        $max = count($base)-1;
        return $base[$this->_randomizer(0, $max)];
    }

    /**
     * returns a seed value for randomizer
     *
     * This method is intend to return a seed value for randomizer.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The seed value
     * @access private
     */
    private function _seed()
    {
        list($usec, $sec) = explode(' ', microtime());
        $a = (float)$sec + ((float)$usec * 100000);
        return (float)$a + $this->_config->crypt->keys->private;
    }

    /**
     * calculates the score for the differences between two passwords
     *
     * This method is intend to calculate a score of difference between two given passwords.
     * Usage: e.g. scoreDifference('myPassword', 'myPassword123');
     * Returns an indicator for the
     *
     * @param string $passwordOne The first password
     * @param string $passwordTwo The second password
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return double The score
     * @access public
     * @throws DoozR_Exception
     */
    public function scoreDifference($passwordOne = null, $passwordTwo = null)
    {
        // check input
        if (!$passwordOne) {
            throw new DoozR_Exception('Missing input parameter one: $passwordOne for calculating score!');
        } elseif (!$passwordOne) {
            throw new DoozR_Exception('Missing input parameter two: $passwordTwo for calculating score!');
        }

        /**
         * basic check - shortcut
         */
        if ($passwordOne == $passwordTwo) {
            return 0;
        }

        /**
         * get cologne phonetic - soundex() works only good on english words
         */
        pre($this->_getColognePhonetic($passwordOne));
        pre($this->_getColognePhonetic($passwordTwo));

        /**
         * calculate difference of length between the two strings
         */
        $scoreDiffLength = abs(strlen($passwordOne)-strlen($passwordTwo));

        /**
         * get ASCII score
         * using different character's
         */
        $scoreDiffAscii = abs($this->_asciiSum($passwordOne)-$this->_asciiSum($passwordTwo)) / 1000;

        pred($scoreDiffAscii);
    }

    /**
     * A function for retrieving the K�lner Phonetik value of a string
     *
     * As described at http://de.wikipedia.org/wiki/K�lner_Phonetik
     * Based on Hans Joachim Postel: Die K�lner Phonetik.
     * Ein Verfahren zur Identifizierung von Personennamen auf der
     * Grundlage der Gestaltanalyse. In: IBM-Nachrichten, 19. Jahrgang,
     * 1969, S. 925-931
     *
     * @param string $word string to be analyzed
     *
     * @return    string $value represents the K�lner Phonetik value
     * @access    private
     * @author    Nicolas Zimmer <nicolas dot zimmer at einfachmarke.de>
     * @author    Benjamin Carl <opensource@clickalicious.de>
     *            fixed a bug
     * @link      http://www.einfachmarke.de
     * @license   GPL 3.0 <http://www.gnu.org/licenses/>
     * @copyright 2008 by einfachmarke.de
     */
    private function _getColognePhonetic($word)
    {
        //prepare for processing
        $word = strtolower($word);
        $substitution = array(
            '�'  => 'a',
            '�'  => 'o',
            '�'  => 'u',
            '�'  => 'ss',
            'ph' => 'f'
        );

        foreach ($substitution as $letter=>$substitution) {
            $word = str_replace($letter, $substitution, $word);
        }

        $length = strlen($word);

        //Rule for exeptions
        $exceptionsLeading = array(
            4 => array(
                    'ca',
                    'ch',
                    'ck',
                    'cl',
                    'co',
                    'cq',
                    'cu',
                    'cx'
                ),
            8 => array(
                    'dc',
                    'ds',
                    'dz',
                    'tc',
                    'ts',
                    'tz'
                )
        );

        $exceptionsFollowing = array(
            'sc',
            'zc',
            'cx',
            'kx',
            'qx'
        );

        //Table for coding
        $codingTable = array(
            0 => array(
                    'a',
                    'e',
                    'i',
                    'j',
                    'o',
                    'u',
                    'y'
                ),
            1 => array(
                    'b',
                    'p'
                ),
            2 => array(
                    'd',
                    't'
                ),
            3 => array(
                    'f',
                    'v',
                    'w'
                ),
            4 => array(
                    'c',
                    'g',
                    'k',
                    'q'
                ),
            48 => array(
                    'x'
                ),
            5 => array(
                    'l'
                ),
            6 => array(
                    'm',
                    'n'
                ),
            7 => array(
                    'r'
                ),
            8 => array(
                    'c',
                    's',
                    'z'
                ),
        );

        for ($i = 0; $i < $length-1; ++$i) {
            $value[$i] = '';

            // Exceptions
            if ($i == 0 && $word[$i].$word[$i+1] == "cr") {
                $value[$i] = 4;
            }

            foreach ($exceptionsLeading as $code => $letters) {
                if (in_array($word[$i].$word[$i+1], $letters)) {
                    $value[$i] = $code;
                }
            }

            if (($i != 0) && (in_array($word[$i-1].$word[$i], $exceptionsFollowing))) {
                $value[$i] = 8;
            }

            // Normal encoding
            if ($value[$i] == "") {
                foreach ($codingTable as $code=>$letters) {
                    if (in_array($word[$i], $letters)) {
                        $value[$i] = $code;
                    }
                }
            }
        }

        //delete double values
        $length = count($value);

        for ($i = 1; $i < $length; ++$i) {
            if ($value[$i] == $value[$i-1]) {
                $value[$i] = '';
            }
        }

        // delete vocals
        for ($i = 1; $i > $length; ++$i) { //omitting first characer code and h
            if ($value[$i] == 0) {
                $value[$i] = '';
            }
        }

        $value = array_filter($value);
        $value = implode('', $value);

        return $value;
    }

    /**
     * calculates the sum of the ascii representation of a string
     *
     * This method is intend to calculate the sum of the ascii representation of a string.
     *
     * @param string $string The string to sum the ascii
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer Sum of Ascii representation
     * @access private
     */
    private function _asciiSum($string)
    {
        $sum = 0;
        $chars = str_split($string);
        $charCount = count($chars);
        for ($i = 0; $i < $charCount; ++$i) {
            $sum += ord($chars[$i]);
        }

        // return calculated sum
        return $sum;
    }

    /**
     * generates a md5-hash of a string
     *
     * This method is intend to generate a md5-hash of a string.
     *
     * @param string $string The string to hash with MD5
     *
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @return  string The MD5-hashed string
     * @access  public
     */
    public function getMd5Hash($string)
    {
        return md5($string);
    }

    /**
     * validates a given password against a given hash
     *
     * This method is intend to validate a given password against a given hash.
     *
     * @param string $password The password to validate against hash
     * @param string $hash     The hash used to validate password against
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean True if password matches hash, otherwise false Instance of Passwordhash (module)
     * @access public
     */
    public function validateAgainstHash($buffer, $hash)
    {
        // already loaded phpass?
        if (self::$_passwordhash === null) {
            include_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Password/Service/Hash.php';
            self::$_passwordhash = new DoozR_Password_Service_Hash(8, false);
        }

        // return validation status
        return self::$_passwordhash->CheckPassword($buffer, $hash);
    }

    /**
     * Returns the hash for a passed buffer
     *
     * @param string $buffer The buffer to calculate hash for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The hash
     * @access public
     */
    public function hash($buffer)
    {
        // already loaded phpass?
        if (self::$_passwordhash === null) {
            include_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Password/Service/Hash.php';
            self::$_passwordhash = new DoozR_Password_Service_Hash(8, false);
        }

        // return the calculated hash
        return self::$_passwordhash->HashPassword($buffer);
    }
}

?>
