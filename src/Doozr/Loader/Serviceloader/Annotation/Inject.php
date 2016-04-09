<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Doozr\Loader\Serviceloader\Annotation;

/**
 * Doozr - Loader - Serviceloader - Annotation - Inject
 *
 * Inject.php - Inject Annotation for DI of Doozr.
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
 * @package    Doozr_Loader
 * @subpackage Doozr_Loader_Serviceloader
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

/**
 * Doozr - Loader - Serviceloader - Annotation - Inject
 *
 * Inject Annotation for DI of Doozr.
 *
 * className       string      "Doozr_Registry" ASCII
 * target          string      "__construct" ASCII
 * instance        null
 * type            string      "constructor" ASCII
 * value           null
 * position        string      "1"
 *
 * @category   Doozr
 * @package    Doozr_Loader
 * @subpackage Doozr_Loader_Serviceloader
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 * @Annotation
 */
class Inject
{
    /**
     * The service Id.
     * @example doozr.cache.service OR doozr.configuration OR doozr.registry
     *
     * @var string
     * @access public
     */
    public $id;

    /**
     * The class to inject (name required for auto-matching of position in arguments or for instantiating).
     *
     * @var string
     * @access public
     */
    public $className;

    /**
     * The instance for an injection.
     *
     * @var object
     * @access public
     */
    public $instance;

    /**
     * The arguments for the dependency class.
     *
     * @var string
     * @access public
     */
    public $arguments;

    /**
     * The constructor of the dependency class.
     * It's not the constructor of the class where this dependency will be injected into.
     * @see {$target}
     *
     * @var string
     * @access public
     */
    public $constructor;

    /**
     * Link(Id) to another service.
     *
     * @var string
     * @access public
     */
    public $link;

    /**
     * The target for the injection (name of property, name of method like __construct or something like this).
     *
     * @var string
     * @access public
     */
    public $target = self::DEFAULT_TARGET;

    /**
     * The type of an injection.
     *
     * @var string
     * @access public
     */
    public $type = self::DEFAULT_TYPE;

    /**
     * The position for an injection.
     *
     * @var int
     * @access public
     */
    public $position;

    /**
     * The default type of an injection.
     *
     * @var string
     * @access public
     */
    const DEFAULT_TYPE = 'constructor';

    /**
     * The default target of an injection.
     *
     * @var string
     * @access public
     */
    const DEFAULT_TARGET = '__construct';

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     */
    public function __construct()
    {
        $this->id = $this->getRandomId();
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Creates a random unique Id for this instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The random and unique Id
     * @access protected
     */
    protected function getRandomId()
    {
        /*
        try {
            // Generate a version 1 (time-based) UUID object like e4eaaaf2-d142-11e1-b3e4-080027620cdd
            $uuid1 = Uuid::uuid1();
            $id = $uuid1->toString();

        } catch (UnsatisfiedDependencyException $exception) {
            $id = md5(microtime() . $this->getClassName());
        }
        */

        $id = md5(microtime() . $this->className);
        return $id;
    }
}
