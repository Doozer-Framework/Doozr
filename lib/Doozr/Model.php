<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Model
 *
 * Model.php - Model of Doozr - This Model-class provides access to Database libs in Model/Lib/...
 * by acting as a configurable (.config.json) proxy.
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
 * @package    Doozr_Kernel
 * @subpackage Doozr_Kernel_Model
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Decorator/Singleton.php';

/**
 * Doozr - Model
 *
 * Model of Doozr - This Model-class provides access to Database libs in Model/Lib/...
 * by acting as a configurable (.config.json) proxy.
 *
 * @category   Doozr
 * @package    Doozr_Kernel
 * @subpackage Doozr_Kernel_Model
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Model extends Doozr_Base_Decorator_Singleton
{
    /**
     * The decorator configuration
     *
     * @var array
     * @access protected
     */
    protected $decoratorConfiguration;

    /**
     * Doozr_Path instance for path management
     *
     * @var Doozr_Path
     * @access protected
     */
    protected $path;

    /**
     * Doozr_Configuration instance for access to configuration
     *
     * @var Doozr_Configuration
     * @access protected
     */
    protected $config;

    /**
     * Doozr_Logging instance for logging
     *
     * @var Doozr_Logging
     * @access protected
     */
    protected $logger;


    /**
     * Constructor.
     *
     * @param array               $databaseConfiguration Configuration for the Generic Decorator Class
     * @param Doozr_Path          $path                  Instance of Doozr_Path
     * @param Doozr_Configuration $config                Instance of Doozr_Configuration
     * @param Doozr_Logging       $logger                Instance of Doozr_Logging
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Model
     * @access protected
     */
    protected function __construct(
        array        $databaseConfiguration,
        Doozr_Path   $path,
        Doozr_Configuration $config,
        Doozr_Logging $logger
    ) {
        // what is to decorate?
        $this->setPath($path);
        $this->setConfig($config);
        $this->setLogger($logger);
        $this->setDecoratorConfiguration($databaseConfiguration);
        $this->setEnabled($this->getConfig()->kernel->model->enabled);

        // If database is enabled -> start decorating
        if ($this->getEnabled() === true) {
            $this->init($this->decoratorConfiguration, $this->path);

        }
    }

    /**
     * Setter for enabled
     *
     * @param bool $enabled TRUE is database (model) is enabled. FALSE to set disabled.
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
     * @param bool $enabled TRUE is database (model) is enabled. FALSE to set disabled.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Model Instance for chaining
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
     * @param Doozr_Path $path The Doozr_Path instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setPath(Doozr_Path $path)
    {
        $this->path = $path;
    }

    /**
     * Setter for path.
     *
     * @param Doozr_Path $path The Doozr_Path instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Model Instance for chaining
     * @access protected
     */
    protected function path(Doozr_Path $path)
    {
        $this->setPath($path);
        return $this;
    }

    /**
     * Getter for path.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Path The Doozr_Path instance
     * @access protected
     */
    protected function getPath()
    {
        return $this->path;
    }

    /**
     * Setter for logger.
     *
     * @param Doozr_Logging $logger Doozr logger
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setLogger(Doozr_Logging $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Setter for logger.
     *
     * @param Doozr_Logging $logger Doozr logger
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Model Instance for chaining
     * @access protected
     */
    protected function logger(Doozr_Logging $logger)
    {
        $this->setLogger($logger);
        return $this;
    }

    /**
     * Getter for logger.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Logging Instance of Doozr logger
     * @access protected
     */
    protected function getLogger()
    {
        return $this->logger;
    }

    /**
     * Setter for config.
     *
     * @param Doozr_Configuration $config Doozr config
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setConfig(Doozr_Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * Setter for config.
     *
     * @param Doozr_Configuration $config Doozr config
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Model Instance for chaining
     * @access protected
     */
    protected function config(Doozr_Configuration $config)
    {
        $this->setConfig($config);
        return $this;
    }

    /**
     * Getter for config.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Configuration The instance
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
     * @return Doozr_Model Instance for chaining
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
