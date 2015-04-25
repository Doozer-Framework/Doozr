<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Demonstration - Presenter
 *
 * Index.php - Base-Class for all classes of the Doozr Framework except
 * the classes following the singleton pattern.
 *
 * PHP versions 5.4
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
 *
 * Copyright (c) 2005 - 2015, Benjamin Carl - All rights reserved.
 *
 * @category   Doozr
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Class
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

use Doozr\Route\Annotation\Route;

/**
 * Demonstration Presenter for 'Hello World!'
 *
 * This demonstration of a simple presenter is the base for handling a request.
 * The index-presenter (Presenter_Index) and the index-action (indexAction) is
 * involved. The index-action retrieves the data from model:
 *
 *     $buffer = $this->getModel()->getData();
 *
 * and the it dispatches it via:
 *
 *     $this->setData($buffer);
 *
 * which will result in a rendering of the data if a view with rendering capabilities
 * was attached. If not (now view for example) it just will end up in a HTTP response
 * 200. Everything handled fine.
 *
 * @Route(
 *     route="/index/index",
 *     method="GET",
 *     presenter="index",
 *     action="index"
 * )
 *
 * @Route(
 *     route="/",
 *     method="GET",
 *     presenter="index",
 *     action="index"
 * )
 */
final class Presenter_Index extends Doozr_Base_Presenter
{
    /**
     * Index-action.
     *
     * The default action of a route (e.g. /hello would try to reach the Presenter_Hello
     * and call its indexAction). In our demonstration we use this mechanism to route
     * the request simple with minimal configuration overhead.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function indexAction()
    {
        // Retrieve data from model instance
        $buffer = $this->getModel()->getData();

        // Set data to trigger events in view (and maybe also model [two way data binding])
        $this->setData($buffer);
    }
}
