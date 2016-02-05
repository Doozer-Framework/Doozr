<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Di - Map - Annotation.
 *
 * Annotation.php - Annotation based map representation of Di. This map is filled
 * through an annotation reader instead like for example through a JSON importer
 * like we did in our Static map.
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
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Di/Map.php';

/**
 * Doozr - Di - Map - Annotation.
 *
 * Annotation based map representation of Di. This map is filled
 * through an annotation reader instead like for example through a JSON importer
 * like we did in our Static map.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @link       https://github.com/clickalicious/Di
 */
class Doozr_Di_Map_Annotation extends Doozr_Di_Map
{
    /**
     * Annotation parser instance.
     *
     * @var Doozr_Di_Parser_Interface
     */
    protected $parser;

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param Doozr_Di_Collection       $collection Doozr_Di_Collection to collect dependencies in.
     * @param Doozr_Di_Parser_Interface $parser     Doozr_Di_Parser_Annotation to parse dependencies with.
     * @param Doozr_Di_Dependency       $dependency Doozr_Di_Dependency base object for cloning dependencies from.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __construct(
        Doozr_Di_Collection       $collection,
        Doozr_Di_Parser_Interface $parser,
        Doozr_Di_Dependency       $dependency
    ) {
        $this
            ->collection($collection)
            ->parser($parser)
            ->dependency($dependency);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Builds the collection from dependency parser result for given class.
     *
     * @param string $classname The name of the class to parse dependencies for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function generate($classname)
    {
        // set input
        $this->getParser()->setInput(
            ['classname' => $classname]
        );

        // Add these dependencies to collection
        $this->addRawDependenciesToCollection($this->getParser()->parse(), $classname);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Adds the given raw dependencies (array) to the collection for given classname
     * This method is intend to add the given raw dependencies (array) to the collection for given classname.
     *
     * @param array  $rawDependencies Dependencies as raw array.
     * @param string $classname       Name of the class having the dependencies.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function addRawDependenciesToCollection(array $rawDependencies, $classname = '')
    {
        $rawDependencies = [
            $classname => [
                'classname'    => $classname,
                'dependencies' => array_merge([], $rawDependencies),
            ],
        ];

        parent::addRawDependenciesToCollection($rawDependencies);
    }

    /**
     * Setter for parser.
     *
     * @param Doozr_Di_Parser_Interface $parser The parser of the parser.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setParser(Doozr_Di_Parser_Interface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Fluent: Setter for parser.
     *
     * @param Doozr_Di_Parser_Interface $parser The parser of the parser.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function parser(Doozr_Di_Parser_Interface $parser)
    {
        $this->setParser($parser);

        return $this;
    }

    /**
     * Getter for Parser.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Di_Parser_Interface The Parser if set, otherwise NULL
     */
    protected function getParser()
    {
        return $this->parser;
    }
}
