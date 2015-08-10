<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - The PHP-Framework
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
 */

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
if (
    isset($_SERVER['HTTP_CLIENT_IP']) ||
    isset($_SERVER['HTTP_X_FORWARDED_FOR']) ||
    (
        !preg_match("/^192/", @$_SERVER['REMOTE_ADDR']) &&
        !in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1'))
    )
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}


/**
 * ENVIRONMENT:
 * You can override the default environment by a defined constant:
 * define('DOOZR_APP_ENVIRONMENT', 'development|testing|staging|production');
 *
 * or by an environment variable which can be set via apache config
 * for example on a per vhost base or like this with PHP:
 * putenv('DOOZR_APP_ENVIRONMENT', 'development|testing|staging|production');
 *
 * PATH TO APP:
 * You can override the default app path by a defined constant:
 * define('DOOZR_APP_ROOT', '/path/to/app');
 *
 * or by an environment variable which can be set via apache config
 * for example on a per vhost base or like this with PHP:
 * putenv('DOOZR_APP_ROOT = /path/to/app');
 *
 * In the default install you won't need this statements above!
 */

// App in development environment
define('DOOZR_APP_ENVIRONMENT', 'development');
//define('DOOZR_DEBUGGING', true);
//define('DOOZR_LOGGING', true);
//define('DOOZR_PROFILING', true);

/**
 * Get composer and bootstrap Doozr ...
 */
require_once realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '../vendor/autoload.php');

/**
 * Check for internal webserver request for real file ...
 */
if (
    PHP_SAPI === 'cli-server' &&
    file_exists(realpath($_SERVER['DOCUMENT_ROOT'] . parse_url($_SERVER['REQUEST_URI'])['path']))
) {
    return false;
}

// Override defaults
$_SERVER['QUERY_STRING'] = (
    !isset($_SERVER['QUERY_STRING']) ||
    $_SERVER['QUERY_STRING'] === '/' ||
    $_SERVER['QUERY_STRING'] === ''
) ?
    '/' :
    $_SERVER['QUERY_STRING'];

// Try to load .env file with environmental settings
try {
    $dotenv = new Dotenv\Dotenv(realpath(__DIR__ . '/..'));
    $dotenv->load();

} catch (InvalidArgumentException $exception) {}

// Bootstrap
require_once 'Doozr/Bootstrap.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Relay\Runner;

// Build queue for running middleware through relay
$queue[] = function(Request $request, Response $response, callable $next) {

    $app = Doozr_Kernel_App::boot(
        DOOZR_APP_ENVIRONMENT,
        DOOZR_RUNTIME_ENVIRONMENT,
        DOOZR_DEBUGGING,
        DOOZR_CACHING,
        DOOZR_LOGGING,
        DOOZR_DOCUMENT_ROOT,
        DOOZR_APP_ROOT
    );

    return $app->handle($request, $response, !DOOZR_DEBUGGING);
};

// Create a Relay Runner instance ...
$runner = new Runner($queue);

// ... and run it with the queue defined above
$response = $runner(
    new \Doozr_Request_Web(
        new \Doozr_Request_State()
    ),
    new \Doozr_Response_Web(
        new \Doozr_Response_State()
    )
);

// After running the whole queue send the response (HTTP way)
$responseSender = new Doozr_Response_Sender_Web($response);
$responseSender->send();

/**
 * If you want to call normal files within this directory feel free to :)
 */
