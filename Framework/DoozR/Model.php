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
 * @subpackage DoozR_Core_Model
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Decorator/Singleton.php';

/**
 * DoozR - Model
 *
 * Model of the DoozR Framework. This Model-class provides access
 * to Database libs in Model/Lib/... - by acting as a configurable (.config)
 * proxy.
 *
 * @category   DoozR
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Model
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Model extends DoozR_Base_Decorator_Singleton
{
    /**
     * The decorator configuration
     *
     * @var array
     * @access protected
     */
    protected $decoratorConfiguration;

    /**
     * DoozR_Path instance for path management
     *
     * @var DoozR_Path
     * @access protected
     */
    protected $path;

    /**
     * DoozR_Config instance for access to configuration
     *
     * @var DoozR_Config
     * @access protected
     */
    protected $config;

    /**
     * DoozR_Logger instance for logging
     *
     * @var DoozR_Logger
     * @access protected
     */
    protected $logger;


    /**
     * This method is the constructor of the core class.
     *
     * @param array        $databaseConfiguration Configuration for the Generic Decorator Class
     * @param DoozR_Path   $path                  Instance of DoozR_Path
     * @param DoozR_Config $config                Instance of DoozR_Config
     * @param DoozR_Logger $logger                Instance of DoozR_Logger
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Model
     * @access protected
     */
    protected function __construct(
        array        $databaseConfiguration,
        DoozR_Path   $path,
        DoozR_Config $config,
        DoozR_Logger $logger
    ) {
        // what is to decorate?
        $this->setPath($path);
        $this->setConfig($config);
        $this->setLogger($logger);
        $this->setDecoratorConfiguration($databaseConfiguration);
        $this->setEnabled($this->getConfig()->database->enabled());

        // If database is enabled -> start decorating
        if ($this->getEnabled() === true) {
            $this->init($this->decoratorConfiguration, $this->path);

        }
    }

    /**
     * Setter for enabled
     *
     * @param boolean $enabled TRUE is database (model) is enabled. FALSE to set disabled.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * Setter for enabled
     *
     * @param boolean $enabled TRUE is database (model) is enabled. FALSE to set disabled.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Model Instance for chaining
     * @access protected
     */
    protected function enabled($enabled)
    {
        $this->setEnabled($enabled);
        return $this;
    }

    /**
     * Getter for enabled
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool The enabled status
     * @access protected
     */
    protected function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Setter for path.
     *
     * @param DoozR_Path $path The DoozR_Path instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setPath(DoozR_Path $path)
    {
        $this->path = $path;
    }

    /**
     * Setter for path.
     *
     * @param DoozR_Path $path The DoozR_Path instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Model Instance for chaining
     * @access protected
     */
    protected function path(DoozR_Path $path)
    {
        $this->setPath($path);
        return $this;
    }

    /**
     * Getter for path.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Path The DoozR_Path instance
     * @access protected
     */
    protected function getPath()
    {
        return $this->path;
    }

    /**
     * Setter for logger.
     *
     * @param DoozR_Logger $logger DoozR logger
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setLogger(DoozR_Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Setter for logger.
     *
     * @param DoozR_Logger $logger DoozR logger
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Model Instance for chaining
     * @access protected
     */
    protected function logger(DoozR_Logger $logger)
    {
        $this->setLogger($logger);
        return $this;
    }

    /**
     * Getter for logger.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Logger Instance of DoozR logger
     * @access protected
     */
    protected function getLogger()
    {
        return $this->logger;
    }

    /**
     * Setter for config.
     *
     * @param DoozR_Config $config DoozR config
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setConfig(DoozR_Config $config)
    {
        $this->config = $config;
    }

    /**
     * Setter for config.
     *
     * @param DoozR_Config $config DoozR config
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Model Instance for chaining
     * @access protected
     */
    protected function config(DoozR_Config $config)
    {
        $this->setConfig($config);
        return $this;
    }

    /**
     * Getter for config.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Config The instance
     * @access protected
     */
    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * Setter for decorator configuration.
     *
     * @param array $decoratorConfiguration The configuration
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setDecoratorConfiguration(array $decoratorConfiguration)
    {
        $this->decoratorConfiguration = $decoratorConfiguration;
    }

    /**
     * Setter for decorator configuration.
     *
     * @param array $decoratorConfiguration The configuration
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Model Instance for chaining
     * @access protected
     */
    protected function decoratorConfiguration(array $decoratorConfiguration)
    {
        $this->setDecoratorConfiguration($decoratorConfiguration);
        return $this;
    }

    /**
     * Getter for decorator configuration.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The configuration.
     * @access protected
     */
    protected function getDecoratorConfiguration()
    {
        return $this->config;
    }
}
