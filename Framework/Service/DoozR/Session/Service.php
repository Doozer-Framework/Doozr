<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Session - Service
 *
 * Service.php - Session Facade to PHP's session implementation. Brings a lot of required
 * stuff like support for en-/decrypting sessions (for both variables and content - both
 * configurable) with high secure encryption standard AES256! Also brings session fixation
 * protection with a configurable regenerate cycle, IP-binding, and so on ...
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
 * @subpackage DoozR_Service_Session
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Service/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Crud/Interface.php';
require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Session/Service/Interface.php';

/**
 * DoozR - Session - Service
 *
 * Session Facade to PHP's session implementation. Brings a lot of required
 * stuff like support for en-/decrypting sessions (for both variables and content - both
 * configurable) with high secure encryption standard AES256! Also brings session fixation
 * protection with a configurable regenerate cycle, IP-binding, and so on ...
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Session
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @service    Singleton
 * @inject     DoozR_Registry:DoozR_Registry identifier:getInstance type:constructor position:1
 */
class DoozR_Session_Service extends DoozR_Base_Service_Singleton
    implements
    DoozR_Base_Crud_Interface,
    DoozR_Session_Service_Interface
{
    /**
     * Instance of DoozR_Crypt
     *
     * @var DoozR_Crypt
     * @access protected
     */
    protected $crypt;

    /**
     * Instance of DoozR_Logger
     *
     * @var DoozR_Logger
     * @access protected
     */
    protected $logger;

    /**
     * Session-Id
     *
     * @var string
     * @access protected
     */
    protected $id;

    /**
     * IP-address of the client
     *
     * @var string
     * @access protected
     */
    protected $clientIp;

    /**
     * User-Agent of the client
     *
     * @var string
     * @access protected
     */
    protected $userAgent;

    /**
     * Domain used for cookie(s)
     *
     * @var string
     * @access protected
     */
    protected $domain;

    /**
     * The path used for session cookie(s)
     *
     * @var integer
     * @access protected
     */
    protected $path = self::DEFAULT_BIND_PATH_PATH;

    /**
     * Lifetime of Session.
     * Default is 3600 (1h = 60 minutes * 60 seconds)
     *
     * @var integer
     * @access protected
     */
    protected $lifetime = self::DEFAULT_LIFETIME;

    /**
     * Garbage collector time.
     *
     * @var integer
     * @access protected
     */
    protected $gcTime = self::DEFAULT_GCTIME;

    /**
     * Session identifier name
     *
     * @var string
     * @access protected
     */
    protected $identifier = self::DEFAULT_IDENTIFIER;

    /**
     * Contains status of ssl secure
     *
     * @var boolean
     * @access protected
     */
    protected $ssl = self::DEFAULT_SEC_FLAG_SSL;

    /**
     * Contains status of httpOnly secure
     *
     * @var boolean
     * @access protected
     */
    protected $httpOnly = self::DEFAULT_SEC_FLAG_HTTPONLY;

    /**
     * Contains obfuscate status for session (-identifier/-name)
     * If set to TRUE the session name/id will be obfuscated, if FALSE
     * it is set like set in:
     * @see: $_identifier
     *
     * @var boolean
     * @access protected
     */
    protected $obfuscate = self::DEFAULT_OBFUSCATE;

    /**
     * holds status of session encrypting
     *
     * @var boolean
     * @access protected
     */
    protected $encrypt = self::DEFAULT_ENCRYPT;

    /**
     * Contains true if cookies are used for storing session
     * otherwise false if not
     *
     * @var boolean
     * @access protected
     */
    protected $useCookies = self::DEFAULT_USE_COOKIES;

    /**
     * Contains the current status of session.
     * True = session already started by calling session_start(),
     * False = modifications of parameter still possible
     *
     * @var boolean
     * @access protected
     */
    protected $started = false;

    /**
     * Contains the private key for en-/ and decryption
     *
     * @var string
     * @access protected
     */
    protected $privateKey;

    /**
     * Contains the count of cycles on which the Session-Id
     * get regenerated. Default = 0 = never/disabled
     *
     * @var integer
     * @access protected
     */
    protected $regenerateCycles = self::DEFAULT_REGENERATE_CYCLES;

    /**
     * Contains the methods to execute after an event like "start" is triggered.
     * @example: "start"-calls are executed right after a session was started.
     *
     * @var array
     * @access protected
     */
    protected $callStack = array(
        'start' => array()
    );

    /**
     * Contains status of supporting setcookie features added with PHP 5.2
     * like http-only. Default assumes not supported = false
     *
     * @var boolean
     * @access protected
     */
    protected $flags = array(
        'httpOnly' => false,
        'ssl'      => false
    );

    /**
     * The default identifier (session name)
     *
     * @var string
     * @access const
     */
    const DEFAULT_IDENTIFIER = 'DoozR';

    /**
     * The default lifetime (of session)
     *
     * @var integer
     * @access const
     */
    const DEFAULT_LIFETIME = 3600;

    /**
     * The default garbage-collector timeout
     *
     * @var integer
     * @access const
     */
    const DEFAULT_GCTIME = 3600;

    /**
     * The default status of obfuscate
     *
     * @var boolean
     * @access const
     */
    const DEFAULT_OBFUSCATE = false;

    /**
     * The default status of cookie usage for session transmission
     * between client and server
     *
     * @var boolean
     * @access const
     */
    const DEFAULT_USE_COOKIES = true;

    /**
     * The default status of binding session to clients IP
     *
     * @var boolean
     * @access const
     */
    const DEFAULT_BIND_IP = false;

    /**
     * The default count of octets used for checking clients IP.
     * Default is 4 = XXX.XXX.XXX.XXX - you can also use a lower
     * value like 2 = XXX.XXX e.g. if a proxy (AOL) is between
     * client and server.
     *
     * @var integer
     * @access const
     */
    const DEFAULT_BIND_IP_OCTETS = 4;

    /**
     * The identifier used when storing clients IP in session
     *
     * @var string
     * @access const
     */
    const DEFAULT_BIND_IP_IDENTIFIER = 'DOOZR_SEC_FLAG_BOUND_TO';

    /**
     * The default status of bind session to domain
     *
     * @example:
     * TRUE = set cookie validity for active domain (www.example.tld)
     * FALSE = disabled
     *
     * @var boolean
     * @access const
     */
    const DEFAULT_BIND_DOMAIN = true;

    /**
     * The default mode for binding to domain
     *
     * @example:
     * domain = use full domain (www.example.tld)
     * subdomain = use subdomain (.example.tld) - so all subdomains can access cookie
     *
     * @var string
     * @access const
     */
    const DEFAULT_BIND_DOMAIN_MODE = 'domain';

    /**
     * The default status of binding to a specific path
     * TRUE to enable (default = "/")
     * FALSE to disable
     *
     * @var boolean
     * @access const
     */
    const DEFAULT_BIND_PATH = false;

    /**
     * The default path to bind to
     *
     * @example:
     * '/'          = bind to root of current domain (www.example.tld/)
     * '/subfolder' = bind to subfolder '/subfolder' (www.example.tld/subfolder)
     *
     * @var string
     * @access const
     */
    const DEFAULT_BIND_PATH_PATH = '/';

    /**
     * The default status of encryption
     * TRUE = enable encryption
     * FALSE = disable encryption
     *
     * @var boolean
     * @access const
     */
    const DEFAULT_ENCRYPT = false;

    /**
     * The default cipher for encryption
     * As default this class use AES encryption container
     *
     * @var string
     * @access const
     */
    const DEFAULT_ENCRYPT_CIPHER = 'Aes';

    /**
     * The default encoding for encrypted values
     * Cause due to the encryption the encrypted string can contain
     * control-chars which could break the processing.
     * So we use an encoding for encrypted strings.
     *
     * @var string
     * @access const
     */
    const DEFAULT_ENCRYPT_ENCODING = 'uuencode';

    /**
     * The default regeneration cycles
     * This value defines on which cycle the next session-id regeneration process starts.
     *
     * @example
     * 0     = disabled
     * 1     = on each request
     * 2 - X = on each 2nd or X-st/nd/rd request
     *
     * @var integer
     * @access const
     */
    const DEFAULT_REGENERATE_CYCLES = 0;

    /**
     * The identifier for storing/reading active cycle in/from session
     *
     * @var string
     * @access const
     */
    const DEFAULT_REGENERATE_CYCLES_IDENTIFIER = 'DOOZR_REGENERATE_CYCLE';

    /**
     * The default flag status for SSL support
     *
     * @var boolean
     * @access const
     */
    const DEFAULT_SEC_FLAG_SSL = false;

    /**
     * The default flag status for HTTPOnly support
     *
     * @var boolean
     * @access const
     */
    const DEFAULT_SEC_FLAG_HTTPONLY = false;


    /**
     * replacement for __construct
     *
     * This method is intend as replacement for __construct
     * PLEASE DO NOT USE __construct() - make always use of __tearup()!
     *
     * Session-Id could be overridden cause of a bug in SWF-Upload for example.
     * SWFUpload/Multiupload-SWF lost session_id while uploading: http://code.google.com/p/swfupload/wiki/FAQ
     *
     * @param string  $sessionId  The session-Id to use as predefined
     * @param boolean $autoInit   TRUE to automatically start session, FALSE to do not
     * @param double  $phpVersion The PHP-Version this instance of session module running on
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __tearup($sessionId = null, $autoInit = false, $phpVersion = DOOZR_PHP_VERSION)
    {
        // get instance of logger
        $this->logger = (isset(self::getRegistry()->logger)) ? self::getRegistry()->logger : null;

        // store session-id always
        $this->setId($sessionId);

        // store settings from PHP global config
        $this->setClientIp($_SERVER['REMOTE_ADDR']);
        $this->setUserAgent($_SERVER['HTTP_USER_AGENT']);
        $this->setDomain($_SERVER['SERVER_NAME']);

        // check for newer features
        // PHP-Version >= 5.2
        $this->setFlagStatus('httpOnly', ($phpVersion >= 5.2));
        // PHP-Version >= 4.0.4 (for us = 4.1)
        $this->setFlagStatus('ssl', ($phpVersion >= 4.1));

        // automatic start session?
        if ($autoInit === true || self::getRegistry()->config->session->autoinit()) {
            // start initialization with config from core
            $this->autoInit(
                self::getRegistry()->config->session()
            );
        }
    }

    /**
     * Initialize complete basic setup from config
     *
     * This method is intend to initialize the complete basic setup from config.
     *
     * @param mixed $config The config as object (stdClass) or as array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function autoInit($config)
    {
        // Convert to object e.g. for case if user passes array
        if (is_array($config)) {
            $config = array_to_object($config);
        }

        // Setup basics
        $this->configure(
            $config->identifier,
            $config->lifetime,
            $config->gcTime
        );

        // Setup security if enabled
        if ($config->security->enabled) {

            // Security features
            $this->security(
                $config->security->ssl,
                $config->security->httponly
            );

            // Configure obfuscation of Session-Identifier
            if ($config->security->obfuscate) {
                $this->enableObfuscation();
            } else {
                $this->disableObfuscation();
            }

            // IP binding
            if ($config->security->bind->ip->enabled) {
                $this->bindToIp($config->security->bind->ip->octets);
            }

            // Domain binding
            if ($config->security->bind->domain->enabled) {
                $this->bindToDomain($config->security->bind->domain->mode);
            }

            // Path binding
            if ($config->security->bind->path->enabled) {
                $this->bindToPath($config->security->bind->path->path);
            }

            // Encryption
            if ($config->security->encryption->enabled) {
                // enable encryption of session
                $this->enableEncryption(
                    $config->security->encryption->cipher,
                    $config->security->encryption->encoding
                );

            } else {
                $this->disableEncryption();
            }

            // Regeneration of Session-Id
            $this->setRegenerateCycles($config->security->regenerate);
        }

        // go for it
        $this->start();
    }

    /**
     * Setup the basic configuration
     *
     * This method is intend to set the basic configuration up.
     *
     * @param string  $identifier The identifier to use for session
     * @param integer $lifetime   The lifetime of the session
     * @param integer $gcTime     The timeout in seconds of garbage collector
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function configure(
        $identifier,
        $lifetime,
        $gcTime
    ) {
        // set the identifier
        $this->setIdentifier($identifier);

        // set the identifier from configuration
        $this->setLifetime($lifetime);
        ini_set('session.use_cookies', ($this->useCookies) ? 1 : 0);
        ini_set('session.cookie_lifetime', $this->lifetime);

        // set garbage collector timeout
        $this->setGcTime($gcTime);
        ini_set('session.gc_maxlifetime', $this->gcTime);
    }

    /**
     * Setup security settings of session
     *
     * This method is intend to setup the session security.
     *
     * @param boolean $ssl      TRUE to enable ssl flag for cookies/session, FALSE to do not
     * @param boolean $httpOnly TRUE to enable httpOnly flag for cookies/session, FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function security($ssl = self::DEFAULT_SEC_FLAG_SSL, $httpOnly = self::DEFAULT_SEC_FLAG_HTTPONLY)
    {
        // ssl (secured transmission)
        if ($ssl) {
            // set ssl to enabled if 1) configured TRUE and supported by server!
            $this->setSsl(
                is_ssl() && $this->getFlagStatus('ssl')
            );
        } else {
            $this->setSsl(false);
        }

        // http-only (e.g. prevent JavaScript access to cookie ...)
        if ($httpOnly) {
            // set ssl to enabled if 1) configured TRUE and supported by server!
            $this->setHttpOnly(
                $this->getFlagStatus('httpOnly')
            );
        } else {
            $this->setHttpOnly(false);
        }
    }

    /**
     * binds the session to the current IP of user (client)
     *
     * This method is intend to bind the session to the current IP of user (client).
     * Through changing $octets to a lower value you are able to bind to an ip address
     * also if the user is using a proxy-connection like AOL-users do.
     *
     * @param integer $octets The count of octets to use for binding session to ip
     * @param string  $ip     The Ip-Address of the client to bind session to
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function bindToIp($octets = self::DEFAULT_BIND_IP_OCTETS, $ip = null)
    {
        // if no ip given get previously stored client ip
        if (!$ip) {
            $ip = $this->getClientIp();
        }

        // check for lowered octet count
        if ($octets < 4) {
            // ... and if -> construct ip by given octet count
            $ip = $this->getDotParts($ip, $octets);
        }

        // try to read from session
        try {
            $ipFromSession = $this->get(self::DEFAULT_BIND_IP_IDENTIFIER);

        } catch (DoozR_Session_Service_Exception $e) {
            $ipFromSession = null;
        }


        // at this point the ip is in format configured (X-octets)
        if ($ipFromSession !== null) {
            // found ip in session! try to validate and destroy if suspicious
            if ($ipFromSession != $ip) {
                $this->log('Session seems to be hijacked! Destroying session and closing connection!');
                $this->destroy();
            }
        } else {
            $this->set(self::DEFAULT_BIND_IP_IDENTIFIER, $ip);
        }

        // return success
        return true;
    }

    /**
     * Binds the session (cookie) to the passed Domain
     *
     * This method is intend to bind the session (cookie) to the passed domain.
     *
     * @param string $mode   The mode. Can be "domain" (uses the full domain) or "subdomain" uses (.subdomain.tld)
     * @param string $domain The domain to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function bindToDomain($mode = self::DEFAULT_BIND_DOMAIN_MODE, $domain = null)
    {
        // if no domain passed through - get the previously stored one
        if (!$domain) {
            $domain = $this->getDomain();
        }

        // all advanced features only available for named hosts - no ip!
        if (!is_ip($domain)) {
            switch (strtolower($mode)) {
            case 'subdomain':
                $domain = '.'.$this->getDotParts($domain, 2, 'rtl');
                break;
            }
        }

        // store
        $this->setDomain($domain);
    }

    /**
     * Binds the session (cookie) to the passed Path
     *
     * This method is intend to bind the session (cookie) to the passed path.
     *
     * @param string $path The path to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function bindToPath($path = self::DEFAULT_BIND_PATH_PATH)
    {
        $this->setPath($path);
    }

    /**
     * Enables encryption
     *
     * This method is intend to enable the encryption for this instance including cipher and encoding.
     *
     * @param string $cipher   The algorithm (container of Service crypt) used for encryption
     * @param string $encoding The encoding to use for safety handable encrypted value
     *                         (can be either "uuencode" or "base64")
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function enableEncryption($cipher = self::DEFAULT_ENCRYPT_CIPHER, $encoding = self::DEFAULT_ENCRYPT_ENCODING)
    {
        // Get security
        include_once DOOZR_DOCUMENT_ROOT . 'DoozR/Security.php';

        // Get module crypt
        $this->crypt = DoozR_Loader_Serviceloader::load('crypt', $cipher, $encoding);

        // Store private key for en-/decryption
        $this->privateKey = DoozR_Security::getPrivateKey();

        // Set key to crypt-module
        $this->crypt->setKey($this->privateKey);

        // set enabled
        $this->encrypt = true;
    }

    /**
     * Disables encryption
     *
     * This method is intend to disable the encryption for this instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function disableEncryption()
    {
        $this->encrypt = false;
    }

    /**
     * Checks the current encryption status
     *
     * This method is intend to check the status of encryption.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if encryption is enabled, otherwise FALSE if not
     * @access public
     */
    public function useEncryption()
    {
        return ($this->encrypt === true);
    }

    /**
     * Starts the session
     *
     * This method is intend to start the session after setup was done.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @throws DoozR_Session_Service_Exception
     */
    public function start()
    {
        // define the name of the session (this will replace the PHPSESSID identifier for cookie ...)
        session_name(
            $this->getIdentifier()
        );

        // set session-id if forced
        $sessionId = $this->getId();

        if ($sessionId !== null) {
            session_id($sessionId);
            $this->setId($sessionId);

        } else {
            $sessionId = session_id();
        }

        // set params for cookie!
        session_set_cookie_params(
            $this->getLifetime(),
            $this->getPathToClass(),
            $this->getDomain(),
            $this->getSsl(),
            $this->getHttpOnly()
        );

        // start the session
        $this->setStarted(
            session_start()
        );

        // for the uncommon case, that the session could not be initialized
        if (!$this->wasStarted()) {
            throw new DoozR_Session_Service_Exception(
                'Session could not be started! Please check your configuration (php.ini).'
            );
        } else {
            $this->setId(session_id());
        }

        // set session-cookie-parameter
        $this->setSessionCookie(
            $this->getIdentifier(),
            ($sessionId) ? $sessionId : session_id(),
            $this->getLifetime(),
            $this->getPathToClass(),
            $this->getDomain(),
            $this->getSsl(),
            $this->getHttpOnly()
        );

        // check stack for functions to execute
        $callStack = $this->callStack['start'];

        // iterate over callstack
        foreach ($callStack as $call) {
            call_user_func(array($call[0], $call[1]));
        }

        // log created session-id and defined session parameter (cookie params)
        $this->log(
            'Session-Id: '.$this->getId().' Session-Cookie-Parameter: '.
            var_export(
                session_get_cookie_params(),
                true
            )
        );
    }

    /**
     * Handle the regeneration process
     *
     * This method is intend to handle the regeneration process.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @throws DoozR_Session_Service_Exception
     */
    public function handleRegenerate()
    {
        // check for invalid call
        if (!$this->wasStarted()) {
            throw new DoozR_Session_Service_Exception(
                'Error while handling Session regeneration! handleRegenerate() must be called after session start().'
            );
        }

        // check if session must be regenerated
        if (($currentCycle = $this->mustRegenerate()) === true) {
            // do regenerate
            $this->regenerate();

            // reset cycle count cause after regenerate we start from scratch
            $currentCycle = 0;
        }

        // store current cycle count in session
        $this->set(self::DEFAULT_REGENERATE_CYCLES_IDENTIFIER, $currentCycle);
    }

    /**
     * Set status of session started to passed value
     *
     * This method is intend to set the status of session started to passed value.
     *
     * @param boolean $status TRUE if session was started, FALSE if not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setStarted($status)
    {
        $this->started = $status;
    }

    /**
     * Returns status of session started
     *
     * This method is intend to return the status of session started.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if session was already started, FALSE if not
     * @access public
     */
    public function wasStarted()
    {
        return $this->started;
    }

    /**
     * Sets the status of a given flag
     *
     * This method is intend to set the status of a given flag.
     *
     * @param string  $flag   The flag to set status for
     * @param boolean $status The status TRUE or FALSE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setFlagStatus($flag, $status)
    {
        if (is_array($flag)) {
            foreach ($flag as $singleFeature) {
                $this->flags[$singleFeature] = $status;
            }
        } else {
            $this->flags[$flag] = $status;
        }
    }

    /**
     * Returns the status of a given flag
     *
     * This method is intend to return the status of a given flag.
     *
     * @param string $flag The flag to return status for, NULL to return all flags as array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Boolean TRUE if flag is supported, otherwise FALSE if not - Array of all flags
     * @access public
     */
    public function getFlagStatus($flag = null)
    {
        if (!$flag || !isset($this->flags[$flag])) {
            return $this->flags;
        } else {
            return $this->flags[$flag];
        }
    }

    /**
     * Sets the client IP
     *
     * This method is intend to set the IP of the current client.
     *
     * @param string $clientIp The IP to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setClientIp($clientIp)
    {
        $this->clientIp = $clientIp;
    }

    /**
     * Returns the client IP
     *
     * This method is intend to return the IP of the client.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The IP of the client
     * @access public
     */
    public function getClientIp()
    {
        return $this->clientIp;
    }

    /**
     * Sets the User-Agent
     *
     * This method is intend to set the User-Agent of the current client.
     *
     * @param string $userAgent The user agent to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    /**
     * Returns the User-Agent
     *
     * This method is intend to return the User-Agent of the current client.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The user-agent
     * @access public
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Sets the Domain of the server
     *
     * This method is intend to set the domain the current app is running on.
     *
     * @param string $domain The domain to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * Returns the Domain
     *
     * This method is intend to return the Domain to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The domain
     * @access public
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Enables the obfuscation
     *
     * This method is intend to enable the obfuscation.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function enableObfuscation()
    {
        $this->obfuscate = true;
    }

    /**
     * Disables the obfuscation
     *
     * This method is intend to disable the obfuscation.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function disableObfuscation()
    {
        $this->obfuscate = false;
    }

    /**
     * Return current status of obfuscation
     *
     * This method is intend to return the current status of obfuscation.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if enabled, otherwise FALSE
     * @access public
     */
    public function useObfuscation()
    {
        return ($this->obfuscate === true);
    }

    /**
     * Sets the current status of SSL-encryption
     *
     * This method is intend to set the status of SSL-encryption.
     *
     * @param boolean $status TRUE if SSL is enabled, otherwise FALSE if not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setSsl($status)
    {
        $this->ssl = $status;
    }

    /**
     * Returns the current status of SSL-encryption
     *
     * This method is intend to return the status of SSL-encryption.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if SSL is enabled, otherwise FALSE if not
     * @access public
     */
    public function getSsl()
    {
        return $this->ssl;
    }

    /**
     * Sets the current status of HTTP-Only is supported
     *
     * This method is intend to return the status of SSL-encryption.
     *
     * @param boolean $status TRUE if HTTP-Only is supported, otherwise FALSE if not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setHttpOnly($status)
    {
        $this->httpOnly = $status;
    }

    /**
     * Returns the current status of HTTP-Only is supported
     *
     * This method is intend to return the status of HTTP-Only support.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if HTTP-Only is supported, otherwise FALSE if not
     * @access public
     */
    public function getHttpOnly()
    {
        return $this->httpOnly;
    }

    /**
     * stores a given variable with value in session
     *
     * This method is intend to store a given variable with value in session
     *
     * @param string $variable The name of the session-variable to set
     * @param mixed  $value    The value of the session-variable to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function set($variable, $value)
    {
        // translate (if encryption enabled) variable
        $variable = $this->translate($variable);

        // translate (if encryption enabled) value
        $value = $this->translate($value);

        // and store
        $_SESSION[$variable] = $value;

        // success
        return true;
    }

    /**
     * Returns a variable from session by passed name/identifier.
     *
     * @param string $variable The name of the session variable to return
     * @param null   $value    The default value to return in case of exception
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Value of the requested session variable if set, otherwise NULL
     * @access public
     * @throws DoozR_Session_Service_Exception
     */
    public function get($variable, $value = null)
    {
        // translate call for variable identifier (_translate() just encrypt the name if encryption is enabled)
        $variable = $this->translate($variable);

        // check if requestes var is set
        if (isset($_SESSION[$variable])) {
            // if session is encrypted
            if ($this->useEncryption()) {
                // get decrypted value -> decrypt -> unserialized
                $value = $this->crypt->decrypt($_SESSION[$variable], $this->privateKey);
            } else {
                // return plain from session
                $value = $_SESSION[$variable];
            }

        } else {
            throw new DoozR_Session_Service_Exception(
                'Session variable "' . $variable . '" could not be retrieved from session. Ensure that it is set first.'
            );
        }

        // return default = not set = null
        return $value;
    }

    /**
     * returns the isset status of variable
     *
     * This method is intend to return the isset status of variable
     *
     * @param string $variable The name of the session-variable to check if set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if variable is set, otherwise FALSE
     * @access public
     */
    public function issetVariable($variable)
    {
        // translate the variable-name
        $variable = $this->translate($variable);

        // return the isset status of given variable
        return isset($_SESSION[$variable]);
    }

    /**
     * unsets a session-variable
     *
     * This method is intend to unset a session-variable
     *
     * @param string $variable The name of the session-variable to unset
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function unsetVariable($variable)
    {
        // translate variable name
        $variable = $this->translate($variable);

        // unset
        unset($_SESSION[$variable]);

        // return status
        return (!$this->issetVariable($variable));
    }

    /**
     * Sets the Session-Id
     *
     * This method is intend to set the Session-Id used for current session.
     *
     * @param mixed $sessionId The Session-Id to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function setId($sessionId)
    {
        $this->id = $sessionId;
    }

    /**
     * Returns the current active Session-Id
     *
     * This method is intend to return the current active Session-Id.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The current active Session-Id
     * @access public
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the identifier
     *
     * This method is intend to set the identifier.
     *
     * @param string $identifier The identifier to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @throws DoozR_Session_Service_Exception
     */
    public function setIdentifier($identifier)
    {
        // check for already started session first
        if ($this->wasStarted()) {
            throw new DoozR_Session_Service_Exception(
                'Identifier cannot be changed! The identifier (session-name) must be set BEFORE start() is called.'
            );
        }

        // store identifier
        $this->identifier = $identifier;
    }

    /**
     * Returns the identifier
     *
     * This method is intend to return the identifier.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The identifier to return
     * @access public
     */
    public function getIdentifier()
    {
        // Get identifier
        $identifier = 'aloha'; //$this->identifier;

        // Who is requesting (fingerprint client)
        $allHeaders = getallheaders();

        // Get arguments
        /*
        $headers = array(
            (isset($allHeaders['USER_AGENT']))      ? $allHeaders['USER_AGENT']      : null,
            (isset($allHeaders['ACCEPT']))          ? $allHeaders['ACCEPT']          : null,
            (isset($allHeaders['ACCEPT_LANGUAGE'])) ? $allHeaders['ACCEPT_LANGUAGE'] : null,
            (isset($allHeaders['ACCEPT_ENCODING'])) ? $allHeaders['ACCEPT_ENCODING'] : null,
        );
        */
        $headers = array();

        // If identifier must be obfuscated
        if ($this->useObfuscation()) {
            $identifier = $this->generateHash(
                $identifier.
                $this->generateFingerprint(
                    $this->getClientIp(),
                    $headers
                )
            );
        }

        //
        return $identifier;
    }

    /**
     * Returns fingerprint for any type of input/argument
     *
     * This method is intend to fingerprint any input. The count of input is unlimited.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Fingerprint for input
     * @access public
     */
    public function generateFingerprint()
    {
        // Assume empty fingerprint
        $fingerprint = '';

        // Get arguments
        $arguments = func_get_args();

        foreach ($arguments as $argument) {
            $fingerprint .= serialize($argument);
        }

        return $fingerprint;
    }

    /**
     * Sets the lifetime
     *
     * This method is intend to set the lifetime.
     *
     * @param integer $lifetime The lifetime to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;
    }

    /**
     * Returns the lifetime
     *
     * This method is intend to return the lifetime.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The lifetime to return
     * @access public
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     * Sets the garbage collector timeout
     *
     * This method is intend to set the garbage collector timeout.
     *
     * @param integer $gcTime The time to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setGcTime($gcTime)
    {
        $this->gcTime = $gcTime;
    }

    /**
     * Returns the gc-timeout
     *
     * This method is intend to return the garbage collector timeout.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The garbage collector timeout in seconds
     * @access public
     */
    public function getGcLifetime()
    {
        return $this->gcTime;
    }

    /**
     * Sets the path
     *
     * This method is intend to set the path.
     *
     * @param string $path The path to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Returns the path
     *
     * This method is intend to return the path.
     *
     * @param boolean $resolveSymlinks NOT USED HERE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The active path
     * @access public
     */
    public function getPathToClass($resolveSymlinks = false)
    {
        return $this->path;
    }

    /**
     * destroys a session
     *
     * destroy an existing session! clean session garbage collector
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean True if everything wents fine, otherwise false
     * @access public
     */
    public function destroy()
    {
        // 1st we regenerate the id of session and delete the old existing data
        $this->regenerate(true);

        // return status of destroying session
        return session_destroy();
    }

    /**
     * Configures the regeneration of the Session-Id by given cycles
     *
     * This method is intend to configure the regeneration of the Session-Id by given cycles.
     *
     * @param integer $cycles The count of cycles used for regeneration. 0 = disabled,
     *                        1 = on every request ... 8 = on each 8th request ...
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setRegenerateCycles($cycles = self::DEFAULT_REGENERATE_CYCLES)
    {
        // do we need to do anything? cause a "0" means deactivated!
        $this->regenerateCycles = $cycles;

        // set only if cycle count is given
        if ($cycles > 0) {
            // execute right after session start
            $this->callStack['start']['regenerate'] = array(
                $this,
                'handleRegenerate'
            );
        } else {
            // remove from callstack if exists
            if (isset($this->callStack['start']['regenerate'])) {
                unset($this->callStack['start']['regenerate']);
            }
        }
    }

    /**
     * Returns the count of cycles on which the Session-Id must be regenerated
     *
     * This method is intend to returns the count of cycles on which the Session-Id must be regenerated.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The count of cycles
     * @access public
     */
    public function getRegenerateCycles()
    {
        return $this->regenerateCycles;
    }

    /**
     * Returns status of required session id regeneration
     *
     * This method is intend to return the status of session id must be regenerated.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if Session-Id must be regenerated, FALSE if not
     * @access public
     */
    public function mustRegenerate()
    {
        // try to read counter from session
        try {
            $currentCycle = $this->get(self::DEFAULT_REGENERATE_CYCLES_IDENTIFIER);

        } catch (DoozR_Session_Service_Exception $e) {
            $currentCycle = 0;
        }

        // increase cycle
        $currentCycle++;

        // check if current cycle is the one for regenerate
        if ($currentCycle == $this->getRegenerateCycles()) {
            // regenerate
            return true;
        }

        // do not regenerate
        return $currentCycle;
    }

    /**
     * Regenerates the whole session
     *
     * This method is intend to regenerate the session-id for current session
     *
     * @param boolean $flush TRUE to flush the whole session content, otherwise FALSE to transfer to new session
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if everything wents fine, otherwise FALSE
     * @access public
     */
    public function regenerate($flush = false)
    {
        // regenerate and fetch status
        $status = session_regenerate_id($flush);

        // store fresh created session-id
        $this->setId(session_id());

        // check for lifetime
        if ($flush) {
            // if we flush - we flush cookie too! so set date/time in past
            $lifetime = (time() - $this->getLifetime());
        } else {
            // we use the current defined lifetime
            $lifetime = $this->getLifetime();
        }

        // reset session cookie
        $status &= $this->setSessionCookie(
            $this->getIdentifier(),
            $this->getId(),
            $lifetime,
            $this->getPathToClass(),
            $this->getDomain(),
            $this->getSsl(),
            $this->getHttpOnly()
        );

        // return status
        return $status;
    }

    /**
     * Crud access for create. Creates a session entry.
     *
     * @param string $key   The key to store value under in session
     * @param mixed  $value Whatever variable type (complex or simple) to store
     *
     * @example $session->create('foo', 'bar');
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function create($key, $value)
    {
        return $this->set($key, $value);
    }

    /**
     * cRud access for read. Returns a session entry.
     *
     * @param string $key The key to return value for
     *
     * @example $session->read('foo');
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function read($key)
    {
        return $this->get($key);
    }

    /**
     * crUd access for update. Updates a session entry.
     *
     * @param string $key   The key of the variable to update value for
     * @param mixed  $value The value to set as update
     *
     * @example $session->update('foo', 'baz');
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function update($key, $value)
    {
        return $this->set($key, $value);
    }

    /**
     * cruD access for delete. Deletes a session entry.
     *
     * @param string $key The key of the variable to delete
     *
     * @example $session->delete('foo');
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     */
    public function delete($key)
    {
        return $this->unsetVariable($key);
    }

    /**
     * Forwards a message to passed logger if exists (not null)
     *
     * @param string $message The message to log
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function log($message)
    {
        if ($this->logger !== null) {
            $this->logger->log(DoozR_Logger_Constant::DEBUG, $message);
        }
    }

    /**
     * Configures the session cookie parameter
     *
     * This method is intend to configure the session cookie parameter.
     *
     * @param string  $identifier The identifier to set
     * @param string  $sessionId  The current session id
     * @param integer $lifetime   The lifetime of the session and cookie
     * @param string  $path       The path for the session cookie validity (default = / = all subfolder)
     * @param string  $domain     The domain to set
     * @param boolean $ssl        TRUE to submit cookie via SSL only, otherwise FALSE to do not so
     * @param mixed   $httpOnly   NULL (default) to do not set this flag, TRUE to set, FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if operation was successful, otherwise FALSE
     * @access protected
     */
    protected function setSessionCookie($identifier, $sessionId, $lifetime, $path, $domain, $ssl, $httpOnly = null)
    {
        // calculate concrete expiration date/time
        $expire = time() + $lifetime;

        // check for httpOnly - cause only supported >= 5.2
        if ($httpOnly != null && $this->getFlagStatus('httpOnly')) {
            session_set_cookie_params($lifetime, $path, $domain, $ssl, $httpOnly);
            $result = setcookie($identifier, $sessionId, $expire, $path, $domain, $ssl, $httpOnly);
        } else {
            session_set_cookie_params($lifetime, $path, $domain, $ssl);
            $result = setcookie($identifier, $sessionId, $expire, $path, $domain, $ssl);
        }

        return $result;
    }

    /**
     * returns an encrypted string for given input if encryption is enabled
     *
     * This method is intend to return an encrypted string for given input if encryption is enabled
     *
     * @param string $string The string to translate/encrypt
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The input encrypted or plain if encryption is enabled
     * @access protected
     */
    protected function translate($string)
    {
        // check if encryption is enabled
        if ($this->useEncryption()) {
            $string = $this->crypt->encrypt($string, $this->privateKey);
        }

        // (otherwise just) return the string
        return $string;
    }

    /**
     * Returns a part of the input string splitted by count of dots from left or right
     *
     * This method is intend to return a part of the input string splitted by count of dots from left or right.
     *
     * @param string  $string    The string to split
     * @param integer $parts     The count of parts to return
     * @param string  $direction The direction for processing. Can be either LTR or RTL.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The resulting String
     * @access protected
     */
    protected function getDotParts($string, $parts, $direction = 'ltr')
    {
        if ($direction == 'ltr') {
            return implode('.', array_slice(explode('.', $string), 0, $parts));
        } else {
            return implode('.', array_slice(explode('.', $string), (substr_count($string, '.') + 1) - $parts, $parts));
        }
    }

    /**
     * Returns hash-value for passed in phrase.
     *
     * @param string $phrase The phrase to return hash for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The resulting String
     * @access protected
     */
    protected function generateHash($phrase)
    {
        // bytes * bits
        $size = strlen($phrase) * 8;

        if (DOOZR_SECURE_HASH === true && $size >= 1024) {
            $hash = hash('sha512', $phrase);

        } elseif (DOOZR_SECURE_HASH === true && $size >= 768) {
            $hash = hash('sha256', $phrase);

        } elseif (DOOZR_SECURE_HASH === true && $size >= 512) {
            $hash = hash('sha256', $phrase);

        } elseif ($size >= 320) {
            $hash = sha1($phrase);

        } else {
            $hash = md5($phrase);

        }

        return $hash;
    }
}
