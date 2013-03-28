<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Config - Container - Abstract
 *
 * Abstract.php - Abstract base for Config container usable by config manager
 * of the DoozR Framework (e.g. DoozR_Config).
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
 * @package    DoozR_Config
 * @subpackage DoozR_Config_Container
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Class/Singleton/Strict.php';

/**
 * DoozR - Config - Container - Abstract
 *
 * Abstract base for Config container usable by config manager
 * of the DoozR Framework (e.g. DoozR_Config).
 *
 * @category   DoozR
 * @package    DoozR_Config
 * @subpackage DoozR_Config_Container
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Config_Container_Abstract extends DoozR_Base_Class_Singleton_Strict
{
    /**
     * Contains the configuration
     *
     * @var array
     * @access protected
     */
    protected $configuration = array();

    /**
     * Contains the list of valid resources
     * to prevent further filesystem accesses
     *
     * @var array
     * @access protected
     */
    protected $validResources = array();

    /**
     * Contains the list replacements
     *
     * @var array
     * @access protected
     */
    protected $replacementMatrix = array();

    /**
     * Contains an instance of cache
     *
     * @var object
     * @access protected
     */
    protected $cache;

    /**
     * Contains an instance of DoozR_Path
     *
     * @var object
     * @access protected
     */
    protected $path;

    /**
     * Contains an instance of DoozR_Logger
     *
     * @var object
     * @access protected
     */
    protected $logger;

    /**
     * Contains the current part of the chain
     *
     * @var object
     * @access protected
     */
    protected $currentChainlink;

    /**
     * Marks a configuration as changed (dirty)
     * So it can be written to filesystem on __destruct
     *
     * @var boolean
     * @access protected
     */
    protected $dirty = false;


    /**
     * This method is the constructor of the class.
     *
     * @param DoozR_Path_Interface   $path          An instance of DoozR_Path
     * @param DoozR_Logger_Interface $logger        An instance of DoozR_Logger
     * @param boolean                $enableCaching TRUE to enable caching, otherwise FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     * @throws DoozR_Exception
     */
    protected function __construct(DoozR_Path_Interface $path, DoozR_Logger_Interface $logger, $enableCaching = false)
    {
        $this->path   = $path;
        $this->logger = $logger;
        $this->cache  = DoozR_Loader_Moduleloader::load('cache', array(DOOZR_UNIX));

        // try to use memcache as container
        try {
            $this->cache->setContainer('memcache');

        } catch (Exception $e) {
            // use file-container as fallback
            $this->cache->setContainerOptions(
                array(
                    'directory'      => $path->get('cache'),
                    'filenamePrefix' => 'cache_'
                )
            );

            // try to load file container
            try {
                $this->cache->setContainer('file');

            } catch (DoozR_Cache_Module_Exception $e) {
                throw new DoozR_Exception(
                    'Error while initializing cache! Neither file nor memcache container can be used.',
                    null,
                    $e
                );
            }
        }

        //
        $this->attachDefaultReplacements();
    }

    /*******************************************************************************************************************
     * // BEGIN OVERLOADABLE METHODS OF CONTAINER
     ******************************************************************************************************************/

    /**
     * attaches the default transformations
     *
     * This method is called from constructor to attach the default transformations
     * like DOCUMENT_ROOT or SERVERNAME (DOMAIN). The so called "transforms" are applied to each
     * part of the config (section or value) and replaces placeholder with runtime information(s).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function attachDefaultReplacements()
    {
        // get all constants
        $constants = get_defined_constants(true);

        // cut off from us defined ones
        $contants = $constants['user'];

        // add for replacement
        foreach ($contants as $constant => $value) {
            $this->attachReplacement('{'.$constant.'}', $value);
        }

        // server-name
        $this->attachReplacement(
            '{DOOZR_SERVERNAME}',
            (isset($_SERVER['SERVER_NAME'])) ? $_SERVER['SERVER_NAME'] : 'SERVER_NAME'
        );
    }

    /**
     * replaces the current defined replaces in a given mixed var
     *
     * This method is intend to replace the current defined replaces in a given mixed var
     *
     * @param mixed $configuration The content to replace the replacements in
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The content including the concrete replaces
     * @access protected
     * @throws  DoozR_Exception
     */
    protected function doReplacement($configuration)
    {
        // only string work for current versions
        if (!is_string($configuration)) {
            throw new DoozR_Exception(
                'Error while replacing placeholder in configuration. Replacement currently only works with strings ("'.
                gettype($configuration).'" was given).'
            );
        }

        foreach ($this->replacementMatrix as $search => $replace) {
            $configuration = str_replace($search, $replace, $configuration);
        }

        // return the replaced content
        return $configuration;
    }

    /**
     * Returns an unique Id for a given resource
     *
     * This method is intend to generate and return an unique Id for a given resource.
     *
     * @param mixed $resource Any type of variable
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The generated unique Id
     * @access public
     */
    public function getUid($resource)
    {
        return md5(serialize($resource));
    }

    /**
     * Stores the raw-configuration of the current instance
     *
     * This method is intend to store the raw configuration of the current instance.
     *
     * @param mixed $configuration The configuration to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setRaw($configuration)
    {
        $this->configuration['raw'] = $configuration;
    }

    /**
     * Returns the raw-configuration of the current instance
     *
     * This method is intend to store the raw configuration of the current instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The raw configuration of the current instance
     * @access public
     */
    public function getRaw()
    {
        return $this->configuration['raw'];
    }

    /**
     * Stores the processed-configuration of the current instance
     *
     * This method is intend to store the processed configuration of the current instance.
     *
     * @param mixed $configuration The configuration to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setProcessed($configuration)
    {
        $this->configuration['processed'] = $configuration;
    }

    /**
     * Returns the processed-configuration of the current instance
     *
     * This method is intend to store the processed configuration of the current instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The processed configuration of the current instance
     * @access public
     */
    public function getProcessed()
    {
        return $this->configuration['processed'];
    }

    /**
     * Stores the parsed-configuration of the current instance
     *
     * This method is intend to store the parsed configuration of the current instance.
     *
     * @param mixed $configuration The configuration to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setParsed($configuration)
    {
        $this->configuration['parsed'] = $configuration;
    }

    /**
     * Returns the processed-configuration of the current instance
     *
     * This method is intend to store the processed configuration of the current instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The processed configuration of the current instance
     * @access public
     */
    public function getParsed()
    {
        return $this->configuration['parsed'];
    }

    /**
     * Merges two configuration objects
     *
     * This method is intend to merge to objects of configurations recursive to a new one.
     * Where new keys are created in configuration-1 and existing values! are overwritten
     * by values of configuration-2 (smart override).
     *
     * @param mixed $configuration1 The configuration to merge the second configuration in
     * @param mixed $configuration2 The configuration to merge into the first one
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object The resulting + merged configuration object
     * @access public
     */
    public function mergeConfigurations($configuration1, $configuration2)
    {
        $a1 = object_to_array($configuration1);
        $a2 = object_to_array($configuration2);
        $r1 = array_replace_recursive($a1, $a2);
        $r2 = array_to_object($r1);

        // merge configurations and return result as object
        return $r2;
    }

    /*******************************************************************************************************************
     * \\ END OVERLOADABLE METHODS OF CONTAINER
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN PUBLIC API
     ******************************************************************************************************************/

    /**
     * attaches a concrete transform (replace X with Y || from => to)
     *
     * This method is used to attach a transformation (replacement) for a given string
     *
     * @param string $from The value which should be replaced
     * @param string $to   The value which should be inserted for every occurance of $from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function attachReplacement($from, $to)
    {
        $this->replacementMatrix[$from] = addcslashes($to, '\\');
    }

    /**
     * Stores a complete configuration
     *
     * This method is intend to store a complete configuration array with all it parts (e.g.
     * raw, processed, parsed).
     *
     * @param array   $configuration The configuration array to store
     * @param boolean $merge         TRUE if configuration should be merged with existing
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setConfiguration(array $configuration, $merge)
    {
        if (empty($this->configuration) || !$merge) {
            // simple store
            $this->configuration = $configuration;
        } else {
            // merge (currently only the parsed get merged!
            $this->configuration['parsed'] = $this->mergeConfigurations(
                $this->configuration['parsed'],
                $configuration['parsed']
            );
        }
    }

    /**
     * Returns complete configuration or a part of it
     *
     * This method is intend to return either the full configuration or parts of it.
     *
     * @param array $part The configuration array to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Complete configuration or value of a part
     * @access public
     */
    public function getConfiguration($part = null)
    {
        if ($part) {
            $data = (isset($this->configuration[$part])) ? $this->configuration[$part] : null;
        } else {
            $data = $this->configuration;
        }

        if ($data && is_array($data)) {
            $data = array_to_object($data);
        }

        return $data;
    }

    /*******************************************************************************************************************
     * \\ END PUBLIC API
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN CHAINING SUPPORT FOR READING CONFIG
     ******************************************************************************************************************/

    /**
     * Returns a node/value from config
     *
     * This method is intend to return a node/value from config. The magic-method __get
     * is used for generic chaining and returning values.
     *
     * @param string $node The node to return
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Requested node/value
     * @access public
     */
    public function __get($node)
    {
        // first of all get an active chain
        if (!$this->currentChainlink) {
            $this->currentChainlink = $this->getConfiguration('parsed');
        }

        // check if the node exists
        if (!isset($this->currentChainlink->{$node})) {
            // throw exception
            throw new DoozR_Config_Container_Exception('Config entry "'.$node.'" does not exist!');
        }

        // retrieve next requested chain relative to base
        $this->currentChainlink = $this->currentChainlink->{$node};

        // return active instance for chaining ...
        if (is_object($this->currentChainlink)) {
            return $this;
        } else {
            // or value if no more chaining possible + reset chain for following calls
            $value = $this->currentChainlink;

            // reset
            $this->currentChainlink = null;

            // return
            return $value;
        }
    }

    /**
     * Returns a node/value from config
     *
     * This method is intend to return a node/value from config. The magic-method __call
     * is used for generic chaining and returning values.
     *
     * @param string $node          The node to return
     * @param array  $returnAsArray TRUE to return node/value as array, otherwise FALSE to return object (default)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Requested node/value
     * @access public
     */
    public function __call($node, $returnAsArray)
    {
        // get active chain
        if (!$this->currentChainlink) {
            $this->currentChainlink = $this->getConfiguration('parsed');
        }

        // check if the node exists
        if (!isset($this->currentChainlink->{$node})) {
            // throw exception
            throw new DoozR_Config_Container_Exception('Config entry "'.$node.'" does not exist!');
        }

        // get correct transformed argument
        if (count($returnAsArray)) {
            $returnAsArray = $returnAsArray[0];
        } else {
            $returnAsArray = false;
        }

        // return as array?
        if ($returnAsArray) {
            $result = object_to_array($this->currentChainlink->{$node});
        } else {
            $result = $this->currentChainlink->{$node};
        }

        // reset after __call()
        $this->currentChainlink = null;

        // return the result
        return $result;
    }

    /*******************************************************************************************************************
     * \\ END CHAINING SUPPORT FOR READING CONFIG
     ******************************************************************************************************************/
}

?>
