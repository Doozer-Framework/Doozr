<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Route
 *
 * Route.php - Extends Apache/IIS/... mod_rewrite.
 * This script takes the argument(s) passed to by mod_rewrite (.htaccess) and
 * parse the Object, Action ... out of it. For this it makes use of the
 * configuration, which contains the excludes, the pattern, translation and the
 * regexp. Afterwards it checks for configured pattern (MVP, MVC, or none) and
 * dispatches the call accordingly.
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
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Router
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

// include + execute bootstrapper of DoozR
require_once 'DoozR/Bootstrap.php';

// get an instance of DoozR (Core-Class)
$DoozR = DoozR_Core::getInstance();

// get registry and some required objects
$registry = DoozR_Registry::getInstance();
$front    = $registry->front;
$config   = $registry->config;

// retrieve current runningmode (can be either WEB or CLI (but lowercase)
$runningMode = $front->getRunningMode();

// check for running-mode and retrieve request-uri
if ($runningMode == DoozR_Controller_Front::RUNNING_MODE_WEB
    || $runningMode == DoozR_Controller_Front::RUNNING_MODE_CLI
) {

    // now begin generic parsing of pattern
    // get exclude(s) and build exclude-regexp of it/them
    $exclude = regexp($config->router->exclude(), 'exclude');

    // get pattern and inject exclude in pattern
    $pattern = str_replace('[[EXCLUDE]]', $exclude, $config->router->pattern->pattern());

    // get object(s) + action(s) from request
    $result = preg_match_all($pattern, $_SERVER['REQUEST_URI'], $request);

    // get pattern translation as array from config
    $translation = explode('/', $config->router->pattern->translation());

    // get count of parts used in current pattern
    // outside the following loop -> performance
    $countParts = count($translation);

    // parse the parts
    for ($i = 0; $i < $countParts; ++$i) {
        //
        if (isset($request[1][$i])) {
            $$translation[$i] = ucfirst(strtolower($request[1][$i]));
        } else {
            $$translation[$i] = 'Default';
        }

        // a list for dispatch (name => value - pairs)
        $requestVariables[$translation[$i]] = $$translation[$i];
    }

} else {

    // UNKNOWN and/or currently not supported!
    $msg  = 'DoozR - The PHP-Framework - Git-Version: $'.DoozR_Core::getVersion(true).' (on '.php_uname().') - ';
    $msg .= 'Running a DoozR-based application in "'.mb_strtoupper($runningMode).'"-mode is not supported!';

    // show message
    pred($msg);
}


// Construct new route based on requested URL
$newRoute = implode('_', $requestVariables);


// Dispatch the new route to logger-subsystem (e.g. for use as filename in file-logger)
$registry->logger->route($newRoute);


// Check for usage of DoozR's MVC-/MVP-structure
if ($config->base->pattern->enabled()) {

    // ENABLED -> dispatch request (MVP/MVC) to DoozR's Back_Controller
    $registry->back->dispatch($requestVariables, $translation, $config->base->pattern->type());

} else {

    // DISABLED -> dispatch request right here:
    // get file of application
    $applicationFile = DOOZR_APP_ROOT.implode(DIRECTORY_SEPARATOR, $requestVariables).'.php';

    // get filesystem module
    $filesystem = DoozR_Loader_Moduleloader::load('filesystem');

    // validate the existence of the application file and ...
    if (!$filesystem->exists($applicationFile)) {
        // ... send 404 Header if not exists
        $front->getResponse()->sendHTTPStatus(404);
        exit;
    }

    // ... otherwise get application (file)
    include_once $applicationFile;
}

?>
