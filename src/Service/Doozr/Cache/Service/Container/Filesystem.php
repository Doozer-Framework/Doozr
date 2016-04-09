<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Cache - Service - Container - Filesystem.
 *
 * Filesystem.php - Container Filesystem: Serves I/O access to the filesystem.
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
require_once DOOZR_DOCUMENT_ROOT.'Service/Doozr/Cache/Service/Container.php';

/**
 * Doozr - Cache - Service - Container - Filesystem.
 *
 * Container Filesystem: Serves I/O access to the filesystem.
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
class Doozr_Cache_Service_Container_Filesystem extends Doozr_Cache_Service_Container
{
    /**
     * File locking.
     *
     * With file container, it's possible, that you get corrupted data-entries under bad circumstances.
     * The file locking must improve this problem but it's experimental stuff. So the default value is false.
     * But it seems to give good results
     *
     * @var bool
     */
    protected $locking = true;

    /**
     * List of group-directories.
     *
     * @var array
     */
    protected $directoriesByNamespace = [];

    /**
     * Directory where to put the cache files. Make sure to add a trailing slash!
     *
     * @var string
     */
    protected $directory;

    /**
     * Filename prefix for cache files.
     *
     * You can use the filename prefix to implement a "domain" based cache or just to give the files a more
     * descriptive name. The word "domain" is borroed from a user authentification system. One user id
     * (cached dataset with the ID x) may exists in different domains (different filename prefix). You might want
     * to use this to have different cache values for a production, development and quality assurance system.
     * If you want the production cache not to be influenced by the quality assurance activities, use different
     * filename prefixes for them.
     *
     * I personally don't think that you'll never need this, but 640kb happend to be not enough, so...
     * you know what I mean. If you find a useful application of the feature please update this inline doc.
     *
     * @var string
     */
    protected $filenamePrefix = '';

    /**
     * Max Line Length of userdata.
     *
     * If set to 0, it will take the default (1024 in php 4.2, unlimited in php 4.3)
     * see http://ch.php.net/manual/en/function.fgets.php for details
     *
     * @var int
     */
    protected $maxUserdataLineLength = 257;

    /**
     * the allowed options specific for this container.
     *
     * @var array
     */
    protected $thisContainerAllowedOptions = [
        'directory',
        'filenamePrefix',
        'maxUserdataLineLength',
    ];

    /**
     * Whether the filesystem structure used for caching is flat:.
     *
     * @example /tmp/doozr.cache (better performance)
     *
     * or not
     * @example /tmp/doozr/cache
     *
     * @var bool
     */
    protected $flatDirectoryStructure = true;


    /**
     * Constructor.
     *
     * @param array $options Custom configuration options
     *
     * @throws Doozr_Cache_Service_Exception
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Cache_Service_Container_Filesystem
     */
    public function __construct(array $options = [])
    {
        // Do the check and transfer of allowed options
        parent::__construct($options);

        $this
            ->directory(sys_get_temp_dir().DIRECTORY_SEPARATOR)
            ->prepareFilesystemAccess()
            ->clear();
    }

    /**
     * Creates a new dataset from input and storages it in cache.
     *
     * WARNING: If you supply userdata it must not contain any linebreaks,
     * otherwise it will break the filestructure.
     *
     * @param string $key       The entry key
     * @param string $value     The entry value
     * @param string $namespace The dataset namespace
     * @param int    $lifetime  Date/Time on which the cache-entry expires
     * @param string $userdata  The custom userdata to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     *
     * @throws Doozr_Cache_Service_Exception
     */
    public function create($key, $value, $lifetime, $namespace, $userdata = null)
    {
        // Get internal used key
        $key = $this->calculateUuid($key);

        // On create we need to purge the old entry from runtime cache ...
        $this->purgeRuntimeCache($key, $namespace);

        // Get filename ...
        $filename = $this->getFilenameByKeyAndNamespace($key, $namespace);

        // Build dataset from input
        $dataset = [
            $this->getExpiresAbsolute($lifetime),
            $userdata,
            $this->encode($value),
        ];

        // File format: 1st line: expiration date, 2nd line: user data, 3rd+ lines: cache data
        $result = $this->writeFile(
            $filename,
            $dataset
        );

        if ($result === true) {
            $this->addToRuntimeCache(
                $key,
                $dataset,
                $namespace
            );
        }

        return $result;
    }

    /**
     * Reads an entry from cache.
     *
     * @param string $key       The key to read data from
     * @param string $namespace The namespace used for that
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool|array The data from cache if set, otherwise FALSE
     */
    public function read($key, $namespace)
    {
        // Get internal used key
        $key = $this->calculateUuid($key);

        // Try to retrieve data from runtime cache ...
        $dataset = $this->getFromRuntimeCache($key, $namespace);

        // Check for result from runtime cache
        if ($dataset === false) {

            // get filename
            $filename = $this->getFilenameByKeyAndNamespace($key, $namespace);

            // if file does not exist -> then there is nothing to read
            if (!file_exists($filename)) {
                return false;
            }

            // Read (no lock = change possible from another process!)
            $dataset = $this->readFile($filename, false);
        }

        return $dataset;
    }

    /**
     * Deletes a dataset from cache.
     *
     * @param string $key       The key of the cache entry
     * @param string $namespace The namespace of the dataset
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE on success, otherwise FALSE
     */
    public function delete($key, $namespace)
    {
        // Get internal key
        $key = $this->calculateUuid($key);

        // Assume the process will fail
        $result = true;

        // First of all - remove element from namespace ...
        $this->purgeRuntimeCache($key, $namespace);

        // Get file
        $filename = $this->getFilenameByKeyAndNamespace($key, $namespace);

        // Check if file exists
        if (file_exists($filename) === true) {
            $result = unlink($filename);

            // clear php's file cache
            $this->clear();
        }

        // return the result
        return $result;
    }

    /**
     * Returns the path to write cache files to for a given namespace.
     *
     * @param string $namespace The namespace to return path for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The path to write cache files for given namespace to
     *
     * @throws Doozr_Cache_Service_Exception
     */
    protected function getDirectoryByNamespace($namespace, $create = true)
    {
        // Check if directory is already known and surely created and writable!
        if (!isset($this->directoriesByNamespace[$namespace])) {

            // Check for type of directory structure
            if ($this->isFlatDirectoryStructure() !== true) {
                $structure = explode(self::NAMESPACE_SEPARATOR, $namespace);
            } else {
                $structure = [$namespace];
            }

            $targetDirectory = $this->getDirectory().implode(DIRECTORY_SEPARATOR, $structure);

            // Check if not already created ...
            if ($create === true && file_exists($targetDirectory) === false) {

                // Check if base folder is writable
                if (is_writeable($this->getDirectory()) === false) {
                    throw new Doozr_Cache_Service_Exception(
                        sprintf('Directory: "%s". isn\'t writable. Check permissions and path.', $this->getDirectory())
                    );
                }

                $directory = $this->getDirectory();

                foreach ($structure as $node) {
                    if (file_exists($directory.$node) === false) {
                        if (!mkdir($directory.$node, 0755)) {
                            throw new Doozr_Cache_Service_Exception(
                                sprintf('Can\'t make directory "%s". Check permissions and path.', $directory)
                            );
                        }

                        // mach directory
                        $directory .= $node.DIRECTORY_SEPARATOR;
                    }
                }

                // Clears file status cache
                $this->clear();
            }

            $this->directoriesByNamespace[$namespace] = $targetDirectory.DIRECTORY_SEPARATOR;
        }

        return $this->directoriesByNamespace[$namespace];
    }

    /**
     * Flushes the cache.
     *
     * This method is intend to purge the cache. It removes all caches datasets from the cache.
     *
     * @param string $namespace The dataset namespace to purge
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed Number of removed datasets on success, otherwise FALSE
     */
    public function purge($namespace)
    {
        $this->purgeRuntimeCache();

        $directory = $this->getDirectoryByNamespace($namespace);

        // delete entries and retrieve count
        $removedEntries = $this->removeEntries($directory);

        // remove namespace from array
        unset($this->directoriesByNamespace[$namespace]);

        // clear PHP's file cache
        $this->clear();

        // return the count of entries
        return $removedEntries;
    }

    /**
     * Checks if a dataset exists.
     *
     * This method is intend to check if a dataset exists.
     *
     * @param string $key       The key of the dataset
     * @param string $namespace The namespace of the dataset
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if file exist, otherwise FALSE
     *
     * @throws Doozr_Cache_Service_Exception
     */
    public function exists($key, $namespace)
    {
        // Get internal used key
        $key = $this->calculateUuid($key);

        return file_exists(
            $this->getFilenameByKeyAndNamespace($key, $namespace)
        );
    }

    /**
     * Checks if an element for a passed key & namespace combination is already expired.
     *
     * @param string $key       The key to check
     * @param string $namespace The namespace to look in
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if element is expired, otherwise FALSE
     *
     * @throws Doozr_Cache_Service_Exception
     */
    public function expired($key, $namespace)
    {
        // Get internal used key
        $key = $this->calculateUuid($key);

        // Assume item expired
        $result = true;

        // Read content from file with exclusive locking
        $dataset = $this->readFile(
            $this->getFilenameByKeyAndNamespace($key, $namespace),
            false
        );

        // Check if lifetime of entry (is written within the entry) smaller current timestamp ( = not expired = valid)
        if ((int) $dataset[0] > time()) {
            $this->addToRuntimeCache($key, $dataset, $namespace);
            $result = false;
        }

        return $result;
    }

    /**
     * Prepares the filesystem for smooth access (directory exists and writable check, trailing slash ...).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     *
     * @throws Doozr_Cache_Service_Exception
     */
    protected function prepareFilesystemAccess()
    {
        $directory = $this->getDirectory();

        // Convert relative paths to absolute. Cause it looks like the __destruct has problems with relative paths
        if ($this->unix && DIRECTORY_SEPARATOR !== $directory{0}) {
            $directory = realpath(getcwd().DIRECTORY_SEPARATOR.$directory).DIRECTORY_SEPARATOR;
        }

        // Check if a trailing slash is in directory -> we require
        if ($directory{strlen($directory) - 1} != DIRECTORY_SEPARATOR) {
            $directory .= DIRECTORY_SEPARATOR;
        }

        if (!file_exists($directory) && !is_dir($directory)) {
            if (mkdir($directory, 0755)) {
                throw new Doozr_Cache_Service_Exception(
                    sprintf('Directory "%s" for caching could not be created!', $directory)
                );
            }
        }

        $this->setDirectory($directory);

        return $this;
    }

    /**
     * Deletes all expired files. Garbage collection for files is a rather "expensive", "long time" operation.
     * All files in the cache directory have to be examined which means that they must be opened for reading,
     * the expiration date has to be read from them and if necessary they have to be unlinked (removed).
     *
     * @param string $namespace The namespace to delete items from
     * @param int    $lifetime  The maximum age for an entry of the cache
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int The number of elements removed in run
     */
    public function garbageCollection($namespace, $lifetime)
    {
        // Clear file cache
        $this->clear();

        // Get the directory to work on -> BUT don't create the structure if not exist
        $directory = $this->getDirectoryByNamespace($namespace, false);

        // Assume 0 removed elements
        $result = 0;
        if (file_exists($directory) === true) {
            $result = $this->doGarbageCollection($directory, $lifetime);
        }

        // Return the result of the operation
        return $result;
    }

    /**
     * Does the recursive gc procedure.
     *
     * @param string $directory Directory to do gc on
     * @param int    $lifetime  Maximum lifetime in seconds of an no longer used/touched entry
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int The number of elements collected and removed
     *
     * @throws Doozr_Cache_Service_Exception
     */
    protected function doGarbageCollection($directory, $lifetime)
    {
        // Check permissions
        if (
               !is_writable($directory)
            || !is_readable($directory)
            || !($directoryHandle = opendir($directory))
        ) {
            throw new Doozr_Cache_Service_Exception(
                sprintf('Can\'t write to directory "%s". Check permissions and path.', $directory)
            );
        }

        // Assume we do not collect anything
        $elementsCollected = 0;

        // Get files from directory ...
        while ($filename = readdir($directoryHandle)) {
            if ($filename === '.' || $filename === '..') {
                // skip . + ..
                continue;
            }

            $filename = $directory.$filename;

            // Security check -> we do NOT have recursive structures cause we cant ...
            if (is_dir($filename)) {
                $this->doGarbageCollection($filename.DIRECTORY_SEPARATOR, $lifetime);
                continue;
            }

            // Get last access-time => BUT WHY`?
            $lastAccess = filemtime($filename);

            // Checking last access is so much faster then reading from file so we try to exclude file read here!
            if ((time() - $lastAccess) > $lifetime) {
                if (false === unlink($filename)) {
                    throw new Doozr_Cache_Service_Exception(
                        sprintf('Can\'t unlink cache file "%s", skipping. Check permissions and path.', $filename)
                    );
                    continue;
                } else {
                    ++$elementsCollected;
                }
            } else {
                // Get getMetaComponents on file
                $fileHandle = @fopen($filename, 'rb');

                if (!$fileHandle) {
                    throw new Doozr_Cache_Service_Exception(
                        sprintf('Can\'t unlink cache file "%s", skipping. Check permissions and path.', $filename)
                    );
                    continue;
                }

                // Get expire date from within the file - first line the first 11 bytes.
                $expire = fgets($fileHandle, 11);

                // close file getMetaComponents
                fclose($fileHandle);

                // Remove if expired
                if ($expire <= time()) {
                    if (unlink($filename) === false) {
                        throw new Doozr_Cache_Service_Exception(
                            sprintf('Can\'t unlink cache file "%s", skipping. Check permissions and path.', $filename)
                        );
                        continue;
                    }
                } else {
                    $this->addEntry(
                        $lastAccess,
                        [
                            'file' => $filename,
                            'size' => filesize($filename),
                        ]
                    );

                    $this->setTotalSize($this->getTotalSize() + filesize($filename));
                }
            }
        }

        // close handler
        closedir($directoryHandle);

        // flush the disk state cache
        $this->clear();

        // Check the space used by the cache entries
        if ($this->getTotalSize() > $this->getHighwaterMarker()) {
            $entries = $this->getEntries();
            krsort($entries);
            reset($entries);

            while (($this->getTotalSize() > $this->getLowwaterMarker()) && count($entries) > 0) {
                $entry = array_shift($entries);

                if (@unlink($entry['file'])) {
                    $this->setTotalSize($this->getTotalSize() - $entry['size']);
                } else {
                    throw new Doozr_Cache_Service_Exception(
                        sprintf('Can\'t unlink cache file "%s". Check permissions and path.', $entry['file'])
                    );
                }
            }

            // Update
            $this->setEntries($entries);
        }

        // Finally done -> seems to be a worth a true
        return $elementsCollected;
    }

    /**
     * returns the filename for the specified id.
     *
     * This method is intend to return the filename for the specified id.
     *
     * @param string $key       The key of the dataset
     * @param string $namespace The cache namespace
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The filename
     *
     * @throws Doozr_Cache_Service_Exception
     */
    protected function getFilenameByKeyAndNamespace($key, $namespace)
    {
        return $this->getDirectoryByNamespace($namespace).$this->getFilenamePrefix().$key;
    }

    /**
     * Setter for locking.
     *
     * @param int $locking The locking to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setLocking($locking)
    {
        $this->locking = $locking;
    }

    /**
     * Setter for locking.
     *
     * @param int $locking The locking to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function locking($locking)
    {
        $this->setLocking($locking);

        return $this;
    }

    /**
     * Getter for locking.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|bool locking if set, otherwise NULL
     */
    protected function getLocking()
    {
        return $this->locking;
    }


    /**
     * Setter for directory.
     *
     * @param int $directory The directory to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setDirectory($directory)
    {
        $this->directory = $directory;
    }

    /**
     * Setter for directory.
     *
     * @param int $directory The directory to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function directory($directory)
    {
        $this->setDirectory($directory);

        return $this;
    }

    /**
     * Getter for directory.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|bool directory if set, otherwise NULL
     */
    protected function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Setter for flatDirectoryStructure.
     *
     * @param bool $state TRUE to set to flat, otherwise FALSE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setFlatDirectoryStructure($state)
    {
        $this->flatDirectoryStructure = $state;
    }

    /**
     * Setter for flatDirectoryStructure.
     *
     * @param bool $state TRUE to set to flat, otherwise FALSE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function flatDirectoryStructure($state)
    {
        $this->setFlatDirectoryStructure($state);

        return $this;
    }

    /**
     * Getter for flatDirectoryStructure.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|bool flatDirectoryStructure if set, otherwise NULL
     */
    protected function getFlatDirectoryStructure()
    {
        return $this->flatDirectoryStructure;
    }

    /**
     * Alias for getter for flatDirectoryStructure (as is...).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|bool flatDirectoryStructure if set, otherwise NULL
     */
    protected function isFlatDirectoryStructure()
    {
        return $this->flatDirectoryStructure === true;
    }

    /**
     * Setter for filenamePrefix.
     *
     * @param bool $state TRUE to set to flat, otherwise FALSE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setFilenamePrefix($state)
    {
        $this->filenamePrefix = $state;
    }

    /**
     * Setter for filenamePrefix.
     *
     * @param bool $state TRUE to set to flat, otherwise FALSE
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function filenamePrefix($state)
    {
        $this->setFilenamePrefix($state);

        return $this;
    }

    /**
     * Getter for filenamePrefix.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return null|bool filenamePrefix if set, otherwise NULL
     */
    protected function getFilenamePrefix()
    {
        return $this->filenamePrefix;
    }

    /**
     * Writes the passed data to file.
     *
     * @param string $filename The filename to write to
     * @param array  $dataset  The dataset to write
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return bool TRUE if file could be written successful, otherwise FALSE
     *
     * @throws Doozr_Cache_Service_Exception
     */
    protected function writeFile($filename, array $dataset)
    {
        // Assume fail
        $result = false;

        // Get getMetaComponents on file to write binary ...
        $fileHandle = @fopen($filename, 'wb');

        if (!$fileHandle) {
            throw new Doozr_Cache_Service_Exception(
                sprintf('Can\'t access "%s" to store cache data. Check access rights and path', $filename)
            );
        }

        // File locking (exclusive lock)
        if ($this->getLocking() === true) {
            flock($fileHandle, LOCK_EX);
        }

        $result = (fwrite($fileHandle, implode(PHP_EOL, $dataset)) !== false);

        // Remove file-lock
        if ($this->getLocking() === true) {
            flock($fileHandle, LOCK_UN);
        }

        // close getMetaComponents
        fclose($fileHandle);

        return $result;
    }

    /**
     * Reads a file from filesystem, modifies its "last access time" and returns values structured.
     *
     * @param string $filename The filename to read
     * @param bool   $locking  TRUE to lock file exclusive, FALSE to do not
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The data of the data-set prepared in a clean array structure
     *
     * @throws Doozr_Cache_Service_Exception
     */
    protected function readFile($filename, $locking = true)
    {
        if (!($fileHandle = @fopen($filename, 'rb'))) {
            throw new Doozr_Cache_Service_Exception(
                sprintf('Can\'t access cache file "%s". Check access rights and path.', $filename)
            );
        }

        // File locking (shared lock)
        ($locking === true) ?: flock($fileHandle, LOCK_SH);

        // File format: 1st line: expiration date - 2nd line: user data - 3rd+ lines: cache data
        $expire = trim(fgets($fileHandle, 12));

        if ($this->maxUserdataLineLength == 0) {
            $userdata = trim(fgets($fileHandle));
        } else {
            $userdata = trim(fgets($fileHandle, $this->maxUserdataLineLength));
        }

        $buffer = '';
        while (!feof($fileHandle)) {
            $buffer .= fread($fileHandle, 8192);
        }

        $value = $this->decode($buffer);

        // Unlocking
        ($locking === true) ?: flock($fileHandle, LOCK_UN);

        fclose($fileHandle);

        // last usage date used by the gc - maxlifetime touch without second param produced stupid entries...
        touch($filename, time());

        $this->clear();

        // Return the result
        return [
            $expire,
            $userdata,
            $value,
        ];
    }

    /**
     * Removes entries from cache storage recursive.
     *
     * @param string $directory The directory to delete/remove/unlink
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return mixed Number of removed entries on success, otherwise FALSE
     *
     * @throws Doozr_Cache_Service_Exception
     */
    protected function removeEntries($directory)
    {
        if (
            !is_writable($directory) ||
            !is_readable($directory) ||
            !is_dir($directory) ||
            !($directoryHandle = opendir($directory))
        ) {
            throw new Doozr_Cache_Service_Exception(
                sprintf('Can\'t remove directory "%s". Check permissions and path.', $directory)
            );
        }

        // count of entries removed
        $entriesRemoved = 0;

        // Iterate
        while (false !== $file = readdir($directoryHandle)) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            // Combine directory + file
            $file = $directory.$file;

            // Check if entry is directory
            if (is_dir($file)) {
                // if is directory add slash
                $file .= DIRECTORY_SEPARATOR;

                // Now remove entries and get count
                $entriesRemoved += $this->removeEntries($file);
            } else {
                // Entry is a file -> so remove
                if (unlink($file)) {
                    ++$entriesRemoved;
                }
            }
        }

        // according to php-manual the following is needed for windows installations.
        closedir($directoryHandle);

        // unset the getMetaComponents
        unset($directoryHandle);

        // if directory given isn't the cache-directory -> remove it to
        if ($directory !== $this->getDirectory()) {
            rmdir($directory);
            ++$entriesRemoved;
        }

        // return the count of removed entries
        return $entriesRemoved;
    }

    /**
     * Shortcut to clearstatcache.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function clear()
    {
        // clear file status cache
        clearstatcache();

        return $this;
    }
}
