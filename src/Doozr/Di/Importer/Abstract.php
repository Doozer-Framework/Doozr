<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Di - Importer - Abstract
 *
 * Abstract.php - Abstract base for importers of the Di Library. This base class
 * provides functionality for ...
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
 * @package    Doozr_Di
 * @subpackage Doozr_Di_Importer
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Di
 */

/**
 * Doozr - Di - Importer - Abstract
 *
 * Di abstract base class for importer.
 *
 * @category   Doozr
 * @package    Doozr_Di
 * @subpackage Doozr_Di_Importer
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link       https://github.com/clickalicious/Di
 * @abstract
 */
abstract class Doozr_Di_Importer_Abstract
{
    /**
     * Input.
     *
     * @var mixed
     * @access protected
     */
    protected $input;

    /**
     * Content.
     *
     * @var mixed
     * @access protected
     */
    protected $content;

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Sets the content of the current instance
     *
     * This method is intend to set the content of dependencies of the current instance.
     *
     * @param mixed $content The content to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Fluent: Sets the content of the current instance
     *
     * This method is intend to set the content of dependencies of the current instance.
     *
     * @param mixed $content The content to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function content($content)
    {
        $this->setContent($content);

        return $this;
    }

    /**
     * Returns the content of the current instance
     *
     * This method is intend to return the content of dependencies of the current instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The content of dependencies
     * @access public
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Setter for input.
     *
     * @param mixed $input The input to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setInput($input)
    {
        // reset on setting new input!
        $this->reset();

        $this->input = $input;
    }

    /**
     * Fluent: Setter for input.
     *
     * @param mixed $input The input to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function input($input)
    {
        $this->setInput($input);

        return $this;
    }

    /**
     * Getter for input.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The input
     * @access public
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Resets the input
     *
     * This method is intend to reset the input.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function reset()
    {
        $this->input = null;

        // Fluent / chaining
        return $this;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PROTECTED
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Reads content from given file
     *
     * This method is intend to read content from given file and store it in $content.
     *
     * @param string $file The file to read from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The function returns the read data or false on failure.
     * @access protected
     * @throws Doozr_Di_Exception
     */
    protected function readFile($file)
    {
        if (false === file_exists($file) || false === is_file($file)) {
            throw new Doozr_Di_Exception(
                sprintf(
                    'Error reading file. File "%s" does not exist or is not a valid file', $file
                )
            );
        }

        return file_get_contents($file);
    }
}
