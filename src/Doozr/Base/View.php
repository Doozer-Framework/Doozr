<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Base - View
 *
 * View.php - Base class for Views of Doozr.
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
 * Please feel free to contact us via e-mail: <opensource@clickalicious.de>
 *
 * @category   Doozr
 * @package    Doozr_Base
 * @subpackage Doozr_Base_View
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/View/Observer.php';

/**
 * Doozr - Base - View
 *
 * Base class for Views of Doozr.
 *
 * @category   Doozr
 * @package    Doozr_Base
 * @subpackage Doozr_Base_View
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Base_View extends Doozr_Base_View_Observer
    implements
    Doozr_Base_View_Interface
{
    /**
     * The data to show
     *
     * @var mixed
     * @access protected
     */
    protected $data;

    /**
     * Debug state of this class' instance.
     *
     * @var bool
     * @access protected
     */
    protected $debugging;

    /**
     * Caching state of this class' instance
     *
     * @var bool
     * @access protected
     */
    protected $caching;

    /**
     * The output runtimeEnvironment used
     *
     * @var int
     * @access protected
     */
    protected $outputMode;

    /**
     * holds the path to templates
     *
     * @var string
     * @access protected
     */
    protected $pathTemplates;

    /**
     * Active/last route.
     *
     * @var Doozr_Request_Route_State
     * @access protected
     */
    protected $route;

    /**
     * Translator instance used to pass to template service/system
     *
     * @var Doozr_I18n_Service
     * @access protected
     */
    protected $translator;

    /**
     * Request state object
     *
     * @var Doozr_Base_State_Interface
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
     * Contains the Doozr main configuration object
     *
     * @var Doozr_Configuration
     * @access protected
     */
    protected $configuration;

    /**
     * Contains an instance of the module Doozr_Cache_Service
     *
     * @var Doozr_Cache_Service
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
     * Extension of templates.
     *
     * @var string
     * @access protected
     */
    protected $templateExtension = 'cli';

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param Doozr_Registry      $registry     Doozr registry
     * @param Doozr_Request_State $requestState Request state
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     * @throws Doozr_Base_View_Exception
     */
    public function __construct(
        Doozr_Registry      $registry,
        Doozr_Request_State $requestState
    ) {
        // Store all instances for further use ...
        $this
            ->registry($registry)
            ->route($requestState->getAttribute('route'))
            ->cache($registry->getCache())
            ->configuration($registry->getConfiguration())
            ->arguments($requestState->getQueryParams())
            ->requestState($requestState)
            ->debugging($registry->getParameter('doozr.kernel.debugging'))
            ->caching($registry->getParameter('doozr.kernel.caching'));

        // Check for __tearup - Method (it's Doozr's __construct-like magic-method)
        if ($this->hasMethod('__tearup') && is_callable(array($this, '__tearup'))) {
            $result = $this->__tearup($this->getRoute());

            if ($result !== true) {
                throw new Doozr_Base_View_Exception(
                    '__tearup() must (if set) return TRUE. __tearup() executed and it returned: ' .
                    var_export($result, true)
                );
            }
        }
    }

    /*------------------------------------------------------------------------------------------------------------------
    | SETTER & GETTER / HASSER & ISSER
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for templateExtension.
     *
     * @param string $templateExtension The templates extension
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setTemplateExtension($templateExtension)
    {
        $this->templateExtension = $templateExtension;
    }

    /**
     * Fluent: Setter for templateExtension.
     *
     * @param string $templateExtension The templates extension
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function templateExtension($templateExtension)
    {
        $this->setTemplateExtension($templateExtension);

        return $this;
    }

    /**
     * Getter for templateExtension.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Template extension
     * @access protected
     */
    protected function getTemplateExtension()
    {
        return $this->templateExtension;
    }

    /**
     * Setter for debugging.
     *
     * @param bool $debugging TRUE enable debugging, otherwise FALSE to disable
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setDebugging($debugging)
    {
        $this->debugging = $debugging;
    }

    /**
     * Fluent: Setter for debugging.
     *
     * @param bool $debugging TRUE enable debugging, otherwise FALSE to disable
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function debugging($debugging)
    {
        $this->setDebugging($debugging);

        return $this;
    }

    /**
     * Getter for debugging.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if debugging is enabled, otherwise FALSE
     * @access protected
     */
    protected function getDebugging()
    {
        return $this->debugging;
    }

    /**
     * Isser for debugging.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if debugging is enabled, otherwise FALSE if not
     * @access protected
     */
    protected function isDebugging()
    {
        return (true === $this->getDebugging());
    }

    /**
     * Setter for caching.
     *
     * @param bool $caching TRUE enable caching, otherwise FALSE to disable
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setCaching($caching)
    {
        $this->caching = $caching;
    }

    /**
     * Fluent: Setter for caching.
     *
     * @param bool $caching TRUE enable caching, otherwise FALSE to disable
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function caching($caching)
    {
        $this->setCaching($caching);

        return $this;
    }

    /**
     * Getter for caching.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if caching is enabled, otherwise FALSE
     * @access protected
     */
    protected function getCaching()
    {
        return $this->caching;
    }

    /**
     * Hasser for caching.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if caching is enabled, otherwise FALSE if not
     * @access protected
     */
    protected function hasCaching()
    {
        return (true === $this->getCaching());
    }

    /**
     * Setter for route.
     *
     * @param Doozr_Request_Route_State $route The route to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setRoute(Doozr_Request_Route_State $route)
    {
        $this->route = $route;
    }

    /**
     * Setter for route.
     *
     * @param Doozr_Request_Route_State $route The route to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function route(Doozr_Request_Route_State $route)
    {
        $this->setRoute($route);

        return $this;
    }

    /**
     * Getter for route.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Request_Route_State The route stored, otherwise NULL
     * @access protected
     */
    protected function getRoute()
    {
        return $this->route;
    }

    /**
     * Setter for cache.
     *
     * @param Doozr_Cache_Service $cache The cache service instance to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setCache(Doozr_Cache_Service $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Setter for cache.
     *
     * @param Doozr_Cache_Service $cache The cache service instance to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function cache(Doozr_Cache_Service $cache)
    {
        $this->setCache($cache);

        return $this;
    }

    /**
     * Getter for cache.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Cache_Service|null The cache service instance stored, otherwise NULL
     * @access protected
     */
    protected function getCache()
    {
        return $this->cache;
    }

    /**
     * Setter for configuration.
     *
     * @param Doozr_Configuration_Interface $configuration The configuation object
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setConfiguration(Doozr_Configuration_Interface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Setter for configuration with fluent API support for chaining calls to this class.
     *
     * @param Doozr_Configuration $configuration The
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function configuration(Doozr_Configuration $configuration)
    {
        $this->setConfiguration($configuration);

        return $this;
    }

    /**
     * Getter for configuration.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Configuration The configuration stored
     * @access protected
     */
    protected function getConfiguration()
    {
        return $this->configuration;
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
     * @param Doozr_Base_State_Interface $requestState
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
     * @param Doozr_Base_State_Interface $requestState
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
     * @return Doozr_Base_State_Interface Request state
     * @access protected
     */
    protected function getRequestState()
    {
        return $this->requestState;
    }

    /**
     * Setter for translator.
     *
     * @param Doozr_I18n_Service $translator Instance of translator service
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setTranslator(Doozr_I18n_Service $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Setter for translator.
     *
     * @param Doozr_I18n_Service $translator Instance of translator service
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function translator(Doozr_I18n_Service $translator)
    {
        $this->setTranslator($translator);

        return $this;
    }

    /**
     * Getter for translator.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_I18n_Service The I18n service instance
     * @access protected
     */
    protected function getTranslator()
    {
        return $this->translator;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is the setter for the data to use in the action method.
     *
     * @param mixed   $data   The data to set
     * @param bool $render Controls if renderer (if exist) should be called (set to TRUE)
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
        if (true === $render) {
            // Lookup specific renderer per view! view:action
            $specificViewRenderer = '__render' . ucfirst($this->getRoute()->getPresenter());

            // check if specific renderer is callable
            if (method_exists($this, $specificViewRenderer)) {
                // Call renderer
                $result = $this->{$specificViewRenderer}($this->data);

            } elseif (method_exists($this, '__render')) {
                // Always check fallback -> one generic __render for all actions used by Doozr for REST API
                $result = $this->{'__render'}($this->data);
            }
        }

        return $result;
    }

    /**
     * This method (container) is intend to return the data for a requested runtimeEnvironment.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The data for the runtimeEnvironment requested
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
     * @return string The fingerprint passed in and stored
     * @access protected
     */
    protected function setFingerprint($fingerprint)
    {
        $this->fingerprint = $fingerprint;

        return $fingerprint;
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
     * @return string Fingerprint if set, otherwise NULL
     * @access protected
     */
    protected function getFingerprint()
    {
        return $this->fingerprint;
    }

    /**
     * Setter for output runtimeEnvironment.
     *
     * @param int $outputMode The output runtimeEnvironment in format PHPTAL understand & accepts
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
     * Setter for output runtimeEnvironment.
     *
     * @param int $outputMode The output runtimeEnvironment in format PHPTAL understand & accepts
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
     * Getter for output runtimeEnvironment.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer Output runtimeEnvironment if set, otherwise NULL
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
     * @param array                        $data        The data as override for internal stored data
     * @param string                       $fingerprint Optional fingerprint used as cache identifier for front- and
     *                                                  backend! Hint: Rendering user specific data an user identifier
     *                                                  MUST be used as salt when generating the fingerprint!!!
     *                                                  Otherwise user specific data can and will be sent to another
     *                                                  user!. So the following rule should be followed:
     *                                                  - generic view/template no user data = fingerprint by
     *                                                    content/path/url
     *                                                  - user specific view/template with user data = use
     *                                                    session-id or user-id!
     * @param Doozr_I18n_Service_Interface $i18n        An instance of a Doozr I18n service
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if successful, otherwise FALSE
     * @access protected
     * @throws \Doozr_Base_View_Exception
     * @throws \Doozr_Exception
     * @throws \PHPTAL_ConfigurationException
     */
    protected function render(array $data = [], $fingerprint = null, Doozr_I18n_Service_Interface $i18n = null)
    {
        $this->setFingerprint(
            $this->generateFingerprint(
                $fingerprint,
                $data
            )
        );

        $html = null;

        if (false === $this->getConfiguration()->kernel->debugging->enabled) {

            // We try to receive data for rendering from cache :) this is much faster
            try {
                $html = $this->cache->read($this->getFingerprint());

            } catch (Doozr_Cache_Service_Exception $e) {
                $html = null;
            }
        }

        // If data was/could not be retrieved we get it fresh here ...
        if ($html === null) {

            // Get name of template file
            $templateFile = $this->configuration->kernel->view->template->path .
                            $this->translateToTemplateFilename() . '.' . $this->getTemplateExtension();

            if (false === $this->getRegistry()->getFilesystem()->exists($templateFile)) {
                throw new Doozr_Base_View_Exception(
                    sprintf('The template file "%s" is required for rendering but it does not exist.', $templateFile)
                );
            }

            /* @var $template PHPTAL */
            $template = Doozr_Loader_Serviceloader::load('template', $templateFile);

            // Set output runtimeEnvironment ...
            $template->setOutputMode($this->getOutputMode());

            // if I18n passed -> forward to template engine (e.g. PHPTAL)
            if (null !== $i18n) {
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
                $this->configuration->kernel->view->directories->compiled
            );

            // set the encoding of output
            $template->setEncoding(
                $this->configuration->kernel->localization->encoding
            );

            // Output XHTML or HTML5 ... ?
            $template->setOutputMode(
                $this->configuration->kernel->view->settings->outputmode
            );

            // execute = get result
            $html = $template->execute();

            if (true === $this->isDebugging()) {
                $renderer = $this->getRegistry()->getDebugbar()->getJavascriptRenderer();
                $renderer->setBaseUrl('/assets');
                $head = $renderer->renderHead();
                $body = $renderer->render();

                $html = str_replace('</head>', $head . '</head>', $html);
                $html = str_replace('</body>', $body . '</body>', $html);
            }

            // finally store in cache
            try {
                $this->cache->create($html, $this->getFingerprint());

            } catch (Doozr_Cache_Service_Exception $e) {
                pred($e);
            }
        }

        return $html;
    }

    /**
     * This method is intend to translate the current object and action pair to a filename
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The filename constructed
     * @access protected
     */
    protected function translateToTemplateFilename()
    {
        // get object we operate on
        $presenter = ucfirst($this->getRoute()->getPresenter());

        // get action we should operate
        $action = ucfirst($this->getRoute()->getAction());

        // construct relative filename (+path) for current-view template
        return $presenter . DIRECTORY_SEPARATOR . $action;
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
        // Try to load textdomain from system ...
        return strtolower($this->getRoute()->getPresenter().$this->getRoute()->getAction());
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
        $arguments[] = $this->translateToTemplateFilename();

        foreach ($arguments as $argument) {
            $fingerprint .= serialize($argument);
        }

        return md5($fingerprint);
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
        // check for __tearup - Method (it's Doozr's __construct-like magic-method)
        if ($this->hasMethod('__teardown') && is_callable(array($this, '__teardown'))) {
            $this->__teardown();
        }
    }
}
