<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service.
 *
 * Fieldset.php - The Fieldset component is a container component which
 * combines components to a set.
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
require_once DOOZR_DOCUMENT_ROOT.'Service/Doozr/Form/Service/Component/Formcomponent.php';

/**
 * Doozr - Form - Service.
 *
 * The Fieldset component is a container component which combines components to a set.
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
class Doozr_Form_Service_Component_Fieldset extends Doozr_Form_Service_Component_Formcomponent
{
    /**
     * This is the tag-name for HTML output.
     * e.g. "input" or "form". Default empty string "".
     *
     * @var string
     */
    protected $tag = Doozr_Form_Service_Constant::HTML_TAG_FIELDSET;

    /**
     * The id of the legend.
     *
     * @var string
     */
    protected $legendId;

    /*------------------------------------------------------------------------------------------------------------------
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param Doozr_Form_Service_Renderer_Interface $renderer Renderer instance for rendering this component
     * @param Doozr_Form_Service_Component_Legend   $legend   The legend to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return \Doozr_Form_Service_Component_Fieldset
     */
    public function __construct(
        Doozr_Form_Service_Renderer_Interface $renderer = null,
        Doozr_Form_Service_Component_Legend $legend = null
    ) {
        if ($legend !== null) {
            $this->setLegend($legend);
        }

        // Important call so observer storage ... can be initiated
        parent::__construct($renderer);
    }

    /**
     * Setter for disabled.
     *
     * @param string $disabled The disabled state as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setDisabled($disabled = 'disabled')
    {
        $this->setAttribute('disabled', $disabled);
    }

    /**
     * Getter for disabled.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The disabled state
     */
    public function getDisabled()
    {
        return $this->getAttribute('disabled');
    }

    /**
     * Setter for form.
     *
     * @param string $form The form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setForm($form)
    {
        $this->setAttribute('form', $form);
    }

    /**
     * Getter for form.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The form
     */
    public function getForm()
    {
        return $this->getAttribute('form');
    }

    /**
     * Setter for legend.
     *
     * @param Doozr_Form_Service_Component_Legend $legend
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setLegend(Doozr_Form_Service_Component_Legend $legend)
    {
        $this->legendId = $this->addChild($legend);
    }

    /**
     * Getter for legend.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The legend component
     */
    public function getLegend()
    {
        return $this->getChild($this->legendId);
    }

    /**
     * Setter for value.
     *
     * @param mixed $value The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setValue($value)
    {
        /*
         * Intentionally left blank
         */
    }
}
