<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Form - Service
 *
 * Container.php - This class extends the HTML base class to provide
 * the tree management functionality. This means that childs can be
 * added to this class.
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

require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Form/Service/Element/Html/Base.php';

/**
 * DoozR - Form - Service
 *
 * This class extends the HTML base class to provide
 * the tree management functionality. This means that childs can be
 * added to this class.
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
class DoozR_Form_Service_Element_Html_Container extends DoozR_Form_Service_Element_Html_Base
    implements Iterator
{
    /**
     * The template.
     *
     * @var string
     * @access protected
     */
    protected $template = '<{{TAG}}{{ATTRIBUTES}}>{{CHILDS}}</{{TAG}}>';

    /**
     * The child elements collection
     *
     * @var array
     * @access protected
     */
    protected $childs = array();

    /**
     * The parent of this Container
     *
     * @var mixed
     * @access protected
     */
    protected $parent;


    /*-----------------------------------------------------------------------------------------------------------------*
    | Control the child/parent elements
    *-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Custom renderer for all elements which can have
     * child elements (container).
     *
     * @param boolean $forceRender TRUE to force a rerendering if cached,
     *                             otherwise FALSE to do not
     *
     * @return string The rendered HTML code
     */
    public function render($forceRender = false)
    {
        if ($this->html === null || $forceRender === true) {

            // assume empty string as result
            $html = '';

            // get default properties rendered if exists ...
            $rendered = parent::render($forceRender);

            // here is the difference to base::render(...)
            foreach ($this->childs as $child) {
                $html .= $child->render().DoozR_Form_Service_Constant::NEW_LINE;
            }

            // the variable for our template is the rendered HTML from step before
            $variables = array(
                'childs' => $html
            );

            // store to prevent unneeded load
            $this->html = $this->_tpl($rendered, $variables);
        }

        return $this->html;
    }

    /**
     * Adds a child to the collection of childs
     *
     * @param DoozR_Form_Service_Element_Interface $child
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The id of the child as reference
     * @access public
     */
    public function add(DoozR_Form_Service_Element_Interface $child)
    {
        $id = count($this->childs);
        $this->childs[$id] = $child;
        return $id;
    }

    /**
     * Removes a child from collection.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function remove($id)
    {
        unset($this->childs[$id]);
    }

    /**
     * Returns all childs.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The childs
     * @access public
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * Returns a child by passed Id.
     *
     * @param string $id The id to return child for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The child
     * @access public
     */
    public function getChild($id)
    {
        return (isset($this->childs[$id]) ? $this->childs[$id] : null);
    }

    /*-----------------------------------------------------------------------------------------------------------------*
     | Iterator Pattern Implementation
     *----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the current element where
     * pointer points to.
     *
     * @return mixed|null
     * @access public
     */
    public function current()
    {
        /*
        pre($this->pointer);
        pre($this->index);
        pre($this->index[$this->pointer]);
        pre($this->childs);
        */
        return $this->childs[$this->index[$this->pointer]];
    }

    /**
     * Steps to next element
     *
     * @return void
     * @access public
     */
    public function next()
    {
        $this->pointer++;
    }

    /**
     * Returns the current positions pointer
     *
     * @return integer The current position
     * @access public
     */
    public function key()
    {
        return $this->pointer;
    }

    /**
     * Returns the validity of the current pointer
     * position as boolean (TRUE|FALSE)
     *
     * @return boolean TRUE of pointer is valid, otherwise FALSE
     * @access public
     */
    public function valid()
    {
        return $this->pointer < count($this->childs);
    }

    /**
     * Rewinds the pointer to position 0 (1st)
     *
     * @return void
     * @access public
     */
    public function rewind()
    {
        $this->pointer = 0;
    }
}
