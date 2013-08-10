<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Path Interface
 *
 * Interface.php - Path-Interface-Class of the DoozR-Framework
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
 * @subpackage DoozR_Core_Path
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

/**
 * DoozR Path Interface
 *
 * Path-Interface-Class of the DoozR-Framework
 *
 * @category   DoozR
 * @package    DoozR_Core
 * @subpackage DoozR_Core_Path
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
interface DoozR_Path_Interface
{
    /**
     * signature for addIncludePath()
     *
     * @param string $path The path which should be added as include-path
     *
     * @return  void
     * @access  public
     * @static
     */
    public static function addIncludePath($path);

    /**
     * removes a path from php's include searchpaths
     *
     * @param string $path The path which should be removed from include-paths
     *
     * @return  void
     * @access  public
     * @static
     */
    public static function removeIncludePath($path);

    /**
     * signature for get()
     *
     * @param string  $identifier    The path which should be returned
     * @param string  $add           An extension to the path requested
     * @param boolean $trailingSlash True to add a trailing slash (false = default)
     *
     * @return  string The path requested
     * @access  private
     * @static
     */
    public function get($identifier, $add = '', $trailingSlash = false);

    /**
     * signature for set()
     *
     * @param string  $identifier The name of the path which should be set
     * @param string  $path       The path which should be set
     * @param boolean $force      True to force overwrite of already existing identifier
     *
     * @return  boolean True if successful otherwise false
     * @access  private
     * @static
     */
    public function set($identifier, $path, $force = false);

    /**
     * converts a given module name to a path
     *
     * This method is intend to convert a given module name to a path.
     *
     * @param string $serviceName The name of the module to retrieve the path for
     * @param string $namespace  The namespace to use for building path to module
     *
     * @return  string The path requested
     * @access  public
     * @static
     */
    public static function moduleToPath($serviceName, $namespace = 'DoozR');

    /**
     * returns slash-corrected path
     *
     * corrects slashes with "wrong" direction in a path and returns it
     *
     * @param string $path The path to correct slashes in
     *
     * @return  string The path with corrected slash-direction
     * @access  public
     * @static
     */
    public static function correctPath($path);

    /**
     * merges two path's
     *
     * This method is intend to merge two path-settings under consideration
     * of ../ (n-times)
     *
     * @param string $pathBase  The path used as base
     * @param string $pathMerge The path used as extension to $pathBase
     *
     * @return  string The corrected new merged path
     * @access  public
     * @static
     */
    public function mergePath($pathBase, $pathMerge = '');
}
