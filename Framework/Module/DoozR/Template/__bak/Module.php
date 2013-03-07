<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Module Template
 *
 * Module.php - Module for accessing DoozR's Template Engine
 * (PHPTAL (PHP Template Attribute Language))
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
 * @subpackage DoozR_Module_Template
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

//require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Template/Engine.php';
//require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Template/Engine/Interface.php';
//extends DoozR_Base_Template_Engine implements DoozR_Base_Template_Engine_Interface

/**
 * DoozR Module Template
 *
 * Module for accessing DoozR's Template Engine
 * (PHPTAL (PHP Template Attribute Language))
 *
 * @category   DoozR
 * @package    DoozR_Module
 * @subpackage DoozR_Module_Template
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 * @DoozRType  Singleton
 */
final class DoozR_Template_Module
{
    /**
     * holds the template engine we use for template processing
     *
     * @var string
     * @access const
     */
    const CONFIG_TEMPLATE_ENGINE = 'PHPTAL';

    /**
     * holds the default mode for output of templates
     *
     * @var integer
     * @access const
     */
    const CONFIG_TEMPLATE_MODE = 55;

    /**
     * Contains the extension  the extension of
     *
     * @var string
     * @access const
     */
    const CONFIG_TEMPLATE_EXTENSION = '.html';

    /**
     * Exception codes of module DoozR_Template
     * Range assigned to module [50001-51000]
     *
     * TIP! Use steps of ten when assigning errorcodes
     * So you can be sure, that you can be more specific if
     * you upgrade and/or change your existing code in future.
     */
    const TEMPLATE_ERRORCODE_EXCEPTION_FETCH    = 50001;
    const TEMPLATE_ERRORCODE_EXCEPTION_ASSIGN   = 50011;
    const TEMPLATE_ERRORCODE_EXCEPTION_DISPATCH = 50021;


    /**
     * merges file and path to template and create instance of template
     *
     * This method is intend to merge file and path to template and create instance of template
     *
     * @return  boolean TRUE if successful, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function dispatch()
    {
        // call parent (create full-qualified path and file)
        parent::dispatch();

        // create a new template object
        $this->template = new PHPTAL($this->templatePathAndFile);

        // set mode
        $this->template->setOutputMode(($this->mode) ? $this->mode : self::CONFIG_TEMPLATE_MODE);
    }

    /**
     * assigns variables to the current template
     *
     * This method is intend to assign variables to the current template
     *
     * @param mixed $variable The variable-name to assign
     * @param mixed $value    The variable-value to assign
     *
     * @return  boolean TRUE if successful, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @throws  DoozR_Template_Module_Exception
     */
    public function assignVariable($variable = null, $value = null)
    {
        // basic contractcondition check(s)
        if (is_null($variable)) {
            return false;
        }

        if (is_null($this->template)) {
            throw new DoozR_Template_Module_Exception(
                'Error assigning variable to template object ("'.$this->templatePathAndFile.'") no instance found!',
                DoozR_Template_Module::TEMPLATE_ERRORCODE_EXCEPTION_ASSIGN
            );
        }

        // try to assign variable and its value
        try {
            $this->template->{$variable} = $value;

        } catch (Exception $e) {
            throw new DoozR_Template_Module_Exception(
                'Error assigning variable to template ("'.$this->templatePathAndFile.'") template engine failed!',
                DoozR_Template_Module::TEMPLATE_ERRORCODE_EXCEPTION_ASSIGN,
                $e
            );
        }

        // success
        return true;
    }

    /**
     * returns the result of the template-processing
     *
     * This method is intend to return the result of the template-processing
     *
     * @return string Result of the processed template
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @throws DoozR_Template_Module_Exception
     */
    public function fetch()
    {
        try {
            // execute the template
            return $this->template->execute();

        } catch (Exception $e) {

            pred($e);

            throw new DoozR_Template_Module_Exception(
                'Error fetching result of processed template ("'.$this->templatePathAndFile.'")',
                DoozR_Template_Module::TEMPLATE_ERRORCODE_EXCEPTION_FETCH,
                $e
            );
        }
    }
}

?>
