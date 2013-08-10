<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Request Securitlayer
 *
 * Securitylayer.php - Securitylayer for Request(s) of the DoozR-Framework.
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
 * @package    DoozR_Request
 * @subpackage DoozR_Request_Securitylayer
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

//require_once DOOZR_DOCUMENT_ROOT.'Core/Controller/Securitylayer/Htmlpurifier/HTMLPurifier.standalone.php';
//require_once DOOZR_DOCUMENT_ROOT.'Core/Controller/Securitylayer/IDS/Init.php';

/**
 * DoozR Request Securitlayer
 *
 * Securitylayer for Request(s) of the DoozR-Framework.
 *
 * @category   DoozR
 * @package    DoozR_Request
 * @subpackage DoozR_Request_Securitylayer
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://www.DoozR.org, http://htmlpurifier.org/, http://php-ids.org/
 * @see        -
 * @since      -
 */
class DoozR_Request_Securitylayer
{
    /**
     * holds the instance of HTML-Purifier
     *
     * @var object
     * @access protected
     */
    protected $htmlPurifier;

    /**
     * holds the instance of PHPIDS
     *
     * @var object
     * @access protected
     */
    protected $phpIds;

    /**
     * holds the instance of Path-Finder
     *
     * @var object
     * @access private
     */
    private $_pathFinder;

    /**
     * holds the parsed core-configuration
     *
     * @var array
     * @access private
     */
    private $_coreConfig;

    /**
     * holds the parsed ids-configuration
     *
     * @var array
     * @access private
     */
    private $_idsConfig;

    /**
     * holds the IDS (result/events incl. iterator) in an
     * easy accessible way for retrieve impacts in yet not existing
     * Request_Parameter-objects
     *
     * @var object
     * @access public
     * @static
     */
    public static $idsResult = null;


    /**
     * constructs the class
     *
     * constructor builds the class
     *
     * @return void
     *
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function __construct()
    {
        /*
        // get and store reference to Path-Finder
        $this->_pathFinder = Path_Manager::getInstance();

        // get path to core-config file
        //$doozrCoreConfigFile = $this->_pathFinder->get('DOOZR_CONFIG').'Config.ini.php';
        // init Config_Manager for Config.ini.php
        //$this->_coreConfig = DoozR_Config_Ini::getInstance($doozrCoreConfigFile);

        $this->_coreConfig = DoozR_Core::config();

        //pred($this->_coreConfig->get('FRONTCONTROLLER.SANITIZE'));

        // get instance of HTMLPurifier
        $this->_initHtmlPurifier();

        if ($this->_coreConfig->get('FRONTCONTROLLER.IDS_ENABLED')) {
            // get path to ids-config file
            $doozrIdsConfigFile = $this->_pathFinder->get('DOOZR_CONFIG').'Ids/Ids.ini.php';

            // init Config_Manager for Ids.ini.php
            $this->_idsConfig = DoozR_Config_Ini::getInstance($doozrIdsConfigFile);

            // and an instance of PHPIDS
            $this->_initPhpIds();
        }
        */
    }


    /**
     * init and instantiate HTMLPurifier
     *
     * init and instantiate HTMLPurifier for use in this class for HTML (JS,CSS) Filter
     *
     * @return void
     *
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _initHtmlPurifier()
    {
        // create default config
        $config = HTMLPurifier_Config::createDefault();

        // setup html purifier - we will work alway with given charset/encoding from config!!!
        $config->set('Core', 'Encoding', $this->_coreConfig->get('ENCODING.CHARSET'));
		$config->set('Core', 'EscapeNonASCIICharacters', $this->_coreConfig->get('FRONTCONTROLLER.SANITIZE_ESCAPE_NON_ASCII'));
        $config->set('HTML', 'TidyLevel', $this->_coreConfig->get('FRONTCONTROLLER.SANITIZE_TIDYLEVEL'));

        // get allowed HTML-Tags from config
		$allowedHtmlTags = $this->_coreConfig->get('FRONTCONTROLLER.SANITIZE_ALLOWED_HTML_TAGS');

        // not empty?
		if (!is_null($allowedHtmlTags) && strlen($allowedHtmlTags)) {
            $config->set('HTML', 'Allowed', $allowedHtmlTags);
		}

        // instanciate HTMLPurifier
        $this->htmlPurifier = new HTMLPurifier($config);
    }


    /**
     * init and instantiate PHPIDS
     *
     * init and instantiate PHPIDS for use in this class (Securitylayer for protecting the core)
     *
     * @return void
     *
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    private function _initPhpIds()
    {
        // build ids include path
        $idsIncludePath = $this->_pathFinder->get('DOOZR_CONTROLLER').'Securitylayer/';

        // add path to PHP-IDS to PHP include path
        Path_Manager::addIncludePath($idsIncludePath);

        // init PHPIDS without path to config ...
        $idsInit = IDS_Init::init();

        // ... set config instead here as array -> override
        $idsInit->setConfig($this->_idsConfig->getIni(), true);

        // get allowed (IDS excluded) form fields
        $allowedHtmlFields = $this->_coreConfig->get('FRONTCONTROLLER.SANITIZE_ALLOWED_HTML_FIELDS');

        // get them as array
        $allowedHtmlFields = explode(',', $allowedHtmlFields);

        // got a result?
        if (count($allowedHtmlFields) > 0) {
			// define exceptions from config
            $idsInit->setConfig(
                array(
                    'General' => array(
                        'exceptions' => $allowedHtmlFields
                    )
                ), true);
        }

        // build parameter array for PHPIDS
        $request = array();

		if ($this->_requestSources) {
			foreach ($this->_requestSources as $requestSource) {
				$requestSourcePhpGlobal = '_'.$requestSource;
				global $$requestSourcePhpGlobal;
				$request[$requestSource] = $$requestSourcePhpGlobal;
			}
		}

        // new PHPIDS monitor
        $idsMonitor = new IDS_Monitor($request, $idsInit);

        // execute ids
        $phpIdsResult = $idsMonitor->run();

		if (!$phpIdsResult->isEmpty()) {
            // log if result wasn't empty
            $logger = DoozR_Logger::getInstance();

            $logger->log('DoozR core-protection (IDS) -> detected an impact of: '.$phpIdsResult->getImpact());

			// store result! for setting the impact later ...
			self::$idsResult = $phpIdsResult;
		}

        // TODO: connect to DoozR->logger !!!!!!!!! HERE

        // check for canceling
        $this->_hardeningCore($phpIdsResult->getImpact());

        // store last run result from PHPIDS
        $this->phpIds = $phpIdsResult;
    }


    /**
     * checks detected impact
     *
     * checks detected impact and stops all operations if impact is higher then allowed
     * it stops the execution and returns a 400 Bad Request Header to client.
     *
     * @param integer $totalImpact The total-impact (sum of all impacts of the request)
     *
     * @return void
     *
     * @access  private
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     * @todo    Check also for single impact(s) and make use of a config var (allowed single impact)
     */
    private function _hardeningCore($totalImpact)
    {
        // check overall impact and cancel if greater than allowed max (conf)
        if ($totalImpact > $this->_coreConfig->get('FRONTCONTROLLER.IDS_MAX_ALLOWED_IMPACT')) {
        	$httpHeader = 'HTTP/1.0 400 Bad Request';

        	$logger = DoozR_Logger::getInstance();

			$logger->log(
                'DoozR core-protection -> '.__CLASS__.' (IDS) detected an overall-impact of: '.$totalImpact.
                ' and send: '.$httpHeader.' to client: '.$_SERVER['REMOTE_ADDR']
			);
			header($httpHeader);
			exit;
        }
    }
}
