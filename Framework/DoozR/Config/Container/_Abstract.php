<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Config Abstract
 *
 * Abstract.php - Abstract base for Config-Manager of the DoozR Framework.
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
 * @subpackage DoozR_Config_Abstract
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
 * DoozR Config Abstract
 *
 * Abstract base for Config-Manager of the DoozR Framework.
 *
 * @category   DoozR
 * @package    DoozR_Config
 * @subpackage DoozR_Config_Abstract
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
     * holds a list (matrix) of valid config-files
     * this prevent us for unneeded fs-access'
     *
     * @var array
     * @access protected
     * @static
     */
    protected static $validConfigurationFiles = array();

    /**
     * config-file
     *
     * holds the config-file
     *
     * @var string
     * @access protected
     */
    protected $configFilename = null;

    /**
     * holds the "raw" (untouched) content of an read-in configfile
     *
     * null on initialising - used to hold the content of a read configfile
     *
     * @var mixed
     * @access protected
     */
    protected $configContentRaw = null;

    /**
     * holds the "raw" (but replaced) content of an read-in configfile
     *
     * null on initialising - used to hold the content of a read configfile
     *
     * @var mixed
     * @access protected
     */
    protected $configContentRawReplaced = null;

    /**
     * holds the "parsed" content of an read-in configfile
     *
     * null on initialising - used to hold the content of a read configfile
     *
     * @var mixed
     * @access protected
     */
    protected $configContentParsed = null;

    /**
     * holds the fingerprint of the current instance' configfiles
     *
     * @var string
     * @access private
     */
    private $_fingerprint = null;

    /**
     * holds the config as array
     *
     * null on initialising - used to hold the config-vars (array)
     *
     * @var array
     * @access protected
     */
    protected $configVars = null;

    /**
     * holds the information if we process dots in subcats or not
     *
     * @var boolean
     * @access protected
     */
    protected $parseDots;

    /**
     * holds an instance of the cache-manager
     *
     * this var holds an instance of the cache_manager (PEAR) which manages the
     * caching of content
     *
     * @var object
     * @access protected
     */
    protected $cache;

    /**
     * holds the transformation-information (from -> to)
     *
     * empty array on initialising - used to hold transformation information.
     * This array holds information about what string get transformed to what.
     * Means -->> array['foo']['bar'] = str_replace('foo', 'bar', CONFIGVAR);
     *
     * @var array
     * @access protected
     * @static
     */
    protected static $replacementMatrix = array();

    /**
     * holds the status of is-result-from-cache for the current requested config-file
     *
     * @var boolean
     * @access protected
     */
    protected $cacheHit = false;


    /**
     * constructs the class
     *
     * constructor builds the class
     *
     * @param string $configFilename The configurationfile (full path) to load/parse
     * @param string $fingerprint    Already generated fingerprint from getInstance used as identifier
     *
     * @access  protected
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    protected function __construct(DoozR_Path $path, DoozR_Logger $logger, $configFilename, $fingerprint)
    {
        // call parents constructor
        parent::__construct();

        // store given fingerprint
        $this->_fingerprint = $fingerprint;

        // get cache module without auto run container
        $this->cache = DoozR_Loader_Moduleloader::load('cache', array(DOOZR_UNIX));

        // set config's cache lifetime to unlimited
        $this->cache->setLifetime(86400);

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
                throw new DoozR_Core_Exception(
                    __METHOD__.' - Error while initializing cache! Neither file nor memcache container can be used.',
                    null,
                    $e
                );
            }
        }

        // auto-setup the identifier
        $this->cache->setID($this->_fingerprint);

        // check if content of configfile is already cached?
        if ($this->cache->isCached()) {
            $content = $this->cache->read();
            $content = $content[1];
        } else {
            $content = null;
        }

        // store the filename for access in this class
        $this->configFilename = $configFilename;

        // we use cached content (processed PHP-code) if it was already parsed...
        if ($content) {
            $this->cacheHit = true;
            $content = unserialize($content);

            // map it for further access like it would be created without cache
            $this->configContentRaw         = $content['configRaw'];
            $this->configContentRawReplaced = $content['configRawReplaced'];
            $this->configContentParsed      = $content['configParsed'];
        } else {

            // add default transforms
            $this->_attachDefaultReplacements();

            // read file in
            $this->configContentRaw = $this->openConfigFile($this->configFilename);
        }
    }


    /**
     * returns the parsed content of the configfile
     *
     * This method is intend to return the parsed content of the configfile.
     *
     * @return  mixed Null if configfile was invalid, otherwise the content of the file as processed array
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function getConfig()
    {
        return $this->configContentParsed;
    }


    /**
     * sets the configurations content
     *
     * This method is intend to set the configurations content.
     *
     * @param array $config The configuration as array
     *
     * @return  boolean True if everything wents fine, otherwise false
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function setConfig($config)
    {
        return ($this->configContentParsed = $config);
    }


    /**
     * attaches the default transformations
     *
     * This method is called from constructor to attach the default transformations
     * like DOCUMENT_ROOT or SERVERNAME (DOMAIN). The so called "transforms" are applied to each
     * part of the config (section or value) and replaces placeholder with runtime information(s).
     *
     * @return  void
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _attachDefaultReplacements()
    {
        // doc-root / document-root
        $this->attachReplacement('{DOOZR_DOCUMENT_ROOT}', DOOZR_DOCUMENT_ROOT);

        // app-root / application-root
        $this->attachReplacement('{DOOZR_APP_ROOT}', DOOZR_APP_ROOT);

        // server-name
        $this->attachReplacement(
        	'{DOOZR_SERVERNAME}',
            (isset($_SERVER['SERVER_NAME'])) ? $_SERVER['SERVER_NAME'] : 'SERVER_NAME'
        );
    }


    /**
     * attaches a concrete transform (replace X with Y || from => to)
     *
     * This method is used to attach a transformation (replacement) for a given string
     *
     * @param string $from The value which should be replaced
     * @param string $to   The value which should be inserted for every occurance of $from
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function attachReplacement($from, $to)
    {
        self::$replacementMatrix[$from] = $to;
    }


    /**
     * replaces the current defined replaces in a given mixed var
     *
     * This method is intend to replace the current defined replaces in a given mixed var
     *
     * @param mixed $mixed The content to replace the replacements in
     *
     * @return  mixed The content including the concrete replaces
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    protected function doReplacement($mixed)
    {
        // only string work for current versions
        if (is_string($mixed)) {
            foreach (self::$replacementMatrix as $search => $replace) {
                $mixed = str_replace($search, $replace, $mixed);
            }
        } else {
            throw new DoozR_Base_Exception(
                'Replacement can currenty only be applied on strings! Found: "'.gettype($mixed).'"'
            );
        }

        // return the replaced content
        return $mixed;
    }


    /**
     * opens and read a config-file
     *
     * This method is intend to open and read a config-file.
     *
     * @param string $configFilename The filename (full path) of the config-file to read in
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function openConfigFile($configFilename)
    {
        // validate the given file
        if (!$this->validateConfigFile($configFilename)) {
            return null;
        }

        // read the file
        return file($configFilename);
    }


    /**
     * validates (existence) a config-file
     *
     * This method is intend to validate a config-file.
     *
     * @param string $configFilename The filename (full path) of the config-file to validate
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    protected function validateConfigFile($configFilename)
    {
        // check if already markes as valid
        if (!isset(self::$validConfigurationFiles[self::fingerprint($configFilename)])) {
            if (!file_exists($configFilename)) {
                throw new EConfig_Manager_Exception_File_NotFound($configFilename);
            }
        }

        // valid
        return true;
    }


    /**
     * creates a fingerprint for a given mixed var
     *
     * This method is intend to create a fingerprint for a given mixed var.
     *
     * @param string $mixed The var to fingerprint
     *
     * @return  string The created fingerprint
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @static
     */
    protected static function fingerprint($mixed)
    {
        return md5($mixed);
    }
}

?>
