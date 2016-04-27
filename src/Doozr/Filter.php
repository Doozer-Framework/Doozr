<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Kernel - Filter
 *
 * Filter.php - Generic filter component of Doozr.
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
 * @package    Doozr_Kernel
 * @subpackage Doozr_Kernel_Filter
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Class.php';

/**
 * Doozr - Kernel - Filter
 *
 * Generic filter component of Doozr.
 *
 * @category   Doozr
 * @package    Doozr_Kernel
 * @subpackage Doozr_Kernel_Filter
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Filter extends Doozr_Base_Class
{
    /**
     * Base filter = no filter.
     *
     * @var array
     * @access protected
     */
    protected $filters = [];

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param array $filters The filter(s) to apply (search, replace)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     */
    public function __construct(array $filters)
    {
        $this
            ->filters($filters);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for filters.
     *
     * @param array $filters The filter(s) to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * Fluent: Setter for filters.
     *
     * @param array $filters The filter(s) to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function filters(array $filters)
    {
        $this->setFilters($filters);

        return $this;
    }

    /**
     * Getter for filters.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The filter(s) defined
     * @access protected
     */
    protected function getFilters()
    {
        return $this->filters;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Applies the stored set of filters (search, replace) and returns the result.
     *
     * @param string $target The target to apply filters to
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The result
     * @access public
     */
    public function apply($target)
    {
        $filters = $this->getFilters();

        foreach ($filters as $filter) {
            $target = preg_replace($filter->search, $filter->replace, $target);
        }

        return $target;
    }
}
