<?php

class dummy implements ArrayAccess
{
    public $foo = 'bar';

    public function __sleep()
    {
        echo 'nacht';
    }

    public function __wakeup()
    {
        echo 'tag';
    }

    public function offsetSet($offset, $wert)
    {
        $this->{$offset} = $wert;
    }

    public function offsetExists($var)
    {
        return isset($this->{$var});
    }

    public function offsetUnset($var)
    {
        unset($this->{$var});
    }

    public function offsetGet($var)
    {
        echo 'bäm';
        die;
        return $this->{$var};
    }
}

$_GET = new dummy();

echo $_GET['foo'];
