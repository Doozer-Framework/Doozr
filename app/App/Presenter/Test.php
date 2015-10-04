<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace App;

/**
 * Doozr - Demonstration - Presenter
 *
 * Test.php - Test-Presenter demonstration of Doozr's Presenter implementation.
 *
 * PHP versions 5.5
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
 *
 * Copyright (c) 2005 - 2015, Benjamin Carl - All rights reserved.
 *
 * @category   Doozr
 * @package    Doozr_App
 * @subpackage Doozr_App_Presenter
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
 * The index-presenter (Presenter_Test) and the index-action (indexAction) is
 * involved. The index-action retrieves the data from model:
 *
 *     $buffer = $this->getModel()->getData();
 *
 * and the it dispatches it via:
 *
 *     $this->setData($buffer);
 *
 * This is our so called IPO Input Processing Output. The presenter is the processor
 * in this construction. So it is responsible for manipulating values and stuff like
 * that. This is done here. The presenter retrieves the data (buffer) from the model
 * (optionally) and put it into render queue by calling setData($data). This will
 * result in a rendering of the data if a view with rendering capabilities was
 * attached. If not (now view for example) it just will end up in a HTTP response 200.
 * Everything handled fine.
 */
final class Presenter_Test extends \Doozr_Base_Presenter
{
    /**
     * Index-action.
     *
     * The default action of a route (e.g. /hello would try to reach the Presenter_Hello
     * and call its indexAction). In our demonstration we use this mechanism to route
     * the request simple with minimal configuration overhead.
     *
     * @Route(
     *     route="/doozr",
     *     methods="GET",
     *     presenter="test",
     *     action="index",
     *     name="doozr"
     * )
     *
     * @Route(
     *     route="/clickalicious",
     *     methods="GET",
     *     presenter="test",
     *     action="index",
     *     name="clickalicious"
     * )
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE always
     * @access public
     */
    public function indexAction()
    {
        // Set data to trigger events in view (and maybe also model)
        $this->setData(['title' => 'Doozr', 'hello' => 'Aloha']);

        // Signal success
        return true;
    }

    /**
     * Constructor replacement.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE always
     * @access protected
     */
    protected function __tearup()
    {
        // Intentionally left empty.
        // Here it's up to your imagination.

        // Must return TRUE always!
        return true;
    }

    /**
     * Shutdown event.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function __teardown()
    {
        // Intentionally left empty.
        // Here it's up to your imagination.
    }
}
