<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Di - Map Static
 *
 * Static.php - Static map class of the Di-Library
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
 * @category   Doozr
 * @package    Doozr_Di
 * @subpackage Doozr_Di_Map_Static
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Di
 */

require_once DI_PATH_LIB_DI . 'Map.php';

/**
 * Doozr - Di - Map Static
 *
 * Static map class of the Di-Library
 *
 * @category   Doozr
 * @package    Doozr_Di
 * @subpackage Doozr_Di_Map_Static
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link       https://github.com/clickalicious/Di
 */
class Doozr_Di_Map_Static extends Doozr_Di_Map
{
    /**
     * An instance of Doozr_Di_Importer_*
     *
     * @var Doozr_Di_Importer_*
     * @access protected
     */
    protected $importer;


    /*******************************************************************************************************************
     * PHP CONSTRUCT
     ******************************************************************************************************************/

    /**
     * Constructor.
     *
     * Constructor of this class
     *
     * @param Doozr_Di_Collection         $collection An instance of Doozr_Di_Collection to collect dependencies in
     * @param Doozr_Di_Importer_Interface $importer   An instance of Doozr_Di_Importer_Json to import dependencies with
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \Doozr_Di_Map_Static
     * @access public
     */
    public function __construct(Doozr_Di_Collection $collection, Doozr_Di_Importer_Interface $importer)
    {
        // store given instances
        $this->collection = $collection;
        $this->importer   = $importer;

        $this->importer->setCollection($collection);
    }

    /*******************************************************************************************************************
     * PUBLIC API
     ******************************************************************************************************************/

    /**
     * Builds the collection from dependency parser result for given class
     *
     * This method is intend to build the collection from dependency parser result for given class.
     *
     * @param string $filename The name of the file to parse dependencies from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Di_Collection The build collection
     * @access public
     */
    public function generate($filename)
    {
        // set input
        $this->importer->setInput($filename);

        // do the import
        $this->importer->import();
    }
}
