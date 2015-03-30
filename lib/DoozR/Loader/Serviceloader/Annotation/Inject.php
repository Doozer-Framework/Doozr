<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace DoozR\Loader\Serviceloader\Annotation;

/**
 * DoozR - Loader - Serviceloader - Annotation - Inject
 *
 * Inject.php - Inject Annotation for DI of DoozR.
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2015, Benjamin Carl - All rights reserved.
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
 * @package    DoozR_Loader
 * @subpackage DoozR_Loader_Serviceloader
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

/**
 * DoozR - Loader - Serviceloader - Annotation - Inject
 *
 * Inject Annotation for DI of DoozR.
 *
 * class           string      "DoozR_Registry" ASCII
 * identifier      string      "__construct" ASCII
 * instance        null
 * type            string      "constructor" ASCII
 * value           null
 * position        string      "1"
 *
 * @category   DoozR
 * @package    DoozR_Loader
 * @subpackage DoozR_Loader_Serviceloader
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @Annotation
 */
class Inject
{
    /**
     * The class to inject (name required for automatching of
     * position in arguments or for instanciating).
     *
     * @var string
     * @access public
     */
    public $class;

    /**
     * The identifier for the injection (name of property,
     * name of method like __construct or something like this).
     *
     * @var string
     * @access public
     */
    public $identifier = self::DEFAULT_IDENTIFIER;

    /**
     * The instance for an injection.
     *
     * @var object
     * @access public
     */
    public $instance;

    /**
     * The type of an injection.
     *
     * @var string
     * @access public
     */
    public $type = self::DEFAULT_TYPE;

    /**
     * The value for an injection.
     *
     * @var mixed
     * @access public
     */
    public $value;

    /**
     * The position for an injection.
     *
     * @var int
     * @access public
     */
    public $position = self::DEFAULT_POSITION;

    /**
     * The default position for an injection.
     *
     * @var int
     * @access public
     * @const
     */
    const DEFAULT_POSITION = 1;

    /**
     * The default type of an injection.
     *
     * @var string
     * @access public
     * @const
     */
    const DEFAULT_TYPE = 'constructor';

    /**
     * The default identifier of an injection.
     *
     * @var string
     * @access public
     * @const
     */
    const DEFAULT_IDENTIFIER = '__construct';
}
