<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Di - Importer - Json.
 *
 * Json.php - Di importer for static JSON-maps. This importer imports a map
 * from a JSON-encoded file and adds them to a @see Doozr_Di_Collection.
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
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Di/Importer/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Di/Importer/Interface.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Di/Collection.php';

/**
 * Doozr - Di - Importer - Json.
 *
 * Di importer for static JSON-maps. This importer imports a map
 * from a JSON-encoded file and adds them to a @see Doozr_Di_Collection.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @link       https://github.com/clickalicious/Di
 */
class Doozr_Di_Importer_Json extends Doozr_Di_Importer_Abstract
    implements
    Doozr_Di_Importer_Interface
{
    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Import dependencies from a static JSON Map (Filesystem) and adds them to @see Doozr_Di_Collection.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The imported map
     *
     * @throws Doozr_Di_Exception
     */
    public function import()
    {
        if (null === $this->getInput()) {
            throw new Doozr_Di_Exception(
                'Could not import map. No input set. Please set input first.'
            );
        }

        // Get content from map file
        $this->setContent(
            $this->importMapFromFromJsonFile($this->getInput())
        );

        return $this->getContent();
    }

    /**
     * Exports content as array containing Doozr_Di_Dependency-Instances.
     *
     * This method is intend to export content as array containing Doozr_Di_Dependency-Instances.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array An array containing instances of Doozr_Di_Dependency for each dependency
     */
    public function export()
    {
        return $this->getContent();
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PROTECTED
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the content of a file in JSON-syntax as object.
     *
     * @param string $file The file in JSON-syntax to read from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return object Instance
     *
     * @throws Doozr_Di_Exception
     */
    protected function importMapFromFromJsonFile($file)
    {
        // get content from file
        $content = $this->validate(
            $this->readFile($file)
        );

        if (false === $content) {
            throw new Doozr_Di_Exception(
                sprintf('Error while importing dependencies: "%s".', json_last_error_msg())
            );
        }

        if (false === isset($content['map'][0])) {
            throw new Doozr_Di_Exception(
                sprintf('Require key "%s" not found!', 'map')
            );
        }

        return $content['map'][0];
    }

    /**
     * Validates that a passed string is valid json.
     *
     * @param string $input The input to validate
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool|array FALSE on error, array containing content
     */
    protected function validate($input)
    {
        if (true === is_string($input)) {
            $input = @json_decode($input, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $input = false;
            }
        }

        return $input;
    }
}
