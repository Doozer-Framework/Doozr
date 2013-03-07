<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Module - Form
 *
 * Image.php - Input-field class for creating input-fields e.g. of type "image"
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
 * @package    DoozR_Module
 * @subpackage DoozR_Module_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'Module/DoozR/Form/Module/Element/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT.'Module/DoozR/Form/Module/Element/Interface.php';

/**
 * DoozR - Module - Form
 *
 * Input-field class for creating input-fields e.g. of type "image"
 *
 * @category   DoozR
 * @package    DoozR_Module
 * @subpackage DoozR_Module_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class Image extends DoozR_Form_Module_Element_Abstract implements DoozR_Form_Module_Element_Interface
{
    /**
     * sets the alt-text for the input type image element
     *
     * This method is intend to set the alt-text for the input type image element.
     *
     * @param string $alt The alternate Text to display if image could not be loaded (or on hover)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setAlt($alt)
    {
        return $this->setAttribute('alt', $alt);
    }

    /**
     * sets the alt-text for the input type image element
     *
     * This method is intend to set the alt-text for the input type image element.
     *
     * @param string $alt The alternate Text to display if image could not be loaded (or on hover)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Image The current active instance
     * @access public
     */
    public function alt($alt)
    {
        $this->setAlt($alt);

        // for chaining
        return $this;
    }

    /**
     * returns the alt-text for the input type image element
     *
     * This method is intend to return the alt-text for the input type image element.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING alt-text if set, otherwise NULL
     * @access public
     */
    public function getAlt()
    {
        return $this->getAttribute('alt');
    }

    /**
     * sets the source of the image for the input type image element
     *
     * This method is intend to set the source of the image for the input type image element.
     *
     * @param string $src Specifies the source of the image to use as button
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if attribute successfully set, otherwise FALSE
     * @access public
     */
    public function setSrc($src)
    {
        return $this->setAttribute('src', $src);
    }

    /**
     * sets the source of the image for the input type image element
     *
     * This method is intend to set the source of the image for the input type image element.
     *
     * @param string $src Specifies the source of the image to use as button
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Image The current active instance
     * @access public
     */
    public function src($src)
    {
        $this->setSrc($src);

        // for chaining
        return $this;
    }

    /**
     * returns the source of the image for the input type image element
     *
     * This method is intend to return the source of the image for the input type image element.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @returnmixed STRING source of image if set, otherwise NULL
     * @access public
     */
    public function getSrc()
    {
        return $this->getAttribute('src');
    }
}

?>
