<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Model
 *
 * Model.php - Model of the DoozR Framework. This Model-class provides access
 * to Database libs in Model/Lib/... - by acting as a configurable (.config)
 * proxy.
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
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Model
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Decorator/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Exception.php';

/**
 * DoozR - Model
 *
 * Model.php - Model of the DoozR Framework. This Model-class provides access
 * to Database libs in Model/Lib/... - by acting as a configurable (.config)
 * proxy.
 *
 * @category   DoozR
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Model
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Model extends DoozR_Base_Decorator_Singleton
{
    /**
     * contains an instance of DoozR_Path
     *
     * @var object
     * @access private
     */
    private $_path;

    /**
     * contains an instance of DoozR_Config
     *
     * @var object
     * @access private
     */
    private $_config;

    /**
     * contains an instance of DoozR_Logger
     *
     * @var object
     * @access private
     */
    private $_logger;


    /**
     * This method is the constructor of the core class.
     *
     * @param array  $decoratorConfiguration Configuration for the Generic Decorator Class
     * @param object $path                   Instance of DoozR_Path
     * @param object $config                 Instance of DoozR_Config
     * @param object $logger                 Instance of DoozR_Logger
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object Instance of this class
     * @access protected
     */
    protected function __construct(
        array $decoratorConfiguration,
        DoozR_Path $path,
        DoozR_Config $config,
        DoozR_Logger $logger
    ) {
        // what is to decorate?
        $this->decoratorConfiguration = $decoratorConfiguration;

        // store instances
        $this->_path   = $path;
        $this->_config = $config;
        $this->_logger = $logger;

        // is enabled?
        $this->enabled = $this->_config->database->enabled();

        // if database is enabled -> start decorating
        if ($this->enabled) {
            $this->init($this->decoratorConfiguration, $this->_path);
        }
    }

    /**
     * This method is intend to setup and call generic singleton-getter and return an instance
     * of the requested class.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object instance/object of this class
     * @access public
     */
    public function __destruct()
    {
        // close open database
        $this->close();

        // disconnect from server
        $this->disconnect();
    }
}

?>
