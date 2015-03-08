<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace DoozR\Route\Annotation;

/**
 * DoozR - Route - Annotation - Route
 *
 * Route.php - Route Annotation for DI of DoozR.
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2014, Benjamin Carl - All rights reserved.
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
 *   must display the following acknowledgement: This product includes software
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
 * @category   DoozR
 * @package    DoozR_Route
 * @subpackage DoozR_Route_Annotation
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

/**
 * DoozR - Loader - Serviceloader - Annotation - Route
 *
 * Route Annotation for DI of DoozR.
 *
 * class           string      "DoozR_Registry" ASCII
 * identifier      string      "__construct" ASCII
 * instance        null
 * type            string      "constructor" ASCII
 * value           null
 * position        string      "1"
 *
 * @category   DoozR
 * @package    DoozR_Route
 * @subpackage DoozR_Route_Annotation
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @Annotation
 */
class Route
{
    /**
     * The presenter of the route.
     *
     * @var string
     * @access public
     */
    public $presenter = null;

    /**
     * The action of a route.
     *
     * @var string
     * @access public
     */
    public $action = null;

    /**
     * The route.
     *
     * @var string
     * @access public
     */
    public $route = self::DEFAULT_ROUTE;

    /**
     * The request method (Verb) this route listen on.
     *
     * @var string
     * @access public
     */
    public $methods = self::DEFAULT_METHODS;

    /**
     * Name of the route.
     * Just used as an additional identifier if required.
     *
     * @var string
     * @access public
     */
    public $name;

    /**
     * The description of the route.
     *
     * @var string
     * @access public
     */
    public $description;

    /**
     * The default presenter of a route.
     *
     * @var string
     * @access public
     * @const
     */
    const DEFAULT_PRESENTER = 'index';

    /**
     * The default action of a route.
     *
     * @var string
     * @access public
     * @const
     */
    const DEFAULT_ACTION = 'index';

    /**
     * The default route.
     *
     * @var string
     * @access public
     * @const
     */
    const DEFAULT_ROUTE = '/index/index';

    /**
     * The default methods.
     * By default means -> all methods/verbs allowed.
     * This can be changed by passing a string, for multiple
     * allowed methods/verbs separate them by comma.
     *
     * @example
     * @Route(
     *   ...
     *   method="GET"
     * )
     *
     * @Route(
     *   ...
     *   method="GET,POST"
     * )
     *
     * @var string
     * @access public
     * @const
     */
    const DEFAULT_METHODS = 'OPTIONS,GET,HEAD,POST,PUT,DELETE,TRACE,CONNECT';

    /**
     * Setter for route
     *
     * @param string $route The route to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * Setter for route
     *
     * @param string $route The route to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function route($route)
    {
        $this->setRoute($route);
        return $this;
    }

    /**
     * Getter for route
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The route
     * @access public
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Setter for methods
     *
     * @param string $methods The methods to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setMethods($methods)
    {
        $this->methods = $methods;
    }

    /**
     * Setter for methods
     *
     * @param string $methods The methods to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function methods($methods)
    {
        $this->setMethods($methods);
        return $this;
    }

    /**
     * Getter for methods
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The methods
     * @access public
     */
    public function getMethods()
    {
        return explode(',', $this->methods);
    }

    /**
     * Setter for presenter
     *
     * @param string $presenter The presenter to set
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
     * Setter for presenter
     *
     * @param string $presenter The presenter to set
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
     * Getter for presenter
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The presenter
     * @access public
     */
    public function getPresenter()
    {
        return $this->presenter;
    }

    /**
     * Setter for action
     *
     * @param string $action The action to set
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
     * Setter for action
     *
     * @param string $action The action to set
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
     * Getter for action
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The action
     * @access public
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Setter for name
     *
     * @param string $name The name to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Setter for name
     *
     * @param string $name The name to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function name($name)
    {
        $this->setName($name);
        return $this;
    }

    /**
     * Getter for name
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The name
     * @access public
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Setter for description
     *
     * @param string $description The description to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Setter for description
     *
     * @param string $description The description to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function description($description)
    {
        $this->setDescription($description);
        return $this;
    }

    /**
     * Getter for description
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The description
     * @access public
     */
    public function getDescription()
    {
        return $this->description;
    }
}
