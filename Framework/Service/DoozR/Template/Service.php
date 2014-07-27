<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Template - Service
 *
 * Service.php - Service: Gate for accessing any kind of template library.
 * This module is build upon the deep core integration of
 * DoozR_Base_Template_Engine.
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
 * @subpackage DoozR_Service_Template
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Facade/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Exception.php';

/**
 * DoozR - Template - Service
 *
 * Service: Gate for accessing any kind of template library.
 * This module is build upon the deep core integration of
 * DoozR_Base_Template_Engine.
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Template
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @service    Singleton
 * @inject     DoozR_Registry:DoozR_Registry identifier:getInstance type:constructor position:1
 */
class DoozR_Template_Service extends DoozR_Base_Facade_Singleton
{
    /**
     * The registry for local access
     *
     * @var DoozR_Registry
     * @access private
     */
    private $_registry;

    /**
     * The resource to process
     *
     * @var string
     * @access private
     */
    private $_resource;

    /**
     * The name of the template engine
     *
     * @var string
     * @access private
     */
    private $_library;

    /**
     * The name of the template engine
     *
     * @var string
     * @access private
     */
    private $path;


    /**
     * Fetches and optional returns the processed template
     * for further processing
     *
     * This method is intend to fetch the processed (parsed)
     * template code from current instance and returns it -
     * if parameter return is set to TRUE.
     *
     * @param boolean $return TRUE to return result, FALSE (default) to echo
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Data from template if $return was set to TRUE, otherwise NULL
     * @access public
     * @throws DoozR_Exception
     */
    public function fetch($return = false)
    {
        switch ($this->_library) {
        case 'phptal':
            // execute the template
            try {
                $buffer = $this->execute();
                if ($return === true) {
                    return $buffer;
                } else {
                    echo $buffer;
                }
            } catch (Exception $e) {
                // repack
                throw new DoozR_Exception($e);
            }
            break;
        }

        return null;
    }

    /**
     * Sets the template input (resource)
     *
     * This method is intend to set the template input also
     * known as resource. It can be whatever resource your
     * choosen lib (engine like PHPTAL, Smarty) supports.
     *
     * @param mixed $resource The resource used as input for template engine library
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setTemplate($resource)
    {
        $this->_resource = $resource;
    }

    /**
     * Constructor of the class
     *
     * This method is the constructor of this class.
     *
     * @param DoozR_Registry &$registry The instance of DoozR_Registry
     * @param string         $resource  The resource to load
     * @param array          $config    The resource to set as input (optional)
     *                                  defaults come from config
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __construct(DoozR_Registry &$registry, $resource = null, array $config = null)
    {
        // store registry
        $this->_registry = $registry;

        // store resource
        $this->_resource = $resource;

        // detect and store settings
        if ($config) {
            $this->path    = $config['path'];
            $this->_library = $config['library'];
        } else {
            $this->path    = $this->_registry->config->base->template->path;
            $this->_library = $this->_registry->config->base->template->engine->library;
        }

        // get egine running
        $this->_initEngine($this->_library);
    }

    /**
     * Initializes the engine (e.g. smarty or phptal) and store the instance
     * as decorated object.
     *
     * @param string $engine The name/identifier of the engine we use (phptal)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     * @throws DoozR_Template_Service_Exception
     */
    private function _initEngine($engine)
    {
        switch ($engine) {
        case 'phptal':
            //
            include_once $this->getPathToClass().'Service/Lib/PHPTAL/PHPTAL.php';
            $this->setDecoratedObject(new PHPTAL($this->_resource));
            break;
        default:
        throw new DoozR_Template_Service_Exception(
            'Configured engine "'.$this->_library.'" is currently not supported!'
        );
        }
    }

    /**
     * generic TEMPLATE API
     */

    /**
     * Assigns a variable to the template instance
     *
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
        switch ($this->_library) {
        case 'phptal':
            return ($this->{$variable} = $value);
        break;
        default:
        return false;
        }
    }

    /**
     * method to assign more than one variable at once
     *
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
