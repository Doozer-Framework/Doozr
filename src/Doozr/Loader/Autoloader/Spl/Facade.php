<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Loader - Autoloader - Spl - Facade
 *
 * Facade.php - Facade to the SPL-Autoload-Subsystem
 * A simple and OOP-based Interface for the procedural SPL-functionality.
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
 * @package    Doozr_Loader
 * @subpackage Doozr_Loader_Autoloader
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT.'Doozr/Loader/Autoloader/Spl/Config/Interface.php';

/**
 * Doozr - Loader - Autoloader - Spl - Facade
 *
 * Facade to the SPL-Autoload-Subsystem. A simple and OOP-based Interface for
 * the procedural SPL-functionality.
 *
 * @category   Doozr
 * @package    Doozr_Loader
 * @subpackage Doozr_Loader_Autoloader
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Loader_Autoloader_Spl_Facade
{
    /**
     * holds the autoloaders processed by the SPL-Facade
     *
     * @var array
     * @access private
     * @static
     */
    private static $autoloader = [];

    /**
     * holds the init-done status
     * TRUE = already initialized, otherwise FALSE
     *
     * @var bool
     * @access private
     * @static
     */
    private static $initialized = false;


    /**
     * This method is intend to initialize the basic setup.
     *
     * @param bool $checkMagicAutoload TRUE to keep magic function __autoload working, otherwise FALSE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @static
     */
    public static function init($checkMagicAutoload = true)
    {
        // check first if init wasn't done already
        if (!self::$initialized) {
            // clear stack
            spl_autoload_register(null, false);

            // check if a magic __autoload function exists
            if ($checkMagicAutoload && function_exists('__autoload')) {
                spl_autoload_register('__autoload');
            }

            // mark init done
            self::$initialized = true;
        }
    }

    /**
     * Registers an autoloader by passed configuration. Registers a new autoloader to SPL-Subsystem
     * based on the Information (setup) of given configuration (Doozr_Loader_Autoloader_Spl_Config-Instance).
     *
     * @param Doozr_Loader_Autoloader_Spl_Config[]|Doozr_Loader_Autoloader_Spl_Config $configuration An instance of the Doozr_Loader_Autoloader_Spl_Config
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if autoloader was registered successfully, otherwise FALSE
     * @access public
     * @static
     * @throws Doozr_Exception
     */
    public static function attach($configuration)
    {
        // Assume op will fail
        $result     = false;
        $prioritize = false;

        // init if not already done
        if (!self::$initialized) {
            self::init(true);
        }

        // Check input
        if (is_array($configuration)) {
            /* @var Doozr_Loader_Autoloader_Spl_Config $singleConfig */
            foreach ($configuration as $singleConfig) {
                if (false === $singleConfig instanceof Doozr_Loader_Autoloader_Spl_Config) {
                    throw new Doozr_Exception(
                        'Passed configuration must be of type: "Doozr_Loader_Autoloader_Spl_Config_Interface"'
                    );
                }
            }
        } else {
            if (false === $configuration instanceof Doozr_Loader_Autoloader_Spl_Config) {
                throw new Doozr_Exception(
                    'Passed configuration must be of type: "Doozr_Loader_Autoloader_Spl_Config_Interface"'
                );
            }

            $configuration = array($configuration);
        }

        // iterate passed configurations
        foreach ($configuration as $singleConfig) {

            // retrieve currently configured autoloader
            $registeredAutoloader = self::getSplAutoloader();

            // check if autoloader is already registered (not uid - real check)
            if (
                !$registeredAutoloader ||
                !self::isRegistered(
                    $singleConfig->isClass(),
                    $singleConfig->getClass(),
                    $singleConfig->getMethod(),
                    $registeredAutoloader
                )
            ) {
                // build loader construct ...
                if ($singleConfig->isLoader()) {
                    $loader = array($singleConfig, $singleConfig->getMethod());
                } else {
                    if ($singleConfig->isClass()) {
                        $loader = array($singleConfig->getClass(), $singleConfig->getMethod());
                    } else {
                        $loader = $singleConfig->getMethod();
                    }
                }

                // add extension(s) of current configuration to spl
                self::addFileExtensions($singleConfig->getExtension());

                // register autoloader
                $result = (is_callable($loader) && spl_autoload_register($loader));

                //
                if (
                    $singleConfig->getPriority() !== null &&
                    $singleConfig->getPriority() !== count($registeredAutoloader)
                ) {
                    $prioritize = true;
                }
            }

            // add file-extension from configuration
            self::addFileExtensions($singleConfig->getExtension());

            // store configuration
            self::$autoloader[$singleConfig->getUid()] = $singleConfig;

            if ($prioritize === true) {
                self::changeAutoloaderPriority($singleConfig->getUid(), $singleConfig->getPriority());
            }
        }

        // return TRUE = registered successfully, NULL = already registered, FALSE = error
        return $result;
    }

    /**
     * Releases a previous registered Autoloader
     *
     * This method is intend to release a registered Autoloader by its unique-Id.
     *
     * @param string $uId The unique-id used to identify the Autoloader which should be removed
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool true if Autoloader was released successfully, otherwise false
     * @access public
     * @static
     */
    public static function release($uId)
    {
        // get autoloader setup by uid
        $configuration = self::$autoloader[$uId];

        // assume op will fail
        $result = false;

        // get currently configured autoloader
        $registeredAutoloader = self::getSplAutoloader();

        // check if autoloader is registered (not uid - real check)
        if ($registeredAutoloader && self::isRegistered(
            $configuration->isClass(),
            $configuration->getClass(),
            $configuration->getMethod(),
            $registeredAutoloader
        )) {
            // construct ...
            if ($configuration->isClass()) {
                $loader = array($configuration->getClass(), $configuration->getMethod());
            } else {
                $loader = $configuration->getMethod();
            }

            // unregister autoloader
            $result = spl_autoload_unregister($loader);
        }

        // remove configuration
        unset(self::$autoloader[$uId]);

        // return result
        return $result;
    }

    /**
     * Returns the configuration of the last registered AL (checks current spl-stack)
     *
     * This method is intend to return the configuration of the last registered AL (checks current spl-stack).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Doozr_Loader_Autoloader_Spl_Config of last AL if exist, otherwise boolean FALSE
     * @access public
     * @static
     */
    public static function getLast()
    {
        // get currently registered SPL-Autoloader
        $autoloader = self::getSplAutoloader();

        // assume op fails
        $result = false;

        // check if result from stack valid and containing elements
        if ($autoloader && count($autoloader) > 0) {
            // get first
            $result = self::findAutoloaderByFunction(end($autoloader));
        }

        // return result
        return $result;
    }

    /**
     * Returns the configuration of the first registered AL (checks current spl-stack)
     *
     * This method is intend to return the configuration of the first registered AL (checks current spl-stack).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Doozr_Loader_Autoloader_Spl_Config of first AL if exist, otherwise boolean FALSE
     * @access public
     * @static
     */
    public static function getFirst()
    {
        // get currently registered SPL-Autoloader
        $autoloader = self::getSplAutoloader();

        // assume op fails
        $result = false;

        // check if result from stack valid and containing elements
        if ($autoloader && count($autoloader) > 0) {
            // get first
            $result = self::findAutoloaderByFunction(reset($autoloader));
        }

        // return result
        return $result;
    }

    /**
     * Returns a single - or a list of - configuration(s) of registered Autoloaders
     *
     * This method is intend to return a single - or a list of - configurations of currently registered Autoloaders.
     *
     * @param string $uId An unique-Id of an Autoloader to retrieve a single configuration
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed a single Doozr_Loader_Autoloader_Spl_Config or a list of (array) Doozr_Loader_Autoloader_Spl_Config's
     * @access public
     * @static
     */
    public static function getAutoloader($uId = null)
    {
        if ($uId) {
            $autoloader = (isset(self::$autoloader[$uId])) ? self::$autoloader[$uId] : null;
        } else {
            $autoloader = self::$autoloader;
        }

        // return list or single autoloader
        return $autoloader;
    }

    /**
     * Returns a raw list of currently registered SPL-Autoloader
     *
     * This method is intend to return a raw list of currently registered SPL-Autoloader.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed An array of registered SPL-Autoloader(s) if set, otherwise FALSE
     * @access public
     * @static
     */
    public static function getSplAutoloader()
    {
        return spl_autoload_functions();
    }

    /**
     * Adds a single file-extension to SPL-list of autoload_extensions
     *
     * This method is intend add a single file-extensions to SPL-list of autoload_extensions.
     *
     * @param string $extension The file-extension to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @static
     */
    public static function addFileExtension($extension)
    {
        // if not dot at first position add it here
        if ($extension{0} != '.') {
            $extension = '.'.$extension;
        }

        return self::addFileExtensions(array($extension));
    }

    /**
     * Adds a list of file-extensions to SPL-list of autoload_extensions
     *
     * This method is intend to add a list of file-extensions to SPL-list of autoload_extensions.
     *
     * @param array $extensions The extensions to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @static
     */
    public static function addFileExtensions(array $extensions)
    {
        // get list of currently configured autoload-file-extensions
        $splExtensions = explode(',', spl_autoload_extensions());

        // merge existing extension and new extension and remove duplicates
        $splExtensions = array_unique(array_merge($splExtensions, $extensions));

        // set file extensions for spl
        spl_autoload_extensions(implode(',', $splExtensions));
    }

    /**
     * Changes the priority (order on spl-autoloader stack) for previously registered autoloaders.
     *
     * @param string $uniqueId The unique-Id of the AL to change priority for
     * @param int    $priority The new priority of the AL
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success, otherwise FALSE
     * @access public
     * @static
     */
    public static function changeAutoloaderPriority($uniqueId, $priority = 0)
    {
        // Return if no autoloader for given uid exists
        if (false === isset(self::$autoloader[$uniqueId])) {
            return false;
        }

        // Get current active autoloader order
        $autoloaderPriority = spl_autoload_functions();

        // Count of current file-extensions
        $autoloaderCount = count($autoloaderPriority);

        // Set priority to max possible
        ($priority > ($autoloaderCount - 1)) ? ($priority = $autoloaderCount - 1) : '';

        // Get configuration as $configuration for better reading
        $configuration = self::$autoloader[$uniqueId];

        // Find out what we are looking for ...
        if ($configuration->isClass()) {
            $loader = array($configuration->getClass(), $configuration->getMethod());
        } elseif ($configuration->isLoader()) {
            $loader = array($configuration, $configuration->getMethod());
        } else {
            $loader = $configuration->getFunction();
        }

        // Check if repositioning in general required => If loader already is at position we do nothing.
        if (!(isset($autoloaderPriority[$priority]) && $autoloaderPriority[$priority] === $loader)) {

            // If the new priority is 0 = first element we use the fastest way possible
            if ($priority === 0) {
                // Remove & Directly add at first pos (prepend = true)
                spl_autoload_unregister($loader);
                spl_autoload_register($loader, true, true);

            } else {
                // Remove element from array & insert
                $autoloaderPriority = array_remove_value($autoloaderPriority, $loader);
                array_splice($autoloaderPriority, $priority, 0, array($loader));

                // Unregister autoloader in new order
                foreach ($autoloaderPriority as $autoloader) {
                    spl_autoload_unregister($autoloader);
                }

                // register autoloader in new order
                foreach ($autoloaderPriority as $autoloader) {
                    spl_autoload_register($autoloader);
                }
            }
        }

        return true;
    }

    /**
     * Changes the file-extension priority (order on spl-file-extension stack)
     *
     * This method is intend to change the file-extension priority (order on spl-file-extension stack).
     *
     * @param string  $fileExtension The file-extension to change priority for
     * @param int $priority      The new priority of the file-extension
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success, otherwise FALSE
     * @access public
     * @static
     */
    public static function changeFileExtensionPriority($fileExtension = '.php', $priority = 0)
    {
        // get current active extension order
        $extensionPriority = explode(',', spl_autoload_extensions());

        // count of current file-extensions
        $extensionCount = count($extensionPriority);

        // set prio to max possible
        ($priority > ($extensionCount - 1)) ? ($priority = $extensionCount - 1) : '';

        // check if reposition needed
        if (!(isset($extensionPriority[$priority]) && $extensionPriority[$priority] == $fileExtension)) {
            // remove element by value
            $extensionPriority = array_remove_value($extensionPriority, $fileExtension);

            // and insert
            array_splice($extensionPriority, $priority, 0, $fileExtension);

            // and set to spl's list of autoload extensions
            spl_autoload_extensions(implode(',', $extensionPriority));
        } else {
            // nothing to do
            return false;
        }

        // success
        return true;
    }

    /**
     * Returns the list of file-extension ordered by its priority
     *
     * This method is intend to return the list of file-extension ordered by its priority.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The list of all file-extensions from spl-stack
     * @access public
     * @static
     */
    public static function getFileExtensionPriority()
    {
        return spl_autoload_extensions();
    }

    /**
     * Sets a new list of file-extension ordered by its priority
     *
     * This method is intend to set a new list of file-extension ordered by its priority.
     *
     * @param string $prioritizedFileExtensions The prioritized list of file-extensions
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @static
     */
    public static function setFileExtensionPriority($prioritizedFileExtensions)
    {
        spl_autoload_extensions($prioritizedFileExtensions);
    }

    /**
     * Checks if a autoloader is registered
     *
     * This method is intend to check if a autoloader is registered.
     *
     * @param bool $isClass    True if Autoloader-Method is part of a class
     * @param string  $class      The name of the class containing the Autoloader-Method
     * @param string  $method     The name of the Autolaoder-Method (Function)
     * @param mixed   $autoloader A already retrieved list of currently registered SPL-Autoloaders
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool True if autoloader is regsitered, otherwise false
     * @access private
     * @static
     */
    private static function isRegistered($isClass, $class, $method, $autoloader = null)
    {
        // get autoloader if not received via parameter
        if (!$autoloader) {
            $autoloader = self::getSplAutoloader();
        }

        // construct lookup-string
        if ($isClass) {
            $needle = array($class, $method);
        } else {
            $needle = $method;
        }

        // return status
        return in_array($needle, $autoloader);
    }

    /**
     * Returns a configuration (Doozr_Loader_Autoloader_Spl_Config) of a registered AL by it's AL-function/method
     *
     * This method is intend to return return a configuration (Doozr_Loader_Autoloader_Spl_Config) of a registered AL by
     * it's AL-function/method.
     *
     * @param mixed $function STRING The name of the function, or ARRAY containing Class, Method
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Doozr_Loader_Autoloader_Spl_Config of found AL, otherwise boolean FALSE if not found
     * @access private
     * @static
     */
    private static function findAutoloaderByFunction($function)
    {
        // is function or method of class?
        if (is_array($function)) {
            foreach (self::$autoloader as $autoloader) {
                if ($autoloader->getClass() == $function[0] && $autoloader->getFunction() == $function[1]) {
                    return $autoloader;
                }
            }
        } else {
            foreach (self::$autoloader as $autoloader) {
                if ($autoloader->getFunction() == $function) {
                    return $autoloader;
                }
            }
        }

        // not found
        return false;
    }
}
