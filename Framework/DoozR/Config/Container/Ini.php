<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Config - Container - Ini
 *
 * Ini.php - Config container for managing INI-Type configurations
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
 * @package    DoozR_Config
 * @subpackage DoozR_Config_Container
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Config/Container/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Config/Container/Interface.php';
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Exception.php';

/**
 * DoozR - Config - Container - Ini
 *
 * Config container for managing INI-Type configurations
 *
 * @category   DoozR
 * @package    DoozR_Config
 * @subpackage DoozR_Config_Container
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @implements DoozR_Path,DoozR_Logger
 */
class DoozR_Config_Container_Ini extends DoozR_Config_Container_Abstract implements DoozR_Config_Container_Interface
{
    /**
     * contains an instance of filesystem module
     *
     * @var object
     * @access protected
     */
    protected $filesystem;

    /**
     * Controls caching
     * TRUE to enable caching, or FALSE to do disable
     *
     * @var bool
     * @access private
     * @static
     */
    private static $_cacheEnabled = false;


    /**
     * constructor
     *
     * This method is the constructor.
     *
     * @param object $path   An instance of DoozR_Path
     * @param object $logger An instance of DoozR_Logger
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __construct(DoozR_Path_Interface $path, DoozR_Logger_Interface $logger, $enableCaching = false)
    {
        // call parent constructor
        parent::__construct($path, $logger, $enableCaching);

        // get instance of filesystem module
        $this->filesystem = DoozR_Loader_Serviceloader::load('filesystem');
    }

    /**
     * creates a configuration
     *
     * This method is intend to create a configuration.
     *
     * @param string $resource The resource to create
     * @param mixed  $data     The data to write
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The content of the file
     * @access public
     */
    public function create($resource, $data = '')
    {
        return $this->filesystem->write($resource, $data);
    }

    /**
     * reads a configuration and optionally merge it into existing
     *
     * This method is intend to read a configuration and optionally merge it into an existing one.
     *
     * @param string  $resource The resource to read
     * @param bool $merge    TRUE to merge the read configuration into existing configuration
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function read($resource, $merge = true)
    {
        // get uid for requested resource
        $uid = $this->getUid($resource);

        // translate to cache-uid and set as active in cache module
        $cUid = $this->cache->generateId($uid);

        // check if current resource was cached before
        if ($this->cache->isCached()) {
            // get configuration from cache
            $cached  = $this->cache->read();
            $configuration = $cached[1];

        } else {
            // get configuration (INI)
            #$configuration = $this->_readConfigurationFile($resource);
            $configuration = file_get_contents($resource);

            // parse the configuration
            $configuration = $this->_parse(
                $configuration, true
            );

            // store to cache for next hit if enabled
            if (self::$_cacheEnabled === true) {
                $this->cache->write($configuration);
            }
        }

        // store information to prevent further unneeded FS-access' to check existance...
        $this->validResources[$uid] = true;

        // store configuration
        $this->setConfiguration($configuration, $merge);

        // success
        return $this;
    }

    /**
     * Updates configuration in a file
     *
     * This method is intend to update the configuration in a file.
     *
     * @param string $resource The resource to update
     * @param string $data     The updated data
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     * @see    DoozR_Config_Container_Interface::update()
     */
    public function update($resource, $data)
    {
        pre(__METHOD__.': Not implemented yet!');
    }

    /**
     * Deletes configuration resource
     *
     * This method is intend to delete a configuration resource.
     *
     * @param string $resource The resource to delete
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     * @see    DoozR_Config_Container_Interface::delete()
     */
    public function delete($resource)
    {
        return $this->filesystem->delete($resource);
    }

    /**
     * returns the ini-content (config-file) as (preprocessed) string
     *
     * This method is intend to return the ini-content (config-file) as (preprocessed) string.
     *
     * @param string $data The "raw" config-content (string of data) with already replaced default replacements
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    private function _parseIni($data)
    {
        // store result in
        $parsed = array();

        // split data into its single lines
        $lines = preg_split('/\r\n?|\r?\n/', $data);

        // iterate over lines
        foreach ($lines as $line) {
            // get first char of the current line
            $firstCharOfLine = substr($line, 0, 1);

            // jump if comment ; or #
            if ($firstCharOfLine !== ';' && $firstCharOfLine !== '#') {
                // parse line
                if (preg_match('/^\s*\[\s*(.*)\s*\]\s*$/', $line, $matches)) {
                    // section header
                    $currentSection = strtolower($matches[1]);

                } elseif (preg_match('/^([a-z0-9_.\[\]-]+)\s*=\s*(.*)$/i', $line, $matches)) {
                    // parse value
                    if (preg_match('/^"(?:\\.|[^"])*"|^\'(?:[^\']|\\.)*\'/', $matches[2], $value)) {
                        $value = substr($value[0], 1, -1);
                    } else {
                        $value = preg_replace('/^["\']|\s*;.*$/', '', $matches[2]);
                    }

                    $values = ($value) ? $this->_autoCast($value) : $matches[2];

                    if (!isset($parsed[$currentSection])) {
                        $parsed[$currentSection] = array();
                    }

                    $parsed[$currentSection][strtolower($matches[1])] = $value;
                }
            }
        }

        // return the parsed data
        return $parsed;
    }


    private function _autoCast($input)
    {
        // parse data types
        if (is_string($input)) {
            $input = (string)$input;

        } elseif (is_numeric($input)) {
            $input = (float)$input;

        } elseif (strtolower($input) == 'true') {
            $input = true;

        } elseif (strtolower($input) == 'false') {
            $input = false;

        } elseif (is_json($input)) {
            $input = json_decode($input);

        }

        // return casted
        return $input;
    }



    /**
     * returns parsed configuration
     *
     * This method is intend to return parsed configuration.
     *
     * @param string  $configuration    The raw configuration
     * @param bool $multiDimensional TRUE to parse ini file multidimensional, FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The array containing configuration
     * @access private
     */
    private function _parse($configuration, $multiDimensional = false)
    {
        $raw       = $configuration;
        $processed = $this->_parseIni($configuration, $multiDimensional);
        $parsed    = $this->_parseIni($this->doReplacement($configuration), $multiDimensional);

        $result = array(
            'raw'       => $raw,            // raw configuration in ASCII (format = JSON)
            'processed' => $processed,      // processed configuration object/array
            'parsed'    => $parsed          // processed configuration object/array and placeholder replaced
        );

        // return the combined data
        return $result;
    }

    /**
     * returns configuration from file
     *
     * This method is intend to return configuration from file.
     *
     * @param string $configurationFile The configuration file
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The content of the file
     * @access private
     * @throws DoozR_Config_Container_Exception
     */
    private function _readConfigurationFile($configurationFile)
    {

        if (!isset($this->validResources[$this->getUid($configurationFile)])
            && !$this->filesystem->exists($configurationFile)
        ) {
            throw new DoozR_Config_Container_Exception(
                'Reading configuration failed! Configuration file: "'.$configurationFile.'" does not exist.'
            );
        }

        $buffer = $this->filesystem->read($configurationFile);

        return $buffer;
    }
}
