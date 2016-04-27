<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - The PHP-Framework.
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
 */

/**
 * ENVIRONMENT:
 * You can override the default environment by a defined constant:
 * define('DOOZR_APP_ENVIRONMENT', 'development|testing|staging|production');.
 *
 * or by an environment variable which can be set via apache configuration
 * for example on a per vhost base or like this with PHP:
 * putenv('DOOZR_APP_ENVIRONMENT', 'development|testing|staging|production');
 *
 * PATH TO APP:
 * You can override the default app path by a defined constant:
 * define('DOOZR_APP_ROOT', '/path/to/app');
 *
 * or by an environment variable which can be set via apache configuration
 * for example on a per vhost base or like this with PHP:
 * putenv('DOOZR_APP_ROOT=/path/to/app');
 *
 * In the default install you won't need this statements above!
 */

// Application in production environment
define('DOOZR_APP_ENVIRONMENT', 'production');
//define('DOOZR_APP_NAMESPACE', 'App');
//define('DOOZR_DEBUGGING', false);
//define('DOOZR_LOGGING', false);
//define('DOOZR_PROFILING', false);

// Get composer and bootstrap Doozr ...
require_once realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../vendor/autoload.php');

// Try to load .env file with environmental settings
try {
    $dotenv = new Dotenv\Dotenv(realpath(__DIR__.'/..'));
    $dotenv->load();
} catch (InvalidArgumentException $exception) {
}

// Bootstrap
require_once 'Doozr/Bootstrap.php';

use Clickalicious\CachingMiddleware;
use Cocur\Slugify\Slugify;
use Gpupo\Cache\CacheItem;
use Gpupo\Cache\CacheItemPool;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Relay\Runner;

/*
 * Put CachingMiddleware on stack when caching is enabled
 */
if (true === DOOZR_CACHING) {

    /*
     * Fill queue for running "CachingMiddleware"
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request  Request (PSR) to process
     * @param \Psr\Http\Message\ResponseInterface      $response Response (PSR) to use
     * @param callable                                 $next     Next middleware in stack
     *
     * @return \Psr\Http\Message\ResponseInterface A PSR compatible response
     */
    $queue[] = function(Request $request, Response $response, callable $next) {

        // Create cache item factory
        $cacheItemFactory = function($key) {
            return new CacheItem($key);
        };

        // Create cache item key factory
        $cacheItemKeyFactory = function(Request $request) {
            static $key = null;
            if (null === $key) {
                $uri     = $request->getUri();
                $slugify = new Slugify();
                $key     = $slugify->slugify(trim($uri->getPath(), '/').($uri->getQuery() ? '?'.$uri->getQuery() : ''));
            }

            return $key;
        };

        // Get cache
        $cachingMiddleWare = new CachingMiddleware(
            new CacheItemPool('Filesystem'),
            $cacheItemFactory,
            $cacheItemKeyFactory
        );

        return $cachingMiddleWare($request, $response, $next);
    };
}

/*
 * Put Doozr (Middleware) on stack for processing
 */

/*
 * Fill queue for running "Doozr" middleware
 *
 * @param \Psr\Http\Message\ServerRequestInterface $request  Request (PSR) to process
 * @param \Psr\Http\Message\ResponseInterface      $response Response (PSR) to use
 * @param callable                                 $next     Next middleware in stack
 *
 * @return \Psr\Http\Message\ResponseInterface A PSR compatible response
 */
$queue[] = function(Request $request, Response $response, callable $next) {

    // Boot the App kernel
    $app = Doozr_Kernel_App::boot(
        DOOZR_APP_ENVIRONMENT,
        DOOZR_RUNTIME_ENVIRONMENT,
        DOOZR_UNIX,
        DOOZR_DEBUGGING,
        DOOZR_CACHING,
        DOOZR_CACHING_CONTAINER,
        DOOZR_LOGGING,
        DOOZR_PROFILING,
        DOOZR_APP_ROOT,
        DOOZR_APP_NAMESPACE,
        DOOZR_DIRECTORY_TEMP,
        DOOZR_DOCUMENT_ROOT,
        DOOZR_NAMESPACE,
        DOOZR_NAMESPACE_FLAT
    );

    // Invoke Middleware
    return $app($request, $response, $next);
};

/*
 * Execute the configured stack by running it via \Relay\Runner()
 */

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

/*
 * Send the response as Web response (PSR)
 */

// After running the whole queue send the response (HTTP way)
$responseSender = new Doozr_Response_Sender_Web($response);
$responseSender->send();

/*
 * If you want to call normal files within this directory feel free to :)
 */
