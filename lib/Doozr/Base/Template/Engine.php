<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr Base Template Engine
 *
 * Engine.php - The Base Class for Template-Engines. This class is intend
 * as base for template engines. This class is deeply integrated into the
 * core of Doozr. So this class can interact near to all core
 * functionality and interoperates with module "Doozr_Template".
 *
 * PHP versions 5.4
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
 *
 * Copyright (c) 2005 - 2015, Benjamin Carl - All rights reserved.
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
 * @category   Doozr
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Template
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Facade/Singleton.php';

/**
 * Doozr Base Template Engine
 *
 * The Base Class for Template-Engines. This class is intend
 * as base for template engines. This class is deeply integrated into the
 * core of Doozr. So this class can interact near to all core
 * functionality and interoperates with module "Doozr_Template".
 *
 * @category   Doozr
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Template
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Base_Template_Engine extends Doozr_Base_Facade_Singleton
{
    /**
     * Contains the current instance of template engine
     * (singleton pattern)
     *
     * @var object
     * @access protected
     */
    protected static $instance;

    /**
     * Contains the template
     *
     * @var mixed
     * @access protected
     */
    protected $template;

    /**
     * Contains the runtimeEnvironment in which the output shut be send.
     * Can be either PHPTAL::XHTML or PHPTAL::HTML5 or
     * any other supported namespace of the template engine
     * used.
     *
     * @var string
     * @access protected
     */
    protected $mode;

    /**
     * holds the path to the template files
     *
     * @var string
     * @access protected
     */
    protected $templatePath;

    /**
     * holds the current processed template file
     *
     * @var string
     * @access protected
     */
    protected $templateFile;

    /**
     * holds the full path and file of the current template file
     *
     * @var string
     * @access protected
     */
    protected $resource;

    /**
     * Contains the library to use as engine
     *
     * @var string
     * @access protected
     */
    protected $library;


    /**
     * Constructor.
     *
     * @param string $resource The resource (file, ...) used as source
     * @param string $library  The library to use as engine
     *
     *
     * @access protected
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Base_Template_Engine
     */
    protected function __construct($resource, $library)
    {
        // set the source
        $this->resource = $resource;
        $this->library  = $library;

        // init the template engine
        $this->_init();
    }

    /**
     * Initializes the template engine
     * This method is intend to load the configured template-engine via Doozr::module().
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _init()
    {
        // TODO: check if self::$instance is required here or if we can only set
        // decorated object

        // initialize and store the instance of template engine for further access
        self::$instance = Doozr_Loader_Serviceloader::load($this->library, $this->resource);

        // set this instance as decorated object of our generic Facade and
        // so we can access all the base methods of any lib easily!
        $this->setDecoratedObject(self::$instance);
    }

}
