<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

// Check for execution from www or something!
if ('cli' !== php_sapi_name()) {
    echo 'Please execute the Installer from command line.';
    exit;
}

/**
 * Doozr - Installer - Framework
 *
 * Framework.php - Installer script for installing web, app, bin ... folder to document root after using composer
 * to install Doozr. It's a convenient way to get Doozr running.
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
 * @package    Doozr_Installer
 * @subpackage Doozr_Installer_Framework
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

use Composer\Script\CommandEvent;

define('DOOZR_INSTALLER_VERSION', '$Id$');

/**
 * Doozr - Installer - Framework.
 *
 * Installer script for installing web + app folder to document root after using composer to install Doozr.
 * It's a convenient way to get Doozr running.
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
class Doozr_Installer_Framework extends Doozr_Installer_Base
{
    /**
     * Default folders we install/copy.
     *
     * @var array
     * @static
     */
    protected static $folders = [
        'app',
        'web',
    ];

    /**
     * Choice YES [y].
     *
     * @var string
     * @const
     */
    const CHOICE_Y    = 'y';

    /**
     * Choice No [n].
     * 
     * @var string
     * @const
     */
    const CHOICE_N    = 'n';

    /**
     * Choice QUIT [quit].
     *
     * @var string
     * @const
     */
    const CHOICE_QUIT = 'quit';

    /**
     * Installer process for Doozr's bootstrap project based on post install event hook on composer.
     *
     * @param CommandEvent $event The event passed in by Composer.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool|null TRUE on success, otherwise FALSE (signal for Composer to resolve with error)
     * @static
     */
    public static function postInstall(CommandEvent $event)
    {
        // Detect path to composer.json
        self::setInstallPath(
            self::retrieveInstallPath()
        );

        // We must include autoloader - funny.
        require_once self::getInstallPath().DIRECTORY_SEPARATOR.'vendor/autoload.php';

        // Store extra from composer
        self::setExtra(
            $event->getComposer()->getPackage()->getExtra()
        );

        // Force colors
        \cli\Colors::enable();

        // Process event
        return self::handleEvent($event);
    }

    /**
     * Handles a received event - dispatcher.
     *
     * @param CommandEvent $event The event triggered by Composer
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool|null TRUE on success, otherwise FALSE
     */
    protected static function handleEvent(CommandEvent $event)
    {
        $valid = false;

        // Construct menus ...
        $menu1 = [
            'install' => \cli\Colors::colorize('%N%gInstall Doozr\'s Demo project%N'),
            'quit'    => \cli\Colors::colorize('%N%rQuit %N'),
        ];
        $menu2 = \cli\Colors::colorize('%NInstall to %g'.self::getInstallPath().' %N');
        $menu3 = [
            'change' => \cli\Colors::colorize('%N%gChange path to install%N'),
            'quit'   => \cli\Colors::colorize('%N%rQuit%N'),
        ];
        $menu4 = 'Enter path';

        // Show the big old school banner - yeah i like :)
        self::showDoozrBanner();

        // Retrieve and store arguments
        self::initArguments($event);

        // show Version information
        self::showVersion();

        // Begin CLI loop
        while (true) {
            // Ask for OK for install in general ...
            $resolved = self::resolveTree($menu1, 'install');
            if ($resolved === self::CHOICE_QUIT) {
                break;

            } else {

                // Ask if auto detected install path is ok ...
                $resolved = self::resolveChoice($menu2);

                if ($resolved === self::CHOICE_Y) {
                    // Try to validation and use the auto detected path ...
                    try {
                        self::validatePath(self::getInstallPath());
                        $valid = true;
                    } catch (Exception $e) {
                        return self::showError($e->getMessage());
                    }

                    // If operation failed above -> Ask user for alternative path to install
                    if ($valid !== true) {
                        self::showError('Automatic detected path seems to be invalid. Please choose another path!');
                        self::askAlternatePath($menu4);

                    }
                } else {
                    // Check for alternate path ...
                    $resolved = self::resolveTree($menu3, 'change');

                    // If user decided to change the path
                    if ($resolved === 'change') {
                        // If operation failed above -> Ask user for path to install
                        $valid = self::askAlternatePath($menu4);

                    } else {
                        // Quit
                        break;
                    }
                }

                // Check if alternate path also failed in case of exception ...
                if ($valid === true) {
                    if (self::install(self::getInstallPath().DIRECTORY_SEPARATOR) === true) {
                        self::showSuccess();
                        self::showVhostExample(self::getInstallPath());
                        self::showOutro(self::getInstallPath());

                    } else {
                        self::showFailed();
                    }
                }
            }

            // OK, continue on to composer install
            return $valid;
        }

        // Something failed and we ended up here.
        return false;
    }

    /**
     * Ask user for alternate path.
     *
     * @param string $menu The menu to render
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string
     * @throws Exception
     */
    protected static function askAlternatePath($menu)
    {
        $valid = false;

        while (false === $valid) {
            try {
                self::setInstallPath(
                    self::validatePath(
                        \cli\prompt($menu, $default = false, $marker = ': ')
                    )
                );

                return true;

            } catch (Exception $exception) {
                self::showError($exception->getMessage());
            }
        }
    }

    /**
     * Shows the success message after install was successful.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE
     * @static
     */
    protected static function showSuccess()
    {
        \cli\line();
        \cli\line(
            \cli\Colors::colorize('%N%n%gInstallation of %yDoozr\'s%g bootstrap project was successful.%N%n')
        );

        return true;
    }

    /**
     * Shows an error message.
     *
     * @param string $message Message to display
     *
     * @return bool FALSE
     * @static
     */
    protected static function showError($message)
    {
        \cli\line();
        \cli\err(
            \cli\Colors::colorize('%N%n%1'.$message.'%N%n')
        );

        return false;
    }

    /**
     *
     *
     * @param string $menu
     */
    protected static function resolveChoice($menu, $choices = 'yn', $default = self::CHOICE_Y)
    {
        $choice = false;
        while ($choice === false) {
            $choice = \cli\choose($menu, $choices, $default);
        }
        \cli\line();

        return $choice;
    }

    protected static function resolveTree($menu, $default = self::CHOICE_QUIT, $text = 'Your choice:')
    {
        $choice = false;
        while ($choice === false) {
            $choice = \cli\menu($menu, $default, \cli\Colors::colorize($text));
        }
        \cli\line();

        return $choice;
    }

    /**
     * Shows the failed message after install failed.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @static
     */
    protected static function showFailed()
    {
        \cli\line();
        \cli\line(
            \cli\Colors::colorize('%N%n%1Installation of Doozr\'s bootstrap project failed.%N%n')
        );
    }

    /**
     * Shows the outro message after install succeeded to inform about management console.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @static
     */
    protected static function showOutro($projectRoot = 'n.a.')
    {
        \cli\line();
        \cli\line(\cli\Colors::colorize('%nEnjoy developing with %yDoozr%n'));
        \cli\line('To maintain your app you can now run %k%Uphp app/console%n%N from your project');
        \cli\line('root: %k%U'.$projectRoot.'%n%N');
        \cli\line();
        \cli\line('This will offer you options like:');
        \cli\line('  --webserver=start To run Doozr on PHP\'s internal webserver - instantly.');
    }

    /**
     * Installs the folders required for the bootstrap project from repo to project folder.
     *
     * @param string $targetDirectory The directory where to put the files/folders.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     * @static
     */
    protected static function install($targetDirectory)
    {
        $notify = new \cli\notify\Spinner(\cli\Colors::colorize('%N%n%yInstalling bootstrap project ...%N%n'), 100);

        // Define source & destination
        $source      = self::getSourcePath();
        $destination = $targetDirectory;

        // Iterate and copy ...
        foreach (self::getFolders() as $folder) {
            self::xcopy($source.$folder, $destination.$folder);
        }

        $target = realpath($destination.'vendor/maximebf/debugbar/src/DebugBar/Resources');
        $link   = (realpath($destination.'web') !== false) ?
            realpath($destination.'web').DIRECTORY_SEPARATOR.'assets' :
            false;

        if ($target !== false && $link !== false) {
            // Create important symlinks to required assets like for DebugBar
            $symlinked = symlink($target, $link);
        } else {
            $symlinked = false;
        }

        if ($symlinked === false) {
            self::showError(
                'Could not create symlink from "'.$target.'" to "'.$link.'"'
            );
        }

        $notify->finish();

        return true;
    }

    /**
     * Setter for folders.
     *
     * @param array $folders The folders to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected static function setFolders(array $folders = [])
    {
        self::$folders = $folders;
    }

    /**
     * Getter for folders.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The folders.
     */
    protected static function getFolders()
    {
        return self::$folders;
    }

    /**
     * Validates a path for installation.
     *
     * @param string $path The path to validation
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string TRUE if path is valid, otherwise FALSE
     *
     * @throws Exception
     */
    protected static function validatePath($path)
    {
        // Validate path by default logic
        $path = parent::validatePath($path);

        // Collection of existing folders for error message
        $existingFolders = [];

        // Check now if any of the target directories exists
        foreach (self::getFolders() as $folder) {
            if (file_exists($path.$folder)) {
                $existingFolders[] = $folder.'/';
            }
        }

        // Any folder found? => Exception
        if (count($existingFolders) > 0) {
            throw new Exception(
                'The target directory contains the following files/folders already: '.
                implode(' & ', $existingFolders).'.'.PHP_EOL.'Remove those files/folders first and try again.'.
                PHP_EOL
            );
        }

        return $path;
    }
}
