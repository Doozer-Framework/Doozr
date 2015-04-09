<?php

/**
 * Class DoozR_Config_Service_Container_Base
 */
abstract class DoozR_Config_Service_Container_Base
{
    protected $path;

    protected $logger;

    protected $cache;


    /**
     * @param DoozR_Path_Interface $path
     * @param DoozR_Logger_Interface $logger
     * @param $cache
     */
    public function __construct(DoozR_Path_Interface $path, DoozR_Logger_Interface $logger, $cache)
    {
        $this->path   = $path;
        $this->logger = $logger;
        $this->cache  = $cache;
    }
}
