<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Request - Filter - State
 *
 * State.php - DTO: Filter model representation.
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
 * @package    Doozr_Request
 * @subpackage Doozr_Request_Filter
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/State.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/State/Interface.php';

/**
 * Doozr - Request - Filter - State
 *
 * State.php - DTO: Filter model representation.
 *
 * @category   Doozr
 * @package    Doozr_Request
 * @subpackage Doozr_Request_Filter
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Request_Filter_State extends Doozr_Base_State
    implements
    Doozr_Base_State_Interface
{
    /**
     * The offset to use.
     *
     * @var int
     * @access protected
     */
    protected $offset;

    /**
     * The limit to use.
     *
     * @var int
     * @access protected
     */
    protected $limit;

    /**
     * The path of the current URL.
     *
     * @var string
     * @access protected
     */
    protected $path;

    /**
     * The query of the current URL.
     *
     * @var string
     * @access protected
     */
    protected $query;

    /**
     * The fields to filter.
     *
     * @var array
     * @access protected
     */
    protected $fields = [];

    /**
     * The sorting to use.
     *
     * @var array
     * @access protected
     */
    protected $sorting = [];

    /**
     * The grouping to use.
     *
     * @var array
     * @access protected
     */
    protected $grouping = [];

    /**
     * The arguments to use.
     *
     * @var array
     * @access protected
     */
    protected $arguments = [];

    /*------------------------------------------------------------------------------------------------------------------
    | SETTER, GETTER, ADDER, REMOVER, ISSER & HASSER
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for fields.
     *
     * @param array $fields The fields to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * Fluent: Setter for fields.
     *
     * @param array $fields The fields to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function fields(array $fields)
    {
        $this->setFields($fields);

        return $this;
    }

    /**
     * Getter for fields.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Collection of fields
     * @access public
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Setter for sorting.
     *
     * @param array $sorting The sorting to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setSorting(array $sorting)
    {
        $this->sorting = $sorting;
    }

    /**
     * Fluent: Setter for sorting.
     *
     * @param array $sorting The sorting to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function sorting(array $sorting)
    {
        $this->setSorting($sorting);

        return $this;
    }

    /**
     * Adder for sorting.
     *
     * @param mixed $sorting The sorting to add.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function addSorting($sorting)
    {
        $this->sorting[] = $sorting;
    }

    /**
     * Remover for sorting.
     *
     * @param mixed $index The index of the sorting to remove.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function removeSorting($index)
    {
        unset($this->sorting[$index]);
    }

    /**
     * Getter for sorting.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Collection of sorting
     * @access public
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * Setter for grouping.
     *
     * @param array $grouping The grouping to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setGrouping(array $grouping)
    {
        $this->grouping = $grouping;
    }

    /**
     * Fluent: Setter for grouping.
     *
     * @param array $grouping The grouping to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function grouping($grouping)
    {
        $this->setGrouping($grouping);

        return $this;
    }

    /**
     * Adder for grouping.
     *
     * @param mixed $grouping The grouping to add.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function addGrouping($grouping)
    {
        $this->grouping[] = $grouping;
    }

    /**
     * Remover for grouping.
     *
     * @param mixed $index The index of the grouping to be removed.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function removeGrouping($index)
    {
        unset($this->grouping[$index]);
    }

    /**
     * Getter for grouping.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array|null The grouping if set, otherwise NULL
     * @access public
     */
    public function getGrouping()
    {
        return $this->grouping;
    }

    /**
     * Setter for offset.
     *
     * @param int $offset The offset to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * Fluent: Setter for offset.
     *
     * @param int $offset The offset to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function offset($offset)
    {
        $this->setOffset($offset);

        return $this;
    }

    /**
     * Getter for offset.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The offset if set, otherwise NULL
     * @access public
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Setter for limit.
     *
     * @param int $limit The limit to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * Fluent: Setter for limit.
     *
     * @param int $limit The limit to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function limit($limit)
    {
        $this->setLimit($limit);

        return $this;
    }

    /**
     * Getter for limit.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The limit if set, otherwise NULL (no limit)
     * @access public
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Setter for path.
     *
     * @param string $path The path to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Fluent: Setter for path.
     *
     * @param string $path The path to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function path($path)
    {
        $this->setPath($path);

        return $this;
    }

    /**
     * Getter for path.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The path if set, otherwise NULL
     * @access public
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Setter for query.
     *
     * @param string $query The query to use.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * Fluent: Setter for query.
     *
     * @param string $query The query to use.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function query($query)
    {
        $this->setQuery($query);

        return $this;
    }

    /**
     * Getter for query.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The query if set, otherwise NULL
     * @access public
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Adder for argument.
     *
     * @param string $argument The argument to add.
     * @param mixed  $value    The value of the argument.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function addArgument($argument, $value)
    {
        $this->arguments[$argument] = $value;
    }

    /**
     * Remover for argument.
     *
     * @param string $argument The argument to remove.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function removeArgument($argument)
    {
        if (true === isset($this->arguments[$argument])) {
            unset($this->arguments[$argument]);
        }
    }
}
