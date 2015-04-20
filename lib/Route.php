<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Route
 *
 * Route.php - Dispatches to DoozR's Routing.
 *
 *
 * PHP versions
 *
 * LICENSE:
 * DoozR - The lightweight PHP-Framework for high-performance websites
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
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Router
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once 'DoozR/Bootstrap.php';
require_once 'DoozR/Route.php';

// Run the DoozR core to prepare base and extend PHP
DoozR_Core::run();

// Get registry and configuration as well
/* @var $registry DoozR_Registry */
$registry = DoozR_Registry::getInstance();
$config   = $registry->getConfig();

// Iterate filter and prepare URL
foreach ($config->request->filter as $filter) {
    $registry->getRequest()->setUrl(
        preg_replace($filter->search, $filter->replace, $registry->getRequest()->getUrl())
    );
}

// Inject route from config to request state
$registry->getRequest()->setRouteConfig($config->redirect);

// Combine supported runtime environments
$supportedEnvironments = array(
    DoozR_Request_State::RUNTIME_ENVIRONMENT_WEB,
    DoozR_Request_State::RUNTIME_ENVIRONMENT_CLI,
    DoozR_Request_State::RUNTIME_ENVIRONMENT_HTTPD,
);

// Check for supported runtimeEnvironment
if (in_array($registry->getRequest()->getRuntimeEnvironment(), $supportedEnvironments) === true) {

    if ($config->cache->enabled === true) {
        /* @var DoozR_Cache_Service $cacheService */
        $cacheService = $registry->getCache();

    } else {
        $cacheService = null;
    }

    // Run route init
    return DoozR_Route::init(
        $registry,
        $registry->getRequest(),
        $cacheService,
        $config->cache->enabled,
        $config->base->pattern->autorun
    );

} else {

    // UNKNOWN and/or currently not supported!
    $msg  = 'DoozR - The lightweight PHP-Framework for high-performance websites  - Git-Version: ' . DOOZR_VERSION . ' (on ' . php_uname() . ') - ';
    $msg .= 'Running a DoozR-based application in "' . strtoupper($registry->getRequest()->getRuntimeEnvironment()) .
            '"-runtimeEnvironment is not supported!';

    // show message
    pred($msg);
}
