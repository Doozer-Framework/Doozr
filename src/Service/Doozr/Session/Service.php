<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Session - Service.
 *
 * Service.php - Session Facade to PHP's session implementation. Brings a lot of required
 * stuff like support for en-/decrypting sessions (for both variables and content - both
 * configurable) with high secure encryption standard AES256! Also brings session fixation
 * protection with a configurable regenerate cycle, IP-binding, and so on ...
 *
 * PHP versions 5.5
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
 *
 * Copyright (c) 2005 - 2016, Benjamin Carl - All rights reserved.
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
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Service/Singleton.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Crud/Interface.php';
require_once DOOZR_DOCUMENT_ROOT.'Service/Doozr/Session/Service/Interface.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Service/Interface.php';

use Psr\Log\LoggerInterface;
use Doozr\Loader\Serviceloader\Annotation\Inject;

/**
 * Doozr - Session - Service.
 *
 * Session Facade to PHP's session implementation. Brings a lot of required
 * stuff like support for en-/decrypting sessions (for both variables and content - both
 * configurable) with high secure encryption standard AES256! Also brings session fixation
 * protection with a configurable regenerate cycle, IP-binding, and so on ...
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 * @Inject(
 *     link   = "doozr.registry",
 *     type   = "constructor",
 *     target = "getInstance"
 * )
 */
class Doozr_Session_Service extends Doozr_Base_Service_Singleton
    implements
    Doozr_Base_Service_Interface,
    Doozr_Base_Crud_Interface,
    Doozr_Session_Service_Interface
{
    /**
     * Crypt Service.
     *
     * @var Doozr_Crypt_Service
     */
    protected $cryptService;

    /**
     * Kernel security layer.
     *
     * @var Doozr_Security
     */
    protected $security;

    /**
     * Instance of Doozr_Logging.
     *
     * @var Doozr_Logging
     */
    protected $logger;

    /**
     * Session-Id.
     *
     * @var string
     */
    protected $id;

    /**
     * IP-address of the client.
     *
     * @var string
     */
    protected $clientIp;

    /**
     * User-Agent of the client.
     *
     * @var string
     */
    protected $userAgent;

    /**
     * Domain the session get bound to.
     *
     * @var string
     */
    protected $domain;

    /**
     * Path the session get bound to.
     *
     * @var int
     */
    protected $path = self::DEFAULT_BIND_PATH_PATH;

    /**
     * Lifetime of Session.
     * Default is 3600 (1h = 60 minutes * 60 seconds).
     *
     * @var int
     */
    protected $lifetime = self::DEFAULT_LIFETIME;

    /**
     * Garbage collector time.
     *
     * @var int
     */
    protected $gcTime = self::DEFAULT_GCTIME;

    /**
     * Session identifier name.
     *
     * @var string
     */
    protected $identifier = self::DEFAULT_IDENTIFIER;

    /**
     * Contains status of ssl secure.
     *
     * @var bool
     */
    protected $ssl = self::DEFAULT_SEC_FLAG_SSL;

    /**
     * Contains status of httpOnly secure.
     *
     * @var bool
     */
    protected $httpOnly = self::DEFAULT_SEC_FLAG_HTTPONLY;

    /**
     * Whether the session identifier (session_name()) will be obfuscated or not.
     *
     * @var bool
     */
    protected $obfuscate = self::DEFAULT_OBFUSCATE;

    /**
     * holds status of session encrypting.
     *
     * @var bool
     */
    protected $encrypt = self::DEFAULT_ENCRYPT;

    /**
     * Contains true if cookies are used for storing session
     * otherwise false if not.
     *
     * @var bool
     */
    protected $useCookies = self::DEFAULT_USE_COOKIES;

    /**
     * Contains the current status of session.
     * True = session already started by calling session_start(),
     * False = modifications of parameter still possible.
     *
     * @var bool
     */
    protected $started = false;

    /**
     * Contains the private key for en-/ and decryption.
     *
     * @var string
     */
    protected $privateKey;

    /**
     * Contains the count of cycles on which the Session-Id
     * get regenerated. Default = 0 = never/disabled.
     *
     * @var int
     */
    protected $regenerateCycles = self::DEFAULT_REGENERATE_CYCLES;

    /**
     * Contains the methods to execute after an event like "start" is triggered.
     *
     * @example: "start"-calls are executed right after a session was started.
     *
     * @var array
     */
    protected $callStack = [
        'start' => [],
    ];

    /**
     * Contains status of supporting setcookie features added with PHP 5.2
     * like http-only. Default assumes not supported = false.
     *
     * @var bool
     */
    protected $flags = [
        'httpOnly' => false,
        'ssl'      => false,
    ];

    /**
     * The default identifier (session name).
     *
     * @var string
     */
    const DEFAULT_IDENTIFIER = 'Doozr';

    /**
     * The default lifetime (of session).
     *
     * @var int
     */
    const DEFAULT_LIFETIME = 3600;

    /**
     * The default garbage-collector timeout.
     *
     * @var int
     */
    const DEFAULT_GCTIME = 3600;

    /**
     * The default status of obfuscate.
     *
     * @var bool
     */
    const DEFAULT_OBFUSCATE = false;

    /**
     * The default status of cookie usage for session transmission
     * between client and server.
     *
     * @var bool
     */
    const DEFAULT_USE_COOKIES = true;

    /**
     * The default status of binding session to clients IP.
     *
     * @var bool
     */
    const DEFAULT_BIND_IP = false;

    /**
     * The default count of octets used for checking clients IP.
     * Default is 4 = XXX.XXX.XXX.XXX - you can also use a lower
     * value like 2 = XXX.XXX e.g. if a proxy (AOL) is between
     * client and server.
     *
     * @var int
     */
    const DEFAULT_BIND_IP_OCTETS = 4;

    /**
     * The identifier used when storing clients IP in session.
     *
     * @var string
     */
    const DEFAULT_BIND_IP_IDENTIFIER = 'DOOZR_SEC_FLAG_BOUND_TO';

    /**
     * The default status of bind session to domain.
     *
     * @example:
     * TRUE = set cookie validity for active domain (www.example.tld)
     * FALSE = disabled
     *
     * @var bool
     */
    const DEFAULT_BIND_DOMAIN = true;

    /**
     * The default runtimeEnvironment for binding to domain.
     *
     * @example:
     * domain = use full domain (www.example.tld)
     * subdomain = use subdomain (.example.tld) - so all subdomains can access cookie
     *
     * @var string
     */
    const DEFAULT_BIND_DOMAIN_MODE = 'domain';

    /**
     * The default status of binding to a specific path
     * TRUE to enable (default = "/")
     * FALSE to disable.
     *
     * @var bool
     */
    const DEFAULT_BIND_PATH = false;

    /**
     * The default path to bind to.
     *
     * @example:
     * '/'          = bind to root of current domain (www.example.tld/)
     * '/subfolder' = bind to subfolder '/subfolder' (www.example.tld/subfolder)
     *
     * @var string
     */
    const DEFAULT_BIND_PATH_PATH = '/';

    /**
     * The default status of encryption
     * TRUE = enable encryption
     * FALSE = disable encryption.
     *
     * @var bool
     */
    const DEFAULT_ENCRYPT = false;

    /**
     * The default cipher for encryption
     * As default this class use AES encryption container.
     *
     * @var string
     */
    const DEFAULT_ENCRYPT_CIPHER = 'Aes';

    /**
     * The default encoding for encrypted values
     * Cause due to the encryption the encrypted string can contain
     * control-chars which could break the processing.
     * So we use an encoding for encrypted strings.
     *
     * @var string
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
     * @var int
     */
    const DEFAULT_REGENERATE_CYCLES = 0;

    /**
     * The identifier for storing/reading active cycle in/from session.
     *
     * @var string
     */
    const DEFAULT_REGENERATE_CYCLES_IDENTIFIER = 'DOOZR_REGENERATE_CYCLE';

    /**
     * The default flag status for SSL support.
     *
     * @var bool
     */
    const DEFAULT_SEC_FLAG_SSL = false;

    /**
     * The default flag status for HTTPOnly support.
     *
     * @var bool
     */
    const DEFAULT_SEC_FLAG_HTTPONLY = false;

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Service entry point.
     *
     * @param string $sessionId The session-Id to use as predefined
     *
     * @internal param bool $autoInit TRUE to automatically start session, FALSE to do not
     *
     * @author   Benjamin Carl <opensource@clickalicious.de>
     */
    public function __tearup($sessionId = null)
    {
        $configuration = self::getRegistry()->getConfiguration()->session;

        $this
            ->logger(self::getRegistry()->getLogging())
            ->id($sessionId)
            ->clientIp($_SERVER['REMOTE_ADDR'])
            ->userAgent($_SERVER['HTTP_USER_AGENT'])
            ->domain($_SERVER['SERVER_NAME'])
            ->flagStatus('httpOnly', true)
            ->flagStatus('ssl', true);

        if (true === $configuration->autoinit) {
            $this
                ->init($configuration)
                ->start();
        }
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setup the basic configuration.
     *
     * @param string $identifier Identifier to use for session
     * @param int    $lifetime   Lifetime of the session
     * @param int    $gcTime     Timeout in seconds of garbage collector
     * @param bool   $useCookies TRUE to use cookies for session transport, otherwise FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function configure(
        $identifier,
        $lifetime,
        $gcTime,
        $useCookies
    ) {
        $this
            ->identifier($identifier)
            ->lifetime($lifetime)
            ->gcTime($gcTime)
            ->useCookies($useCookies);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    protected function setCryptService(Doozr_Crypt_Service $cryptService)
    {
        $this->cryptService = $cryptService;
    }

    protected function cryptService(Doozr_Crypt_Service $cryptService)
    {
        $this->setCryptService($cryptService);

        return $this;
    }

    protected function getCryptService()
    {
        return $this->cryptService;
    }

    protected function setSecurity(Doozr_Security $security)
    {
        $this->security = $security;
    }

    protected function security(Doozr_Security $security)
    {
        $this->setSecurity($security);

        return $this;
    }

    protected function getSecurity()
    {
        return $this->security;
    }

    /**
     * Basic initialization of session like configuring PHP.
     *
     * @param \stdClass|Doozr_Configuration_Hierarchy_Session $configuration The configuration as object (stdClass) or as array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function init(\stdClass $configuration)
    {
        // Setup basics
        $this->configure(
            $configuration->identifier,
            $configuration->lifetime,
            $configuration->gcTime,
            $configuration->useCookies
        );

        // Setup security if enabled
        if ($configuration->security->enabled) {

            /*
                // Security features
                $this->initSessionSecurity(
                    $configuration->security->ssl,
                    $configuration->security->httponly
                );

                // Configure obfuscation of Session-Identifier
                if ($configuration->security->obfuscate) {
                    $this->enableObfuscation();
                } else {
                    $this->disableObfuscation();
                }

                // IP binding
                if ($configuration->security->bind->ip->enabled) {
                    $this->bindToIp($configuration->security->bind->ip->octets);
                }

                // Domain binding
                if ($configuration->security->bind->domain->enabled) {
                    $this->bindToDomain($configuration->security->bind->domain->mode);
                }

                // Path binding
                if ($configuration->security->bind->path->enabled) {
                    $this->bindToPath($configuration->security->bind->path->path);
                }
            */

            // Encryption
            if ($configuration->security->encryption->enabled) {
                $this->enableEncryption(
                    $configuration->security->encryption->cipher,
                    $configuration->security->encryption->encoding
                );

            } else {
                $this->disableEncryption();
            }

            /*
                // Regeneration of Session-Id
                $this->setRegenerateCycles($configuration->security->regenerate);
            */
        }

        return $this;
    }

    /**
     * Starts the session.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @throws Doozr_Session_Service_Exception
     */
    protected function start()
    {
        // Configure name of session (this will replace the PHPSESSID identifier for cookie ...)
        session_name($this->getIdentifier());

        $sessionId = $this->getId();

        if (null !== $sessionId) {
            session_id($sessionId);
        }

        // set params for cookie!
        session_set_cookie_params(
            $this->getLifetime(),
            $this->retrievePathToCurrentClass(),
            $this->getDomain(),
            $this->getSsl(),
            $this->getHttpOnly()
        );

        $this->setStarted(session_start());

        if (null === $sessionId) {
            $sessionId = session_id();
            $this->setId($sessionId);
        }

        // Check stack for functions to execute
        $callStack = $this->callStack['start'];

        // Iterate over callstack
        foreach ($callStack as $call) {
            call_user_func([$call[0], $call[1]]);
        }

        // log created session-id and defined session parameter (cookie params)
        $this->getLogger()->debug(
            'session-id: '.$sessionId.' session-cookie-parameter: '.
            var_export(
                session_get_cookie_params(),
                true
            )
        );
    }

    /**
     * Configures the session cookie parameter.
     *
     * @param string $identifier The identifier to set
     * @param string $sessionId  The current session id
     * @param int    $lifetime   The lifetime of the session and cookie
     * @param string $path       The path for the session cookie validity (default = / = all subfolder)
     * @param string $domain     The domain to set
     * @param bool   $ssl        TRUE to submit cookie via SSL only, otherwise FALSE to do not so
     * @param mixed  $httpOnly   NULL (default) to do not set this flag, TRUE to set, FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if operation was successful, otherwise FALSE
     */
    protected function setSessionCookie($identifier, $sessionId, $lifetime, $path, $domain, $ssl, $httpOnly = null)
    {
        // calculate concrete expiration date/time
        $expire = time() + $lifetime;

        // check for httpOnly - cause only supported >= 5.2
        if (null !== $httpOnly && $this->getFlagStatus('httpOnly')) {
            session_set_cookie_params($lifetime, $path, $domain, $ssl, $httpOnly);
            $result = setcookie($identifier, $sessionId, $expire, $path, $domain, $ssl, $httpOnly);

        } else {
            session_set_cookie_params($lifetime, $path, $domain, $ssl);
            $result = setcookie($identifier, $sessionId, $expire, $path, $domain, $ssl);

        }

        return $result;
    }

    /**
     * returns an encrypted string for given input if encryption is enabled.
     *
     * @param string $value The string to encrypt/encrypt
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The input encrypted or plain if encryption is enabled
     */
    protected function encrypt($value)
    {
        return $this->getCryptService()->encrypt(serialize($value), $this->getSecurity()->getPrivateKey());
    }

    protected function decrypt($value)
    {
        return unserialize($this->getCryptService()->decrypt($value, $this->getSecurity()->getPrivateKey()));
    }

    /**
     * Returns a part of the input string splitted by count of dots from left or right.
     *
     * @param string $string    The string to split
     * @param int    $parts     The count of parts to return
     * @param string $direction The direction for processing. Can be either LTR or RTL.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The resulting String
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
     * Setup security settings of session.
     *
     * @param bool $ssl      TRUE to enable ssl flag for cookies/session, FALSE to do not
     * @param bool $httpOnly TRUE to enable httpOnly flag for cookies/session, FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function initSessionSecurity($ssl = self::DEFAULT_SEC_FLAG_SSL, $httpOnly = self::DEFAULT_SEC_FLAG_HTTPONLY)
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
     * binds the session to the current IP of user (client).
     * This method is intend to bind the session to the current IP of user (client).
     * Through changing $octets to a lower value you are able to bind to an ip address
     * also if the user is using a proxy-connection like AOL-users do.
     *
     * @param int    $octets The count of octets to use for binding session to ip
     * @param string $ip     The Ip-Address of the client to bind session to
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
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
        } catch (Doozr_Session_Service_Exception $e) {
            $ipFromSession = null;
        }


        // at this point the ip is in format configured (X-octets)
        if ($ipFromSession !== null) {
            // found ip in session! try to validation and destroy if suspicious
            if ($ipFromSession != $ip) {
                $this->getLogger()->debug('Session seems to be hijacked! Destroying session and closing connection!');
                $this->destroy();
            }
        } else {
            $this->set(self::DEFAULT_BIND_IP_IDENTIFIER, $ip);
        }

        // return success
        return true;
    }

    /**
     * Binds the session (cookie) to the passed Domain.
     *
     * @param string $mode   The runtimeEnvironment. Can be "domain" (uses the full domain) or "subdomain" uses (.subdomain.tld)
     * @param string $domain The domain to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function bindToDomain($mode = self::DEFAULT_BIND_DOMAIN_MODE, $domain = null)
    {
        // if no domain passed through - get the previously stored one
        if (!$domain) {
            $domain = $this->getDomain();
        }

        // All advanced features only available for named hosts - no ip!
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
     * Binds the session (cookie) to the passed Path.
     *
     * @param string $path The path to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function bindToPath($path = self::DEFAULT_BIND_PATH_PATH)
    {
        $this->setPath($path);
    }

    /**
     * Enables encryption.
     * This method is intend to enable the encryption for this instance including cipher and encoding.
     *
     * @param string $cipher   The algorithm (container of Service crypt) used for encryption
     * @param string $encoding The encoding to use for safety handable encrypted value
     *                         (can be either "uuencode" or "base64")
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function enableEncryption($cipher = self::DEFAULT_ENCRYPT_CIPHER, $encoding = self::DEFAULT_ENCRYPT_ENCODING)
    {
        /* @var $this->cryptService Doozr_Crypt_Service */
        $this
            ->cryptService(
                Doozr_Loader_Serviceloader::load('crypt', $cipher, $encoding)
            )
            ->security(
                self::getRegistry()->getSecurity()
            );

        $this->encrypt = true;
    }

    /**
     * Disables encryption.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function disableEncryption()
    {
        $this->encrypt = false;
    }

    /**
     * Checks the current encryption status.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if encryption is enabled, otherwise FALSE if not
     */
    public function hasEncryption()
    {
        return (true === $this->encrypt);
    }

    /**
     * Handle the regeneration process.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @throws Doozr_Session_Service_Exception
     */
    public function handleRegenerate()
    {
        // check for invalid call
        if (!$this->wasStarted()) {
            throw new Doozr_Session_Service_Exception(
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
     * Set status of session started to passed value.
     *
     * @param bool $status TRUE if session was started, FALSE if not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setStarted($status)
    {
        $this->started = $status;
    }

    /**
     * Returns status of session started.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if session was already started, FALSE if not
     */
    public function wasStarted()
    {
        return $this->started || ('' !== session_id());
    }

    /**
     * Sets the status of a given flag.
     *
     * @param string $flag   The flag to set status for
     * @param bool   $status The status TRUE or FALSE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
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

    public function flagStatus($flag, $status)
    {
        $this->setFlagStatus($flag, $status);

        return $this;
    }

    /**
     * Returns the status of a given flag.
     *
     * @param string $flag The flag to return status for, NULL to return all flags as array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed Boolean TRUE if flag is supported, otherwise FALSE if not - Array of all flags
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
     * Sets the client IP.
     *
     * @param string $clientIp The IP to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setClientIp($clientIp)
    {
        $this->clientIp = $clientIp;
    }

    public function clientIp($clientIp)
    {
        $this->setClientIp($clientIp);

        return $this;
    }

    /**
     * Returns the client IP.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The IP of the client
     */
    public function getClientIp()
    {
        return $this->clientIp;
    }

    /**
     * Sets the User-Agent.
     *
     * @param string $userAgent The user agent to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    public function userAgent($userAgent)
    {
        $this->setUserAgent($userAgent);

        return $this;
    }

    /**
     * Returns the User-Agent.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The user-agent
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Sets the Domain of the server.
     *
     * @param string $domain The domain to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    public function domain($domain)
    {
        $this->setDomain($domain);

        return $this;
    }

    /**
     * Returns the Domain.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The domain
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Enables the obfuscation.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function enableObfuscation()
    {
        $this->obfuscate = true;
    }

    /**
     * Disables the obfuscation.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function disableObfuscation()
    {
        $this->obfuscate = false;
    }

    /**
     * Return current status of obfuscation.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if enabled, otherwise FALSE
     */
    public function useObfuscation()
    {
        return $this->obfuscate === true;
    }

    /**
     * Sets the current status of SSL-encryption.
     *
     * @param bool $status TRUE if SSL is enabled, otherwise FALSE if not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setSsl($status)
    {
        $this->ssl = $status;
    }

    /**
     * Returns the current status of SSL-encryption.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if SSL is enabled, otherwise FALSE if not
     */
    public function getSsl()
    {
        return $this->ssl;
    }

    /**
     * Sets the current status of HTTP-Only is supported.
     *
     * @param bool $status TRUE if HTTP-Only is supported, otherwise FALSE if not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setHttpOnly($status)
    {
        $this->httpOnly = $status;
    }

    /**
     * Returns the current status of HTTP-Only is supported.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if HTTP-Only is supported, otherwise FALSE if not
     */
    public function getHttpOnly()
    {
        return $this->httpOnly;
    }

    /**
     * storages a given variable with value in session.
     *
     * @param string $variable The name of the session-variable to set
     * @param mixed  $value    The value of the session-variable to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     */
    public function set($variable, $value)
    {
        // Check for encryption and encrypt if required
        if (true === $this->hasEncryption()) {
            $variable = $this->encrypt($variable);
            $value    = $this->encrypt($value);
        }

        $_SESSION[$variable] = $value;

        return true;
    }

    /**
     * Returns a variable from session by passed name/identifier.
     *
     * @param string $variable The name of the session variable to return
     * @param null   $value    The default value to return in case of exception
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed Value of the requested session variable if set, otherwise NULL
     *
     * @throws Doozr_Session_Service_Exception
     */
    public function get($variable, $value = null)
    {
        // Check for encryption and encrypt if required
        if (true === $this->hasEncryption()) {
            $variable = $this->encrypt($variable);
        }

        // Check if requested var is set
        if (true === isset($_SESSION[$variable])) {

            if (true === $this->hasEncryption()) {
                // Get decrypted value -> decrypt -> unserialized
                $value = $this->decrypt($_SESSION[$variable]);

            } else {
                // Return plain from session
                $value = $_SESSION[$variable];
            }

        } else {
            // Throw a new exception ...
            throw new Doozr_Session_Service_Exception(
                sprintf(
                    'Session variable "%s" could not be retrieved from session. Ensure that it is set first.',
                    $variable
                )
            );
        }

        return $value;
    }

    /**
     * returns the isset status of variable.
     *
     * @param string $variable The name of the session-variable to check if set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if variable is set, otherwise FALSE
     */
    public function issetVariable($variable)
    {
        // Encrypt the variable-name?
        if (true === $this->hasEncryption()) {
            $variable = $this->encrypt($variable);
        }

        // return the isset status of given variable
        return (true === isset($_SESSION[$variable]));
    }

    /**
     * Unsets a variable from session.
     *
     * @param string $variable The name of the session-variable to unset
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     */
    public function unsetVariable($variable)
    {
        // Encrypt variable name?
        if (true === $this->hasEncryption()) {
            $variable = $this->encrypt($variable);
        }

        unset($_SESSION[$variable]);

        return (false === $this->issetVariable($variable));
    }

    /**
     * Sets the Session-Id.
     *
     * @param string $id The Session-Id to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * FLuent: Setter for id.
     *
     * @param string $id The Session-Id to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     */
    public function id($id)
    {
        $this->setId($id);

        return $this;
    }

    /**
     * Returns the current active Session-Id.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The current active Session-Id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the identifier.
     *
     * @param string $identifier The identifier to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @throws Doozr_Session_Service_Exception
     */
    public function setIdentifier($identifier)
    {
        if (true === $this->wasStarted()) {
            throw new Doozr_Session_Service_Exception(
                'Identifier cannot be changed! The identifier (session-name) must be set BEFORE start() is called.'
            );
        }

        // store identifier
        $this->identifier = $identifier;
    }

    /**
     * @param $identifier
     *
     * @return $this
     */
    public function identifier($identifier)
    {
        $this->setIdentifier($identifier);

        return $this;
    }

    /**
     * Returns the identifier with implemented fingerprinting to ensure higher security.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The identifier to return
     */
    public function getIdentifier()
    {
        // Get identifier
        $identifier = $this->identifier;

        // If identifier must be obfuscated
        if (true === $this->useObfuscation()) {

            // Who is requesting (fingerprint client)
            $headers = getallheaders();

            // Get arguments
            $filter = ['USER-AGENT', 'ACCEPT', 'ACCEPT-LANGUAGE', 'ACCEPT-ENCODING'];

            foreach ($headers as $header => $value) {
                if (false === in_array(strtoupper($header), $filter)) {
                    unset($headers[$header]);
                }
            }

            $identifier = sha1(
                $identifier.
                $this->generateFingerprint(
                    $this->getClientIp(),
                    $headers
                )
            );
        }

        return $identifier;
    }

    public function setUseCookie($useCookies)
    {
        $this->useCookies = $useCookies;

        ini_set('session.use_cookies', (true === $useCookies) ? '1' : '0');
    }

    public function useCookies($useCookies)
    {
        $this->setUseCookie($useCookies);

        return $this;
    }

    public function getUseCookies()
    {
        return $this->useCookies;
    }

    /**
     * Returns fingerprint for any type of input/argument.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string Fingerprint for input
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
     * Setter for lifetime.
     *
     * @param int $lifetime The lifetime to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;

        ini_set('session.cookie_lifetime', $lifetime);
    }

    public function lifetime($lifetime)
    {
        $this->setLifetime($lifetime);

        return $this;
    }

    /**
     * Getter for lifetime.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int The lifetime
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     */
    public function logger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Sets the garbage collector timeout.
     *
     * @param int $gcTime The time to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setGcTime($gcTime)
    {
        $this->gcTime = $gcTime;

        ini_set('session.gc_maxlifetime', $gcTime);
    }

    public function gcTime($gcTime)
    {
        $this->gcTime = $gcTime;

        return $this;
    }

    /**
     * Returns the gc-timeout.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int The garbage collector timeout in seconds
     */
    public function getGcTime()
    {
        return $this->gcTime;
    }

    /**
     * Sets the path.
     *
     * @param string $path The path to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Returns the path.
     *
     * @param bool $resolveSymlinks NOT USED HERE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The active path
     */
    public function retrievePathToCurrentClass($resolveSymlinks = false)
    {
        return $this->path;
    }

    /**
     * destroys a session.
     *
     * destroy an existing session! clean session garbage collector
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool True if everything wents fine, otherwise false
     */
    public function destroy()
    {
        // 1st we regenerate the id of session and delete the old existing data
        $this->regenerate(true);

        // return status of destroying session
        return session_destroy();
    }

    /**
     * Configures the regeneration of the Session-Id by given cycles.
     *
     * @param int $cycles The count of cycles used for regeneration. 0 = disabled,
     *                    1 = on every request ... 8 = on each 8th request ...
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setRegenerateCycles($cycles = self::DEFAULT_REGENERATE_CYCLES)
    {
        // do we need to do anything? cause a "0" means deactivated!
        $this->regenerateCycles = $cycles;

        // set only if cycle count is given
        if ($cycles > 0) {
            // execute right after session start
            $this->callStack['start']['regenerate'] = [
                $this,
                'handleRegenerate',
            ];
        } else {
            // remove from callstack if exists
            if (isset($this->callStack['start']['regenerate'])) {
                unset($this->callStack['start']['regenerate']);
            }
        }
    }

    /**
     * Returns the count of cycles on which the Session-Id must be regenerated.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int The count of cycles
     */
    public function getRegenerateCycles()
    {
        return $this->regenerateCycles;
    }

    /**
     * Returns status of required session id regeneration.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if Session-Id must be regenerated, FALSE if not
     */
    public function mustRegenerate()
    {
        // try to read counter from session
        try {
            $currentCycle = $this->get(self::DEFAULT_REGENERATE_CYCLES_IDENTIFIER);
        } catch (Doozr_Session_Service_Exception $e) {
            $currentCycle = 0;
        }

        // increase cycle
        ++$currentCycle;

        // check if current cycle is the one for regenerate
        if ($currentCycle == $this->getRegenerateCycles()) {
            // regenerate
            return true;
        }

        // do not regenerate
        return $currentCycle;
    }

    /**
     * Regenerates the whole session.
     *
     * @param bool $flush TRUE to flush the whole session content, otherwise FALSE to transfer to new session
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if everything wents fine, otherwise FALSE
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

        /*
        // reset session cookie
        $status &= $this->setSessionCookie(
            $this->getIdentifier(),
            $this->getId(),
            $lifetime,
            $this->retrievePathToCurrentClass(),
            $this->getDomain(),
            $this->getSsl(),
            $this->getHttpOnly()
        );
        */

        // return status
        return $status;
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Fulfill: Doozr_Base_Crud_Interface
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Crud access for create. Creates a session entry.
     *
     * @param string $key   The key to store value under in session
     * @param mixed  $value Whatever variable type (complex or simple) to store
     *
     * @example $session->create('foo', 'bar');
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
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
     *
     * @return bool TRUE on success, otherwise FALSE
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
     *
     * @return bool TRUE on success, otherwise FALSE
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
     *
     * @return bool TRUE on success, otherwise FALSE
     */
    public function delete($key)
    {
        return $this->unsetVariable($key);
    }
}
