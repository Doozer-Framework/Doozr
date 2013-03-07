<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Doodi - CouchDB - Container - Bootstrapper
 *
 * DoodiCouchdbContainer.bootstrapper.php - This is the bootstrap file, which sets up an autoload
 * mechanism using spl-Autoload-register, which makes all PHPillow classes available in your scripts.
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
 * @package    DoozR_Model
 * @subpackage DoozR_Model_Doodi
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

/**
 * autoloader for Doodi-Container classes
 *
* autoloader for Doodi-Container classes
 *
 * @param string $class The class to autoload
 *
 * @return  boolean TRUE on success
 * @access  public
 * @author  Benjamin Carl <opensource@clickalicious.de>
 * @since   Method available since Release 1.0.0
 * @version 1.0
 */
function Doodi_Couchdb_Container_autoload($class)
{
    // holds the classes map static in memory
    static $classes;

    // get statically (prebuild map of file <-> location)
    if ($classes === null) {
        $classes = include dirname(__FILE__).'/Autoload/DoodiCouchdbAutoload.php';
    }

    // check if classname can be found in map or if it must be constructed from classname
    if (!isset($classes[$class])) {
        // try here to load file by name
        $folder = dirname(__FILE__).DIRECTORY_SEPARATOR.
                  str_replace('_', DIRECTORY_SEPARATOR, str_replace('Doodi_Couchdb_', '', $class)).DIRECTORY_SEPARATOR;
        $file = $folder.str_replace('_', '', $class).'.class.php';

        if (!file_exists($file)) {
            // return status => failed
            return false;
        }
    } else {
        // get filename from prebuild array (the fastest way!)
        $file = dirname(__FILE__).'/'.$classes[$class];
    }

    // now include the file
    include $file;

    // return status => success
    return true;
}

/**
 * register the autoloader
 */
spl_autoload_register('Doodi_Couchdb_Container_autoload');

?>
