<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Di - Importer Json
 *
 * Json.php - Importer (JSON-Localize) of the Di-Framework
 *
 * PHP versions 5.4
 *
 * LICENSE:
 * Doozr - Di - The Dependency Injection Framework
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
 * @package    Doozr_Di
 * @subpackage Doozr_Di_Importer_Json
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Di
 */

require_once DI_PATH_LIB . 'Object/Freezer.php';
require_once DI_PATH_LIB_DI . 'Importer/Abstract.php';
require_once DI_PATH_LIB_DI . 'Importer/Interface.php';
require_once DI_PATH_LIB_DI . 'Dependency.php';
require_once DI_PATH_LIB_DI . 'Collection.php';

/**
 * Doozr - Di - Importer Json
 *
 * Importer (JSON-Localize) of the Di-Framework
 *
 * @category   Di
 * @package    Doozr_Di
 * @subpackage Doozr_Di_Importer_Json
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link       https://github.com/clickalicious/Di
 */
class Doozr_Di_Importer_Json extends Doozr_Di_Importer_Abstract implements Doozr_Di_Importer_Interface
{
    /*******************************************************************************************************************
     * PUBLIC API
     ******************************************************************************************************************/

    /**
     * Import content from JSON-File
     *
     * This method is intend to return the content of a JSON-Formatted file.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access public
     * (non-PHPdoc)
     * @see Doozr_Di_Importer_Interface::import()
     * @throws Doozr_Di_Exception
     */
    public function import()
    {
        // check for collection
        if (!$this->collection) {
            throw new Doozr_Di_Exception(
                'Could not import map. No collection set. Please set a collection first.'
            );
        }

        // check for input
        if (!$this->input) {
            throw new Doozr_Di_Exception(
                'Could not import map. No input set. Please set input first.'
            );
        }

        // get content from file
        $content = $this->_getObjectFromJsonFile($this->input);

        // get object freezer
        $freezer = new Object_Freezer();

        // iterate over all dependencies defined -> here TARGET CLASSNAME
        foreach ($content->map as $target) {
            // iterate current stdClass to retrieve name of dependend class
            foreach ($target as $classname => $configuration) {

                // arguments for target set?
                if (isset($configuration->arguments)) {
                    $this->collection->addArguments($classname, $configuration->arguments);
                }

                // constructor (e.g. singleton static method ...)
                if (isset($configuration->constructor)) {
                    $this->collection->setConstructor($classname, $configuration->constructor);
                }

                // get defined dependencies
                $dependencies = $configuration->dependencies;

                // iterate all dependencies for target
                foreach ($dependencies as $setup) {

                    // create new Dependency Object
                    $dependency = new Doozr_Di_Dependency($setup->classname);
                    $dependency->setConfiguration((array) $setup->config);
                    $dependency->setIdentifier($setup->identifier);

                    // check for frozen instance and thaw it if found
                    if ($setup->instance !== null) {
                        $dependency->setInstance($freezer->thaw(unserialize($setup->instance)));
                    }

                    // store arguments
                    if (isset($setup->arguments)) {
                        $dependency->setArguments($setup->arguments);
                    }

                    // store constructor
                    if (isset($setup->constructor)) {
                        $dependency->setConstructor($setup->constructor);
                    }

                    // add the dependency object to our collection
                    $this->collection->addDependency($classname, $dependency);
                }
            }
        }

        // success
        return true;
    }

    /**
     * Exports content as array containing Doozr_Di_Dependency-Instances
     *
     * This method is intend to export content as array containing Doozr_Di_Dependency-Instances.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array An array containing instances of Doozr_Di_Dependency for each dependency
     * @access public
     * (non-PHPdoc)
     * @see Doozr_Di_Importer_Interface::export()
     */
    public function export()
    {
        return $this->collection;
    }

    /*******************************************************************************************************************
     * PRIVATE
     ******************************************************************************************************************/

    /**
     * Returns the content of a JSON file as object
     *
     * This method is intend to return the content of a JSON file as an object.
     *
     * @param string $file The JSON file to read from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @throws Doozr_Di_Exception
     */
    private function _getObjectFromJsonFile($file)
    {
        // get content from file
        $content = $this->readFile($file);

        // parse string as json
        $content = @json_decode($content);

        if (!$content) {
            throw new Doozr_Di_Exception(
                'Error while importing dependencies. Given file does not contain valid JSON. '.
                'Please ensure that the content of the file is valid JSON.'
            );
        }

        // return json decoded object
        return $content;
    }
}
