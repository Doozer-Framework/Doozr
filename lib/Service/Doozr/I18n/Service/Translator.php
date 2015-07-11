<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Service - I18n
 *
 * Translator.php - Translator is responsible for translation within module I18n.
 * The translator has the following responsebilities:
 *
 *  - A
 *  - B
 *  - C
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
 * @package    Doozr_Service
 * @subpackage Doozr_Service_I18n
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Class.php';

/**
 * Doozr - Service - I18n
 *
 * Translator is responsible for translation within the module I18n
 *
 * @category   Doozr
 * @package    Doozr_Service
 * @subpackage Doozr_Service_I18n
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_I18n_Service_Translator extends Doozr_Base_Class
{
    /**
     * Active locale
     *
     * @var string
     * @access protected
     */
    protected $locale;

    /**
     * Active encoding
     *
     * @var string
     * @access protected
     */
    protected $encoding;

    /**
     * Locale we use for translation if redirect is used
     *
     * @var string
     * @access protected
     */
    protected $redirectLocale;

    /**
     * Cache state.
     * Either TRUE = enabled || FALSE = disabled
     *
     * @var bool
     * @access protected
     */
    protected $cacheEnabled;

    /**
     * Lifetime of cached elements.
     *
     * @var int
     * @access protected
     */
    protected $cacheLifetime;

    /**
     * Path to translation files.
     *
     * @var string
     * @access protected
     */
    protected $pathToTranslations;

    /**
     * The NAME of the translator interface:
     * Gettext, Text, ...
     *
     * @var string
     * @access protected
     */
    protected $translatorInterface;

    /**
     * Translator-interface instances.
     * Can be either: Gettext, Text, ...
     *
     * @var Doozr_I18n_Service_Interface_Interface[]
     * @access protected
     * @static
     */
    protected static $translatorInterfaces = [];

    /**
     * I18n-configuration of the I18n-Service
     *
     * @var object
     * @access protected
     */
    protected $configI18n;

    /**
     * I10n-configuration of the locale of this instance
     *
     * @var object
     * @access protected
     */
    protected $configL10n;

    /**
     * Namespace(s) used by this instance
     *
     * @var array
     * @access protected
     */
    protected $namespaces = [];

    /**
     * Key identifier for translation-table
     *
     * @var string
     * @access protected
     */
    protected $translationTableUid;

    /**
     * Mode "translate" = default/basic/simple translate
     *
     * @var string
     * @access public
     */
    const MODE_TRANSLATE = 'translate';

    /**
     * Mode "translateEncode" = encoded output/result
     *
     * @var string
     * @access public
     */
    const MODE_TRANSLATE_ENCODE = 'translateEncode';

    /**
     * Mode "translateEncodePlus" = special encoding return value
     *
     * @var string
     * @access public
     */
    const MODE_TRANSLATE_ENCODE_PLUS = 'translateEncodePlus';

    /*------------------------------------------------------------------------------------------------------------------
     | MAIN CONTROL METHODS (CONSTRUCTOR AND INIT)
     +----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor
     *
     * This method is intend to act as constructor.
     *
     * @param string                 $locale     The locale this instance is working with
     * @param string                 $encoding   The encoding for this instance
     * @param Doozr_Configuration_Interface $configI18n An instance of Doozr_Config_Ini holding the I18n-config
     * @param Doozr_Configuration_Interface $configL10n An instance of Doozr_Config_Ini holding the I10n-config (for locale)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \Doozr_I18n_Service_Translator Instance of this class
     * @access public
     */
    public function __construct(
                               $locale,
                               $encoding,
        Doozr_Configuration_Interface $configI18n,
        Doozr_Configuration_Interface $configL10n
    ) {
        $this
            ->locale($locale)
            ->redirectLocale(
                (true === isset($configL10n->redirect) && true === isset($configL10n->redirect->target)) ?
                    $configL10n->redirect->target :
                    null
            )
            ->encoding($encoding)
            ->cacheEnabled($configI18n->kernel->caching->enabled)
            ->cacheLifetime($configI18n->kernel->caching->lifetime)
            ->pathToTranslations($configI18n->i18n->path)
            ->translatorInterface(
                ucfirst(strtolower($configI18n->i18n->translator->interface))
            )
            ->configI18n($configI18n)
            ->configL10n($configL10n);
    }

    /*------------------------------------------------------------------------------------------------------------------
     | PUBLIC API
     +----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for translationTableUid.
     *
     * @param string $translationTableUid The uuid to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setTranslationTableUid($translationTableUid)
    {
        $this->translationTableUid = $translationTableUid;
    }

    /**
     * Setter for translationTableUid.
     *
     * @param string $translationTableUid The uuid to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function translationTableUid($translationTableUid)
    {
        $this->setTranslationTableUid($translationTableUid);
        return $this;
    }

    /**
     * Getter for translationTableUid.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The uuid if set, otherwise NULL
     * @access public
     */
    public function getTranslationTableUid()
    {
        return $this->translationTableUid;
    }

    /**
     * Setter for $translatorInterface.
     *
     * @param string $translatorInterface The translatorInterface to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setTranslatorInterface($translatorInterface)
    {
        $this->translatorInterface = $translatorInterface;
    }

    /**
     * Fluent setter for $translatorInterface.
     *
     * @param string $translatorInterface The translatorInterface to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function translatorInterface($translatorInterface)
    {
        $this->setTranslatorInterface($translatorInterface);
        return $this;
    }

    /**
     * Returns the active translatorInterface of the translator instance
     *
     * This method is intend to return the active translatorInterface of
     * the translator instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|string The active translatorInterface if set, otherwise NULL
     * @access public
     */
    public function getTranslatorInterface()
    {
        return $this->translatorInterface;
    }

    /**
     * Setter for $locale.
     *
     * @param string $locale The locale to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Fluent setter for $locale.
     *
     * @param string $locale The locale to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function locale($locale)
    {
        $this->setLocale($locale);
        return $this;
    }

    /**
     * Returns the active locale of the translator instance
     *
     * This method is intend to return the active locale of
     * the translator instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|string The active locale if set, otherwise NULL
     * @access public
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Setter for $redirectLocale.
     *
     * @param string $redirectLocale The redirectLocale to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setRedirectLocale($redirectLocale)
    {
        $this->redirectLocale = $redirectLocale;
    }

    /**
     * Fluent setter for $redirectLocale.
     *
     * @param string $redirectLocale The redirectLocale to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function redirectLocale($redirectLocale)
    {
        $this->setRedirectLocale($redirectLocale);
        return $this;
    }

    /**
     * Returns the active redirectLocale of the translator instance
     *
     * This method is intend to return the active redirectLocale of
     * the translator instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|string The active redirectLocale if set, otherwise NULL
     * @access public
     */
    public function getRedirectLocale()
    {
        return $this->redirectLocale;
    }

    /**
     * Setter for $encoding.
     *
     * @param string $encoding The encoding to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * Fluent setter for $encoding.
     *
     * @param string $encoding The encoding to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function encoding($encoding)
    {
        $this->setEncoding($encoding);
        return $this;
    }

    /**
     * Returns the active encoding of the translator instance
     *
     * This method is intend to return the active encoding of
     * the translator instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|string The active encoding if set, otherwise NULL
     * @access public
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Setter for $configI18n.
     *
     * @param string $configI18n The configI18n to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setConfigI18n($configI18n)
    {
        $this->configI18n = $configI18n;
    }

    /**
     * Fluent setter for $configI18n.
     *
     * @param string $configI18n The configI18n to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function configI18n($configI18n)
    {
        $this->setConfigI18n($configI18n);
        return $this;
    }

    /**
     * Returns the active configI18n of the translator instance
     *
     * This method is intend to return the active configI18n of
     * the translator instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|string The active configI18n if set, otherwise NULL
     * @access public
     */
    public function getConfigI18n()
    {
        return $this->configI18n;
    }

    /**
     * Setter for $configL10n.
     *
     * @param string $configL10n The configL10n to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setConfigL10n($configL10n)
    {
        $this->configL10n = $configL10n;
    }

    /**
     * Fluent setter for $configL10n.
     *
     * @param string $configL10n The configL10n to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function configL10n($configL10n)
    {
        $this->setConfigL10n($configL10n);
        return $this;
    }

    /**
     * Returns the active configL10n of the translator instance
     *
     * This method is intend to return the active configL10n of
     * the translator instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|string The active configL10n if set, otherwise NULL
     * @access public
     */
    public function getConfigL10n()
    {
        return $this->configL10n;
    }

    /**
     * Setter for $cacheEnabled.
     *
     * @param string $cacheEnabled The cacheEnabled to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setCacheEnabled($cacheEnabled)
    {
        $this->cacheEnabled = $cacheEnabled;
    }

    /**
     * Fluent setter for $cacheEnabled.
     *
     * @param string $cacheEnabled The cacheEnabled to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function cacheEnabled($cacheEnabled)
    {
        $this->setCacheEnabled($cacheEnabled);
        return $this;
    }

    /**
     * Returns the active cacheEnabled of the translator instance
     *
     * This method is intend to return the active cacheEnabled of
     * the translator instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|string The active cacheEnabled if set, otherwise NULL
     * @access public
     */
    public function getCacheEnabled()
    {
        return $this->cacheEnabled;
    }

    /**
     * Setter for $cacheLifetime.
     *
     * @param string $cacheLifetime The cacheLifetime to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setCacheLifetime($cacheLifetime)
    {
        $this->cacheLifetime = $cacheLifetime;
    }

    /**
     * Fluent setter for $cacheLifetime.
     *
     * @param string $cacheLifetime The cacheLifetime to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function cacheLifetime($cacheLifetime)
    {
        $this->setCacheLifetime($cacheLifetime);
        return $this;
    }

    /**
     * Returns the active cacheLifetime of the translator instance
     *
     * This method is intend to return the active cacheLifetime of
     * the translator instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|string The active cacheLifetime if set, otherwise NULL
     * @access public
     */
    public function getCacheLifetime()
    {
        return $this->cacheLifetime;
    }

    /**
     * Setter for $pathToTranslations.
     *
     * @param string $pathToTranslations The pathToTranslations to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setPathToTranslations($pathToTranslations)
    {
        $this->pathToTranslations = $pathToTranslations;
    }

    /**
     * Fluent setter for $pathToTranslations.
     *
     * @param string $pathToTranslations The pathToTranslations to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function pathToTranslations($pathToTranslations)
    {
        $this->setPathToTranslations($pathToTranslations);
        return $this;
    }

    /**
     * Returns the active pathToTranslations of the translator instance
     *
     * This method is intend to return the active pathToTranslations of
     * the translator instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return null|string The active pathToTranslations if set, otherwise NULL
     * @access public
     */
    public function getPathToTranslations()
    {
        return $this->pathToTranslations;
    }

    /**
     * returns the redirect-status of the translator instance
     *
     * This method is intend to return the redirect-status of the translator instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if redirect, otherwise FALSE
     * @access public
     */
    public function hasRedirect()
    {
        return ($this->locale != $this->redirectLocale);
    }

    /**
     * sets a single namespace or an array of namespaces as active
     *
     * This method is intend to set a single namespace or an array of namespaces as active.
     * These namespace(s) is/are used for reading strings from e.g. (home.ini).
     *
     * @param mixed $namespace STRING single namespace, or ARRAY collection of namespaces
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success, otherwise FALSE
     * @access public
     */
    public function setNamespace($namespace)
    {
        // Check if is array
        if (is_string($namespace)) {
            // and make namespace an array if not
            $namespace = array($namespace);
        }

        // Store
        $this->setNamespaces($namespace);

        // and trigger namespace changed
        $this->namespaceChanged();

        return true;
    }

    /**
     * Adds a namespace to the list of active namespaces
     *
     * This method is intend to add a namespace to the list of active namespaces.
     *
     * @param mixed $namespace STRING single namespace, or ARRAY collection of namespaces
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if namespace(s) have been added, otherwise FALSE
     * @access public
     */
    public function addNamespace($namespace)
    {
        // Assume no operation
        $result = in_array($namespace, $this->getNamespaces());

        // Check if not already set
        if (false !== $result) {

            // make array for iteration
            if (is_string($namespace)) {
                $namespace = array($namespace);
            }

            // iterate over namespaces and
            foreach ($namespace as $singleNamespace) {
                // store namespace
                $this->namespaces[] = $singleNamespace;
            }

            // Namespace add = namespace changed event
            $this->namespaceChanged();

            $result = true;
        }

        return $result;
    }

    /**
     * Setter for namespaces
     *
     * @param array $namespaces The namespaces to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setNamespaces(array $namespaces)
    {
        $this->namespaces = $namespaces;
    }

    /**
     * returns the currently active namespace(s)
     *
     * This method is intend to return the currently active namespace(s).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array List of active namespaces
     * @access public
     */
    public function getNamespaces()
    {
        return $this->namespaces;
    }

    /**
     * checks if given namespace is part of active namespaces
     *
     * This method is intend to check if given namespace is part of active namespaces.
     *
     * @param string $namespace The namespace to check its existence
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if namespace is in list, otherwise FALSE
     * @access public
     */
    public function hasNamespace($namespace = null)
    {
        if ($namespace === null) {
            $result = (count($this->namespaces) > 0);

        } else {
            $result = in_array($namespace, $this->namespaces);
        }

        return $result;
    }

    /**
     * checks if given namespace is part of active namespaces
     *
     * This method is intend to check if given namespace is part of active namespaces.
     *
     * @param string $namespace The namespace to check its existence
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if namespace is in list, otherwise FALSE
     * @access public
     */
    public function removeNamespace($namespace)
    {
        // assume no success
        $result = false;

        // check if namespace exists in active namespaces
        if ($this->hasNamespace($namespace)) {
            // remove the elements
            $this->namespaces = array_diff($this->namespaces, array($namespace));

            // reindex the array
            $this->namespaces = array_values($this->namespaces);

            // namespace removed
            $result = true;
        }

        // check for result and trigger namespace-changed if
        if ($result) {
            // namespace changed
            $this->namespaceChanged();
        }

        // return the result of op
        return $result;
    }

    /**
     * Translates a string.
     *
     * This method is intend to translate a string.
     *
     * @param string $key       The string to translate
     * @param mixed  $arguments The arguments to pass to the translation
     *
     * @return string|boolean Translated STRING on success, otherwise FALSE
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function _($key, $arguments = null)
    {
        return $this->translate($key, $arguments, self::MODE_TRANSLATE);
    }

    /**
     * Translates a string and encodes it
     *
     * This method is intend to translate a string and encode it afterwards.
     *
     * @param string $key       The string to translate
     * @param mixed  $arguments The arguments to pass to the translation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|boolean Translated STRING on success, otherwise FALSE
     * @access public
     */
    public function __($key, $arguments = null)
    {
        return $this->translate($key, $arguments, self::MODE_TRANSLATE_ENCODE);
    }

    /**
     * translates a string and encode it
     *
     * This method is intend to translate a string and encode it.
     *
     * @param string $key       The string to translate
     * @param mixed  $arguments The arguments to pass to the translation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|boolean Translated STRING on success, otherwise FALSE
     * @access public
     */
    public function ___($key, $arguments = null)
    {
        return $this->translate($key, $arguments, self::MODE_TRANSLATE_ENCODE_PLUS);
    }

    /*------------------------------------------------------------------------------------------------------------------
     | TOOLS & HELPER
     +----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is intend to re-initialize the namespaces in translation-tables.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function namespaceChanged()
    {
        // Get locale we target (performance)
        $encoding       = $this->getEncoding();
        $redirectLocale = $this->getRedirectLocale();

        // Init interface for translation
        if ((!self::$translatorInterfaces) || (false === isset(self::$translatorInterfaces[$encoding]))) {
            self::$translatorInterfaces[$encoding] = $this->translatorInterfaceFactory();
        }

        $locale = ($redirectLocale !== null) ? $redirectLocale : $this->getLocale();

        // Set the new namespace and retrieve key to translationtable
        $this->setTranslationTableUid(
            self::$translatorInterfaces[$encoding]->initLocaleNamespace(
                $locale,
                $this->getNamespaces()
            )
        );

        return true;
    }

    /**
     * Initializes the translator (interface to translations [file, gettext, db])
     *
     * This method is intend to initialize the translator (interface to translations [file, gettext, db]).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_I18n_Service_Interface_Gettext|Doozr_I18n_Service_Interface_Text An instance of Gettext or Text I
     * @access protected
     */
    protected function translatorInterfaceFactory()
    {
        // Combine some parts to a config for the interface
        $config                  = $this->getConfigI18n()->i18n;
        $config->path            = $this->getPathToTranslations();
        $config->cache->enabled  = $this->getCacheEnabled();
        $config->cache->lifetime = $this->getCacheLifetime();
        $config->encoding        = $this->getEncoding();

        // Include required file -> NO autoloading -> cause of performance!
        include_once DOOZR_DOCUMENT_ROOT . 'Service/Doozr/I18n/Service/Interface/' .
            $this->getTranslatorInterface() . '.php';

        // Combine classname
        $interfaceClass = 'Doozr_I18n_Service_Interface_' . $this->getTranslatorInterface();

        // Instanciate and return instance
        return $interfaceClass::getInstance($config);
    }

    /**
     * Translates a passes string
     *
     * This method is intend to act as the backend method for translation requests by _() __() and ___().
     *
     * @param string     $key       The string to translate
     * @param mixed      $arguments The arguments used by translator for translation (e.g. inserting values)
     * @param int|string $mode      The runtimeEnvironment in which the translation is requested (normal, encode or encode-plus)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed STRING translation on success, otherwise FALSE
     * @access protected
     * @throws Doozr_I18n_Service_Exception
     */
    protected function translate($key, $arguments = null, $mode = self::MODE_TRANSLATE)
    {
        $encoding = $this->getEncoding();

        if ($this->hasNamespace() === false) {
            throw new Doozr_I18n_Service_Exception(
                'Translation without namespace is not possible. Please set a namespace via setNamespace(...) ' .
                'or addNamespace(...) first.'
            );
        }

        // Translate
        $translation = self::$translatorInterfaces[$encoding]->lookup(
            $key,
            $this->getTranslationTableUid(),
            $arguments
        );

        // encode result? => check runtimeEnvironment
        switch ($mode) {
        case self::MODE_TRANSLATE_ENCODE:
            $translation = htmlspecialchars($translation, ENT_QUOTES & ENT_DISALLOWED & ENT_HTML5 , 'UTF-8');
            break;

        case self::MODE_TRANSLATE_ENCODE_PLUS:
            $translation = htmlentities($translation, ENT_QUOTES & ENT_DISALLOWED & ENT_HTML5 , 'UTF-8', false);
            break;
        }

        return $translation;
    }
}
