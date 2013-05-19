<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - I18n - Service - Interface - Base
 *
 * Base.php - I18n Translation Base Interface
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
 * @package    DoozR_Service
 * @subpackage DoozR_Service_I18n
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
 * DoozR - I18n - Service - Interface - Base
 *
 * I18n Translation Base Interface
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_I18n
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_I18n_Service_Interface_Base extends DoozR_Base_Class_Singleton
{
    /**
     * the translation table collection (for all locale!)
     * key = crc of locale + namespace(s)
     *
     * @var array
     * @access protected
     * @static
     */
    protected static $translationTables = array();

    /**
     * the translations
     *
     * @var array
     * @access protected
     * @static
     */
    protected static $translations = array();

    /**
     * module cache
     *
     * @var object
     * @access private
     * @static
     */
    protected static $cache;


    /*******************************************************************************************************************
     * // BEGIN PUBLIC INTERFACES
     ******************************************************************************************************************/

    /**
     * starts the initializing of given locale and namespace
     *
     * This method is intend to start the initializing of given locale and namespace and return the corresponding key.
     *
     * @param string $locale    The locale to init
     * @param string $namespace The namespace to init
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The key (identifier) for the created translationstable
     * @access public
     */
    public function initLocaleNamespace($locale, $namespace)
    {
        // get checksum for translation-table + namespace(s)
        $crc = checksum($locale, $namespace);

        // if the table wasn't loaded before
        if (!isset(self::$translationTables[$crc])) {
            // caching enabled?
            if (self::$cache) {
                // identify cache content by internal crc
                self::$cache->setId($crc);

                // check if content of translationtable is already cached?
                $cachedContent = self::$cache->isCached();
            } else {
                // no cache = no result
                $cachedContent = false;
            }

            // if we did not receive a valid result from cache parse file(s)
            if ($cachedContent === false) {

                if (!method_exists($this, 'buildTranslationtable')) {
                    throw new Exception('EXCEPTION_MODULE_DOOZR_I18N', 123456);

                } else {

                    // build translationtable
                    $translationTable = $this->buildTranslationtable($locale, $namespace, $crc);

                    // cache translationtable
                    self::$cache->create($translationTable);

                    // set local
                    self::$translationTables[$crc] = $translationTable;
                }
            } else {
                // put the cached content into local translationtable
                self::$translationTables[$crc] = $cachedContent;
            }
        }

        // finally return the crc (key/identifier)
        return $crc;
    }

    /*******************************************************************************************************************
     * \\ END PUBLIC INTERFACES
     ******************************************************************************************************************/

    /*******************************************************************************************************************
     * // BEGIN MAIN CONTROL METHODS (CONSTRUCTOR AND INIT)
     ******************************************************************************************************************/

    /**
     * This method is intend to act as constructor.
     *
     * @param string $config The config for this type of interface
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object Instance of this class
     * @access protected
     */
    protected function __construct($config)
    {
        // if cache enabled - get module cache and setup
        if (!self::$cache && ($config['cache']['enabled'] !== null)) {
            // get module cache
            self::$cache = DoozR_Loader_Serviceloader::load('cache', array(DOOZR_UNIX, $config['cache']['enabled']));

            // set its lifetime
            self::$cache->setLifetime($config['cache']['lifetime']);
        }
    }

    /*******************************************************************************************************************
     * \\ END MAIN CONTROL METHODS (CONSTRUCTOR AND INIT)
     ******************************************************************************************************************/
}

?>
