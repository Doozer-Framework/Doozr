<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Base - View
 *
 * View.php - Base class for view-layers from MV(C|P)
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
 * Please feel free to contact us via e-mail: <opensource@clickalicious.de>
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_View
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/View/Observer.php';

/**
 * DoozR - Base - View
 *
 * Base master-class for building a view
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_View
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Base_View extends DoozR_Base_View_Observer implements DoozR_Base_View_Interface
{
    /**
     * holds the data to show
     *
     * @var mixed
     * @access protected
     */
    protected $data;

    /**
     * The output mode used
     *
     * @var int
     * @access protected
     */
    protected $outputMode = PHPTAL::HTML5;

    /**
     * holds the path to templates
     *
     * @var string
     * @access protected
     */
    protected $pathTemplates;

    /**
     * contains the complete request
     *
     * @var array
     * @access protected
     */
    protected $request;

    /**
     * The original untouched request
     *
     * @var array
     * @access protected
     */
    protected $originalRequest;

    /**
     * Translator instance used to pass to template service/system
     *
     * @var DoozR_I18n_Service
     * @access protected
     */
    protected $translator;

    /**
     * Request state object
     *
     * @var DoozR_Base_State_Interface
     * @access protected
     */
    protected $requestState;

    /**
     * The arguments passed with the request
     *
     * @var array
     * @access protected
     */
    protected $arguments;

    /**
     * The instance of DoozR_Controller_Front
     *
     * @var DoozR_Controller_Front
     * @access protected
     */
    protected $front;

    /**
     * Contains the DoozR main configuration object
     *
     * @var DoozR_Config
     * @access protected
     */
    protected $configuration;

    /**
     * contains the translation for reading request
     *
     * @var array
     * @access protected
     */
    protected $translation;

    /**
     * Contains an instance of the module DoozR_Cache_Service
     *
     * @var DoozR_Cache_Service
     * @access protected
     */
    protected $cache;

    /**
     * The fingerprint of the view state
     * (e.g. used for ETag)
     *
     * @var string
     * @access protected
     */
    protected $fingerprint;


    /**
     * Constructor.
     *
     * @param DoozR_Registry             $registry     DoozR_Registry containing all core components
     * @param DoozR_Base_State_Interface $requestState Whole request as state
     * @param array                      $request      The whole request as processed by "Route"
     * @param array                      $translation  The translation required to read the request
     * @param DoozR_Cache_Service        $cache        An instance of DoozR_Cache_Service
     * @param DoozR_Config               $config       An instance of DoozR_Config with Core-Configuration
     * @param DoozR_Controller_Front     $front        An instance of DoozR_Front
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Base_View
     * @access public
     * @throws DoozR_Base_View_Exception
     */
    public function __construct(
        DoozR_Registry             $registry,
        DoozR_Base_State_Interface $requestState,
        array                      $request,
        array                      $translation,
        DoozR_Cache_Service        $cache,
        DoozR_Config               $config,
        DoozR_Controller_Front     $front
    ) {
        // Store all instances for further use ...
        $this
            ->registry($registry)
            ->request($request)
            ->translation($translation)
            ->originalRequest($requestState->getRequest())
            ->cache($cache)
            ->configuration($config)
            ->front($front)
            ->arguments($requestState->getArguments())
            ->requestState($requestState);

        // Check for __tearup - Method (it's DoozR's __construct-like magic-method)
        if ($this->hasMethod('__tearup') && is_callable(array($this, '__tearup'))) {
            $result = $this->__tearup($request, $translation);

            if ($result !== true) {
                throw new DoozR_Base_View_Exception(
                    '__tearup() must (if set) return TRUE. __tearup() executed and it returned: ' .
                    var_export($result, true)
                );
            }
        }
    }

    /**
     * Setter for request.
     *
     * @param array $request The request to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setRequest(array $request)
    {
        $this->request = $request;
    }

    /**
     * Setter for request.
     *
     * @param array $request The request to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function request(array $request)
    {
        $this->setRequest($request);
        return $this;
    }

    /**
     * Getter for request.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array|null The request stored, otherwise NULL
     * @access protected
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * Setter for translation.
     *
     * @param array $translation The translation to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setTranslation(array $translation)
    {
        $this->translation = $translation;
    }

    /**
     * Setter for translation.
     *
     * @param array $translation The translation to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function translation(array $translation)
    {
        $this->setTranslation($translation);
        return $this;
    }

    /**
     * Getter for translation.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array|null The translation stored, otherwise NULL
     * @access protected
     */
    protected function getTranslation()
    {
        return $this->translation;
    }

    /**
     * Setter for originalRequest.
     *
     * @param mixed $originalRequest The originalRequest to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setOriginalRequest($originalRequest)
    {
        $this->originalRequest = $originalRequest;
    }

    /**
     * Setter for originalRequest.
     *
     * @param mixed $originalRequest The originalRequest to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function originalRequest($originalRequest)
    {
        $this->setOriginalRequest($originalRequest);
        return $this;
    }

    /**
     * Getter for originalRequest.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed|null The originalRequest stored, otherwise NULL
     * @access protected
     */
    protected function getOriginalRequest()
    {
        return $this->originalRequest;
    }

    /**
     * Setter for cache.
     *
     * @param DoozR_Cache_Service $cache The cache service instance to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setCache(DoozR_Cache_Service $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Setter for cache.
     *
     * @param DoozR_Cache_Service $cache The cache service instance to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function cache(DoozR_Cache_Service $cache)
    {
        $this->setCache($cache);
        return $this;
    }

    /**
     * Getter for cache.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Cache_Service|null The cache service instance stored, otherwise NULL
     * @access protected
     */
    protected function getCache()
    {
        return $this->cache;
    }

    /**
     * Setter for configuration.
     *
     * @param DoozR_Config_Interface $configuration The configuation object
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setConfiguration(DoozR_Config_Interface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Setter for configuration with fluent API support for chaining calls to this class.
     *
     * @param DoozR_Config_Interface $configuration The
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function configuration(DoozR_Config_Interface $configuration)
    {
        $this->setConfiguration($configuration);
        return $this;
    }

    /**
     * Getter for configuration.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Config_Interface The configuration stored
     * @access protected
     */
    protected function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Setter for front.
     *
     * @param DoozR_Controller_Front $front Instance of DoozR_Controller_Front
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setFront(DoozR_Controller_Front $front)
    {
        $this->front = $front;
    }

    /**
     * Setter for front.
     *
     * @param DoozR_Controller_Front $front Instance of DoozR_Controller_Front
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function front(DoozR_Controller_Front $front)
    {
        $this->setFront($front);
        return $this;
    }

    /**
     * Getter for front.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Controller_Front Instance of front controller
     * @access protected
     */
    protected function getFront()
    {
        return $this->front;
    }

    /**
     * Setter for arguments.
     *
     * @param array $arguments The arguments
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setArguments($arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * Setter for arguments.
     *
     * @param array $arguments The arguments
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function arguments($arguments)
    {
        $this->setArguments($arguments);
        return $this;
    }

    /**
     * Getter for arguments.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The arguments
     * @access protected
     */
    protected function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Setter for request state.
     *
     * @param DoozR_Base_State_Interface $requestState
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setRequestState($requestState)
    {
        $this->requestState = $requestState;
    }

    /**
     * Setter for request state.
     *
     * @param DoozR_Base_State_Interface $requestState
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function requestState($requestState)
    {
        $this->setRequestState($requestState);
        return $this;
    }

    /**
     * Getter for request state.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Request state
     * @access protected
     */
    protected function getRequestState()
    {
        return $this->requestState;
    }

    /**
     * Setter for translator.
     *
     * @param DoozR_I18n_Service $translator Instance of translator service
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setTranslator(DoozR_I18n_Service $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Setter for translator.
     *
     * @param DoozR_I18n_Service $translator Instance of translator service
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function translator(DoozR_I18n_Service $translator)
    {
        $this->setTranslator($translator);
        return $this;
    }

    /**
     * Getter for translator.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_I18n_Service The I18n service instance
     * @access protected
     */
    protected function getTranslator()
    {
        return $this->translator;
    }

    /**
     * This method is the setter for the data to use in the action method.
     *
     * @param mixed   $data   The data to set
     * @param boolean $render Controls if renderer (if exist) should be called (set to TRUE)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if successful, otherwise FALSE
     * @access public
     */
    public function setData($data = null, $render = true)
    {
        // Store data (reached) from model in this view!
        $this->data = $data;

        // Assume null result out if render = false
        $result = null;

        // Do render the view?
        if ($render) {
            // Lookup specific renderer
            $specificViewRenderer = '__render' . ucfirst($this->request[$this->translation[1]]);

            // check if specific renderer is callable
            if (method_exists($this, $specificViewRenderer)) {
                // Call renderer
                $result = $this->{$specificViewRenderer}($this->data);

            } elseif (method_exists($this, '__render')) {
                // Always check fallback -> one generic __render for all actions :) maybe used in API's
                $result = $this->{'__render'}($this->data);
            }
        }

        return $result;
    }

    /**
     * This method (container) is intend to return the data for a requested mode.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The data for the mode requested
     * @access public
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Setter for fingerprint.
     *
     * @param string $fingerprint The fingerprint to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setFingerprint($fingerprint)
    {
        $this->fingerprint = $fingerprint;
    }

    /**
     * Setter for fingerprint.
     *
     * @param string $fingerprint The fingerprint to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function fingerprint($fingerprint)
    {
        $this->setFingerprint($fingerprint);
        return $this;
    }

    /**
     * Getter for fingerprint.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null Fingerprint if set, otherwise NULL
     * @access protected
     */
    protected function getFingerprint()
    {
        return $this->fingerprint;
    }

    /**
     * Setter for output mode.
     *
     * @param int $outputMode The output mode in format PHPTAL understand & accepts
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setOutputMode($outputMode = PHPTAL::HTML5)
    {
        $this->outputMode = $outputMode;
    }

    /**
     * Setter for output mode.
     *
     * @param int $outputMode The output mode in format PHPTAL understand & accepts
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function outputMode($outputMode = PHPTAL::HTML5)
    {
        $this->setOutputMode($outputMode);
        return $this;
    }

    /**
     * Getter for output mode.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return int|null Output mode if set, otherwise NULL
     * @access public
     */
    public function getOutputMode()
    {
        return $this->outputMode;
    }

    /**
     * This method is intend to render the current state of the view as html. For this it makes use of the base
     * template engine, and html5 template files. If you need another output or something like this, you must
     * overwrite this method.
     *
     * @param array                     $data        The data as override for internal stored data
     * @param string                    $fingerprint Optional fingerprint used as cache identifier for front- and
     *                                               backend! Hint: Rendering user specific data an user identifier
     *                                               MUST be used as salt when generating the fingerprint!!!
     *                                               Otherwise user specific data can and will be sent to another
     *                                               user!. So the following rule should be followed:
     *                                                   - generic view/template no user data = fingerprint by
     *                                                     content/path/url
     *                                                   - user specific view/template with user data = use
     *                                                     session-id or user-id!
     * @param PHPTAL_TranslationService $i18n        An instance of a DoozR I18n service
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successful, otherwise FALSE
     * @access protected
     */
    protected function render(array $data = array(), $fingerprint = null, PHPTAL_TranslationService $i18n = null)
    {
        $this->setFingerprint(
            $this->generateFingerprint(
                $fingerprint,
                $data
            )
        );

        $html = null;

        // @todo use $this->getConfiguration()->debug->enabled() instead?!
        if (DOOZR_DEBUG === false) {

            // We try to receive data for rendering from cache :) this is much faster
            try {
                $html = $this->cache->read($this->getFingerprint());

            } catch (DoozR_Cache_Service_Exception $e) {
                $html = null;
            }
        }

        // If data was/could not be retrieved we get it fresh here ...
        if ($html === null) {

            // Get name of template file
            $templateFile = $this->configuration->base->template->path() . $this->translateToTemplatefile() . '.html';

            /* @var $template PHPTAL */
            $template = DoozR_Loader_Serviceloader::load('template', $templateFile);

            // Set output mode ...
            $template->setOutputMode($this->getOutputMode());

            // if I18n passed -> forward to template engine (e.g. PHPTAL)
            if ($i18n !== null) {
                $i18n->useDomain($this->translateToTextdomain());
                $template->setTranslator($i18n);
                $template->{'doozr_locale'} = $i18n->getActiveLocale();
            }

            // Assign data from passed in array to template (for use as a template variable)
            foreach ($data as $key => $value) {
                $template->{$key} = $value;
            }

            // setup template compile output dir
            $template->setPhpCodeDestination(
                $this->configuration->phptal->directories->compiled()
            );

            // set the encoding of output
            $template->setEncoding(
                $this->configuration->locale->encoding()
            );

            // Output XHTML or HTML5 ... ?
            $template->setOutputMode(
                $this->configuration->phptal->settings->outputmode()
            );

            // execute = get result
            $html = $template->execute();

            // finally store in cache
            try {
                $this->cache->create($html, $this->getFingerprint());

            } catch (DoozR_Cache_Service_Exception $e) {
                pred($e);

            }
        }

        /* @var $response DoozR_Response_Web */
        $response = $this->getFront()->getResponse();

        // Try to get default header for responses from configuration and add them here ...
        try {
            $headers = $this->configuration->transmission->header();
        } catch (Exception $e) {
            $headers = array();
        }

        foreach ($headers as $category) {
            foreach ($category as $type => $header) {
                if ($type === 'mvp') {
                    foreach ($header as $key => $value) {
                        $response->sendHeader($key . ': ' . $value);
                    }
                }
            }
        }

        // Shorten fingerprint (extra long) to used as etag for client (reduces the weight transported <=> directions)
        $etag = md5($this->getFingerprint());

        // send our data as HTML through response
        $response->sendHtml($html, $etag, true);
    }

    /**
     * This method is intend to translate the current object and action pair to a filename
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The filename constructed
     * @access protected
     */
    protected function translateToTemplatefile()
    {
        // get object we operate on
        $object = ucfirst($this->request[$this->translation[0]]);

        // get action we should operate
        $action = ucfirst($this->request[$this->translation[1]]);

        // construct relative filename (+path) for current-view template
        return $object . DIRECTORY_SEPARATOR . $action;
    }

    /**
     * Translates the current setup of view parameter to a textdomain which can
     * and should be used to translate strings (i18n) for example via gettext
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The textdomain
     * @access protected
     */
    protected function translateToTextdomain()
    {
        return strtolower($this->request[$this->translation[0]]);
    }

    /**
     * Generates and returns fingerprint for the current instance & request.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The generated fingerprint
     * @access protected
     */
    protected function generateFingerprint()
    {
        // Assume empty fingerprint
        $fingerprint = '';

        // Who is requesting (fingerprint client)
        $headers = getallheaders();

        // Get arguments
        $arguments   = func_get_args();
        $arguments[] = $_SERVER['REMOTE_ADDR'];
        $arguments[] = (isset($headers['USER_AGENT'])) ? $headers['USER_AGENT'] : null;
        $arguments[] = (isset($headers['ACCEPT'])) ? $headers['ACCEPT'] : null;
        $arguments[] = (isset($headers['ACCEPT_LANGUAGE'])) ? $headers['ACCEPT_LANGUAGE'] : null;
        $arguments[] = (isset($headers['ACCEPT_ENCODING'])) ? $headers['ACCEPT_ENCODING'] : null;
        $arguments[] = $this->translateToTemplatefile();

        foreach ($arguments as $argument) {
            $fingerprint .= serialize($argument);
        }

        return $this->generateHash($fingerprint);
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

    /**
     * Dispatch observer default behavior.
     * This dispatches the data and the stored translator instance to render().
     *
     * @param SplSubject $subject The subject to retrieve data from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success, otherwise FALSE
     * @access protected
     */
    protected function __update(SplSubject $subject)
    {
        return $this->setData($subject->getData());
    }

    /**
     * This method is intend to call the teardown method of a model if exist
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __destruct()
    {
        // check for __tearup - Method (it's DoozR's __construct-like magic-method)
        if ($this->hasMethod('__teardown') && is_callable(array($this, '__teardown'))) {
            $this->__teardown();
        }
    }
}
