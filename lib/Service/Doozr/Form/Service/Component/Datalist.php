<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service
 *
 * Datalist.php - Extends Html Base component to build a valid select
 * component.
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
 * @subpackage Doozr_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Service/Doozr/Form/Service/Component/Select.php';
require_once DOOZR_DOCUMENT_ROOT . 'Service/Doozr/Form/Service/Component/Interface/Option.php';

/**
 * Doozr - Form - Service
 *
 * Extends Html Base component to build a valid select component.
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
class Doozr_Form_Service_Component_Datalist extends Doozr_Form_Service_Component_Select
{
    /**
     * This is the tag-name for HTML output.
     * e.g. "input" or "form" => in this case = SELECT
     *
     * @var string
     * @access protected
     */
    protected $tag = Doozr_Form_Service_Constant::HTML_TAG_DATALIST;

    /**
     * Mark this component as parent
     *
     * @var string
     * @access protected
     */
    protected $type = Doozr_Form_Service_Constant::COMPONENT_CONTAINER;

    /*------------------------------------------------------------------------------------------------------------------
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param Doozr_Form_Service_Renderer_Interface  $renderer  Renderer instance for rendering this component
     * @param Doozr_Form_Service_Validator_Interface $validator Validator instance for validating this component
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \Doozr_Form_Service_Component_Datalist
     * @access public
     */
    public function __construct(
        Doozr_Form_Service_Renderer_Interface $renderer = null,
        Doozr_Form_Service_Validator_Interface $validator = null
    ) {
        // Important call so observer storage ... can be initiated
        parent::__construct($renderer, $validator);
    }

    /**
     * Proxy to parents addOption -> cause we need to modify the input and we want to
     * do this inline.
     *
     * @param Doozr_Form_Service_Component_Interface_Option $option The component to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function addOption(Doozr_Form_Service_Component_Interface_Option $option)
    {
        // Beim addOption() von Datalist modifiy template so das <option></option> zu <option dkjdkjdkd/> wird!
        $option->setTemplate(Doozr_Form_Service_Constant::TEMPLATE_DEFAULT_NONCLOSING);

        return parent::addOption($option);
    }
}
