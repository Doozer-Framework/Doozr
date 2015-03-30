<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Base - Database - Abstract
 *
 * Abstract.php - Abstract base class for building a Database Abstraction Layer for the DoozR Framework
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
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
 * Please feel free to contact us via e-mail: <opensource@clickalicious.de>
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Database
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

/**
 * DoozR - Base - Database - Abstract
 *
 * Base/master-abstract-class for building a Database
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Database
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @abstract
 */
abstract class DoozR_Base_Database_Facade_Abstract extends DoozR_Base_Class
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
    protected static $ormDirectory = array();

    /**
     * stored instance identifier used for config
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
     * Instance of DoozR_Config
     *
     * @var DoozR_Config
     * @access protected
     */
    protected $config;

    /**
     * Instance of DoozR_Logger
     *
     * @var DoozR_Logger
     * @access protected
     */
    protected $logger;


    /**
     * constructs the class
     *
     * constructor builds the class
     *
     * @param DoozR_Path   $path   Instance of DoozR_Path manager for path'
     * @param DoozR_Config $config Instance of DoozR_Config holding DoozR's config
     * @param DoozR_Logger $logger Instance of DoozR_Logger
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __construct(DoozR_Path $path, DoozR_Config $config, DoozR_Logger $logger)
    {
        // store instances
        $this->path   = $path;
        $this->config = $config;
        $this->logger = $logger;

        // call parent's constructor
        parent::__construct();
    }

    /**
     * setter for current used config-identifier
     *
     * This method is intend as setter for current used config-identifier.
     *
     * @param string $identifier The identifier to use for this instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function setInstanceIdentifier($identifier)
    {
        return ($this->instanceIdentifier = $identifier);
    }

    /**
     * getter for current used config-identifier
     *
     * This method is intend as getter for current used config-identifier.
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
     * @throws DoozR_Exception
     */
    public function dispatch(&$referenceWrapper = null)
    {
        throw new DoozR_Exception(
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
     * @return boolean True if file exists AND readable AND include succeeded
     * @access protected
     * @throws DoozR_Exception
     */
    protected function loadFile($filename)
    {
        // check if file exists
        if (!file_exists($filename)) {
            throw new DoozR_Exception(
                'Error file: "'.$filename.'" not found! ORM could not be initialized.'
            );
        }

        // check if file is readable
        if (!is_readable($filename)) {
            throw new DoozR_Exception(
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
        return str_replace('DoozR_Model_', '', str_replace('_Facade', '', get_class($this)));
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
            $directory  = constant(get_class($this).'::ORM_DIRECTORY');

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
     * retrieves the configuration for ORM/DBA via DoozR_Core::config()
     *
     * This method is intend to retrieve the configuration for ORM/DBA via DoozR_Core::config().
     *
     * @param string $orm The name of the ORM to retrieve config for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Config retrieved by DoozR_Core::config()
     * @access protected
     * @throws DoozR_Exception
     */
    protected function retrieveOrmConfig($orm)
    {
        // try to load configuration of orm
        try {
            // retrieve config for ORM/DBA
            self::$ormConfig = $this->config->database->{$orm};

        } catch(DoozR_Config_Ini_Exception $e) {

            throw new DoozR_Exception(
                'Configuration for ORM: "'.$orm.'" could not be retrieved! Please check your configuration',
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
     * @param mixed $instanceIdentifier The identifier to use for config-retrieval, NULL to return the whole config
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array $ormConfig The config for the ORM/DBA
     * @access public
     * @static
     * @throws DoozR_Exception
     */
    protected function getConfig($instanceIdentifier = null)
    {
        // correct format uppercase
        $instanceIdentifier = strtoupper($instanceIdentifier);

        // check if identifier exist
        if (!isset(self::$ormConfig[$instanceIdentifier])) {
            throw new DoozR_Exception(
                'Invalid identifier: "'.$instanceIdentifier.'"! Config could not be retrieved.'
            );
        }

        // return a nice prepared configuration for current instance
        return self::$ormConfig[$instanceIdentifier];
    }
}
