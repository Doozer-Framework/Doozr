<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Oauth2 - Service
 *
 * Service.php - Service for OAuth2 server + client support
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2013, Benjamin Carl - All rights reserved.
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
 * @subpackage DoozR_Service_Oauth2
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Service/Multiple/Facade.php';
require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Oauth2/Service/Lib/Oauth2/Autoloader.php';

/**
 * DoozR - Oauth2 - Service
 *
 * Service for interfacing Minify
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Oauth2
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @service    Multiple
 * @inject     DoozR_Registry:DoozR_Registry identifier:__construct type:constructor position:1
 */
class DoozR_Oauth2_Service extends DoozR_Base_Service_Multiple_Facade
{
    /**
     * Mode CLIENT
     *
     * @var integer
     * @access const
     */
    const MODE_CLIENT = 0;

    /**
     * Mode SERVER
     *
     * @var integer
     * @access const
     */
    const MODE_SERVER = 1;

    /**
     * Container
     */
    const CONTAINER_PDO = 'PDO';

    /**
     * Constructor of this class
     *
     * @param integer $mode The mode of this service instance (can be either server or client)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object instance of this class
     * @access public
     */
    public function __tearup($mode = self::MODE_SERVER, $container = self::CONTAINER_PDO, array $config = array())
    {
        if ($mode === self::MODE_SERVER) {
            // register the autoloader
            OAuth2\Autoloader::register();

            // create storage container for persistence
            $storage = new OAuth2\Storage\Pdo($config);

            // create OAuth2 Server instance
            $realObject = new OAuth2\Server($storage);

        } else {
            // here we would create a client instance
        }

        self::setRealObject(
            $realObject
        );
    }
}
