<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service
 *
 * Abstract.php Class Doozr_Form_Service_Component_Html_Abstract is
 * a simple basic HTML-Component in an abstract form.
 *
 * PHP versions 5.4
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
 * @subpackage Doozr_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

/**
 * Doozr - Form - Service
 *
 * Class Doozr_Form_Service_Component_Html_Abstract is
 * a simple basic HTML-Component in an abstract form.
 *
 * @category   Doozr
 * @package    Doozr_Service
 * @subpackage Doozr_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 * @abstract
 */
abstract class Doozr_Form_Service_Component_Html_Abstract
{
    /**
     * Tag: The name(identifier) of the HTML-Component
     * e.g. BODY, HR, H1, SPAN ...
     *
     * @var string
     * @access protected
     */
    protected $tag;

    /**
     * Is this a self-closing tag?
     *
     * @var bool
     * @access protected
     */
    protected $selfClosing = false;

    /**
     * The HTML-Version this component is for/from
     *
     * @var int
     * @access protected
     */
    protected $htmlVersion = Doozr_Form_Service_Constant::HTML_VERSION_5;

    /**
     * The HTML-Version this component is for/from
     *
     * @var int
     * @access protected
     */
    protected $attributes = array();

    /**
     * Setter for attributes[]
     *
     * @param string      $key   The name of the attribute
     * @param null|string $value The value of the attribute
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setAttribute($key, $value = null)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Getter for attributes[]
     *
     * @param $key The name of the attribute
     *
     * @return null|mixed The attributes value if set, otherwise NULL
     * @access public
     */
    public function getAttribute($key)
    {
        return (isset($this->attributes[$key]) ? $this->attributes[$key] : null);
    }

    /**
     * Setter for an array of attributes[]
     *
     * @param array $attributes The attributes to set as an array
     *
     * @return void
     * @access public
     */
    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $attribute => $value) {
            $this->setAttribute($attribute, $value);
        }
    }

    /**
     * Getter for attributes[]
     *
     * @return array|int
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
