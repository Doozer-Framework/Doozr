<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Configuration
 *
 * Configuration.php - Configuration container for a Json reader (based on filesystem reader) to read Json
 * configurations and make use of three possible layers of caching [REQUEST -> [CACHE:RUNTIME] -> [CACHE:CONFIG]
 * -> [CACHE:FILESYSTEM] -> read from filesystem/network. So lookup is (look in runtime cache) then (look in
 * config stored in memory) then (look in memory for file) then access filesystem/network the 1st time. Speedup!!!
 *
 * PHP versions 5.5
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
 * @package    Doozr_Kernel
 * @subpackage Doozr_Kernel_Configuration
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Class/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Configuration/Interface.php';

/**
 * Doozr - Configuration
 *
 * Configuration container for a Json reader (based on filesystem reader) to read Json configurations and
 * make use of three possible layers of caching [REQUEST -> [CACHE:RUNTIME] -> [CACHE:CONFIG] -> [CACHE:FILESYSTEM] ->
 * read from filesystem/network.
 * So lookup is (look in runtime cache) then (look in config stored in memory) then (look in memory for file) then
 * access filesystem/network the 1st time. Speedup!!!
 *
 * @category   Doozr
 * @package    Doozr_Kernel
 * @subpackage Doozr_Kernel_Configuration
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 * @property   Doozr_Base_Configuration_Hierarchy_Kernel $kernel
 * @property   Doozr_Base_Configuration_Hierarchy_I18n $i18n
 * @property   Doozr_Base_Configuration_Hierarchy_Session $session
 */
class Doozr_Configuration extends Doozr_Base_Class_Singleton
    implements
    Doozr_Configuration_Interface
{
    /**
     * The UUID of the active configuration
     *
     * @var string
     * @access protected
     */
    protected $uuid = '';

    /**
     * The cache status.
     *
     * @var bool
     * @access protected
     */
    protected $cache;

    /**
     * Namespace for cache e.g.
     *
     * @var string
     * @access protected
     */
    protected $namespace;

    /**
     * The dirty flag. Indicator for this instance to know
     * when it is time to start merging and combining UUIDs.
     *
     * @var bool
     * @access protected
     */
    protected $dirty = false;

    /**
     * Instance of cache service.
     *
     * @var Doozr_Cache_Service
     * @access protected
     */
    protected $cacheService;

    /**
     * A config reader instance. In this case (Doozr uses JSON):
     *
     * @var Doozr_Configuration_Reader_Interface
     * @access protected
     */
    protected $configReader;

    /**
     * The merged configuration required for returning content
     *
     * @var \stdClass
     * @access protected
     */
    protected $configuration;

    /**
     * The configuration required for returning content
     *
     * @var Doozr_Configuration_Reader_Interface[]
     * @access protected
     */
    protected $configurations;


    /**
     * Constructor.
     *
     * @param Doozr_Configuration_Reader_Interface $configReader A container e.g. Json or Ini
     * @param Doozr_Cache_Service                  $cacheService Doozr_Cache_Service Instance
     * @param bool                                 $cache        TRUE to enable caching, FALSE to do disable
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \Doozr_Configuration
     * @access protected
     */
    protected function __construct(
        Doozr_Configuration_Reader_Interface $configReader,
        Doozr_Cache_Service                  $cacheService = null,
                                             $cache        = false
    ) {
        $this
            ->configReader($configReader)
            ->cacheService($cacheService)
            ->configuration(new \stdClass())
            ->namespace_(DOOZR_NAMESPACE_FLAT . '.cache.configuration')
            ->cache($cache);
    }

    /**
     * Reads a configuration by using the injected Doozr_Configuration_Reader_Interface.
     * The result of the call will be merged with previously loaded configurations
     * and it will be cached.
     *
     * @param string $filename The filename to parse
     *
     * @return Doozr_Configuration_Reader_Interface|mixed|null|stdClass
     * @throws Doozr_Cache_Service_Exception
     * @throws Doozr_Configuration_Reader_Exception
     */
    public function read($filename)
    {
        // Create UUID in a generic way
        $this->setUuid(md5($this->getUuid() . $filename));

        // Get all loaded configurations
        $configurations = $this->getConfigurations();

        // Now check if the configuration is in runtime cache
        if (isset($configurations[$this->getUuid()]) === false) {

            // Otherwise look for cached version?
            if (true === $this->getCache()) {

                try {
                    $configuration = $this->getCacheService()->read($this->getUuid(), $this->getNamespace());

                    // Check returned value => NULL = possible timed out cached entry ...
                    if ($configuration !== null) {

                        $configurations[$this->getUuid()] = $configuration;
                        $this->setConfiguration($configuration);
                        $this->setConfigurations($configurations);

                        return $configuration;
                    }

                } catch (Doozr_Cache_Service_Exception $e) {
                    //
                }
            }

            // If not cached we need to clone a config parser and begin parsing and merging
            $configuration = clone $this->getConfigReader();
            $configuration->read($filename);
            $configurations[$this->getUuid()] = $configuration->get();

            // Merge with master
            $configuration = $this->merge(
                $this->getConfiguration(),
                $configuration->get()
            );

            // Store merge result
            if ($this->getCache() === true) {
                $this->getCacheService()->create($this->getUuid(), $configuration, null, $this->getNamespace());
            }

            // Store configurations
            $this->setConfiguration($configuration);
            $this->setConfigurations($configurations);

        } else {
            return $configurations[$this->getUuid()];
        }
    }

    /**
     * Setter for key => value pairs of config.
     * This method does not persist!
     *
     * @param string $node  The key used for entry
     * @param mixed  $value The value (every type allow) be sure to check if it is supported by your chosen config type
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function set($node, $value)
    {
        //return $this->getDecoratedObject()->{$node} = $value;
    }

    /**
     * Getter for value of passed node.
     *
     * @param string $node The key used for value lookup.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed|null The value of configuration node if set, otherwise NULL
     * @access public
     */
    public function get($node = null)
    {
        if ($node !== null) {
            $nodes = explode(':', $node);
            $configuration = $this->getConfiguration();
            foreach ($nodes as $node) {
                $configuration = $configuration->{$node};
            }

        } else {
            $configuration = $this->getConfiguration();
        }

        return $configuration;
    }

    /**
     * Generic getter to provide a Doozr_Configuration_Reader_Interface like interface to master configuration
     * e.g. Doozr_Configuration->foo->bar;
     *
     * @param string $property The property requested
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed|null The value of the node requested, otherwise NULL
     * @access public
     */
    public function __get($property)
    {
        return $this->get($property);
    }

    /**
     * Setter for uuid.
     *
     * @param string $uuid The uuid to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Fluent setter for uuid.
     *
     * @param string $uuid The uuid to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance of this class for chaining (fluent interface pattern)
     * @access protected
     */
    protected function uuid($uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * Getter for uuid.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Uuid if set, otherwise NULL
     * @access protected
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Setter for namespace.
     *
     * @param bool $namespace The namespace to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Fluent setter for namespace.
     *
     * @param bool $namespace The namespace to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance of this class for chaining (fluent interface pattern)
     * @access protected
     */
    protected function namespace_($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * Getter for namespace.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Namespace if set, otherwise NULL
     * @access protected
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Setter for cache.
     *
     * @param bool $cache The cache to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * Fluent setter for cache.
     *
     * @param bool $cache The cache to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance of this class for chaining (fluent interface pattern)
     * @access protected
     */
    protected function cache($cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * Getter for cache.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean Cache if set, otherwise NULL
     * @access protected
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Setter for cache service.
     *
     * @param Doozr_Cache_Service $cacheService
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setCacheService(Doozr_Cache_Service $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Fluent setter for cache service.
     *
     * @param null|Doozr_Cache_Service $cacheService
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance of this class for chaining (fluent interface pattern)
     * @access protected
     */
    protected function cacheService(Doozr_Cache_Service $cacheService)
    {
        $this->setCacheService($cacheService);
        return $this;
    }

    /**
     * Getter for cache service.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|Doozr_Cache_Service Instance of cache service if set, otherwise NULL
     * @access protected
     */
    protected function getCacheService()
    {
        return $this->cacheService;
    }

    /**
     * Setter for configReader.
     *
     * @param Doozr_Configuration_Reader_Interface $configReader
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setConfigReader(Doozr_Configuration_Reader_Interface $configReader)
    {
        $this->configReader = $configReader;
    }

    /**
     * Fluent setter for config reader.
     *
     * @param Doozr_Configuration_Reader_Interface $configReader
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance of this class for chaining (fluent interface pattern)
     * @access protected
     */
    protected function configReader(Doozr_Configuration_Reader_Interface $configReader)
    {
        $this->configReader = $configReader;
        return $this;
    }

    /**
     * Getter for configReader.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Configuration_Reader_Interface Instance of a config reader if set, otherwise NULL
     * @access protected
     */
    protected function getConfigReader()
    {
        return $this->configReader;
    }

    /**
     * Setter for configuration.
     *
     * @param \stdClass $configuration
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setConfiguration(\stdClass $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Setter for configuration.
     *
     * @param \stdClass $configuration
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function configuration(\stdClass $configuration)
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * Getter for configuration.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return stdClass The configuration if set, otherwise NULL
     * @access protected
     */
    protected function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Setter for configuration.
     *
     * @param array $configurations The configurations to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setConfigurations(array $configurations)
    {
        $this->configurations = $configurations;
    }

    /**
     * Setter for configuration.
     *
     * @param array $configurations The configurations to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining.
     * @access protected
     */
    protected function configurations(array $configurations)
    {
        $this->configurations = $configurations;
        return $this;
    }

    /**
     * Getter for all parsed/processed configurations.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Configuration_Reader_Interface[]
     * @access protected
     */
    protected function getConfigurations()
    {
        return $this->configurations;
    }

    /**
     * Merges two configurations of type \stdClass.
     *
     * @param stdClass $object1 The master configuration
     * @param stdClass $object2 The configuration to merge with master
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \stdClass The merged result
     * @access protected
     */
    protected function merge(\stdClass $object1, \stdClass $object2)
    {
        return array_to_object(
            array_replace_recursive(object_to_array($object1) , object_to_array($object2))
        );
    }
}
