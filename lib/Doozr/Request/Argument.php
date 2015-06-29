<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Request - Argument
 *
 * Argument.php - The Argument part of Doozr_Request_Arguments (Parameter => Argument).
 *
 * PHP versions 5.5
 *
 * LICENSE: Doozr - PHP Framework - Copyright (c) 2009, Benjamin Carl -
 * All rights reserved. Redistribution and use in source and binary forms, with
 * or without modification, are permitted provided that the following conditions
 * are met: Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.* Redistributions in binary
 * form must reproduce the above copyright notice, this list of conditions and the
 * following disclaimer in the documentation and/or other materials provided with
 * the distribution. * All advertising materials mentioning features or use of
 * this software must display the following acknowledgement: This product includes
 * software developed by Benjamin Carl and its contributors.
 *
 * Neither the name of Benjamin Carl nor the names of its contributors may be used
 * to endorse or promote products derived from this software without specific prior
 * written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * Please feel free to contact us via e-mail: opensource@clickalicious.de
 *
 * @category   Doozr
 * @package    Doozr_Request
 * @subpackage Doozr_Request_Argument
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

/**
 * Doozr - Request - Argument
 *
 * The Argument part of Doozr_Request_Arguments (Parameter => Argument).
 *
 * @category   Doozr
 * @package    Doozr_Request
 * @subpackage Doozr_Request_Argument
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Request_Argument
{
    /**
     * The sanitized/cleaned value
     *
     * @var mixed
     * @access protected
     */
    protected $value;

    /**
     * The raw (original + unmodified) value
     *
     * @var mixed
     * @access protected
     */
    protected $rawValue;

    /**
     * The impact of this parameter-value
     *
     * @var int
     * @access protected
     */
    protected $impact = 0;


    /**
     * Constrcutor.
     *
     * @param mixed $value The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \Doozr_Request_Argument An instance of this class
     * @access public
     */
    public function __construct($value)
    {
        // (pre)set value of this request-value
        $this->value = $this->clean($value, true, false);

        // set value of this request-value
        $this->rawValue = $value;
    }

    /**
     * Cleans the input variable. This method removes tags with strip_tags and afterwards
     * it turns all non-safe characters to its htmlentities.
     *
     * @param mixed   $mixed        The input to clean
     * @param bool $stripTags    TRUE to strip tags from mixed, otherwise FALSE to do not
     * @param bool $htmlEntities TRUE to convert non-url safe characters to URL-encoded values, FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The cleaned input
     * @access public
     */
    public function clean($mixed, $stripTags = true, $htmlEntities = true)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = self::clean($value, $stripTags, $htmlEntities);
            }

        } else {
            if ($stripTags === true) {
                $mixed = strip_tags($mixed);
            }

            if ($htmlEntities === true) {
                $mixed = htmlentities($mixed);
            }
        }

        return $mixed;
    }

    /**
     * sets the (sanitized) value
     *
     * This method is intend to set the (sanitized) value
     *
     * @param mixed $value The value to set
     *
     * @return bool TRUE if successful set, otherwise FALSE
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function set($value)
    {
        return (
            ($this->value = $this->clean($value, true, false)) &&
            ($this->rawValue = $value)
        );
    }

    /**
     * returns the (sanitized) value
     *
     * returns the (sanitized) value
     *
     * @return mixed The value
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function get()
    {
        return array(
            'sanitized' => $this->value,
            'raw'       => $this->rawValue
        );
    }

    /**
     * sets the (raw) value
     *
     * This method is intend to set the (raw) value
     *
     * @param mixed $value The value to set
     *
     * @return bool TRUE if successful set, otherwise FALSE
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setRaw($value)
    {
        return ($this->rawValue = $value);
    }

    /**
     * returns the raw (original) value
     *
     * returns the raw (original) value
     *
     * @return mixed The raw/original value
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function getRaw()
    {
        return $this->rawValue;
    }

    /**
     * sets the (sanitized) value
     *
     * sets the (sanitized) value
     *
     * @param mixed $value The sanitized/cleaned value
     *
     * @return void
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setSanitized($value)
    {
        $this->value = $value;
    }

    /**
     * returns the (sanitized) value
     *
     * returns the (sanitized) value
     *
     * @return mixed The value
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function getSanitized()
    {
        return $this->value;
    }

    /**
     * sets the impact of this value
     *
     * sets the impact of this value
     *
     * @param int $impact The impact of this Request-Value
     *
     * @return void
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setImpact($impact = 0)
    {
        $this->impact = $impact;
    }

    /**
     * returns the impact of this value
     *
     * returns the impact of this value
     *
     * @return integer The impact of this Request-Value
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function getImpact()
    {
        return $this->impact;
    }

    /**
     * Magic for accessing the value simply by strval($object)
     *
     * @return null|string The raw value set if set, otherwise NULL
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __toString()
    {
        return $this->rawValue;
    }
}
