<?php

class Doozr_Formatter_Default extends \DebugBar\DataFormatter\DataFormatter
{
    public function formatVar($var)
    {
        echo $var;die;
        return $var;

    }
}
