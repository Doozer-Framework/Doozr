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
 */

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
 * putenv('DOOZR_APP_ROOT=/path/to/app');
 *
 * In the default install you won't need this statements above!
 */

// Application in production environment
define('DOOZR_APP_ENVIRONMENT', 'production');
//define('DOOZR_DEBUGGING', false);
//define('DOOZR_LOGGING', false);

/**
 * Get composer and bootstrap Doozr ...
 */
require_once realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '../vendor/autoload.php');
require_once 'Doozr/Bootstrap.php';







/**
 * 8< ----------------------------------------
 */

// Initialize Doozr App Kernel
$app = Doozr_Kernel_App::boot(DOOZR_APP_ENVIRONMENT, DOOZR_RUNTIME_ENVIRONMENT, false);

// Handle the default detected request (Web or Cli)
$response = $app->handle();

// Send response to client
$response->send();

die;

/**
 * ---------------------------------------- >8
 */









/**
 * Prototype implementation of relay.relay here ...
 * Just test out for this pattern in this very 1st place.
 * Possible to add any compatible framework stack!
 */
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Relay\Relay;

// Create request & response pair ...
$request  = new \Doozr_Request_Web(
    new \Doozr_Request_State()
);

$response = new \Doozr_Response_Web(
    new \Doozr_Response_State()
);

// Build queue for middlewares
$queue[] = function (Request $request, Response $response, callable $next) {

    /* @var $app Doozr_Kernel_App Get kernel instance */
    $app = Doozr_Kernel_App::boot(DOOZR_APP_ENVIRONMENT, DOOZR_RUNTIME_ENVIRONMENT, false);

    // Handle the default detected request (Web or Cli)
    $response = $app->handle($request, $response);


};

// Get relay & execute ...
$dispatcher = new Relay($queue);
$dispatcher($request, $response);

die;



/**
 * In theory the request is/was handled. thats it. now we can achieve that other middleware can
 * intercept our result and modify it further. We're also able to implement this symfony like
 * request/response caching before the kernel is called by implementing a lightweight middleware
 * for caching through varnish for example
 *
 *   ||
 *   REQUEST
 *     ||
 *     RELAY
 *       ||
 *       CACHED? => YES = VARNISH = RESPONSE
 *       ||
 *       NO? => PROCESS => CACHE => SEND RESPONSE
 *
 * and so on :)
 */

/**
 * If you want to call normal files within this directory feel free to :)
 */
