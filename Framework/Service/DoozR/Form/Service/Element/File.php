<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Form - Service
 *
 * File.php - Extension to default Input-Element <input type="..." ...
 * but with some specific file-upload specific.
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
 * @subpackage DoozR_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Form/Service/Element/Input.php';

/**
 * DoozR - Form - Service
 *
 * Extension to default Input-Element <input type="..." ...
 * but with some specific file-upload specific.
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Form_Service_Element_File extends DoozR_Form_Service_Element_Input
{
    /**
     * The maximum filesize allowed for fileuploads in Bytes.
     *
     * @var integer
     * @access protected
     */
    protected $maxFilesize = 0;

    /**
     * The filename
     *
     * @var string
     * @access protected
     */
    protected $file;


    /**
     * Constructor.
     *
     * @param string $name      The name of the element
     * @param array  $arguments The arguments passed with current request (e.g. $_POST, $_GET ...)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Element_Radio Instance of this class
     * @access public
     */
    public function __construct(
        $name,
        $arguments = array()
    ) {
        $this->setType('file');
        return parent::__construct($name, $arguments);
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Render to HTML also specific for file.
     *
     * @param boolean $forceRender TRUE to force a render also if already rendered, otherwise
     *                             FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The rendered HTML
     * @access public
     */
    public function render($forceRender = false)
    {
        // first we take the rendered html
        $html = parent::render(true);

        if ($this->getFile() !== null) {

            // transform filename to filename without path ...
            $pathinfo = pathinfo($this->getFile());

            $html = '<div id="DoozR-Form-Uploaded-1" class="DoozR-Form-Uploaded">' .
                $pathinfo['basename'] .
                '</div>' . $html;
        }

        // and now we add a special field right before this one
        $html = '<input type="hidden" name="MAX_FILE_SIZE" value="'.$this->getMaxFilesize().'" />' . PHP_EOL . $html;

        return $html;
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Setter & Getter
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for value.
     *
     * @param mixed $value The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setValue($value)
    {
        return $this->setFile($value);
    }

    /**
     * Getter for value.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Value of this element
     * @access public
     */
    public function getValue()
    {
        return $this->getFile();
    }

    /**
     * Setter for file.
     *
     * @param string|null $file The file
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setFile($file = null)
    {
        $this->file = $file;
    }

    /**
     * Getter for file.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The filename if set, otherwise NULL
     * @access public
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Sets the maximum size in bytes a file is allowed to have.
     *
     * @param integer|string $maxFilesize The maximum allowed size in bytes
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The size in bytes
     * @access public
     */
    public function setMaxFilesize($maxFilesize = 'auto')
    {
        switch ($maxFilesize) {
            case 'auto':
                $maxFilesize = $this->convertToBytes(
                    ini_get('upload_max_filesize')
                );
                break;
        }

        $this->maxFilesize = $maxFilesize;
        $this->setAttribute('filesize', $maxFilesize);
    }

    /**
     * Returns the maximum size in bytes a file is allowed to have.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The size in bytes
     * @access public
     */
    public function getMaxFilesize()
    {
        return $this->maxFilesize;
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Tools & Helper
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Converts an INI-Value from string to bytes
     *
     * @param string $value The value to convert
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The value in bytes
     * @access protected
     */
    protected function convertToBytes($value)
    {
        $value = trim($value);

        $last = strtolower($value[strlen($value)-1]);

        switch ($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $value *= 1024;
                break;

            case 'm':
                $value *= 1024;
                break;

            case 'k':
                $value *= 1024;
                break;
        }

        return $value;
    }
}
