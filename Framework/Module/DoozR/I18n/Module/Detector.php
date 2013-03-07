<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Module I18n
 *
 * Detector.php - Locale detection part of the module I18n
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

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Class/Singleton.php';

/**
 * DoozR Module I18n
 *
 * Locale detection part of the module I18n
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
class DoozR_I18n_Module_Detector extends DoozR_Base_Class_Singleton
{
    /**
     * an array containing the "defaults" for fallback solutions
     *
     * @var array
     * @access private
     * @static
     */
    private static $_defaults;

    /**
     * the store(s) available for storing preferences
     *
     * @var array
     * @access private
     * @static
     */
    private static $_stores;

    /**
     * the identifier used to identify I18n prefered data in store(s)
     *
     * @var string
     * @access private
     * @static
     */
    private static $_identifier;

    /**
     * an array containing all available locales on the system
     *
     * @var array
     * @access private
     * @static
     */
    private static $_availableLocales;

    /**
     * the session module instance
     *
     * @var object
     * @access private
     * @static
     */
    private static $_session;

    /**
     * status of initialization
     *
     * @var boolean
     * @access private
     * @static
     */
    private static $_initialized = false;

    /**
     * the current active and prefered locale
     *
     * @var string
     * @access private
     */
    private $_locale;

    /**
     * the current active and prefered weight
     *
     * @var double
     * @access private
     */
    private $_weight;

    /**
     * the current active and prefered language
     *
     * @var string
     * @access private
     */
    private $_language;

    /**
     * the current active and prefered country(-code)
     *
     * @var string
     * @access private
     */
    private $_country;

    /**
     * an array containing all detected locales
     *
     * @var array
     * @access private
     */
    private $_detectedLocales;

    /**
     * an array containing all detected languages
     *
     * @var array
     * @access private
     */
    private $_detectedLanguages;

    /**
     * an array containing all detected countries
     *
     * @var array
     * @access private
     */
    private $_detectedCountries;

    private static $_preferenceLifetime = 7776000;

    private $_touched = false;


    /*******************************************************************************************************************
     * // BEGIN PUBLIC INTERFACE
     ******************************************************************************************************************/

    /**
     * returns the current active locale in consideration of order
     *
     * This method is intend to return the current active locale in consideration of order.
     *
     * @return  string The current active locale like "de" or "at"
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getLocale()
    {
        // get the stored locale
        return $this->_locale;
    }


    /**
     * returns the weight of the current active locale
     *
     * This method is intend to return the weight of the current active locale
     *
     * @return  string The current active locale like "de" or "at"
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getWeight()
    {
        // get the stored weight of locale
        return $this->_weight;
    }


    /**
     * returns all detected locales
     *
     * This method is intend to return all detected locales
     *
     * @return  array All detected locales
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getLocales()
    {
        // get all stored locales
        return $this->_detectedLocales;
    }


    /**
     * returns the current active country
     *
     * This method is intend to return the current active country
     *
     * @return  string The current active country like "de" or "at"
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getCountry()
    {
        // get the stored country
        return $this->_country;
    }


    /**
     * returns all detected countries
     *
     * This method is intend to return all detected countries
     *
     * @return  array All detected countries
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getCountries()
    {
        // get all stored locales
        return $this->_detectedCountries;
    }


    /**
     * returns the current active language
     *
     * This method is intend to return the current active language
     *
     * @return  string The current active language like "de" or "at"
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getLanguage()
    {
        // get the stored language
        return $this->_language;
    }


    /**
     * returns all detected languages
     *
     * This method is intend to return all detected languages
     *
     * @return  array All detected languages
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getLanguages()
    {
        // get all stored locales
        return $this->_detectedLanguages;
    }


    /**
     * returns the complete set of current active locale, language + country
     *
     * This method is intend to return the complete set of current active locale, language + country.
     *
     * @return  array The complete set of current active locale-settings
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getLocalePreferences()
    {
        return array(
            'locale'   => $this->_locale,
            'language' => $this->_language,
            'country'  => $this->_country
        );
    }


    /**
     * checks if a given locale-code is valid
     *
     * This method is intend to check if all requirements are fulfilled.
     *
     * @param string $code de, de-AT, en-us ...
     *
     * @return  boolean TRUE if valid, otherwise FALSE
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function isValidLocaleCode($code = '')
    {
        return (preg_match('(^([a-zA-Z]{2})((_|-)[a-zA-Z]{2})?$)', $code) > 0) ? true : false;
    }


    /**
     * detect the user-prefered locale
     *
     * This method is intend to detect the user prefered locale.
     *
     * @param boolean $lookupAlternative TRUE to try to find a matching locale, FALSE to use systems default as fallback
     *
     * @return  boolean TRUE if detection was succesful
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function detect($lookupAlternative = true)
    {
        if (!self::$_initialized) {
            // finally init
            self::$_initialized = $this->_init($lookupAlternative);
        }

        // success
        return true;
    }


    /*******************************************************************************************************************
     * \\ END PUBLIC INTERFACE
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN TOOLS + HELPER
     ******************************************************************************************************************/

    /**
     * initializes and starts the detection
     *
     * This method is intend to initialize and start the detection.
     *
     * @param boolean $lookupAlternative TRUE to try to find a matching locale, FALSE to use systems default as fallback
     *
     * @return  boolean TRUE if detection was succesful
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _init($lookupAlternative)
    {
        // 1st try to retrieve previously stored pereferences from session (fastet store) and cookie
        $userPreferences = $this->_readPreferences();

        // retrieving of stored preferences failed
        if (!$userPreferences) {
            // now try to detect
            $userPreferences = $this->_detectPreferences();

            // check if retrieved locale exists
            if (!in_array($userPreferences['locale'], self::$_availableLocales)) {

                // look in the list of retrieved locales for the next matching one (alternative = true)
                if ($lookupAlternative) {
                    // reset the one found
                    $userPreferences = null;

                    // get count of found locales
                    $countDetectedLocales = count($this->_detectedLocales);

                    // iterate over all detected locales to find the first matching one
                    for ($i = 1; $i < $countDetectedLocales; ++$i) {
                        // check if we got a matching locale
                        if (in_array($this->_detectedLocales[$i]['locale'], self::$_availableLocales)) {
                            $userPreferences['locale']   = $this->_detectedLocales[$i]['locale'];
                            $userPreferences['weight']   = $this->_detectedLocales[$i]['weight'];
                            $userPreferences['language'] = $this->_detectedLanguages[$i];
                            $userPreferences['country']  = $this->_detectedCountries[$i];

                            // leave loop
                            break;
                        }
                    }
                }

                // if we should'nt lookup an alternative OR no locale was detected -> use systems default
                if (!$lookupAlternative || !$userPreferences) {
                    // or if we use the systems default locale if auto-detect fails
                    $userPreferences['locale']   = self::$_defaults['locale'];
                    $userPreferences['weight']   = self::$_defaults['weight'];
                    $userPreferences['language'] = self::$_defaults['language'];
                    $userPreferences['country']  = self::$_defaults['country'];
                }
            }

            // we did change something
            $this->_touched = true;
        }

        // store retrieved data in class and afterwards in store(s)
        $this->_locale   = $userPreferences['locale'];
        $this->_weight   = $userPreferences['weight'];
        $this->_language = $userPreferences['language'];
        $this->_country  = $userPreferences['country'];

        // finally store in defines stores
        if ($this->_touched) {
            $this->_writePreferences($userPreferences);
        }

        // init succesfully done (means locale, language and country are set to an existing locale)
        return true;
    }


    /**
     * writes the given preferences to all configured stores
     *
     * This method is intend to write the given preferences to all configured stores.
     *
     * @param array $preferences The preferences to store
     *
     * @return  boolean TRUE if storing was successful, otherwise FALSE
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _writePreferences(array $preferences)
    {
        // assume result true
        $result = true;

        // iterate over stores and try to reconstruct the previously stored preferences
        foreach (self::$_stores as $store) {
            // construct method-name for current store
            $method = '_write'.ucfirst($store);

            // try to get preferences from store
            $result = $result && $this->{$method}($preferences);
        }

        // succesfully retrieved
        return $result;
    }


    /**
     * controls the detection of the users locale preferences
     *
     * This method is intend to control the detection of the users locale preferences.
     *
     * @return  array An array containing the prefered "locale" + "weight", "language" and "country"
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _detectPreferences()
    {
        // try to detect locale by user-agents header
        $detectedPreferences = $this->_detectByRequestHeader();

        // FALLBACK: try to detect by user's ip/dns-hostname
        if (!$detectedPreferences) {
            // FALLBACK: try to detect by user's ip/dns-hostname
            $detectedPreferences = $this->_detectByUserIp();
        }

        // and finally store the detected results
        return $detectedPreferences;
    }


    /**
     * returns the user's prefered locale from store
     *
     * This method is intend to return the user's prefered, previously stored, locale from store.
     *
     * @return  mixed ARRAY containing the prefered-config, otherwise NULL
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _readPreferences()
    {
        // assume empty user-preferences
        $storedPreferences = false;

        // iterate over stores and try to reconstruct the previously stored preferences
        foreach (self::$_stores as $store) {
            // construct method-name for current store
            $method = '_read'.ucfirst($store);

            // try to get preferences from store
            $storedPreferences = $this->{$method}();

            // if result was retrieved successfully we can
            if ($storedPreferences) {
                break;
            }
        }

        // succesfully retrieved
        return $storedPreferences;
    }


    /**
     * returns an instance of the session module
     *
     * This method is intend to return an instance of the session module.
     *
     * @return  object An instance of the module DoozR_Session
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _getSession()
    {
        if (!self::$_session) {
            self::$_session = DoozR_Loader_Moduleloader::load('session');
        }

        return self::$_session;
    }


    /**
     * detects the available locales by request-header
     *
     * This method is intend to detect the available locales by request-header.
     *
     * @return  void
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _detectByRequestHeader()
    {
        // direct stop of processing if needed header not set
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return false;
        }

        // get header accept-language
        //$acceptedLanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        // TODO: remove
        $acceptedLanguages = explode(',', 'de-de,de;q=0.8,en;q=0.3,en-us;q=0.5');

        // valid result?
        if (count($acceptedLanguages) < 1) {
            return false;
        }

        // iterate over raw header-entries and extract
        foreach ($acceptedLanguages as $acceptedLanguage) {
            // prepare by splitting
            $parts = explode(';', $acceptedLanguage);

            // locale preset
            $locale = strtolower(trim($parts[0]));
            $weight = 1;

            // check if q is set
            if (count($parts) > 1) {
                $weight = trim(str_replace('q=', '', $parts[1]));
            }

            // if locale is valid add it
            if ($this->isValidLocaleCode($locale) && $weight >= 0) {
                $this->_addLocale($locale, $weight);
            }
        }

        // now sort by it's weight!
        $this->_detectedLocales = $this->_sortByWeight($this->_detectedLocales);

        // iterate over sorted result and retrieve language and country in correct order
        foreach ($this->_detectedLocales as $localeSet) {
            $parts = explode('-', $localeSet['locale']);
            $parts[1] = (isset($parts[1])) ? $parts[1] : $parts[0];

            // get language and country validated
            $language = ($this->isValidLocaleCode($parts[0])) ? $parts[0] : null;
            $country = ($this->isValidLocaleCode($parts[1])) ? $parts[1] : null;

            // store
            $this->_detectedLanguages[] = $language;
            $this->_detectedCountries[] = $country;
        }

        // success - return the prefered locale-set
        return array(
            'locale'   => $this->_detectedLocales[0]['locale'],
            'weight'   => $this->_detectedLocales[0]['weight'],
            'language' => $this->_detectedLanguages[0],
            'country'  => $this->_detectedCountries[0]
        );
    }


    /**
     * detects available locale(s) by requesting hostname/client-ip|domain
     *
     * This method is intend to detect available locale(s) by requesting hostname/client-ip|domain.
     *
     * @return  boolean TRUE on success, otherwise FALSE
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _detectByUserIp()
    {
        // get ip
        $ip = $_SERVER['REMOTE_ADDR'];

        // hostname (dialin/dialup hostname e.g. PH-1411J-uBR10k-02-Te-1-2-0.bilk.unity-media.net)
        $host = gethostbyaddr($ip);

        // get country by hostname
        $country = $this->_translateDomainToCountrycode($host);

        // if not retrieved a valid result => we check the server's domain!
        if (!$country) {
            $country = $this->_translateDomainToCountrycode($_SERVER['SERVER_NAME']);
        }

        // if still no result => assume operation failed!
        if (!$country) {
            return false;
        }

        // store country + make unique
        if (!in_array($country, $this->_detectedCountries)) {
            $this->_detectedCountries[] = $country;
        }

        // success
        return true;
    }


    /**
     * sorts the detected locales by it's weight
     *
     * This method is intend to sort the detected locales by it's weight
     *
     * @param array $locales The locales array to sort by weight
     *
     * @return  array By weight sorted elements
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _sortByWeight(array $locales)
    {
        // new index
        $index = array();

        // for those who are interested in (int) or intval() whats faster:
        // http://www.entwicklerblog.net/php/php-variable-in-integer-verwandeln-intfoo-oder-intvalfoo/
        foreach ($locales as $localeSet) {
            $index[$localeSet['locale']] = (int)$localeSet['weight'];
        }

        // make values the keys
        $index = array_flip($index);

        // sort by key
        krsort($index);

        // rebuild
        $locales = array();

        foreach ($index as $weight => $locale) {
            $locales[] = array(
                'locale' => $locale,
                'weight' => ($weight/10) // restore the correct weight here -> after sorting
            );
        }

        // return new constructed
        return $locales;
    }


    /**
     * adds a locale (and its language + country) to the list of available locales
     *
     * adds a locale (and its language + country) to the list of available locales
     *
     * @param string $locale The locale to add
     * @param double $weight The weight as double (e.g. 0.8 [q=0.8])
     *
     * @return  object Instance of this class
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _addLocale($locale, $weight = 0)
    {
        // doing this we prevent array_flip from throwing error on double/float-values!
        $weight *= 10;

        // insert the locale
        $this->_detectedLocales[] = array(
            'locale' => $locale,
            'weight' => $weight
        );
    }


    /**
     * translates a given domain (like "de" or "com") to countrycode
     *
     * This method is intend to translate a given domain (like "de" or "com") to countrycode.
     *
     * @param string $domain The domain to retrieve countrycode from
     *
     * @return  mixed STRING countrycode if translation successful, otherwise FALSE
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _translateDomainToCountrycode($domain = '')
    {
        // check for dotted hostname (domain)
        if (strrpos($domain, '.') < 1) {
            return false;
        }

        // split by dot
        $parts = explode('.', $domain);

        // get domain (last element of previously explode op)
        $domain = array_pop($parts);

        // top level domains (com, org, gov, aero, info) or eu-domain are all english
        if (strlen($domain) > 2 || $domain === 'eu') {
            // assume all domain with length > 2 are english
            return 'en';
        } elseif (strlen($domain) == 2) {
            // country domains
            return $domain;
        }

        // no success
        return false;
    }


    /**
     * writes preferences to session
     *
     * This method is intend to write preferences to session.
     *
     * @param array $preferences The preferences to store in session
     *
     * @return  boolean TRUE on success, otherwise FALSE
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _writeSession(array $preferences)
    {
        // store preferences in session and return result
        return $this->_getSession()->set(self::$_identifier, $preferences);
    }


    /**
     * reads a previous stored locale-config from session
     *
     * This method is intend to read a previous stored locale-config from session.
     *
     * @return  mixed ARRAY with locale-settings if previously stored in session, otherwise NULL
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _readSession()
    {
        // assume empty cookie / no stored config
        $storedSettings = $this->_getSession()->get(self::$_identifier);

        // check the result for validity
        if (!$storedSettings || !$this->isValidLocaleCode($storedSettings['locale'])) {
            return null;
        }

        // return result
        return $storedSettings;
    }


    /**
     * writes preferences to a cookie
     *
     * This method is intend to write preferences to a cookie on user's client.
     *
     * @param array $preferences The preferences to store in session
     *
     * @return  boolean TRUE on success, otherwise FALSE
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _writeCookie(array $preferences)
    {
        // combine data
        $data     = implode(',', $preferences);
        $lifetime = time() + self::$_preferenceLifetime;
        $path     = '/';
        $server   = explode('.', $_SERVER['SERVER_NAME']);
        $domain   = '';

        for ($i = 2; $i > 0; --$i) {
            $domain = '.'.$server[$i].$domain;
        }

        // store preferences in cookie and return result
        return setcookie(self::$_identifier, $data, $lifetime, $path, $domain);
    }


    /**
     * reads a cookie with a previous stored locale-config (state)
     *
     * This method is intend to read a cookie with a previous stored locale-config (state).
     *
     * @return  boolean TRUE on success, otherwise FALSE
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _readCookie()
    {
        // check if is set
        if (isset($_COOKIE[self::$_identifier])) {
            $storedSettings = explode(',', $_COOKIE[self::$_identifier]);

            // locale
            if ($this->isValidLocaleCode($storedSettings[0])
                && $this->isValidLocaleCode($storedSettings[2])
                && $this->isValidLocaleCode($storedSettings[3])
            ) {
                $locale = array(
                    'locale'   => $storedSettings[0],
                    'weight'   => (double)$storedSettings[1],
                    'language' => $storedSettings[2],
                    'country'  => $storedSettings[3]
                );
            } else {
                $locale = null;
            }
        }

        // return result
        return (isset($locale)) ? $locale : null;
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
     * @param object $config An instance of DoozR_Config_Ini holding the I18n-config
     *
     * @return  object Instance of this class
     * @access  protected
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    protected function __construct($config)
    {
        // locale defaults
        self::$_defaults = array(
            'locale'   => $config->general->default_locale(),
            'language' => $config->general->default_language(),
            'country'  => $config->general->default_country(),
            'weight'   => $config->general->default_weight()
        );

        // a collection of locales available
        self::$_availableLocales = explode(',', $config->general->available_locale());

        // get "prefered-locale"-stores in correct order
        self::$_stores = explode(',', $config->user->stores());

        // get lifetime for stored preference data
        self::$_preferenceLifetime = $config->user->preference_lifetime();

        // the identifier for stores
        self::$_identifier = $config->user->identifier();
    }

    /*******************************************************************************************************************
     * \\ END MAIN CONTROL METHODS (CONSTRUCTOR AND INIT)
     ******************************************************************************************************************/
}

?>
