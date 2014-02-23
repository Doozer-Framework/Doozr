<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Form - Service
 *
 * Interface.php - The interface for all elements used in userland.
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
 */

/**
 * DoozR - Form - Service
 *
 * The interface for all elements used in userland.
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
interface DoozR_Form_Service_Element_Interface
{

    /**
     * Returns the valid state of the element.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if element state is valid, otherwise FALSE
     * @access public
     */
    public function isValid($arguments = array(), $store = array());


    #public function hasUpload();


    /**
     * Stores/adds the passed validation information.
     *
     * @param string      $validation The type of validation
     * @param null|string $value      The value for validation or NULL
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Element_Input
     * @access public
     */
    public function addValidation($validation, $value = null);

    /**
     * Getter for validation.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Validations as array
     * @access public
     */
    public function getValidation();

    /**
     * Setter for value.
     *
     * @param mixed $value The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setValue($value);

    /**
     * Getter for value.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Value of this element
     * @access public
     */
    public function getValue();
}
