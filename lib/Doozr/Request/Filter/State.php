<?php


require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/State.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/State/Interface.php';

/**
 * Class Doozr_Request_Filter_State
 *
 * More specific filter version of Doozr_Base_State
 */
class Doozr_Request_Filter_State extends Doozr_Base_State
    implements
    Doozr_Base_State_Interface
{
    protected $fields;

    protected $sorting = [];

    protected $grouping = [];

    protected $offset;

    protected $limit;

    protected $arguments = [];



    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    public function fields($fields)
    {
        $this->setFields($fields);
        return $this;
    }

    public function getFields()
    {
        return $this->fields;
    }



    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
    }

    public function sorting($sorting)
    {
        $this->setSorting($sorting);
        return $this;
    }

    protected function addSorting($sorting)
    {
        $this->sorting[] = $sorting;
    }

    protected function removeSorting($index)
    {
        unset($this->sorting[$index]);
    }

    public function getSorting()
    {
        return $this->sorting;
    }



    public function setGrouping($grouping)
    {
        $this->grouping = $grouping;
    }

    public function grouping($grouping)
    {
        $this->setGrouping($grouping);
        return $this;
    }

    public function addGrouping($grouping)
    {
        $this->grouping[] = $grouping;
    }

    protected function removeGrouping($index)
    {
        unset($this->grouping[$index]);
    }

    public function getGrouping()
    {
        return $this->grouping;
    }



    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    public function offset($offset)
    {
        $this->setOffset($offset);
        return $this;
    }

    public function getOffset()
    {
        return $this->offset;
    }


    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function limit($limit)
    {
        $this->setLimit($limit);
        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }


    public function addArgument($argument, $value)
    {
        $this->arguments[$argument] = $value;
    }

    public function removeArgument($argument)
    {
        if (isset($this->arguments[$argument]) === true) {
            unset($this->arguments[$argument]);
        }
    }



    public function setPath($path)
    {
        $this->path = $path;
    }

    public function path($path)
    {
        $this->setPath($path);
        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }



    public function setQuery($query)
    {
        $this->query = $query;
    }

    public function query($query)
    {
        $this->setQuery($query);
        return $this;
    }

    public function getQuery()
    {
        return $this->query;
    }
}
