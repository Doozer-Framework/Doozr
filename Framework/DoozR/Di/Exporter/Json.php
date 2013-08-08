<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Di Exporter Json
 *
 * Json.php - Exporter (JSON-Format) of the Di-Framework
 *
 * PHP versions 5
 *
 * LICENSE:
 * Di - The Dependency Injection Framework
 *
 * Copyright (c) 2012, Benjamin Carl - All rights reserved.
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
 * @category   Di
 * @package    DoozR_Di_Framework
 * @subpackage DoozR_Di_Framework_Exporter_Json
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2012 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id: $
 * @link       https://github.com/clickalicious/Di
 * @see        -
 * @since      -
 */

require_once DI_PATH_LIB_DI.'Exporter/Abstract.php';
require_once DI_PATH_LIB_DI.'Exporter/Interface.php';
require_once DI_PATH_LIB_DI.'Dependency.php';
require_once DI_PATH_LIB_DI.'Collection.php';

/**
 * external library Object-Freezer by Sebastian Bergmann
 */
require_once DI_PATH_LIB.'Object/Freezer.php';

/**
 * Di Exporter Json
 *
 * Exporter (JSON-Format) of the Di-Framework
 *
 * @category   Di
 * @package    DoozR_Di_Framework
 * @subpackage DoozR_Di_Framework_Exporter_Json
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2012 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link       https://github.com/clickalicious/Di
 * @see        -
 * @since      -
 */
class DoozR_Di_Exporter_Json extends DoozR_Di_Exporter_Abstract implements DoozR_Di_Exporter_Interface
{

    /*******************************************************************************************************************
     * PUBLIC API
     ******************************************************************************************************************/

    /**
     * Exports current content of DoozR_Di_Collection ($this->collection) to a JSON-File
     *
     * This method is intend to write current content of DoozR_Di_Collection ($this->collection) to a JSON-File.
     *
     * @param boolean $exportInstances TRUE to export instances as well, otherwise FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     * (non-PHPdoc)
     * @see DoozR_Di_Exporter_Interface::export()
     * @throws DoozR_Di_Exception
     */
    public function export($exportInstances = true)
    {
        // check for collection
        if (!$this->collection) {
            throw new DoozR_Di_Exception(
                'Could not import map. No collection set. Please set a collection first.'
            );
        }

        // check for input
        if (!$this->output) {
            throw new DoozR_Di_Exception(
                'Could not export map. No output file set. Please set output first.'
            );
        }

        // check if input directory exists and if it is writable
        if (!is_dir(dirname($this->output)) || (!is_writable(dirname($this->output)))) {
            throw new DoozR_Di_Exception(
                'Could not export map. Output directory "'.dirname($this->output).'" does not exist or isn\'t writable.'
            );
        }

        /*
        "Foo": {
            "arguments": ["I R Baboon!"],
            "dependencies": [
                {
                    "id": "Database1",
                    "classname": "Database",
                    "arguments": ["foo", "bar", "baz"],
                    "instance": null,
                    "config": {
                        "type": "constructor"
                    }
                },
                {
                    "id": "Logger1",
                    "classname": "Logger",
                    "instance": null,
                    "config": {
                        "type": "method",
                        "value": "setLogger"
                    }
                }
            ]
        }
        */

        // get instance of the freezer
        $freezer = new Object_Freezer();

        // the collection for export in correct JSON structure
        $collection = array();

        // iterate over collection
        foreach ($this->collection as $class => $dependencies) {

            // collect dependencies for $class in an array
            $collection[$class] = new stdClass();

            // check for arguments
            ($this->collection->getArguments($class)) ?
                $collection[$class]->arguments = $this->collection->getArguments($class) :
                null;

            // check for custom arguments
            ($this->collection->getConstructor($class)) ?
                $collection[$class]->constructor = $this->collection->getConstructor($class) :
                null;

            // iterate over existing dependencies, translate to JSON structure and store temporary in $collection[]
            foreach ($dependencies as $count => $dependency) {
                /* @var $dependency DoozR_Di_Dependency */

                // temp object for storage
                $tmp = new stdClass();

                // the identifier
                $tmp->identifier = $dependency->getIdentifier();

                // the classname
                $tmp->classname = $dependency->getClassname();

                // the arguments
                if ($dependency->getArguments()) {
                    $tmp->arguments = $dependency->getArguments();
                }

                // the instance
                if ($exportInstances === true) {
                    if (is_object($dependency->getInstance())) {
                        $tmp->instance = serialize($freezer->freeze($dependency->getInstance()));
                    } else {
                        $tmp->instance = $dependency->getInstance();
                    }
                } else {
                    $tmp->instance = null;
                }

                // the config
                $tmp->config = $dependency->getConfiguration();

                // store created object to $collection
                $collection[$class]->dependencies[] = $tmp;
            }
        }

        // create tmp object for JSON export
        $output = new stdClass();

        // set collection as output for our map
        $output->map = array(
            $collection
        );

        // write content to file
        $this->writeFile(
            $this->output, json_encode($output)
        );

        // success
        return true;
    }

    /**
     * Imports content to store as collection for later export
     *
     * This method is intend to set the collection used for export.
     *
     * @param DoozR_Di_Collection $collection The collection instance of DoozR_Di_Collection to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     * (non-PHPdoc)
     * @see DoozR_Di_Exporter_Interface::import()
     */
    public function import(DoozR_Di_Collection $collection)
    {
        return ($this->collection = $collection);
    }
}
