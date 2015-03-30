<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Response
 *
 * Response.php - Response state container.
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
 * @package    DoozR_Response
 * @subpackage DoozR_Response
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Registry.php';
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/State/Container.php';
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/State/Interface.php';

/**
 * DoozR - Response
 *
 * Response state container.
 *
 * @category   DoozR
 * @package    DoozR_Response
 * @subpackage DoozR_Response
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Response extends DoozR_Base_State_Container
{
    /**
     * The type native for PHP request sources
     *
     * @var int
     * @access const
     */
    const NATIVE = 0;

    /**
     * The type emulated for PHP request sources
     *
     * @var int
     * @access const
     */
    const EMULATED = 1;

    protected $header;

    protected $buffer;



    /**
     * Constructor.
     *
     * Custom constructor which is required to set app.
     * And then it calls the parent constructor which does the bootstrapping.
     *
     * @param DoozR_Registry             $registry    The registry containing all important instances
     * @param DoozR_Base_State_Interface $stateObject The state object instance to use for saving state (DI)
     * @param string                     $sapi        The SAPI runtimeEnvironment of active PHP Instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Response
     * @access public
     */
    public function __construct(
        DoozR_Registry             $registry,
        DoozR_Base_State_Interface $stateObject,
                                   $sapi         = PHP_SAPI
    ) {
        $this->setRegistry($registry);

        parent::__construct($stateObject);
    }
}
