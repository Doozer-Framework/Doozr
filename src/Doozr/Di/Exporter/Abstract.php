<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Di - Exporter - Abstract.
 *
 * Abstract.php - Abstract base class for exporter of Di.
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
 * @link       https://github.com/clickalicious/Di
 */

/**
 * Doozr - Di - Exporter - Abstract.
 *
 * Abstract base class for exporter of Di.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @link       https://github.com/clickalicious/Di
 */
abstract class Doozr_Di_Exporter_Abstract
{
    /**
     * Contains all dependencies as Doozr_Di_Collection.
     *
     * @var Doozr_Di_Collection
     */
    protected $collection;

    /**
     * Contains the output.
     *
     * @var mixed
     */
    protected $output;

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Sets the collection of the current instance.
     *
     * This method is intend to set the collection of dependencies of the current instance.
     *
     * @param Doozr_Di_Collection $collection The collection to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Exporter_Abstract
     */
    public function setCollection(Doozr_Di_Collection $collection)
    {
        $this->collection = $collection;

        // fluent / chaining
        return $this;
    }

    /**
     * Returns the collection of the current instance.
     *
     * This method is intend to return the collection of dependencies of the current instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Collection The collection of dependencies
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Sets the output.
     *
     * This method is intend to set the output.
     *
     * @param mixed $output The output to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Exporter_Abstract
     */
    public function setOutput($output)
    {
        // reset on setting new output!
        $this->reset();

        $this->output = $output;

        // fluent / chaining
        return $this;
    }

    /**
     * Returns the output.
     *
     * This method is intend to return the output.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed The output
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Resets the output.
     *
     * This method is intend to reset the output.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Exporter_Abstract
     */
    public function reset()
    {
        $this->output = null;

        // fluent / chaining
        return $this;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PROTECTED
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Writes content to a given file.
     *
     * This method is intend to write content to a given file
     *
     * @param string $file The file to read from
     * @param string $data
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int Bytes written, FALSE on failure
     *
     * @throws Doozr_Di_Exception
     */
    protected function writeFile($file, $data)
    {
        return file_put_contents(
            $file,
            $data
        );
    }
}
