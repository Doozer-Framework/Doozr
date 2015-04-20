<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Di - Importer Interface
 *
 * Interface.php - Interface class for all Importer of the Di-Framework
 *
 * PHP versions 5.4
 *
 * LICENSE:
 * DoozR - Di - The Dependency Injection Framework
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
 * @package    DoozR_Di
 * @subpackage DoozR_Di_Importer_Interface
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Di
 */

/**
 * DoozR - Di - Importer Interface
 *
 * Interface class for all Importer of the Di-Framework
 *
 * @category   Di
 * @package    DoozR_Di
 * @subpackage DoozR_Di_Importer_Interface
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link       https://github.com/clickalicious/Di
 */
interface DoozR_Di_Importer_Interface
{
    /**
     * Contract for import
     *
     * @return boolean TRUE on success, otherwise FALSE
     */
    public function import();

    /**
     * Contract for export
     *
     * @return array An array containing a collection of DoozR_Di_Dependency instances
     */
    public function export();

    /**
     * Contract for setCollection
     *
     * @param DoozR_Di_Collection $collection The collection to set as an DoozR_Di_Collection object
     *
     * @return void
     */
    public function setCollection(DoozR_Di_Collection $collection);

    /**
     * Contract for getCollection
     *
     * @return DoozR_Di_Collection The collection of dependencies in an DoozR_Di_Collection object
     */
    public function getCollection();

    /**
     * Contract for setInput
     *
     * @param mixed $input The input to set
     *
     * @return void
     */
    public function setInput($input);

    /**
     * Contract for getInput
     *
     * @return mixed The input set, otherwise NULL
     */
    public function getInput();
}
