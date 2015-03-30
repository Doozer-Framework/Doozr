<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Service - I18n
 *
 * Translator.php - Translator is responsible for translation within module I18n
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
 * @package    DoozR_Service
 * @subpackage DoozR_Service_I18n
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Class.php';

/**
 * DoozR - Service - I18n
 *
 * Translator is responsible for translation within the module I18n
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_I18n
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_I18n_Service_Translator extends DoozR_Base_Class
{
    /**
     * Real locale of the translator
     *
     * @var string
     * @access protected
     */
    protected $locale;

    /**
     * The encoding for this locale
     *
     * @var string
     * @access protected
     */
    protected $encoding;

    /**
     * Locale we use for translation if redirect runtimeEnvironment enabled
     *
     * @var string
     * @access protected
     */
    protected $redirectLocale;

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
    protected $namespaces = array();

    /**
     * Translator-interface can be either "Text" or "Gettext" or "MySQL"
     *
     * @var DoozR_I18n_Service_Interface_Abstract[]
     * @access protected
     * @static
     */
    protected static $translatorInterface = array();

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
     * @const
     */
    const MODE_TRANSLATE = 'translate';

    /**
     * Mode "translateEncode" = encoded output/result
     *
     * @var string
     * @access public
     * @const
     */
    const MODE_TRANSLATE_ENCODE = 'translateEncode';

    /**
     * Mode "translateEncodePlus" = special encoding return value
     *
     * @var string
     * @access public
     * @const
     */
    const MODE_TRANSLATE_ENCODE_PLUS = 'translateEncodePlus';


    /*------------------------------------------------------------------------------------------------------------------
     | BEGIN PUBLIC INTERFACES
     +----------------------------------------------------------------------------------------------------------------*/

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
     * returns the redirect-status of the translator instance
     *
     * This method is intend to return the redirect-status of the translator instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if redirect, otherwise FALSE
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
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function setNamespace($namespace)
    {
        // check if is array
        if (is_string($namespace)) {
            // and make namespace an array if not
            $namespace = array($namespace);
        }

        // store
        $this->namespaces = $namespace;

        // and trigger namespace changed
        $this->namespaceChanged();

        // return result
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
     * @return boolean TRUE if namespace(s) have been added, otherwise FALSE
     * @access public
     */
    public function addNamespace($namespace)
    {
        // assume no operation
        $result = in_array($namespace, $this->namespaces);

        // check if not already set
        if (!$result) {

            // make array for iteration
            if (is_string($namespace)) {
                $namespace = array($namespace);
            }

            // iterate over namespaces and
            foreach ($namespace as $singleNamespace) {
                // store namespace and return result
                $this->namespaces[] = $singleNamespace;
            }

            // namespace changed
            $this->namespaceChanged();

            // success
            $result = true;
        }

        // return the result
        return $result;
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
    public function getNamespace()
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
     * @return boolean TRUE if namespace is in list, otherwise FALSE
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
     * @return boolean TRUE if namespace is in list, otherwise FALSE
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
     | BEGIN TOOLS + HELPER
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
        // init interface for translation
        #if (!self::$_translatorInterface) {
            self::$translatorInterface[$this->encoding] = $this->getTranslatorInterface();
        #}

        $locale = ($this->redirectLocale) ? $this->redirectLocale : $this->locale;

        // set the new namespace and retrieve key to translationtable
        $this->translationTableUid = self::$translatorInterface[$this->encoding]->initLocaleNamespace(
            $locale,
            $this->namespaces
        );

        // success
        return true;
    }

    /**
     * Initializes the translator (interface to translations [file, gettext, db])
     *
     * This method is intend to initialize the translator (interface to translations [file, gettext, db]).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_I18n_Service_Interface_Gettext|DoozR_I18n_Service_Interface_Text An instance of Gettext or Text I
     * @access protected
     */
    protected function getTranslatorInterface()
    {
        // Get type of translator interface from general module configuration
        $interfaceType = $this->configI18n->i18n->translator->mode();

        // combine some parts to a config for the interface
        $config = array(
            'path'     => $this->configI18n->i18n->path(),
            'cache'    => array(
                'enabled'  => $this->configI18n->cache->container(),
                'lifetime' => $this->configI18n->cache->lifetime()
            ),
            'encoding' => $this->encoding,
        );

        // include required file -> NO autoloading -> performance!
        include_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/I18n/Service/Interface/'.$interfaceType.'.php';

        // combine classname
        $interfaceClass = 'DoozR_I18n_Service_Interface_'.$interfaceType;

        // instanciate and return instance
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
     * @throws DoozR_I18n_Service_Exception
     */
    protected function translate($key, $arguments = null, $mode = self::MODE_TRANSLATE)
    {
        if ($this->hasNamespace() === false) {
            throw new DoozR_I18n_Service_Exception(
                'Translation without namespace is not possible. Please set a namespace via setNamespace(...) ' .
                'or addNamespace(...) first.'
            );
        }

        // Check if translator is already initialized
        if (!self::$translatorInterface[$this->encoding]) {
            self::$translatorInterface[$this->encoding] = $this->getTranslatorInterface();
        }

        // translate
        $translation = self::$translatorInterface[$this->encoding]->lookup(
            $key,
            $this->translationTableUid,
            $arguments
        );

        // encode result? => check runtimeEnvironment
        /*
        switch ($runtimeEnvironment) {
        case self::MODE_TRANSLATE_ENCODE:
            $translation = htmlspecialchars($translation, ENT_QUOTES & ENT_DISALLOWED & ENT_HTML5 , 'UTF-8');
            break;

        case self::MODE_TRANSLATE_ENCODE_PLUS:
            $translation = htmlentities($translation, ENT_QUOTES & ENT_DISALLOWED & ENT_HTML5 , 'UTF-8', false);
            break;
        }
        */

        // return the result
        return $translation;
    }

    /*------------------------------------------------------------------------------------------------------------------
     | BEGIN MAIN CONTROL METHODS (CONSTRUCTOR AND INIT)
     +----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor
     *
     * This method is intend to act as constructor.
     *
     * @param string                 $locale     The locale this instance is working with
     * @param string                 $encoding   The encoding for this instance
     * @param DoozR_Config_Interface $configI18n An instance of DoozR_Config_Ini holding the I18n-config
     * @param DoozR_Config_Interface $configL10n An instance of DoozR_Config_Ini holding the I10n-config (for locale)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_I18n_Service_Translator Instance of this class
     * @access public
     */
    public function __construct(
        $locale,
        $encoding,
        DoozR_Config_Interface $configI18n,
        DoozR_Config_Interface $configL10n
    ) {
        // store the locale of this instance assume no redirect -> work on given locale
        $this->locale         = $locale;
        $this->encoding       = $encoding;
        $this->redirectLocale = (isset($configL10n->redirect)) ? $configL10n->redirect->target() : null;

        // store configurations
        $this->configI18n = $configI18n;
        $this->configL10n = $configL10n;
    }
}
