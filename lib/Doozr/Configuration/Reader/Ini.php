<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Configuration - Reader - Ini
 *
 * Ini.php - Configuration reader for reading JSON configurations and represent
 * them in an object oriented way. The JSON format is extended and we say
 * JSON+ to it. This class also provides caching of contents through a cache
 * service instance (this can be either memcache, filesystem ...).
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
 * @package    Doozr_Configuration
 * @subpackage Doozr_Configuration_Reader
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Configuration/Reader/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Configuration/Interface.php';

/**
 * Doozr - Configuration - Reader - Ini
 *
 * Configuration reader for reading JSON configurations and represent
 * them in an object oriented way. The JSON format is extended and we say
 * JSON+ to it. This class also provides caching of contents through a cache
 * service instance (this can be either memcache, filesystem ...).
 *
 * @category   Doozr
 * @package    Doozr_Configuration
 * @subpackage Doozr_Configuration_Reader
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Configuration_Reader_Ini extends Doozr_Configuration_Reader_Abstract
    implements
    Doozr_Configuration_Interface
{
    /**
     * The decoded content.
     *
     * @var \stdClass
     * @access protected
     */
    protected $decodedContent;

    /**
     * Controls whether the INI sections should be parsed or not.
     * If set to TRUE the result of parsing will be a multidimensional array,
     * otherwise a flat structure is returned.
     *
     * @var bool
     * @access public
     */
    const PHP_INI_PARSER_PROCESS_SECTIONS = true;


    /**
     * Here we proxy the call to parents generic reading and replacing functionality.
     * We do this to receive the processed result here in place to convert it to a real
     * object which is served as fluent API.
     *
     * @param string $filename The filename to read.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \stdClass The configuration as object representation
     * @access public
     * @throws Doozr_Configuration_Reader_Exception
     */
    public function read($filename)
    {
        // Our identifier -> important :D
        $this->setUuid(md5($filename));

        $configuration = null;

        // Is cache enabled?
        if (true === $this->cacheEnabled()) {
            try {
                $configuration = $this->getCacheService()->read($this->getUuid());
            } catch (Doozr_Cache_Service_Exception $exception) {
                // Intentionally left blank
            }
        }

        // Either not cached or cache disabled ...
        if (null === $configuration) {
            // we read the content in same way as before ...
            $configuration = parent::read($filename);
        }

        // but we need to validate here cause our domain
        $configuration = $this->validate($configuration);

        // Error handling
        if ($configuration === false) {
            throw new Doozr_Configuration_Reader_Exception(
                'Configuration could no be parsed. Ensure its valid.'
                /* @todo Ensure Lint on error with exception = details! */
            );
        }

        $this->setDecodedContent($configuration);

        // Cache!
        if ($this->getCache() === true) {
            $this->getCacheService()->update($this->getUuid(), $configuration);
        }

        return $configuration;
    }

    /**
     * Returns the configuration as array.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array|null The configuration as array if set, otherwise NULL
     * @access public
     * @throws Doozr_Configuration_Reader_Exception
     */
    public function getAsArray()
    {
        if ($result = $this->getDecodedContent() === null) {
            throw new Doozr_Configuration_Reader_Exception(
                'Please read() a configuration file before you try to access it as array.'
            );
        }

        if ($result !== null) {
            $result = object_to_array($result);
        }

        return $result;
    }

    public function set($node, $value = null)
    {
        echo 13;
        die;

        if ($node !== null) {
            $nodes = explode(':', $node);
            $configuration = $this->getDecodedContent();

            foreach ($nodes as $node) {
                try {
                    $configuration &= $configuration->{$node};

                } catch (Doozr_Error_Exception $e) {
                    throw new Doozr_Configuration_Reader_Exception(
                        'Configuration does not have a property: "' . $node . '" in configuration.'
                    );
                }
            }

            $configuration = $value;
            $this->setDecodedContent($configuration);

            return true;
        }

        return false;
    }

    /**
     * Returns the decoded JSON config content as whole or for a passed node.
     *
     * @param string|null $node The node to return
     *
     * @return \stdClass The config as stdClass
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result of the request
     * @access public
     * @throws Doozr_Configuration_Reader_Exception
     */
    public function get($node = null)
    {
        if ($node !== null) {
            $nodes         = explode(':', $node);
            $configuration = $this->getDecodedContent();

            foreach ($nodes as $node) {
                if (false === isset($configuration->{$node})) {
                    throw new Doozr_Configuration_Reader_Exception(
                        sprintf('Configuration does not have a property: "%s" in configuration.', $node)
                    );
                }

                $configuration = $configuration->{$node};
            }

            return $configuration;
        }

        return $this->getDecodedContent();
    }

    /**
     * Magic method for provide access to content of this class via a
     * fluent API. Like $config->foo->bar ...
     *
     * @param string $property The property to read from config
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return stdClass The result of the request
     * @access public
     */
    public function __get($property)
    {
        return $this->get($property);
    }

    /**
     * Validates that a passed string is valid ini
     *
     * @param string $input           The input to validate
     * @param bool   $processSections TRUE to process sections, FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return stdClass FALSE on error, STRING with result on success
     * @access protected
     */
    protected function validate($input, $processSections = self::PHP_INI_PARSER_PROCESS_SECTIONS)
    {
        if (true === is_string($input)) {
            $input = @parse_ini_string($input, $processSections);
            // Convert our associative array to an object
            $input = array_to_object(
                array_change_key_case_recursive($input)
            );
        }

        return $input;
    }

    /**
     * Setter for decoded content.
     *
     * @param \stdClass $content The configuration to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setDecodedContent(\stdClass $content)
    {
        $this->decodedContent = $content;
    }

    /**
     * Setter for decoded content.
     *
     * @param \stdClass $content The configuration to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function decodedContent(\stdClass $content)
    {
        $this->setDecodedContent($content);
        return $this;
    }

    /**
     * Getter for decoded content.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return stdClass The configuration if set, otherwise NULL
     * @access protected
     */
    protected function getDecodedContent()
    {
        return $this->decodedContent;
    }
}
