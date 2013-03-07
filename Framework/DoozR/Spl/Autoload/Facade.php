<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR-Loader-Autoloader-Spl-Facade
 *
 * DoozRLoaderAutoloaderSplFacade.php - Facade to the SPL-Autoload-Subsystem
 * A simple and OOP-based Interface for the procedural SPL-functionality.
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
 * @package    DoozR_Loader
 * @subpackage DoozR_Loader_Autoloader_Spl
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2011 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

/**
 * DoozR-Loader-Autoloader-Spl-Facade
 *
 * Facade to the SPL-Autoload-Subsystem.
 * A simple and OOP-based Interface for the procedural SPL-functionality.
 *
 * @category   DoozR
 * @package    DoozR_Loader
 * @subpackage DoozR_Loader_Autoloader_Spl
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2011 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Loader_Autoloader_Spl_Facade
{
    /**
     * holds the autoloaders processed by the SPL-Facade
     *
     * @var array
     * @access private
     * @static
     */
    private static $_autoloader = array();

    /**
     * holds the init-done status
     * TRUE = already initialized, otherwise FALSE
     *
     * @var boolean
     * @access private
     * @static
     */
    private static $_initialized = false;


    /**
     * initializes basic setup
     *
     * This method is intend to initialize the basic setup.
     *
     * @param boolean $checkMagicAutoload TRUE to keep magic function __autoload working, otherwise FALSE
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @static
     */
    public static function init($checkMagicAutoload = true)
    {
        // check first if init wasn't done already
        if (!self::$_initialized) {
            // clear stack
            spl_autoload_register(null, false);

            // check if a magic __autoload function exists
            if ($checkMagicAutoload && function_exists('__autoload')) {
                spl_autoload_register('__autoload');
            }

            // mark init done
            self::$_initialized = true;
        }
    }


    /**
     * registers a Autoloader based on given config
     *
     * This method is intend to register a new Autoloader to SPL-Subsystem based on the Information (setup) of given
     * config (DoozR_Loader_Autoloader_Spl_Config-Instance).
     *
     * @param DoozR_Loader_Autoloader_Spl_Config $config An instance of the DoozR_Loader_Autoloader_Spl_Config-Class
     *
     * @return  boolean true if Autoloader was registered successfully, otherwise false
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @static
     */
    public static function register(DoozR_Loader_Autoloader_Spl_Config $config)
    {
        // retrieve currently configured autoloader
        $registeredAutoloader = self::getSplAutoloader();

        // assume op will fail
        $result = false;

        // check if autoloader is already registered (not uid - real check)
        if (!$registeredAutoloader || !self::_isRegistered(
            $config->isClass(),
            $config->getClass(),
            $config->getMethod(),
            $registeredAutoloader
        )) {
            // construct ...
            if ($config->isClass()) {
                $loader = array($config->getClass(), $config->getMethod());
            } else {
                $loader = $config->getMethod();
            }

            // register autoloader
            if (!$registeredAutoloader || !$config->getPriority()) {
                // we don't need to worry about the order/priority
                $result = (is_callable($loader) && spl_autoload_register($loader));
            } else {
                // check for order cause we got a specific priority

            }
        }

        // add file-extension from config
        self::addFileExtensions($config->getExtension());

        // store config
        self::$_autoloader[$config->getUid()] = $config;

        // return TRUE = registered successfuly, NULL = already registered, FALSE = error
        return $result;
    }


    /**
     * releases a previous registered Autoloader
     *
     * This method is intend to release a registered Autoloader by its unique-Id.
     *
     * @param string $uId The unique-id used to identify the Autoloader which should be removed
     *
     * @return  boolean true if Autoloader was released successfully, otherwise false
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @static
     */
    public static function release($uId)
    {
        // get autoloader setup by uid
        $config = self::$_autoloader[$uId];

        // assume op will fail
        $result = false;

        // get currently configured autoloader
        $registeredAutoloader = self::getSplAutoloader();

        // check if autoloader is registered (not uid - real check)
        if ($registeredAutoloader && self::_isRegistered(
            $config->isClass(),
            $config->getClass(),
            $config->getMethod(),
            $registeredAutoloader
        )) {
            // construct ...
            if ($config->isClass()) {
                $loader = array($config->getClass(), $config->getMethod());
            } else {
                $loader = $config->getMethod();
            }

            // unregister autoloader
            $result = spl_autoload_unregister($loader);
        }

        // remove config
        unset(self::$_autoloader[$uId]);

        // return result
        return $result;
    }


    /**
     * returns the config of the last registered AL (checks current spl-stack)
     *
     * This method is intend to return the config of the last registered AL (checks current spl-stack).
     *
     * @return  mixed DoozR_Loader_Autoloader_Spl_Config of last AL if exist, otherwise boolean FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
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
            $result = self::_findAutoloaderByFunction(end($autoloader));
        }

        // return result
        return $result;
    }


    /**
     * returns the config of the first registered AL (checks current spl-stack)
     *
     * This method is intend to return the config of the first registered AL (checks current spl-stack).
     *
     * @return  mixed DoozR_Loader_Autoloader_Spl_Config of first AL if exist, otherwise boolean FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
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
            $result = self::_findAutoloaderByFunction(reset($autoloader));
        }

        // return result
        return $result;
    }


    /**
     * returns a single - or a list of - configuration(s) of registered Autoloaders
     *
     * This method is intend to return a single - or a list of - configurations of currently registered Autoloaders.
     *
     * @param string $uId An unique-Id of an Autoloader to retrieve a single config
     *
     * @return  mixed a single DoozR_Loader_Autoloader_Spl_Config or a list of (array) DoozR_Loader_Autoloader_Spl_Config's
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @static
     */
    public static function getAutoloader($uId = null)
    {
        if ($uId) {
            $autoloader = (isset(self::$_autoloader[$uId])) ? self::$_autoloader[$uId] : null;
        } else {
            $autoloader = self::$_autoloader;
        }

        // return list or single autoloader
        return $autoloader;
    }


    /**
     * returns a raw list of currently registered SPL-Autoloader
     *
     * This method is intend to return a raw list of currently registered SPL-Autoloader.
     *
     * @return  array A list of registered SPL-Autoloader
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @static
     */
    public static function getSplAutoloader()
    {
        return spl_autoload_functions();
    }


    /**
     * adds a single file-extension to SPL-list of autoload_extensions
     *
     * This method is intend add a single file-extensions to SPL-list of autoload_extensions.
     *
     * @param string $extension The file-extension to add
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
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
     * adds a list of file-extensions to SPL-list of autoload_extensions
     *
     * This method is intend to add a list of file-extensions to SPL-list of autoload_extensions.
     *
     * @param array $extensions The extensions to add
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
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
     * changes the priority (order on spl-autoloader stack) for previously registered ALs
     *
     * This method is intend to change the priority (order on spl-autoloader stack) for previously registered ALs.
     *
     * @param string  $uId      The unique-Id of the AL to change priority for
     * @param integer $priority The new priority of the AL
     *
     * @return  boolean TRUE on success, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @static
     */
    public static function changeAutoloaderPriority($uId, $priority = 0)
    {
        // return if no autoloader for given uid exists
        if (!isset(self::$_autoloader[$uId])) {
            return false;
        }

        // get current active autoloader order
        $autoloaderPriority = spl_autoload_functions();

        // count of current file-extensions
        $autoloaderCount = count($autoloaderPriority);

        // set prio to max possible
        ($priority > ($autoloaderCount-1)) ? ($priority = $autoloaderCount-1) : '';

        // get config as $config for better reading
        $config = self::$_autoloader[$uId];

        // find out what we are looking for ...
        $loader = ($config->isClass()) ? array($config->getClass(), $config->getMethod()) : $config->getFunction();

        // check if reposition needed
        if (!(isset($autoloaderPriority[$priority]) && $autoloaderPriority[$priority] == $loader)) {
            // if the new prio is 0 = first element we use the fastet way possible
            if ($priority == 10) {
                // remove
                spl_autoload_unregister($loader);

                // add at first pos (prepend = true)
                spl_autoload_register($loader, true, true);
            } else {
                // remove element from array
                $autoloaderPriority = array_remove_value($autoloaderPriority, $loader);

                // and insert
                array_splice($autoloaderPriority, $priority, 0, array($loader));

                // unregister autoloader in new order
                foreach ($autoloaderPriority as $autoloader) {
                    spl_autoload_unregister($autoloader);
                }

                // register autoloader in new order
                foreach ($autoloaderPriority as $autoloader) {
                    spl_autoload_register($autoloader);
                }
            }
        } else {
            // nothing to do
            return false;
        }

        // return
        return true;
    }


    /**
     * changes the file-extension priority (order on spl-file-extension stack)
     *
     * This method is intend to change the file-extension priority (order on spl-file-extension stack).
     *
     * @param string  $fileExtension The file-extension to change priority for
     * @param integer $priority      The new priority of the file-extension
     *
     * @return  boolean TRUE on success, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @static
     */
    public static function changeFileExtensionPriority($fileExtension = '.php', $priority = 0)
    {
        // get current active extension order
        $extensionPriority = explode(',', spl_autoload_extensions());

        // count of current file-extensions
        $extensionCount = count($extensionPriority);

        // set prio to max possible
        ($priority > ($extensionCount-1)) ? ($priority = $extensionCount-1) : '';

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
     * returns the list of file-extension ordered by its priority
     *
     * This method is intend to return the list of file-extension ordered by its priority.
     *
     * @return  string The list of all file-extensions from spl-stack
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @static
     */
    public static function getFileExtensionPriority()
    {
        return spl_autoload_extensions();
    }


    /**
     * sets a new list of file-extension ordered by its priority
     *
     * This method is intend to set a new list of file-extension ordered by its priority.
     *
     * @param string $prioritizedFileExtensions The prioritized list of file-extensions
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @static
     */
    public static function setFileExtensionPriority($prioritizedFileExtensions)
    {
        spl_autoload_extensions($prioritizedFileExtensions);
    }


    /**
     * checks if a autoloader is registered
     *
     * This method is intend to check if a autoloader is registered.
     *
     * @param boolean $isClass    True if Autoloader-Method is part of a class
     * @param string  $class      The name of the class containing the Autoloader-Method
     * @param string  $method     The name of the Autolaoder-Method (Function)
     * @param mixed   $autoloader A already retrieved list of currently registered SPL-Autoloaders
     *
     * @return  boolean True if autoloader is regsitered, otherwise false
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @static
     */
    private static function _isRegistered($isClass, $class, $method, $autoloader = null)
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
     * returns a config (DoozR_Loader_Autoloader_Spl_Config) of a registered AL by it's AL-function/method
     *
     * This method is intend to return return a config (DoozR_Loader_Autoloader_Spl_Config) of a registered AL by
     * it's AL-function/method.
     *
     * @param mixed $function STRING The name of the function, or ARRAY containing Class, Method
     *
     * @return  mixed DoozR_Loader_Autoloader_Spl_Config of found AL, otherwise boolean FALSE if not found
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @static
     */
    private static function _findAutoloaderByFunction($function)
    {
        // is function or method of class?
        if (is_array($function)) {
            foreach (self::$_autoloader as $autoloader) {
                if ($autoloader->getClass() == $function[0] && $autoloader->getFunction() == $function[1]) {
                    return $autoloader;
                }
            }
        } else {
            foreach (self::$_autoloader as $autoloader) {
                if ($autoloader->getFunction() == $function) {
                    return $autoloader;
                }
            }
        }

        // not found
        return false;
    }
}

?>
