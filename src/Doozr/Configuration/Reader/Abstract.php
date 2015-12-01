<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Configuration - Reader - Abstract
 *
 * Abstract.php - The Abstract class for config reader. This class provides high level
 * access to filesystem and cache. Can be used for all types of readers.
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

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Class.php';

use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;
use Rhumsaa\Uuid\Uuid;

/**
 * Doozr - Configuration - Reader - Abstract
 *
 * The Abstract class for config reader. This class provides high level
 * access to filesystem and cache. Can be used for all types of readers.
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
abstract class Doozr_Configuration_Reader_Abstract extends Doozr_Base_Class
{
    /**
     * Instance of filesystem service.
     *
     * @var Doozr_Filesystem_Service
     * @access protected
     */
    protected $filesystem;

    /**
     * Instance of cache service.
     *
     * @var Doozr_Cache_Service
     * @access protected
     */
    protected $cacheService;

    /**
     * Filename of configuration we currently process
     *
     * @var string
     * @access protected
     */
    protected $filename;

    /**
     * The active cache state (enabled[true]/disabled[false]) as boolean.
     *
     * @var bool
     * @access protected
     */
    protected $cache = false;

    /**
     * The uuid of the active configuration
     *
     * @var string
     * @access protected
     */
    protected $uuid;

    /**
     * Include Directive {{include($filename)]]
     *
     * @var string
     * @access public
     */
    const DIRECTIVE_INCLUDE = 'include';

    /**
     * Require Directive {{include($filename)]]
     *
     * @var string
     * @access public
     */
    const DIRECTIVE_REQUIRE = 'require';

    /**
     * Constructor.
     *
     * @param Doozr_Base_Service_Interface $filesystem   Instance of filesystem service
     * @param Doozr_Cache_Service          $cacheService Instance of cache service
     * @param bool                         $enableCache  TRUE to enable caching, FALSE to disable
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \Doozr_Configuration_Reader_Abstract
     * @access public
     */
    public function __construct(
        Doozr_Base_Service_Interface $filesystem,
        Doozr_Cache_Service          $cacheService = null,
                                     $enableCache  = false
    ) {
        $this
            ->filesystem($filesystem)
            ->cacheService($cacheService)
            ->cache($enableCache);
    }

    /**
     * Reads a configuration.
     *
     * @param string $filename The filename of the configuration file to read (parse)
     *
     * @return \Doozr_Configuration_Reader_Abstract
     * @throws \Doozr_Cache_Service_Exception
     * @throws \Doozr_Configuration_Exception
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     */
    public function read($filename)
    {
        // Our identifier -> important :D
        $this->setUuid(md5($filename));

        // Is cache enabled?
        if (true === $this->getCache()) {
            try {
                $content = $this->getCacheService()->read($this->getUuid());

                // Check result from cache for NULL => timed out cache entry?!
                if ($content !== null && $content !== '') {
                    return $content;
                }

            } catch (Doozr_Cache_Service_Exception $exception) {
                // Intentionally left blank
            }
        }

        // Check first if we can handle the file (basic check - everything else done by filesystem service)
        if (
            false === $this->getFilesystem()->exists($filename) ||
            false === $this->getFilesystem()->readable($filename)
        ) {
            throw new Doozr_Configuration_Exception(
                sprintf('The file "%s" does either not exist or it is not readable.', $filename)
            );
        }

        // Store config filename
        $this->setFilename($filename);

        // Return the result of the operation ...
        $configuration = $this->process($filename, $this->getUuid());

        // Store content in cache
        if (true === $this->getCache()) {
            if (true !== $this->getCacheService()->update($this->getUuid(), $configuration)) {
                throw new Doozr_Cache_Service_Exception(
                    'Error storing configuration in cache!'
                );
            }
        }

        return $configuration;
    }

    /**
     * Process the filename and return content for it parsed and complete.
     *
     * @param string $filename The filename to process
     * @param string $uuid     The UUID of the file
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The content of the file
     * @access public
     * @throws Doozr_Configuration_Exception
     */
    protected function process($filename, $uuid)
    {
        $configuration = $this->readFile($filename, $uuid);

        // The directives to execute and the fail-on-error status (true|false)
        $directives = array(
            self::DIRECTIVE_INCLUDE => false,
            self::DIRECTIVE_REQUIRE => true,
        );

        foreach ($directives as $directive => $strict) {
            $extracted = $this->extractDirectives($directive, $configuration);

            foreach ($extracted[1] as $index => $include) {
                $includeFilename = realpath(dirname($filename) . DIRECTORY_SEPARATOR . $include);

                if ($includeFilename !== false) {
                    $content = $this->readFile($includeFilename);

                } elseif ($strict === false) {
                    $content = '{}';

                } else {
                    throw new Doozr_Configuration_Exception(
                        'The file "' . $filename . '" could not be included. Sure it exists?'
                    );
                }

                $configuration = str_replace(
                    '"' . $extracted[0][$index] . '"',
                    //$extracted[0][$index],
                    $content,
                    $configuration
                );
            }
        }

        /**
         *
         * @todo RETRIEVE DOOZR CONSTANTS SO EXPENSIVE?
         *
         */

        // After executing directive like function calls
        $variables = array(
            'DOOZR_APP_ROOT'       => str_replace('\\', '\\\\', DOOZR_APP_ROOT),
            'DOOZR_DOCUMENT_ROOT'  => str_replace('\\', '\\\\', DOOZR_DOCUMENT_ROOT),
            'DOOZR_DIRECTORY_TEMP'    => str_replace('\\', '\\\\', DOOZR_DIRECTORY_TEMP),
            'DOOZR_NAMESPACE_FLAT' => DOOZR_NAMESPACE_FLAT,
        );

        // Do default replacements
        foreach ($variables as $variable => $value) {
            $configuration = str_replace('{{' . $variable . '}}', $value, $configuration);
        }

        return $configuration;
    }

    /**
     * Returns the extracted directives of a passed string.
     *
     * @param string $directive to look for
     * @param string $content   to look in
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Of found directives or empty one if no matches
     * @access protected
     */
    protected function extractDirectives($directive, $content)
    {
        $pattern = '/{{' . $directive . '\(?([\w\.]*)\)?}}/';
        preg_match_all($pattern, $content, $result);
        return $result;
    }

    /**
     * Calculates a UUID for a passed string.
     *
     * @param string $input The input to calculate the UUID for.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The UUID
     * @access protected
     */
    protected function calculateUuid($input)
    {
        try {
            // Generate a version 5 (name-based and hashed with SHA1) UUID object
            $uuid5 = Uuid::uuid5(Uuid::NAMESPACE_DNS, $input);
            $uuid = $uuid5->toString();

        } catch (UnsatisfiedDependencyException $e) {
            $uuid = sha1($input);
        }

        return $uuid;
    }

    /**
     * Reads a configuration file.
     * When caching is enabled it will try to read the configuration from cache. If this fails
     * it will try to read from filesystem and stores it to cache afterwards.
     *
     * When caching is disabled it will always try to load from filesystem
     *
     * @param string $filename The filename
     * @param string $uuid     The UUID to read
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The content read
     * @access protected
     */
    protected function readFile($filename, $uuid = null)
    {
        // Check for cache enabled ...
        if ($this->getCache() === true) {
            if ($uuid === null) {
                $uuid = md5($filename);
            }

            try {
                $content = $this->getCacheService()->read($uuid);

                if ($content !== null && $content != "") {
                    return $content;
                }

            } catch (Doozr_Cache_Service_Exception $e) {
                // Intentionally omitted
            }
        }

        // Fetch content from filesystem
        $content = $this->getFilesystem()->read($filename);

        // Check if caching enabled and store read result to cache
        if ($this->getCache() === true) {
            $this->getCacheService()->create($uuid, $content);
        }

        return $content;
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
     * @return bool TRUE if cache is enabled, otherwise FALSE
     * @access protected
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Nice name alias to getCache()
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if cache is enabled, otherwise FALSE
     * @access protected
     */
    protected function cacheEnabled()
    {
        return $this->getCache();
    }

    /**
     * Setter for filename.
     *
     * @param string $filename The filename to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Fluent setter for filename.
     *
     * @param string $filename The filename to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance of this class for chaining (fluent interface pattern)
     * @access protected
     */
    protected function filename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Getter for filename.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Filename if set, otherwise NULL
     * @access protected
     */
    public function getClassFilename()
    {
        return $this->filename;
    }

    /**
     * Setter for filesystem service.
     *
     * @param Doozr_Filesystem_Service $filesystem
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setFilesystem(Doozr_Filesystem_Service $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Fluent setter for filesystem service.
     *
     * @param Doozr_Base_Service_Interface $filesystem
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance of this class for chaining (fluent interface pattern)
     * @access protected
     */
    protected function filesystem(Doozr_Base_Service_Interface $filesystem)
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    /**
     * Getter for filesystem service.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|Doozr_Filesystem_Service Instance of filesystem service if set, otherwise NULL
     * @access protected
     */
    protected function getFilesystem()
    {
        return $this->filesystem;
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
        $this->cacheService = $cacheService;

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
     * Each concrete class must implement some kind of validation of the loaded configuration.
     * It should ensure that the loaded content is valid for the format.
     *
     * @param string $input The input to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success, otherwise FALSE
     * @access protected
     * @abstract
     */
    abstract protected function validate($input);
}
