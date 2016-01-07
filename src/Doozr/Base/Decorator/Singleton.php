<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr Base Decorator Singleton.
 *
 * Singleton.php - Base class for decorators.
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
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Class/Singleton.php';

/**
 * Doozr Base Decorator Singleton.
 *
 * Base class for decorators.
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
class Doozr_Base_Decorator_Singleton extends Doozr_Base_Class_Singleton
{
    /**
     * Configuration for decorator.
     *
     * @var array|bool
     */
    protected $configuration;

    /**
     * Contains the current status of decorated class.
     *
     * @var bool
     */
    protected $enabled;

    protected $decoratedObject;
    protected $decoratedClass;

    /**
     * Initializes the decorated class by calling its init() method (forward call).
     *
     * @param array $configuration Reference to the configuration for decorator
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @throws Doozr_Exception
     *
     * @return mixed Handle to driver (can be of any kind!).
     */
    protected function init(array $configuration)
    {
        // Check for the name of the oxm to decorate
        if (false === isset($configuration['oxm'])) {
            throw new Doozr_Exception(
                sprintf('Base decorator singleton needs OxM name for decorating!')
            );
        }

        // Bootstrap script required for decorated class to run
        if (true === isset($configuration['driver']) && false || null !== $configuration['driver']) {
            $driverFile = DOOZR_DOCUMENT_ROOT.'Model/'.$configuration['oxm'].'/'.$configuration['driver'];

            $this->getRegistry()->getLogger()->debug(
                'Loading model driver for OxM "'.$configuration['oxm'].'" from file "'.$driverFile.'"'
            );

            $result = include_once $driverFile;
        } else {
            $result = true;
        }

        if (false === $result) {
            throw new Doozr_Exception(
                sprintf(
                    'Driver ("%s") configured but could not be loaded or is already loaded.',
                    $configuration['path'].$configuration['driver']
                )
            );
        }

        // Store the decorated class name & instance
        $this->decoratedClass  = 'Model\\'.$configuration['oxm'].'\\Driver';
        $this->decoratedObject = new $this->decoratedClass();

        // Call pre-install hook
        $preInstallResult = forward_static_call(
            [$this->decoratedClass, 'preInstall'],
            $configuration
        );

        // Call install
        $handle = forward_static_call(
            [$this->decoratedClass, 'install'],
            $preInstallResult
        );

        // Call post-install hook
        $postInstallResult = forward_static_call(
            [$this->decoratedClass, 'preInstall'],
            $handle,
            $preInstallResult
        );

        // Store configuration for further processing
        $this->configuration = $preInstallResult;

        return $postInstallResult;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Generic Accessor's
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Generic accessor for object methods.
     *
     * @param string $method    Name of called method
     * @param array  $arguments Arguments as array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed Result
     */
    public function __call($method, array $arguments = null)
    {
        // Forward only the calls which cannot be satisfied by current instance - dumb forwarding
        if (false === method_exists($this, $method)) {

            // Error handling if method does also not exist in decorated object
            if (false === method_exists($this->decoratedObject, $method) || false === is_callable([$this->decoratedObject, $method])) {
                throw new Doozr_Exception(
                    sprintf(
                        'Method "%s" in instance of class "%s" not found or not callable (check if visibility is public)%s!',
                        $method,
                        get_class($this->decoratedObject),
                        (count($arguments) > 0) ? ' (arguments passed: '.var_export($arguments, true).')' : ''
                    )
                );
            }

            if (count($arguments) > 0) {
                $result = call_user_func_array([$this->decoratedObject, $method], $arguments);
            } else {
                $result = call_user_func([$this->decoratedObject, $method]);
            }

            return $result;
        }
    }

    /**
     * Generic accessor for static class methods.
     *
     * @param string $method    Name of called method
     * @param array  $arguments Arguments as array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed Result
     */
    public static function __callStatic($method, array $arguments = null)
    {
        #if
        #return forward_static_call_array()
    }
}
