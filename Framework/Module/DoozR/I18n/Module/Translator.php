<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Module I18n
 *
 * Translator.php - Translator is responsible for translation within module I18n
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
 * @package    DoozR_Module
 * @subpackage DoozR_Module_I18n
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Class.php';

/**
 * DoozR Module I18n
 *
 * Translator is responsible for translation within the module I18n
 *
 * @category   DoozR
 * @package    DoozR_Module
 * @subpackage DoozR_Module_I18n
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_I18n_Module_Translator extends DoozR_Base_Class
{
    /**
     * the real locale of the translator
     *
     * @var string
     * @access private
     */
    private $_locale;

    /**
     * the locale we use for translation if redirect mode enabled
     *
     * @var string
     * @access private
     */
    private $_redirectLocale;

    /**
     * the I18n-configuration of the I18n-Module
     *
     * @var object
     * @access private
     */
    private $_configI18n;

    /**
     * the I10n-configuration of the locale of this instance
     *
     * @var object
     * @access private
     */
    private $_configI10n;

    /**
     * the namespace(s) used by this instance
     *
     * @var array
     * @access private
     */
    private $_namespaces = array();

    /**
     * the translator-interface can be either "Text" or "Gettext" or "MySQL"
     *
     * @var object
     * @access private
     * @static
     */
    private static $_translatorInterface;

    /**
     * the key identifier for translation-table
     *
     * @var string
     * @access private
     */
    private $_translationTableUid;

    /*******************************************************************************************************************
     * // BEGIN PUBLIC INTERFACES
     ******************************************************************************************************************/

    /**
     * returns the redirect-status of the translator instance
     *
     * This method is intend to return the redirect-status of the translator instance.
     *
     * @return  boolean TRUE if redirect, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function hasRedirect()
    {
        return ($this->_locale != $this->_redirectLocale);
    }

    /**
     * sets a single namespace or an array of namespaces as active
     *
     * This method is intend to set a single namespace or an array of namespaces as active.
     * These namespace(s) is/are used for reading strings from e.g. (namespace_home.inc).
     *
     * @param mixed $namespace STRING single namespace, or ARRAY collection of namespaces
     *
     * @return  boolean TRUE on
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function setNamespace($namespace)
    {
        // check if is array
        if (is_string($namespace)) {
            // and make namespace an array if not
            $namespace = array($namespace);
        }

        // store
        $this->_namespaces = $namespace;

        // and trigger namespace changed
        $this->_namespaceChanged();

        // return result
        return true;
    }

    /**
     * adds a namespace to the list of active namespaces
     *
     * This method is intend to add a namespace to the list of active namespaces.
     *
     * @param mixed $namespace STRING single namespace, or ARRAY collection of namespaces
     *
     * @return  boolean TRUE if namespace(s) have been added, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function addNamespace($namespace)
    {
        // assume no operation
        $result = in_array($namespace, $this->_namespaces);

        // check if not already set
        if (!$result) {

            // make array for iteration
            if (is_string($namespace)) {
                $namespace = array($namespace);
            }

            // iterate over namespaces and
            foreach ($namespace as $singleNamespace) {
                // store namespace and return result
                $this->_namespaces[] = $singleNamespace;
            }

            // namespace changed
            $this->_namespaceChanged();

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
     * @return  array List of active namespaces
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getNamespace()
    {
        return $this->_namespaces;
    }

    /**
     * checks if given namespace is part of active namespaces
     *
     * This method is intend to check if given namespace is part of active namespaces.
     *
     * @param string $namespace The namespace to check its existence
     *
     * @return  boolean TRUE if namespace is in list, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function hasNamespace($namespace)
    {
        return in_array($namespace, $this->_namespaces);
    }

    /**
     * checks if given namespace is part of active namespaces
     *
     * This method is intend to check if given namespace is part of active namespaces.
     *
     * @param string $namespace The namespace to check its existence
     *
     * @return  boolean TRUE if namespace is in list, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function removeNamespace($namespace)
    {
        // assume no success
        $result = false;

        // check if namespace exists in active namespaces
        if ($this->hasNamespace($namespace)) {
            // remove the elements
            $this->_namespaces = array_diff($this->_namespaces, array($namespace));

            // reindex the array
            $this->_namespaces = array_values($this->_namespaces);

            // namespace removed
            $result = true;
        }

        // check for result and trigger namespace-changed if
        if ($result) {
            // namespace changed
            $this->_namespaceChanged();
        }

        // return the result of op
        return $result;
    }

    /**
     * translates a string
     *
     * This method is intend to translate a string.
     *
     * @param string $string    The string to translate
     * @param mixed  $arguments The arguments to pass to the translation
     *
     * @return  mixed STRING translated string on success, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function _($string, $arguments = null)
    {
        return $this->_translate('translate', $string, $arguments);
    }

    /**
     * translates a string and encode it
     *
     * This method is intend to translate a string and encode it.
     *
     * @param string $string    The string to translate
     * @param mixed  $arguments The arguments to pass to the translation
     *
     * @return  mixed STRING translated string on success, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function __($string, $arguments = null)
    {
        return $this->_translate('translateEncode', $string, $arguments);
    }

    /**
     * translates a string and encode it
     *
     * This method is intend to translate a string and encode it.
     *
     * @param string $string    The string to translate
     * @param mixed  $arguments The arguments to pass to the translation
     *
     * @return  mixed STRING translated string on success, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function ___($string, $arguments = null)
    {
        return $this->_translate('translateEncodePlus', $string, $arguments);
    }

    /*******************************************************************************************************************
     * \\ END PUBLIC INTERFACES
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN TOOLS + HELPER
     ******************************************************************************************************************/

    /**
     * re-initializes the namespaces in translation-tables
     *
     * This method is intend to re-initialize the namespaces in translation-tables.
     *
     * @return  void
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _namespaceChanged()
    {
        if (!self::$_translatorInterface) {
            // init interface for translation
            self::$_translatorInterface = $this->_getTranslatorInterface();
        }

        $locale = ($this->_redirectLocale) ? $this->_redirectLocale : $this->_locale;

        // set the new namespace and retrieve key to translationtable
        $this->_translationTableUid = self::$_translatorInterface->initLocaleNamespace(
            $locale,
            $this->_namespaces
        );

        // success
        return true;
    }

    /**
     * initializes the translator (interface to translations [file, gettext, db])
     *
     * This method is intend to initialize the translator (interface to translations [file, gettext, db]).
     *
     * @return  void
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _getTranslatorInterface()
    {
        // get type of translator interface from general module configuration
        $interfaceType = $this->_configI18n->i18n->translator->mode();

        // combine some parts to a config for the interface
        $config = array(
            'path'  => $this->_configI18n->i18n->path(),
            'cache' => array(
                'enabled'  => $this->_configI18n->cache->container(),
                'lifetime' => $this->_configI18n->cache->lifetime()
            )
        );

        // combine classname
        $interfaceClass = 'DoozR_I18n_Module_Interface_'.$interfaceType;

        // instanciate
        return $interfaceClass::getInstance($config);
    }

    /**
     * translates strings
     *
     * This method is intend to act as the backend method for translation requests by _() __() and ___().
     *
     * @param integer $mode      The mode in which the translation is requested (normal, encode or encode-plus)
     * @param string  $string    The string to translate
     * @param mixed   $arguments The arguments used by translator for translation (e.g. inserting values)
     *
     * @return  mixed STRING translation on success, otherwise FALSE
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _translate($mode, $string, $arguments)
    {
        // check if translator is already initialized
        if (!self::$_translatorInterface) {
            // init interface for translation
            self::$_translatorInterface = $this->_getTranslatorInterface();
        }

        // translate
        $translation = self::$_translatorInterface->lookup(
            $string,
            $this->_translationTableUid,
            $arguments
        );

        // encode result? => check mode
        switch ($mode) {
        case 'translateEncode':
            $translation = htmlspecialchars(utf8_encode($translation), null, 'UTF-8');
            break;
        case 'translateEncodePlus':
            $translation = htmlentities(utf8_encode($translation));
            break;
        }

        // return the result
        return $translation;
    }

    /*******************************************************************************************************************
     * \\ END TOOLS + HELPER
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN MAIN CONTROL METHODS (CONSTRUCTOR AND INIT)
     ******************************************************************************************************************/

    /**
     * constructor
     *
     * This method is intend to act as constructor.
     *
     * @param string                 $locale     The locale this instance is working with
     * @param DoozR_Config_Interface $configI18n An instance of DoozR_Config_Ini holding the I18n-config
     * @param DoozR_Config_Interface $configI10n An instance of DoozR_Config_Ini holding the I10n-config (for locale)
     *
     * @return object Instance of this class
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __construct($locale, DoozR_Config_Interface $configI18n, DoozR_Config_Interface $configI10n)
    {
        // store the locale of this instance assume no redirect -> work on given locale
        $this->_locale         = $locale;
        $this->_redirectLocale = isset($configI10n->redirect) ? $configI10n->redirect->target() : null;

        // store configurations
        $this->_configI18n = $configI18n;
        $this->_configI10n = $configI10n;

        // call parents constructor
        parent::__construct();
    }

    /*******************************************************************************************************************
     * \\ END MAIN CONTROL METHODS (CONSTRUCTOR AND INIT)
     ******************************************************************************************************************/
}

?>
