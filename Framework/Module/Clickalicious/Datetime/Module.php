<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Clickalicious "Datetime" Module Loader
 *
 * Module.php - Datetime Module-Loader
 *
 * PHP versions 5
 *
 * LICENSE:
 * Clickalicious - Datetime-Module
 *
 * Copyright (c) 2005 - 2013, Benjamin Carl - All rights reserved.
 *
 * Please feel free to contact us via e-mail: opensource@clickalicious.de
 *
 * @category   Clickalicious
 * @package    Clickalicious_Module
 * @subpackage Clickalicious_Module_Datetime
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.clickalicious.de
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Loader/Moduleloader/Multiple.php';

/**
 * Clickalicious "Datetime" Module Loader
 *
 * Datetime Module-Loader
 *
 * @category   Clickalicious
 * @package    Clickalicious_Module
 * @subpackage Clickalicious_Module_Datetime
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.clickalicious.de
 * @see        -
 * @since      -
 */
class Clickalicious_Datetime_Module extends DoozR_Loader_Moduleloader_Multiple
{
    public function __construct()
    {
        $arguments = func_get_args();

        pred($arguments);
        //$this->load($arguments)
    }

}

?>
