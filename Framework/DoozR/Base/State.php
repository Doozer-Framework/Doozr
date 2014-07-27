<?php


class DoozR_Base_State extends DoozR_Base_Class
{
    public function unwrap()
    {
        return get_object_vars($this);
    }
}
