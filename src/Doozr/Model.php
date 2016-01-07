<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Model.
 *
 * Model.php - Model of Doozr - This Model-class provides access to Database libs in Model/Lib/...
 * by acting as a configurable (.config.json) proxy.
 *
 * PHP versions 5.5
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
 *
 * Copyright (c) 2005 - 2016, Benjamin Carl - All rights reserved.
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
 *   must display the following acknowledgment: This product includes software
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
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Decorator/Singleton.php';

/**
 * Doozr - Model.
 *
 * Model of Doozr - This Model-class provides access to Database libs in Model/Lib/...
 * by acting as a configurable (.config.json) proxy.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Model extends Doozr_Base_Decorator_Singleton
{
    /**
     * The decorator configuration.
     *
     * @var array
     */
    protected $decoratorConfiguration;

    /**
     * Doozr_Configuration instance for access to configuration.
     *
     * @var Doozr_Configuration
     */
    protected $config;

    /**
     * Doozr_Logging instance for logging.
     *
     * @var Doozr_Logging
     */
    protected $logger;

    /**
     * The return value of init method of a driver.
     *
     * @var mixed
     */
    protected $handle;

    /**
     * Constructor.
     *
     * @param array          $configuration Configuration for decorating
     * @param Doozr_Registry $registry      Doozr registry instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function __construct(
        array $configuration,
        Doozr_Registry $registry
    ) {
        $this
            ->registry($registry)
            ->decoratorConfiguration($configuration)
            ->enabled($configuration['enabled']);

        // Start decorating if enabled ...
        if (true === $this->isEnabled()) {
            // ... what we get is generic and mixed in result so we store it named as "handle" MODEL->getHandle()
            $this->handle(
                $this->init($this->getDecoratorConfiguration())
            );
        }
    }

    /**
     * Setter for handle.
     *
     * @param mixed $handle The handle
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setHandle($handle)
    {
        $this->handle = $handle;
    }

    /**
     * Setter for handle.
     *
     * @param mixed $handle The handle
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function handle($handle)
    {
        $this->setHandle($handle);

        return $this;
    }

    /**
     * Getter for handle.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed Handle
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * Setter for enabled.
     *
     * @param bool $enabled TRUE is database (model) is enabled. FALSE to set disabled.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * Setter for enabled.
     *
     * @param bool $enabled TRUE is database (model) is enabled. FALSE to set disabled.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Model Instance for chaining
     */
    protected function enabled($enabled)
    {
        $this->setEnabled($enabled);

        return $this;
    }

    /**
     * Getter for enabled.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool The enabled status
     */
    protected function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Natural speak proxy for getEnabled().
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool The enabled status
     */
    protected function isEnabled()
    {
        return $this->getEnabled();
    }

    /**
     * Setter for decorator configuration.
     *
     * @param array $decoratorConfiguration The configuration
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
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
     *
     * @return $this Instance for chaining
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
     *
     * @return array The decorator configuration.
     */
    protected function getDecoratorConfiguration()
    {
        return $this->decoratorConfiguration;
    }
}
