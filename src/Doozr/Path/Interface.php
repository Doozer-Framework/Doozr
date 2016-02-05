<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr Path Interface.
 *
 * Interface.php - Path-Interface-Class of the Doozr-Framework
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

/**
 * Doozr Path Interface.
 *
 * Path-Interface-Class of the Doozr-Framework
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
interface Doozr_Path_Interface
{
    /**
     * Add a path to PHP's include paths.
     *
     * @param string $path The path which should be added to include path's
     *
     * @static
     */
    public static function addIncludePath($path);

    /**
     * Removes a path from PHP's include paths.
     *
     * @param string $path The path which should be removed from include path's
     *
     * @static
     */
    public static function removeIncludePath($path);

    /**
     * Returns path by identifier (e.g. 'temp').
     *
     * @param string $identifier    Path which should be returned
     * @param string $add           Extension to the path requested
     * @param bool   $trailingSlash TRUE to add a trailing slash, FALSE to do not (default)
     *
     * @return string The path requested
     * @static
     */
    public function get($identifier, $add = '', $trailingSlash = false);

    /**
     * Register an path from external source (e.g. created at runtime).
     *
     * @param string $identifier Name of the path which should be set
     * @param string $path       Path which should be set
     * @param bool   $force      TRUE to force overwrite of already existing identifier, FALSE to prevent from overwrite
     *
     * @return bool TRUE on success, otherwise FALSE
     * @static
     */
    public function set($identifier, $path, $force = false);

    /**
     * Converts a given module name to a path.
     *
     * @param string $serviceName Name of the module to retrieve the path for
     * @param string $scope       Namespace to use for building path to module
     *
     * @return string The path requested
     * @static
     */
    public static function serviceToPath($serviceName, $scope = DOOZR_NAMESPACE);

    /**
     * Returns path with slashes matching current OS standard.
     *
     * corrects slashes with "wrong" direction in a path and returns it
     *
     * @param string $path The path to correct slashes in
     *
     * @return string The path with corrected slash-direction
     * @static
     */
    public static function fixPath($path);

    /**
     * Merges two paths under consideration of "../" (directory traversal n-times).
     *
     * @param string $pathBase  Path used as base
     * @param string $pathMerge Path used as extension to $pathBase
     *
     * @return string The corrected new merged path
     * @static
     */
    public function mergePath($pathBase, $pathMerge = '');
}
