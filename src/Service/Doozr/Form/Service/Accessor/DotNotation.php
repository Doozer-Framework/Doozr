<?php

class Doozr_Form_Service_Accessor_DotNotation extends Doozr_Base_Accessor_DotNotation
{
    /**
     * Converts a HTML Form array notation to DotNotation.
     * (e.g. file[foo][bar] to file.foo.bar || file[] to ...)
     *
     * @param $name
     *
     * @return mixed
     */
    public function translateArrayToDotNotation($name)
    {
        $name = str_replace('[', '.', $name);
        $name = str_replace(']', '', $name);
        $name = str_replace('..', '.', $name);
        $name = preg_replace('/\.$/', '.0', $name);

        return $name;
    }

}
