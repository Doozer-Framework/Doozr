<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Template - Service
 *
 * Service.php - Service: Gate for accessing any kind of template library.
 * This module is build upon the deep core integration of
 * Doozr_Base_Template_Engine.
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
 * @subpackage Doozr_Service_Template
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Facade/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Exception.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Service/Interface.php';

use Doozr\Loader\Serviceloader\Annotation\Inject;

/**
 * Doozr - Template - Service
 *
 * Service: Gate for accessing any kind of template library.
 * This module is build upon the deep core integration of
 * Doozr_Base_Template_Engine.
 *
 * @category   Doozr
 * @package    Doozr_Service
 * @subpackage Doozr_Service_Template
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 * @Inject(
 *     class="Doozr_Registry",
 *     identifier="__construct",
 *     type="constructor",
 *     position=1
 * )
 */
class Doozr_Template_Service extends Doozr_Base_Facade_Singleton
    implements
    Doozr_Base_Service_Interface
{
    /**
     * The resource to process
     *
     * @var string
     * @access protected
     */
    protected $resource;

    /**
     * The name of the template engine
     *
     * @var string
     * @access protected
     */
    protected $library;

    /**
     * The name of the template engine
     *
     * @var string
     * @access protected
     */
    protected $path;


    /**
     * Fetches and optional returns the processed template
     * for further processing
     *
     * This method is intend to fetch the processed (parsed)
     * template code from current instance and returns it -
     * if parameter return is set to TRUE.
     *
     * @param bool $return TRUE to return result, FALSE (default) to echo
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Data from template if $return was set to TRUE, otherwise NULL
     * @access public
     * @throws Doozr_Exception
     */
    public function fetch($return = false)
    {
        switch ($this->library) {
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
                throw new Doozr_Exception($e);
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
        $this->resource = $resource;
    }

    /**
     * Constructor of the class
     *
     * This method is the constructor of this class.
     *
     * @param Doozr_Registry &$registry The instance of Doozr_Registry
     * @param string $resource The resource to load
     * @param array $config The resource to set as input (optional)
     *                                  defaults come from config
     *
     * @throws Doozr_Template_Service_Exception
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \Doozr_Template_Service
     * @access public
     */
    public function __construct(Doozr_Registry &$registry, $resource = null, array $config = null)
    {
        // Detect and store settings
        if ($config) {
            $path    = $config['path'];
            $library = $config['library'];
        } else {
            $path    = $registry->config->kernel->view->template->path;
            $library = $registry->config->kernel->view->template->engine->library;
        }

        // Store registry instance
        self::setRegistry($registry);

        // Init
        $this
            ->resource($resource)
            ->path($path)
            ->library($library)
            ->initEngine($library, $path);
    }

    /**
     * Setter for path.
     *
     * @param string $path The path to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Setter for path.
     *
     * @param string $path The path to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function path($path)
    {
        $this->setPath($path);
        return $this;
    }

    /**
     * Getter for path.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The path if set, otherwise NULL
     * @access protected
     */
    protected function getPath()
    {
        return $this->path;
    }

    /**
     * Setter for library.
     *
     * @param string $library The library to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setLibrary($library)
    {
        $this->library = $library;
    }

    /**
     * Setter for library.
     *
     * @param string $library The library to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function library($library)
    {
        $this->setLibrary($library);
        return $this;
    }

    /**
     * Getter for library.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The library if set, otherwise NULL
     * @access protected
     */
    protected function getLibrary()
    {
        return $this->library;
    }

    /**
     * Setter for resource.
     *
     * @param string $resource The resource to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setResource($resource)
    {
        $this->resource = $resource;
    }

    /**
     * Setter for resource.
     *
     * @param string $resource The resource to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function resource($resource)
    {
        $this->setResource($resource);
        return $this;
    }


    /**
     * Getter for resource.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|string The resource if set, otherwise NULL
     * @access protected
     */
    protected function getResource()
    {
        return $this->resource;
    }

    /**
     * Initializes the engine (e.g. PHPTAL) and store the instance as decorated object.
     *
     * @param string $engine The name/identifier of the engine we use (phptal)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     * @throws Doozr_Template_Service_Exception
     */
    protected function initEngine($engine)
    {
        switch ($engine) {
        case 'phptal':
            // We do not need to include PHPTAL cause Composer's job ;)
            $this->setDecoratedObject(new PHPTAL($this->resource));
            break;
        default:
        throw new Doozr_Template_Service_Exception(
            'Configured engine "'.$this->library.'" is currently not supported!'
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
     * @throws Doozr_Exception
     */
    public function assignVariable($variable = null, $value = null)
    {
        switch ($this->library) {
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

    /**
     * The name of this service
     *
     * @var string
     * @access protected
     */
    protected $name;

    /**
     * The type of this service.
     *
     * @var string
     * @access protected
     */
    protected static $type = self::TYPE_SINGLETON;

    /**
     * The type for singleton services.
     *
     * @var string
     * @const
     */
    const TYPE_SINGLETON = 'singleton';

    /**
     * The type for multi instance services.
     *
     * @var string
     * @const
     */
    const TYPE_MULTIPLE = 'multiple';

    /**
     * Returns true if service is singleton.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if service is singleton, otherwise FALSE.
     * @access public
     */
    public function isSingleton()
    {
        return (self::$type === self::TYPE_SINGLETON);
    }

    /**
     * Returns true if service is a multi instance service.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if service is multi instance, otherwise FALSE.
     * @access public
     */
    public function isMultiple()
    {
        return (self::$type === self::TYPE_MULTIPLE);
    }

    /**
     * Returns the name of the service
     *
     * This method is intend to return the name of the current
     * active service.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The name of the service
     * @access public
     */
    public function getName()
    {
        if ($this->name === null) {
            $class = get_called_class();
            if (preg_match('/_+(.+)_+/', $class, $matches) > 0) {
                $this->name = $matches[1];
            } else {
                $this->name = '';
            }
        }

        return $this->name;
    }
    protected $uuid;

    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

}
