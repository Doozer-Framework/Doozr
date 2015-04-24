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

/**
 * Get composer as well as Doozr's router the rest is magic ...
 */
require_once realpath(dirname(__FILE__).DIRECTORY_SEPARATOR . '../vendor/autoload.php');
require_once 'Route.php';

/**
 * If you want to call normal files within this directory feel free to :)
 */
