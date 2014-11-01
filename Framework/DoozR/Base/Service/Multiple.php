<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Base - Service - Multiple
 *
 * Multiple.php - Base-Service for building multi-instance services
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
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Service
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/State/Container.php';

/**
 * DoozR - Base - Service - Multiple
 *
 * Base-Service for building multi-instance services
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Service
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Base_Service_Multiple extends DoozR_Base_State_Container
{
    /**
     * Autoloader auto install control flag.
     * If set to TRUE in inheriting class the autoloader will be installed automatically.
     *
     * @var bool
     * @access protected
     */
    protected $autoloader = false;

    /**
     * The name of this service
     *
     * @var string
     * @access protected
     */
    protected $name;

    protected $uuid;

    /**
     * The type of this service.
     *
     * @var string
     * @access protected
     */
    protected static $type = self::TYPE_MULTIPLE;

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
     * Constructor.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Base_Service_Multiple
     * @access public
     */
    public function __construct()
    {
        // Filter out registry and store accessible through static $registry!
        $arguments = func_get_args();
        $this->setRegistry($arguments[0]);
        $arguments = array_slice($arguments, 1);

        // Check for automagically install autoloader
        if ($this->autoloader === true) {
            $this->initAutoloader($this->getName());
        }

        // dispatch remaining stuff
        if ($this->hasMethod('__tearup')) {
            if ((func_num_args() - 1) > 0) {
                call_user_func_array(
                    array(
                        $this,
                        '__tearup'
                    ),
                    $arguments
                );
            } else {
                call_user_func(
                    array($this, '__tearup')
                );
            }
        }
    }

    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    public function getUuid()
    {
        return $this->uuid;
    }


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
     * Initialize autoloader for this service.
     *
     * Each service get its own autoloader attached to SPL autoloaders.
     *
     * @param string $service The name of the service to init autoloader for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function initAutoloader($service)
    {
        // Register services custom autoloader
        $autoloaderService = new DoozR_Loader_Autoloader_Spl_Config();
        $autoloaderService
            ->setNamespace('DoozR_' . $service)
            ->setNamespaceSeparator('_')
            ->addExtension('php')
            ->setPath(DOOZR_DOCUMENT_ROOT . 'Service')
            ->setDescription('DoozR\'s ' . $service . ' service autoloader. Timestamp: ' . time());

        // Add to SPL through facade
        $this->autoloader = DoozR_Loader_Autoloader_Spl_Facade::attach(
            $autoloaderService
        );
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

    /**
     * Destructor.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __destruct()
    {
        if ($this->hasMethod('__teardown')) {
            $this->__teardown();
        }
    }
}
