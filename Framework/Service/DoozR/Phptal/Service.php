<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Phptal - Service
 *
 * Service.php - Service for interfacing PHPTAL (PHP Template Attribute Language)
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2014, Benjamin Carl - All rights reserved.
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
 * @subpackage DoozR_Service_Phptal
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Phptal/Service/Lib/PHPTAL.php';

/**
 * DoozR - Phptal - Service
 *
 * Service for interfacing PHPTAL (PHP Template Attribute Language)
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Phptal
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @service    Multiple
 * @inject     DoozR_Registry:DoozR_Registry identifier:__construct type:constructor position:1
 */
class DoozR_Phptal_Service extends PHPTAL
{
    /**
     * The DoozR registry for storing elements
     *
     * @var DoozR_Registry
     * @access public
     */
    public $registry;

    /**
     * This method just intercepts while instanciation for
     * filtering out the registry as argument to PHPTAL.
     * PHPTAL takes only one argument -> the filename of the
     * template.
     *
     * @param DoozR_Registry $registry The registry instance to intercept
     * @param string         $path     The path+file to dispatch to PHPTAL
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function __construct(DoozR_Registry $registry, $path = null)
    {
        $this->registry = $registry;

        parent::__construct($path);
    }

    /**
     * This method is intend to assign a variable to the template instance.
     *
     * @param mixed $variable The variable-name to assign
     * @param mixed $value    The variable-value to assign
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successful, otherwise FALSE
     * @access public
     * @throws DoozR_Exception
     */
    public function assignVariable($variable = null, $value = null)
    {
        return ($this->{$variable} = $value);
    }

    /**
     * This method is intend to assign more than one variable at once
     *
     * @param array $variables The variables to assign as array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successful, otherwise FALSE
     * @access public
     */
    public function assignVariables(array $variables)
    {
        // assume successful result
        $result = count($variables) ? true : false;

        // iterate and assign
        foreach ($variables as $variable => $value) {
            $result = ($result && $this->assignVariable($variable, $value));
        }

        // return result of assigning
        return $result;
    }
}
