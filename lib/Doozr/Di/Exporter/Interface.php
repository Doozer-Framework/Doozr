<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Di - Exporter - Interface
 *
 * Interface.php - Interface class for exporter of Di.
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
 * @subpackage Doozr_Di_Exporter
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Di
 */

/**
 * Doozr - Di - Exporter - Interface
 *
 * Interface class for exporter of Di.
 *
 * @category   Doozr
 * @package    Doozr_Di
 * @subpackage Doozr_Di_Exporter
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link       https://github.com/clickalicious/Di
 */
interface Doozr_Di_Exporter_Interface
{
    /**
     * Contract for importing @see Doozr_Di_Collection
     *
     * @param Doozr_Di_Collection $collection The @see Doozr_Di_Collection collection to import
     *
     * @return bool TRUE on success, otherwise FALSE
     */
    public function import(Doozr_Di_Collection $collection);

    /**
     * Contract for export
     *
     * @param bool $exportInstances TRUE to export instances, FALSE to do not
     *
     * @return Doozr_Di_Collection Dependencies
     */
    public function export($exportInstances = true);

    /**
     * Contract for setCollection
     *
     * @param Doozr_Di_Collection $collection The collection to set as an Doozr_Di_Collection object
     *
     * @return void
     */
    public function setCollection(Doozr_Di_Collection $collection);

    /**
     * Contract for getCollection
     *
     * @return Doozr_Di_Collection The collection of dependencies in an Doozr_Di_Collection object
     */
    public function getCollection();

    /**
     * Contract for setOutput
     *
     * @param string $output The output to set
     *
     * @return void
     */
    public function setOutput($output);

    /**
     * Contract for getOutput
     *
     * @return mixed The output set, otherwise NULL
     */
    public function getOutput();
}
