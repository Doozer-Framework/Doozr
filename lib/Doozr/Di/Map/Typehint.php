<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Di - Map - Typehint
 *
 * Typehint.php - Typehint based map class of Di.
 *
 * PHP versions 5.5
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
 * @subpackage Doozr_Di_Map
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Di
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Di/ap.php';

/**
 * Doozr - Di - Map - Typehint
 *
 * Typehint based map class of Di.
 *
 * @category   Doozr
 * @package    Doozr_Di
 * @subpackage Doozr_Di_Map
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link       https://github.com/clickalicious/Di
 */
class Doozr_Di_Map_Typehint extends Doozr_Di_Map
{
    /**
     * Annotation parser instance
     *
     * @var Doozr_Di_Parser_Interface
     * @access protected
     */
    protected $parser;

    /**
     * Constructor.
     *
     * @param Doozr_Di_Collection      $collection Doozr_Di_Collection to collect dependencies in.
     * @param Doozr_Di_Parser_Typehint $parser     Doozr_Di_Parser_Typehint to parse dependencies with
     * @param Doozr_Di_Dependency      $dependency Doozr_Di_Dependency base object for cloning dependencies from.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     */
    public function __construct(
        Doozr_Di_Collection      $collection,
        Doozr_Di_Parser_Typehint $parser,
        Doozr_Di_Dependency      $dependency
    ) {
        // Store given instances
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
     * @return Doozr_Di_Collection The build collection
     * @access public
     */
    public function generate($classname)
    {
        // Set input
        $this->getParser()->setInput(
            ['classname' => $classname]
        );

        // Add dependencies to collection
        $this->addRawDependenciesToCollection($this->getParser()->parse());
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for parser.
     *
     * @param Doozr_Di_Parser_Interface $parser The parser of the parser.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
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
     * @return $this Instance for chaining
     * @access protected
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
     * @return Doozr_Di_Parser_Interface The Parser if set, otherwise NULL
     * @access protected
     */
    protected function getParser()
    {
        return $this->parser;
    }
}
