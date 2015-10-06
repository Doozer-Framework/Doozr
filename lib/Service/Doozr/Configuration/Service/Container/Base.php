<?php

/**
 * Class Doozr_Config_Service_Container_Base
 */
abstract class Doozr_Config_Service_Container_Base
{
    protected $path;

    protected $logger;

    protected $cache;


    /**
     * @param Doozr_Path_Interface $path
     * @param Doozr_Logging_Interface $logger
     * @param $cache
     */
    public function __construct(Doozr_Path_Interface $path, Doozr_Logging_Interface $logger, $cache)
    {
        $this->path   = $path;
        $this->logger = $logger;
        $this->cache  = $cache;
    }
}
