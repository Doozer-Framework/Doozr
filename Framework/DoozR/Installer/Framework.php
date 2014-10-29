<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Installer - Framework
 *
 * Framework.php - Installer script for installing web + app folder to document root
 * after using composer to install DoozR. It's a convenient way to get DoozR
 * running.
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2014, Benjamin Carl - All rights reserved.
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
 * @package    DoozR_Installer
 * @subpackage DoozR_Installer_Framework
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

use Composer\Script\CommandEvent;

/**
 * DoozR - Installer - Framework
 *
 * Installer script for installing web + app folder to document root
 * after using composer to install DoozR. It's a convenient way to get DoozR
 * running.
 *
 * @category   DoozR
 * @package    DoozR_Handler
 * @subpackage DoozR_Handler_Error
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @final
 */
final class DoozR_Installer_Framework extends DoozR_Installer_Base
{
    /**
     * Post install event hook on composer.
     *
     * @param CommandEvent $event
     *
     * @return bool
     */
    public static function postInstall(CommandEvent $event)
    {
        // Detect path to composer.json
        $installPath = self::getInstallPath();
        $io          = $event->getIO();

        // Questions for tree.
        $question1 = 'Install DoozR\'s bootstrap project now? (YES/no) ';
        $question2 = 'Install to "' . $installPath . '"? (YES/no) ';
        $question3 = 'Please enter path to install to: ';
        $question4 = 'Please enter path to install to: ';

        // Ask for install?
        if ($io->askConfirmation($question1, true)) {

            // Ask for path confirm?
            if ($io->askConfirmation($question2, true)) {
                try {
                    $installPath = self::validatePath($installPath);
                } catch (Exception $e) {

                    if ($installPath = $io->askAndValidate($e->getMessage() . PHP_EOL . $question3, array('DoozR_Installer_Framework', 'validatePath'), 5, $installPath)) {
                        self::install($installPath);
                    }
                }

            } else {
                if ($installPath = $io->askAndValidate($question4, array('DoozR_Installer_Framework', 'validatePath'), 5)) {
                    self::install($installPath);
                }
            }

            // ok, continue on to composer install
            return true;
        }

        // exit composer and terminate installation process
        exit;
    }


    public static function install($targetDirectory)
    {
        // The folders to copy
        $folders = array(
            'app',
            'web',
        );

        // Define source & destination
        $source      = self::getSourcePath();
        $destination = $targetDirectory;

        // Iterate and copy ...
        foreach ($folders as $folder) {
            self::xcopy($source . $folder, $destination . $folder);
        }

        return true;
    }

    /**
     * Validates a path for installation.
     *
     * @param string $path The path to validate
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if path is valid, otherwise FALSE
     * @throws Exception
     */
    public static function validatePath($path)
    {
        if (realpath($path) === false) {
            throw new Exception(
                'Path "' . $path . '" does not exist.'
            );
        }

        if (is_dir($path) === false || is_writable($path) === false) {
            throw new Exception(
                'Make sure path "' . $path . '" exists and that it\'s writable.'
            );
        }

        // Make full usable with trailing slash
        $path = realpath($path) . DIRECTORY_SEPARATOR;

        $folder = array();

        if (file_exists($path . 'app')) {
            $folder[] = 'app';
        }

        if (file_exists($path . 'web')) {
            $folder[] = 'web';
        }

        if (count($folder) > 0) {
            throw new Exception(
                'The target directory contains the following files/folders already: ' . implode(' & ', $folder) . '.' .
                PHP_EOL . 'Remove those files/folders first and try again.' . PHP_EOL
            );
        }

        return $path;
    }

    /**
     * Detect and return source path containing the bootstrap project structure.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool Returns true on success, false on failure
     * @access protected
     */
    protected static function getSourcePath()
    {
        $path = DIRECTORY_SEPARATOR . implode(
            DIRECTORY_SEPARATOR,
            array('Framework', 'DoozR','Installer', 'Framework.php')
        );

        return realpath(str_replace($path, '', __FILE__)) . DIRECTORY_SEPARATOR;
    }

    /**
     * Copy a file, or recursively copy a folder and its contents
     *
     * @param string $source      Source path
     * @param string $destination Destination path
     * @param mixed  $permissions New folder creation permissions
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool Returns true on success, false on failure
     * @access protected
     */
    protected static function xcopy($source, $destination, $permissions = 0755)
    {
        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $destination);
        }

        // Simple copy for a file
        if (is_file($source)) {
            return copy($source, $destination);
        }

        // Make destination directory
        if (!is_dir($destination)) {
            mkdir($destination, $permissions);
        }

        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Deep copy directories
            self::xcopy(
                $source . DIRECTORY_SEPARATOR . $entry,
                $destination . DIRECTORY_SEPARATOR . $entry
            );
        }

        // Clean up
        $dir->close();
        return true;
    }

    /**
     * Returns install path relative to current path.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The install path
     * @access protected
     */
    protected static function getInstallPath()
    {
        $path = DIRECTORY_SEPARATOR . implode(
            DIRECTORY_SEPARATOR,
            array('vendor', 'clickalicious', 'doozr', 'Framework', 'DoozR','Installer', 'Framework.php')
        );

        return realpath(str_replace($path, '', __FILE__)) . DIRECTORY_SEPARATOR;
    }
}
