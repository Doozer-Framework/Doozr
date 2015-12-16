<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Request - Route - State
 *
 * State.php - DTO: Route model representation.
 *
 * PHP versions 5.5
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
 *
 * Copyright (c) 2005 - 2016, Benjamin Carl - All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * - All advertising materials mentioning features or use of this software
 *   must display the following acknowledgment: This product includes software
 *   developed by Benjamin Carl and other contributors.
 * - Neither the name Benjamin Carl nor the names of other contributors
 *   may be used to endorse or promote products derived from this
 *   software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * Please feel free to contact us via e-mail: opensource@clickalicious.de
 *
 * @category   Doozr
 * @package    Doozr_Request
 * @subpackage Doozr_Request_Route
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/State.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/State/Interface.php';

/**
 * Doozr - Request - Route - State
 *
 * DTO: Route model representation.
 *
 * @category   Doozr
 * @package    Doozr_Request
 * @subpackage Doozr_Request_Route
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 * @final
 */
final class Doozr_Request_Route_State extends Doozr_Base_State
    implements
    Doozr_Base_State_Interface
{
    /**
     * The presenter the route is targeting.
     *
     * @var string
     * @access protected
     */
    protected $presenter;

    /**
     * The action the route is targeting.
     *
     * @var string
     * @access protected
     */
    protected $action;

    /**
     * The default presenter.
     *
     * @var string
     * @access public
     */
    const DEFAULT_PRESENTER = 'index';

    /**
     * The default action.
     *
     * @var string
     * @access public
     */
    const DEFAULT_ACTION = 'index';

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param string $presenter The presenter
     * @param string $action    The action
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     */
    public function __construct($presenter = self::DEFAULT_PRESENTER, $action = self::DEFAULT_ACTION)
    {
        $this
            ->presenter($presenter)
            ->action($action);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | SETTER, GETTER, ADDER, REMOVER, ISSER & HASSER
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for presenter.
     *
     * @param string $presenter The presenter
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setPresenter($presenter)
    {
        $this->presenter = $presenter;
    }

    /**
     * Fluent: Setter for presenter.
     *
     * @param string $presenter The presenter
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function presenter($presenter)
    {
        $this->setPresenter($presenter);

        return $this;
    }

    /**
     * Getter for presenter.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Presenter
     * @access public
     */
    public function getPresenter()
    {
        return $this->presenter;
    }

    /**
     * Setter for action.
     *
     * @param string $action The action
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Fluent: Setter for action.
     *
     * @param string $action The action
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function action($action)
    {
        $this->setAction($action);

        return $this;
    }

    /**
     * Getter for action.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Action
     * @access public
     */
    public function getAction()
    {
        return $this->action;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | MAGIC
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns a string representation of this class instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Presenter:Action as string
     * @access public
     */
    public function __toString()
    {
        return $this->getPresenter() . ':' . $this->getAction();
    }

}
