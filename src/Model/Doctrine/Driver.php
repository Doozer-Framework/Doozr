<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Model\Doctrine;

/**
 * Doozr - Model - Doctrine - Driver.
 *
 * Driver.php - Driver for "Doctrine" OxM. Provides access to OxM and does some init/bootstrapping stuff.
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
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Model/Driver.php';

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

/**
 * Doozr - Model - Doctrine - Driver.
 *
 * Driver for "Doctrine" OxM. Provides access to OxM and does some init/bootstrapping stuff.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
class Driver extends \Doozr_Base_Model_Driver
{
    /**
     * Collection of EntityManager instances.
     *
     * @var EntityManager[]
     * @static
     */
    public static $connection = [];

    /**
     * The install routine of the driver. All available information for the driver is passed as configuration.
     *
     * @param array $driverConfiguration Configuration of model
     *
     * @throws \Doctrine\ORM\ORMException
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return EntityManager[] An collection of EntityManagers indexed by connection Id
     * @static
     */
    public static function install(array $driverConfiguration)
    {
        // Check if input is valid
        if (false === self::validate($driverConfiguration, ['classes'])) {
            throw new \Doozr_Exception(
                sprintf('Configuration invalid for driver "%s".', __CLASS__)
            );
        }

        $paths             = [$driverConfiguration['entities']];
        $isDevelopmentMode = (\Doozr_Kernel::APP_ENVIRONMENT_DEVELOPMENT === DOOZR_APP_ENVIRONMENT);

        // Connection configuration
        $configuration = Setup::createAnnotationMetadataConfiguration($paths, $isDevelopmentMode);

        foreach ($driverConfiguration['connections'] as $id => $connectionData) {
            self::$connection[$id] = EntityManager::create($connectionData, $configuration);
        }

        // Return the whole connection(s) array. Can be accessed in Model via getRegistry()->getModel()->getHandle()
        return self::$connection;
    }

    /**
     * Returns a connection by its Id or the whole collection if not Id passed.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return EntityManager[]
     */
    public function getConnection($id = null)
    {
        if (null === $id) {
            $result = self::$connection;

        } else {
            if (false === isset(self::$connection[$id])) {
                throw new \Doozr_Exception(
                    sprintf(
                        'EntityManager connection with Id "%s" does not exist!',
                        $id
                    )
                );
            }

            $result = self::$connection[$id];
        }

        return $result;
    }
}
