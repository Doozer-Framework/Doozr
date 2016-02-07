<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service.
 *
 * Input.php - Contract for all HTML/DOM components. This contract garantues
 * that a class using this interface is renderable through render() can take
 * child components (also if it doesn't make sense for some components!) and
 * that a call on render() will return the HTML for the whole component.
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
 * Doozr - Form - Service.
 *
 * Contract for all HTML/DOM components. This contract garantues
 * that a class using this interface is renderable through render() can take
 * child components (also if it doesn't make sense for some components!) and
 * that a call on render() will return the HTML for the whole component.
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
interface Doozr_Form_Service_Component_Interface_Input
{
    /**
     * Sets the HTML input element property "autocapitalize".
     *
     * @param bool $state The state to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setAutocapitalize($state);

    /**
     * Returns the autocapitalize state.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if autocapitalize is on, otherwise FALSE
     */
    public function getAutocapitalize();

    /**
     * Setter for type of input.
     *
     * @param string $type The type to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setType($type);

    /**
     * Getter for type.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string|null The type if set, otherwise NULL
     */
    public function getType();

    /**
     * Sets the name of the list the input element is bound to.
     *
     * @param string $listname The name of the list the input refers to
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setList($listname);

    /**
     * Returns the list the component is bound to.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|string The name of the list this component is bound to, NULL if not bound
     */
    public function getList();

    #accept
    #accesskey
    #mozactionhint
    #autocomplete
    #autofocus
    #autosave
    #checked
    #disabled
    #form
    #formaction
    #formenctype
    #formmethod
    #formnovalidate
    #formtarget
    #height
    #inputmpode
    #max
    #maxlength
    #min
    #multiple
    #name
    #pattern
    #placeholder
    #readonly
    #required
    #selectionDirection
    #size
    #spellcheck
    #src
    #step
    #tabindex
    #usemap
    #value
    #width
    #x-moz-errormessage
}
