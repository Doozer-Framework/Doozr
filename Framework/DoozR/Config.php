<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Config
 *
 * Config.php - Config bootstrap of the DoozR Framework
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
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Config
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Facade/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Config/Interface.php';

/**
 * DoozR - Config
 *
 * Config bootstrap of the DoozR Framework
 *
 * @category   DoozR
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Config
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @implements DoozR_Path,DoozR_Logger
 */
class DoozR_Config extends DoozR_Base_Facade_Singleton implements DoozR_Config_Interface
{
    /**
     * Contains an instance of DoozR_Path
     *
     * @var object
     * @access private
     */
    private $_path;

    /**
     * Contains an instance of DoozR_Logger
     *
     * @var object
     * @access private
     */
    private $_logger;

    /**
     * Default container of configuration
     * Our preferred type is JSON
     *
     * @var string
     * @access const
     */
    const DEFAULT_CONTAINER = 'Json';


    /**
     * This method act as constructor.
     *
     * @param DoozR_Path_Interface   $path          An instance of DoozR_Path
     * @param DoozR_Logger_Interface $logger        An instance of DoozR_Logger
     * @param string                 $container     A container e.g. Json or Ini
     * @param boolean                $enableCaching TRUE to enable internal caching, FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function __construct(
        DoozR_Path_Interface $path,
        DoozR_Logger_Interface $logger,
        $container = self::DEFAULT_CONTAINER,
        $enableCaching = false
    ) {
        // store
        $this->_path   = $path;
        $this->_logger = $logger;

        // create instance through factory and set as object to decorate!
        $this->setDecoratedObject(
            $this->_factory(
                __CLASS__.'_Container_'.ucfirst(strtolower($container)),
                $this->_path->get('framework'),
                array(
                    $this->_path,
                    $this->_logger,
                    $enableCaching
                )
            )
        );
    }

    /**
     * This method is intend to act as factory for creating an instance of a config container.
     *
     * @param string $class     The classname of container
     * @param string $path      The base path to Framework
     * @param mixed  $arguments Arguments to pass to instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object The fresh created instance
     * @access private
     */
    private function _factory($class, $path, $arguments = null)
    {
        // get required file
        include_once $path.str_replace('_', DIRECTORY_SEPARATOR, $class).'.php';

        // create and return a fresh instance
        if ($arguments) {
            return $this->instanciate($class, $arguments);
        } else {
            return new $class();
        }
    }
}
