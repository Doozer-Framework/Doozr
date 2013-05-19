<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Clickalicious "Datetime" Service Loader
 *
 * Service.php - Datetime Service-Loader
 *
 * PHP versions 5
 *
 * LICENSE:
 * Clickalicious - Datetime-Service
 *
 * Copyright (c) 2005 - 2013, Benjamin Carl - All rights reserved.
 *
 * Please feel free to contact us via e-mail: opensource@clickalicious.de
 *
 * @category   Clickalicious
 * @package    Clickalicious_Service
 * @subpackage Clickalicious_Service_Datetime
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.clickalicious.de
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Loader/Serviceloader/Multiple.php';

/**
 * Clickalicious "Datetime" Service Loader
 *
 * Datetime Service-Loader
 *
 * @category   Clickalicious
 * @package    Clickalicious_Service
 * @subpackage Clickalicious_Service_Datetime
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.clickalicious.de
 * @see        -
 * @since      -
 */
class Clickalicious_Datetime_Service extends DoozR_Loader_Serviceloader_Multiple
{
    public function __construct()
    {
        $arguments = func_get_args();

        pred($arguments);
        //$this->load($arguments)
    }

}

?>
