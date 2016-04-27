<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Base - Database - Abstract
 *
 * Abstract.php - Abstract base class for building a Database Abstraction Layer for the Doozr Framework
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
 * Please feel free to contact us via e-mail: <opensource@clickalicious.de>
 *
 * @category   Doozr
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Database
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

/**
 * Doozr - Base - Database - Abstract
 *
 * Base/master-abstract-class for building a Database
 *
 * @category   Doozr
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Database
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 * @abstract
 */
abstract class Doozr_Base_Database_Facade_Abstract extends Doozr_Base_Class
{
    /**
     * reference to DBA/ORM
     *
     * holds an cached object of the dba/orm
     *
     * @var object
     * @access protected
     */
    protected $cachedOrm = null;

    /**
     * ORM-configuration
     *
     * the configuration of/to the ORM/DBA
     *
     * @var array
     * @access private
     * @static
     */
    protected static $ormConfig;

    /**
     * contains the directories
     * (with + without lib directory)
     *
     * @var array
     * @access protected
     * @static
     */
    protected static $ormDirectory = [];

    /**
     * stored instance identifier used for configuration
     * (e.g. for migration FROM A => (TO) B)
     *
     * @var mixed
     * @access protected
     */
    protected $instanceIdentifier;

    /**
     * holds the instance of the path-manager
     *
     * @var object
     * @access protected
     */
    protected $path;

    /**
     * Instance of Doozr_Configuration
     *
     * @var Doozr_Configuration
     * @access protected
     */
    protected $config;

    /**
     * Instance of Doozr_Logging
     *
     * @var Doozr_Logging
     * @access protected
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param Doozr_Path          $path   Instance of Doozr_Path manager for path'
     * @param Doozr_Configuration $config Instance of Doozr_Configuration holding Doozr's configuration
     * @param Doozr_Logging       $logger Instance of Doozr_Logging
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     */
    public function __construct(Doozr_Path $path, Doozr_Configuration $config, Doozr_Logging $logger)
    {
        // store instances
        $this->path   = $path;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * setter for current used configuration-identifier
     *
     * This method is intend as setter for current used configuration-identifier.
     *
     * @param string $identifier The identifier to use for this instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string TRUE on success, otherwise FALSE
     * @access public
     */
    public function setInstanceIdentifier($identifier)
    {
        return ($this->instanceIdentifier = $identifier);
    }

    /**
     * getter for current used configuration-identifier
     *
     * This method is intend as getter for current used configuration-identifier.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING the identifier of this instance if previously set, otherwise NULL
     * @access public
     */
    public function getInstanceIdentifier()
    {
        return $this->instanceIdentifier;
    }

    /**
     * dispatch configuration to ORM/DBA
     *
     * loads the main class or function from the configured ORM/DBA and pass
     * all relevant configuration-settings to it
     *
     * @param object &$referenceWrapper The reference to the wrapper
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Object if cached ORM/DBA object exists, otherwise true
     * @access public
     * @throws Doozr_Exception
     */
    public function dispatch(&$referenceWrapper = null)
    {
        throw new Doozr_Exception(
            'No dispatch method found in the called Wrapper! Need the dispatch Method to configure the called ORM/DBA!',
            E_USER_CORE_FATAL_EXCEPTION
        );
    }

    /**
     * loads a (configuration-)file
     *
     * loads a (configuration-)file after checking it existance and readable-status
     *
     * @param string $filename The name (and path) to the file
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool True if file exists AND readable AND include succeeded
     * @access protected
     * @throws Doozr_Exception
     */
    protected function loadFile($filename)
    {
        // check if file exists
        if (!file_exists($filename)) {
            throw new Doozr_Exception(
                'Error file: "'.$filename.'" not found! ORM could not be initialized.'
            );
        }

        // check if file is readable
        if (!is_readable($filename)) {
            throw new Doozr_Exception(
                'Error file: "'.$filename.'" found but it isn\'t readable! ORM could not be initialized.'
            );
        }

        // return status of include operation
        return include_once $filename;
    }

    /**
     * Returns the library files required for ORM as array
     *
     * @param string $input The identifier for lookup
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The required files ORM/DBA
     * @access public
     */
    public function getLibraryFiles($input)
    {
        // assume the result is the same string as the input
        $result = array($input);

        // just change if method for changing/translation exists
        if (method_exists($this, 'getRequiredLibraryFiles')) {
            $result = $this->getRequiredLibraryFiles($input);
        }

        // and finally return the resulting string
        return $result;
    }

    /**
     * returns the name of the current used ORM/DBA
     *
     * returns the name of the current used ORM/DBA in a dynamic way. no need to define a
     * name anywhere.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The name of the current ORM/DBA
     * @access protected
     */
    protected function getOrmName()
    {
        // ORM-name dynamic by class-name operation
        return str_replace('Doozr_Model_', '', str_replace('_Facade', '', get_class($this)));
    }

    /**
     * returns the directory to the ORM/DBA
     *
     * returns the directory to the ORM/DBA.
     *
     * @param bool $stripLibrary TRUE to get just the root to ORM, otherwise FALSE to retrieve full-path
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The path to the current ORM/DBA
     * @access protected
     */
    protected function getOrmDirectory($stripLibrary = false)
    {
        if (!isset(self::$ormDirectory[$stripLibrary])) {
            // get name of ORM/DBA
            $name = $this->getOrmName();

            // get directory of ORM/DBA
            $directory = constant(get_class($this).'::ORM_DIRECTORY');

            // get generic model path
            if ($directory && !$stripLibrary) {
                $directory = $this->path->get('MODEL', $name.'/'.$directory.'/');
            } else {
                $directory = $this->path->get('MODEL', $name.'/');
            }

            self::$ormDirectory[$stripLibrary] = $directory;
        }

        // return the directory
        return self::$ormDirectory[$stripLibrary];
    }

    /**
     * retrieves the configuration for ORM/DBA via Doozr_Kernel::configuration()
     *
     * This method is intend to retrieve the configuration for ORM/DBA via Doozr_Kernel::configuration().
     *
     * @param string $orm The name of the ORM to retrieve configuration for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean Config retrieved by Doozr_Kernel::configuration()
     * @access protected
     * @throws Doozr_Exception
     */
    protected function retrieveOrmConfig($orm)
    {
        // Try to retrieve configuration for ORM/DBA
        try {
            self::$ormConfig = $this->config->kernel->model->{$orm};

        } catch (Doozr_Config_Ini_Exception $e) {
            throw new Doozr_Exception(
                sprintf(
                    'Configuration for ORM "%s" could not be retrieved! Please check your configuration', $orm
                ),
                null,
                $e
            );
        }

        // return retrieved configuration
        return true;
    }

    /**
     * Returns the configuration for ORM/DBA
     *
     * @param mixed $instanceIdentifier The identifier to use for configuration-retrieval, NULL to return the whole configuration
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array $ormConfig The configuration for the ORM/DBA
     * @access public
     * @static
     * @throws Doozr_Exception
     */
    protected function getConfig($instanceIdentifier = null)
    {
        // correct format uppercase
        $instanceIdentifier = strtoupper($instanceIdentifier);

        // check if identifier exist
        if (!isset(self::$ormConfig[$instanceIdentifier])) {
            throw new Doozr_Exception(
                'Invalid identifier: "'.$instanceIdentifier.'"! Config could not be retrieved.'
            );
        }

        // return a nice prepared configuration for current instance
        return self::$ormConfig[$instanceIdentifier];
    }
}
