<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - I18n - Service - Interface - Abstract.
 *
 * Abstract.php - I18n Translation Abstract Interface
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
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Class/Singleton.php';

/**
 * Doozr - I18n - Service - Interface - Abstract.
 *
 * I18n Translation Abstract Interface
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
abstract class Doozr_I18n_Service_Interface_Abstract extends Doozr_Base_Class_Singleton
{
    /**
     * Translation table collection (for all locale!)
     * key = crc of locale + namespace(s).
     *
     * @var array
     * @static
     */
    protected static $translationTables = [];

    /**
     * Translations.
     *
     * @var array
     * @static
     */
    protected static $translations = [];

    /**
     * Cache service instance for caching.
     *
     * @var Doozr_Cache_Service
     * @static
     */
    protected static $cache;

    /**
     * State of caching. Can be either enabled = TRUE or disabled = FALSE
     * This is required to control cache behavior as override also if a Doozr_Cache_Service is available.
     * If set to false then no matter if an instance exists -> this Interface will never cache!!!
     * Defaults to TRUE cause caching is a good thing. But for example disabled when using gettext™
     * interface - gettext using its very own caching mechanism.
     *
     * @var bool
     */
    protected $cacheEnabled = false;

    /**
     * The lifetime for cache elements.
     *
     * @var int
     */
    protected $cacheLifetime = 3600;

    /**
     * The encoding the instance running in.
     *
     * @var string
     */
    protected $encoding;

    /**
     * Path to locale files (filesystem).
     *
     * @var string
     */
    protected $path;

    /*------------------------------------------------------------------------------------------------------------------
     | MAIN CONTROL METHODS (CONSTRUCTOR AND INIT)
     +----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param \stdClass $config The config for this type of interface
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function __construct(\stdClass $config)
    {
        // Check if requirements fulfilled
        self::checkRequirements();

        // Store the path to translation files
        $this
            ->path($config->path)
            ->cacheEnabled($this->getCacheEnabled() && $config->cache->enabled)
            ->cacheLifetime($config->cache->lifetime)
            ->encoding($config->encoding);

        // If cache enabled - get module cache and setup
        if (!self::$cache && true === $this->getCacheEnabled()) {
            if (true === isset($config->cache->container)) {
                $container = $config->cache->container;
            } else {
                $container = DOOZR_CACHING_CONTAINER;
            }

            if (false === isset($config->cache->namespace)) {
                $namespace = 'doozr.cache.i18n';
            } else {
                $namespace = $config->cache->namespace;
            }

            // Get cache service
            self::$cache = Doozr_Loader_Serviceloader::load(
                'cache',
                $container,
                $namespace,
                [],
                DOOZR_UNIX,
                DOOZR_CACHING
            );
        }
    }

    /*------------------------------------------------------------------------------------------------------------------
     | PUBLIC API
     +----------------------------------------------------------------------------------------------------------------*/

    /**
     * starts the initializing of given locale and namespace.
     *
     * This method is intend to start the initializing of given locale and namespace and return the corresponding key.
     *
     * @param string $locale     The locale to init
     * @param array  $namespaces The namespace to init
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The key (identifier) for the created translationtable
     */
    public function initLocaleNamespace($locale, array $namespaces)
    {
        // get checksum for translation-table + namespaces(s)
        $crc = hash('md4', $locale.serialize($namespaces));

        // Assume cached content does not exist
        $translationTable = false;

        // If we want to initialize the translation table we need at least support for it ...
        if (true  === $this->hasTranslationTableSupport($this)) {

            // We only run the generating code if either: a) never generated before or b) if caching is disabled
            if (false === isset(self::$translationTables[$crc]) || false === $this->getCacheEnabled()) {

                // If caching enabled => try to get table from cache
                if (true === $this->getCacheEnabled()) {
                    try {
                        $translationTable = self::$cache->read($crc);
                    } catch (Doozr_Cache_Service_Exception $e) {
                        // Intentionally left empty
                    }
                }

                // If we did not receive a valid result from cache or if cache is disabled => parse file(s) for table(s)
                if (false === $translationTable) {
                    // Build translationtable
                    $translationTable = $this->buildTranslationtable($locale, $namespaces);
                }

                // Store in cache if caching enabled
                if (true === $this->getCacheEnabled()) {
                    // cache translationtable
                    self::$cache->create($crc, $translationTable, $this->getCacheLifetime());
                }

                // Put into local translationtable (runtime cache!)
                self::$translationTables[$crc] = $translationTable;
            }
        }

        // In all cases return the crc (key/identifier)
        return $crc;
    }

    /*------------------------------------------------------------------------------------------------------------------
     | TOOLS & HELPER
     +----------------------------------------------------------------------------------------------------------------*/

    /**
     * Normalizes a locale to a full qualified locale. For example: If u would pass "de" into it everybody
     * would know - a yeah it's "de_DE" and exactly this is done here.
     *
     * @param string $locale The locale to normalize
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The normalized locale
     */
    protected function normalizeLocale($locale)
    {
        $locale = explode('-', $locale);

        foreach ($locale as $key => &$part) {
            if ($key > 0) {
                $part = strtoupper($part);
            }
        }

        return implode('_', $locale);
    }

    /**
     * Dummy implementation so we can call the check always no matter which constructor is chained.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if requirements fulfilled, otherwise FALSE
     */
    protected static function checkRequirements()
    {
        return true;
    }

    /**
     * Setter for $translationTables.
     *
     * @param array $translationTables The translationTables to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setTranslationTables(array $translationTables = [])
    {
        self::$translationTables = $translationTables;
    }

    /**
     * Fluent setter for $translationTables.
     *
     * @param array $translationTables The translationTables to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function translationTables(array $translationTables = [])
    {
        $this->setTranslationTables($translationTables);

        return $this;
    }

    /**
     * Returns the active translationTables of the translator instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The active translationTables if set, otherwise NULL
     */
    protected function getTranslationTables()
    {
        return self::$translationTables;
    }

    /**
     * Adds a single translationTable to collection of translationTables.
     *
     * @param string $key   The key/index to use
     * @param mixed  $value The value to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function addTranslationTable($key, $value)
    {
        self::$translationTables[$key] = $value;
    }

    /**
     * Adds a single translationTable to collection of translationTables.
     *
     * @param string $key The key/index to remove
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function removeTranslationTable($key)
    {
        if (true === isset(self::$translationTables[$key])) {
            unset(self::$translationTables[$key]);
        }
    }

    /**
     * Setter for $translations.
     *
     * @param array $translations The translations to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setTranslations(array $translations = [])
    {
        self::$translations = $translations;
    }

    /**
     * Fluent setter for $translations.
     *
     * @param array $translations The translations to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function translations(array $translations = [])
    {
        $this->setTranslations($translations);

        return $this;
    }

    /**
     * Returns the active translations of the translator instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The active translations if set, otherwise NULL
     */
    protected function getTranslations()
    {
        return self::$translations;
    }

    /**
     * Adds a single translation to collection of translations.
     *
     * @param string $key   The key/index to use
     * @param mixed  $value The value to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function addTranslation($key, $value)
    {
        self::$translations[$key] = $value;
    }

    /**
     * Adds a single translation to collection of translations.
     *
     * @param string $key The key/index to remove
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function removeTranslation($key)
    {
        if (true === isset(self::$translations[$key])) {
            unset(self::$translations[$key]);
        }
    }

    /**
     * Setter for $cache.
     *
     * @param Doozr_Cache_Service $cache The cache to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setCache(Doozr_Cache_Service $cache)
    {
        self::$cache = $cache;
    }

    /**
     * Fluent setter for $cache.
     *
     * @param Doozr_Cache_Service $cache The cache to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function cache(Doozr_Cache_Service $cache)
    {
        $this->setCache($cache);

        return $this;
    }

    /**
     * Returns the active cache of the translator instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|Doozr_Cache_Service The active cache if set, otherwise NULL
     */
    protected function getCache()
    {
        return self::$cache;
    }

    /**
     * Setter for $cacheEnabled.
     *
     * @param bool $cacheEnabled The cacheEnabled state to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setCacheEnabled($cacheEnabled)
    {
        $this->cacheEnabled = $cacheEnabled;
    }

    /**
     * Fluent setter for $cacheEnabled.
     *
     * @param bool $cacheEnabled The cacheEnabled state to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function cacheEnabled($cacheEnabled)
    {
        $this->setCacheEnabled($cacheEnabled);

        return $this;
    }

    /**
     * Returns the active cacheEnabled of the translator instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|bool State of cacheEnabled if set, otherwise NULL
     */
    protected function getCacheEnabled()
    {
        return $this->cacheEnabled;
    }

    /**
     * Setter for $cacheLifetime.
     *
     * @param int $cacheLifetime The cacheLifetime to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setCacheLifetime($cacheLifetime)
    {
        $this->cacheLifetime = $cacheLifetime;
    }

    /**
     * Fluent setter for $cacheLifetime.
     *
     * @param int $cacheLifetime The cacheLifetime to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function cacheLifetime($cacheLifetime)
    {
        $this->setCacheLifetime($cacheLifetime);

        return $this;
    }

    /**
     * Returns the active cacheLifetime of the translator instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int cacheLifetime if set, otherwise NULL
     */
    protected function getCacheLifetime()
    {
        return $this->cacheLifetime;
    }

    /**
     * Setter for $encoding.
     *
     * @param string $encoding The encoding to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * Fluent setter for $encoding.
     *
     * @param string $encoding The encoding to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function encoding($encoding)
    {
        $this->setEncoding($encoding);

        return $this;
    }

    /**
     * Returns the active encoding of the translator instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|string The active encoding if set, otherwise NULL
     */
    protected function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Setter for $path.
     *
     * @param string $path The path to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Fluent setter for $path.
     *
     * @param string $path The path to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function path($path)
    {
        $this->setPath($path);

        return $this;
    }

    /**
     * Returns the active path of the translator instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|string The active path if set, otherwise NULL
     */
    protected function getPath()
    {
        return $this->path;
    }

    /**
     * Returns either the interface has a translation table support or not.
     *
     * @param Doozr_I18n_Service_Interface_Interface $instance An instance of a class implementing
     *                                                         Doozr_I18n_Service_Interface_Interface to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if translation table support exists, otherwise FALSE
     */
    protected function hasTranslationTableSupport(Doozr_I18n_Service_Interface_Interface $instance)
    {
        $method = 'buildTranslationtable';

        return (method_exists($instance, $method) && is_callable([$instance, $method]));
    }
}
