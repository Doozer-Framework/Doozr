<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Rest - Service
 *
 * Rest.php - Contains some nice REST helper methods and prepares the request
 * in a way which makes it easier processable in the further process.
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
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Rest
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Service/Multiple.php';
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Request/Api.php';
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Service/Interface.php';

use DoozR\Loader\Serviceloader\Annotation\Inject;

/**
 * DoozR - Rest - Service
 *
 * Contains some nice REST helper methods and prepares the request
 * in a way which makes it easier processable in the further process.
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Rest
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @throws     DoozR_Rest_Service_Exception
 * @Inject(
 *     class="DoozR_Registry",
 *     identifier="__construct",
 *     type="constructor",
 *     position=1
 * )
 */
class DoozR_Rest_Service extends DoozR_Base_Service_Multiple implements DoozR_Base_Service_Interface
{
    /**
     * Constructor.
     *
     * @param array   $route
     * @param int $countRootNodes The count of root nodes (e.g. 2 on /Foo/Bar/Demo/Screen/ means
     *                                that /Foo/Bar/ will be taken as root and ripped out)
     *
     * @internal param array $request The original request
     * @author   Benjamin Carl <opensource@clickalicious.de>
     * @return   void
     * @access   public
     */
    public function __tearup(DoozR_Base_State_Interface $requestState, array $route = array(), $countRootNodes = 2)
    {
        // If no custom request data/config is passed ...
        if (empty($route)) {
            // ... use defeault
            $route = array(
                'port'   => $_SERVER['SERVER_PORT'],
                'ip'     => gethostbyname($_SERVER['SERVER_NAME']),
                'domain' => $_SERVER['SERVER_NAME'],
                'ssl'    => is_ssl()
            );
        }

        /* @var $requestState DoozR_Request_State */
        $this->setStateObject($requestState);
    }
}
