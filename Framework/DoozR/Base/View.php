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
 * Please feel free to contact us via e-mail: <opensource@clickalicious.de>
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_View
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/View/Observer.php';

/**
 * DoozR - Base - View
 *
 * Base master-class for building a view
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_View
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Base_View extends DoozR_Base_View_Observer
{
    /**
     * holds the data to show
     *
     * @var mixed
     * @access protected
     */
    protected $data;

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
    protected $config;

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
     * This method is the constructor of this class.
     *
     * @param array                  $request     The whole request as processed by "Route"
     * @param array                  $translation The translation required to read the request
     * @param DoozR_Config           $config      An instance of DoozR_Config with Core-Configuration
     * @param DoozR_Cache_Service    $cache       An instance of DoozR_Cache
     * @param DoozR_Controller_Front $front       An instance of DoozR_Front
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __construct(
        array $request,
        array $translation,
        DoozR_Config $config,
        DoozR_Cache_Service $cache,
        DoozR_Controller_Front $front
    ) {
        // store original request
        $this->config      = $config;
        $this->request     = $request;
        $this->translation = $translation;
        $this->cache       = $cache;
        $this->front       = $front;
        $this->arguments   = $this->front->getRequest()->getRequest();

        // check for __tearup - Method (it's DoozR's __construct-like magic-method)
        if ($this->hasMethod('__tearup') && is_callable(array($this, '__tearup'))) {
            $this->__tearup($request, $translation);
        }
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

    /**
     * This method is the setter for the data to use in the action method.
     *
     * @param mixed   $data   The data to set
     * @param boolean $render Controls if renderer (if exist) should be called (set to TRUE)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successful, otherwise FALSE
     * @access public
     */
    public function setData($data = null, $render = true)
    {
        // store data (reached) from model in this view!
        $this->data = $data;

        // do render the view?
        if ($render) {
            // lookup specific renderer
            $specificViewRenderer = '__render'.ucfirst($this->request[$this->translation[1]]);

            // check if specific renderer is callable
            if (method_exists($this, $specificViewRenderer)) {
                // call renderer
                $this->{$specificViewRenderer}($this->data);
            }
        }

        // return status of operation (store + render)
        return true;
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
     * This method is intend to render the current state of the view as html.
     * For this it makes use of the base template engine, and html5 template
     * files. If you need another output or something like this, you must
     * overwrite this method.
     *
     * @param array $data The data as override for internal stored data
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if successful, otherwise FALSE
     * @access protected
     */
    protected function render(array $data = array(), DoozR_I18n_Service $i18n = null)
    {
        // store given fingerprint
        $this->fingerprint = $this->getFingerprint(1);

        // try to get content from our allumfassendes zaubersystem
        try {
            $data = $this->cache->read($this->fingerprint);

        } catch(DoozR_Cache_Service_Exception $e) {

            // get name of tpl file
            $tplFile = $this->config->base->template->path().$this->translateToTemplatefile().'.html';

            // load the template (generic template engine)
            $tpl = DoozR_Loader_Serviceloader::load('template', array($tplFile));

            // if I18n passed -> forward to template engine (e.g. PHPTAL)
            if ($i18n !== null) {
                $tpl->setTranslator($i18n);
            }

            // set data for template
            foreach ($data as $key => $value) {
                $tpl->{$key} = $value;
                /*
                $tpl->assignVariables(
                    $data
                );
                */
            }

            // setup template compile output dir
            $tpl->setPhpCodeDestination(
                $this->config->phptal->directories->compiled()
            );

            // set the encoding of output
            $tpl->setEncoding(
                $this->config->locale->encoding()
            );

            // Output XHTML or HTML5 ... ?
            $tpl->setOutputMode(
                $this->config->phptal->settings->outputmode()
            );

            // execute = get result
            $data = $tpl->execute();

            // finally store in cache
            try {
                $this->cache->create($data, $this->fingerprint);
            } catch (Exception $e) {
                pred($e);
            }
        }

        // get registry
        $registry = DoozR_Registry::getInstance();

        // get response from registry
        $response = $registry->front->getResponse();

        // header configured?
        try {
            $headers = $registry->config->transmission->header();
        } catch (Exception $e) {
            $headers = null;
        }

        // send configured header
        foreach ($headers as $type => $header) {
            $response->sendHeader($header);
        }

        // send our data as HTML through response
        $response->sendHtml($data, $this->fingerprint);
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
        return $object.DIRECTORY_SEPARATOR.$action;
    }

    /**
     * This method is intend to return the fingerprint for the current instance.
     *
     * @param string $uniqueId An unique Id like a session-id or user-id which
     *                         makes the template unique to this single user
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The calculated fingerprint
     * @access protected
     */
    protected function getFingerprint($uniqueId)
    {
        // the session id is unique and special to each user
        //$session = DoozR_Loader_Serviceloader::load('session');

        // this hash is unique for this current user, the request (e.g. /a/b/) and
        // its arguments (e.g. ?a=b).
        return md5(
            $uniqueId.
            serialize($this->request).
            serialize($this->arguments)
        );
    }
}

?>
