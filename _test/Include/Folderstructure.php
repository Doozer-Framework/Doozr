<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Unit-Test - Bootstrapper
 *
 * Bootstrapper.php - The bootstrapper for Unit-Testing DoozR
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
 * @package    DoozR_Test
 * @subpackage DoozR_Test_Bootstrapper
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

/**
 * define sep as shortcut
 */
$s = DIRECTORY_SEPARATOR;

/**
 * the path to private data of DoozR
 */
define('DOOZR_TEST_FRAMEWORK_PRIVATE_DATA', DOOZR_UT_DOCUMENT_ROOT.'Framework'.$s.'Data'.$s.'Private'.$s.'');

/**
 * path to folder Auth
 */
define('DOOZR_TEST_FRAMEWORK_PRIVATE_DATA_AUTH', DOOZR_TEST_FRAMEWORK_PRIVATE_DATA.'Auth'.$s.'');

/**
 * path to folder Cache
 */
define('DOOZR_TEST_FRAMEWORK_PRIVATE_DATA_CACHE', DOOZR_TEST_FRAMEWORK_PRIVATE_DATA.'Cache'.$s.'');

/**
 * path to folder Cache/Clientdetect
 */
define('DOOZR_TEST_FRAMEWORK_PRIVATE_DATA_CACHE_CLIENTDETECT', DOOZR_TEST_FRAMEWORK_PRIVATE_DATA.'Cache'.$s.'Clientdetect'.$s.'');

/**
 * path to folder Cache/Default
 */
define('DOOZR_TEST_FRAMEWORK_PRIVATE_DATA_CACHE_DEFAULT', DOOZR_TEST_FRAMEWORK_PRIVATE_DATA.'Cache'.$s.'Default'.$s.'');

/**
 * path to folder Cache/Ids
 */
define('DOOZR_TEST_FRAMEWORK_PRIVATE_DATA_CACHE_IDS', DOOZR_TEST_FRAMEWORK_PRIVATE_DATA.'Cache'.$s.'Ids'.$s.'');

/**
 * path to folder Cache/Localization
 */
define('DOOZR_TEST_FRAMEWORK_PRIVATE_DATA_CACHE_LOCALIZATION', DOOZR_TEST_FRAMEWORK_PRIVATE_DATA.'Cache'.$s.'Localization'.$s.'');

/**
 * path to folder Cache/Smarty
 */
define('DOOZR_TEST_FRAMEWORK_PRIVATE_DATA_CACHE_SMARTY', DOOZR_TEST_FRAMEWORK_PRIVATE_DATA.'Cache'.$s.'Smarty'.$s.'');

/**
 * path to folder Config
 */
define('DOOZR_TEST_FRAMEWORK_PRIVATE_DATA_CONFIG', DOOZR_TEST_FRAMEWORK_PRIVATE_DATA.'Config'.$s.'');

/**
 * path to folder Config/Ids
 */
define('DOOZR_TEST_FRAMEWORK_PRIVATE_DATA_CONFIG_IDS', DOOZR_TEST_FRAMEWORK_PRIVATE_DATA.'Config'.$s.'Ids'.$s.'');

/**
 * path to folder Config/Localization
 */
define('DOOZR_TEST_FRAMEWORK_PRIVATE_DATA_CONFIG_LOCALIZATION', DOOZR_TEST_FRAMEWORK_PRIVATE_DATA.'Config'.$s.'Localization'.$s.'');

/**
 * path to folder Config/Smarty
 */
define('DOOZR_TEST_FRAMEWORK_PRIVATE_DATA_CONFIG_SMARTY', DOOZR_TEST_FRAMEWORK_PRIVATE_DATA.'Config'.$s.'Smarty'.$s.'');

/**
 * path to folder Font
 */
define('DOOZR_TEST_FRAMEWORK_PRIVATE_DATA_FONT', DOOZR_TEST_FRAMEWORK_PRIVATE_DATA.'Font'.$s.'');

/**
 * path to folder Font/Ttf
 */
define('DOOZR_TEST_FRAMEWORK_PRIVATE_DATA_FONT_TTF', DOOZR_TEST_FRAMEWORK_PRIVATE_DATA.'Font'.$s.'Ttf');

/**
 * path to folder Log
 */
define('DOOZR_TEST_FRAMEWORK_PRIVATE_DATA_LOG', DOOZR_TEST_FRAMEWORK_PRIVATE_DATA.'Log');

/**
 * path to folder Log
 */
define('DOOZR_TEST_FRAMEWORK_PRIVATE_DATA_TEMP', DOOZR_TEST_FRAMEWORK_PRIVATE_DATA.'Temp');

?>
