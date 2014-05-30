<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Service Cache
 *
 * Filesystem.php - Container Filesystem: Serves I/O access to the filesystem.
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
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Cache
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Cache/Service/Container.php';
require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Cache/Service/Container/Interface.php';

/**
 * DoozR Service Cache
 *
 * Container Filesystem: Serves I/O access to the filesystem.
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Cache
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @throws     Service_DoozR_Cache_Service_Exception
 * @service    Multiple
 */
class DoozR_Cache_Service_Container_Filesystem extends DoozR_Cache_Service_Container
implements DoozR_Cache_Service_Container_Interface
{
    /**
     * File locking
     *
     * With file container, it's possible, that you get corrupted data-entries under bad circumstances.
     * The file locking must improve this problem but it's experimental stuff. So the default value is false.
     * But it seems to give good results
     *
     * @var boolean
     * @access private
     */
    private $_fileLocking = false;

    /**
     * List of cache entries, used within garbageCollection()
     *
     * @var array
     * @access private
     */
    private $_entries = array();

    /**
     * List of group-directories
     *
     * @var array
     * @access private
     */
    private $_groupDirs = array();

    /**
     * Total number of bytes required by all cache entries, used within a gc run.
     *
     * @var int
     * @access private
     */
    private $_totalSize = 0;

    /**
     * Directory where to put the cache files. Make sure to add a trailing slash!
     *
     * @var string
     * @access protected
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
     * @access protected
     */
    protected $filenamePrefix = '';

    /**
     * Max Line Length of userdata
     *
     * If set to 0, it will take the default (1024 in php 4.2, unlimited in php 4.3)
     * see http://ch.php.net/manual/en/function.fgets.php for details
     *
     * @var integer
     * @access protected
     */
    protected $maxUserdataLinelength = 257;

    /**
     * the allowed options specific for this container
     *
     * @var array
     * @access protected
     */
    protected $thisContainerAllowedOptions = array(
        'directory',
        'filenamePrefix',
        'maxUserdataLinelength'
    );


    /**
     * constructor
     *
     * This method is intend to act as constructor.
     * If you use custom configuration options -> ensure that they are enabled via $thisContainerAllowedOptions!
     *
     * @param array $options Custom configuration options
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object instance of this class
     * @access public
     * @throws DoozR_Cache_Service_Exception
     */
    public function __construct(array $options = array())
    {
        // do the check and transfer of allowed options
        parent::__construct($options);

        // important: check cache directory
        if (!$this->directory) {
            throw new DoozR_Cache_Service_Exception(
                'No cache-directory configured! Please configure "directory" if you use container of type "File".'
            );
        }

        // clear file status cache
        clearstatcache();

        // some basic bug-preventive operations
        $this->_preventiveCorrections();
    }

    /**
     * checks if a dataset exists
     *
     * This method is intend to check if a dataset exists.
     *
     * @param string $id    The id of the dataset
     * @param string $group The group of the dataset
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if file exist, otherwise FALSE
     * @access protected
     * @throws DoozR_Cache_Service_Exception
     */
    protected function idExists($id, $group)
    {
        return file_exists(
            $this->_getFilename($id, $group)
        );
    }

    /**
     * does some very important bug-prevention operations
     *
     * This method is intend to do some very important bug-prevention operations.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     * @throws DoozR_Cache_Service_Exception
     */
    private function _preventiveCorrections()
    {
        // convert relative paths to absolute. cause it looks like the deconstructor has problems with relative paths
        if ($this->unix && '/' != $this->directory{0}) {
            $this->directory = realpath(getcwd().'/'.$this->directory).'/';
        }

        // check if a trailing slash is in directory
        if ($this->directory{strlen($this->directory)-1} != DIRECTORY_SEPARATOR) {
            $this->directory .= DIRECTORY_SEPARATOR;
        }

        if (!file_exists($this->directory) || !is_dir($this->directory)) {
            if (mkdir($this->directory, 0755)) {
                throw new DoozR_Cache_Service_Exception(
                    'Cache-Directory could not be created!'
                );
            }
        }
    }

    /**
     * deletes a directory and all files in it
     *
     * This method is intend to delete a directory and all files in it.
     *
     * @param string $directory The directory to delete/remove/unlink
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Number of removed entries on success, otherwise FALSE
     * @access private
     * @throws DoozR_Cache_Service_Exception
     */
    private function _removeEntries($directory)
    {
        if (!is_writable($directory) || !is_readable($directory) || !($directoryHandle = opendir($directory))) {
            throw new DoozR_Cache_Service_Exception(
                'Can\'t remove directory "'.$directory.'". Check permissions and path.'
            );
        }

        // count of entries removed
        $entriesRemoved = 0;

        // iterate
        while (false !== $file = readdir($directoryHandle)) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            // combine directory + file
            $file = $directory.$file;

            // check if entry is directory
            if (is_dir($file)) {
                // if is directory add slash
                $file .= DIRECTORY_SEPARATOR;

                // now remove entries and get count
                $removedEntries = $this->_removeEntries($file);

                // check if return value is valid integer
                if (is_int($removedEntries)) {
                    // increase total removed counter
                    $entriesRemoved += $removedEntries;
                }
            } else {
                // entry is file -> remove
                if (unlink($file)) {
                    $entriesRemoved++;
                }
            }
        }

        // according to php-manual the following is needed for windows installations.
        closedir($directoryHandle);

        // unset the handle - TODO: required?
        unset($directoryHandle);

        // if directory given isn't the cache-directory -> remove it to
        if ($directory != $this->directory) {
            rmdir($directory);
            $entriesRemoved++;
        }

        // return the count of removed entries
        return $entriesRemoved;
    }

    /**
     * flushes the cache
     *
     * This method is intend to flush the cache. It removes all caches datasets from the cache.
     *
     * @param string $group The dataset group to flush
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Number of removed datasets on success, otherwise FALSE
     * @access public
     */
    public function flush($group)
    {
        // flush
        $this->flushPreload();

        // which directory?
        $directory = ($group) ? $this->directory.$group.DIRECTORY_SEPARATOR : $this->directory;

        // delete entries and retrieve count
        $removedEntries = $this->_removeEntries($directory);

        // remove group from array
        unset($this->groupDirs[$group]);

        // clear PHP's file cache
        clearstatcache();

        // return the count of entries
        return $removedEntries;
    }

    /**
     * stores a dataset
     *
     * This method is intend to write data to cache.
     * WARNING: If you supply userdata it must not contain any linebreaks, otherwise it will break the filestructure.
     *
     * @param string     $id       The dataset Id
     * @param string     $data     The data to write to cache
     * @param integer    $expires  Date/Time on which the cache-entry expires
     * @param string     $group    The dataset group
     * @param string     $userdata The custom userdata to add
     * @param null|mixed $userdata The additional userdata
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success
     * @access public
     * @throws DoozR_Cache_Service_Exception
     */
    public function create($id, $data, $expires, $group, $userdata = null)
    {
        // flush
        $this->flushPreload($id, $group);

        // get file to write to
        $file = $this->_getFilename($id, $group);

        // get handle on file
        $fileHandle = @fopen($file, 'wb');

        // throw exception if filehandle can not be received
        if (!$fileHandle) {
            throw new DoozR_Cache_Service_Exception(
                'Can\'t access "'.$file.'" to store cache data. Check access rights and path'
               );
        }

        // file locking (exclusive lock)
        if ($this->_fileLocking) {
            flock($fileHandle, LOCK_EX);
        }

        // file format:
        // 1st line: expiration date
        // 2nd line: user data
        // 3rd+ lines: cache data
        fwrite($fileHandle, $this->getExpiresAbsolute($expires)."\n");
        fwrite($fileHandle, $userdata."\n");
        fwrite($fileHandle, $this->encode($data));

        // remove file-lock
        if ($this->_fileLocking) {
            flock($fileHandle, LOCK_UN);
        }

        // close handle
        fclose($fileHandle);

        // success
        return true;
    }

    /**
     * reads a dataset
     *
     * This method is intend to read data from cache.
     *
     * @param string $id    The dataset Id
     * @param string $group The dataset group
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The data from cache
     * @access public
     * @throws DoozR_Cache_Service_Exception
     */
    public function read($id, $group)
    {
        // get filename
        $file = $this->_getFilename($id, $group);

        // if file does not exist -> then there is nothing to read
        if (!file_exists($file)) {
            return array(
                null,
                null,
                null
            );
        }

        // otherwise retrieve the content
        if (!($fileHandle = @fopen($file, 'rb'))) {
            throw new DoozR_Cache_Service_Exception(
                'Can\'t access cache file "'.$file.'". Check access rights and path.'
              );
        }

        // file locking (shared lock)
        if ($this->_fileLocking) {
            flock($fileHandle, LOCK_SH);
        }

        // file format:
        // 1st line: expiration date
        // 2nd line: user data
        // 3rd+ lines: cache data
        $expire = trim(fgets($fileHandle, 12));

        if ($this->maxUserdataLinelength == 0 ) {
            $userdata = trim(fgets($fileHandle));
        } else {
            $userdata = trim(fgets($fileHandle, $this->maxUserdataLinelength));
        }
        $buffer = '';
        while (!feof($fileHandle)) {
            $buffer .= fread($fileHandle, 8192);
        }
        $data = $this->decode($buffer);

        // Unlocking
        if ($this->_fileLocking) {
            flock($fileHandle, LOCK_UN);
        }
        fclose($fileHandle);

        // last usage date used by the gc - maxlifetime
        // touch without second param produced stupid entries...
        touch($file, time());
        clearstatcache();

        return array($expire, $data, $userdata);
    }

    /**
     * removes a dataset finally from container
     *
     * This method is intend to remove an dataset finally from container.
     *
     * @param string $id    The id of the dataset
     * @param string $group The group of the dataset
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access protected
     */
    public function delete($id, $group)
    {
        // IMPORTANT: flush preload
        $this->flushPreload($id, $group);

        // get file
        $file = $this->_getFilename($id, $group);

        // assume the process will fail
        $result = false;

        // check if file exists
        if (file_exists($file)) {
            // delete file
            $result = unlink($file);

            // clear php's file cache
            clearstatcache();
        }

        // return the result
        return $result;
    }

    /**
     * deletes all expired files
     *
     * This method is intend to delete all expired files.
     * Garbage collection for files is a rather "expensive", "long time" operation. All files in the cache
     * directory have to be examined which means that they must be opened for reading, the expiration date has
     * to be read from them and if neccessary they have to be unlinked (removed). If you have a user comment
     * for a good default gc probability please add it to to the inline docs.
     *
     * @param integer $maxlifetime Maximum lifetime in seconds of an no longer used/touched entry
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean The result of the operation
     * @access public
     * @throws DoozR_Cache_Service_Exception
     */
    public function garbageCollection($maxlifetime)
    {
        // do the flush
        parent::garbageCollection($maxlifetime);

        // clear file cache
        clearstatcache();

        // clean up
        $result = $this->_doGarbageCollection($maxlifetime, $this->directory);

        // check the space used by the cache entries
        if ($this->_totalSize > $this->highwater) {
            krsort($this->_entries);
            reset($this->_entries);

            while ($this->_totalSize > $this->lowwater && list($lastmod, $entry) = each($this->_entries)) {
                if (@unlink($entry['file'])) {
                    $this->_totalSize -= $entry['size'];
                } else {
                    throw new DoozR_Cache_Service_Exception(
                        'Can\'t delete '.$entry['file'].'. Check the permissions.'
                    );
                }
            }
        }

        $this->_entries = array();
        $this->_totalSize = 0;

        // return the result of the operation
        return $result;
    }

    /**
     * returns the filename for the specified id
     *
     * This method is intend to return the filename for the specified id.
     *
     * @param string $id    The dataset Id
     * @param string $group The cache group
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The filename
     * @access private
     * @throws DoozR_Cache_Service_Exception
     */
    private function _getFilename($id, $group)
    {
        //
        if (isset($this->_groupDirs[$group])) {
            return $this->_groupDirs[$group].$this->filenamePrefix.$id;
        }

        // construct path/directory
        $directory = $this->directory.$group.DIRECTORY_SEPARATOR;

        // check if folder is writable
        if (is_writeable($this->directory)) {
            if (!file_exists($directory)) {
                if (!mkdir($directory, 0755)) {
                    throw new DoozR_Cache_Service_Exception(
                        'Can\'t make directory "'.$directory.'". Check permissions and path.'
                    );
                }

                // clears file status cache
                clearstatcache();
            }
        } else {
            throw new DoozR_Cache_Service_Exception(
                'Directory: "'.$this->directory.'". isn\'t writable. Check permissions and path.'
            );
        }

        // store
        $this->_groupDirs[$group] = $directory;

        // return full qualified path + filename
        return $directory.$this->filenamePrefix.$id;
    }

    /**
     * does the recursive gc procedure
     *
     * This method is intend to do the recursive gc procedure.
     *
     * @param integer $maxlifetime Maximum lifetime in seconds of an no longer used/touched entry
     * @param string  $directory   Directory to examine - don't sets this parameter, it's used for a
     *                             recursive function call!
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     * @throws DoozR_Cache_Service_Exception
     */
    private function _doGarbageCollection($maxlifetime, $directory)
    {
        // check permissions
        if (!is_writable($directory)
            || !is_readable($directory)
            || !($directoryHandle = opendir($directory))
        ) {
            throw new DoozR_Cache_Service_Exception(
                'Can\'t remove directory "'.$directory.'". Check permissions and path.'
            );
        }

        // get files
        while ($file = readdir($directoryHandle)) {
            // skip . + ..
            if ($file == '.' || $file == '..') {
                continue;
            }

            $file = $directory.$file;

            if (is_dir($file)) {
                $this->_doGarbageCollection($maxlifetime, $file.'/');
                continue;
            }

            // get handle on file
            $fileHandle = @fopen($file, 'rb');

            // skip trouble makers but inform the user
            if (!$fileHandle) {
                throw new DoozR_Cache_Service_Exception(
                    'Can\'t access cache file "'.$file.', skipping it. Check permissions and path.'
                );
                continue;
            }

            // get expire date
            $expire = fgets($fileHandle, 11);

            // close file handle
            fclose($fileHandle);

            // get last accesstime
            $lastused = filemtime($file);

            $this->_entries[$lastused] = array('file' => $file, 'size' => filesize($file));
            $this->_totalSize += filesize($file);

            // remove if expired
            if ((($expire && $expire <= time()) || ($lastused <= (time() - $maxlifetime)) ) && !unlink($file)) {
                throw new DoozR_Cache_Service_Exception(
                    'Can\'t unlink cache file "'.$file.'", skipping. Check permissions and path.'
                );
            }
        }

        // close handler
        closedir($directoryHandle);

        // flush the disk state cache
        clearstatcache();
    }
}
