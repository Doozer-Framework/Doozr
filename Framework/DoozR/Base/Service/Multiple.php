<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Base Service Multiple
 *
 * Multiple.php - Base-Service for building multi-instance services
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
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Service
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Class.php';

/**
 * DoozR Base Service Multiple
 *
 * Base-Service for building multi-instance services
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Service
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Base_Service_Multiple extends DoozR_Base_Class
{
    /**
     * Contains the instance of DoozR_Registry
     *
     * @var object
     * @access protected
     * @static
     */
    protected $registry;

    /**
     * Autoloader auto install control flag
     * If set to TRUE in inheritent class the autoloader
     * will be installed automatically.
     *
     * @var boolean
     * @access protected
     */
    protected $autoloader = false;

    /**
     * The name of this service
     *
     * @var string
     * @access protected
     */
    protected $name;


    /**
     * Constructor
     *
     * This method acts as constructor.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __construct()
    {
        // filter out registry and store accessible through static $registry!
        $arguments = func_get_args();
        $this->registry = &$arguments[0];
        $arguments = array_slice($arguments, 1);

        // dispatch remaining stuff
        if ($this->hasMethod('__tearup')) {
            if ((func_num_args() - 1) > 0) {
                call_user_func_array(
                    array(
                        $this,
                        '__tearup'
                    ),
                    $arguments
                );
            } else {
                call_user_func(
                    array($this, '__tearup')
                );
            }
        }

        // e.g. log loading of module
        return parent::__construct();
    }

    /**
     * Destructor
     *
     * This method acts as destructor.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __destruct()
    {
        if ($this->hasMethod('__teardown')) {
            $this->__teardown();
        }
    }
}
