<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - View - Web.
 *
 * Web.php - Web specific view for handling HTTP.
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
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/View.php';

/**
 * Doozr - View - Web.
 *
 * Web specific view for handling HTTP.
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
class Doozr_View_Web extends Doozr_Base_View
{
    /**
     * The output mode.
     *
     * @var int
     */
    protected $outputMode = PHPTAL::HTML5;

    /**
     * File extension of template files.
     *
     * @var string
     */
    protected $templateExtension = 'html';

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
     *                                                  content/path/url
     *                                                  - user specific view/template with user data = use
     *                                                  session-id or user-id!
     * @param Doozr_I18n_Service_Interface $i18n        An instance of a Doozr I18n service
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if successful, otherwise FALSE
     *
     * @throws \Doozr_Base_View_Exception
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

        // We try to receive data for rendering from cache :) this is much faster
        if (true === $this->getCaching()) {
            try {
                $html = $this->cache->read($this->getFingerprint());
            } catch (Doozr_Cache_Service_Exception $exception) {
                $html = null;
            }
        }

        // If data was/could not be retrieved we get it fresh here ...
        if (null === $html) {
            // Get name of template file
            $templateFile = $this->configuration->kernel->view->template->path.
                            $this->translateToTemplateFilename().'.'.$this->getTemplateExtension();

            if (false === $this->getRegistry()->getFilesystem()->exists($templateFile)) {
                throw new Doozr_Base_View_Exception(
                    sprintf('The template file "%s" is required for rendering but it does not exist.', $templateFile)
                );
            }

            /* @var $template PHPTAL */
            $template = Doozr_Loader_Serviceloader::load('template', $templateFile);
            $template->setForceReparse(true);
            $template->setCacheLifetime(-1);

            // Set output runtimeEnvironment ...
            $template->setOutputMode($this->getOutputMode());

            // If I18n passed -> forward to template engine (e.g. PHPTAL)
            if (null !== $i18n) {

                // Try to load specific namespace/textdomain for PRESENTER+ACTION
                try {
                    $i18n->useDomain($this->translateToTextdomain());
                } catch (Doozr_I18n_Service_Exception $e) {

                    // We don't care but we log for developing purposes
                    $this->getRegistry()->getLogger()->debug($e->getMessage());

                    // Try to load specific namespace/textdomain for PRESENTER
                    try {
                        $i18n->useDomain($this->translateToTextdomain(true));
                    } catch (Doozr_I18n_Service_Exception $e) {
                        // We don't care but we log for developing purposes
                        $this->getRegistry()->getLogger()->debug($e->getMessage());

                        // Try to load default namespace/textdomain
                        try {
                            $i18n->useDomain($this->getConfiguration()->i18n->default->namespace);
                        } catch (Doozr_I18n_Service_Exception $e) {
                            // We don't care but we log for developing purposes
                            $this->getRegistry()->getLogger()->debug($e->getMessage());
                        }
                    }
                }

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

            $html = $template->execute();

            // Inject Debugbar?
            if (true === $this->isDebugging()) {
                $html = $this->injectDebugbar($html);
            }

            // Cache result?
            if (true === $this->getCaching()) {
                $this->cache->create($html, $this->getFingerprint());
            }
        }

        return $html;
    }

    /**
     * Inject Debugbar into existing (hopefully valid and complete) HTML document string.
     *
     * @param string $html The HTML to inject code into
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The HTML including Debugbar.
     */
    protected function injectDebugbar($html)
    {
        $debugBar = $this->getRegistry()->getDebugbar();

        $debugBar = $this->getRegistry()->getLogger()->getLogger('debugbar')->exportToDebugBar($debugBar);
        $renderer = $debugBar->getJavascriptRenderer();
        $renderer->setBaseUrl('/assets');

        $head = $renderer->renderHead();
        $body = $renderer->render();

        $html = str_replace('</head>', $head.'</head>', $html);
        $html = str_replace('</body>', $body.'</body>', $html);

        return $html;
    }
}
