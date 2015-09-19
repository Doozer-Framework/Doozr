<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Filesystem - Service
 *
 * Service.php - Doozr Service for all filesystem operations with virtual-filesystem
 * support (e.g. for unit-testing).
 *
 * PHP versions 5.5
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
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
 * @package    Doozr_Service
 * @subpackage Doozr_Service_Filesystem
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Service/Multiple.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Exception.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Service/Interface.php';

use Doozr\Loader\Serviceloader\Annotation\Inject;

/**
 * Doozr - Filesystem - Service
 *
 * Doozr Service for all filesystem operations with virtual-filesystem support
 * (e.g. for unit-testing).
 *
 * @category   Doozr
 * @package    Doozr_Service
 * @subpackage Doozr_Service_Filesystem
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 * @Inject(
 *     id     = "bens.foo.bar",
 *     link   = "doozr.registry",
 *     type   = "constructor",
 *     target = "getInstance"
 * )
 */
class Doozr_Filesystem_Service extends Doozr_Base_Service_Multiple
    implements
    Doozr_Base_Service_Interface
{
    /**
     * Status of "is-virtual" of this instance
     *
     * @var bool
     * @access protected
     */
    protected $isVirtual = false;

    /**
     * Reference of module virtual-filesystem
     *
     * @var object
     * @access protected
     */
    protected $vfs;

    /**
     * Contains the last used path of vfs
     *
     * @var string
     * @access protected
     */
    protected $vfsLastPath;

    /**
     * Pattern to check NULL-Byte against
     *
     * @var string
     * @access protected
     * @static
     */
    protected static $nullBytePattern = "\0";

    /**
     * Current resource
     *
     * @var array
     * @access protected
     */
    protected $currentResourceInformation;

    /**
     * Open file-handles for persistent access
     *
     * @see __teardown()
     * @var array
     * @access protected
     */
    protected $fileHandle = [];

    /**
     * Resources info/data
     *
     * @var array
     * @access protected
     */
    protected $resources = [];

    /**
     * File mode for reading
     *
     * @var string
     * @access const
     */
    const FILE_MODE_READ = 'r';

    /**
     * File mode for reading binary (forced)
     *
     * @var string
     * @access const
     */
    const FILE_MODE_READ_BINARY = 'rb';

    /**
     * File mode for reading and creating
     *
     * @var string
     * @access const
     */
    const FILE_MODE_READ_WRITE = 'r+';

    /**
     * File mode for reading and creating binary (forced)
     *
     * @var string
     * @access const
     */
    const FILE_MODE_READ_WRITE_BINARY = 'rb+';

    /**
     * File mode for writing
     *
     * @var string
     * @access const
     */
    const FILE_MODE_WRITE = 'w';

    /**
     * File mode for writing binary (forced)
     *
     * @var string
     * @access const
     */
    const FILE_MODE_WRITE_BINARY = 'wb';

    /**
     * File mode for writing and creating
     *
     * @var string
     * @access const
     */
    const FILE_MODE_WRITE_READ = 'w+';

    /**
     * File mode for writing and creating binary (forced)
     *
     * @var string
     * @access const
     */
    const FILE_MODE_WRITE_READ_BINARY = 'wb+';

    /**
     * File mode for appending
     *
     * @var string
     * @access const
     */
    const FILE_MODE_WRITE_APPEND = 'a';

    /**
     * file mode for appending and creating
     *
     * @var string
     * @access const
     */
    const FILE_MODE_WRITE_READ_APPEND = 'a+';

    /**
     * File mode for binary operations
     *
     * @var int
     * @access const
     */
    const FILE_BINARY = FILE_BINARY;

    /**
     * file mode for ascii operations
     *
     * @var int
     * @access const
     */
    const FILE_TEXT = FILE_TEXT;

    /**
     * file mode for append
     *
     * @var int
     * @access const
     */
    const FILE_APPEND = FILE_APPEND;

    /**
     * lock mode for exclusive locking
     *
     * @var int
     * @access const
     */
    const FILE_LOCK_EXCLUSIVE = LOCK_EX;

    /**
     * complete copy (whole file) max length
     *
     * @var int
     * @access const
     */
    const PHP_STREAM_COPY_ALL = 2000000;

    /**
     * replacement for __construct
     *
     * This method is intend as replacement for __construct
     * PLEASE DO NOT USE __construct() - make always use of __tearup()!
     *
     * @param bool $virtual True to mock a filesystem (vfs = virtual filesystem)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __tearup($virtual = false)
    {
        // is virtual?
        if ($virtual !== false) {
            // store information that this instance is virtual (fs)
            $this->isVirtual = true;

            // get vfs instance
            $this->vfs = Doozr_Loader_Serviceloader::load('virtualfilesystem');
        }
    }

    /**
     * replacement for __destruct
     *
     * This method is intend as replacement for __destruct
     * PLEASE DO NOT USE __destruct() - make always use of __teardown()!
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __teardown()
    {
        // Close all (still) open file handles
        foreach ($this->fileHandle as $uid => $fileHandle) {
            // close
            fclose($fileHandle['handle']);
        }

        // Just to be sure leave current stats
        clearstatcache();
    }

    /*------------------------------------------------------------------------------------------------------------------
    | BEGIN PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Writes a string of data to a file
     *
     * This method is intend to write a string of data to a file.
     *
     * @param string  $file        The file to write the content to
     * @param string  $data        The content to write to the file
     * @param bool $append      TRUE to append to, FALSE to rewrite the file
     * @param bool $create      TRUE to create file if it not exist, FALSE to do not
     * @param bool $modeBoolean TRUE to return result as bool, otherwise FALSE to return number of bytes written
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Number of bytes written if everything wents fine, otherwise false
     * @access public
     * @throws Doozr_Exception
     */
    public function write($file, $data, $append = false, $create = true, $modeBoolean = true)
    {
        // Preprocessing
        $file = $this->_preProcess($file);

        // exception for the case that the file|folder isn't writable
        if (!$this->_is_resource_writable($file)) {
            throw new Doozr_Exception(
                'The file "'.$file.'" could not be written. Current operation failed. Check permissions.'
            );
        }

        // Decide which mode
        if ($append) {
            $result = $this->_append($file, $data, $create);
        } else {
            $result = $this->_rewrite($file, $data, $create);
        }

        // transform result?
        if ($modeBoolean) {
            $result = ($result !== false) ? true : false;
        }

        // return the result of operation
        return $result;
    }

    /**
     * CRUD shortcut to write() for creating files following the CRUD sheme
     *
     * @param string $resource The resource to create
     * @param string $buffer   The content to write
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Number of bytes written if everything wents fine, otherwise false
     * @access public
     * @throws Doozr_Exception
     */
    public function create($resource, $buffer)
    {
        return $this->write($resource, $buffer);
    }

    /**
     * CRUD shortcut to write() for updating the content of a file following the CRUD sheme
     *
     * @param string $resource The resource to create
     * @param string $buffer   The content to write
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Number of bytes written if everything wents fine, otherwise false
     * @access public
     * @throws Doozr_Exception
     */
    public function upate($resource, $buffer)
    {
        return $this->write($resource, $buffer, false, false);
    }

    /**
     * Write content to a file in persistent runtimeEnvironment
     *
     * This method is intend to write content to a file in persistent runtimeEnvironment.
     *
     * @param string $file        The file to write the content to
     * @param string $data        The content to write to the file
     * @param bool   $append      TRUE to append to, FALSE to rewrite the file
     * @param bool   $create      TRUE to create file if it not exist, FALSE to do not
     * @param bool   $modeBoolean TRUE to return result as bool, otherwise FALSE to return number of bytes written
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed TRUE/FALSE if runtimeEnvironment = bool, otherwise number of bytes written on success + false on failure
     * @access public
     * @throws Doozr_Exception
     */
    public function pwrite($file, $data, $append = false, $create = true, $modeBoolean = true)
    {
        // Preprocessing
        $file = $this->_preProcess($file);

        // exception for the case that the file|folder isn't writable
        if (!$this->_is_resource_writable($file)) {
            throw new Doozr_Exception(
                'Could not persistent write to file "'.$file.'". Current operation failed. Check permissions.'
            );
        }

        // Get hash (previously calculated)
        $uid = $this->getCurrentResourceInformation('uid');

        // get handle and write data
        $result = $this->_fwrite(
            $this->_getFileHandle($file, ($append ? self::FILE_MODE_WRITE_APPEND : self::FILE_MODE_WRITE)),
            $data
        );

        // transform result?
        if ($modeBoolean) {
            $result = ($result !== false) ? true : false;
        }

        // return the result of operation
        return $result;
    }

    /**
     * Writes binary-data to a file
     *
     * This method is intend to write binary-data to a file.
     *
     * @param string $file        The file to write the content to
     * @param string $data        The content to write to the file
     * @param bool   $create      TRUE to create file if it not exist, FALSE to do not
     * @param bool   $append      Controls if the content should be appended or rewrite the file
     * @param bool   $modeBoolean TRUE to return result as bool, otherwise FALSE to return number of bytes written
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Number of bytes written if everything wents fine, otherwise false
     * @access public
     * @throws Doozr_Exception
     */
    public function writeBinary($file, $data, $create = true, $append = false, $modeBoolean = true)
    {
        // Preprocessing
        $file = $this->_preProcess($file);

        // exception for the case that the file|folder isn't writable
        if (!$this->_is_writable($file)) {
            throw new Doozr_Exception(
                'Could not write binary data to file. The file "'.$file.'" could not be written. '.
                'Current operation failed. Check permissions.'
            );
        }

        // append or complete rewrite file?
        if ($append) {
            $result = $this->_append($file, $data, $create, self::FILE_BINARY);
        } else {
            $result = $this->_rewrite($file, $data, $create, self::FILE_BINARY);
        }

        // transform result?
        if ($modeBoolean) {
            $result = ($result !== false) ? true : false;
        }

        // return the result of operation
        return $result;
    }

    /**
     * Appends binary data to a file
     *
     * This method is intend to append binary data to a file.
     *
     * @param string $file       The file to write the content to
     * @param string $data       The content to write to the file
     * @param bool   $create     TRUE to create file if it not exist, FALSE to do not
     * @param bool   $writecheck Controls if writecheck should be performed
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Number of bytes written if everything wents fine, otherwise false
     * @access public
     * @throws Doozr_Exception
     */
    public function appendBinary($file, $data, $create = true, $writecheck = true)
    {
        // Preprocessing
        $file = $this->_preProcess($file);

        // if write checking is enabled
        if ($writecheck && !$this->_is_writable($file)) {
            throw new Doozr_Exception(
                'Could not write binary data to file. The file "'.$file.'" could not be written. '.
                'Current operation failed. Check permissions.'
            );
        }

        // return result of operation
        return $this->_append($file, $data, $create, self::FILE_BINARY);
    }

    /**
     * appends a string to a file
     *
     * This method is intend to append a string (or binary-data) to a file.
     *
     * @param string $file        The file to write the content to
     * @param string $data        The content to write to the file
     * @param bool   $create      TRUE to create file if it not exists, FALSE to do not
     * @param bool   $modeBoolean TRUE to return result as bool, otherwise FALSE to return number of bytes written
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Number of bytes written if everything wents fine, otherwise false
     * @access public
     * @throws Doozr_Exception
     */
    public function append($file, $data, $create = true, $modeBoolean = true)
    {
        // Preprocessing
        $file = $this->_preProcess($file);

        if (!$create && !$this->_resource_exists($file)) {
            throw new Doozr_Exception(
                'Could not append to file. The file: "'.$file.'" does not exist.'.
                'Current operation failed. Create file first or set parameter $create to TRUE'
            );
        }

        // exception for the case that the file|folder isn't writable
        if (!$this->_is_writable($file)) {
            throw new Doozr_Exception(
                'Could not append to file. Could not write given content to file: "'.$file.'" '.
                'Current operation failed. Check permissions.'
            );
        }

        // put the content to file
        $result = $this->_append($file, $data, $create);

        // transform result?
        if ($modeBoolean) {
            $result = ($result >= 0) ? true : false;
        }

        // return result of write operation
        return $result;
    }

    /**
     * Returns the content of a file
     *
     * This method is intend to return the content of a file.
     *
     * @param string $file             File to read from
     * @param int    $maxlen           Number of bytes to read
     * @param bool   $use_include_path TRUE to search file in include path, otherwise FALSE to do not
     * @param string $context          Context for reading from file
     * @param int    $offset           The offset from which to start reading
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The content of the file
     * @access public
     * @throws Doozr_Exception
     */
    public function read(
        $file,
        $maxlen = self::PHP_STREAM_COPY_ALL,
        $use_include_path = false,
        $context = null,
        $offset = -1
    ) {
        // Preprocessing
        $file = $this->_preProcess($file);

        // exception for the case that the file to read does not exist
        if (!$this->_resource_exists($file)) {
            throw new Doozr_Exception(
                'Could not read from file. File: "'.$file.'" does not exist. Current operation failed.'
            );
        }

        $buffer = $this->_file_get_contents($file, $use_include_path, $context, $offset, $maxlen);

        // return content from file
        return $buffer;
    }

    /**
     * Returns the content of a file
     *
     * This method is intend to return the content of a file in persistent runtimeEnvironment.
     *
     * @param string $file   The name of the file to read
     * @param mixed  $length The number of bytes to read
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The content of the file
     * @access public
     * @throws Doozr_Exception
     */
    public function pread($file, $length = null)
    {
        // Preprocessing
        $file = $this->_preProcess($file);

        // exception for the case that the file to read does not exist
        if (!$this->_resource_exists($file)) {
            throw new Doozr_Exception(
                'Could not persistent read from file. The file: "'.$file.'" does not exist. '.
                'Current operation failed. Check permissions too.'
            );
        }

        // get unique id of file
        $uid = $this->getCurrentResourceInformation('uid');

        if (!$this->getResourceInformation($uid, 'size')) {
            // store the filesize for further operations
            $size = $this->_filesize($file);

            // set size to current resource information
            $this->setCurrentResourceInformation($size, 'size');

            // update stored resource information
            $this->setResourceInformation($uid, $this->getCurrentResourceInformation());
        }

        // read a specified length of bytes?
        if (!$length) {
            $length = $this->getCurrentResourceInformation('size');

        } elseif ($length > $this->getCurrentResourceInformation('size')) {
            $length = $this->getCurrentResourceInformation('size');

        }

        // read defined length of bytes and return
        return $this->_fread(
            $this->_getFileHandle($file, self::FILE_MODE_READ),
            $length
        );
    }

    /**
     * Reads content from file and return a row-based array
     *
     * This method is intend to read content from a file and return a row-based array.
     *
     * @param string $file The file to read into array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The content of the file
     * @access public
     * @throws Doozr_Exception
     */
    public function readArray($file = null)
    {
        // Preprocessing
        $file = $this->_preProcess($file);

        // exception for the case that the file to read does not exist
        if (!$this->_resource_exists($file)) {
            throw new Doozr_Exception(
                'Could not read file into array. The file: "'.$file.'" does not exist. '.
                'Current operation failed. Check permissions too.'
            );
        }

        // return content as array
        return $this->_file($file);
    }


    /**
     * checks existence of a resource (file | folder)
     *
     * This method is intend to check the existence of a resource (file | folder).
     *
     * @param string $resource The resource to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if resource exists, otherwise FALSE
     * @access public
     */
    public function exists($resource)
    {
        // Preprocessing
        $resource = $this->_preProcess($resource);

        // return result
        return $this->_resource_exists($resource);
    }

    /**
     * Reads and parses a PHP file.
     *
     * This method is intend to read and parse a PHP-file.
     * The content is parsed end executed by PHP runtime and
     * afterwards the whole buffer is returned.
     *
     * @param string $file The file to parse as php-file
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The content of the file
     * @access public
     * @throws Doozr_Exception
     */
    public function parse($file)
    {
        // Preprocessing
        $file = $this->_preProcess($file);

        // exception for the case that the file to read does not exist
        if (!$this->_resource_exists($file)) {
            throw new Doozr_Exception(
                'Could not parse file. The file: "'.$file.'" does not exist. '.
                'Current operation failed. Check permissions too.'
            );
        }

        // parse (interpret) php-code and return result
        return $this->_parse($file);
    }

    /**
     * Removes a resource from filesystem
     *
     * This method is intend to remove a resource from filesystem.
     *
     * @param string $resource  The resource to remove
     * @param bool   $recursive TRUE to remove content (if directory) recursive
     * @param string $context   The context
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if the resource was removed, FALSE otherwise.
     * @access public
     * @throws Doozr_Exception
     */
    public function delete($resource, $recursive = false, $context = null)
    {
        // Preprocessing
        $resource = $this->_preProcess($resource);

        if ($this->_is_dir($resource)) {
            throw new Doozr_Exception(
                'Directory could not be deleted. Deletion of directories currently not supported.'
            );
        } else {
            return $this->_unlink($resource);
        }
    }

    /**
     * Removes a resource from filesystem
     *
     * This method is intend to remove a resource from filesystem.
     *
     * @param string $resource  The resource to remove
     * @param bool   $recursive TRUE to remove content (if directory) recursive
     * @param string $context   The context
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if the resource was removed, FALSE otherwise.
     * @access public
     */
    public function unlink($resource, $recursive = false, $context = null)
    {
        return $this->delete($resource, $recursive, $context);
    }

    /**
     * Indexes a path and return contents as indexed array.
     *
     * @param string $path The path to index.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return RecursiveIteratorIterator The result as iterator.
     * @access public
     */
    public function index($path)
    {
        return new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST
        );
    }

    /**
     * Tells whether the given resource is writable
     *
     * This method is intend to check if a given resource is writable.
     *
     * @param string $resource The resource to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if the resource is writable, FALSE otherwise.
     * @access public
     */
    public function is_writable($resource)
    {
        // Preprocessing
        $resource = $this->_preProcess($resource);

        return $this->_is_writable($resource);
    }

    /**
     * Tells whether the resource is readable.
     *
     * @param string $resource The resource to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if the resource is readable, FALSE otherwise.
     * @access public
     */
    public function readable($resource)
    {
        // Preprocessing
        $resource = $this->_preProcess($resource);

        return $this->_is_readable($resource);
    }

    /**
     * Tells whether the given resource is a regular file
     *
     * This method is intend to check if a given resource is a file.
     *
     * @param string $resource The resource to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if the file exists and is a regular file, FALSE otherwise.
     * @access public
     */
    public function is_file($resource)
    {
        // Preprocessing - clean the resource given (part of fs-protection)
        $resource = $this->_preProcess($resource);

        // return result
        return $this->_is_file($resource);
    }

    /**
     * Tells whether the given resource is a regular directory
     *
     * This method is intend to check if a given resource is a directory.
     *
     * @param string $resource The resource to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if the file exists and is a directory, FALSE otherwise.
     * @access public
     */
    public function is_dir($resource)
    {
        // Preprocessing - clean the resource given (part of fs-protection)
        $resource = $this->_preProcess($resource);

        // return result
        return $this->_is_dir($resource);
    }

    /**
     * Returns the status of "is-virtual" of this instance
     *
     * returns the status of "is-virtual" of this instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool True if instance works on virtual filesystem, otherwise false
     * @access public
     */
    public function isVirtual()
    {
        return $this->isVirtual;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Corrects slashed in windows direction to make them compatible with vfsStream
     *
     * This method is intend as helper for making win-styled slashes vfsStream compatible.
     *
     * @param string $resource The resource (path or file) to correct
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The corrected resource (input)
     * @access protected
     */
    protected function _vfsSlashes($resource)
    {
        return str_replace('\\', '/', $resource);
    }

    /**
     * Prepares a resource for virtual filesystem
     *
     * This method is intend to prepare a resource for virtual filesystem access.
     *
     * @param string $resource The resource to prepare
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The prepared resource
     * @access protected
     */
    protected function _prepareForVfs($resource)
    {
        // get folder from file
        $path = dirname($resource);

        // fix slashed for vfs
        $path = $this->_vfsSlashes($path);

        // and store as root for virtual-filesystem
        if ($this->vfsLastPath != $path) {
            $this->vfs->setup($path);
            $this->vfsLastPath = $path;
        }

        // wrap file
        return $this->vfs->url($resource);
    }

    /**
     * Stores information about the current resource
     *
     * This method is intend to store information about the current resource. Complete or partially.
     *
     * @param mixed  $information The information to store
     * @param string $part        Set if a single part should be stored, keep empty for storing complete array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setCurrentResourceInformation($information, $part = null)
    {
        if ($part) {
            $this->currentResourceInformation[$part] = $information;
        } else {
            $this->currentResourceInformation = $information;
        }

    }

    /**
     * Returns information about the current resource
     *
     * This method is intend to return information about the current resource. Complete or partially.
     *
     * @param string $part Set if a single part should be stored, keep empty for returning complete array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Single information or array
     * @access protected
     */
    protected function getCurrentResourceInformation($part = null)
    {
        if ($part) {
            return $this->currentResourceInformation[$part];
        } else {
            return $this->currentResourceInformation;
        }
    }

    /**
     * Stores information about a resource
     *
     * This method is intend to return information about a resource. Complete or partially.
     *
     * @param string $uid         The uid of the resource to store information about
     * @param array  $information The information to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setResourceInformation($uid, array $information)
    {
        $this->resources[$uid] = $information;
    }

    /**
     * Returns information about a resource
     *
     * This method is intend to return information about a resource. Complete or partially.
     *
     * @param string $uid  The uid of the resource to store information about
     * @param string $part Set if a single part should be stored, keep empty for returning complete array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Single information or array
     * @access protected
     */
    protected function getResourceInformation($uid, $part = null)
    {
        if ($part) {
            return isset($this->resources[$uid][$part]) ? $this->resources[$uid][$part] : false;
        } else {
            return $this->resources[$uid];
        }
    }

    /**
     * Returns a file-handle for requested runtimeEnvironment
     *
     * This method is intend to return a file-handle for requested runtimeEnvironment
     *
     * @param string  $file File to get handle on
     * @param string  $mode Mode to open file
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Handle on given file
     * @access protected
     */
    protected function _getFileHandle($file, $mode = self::FILE_MODE_READ)
    {
        // get unique Id by file
        $uid = $this->_getUid($file);

        // check if any usable handle exists
        if (!isset($this->fileHandle[$uid]) || $this->fileHandle[$uid]['runtimeEnvironment'] != $mode) {
            // create handle
            $this->fileHandle[$uid] = array(
                'runtimeEnvironment'   => $mode,
                'handle' => $this->_fopen(
                    $file,
                    $mode
                )
            );
        }

        // return the correct handle
        return $this->fileHandle[$uid]['handle'];
    }

    /**
     * returns content of a PHP-file interpreted
     *
     * This method is intend to return content of a PHP-file interpreted by using ob_-methods.
     *
     * @param string $file The file to read
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The (interpreted) content of the file
     * @access protected
     */
    protected function _getFileContentFromBuffer($file)
    {
        // start buffering
        ob_start();

        // simply include file (we get the content later ...)
        include $file;

        // get content from buffer (ob)
        $content = ob_get_contents();

        // stop buffering
        ob_end_clean();

        return $content;
    }

    /**
     * Reads a file and parse the PHP in it
     *
     * This method is intend to read and parse a PHP-file.
     *
     * @param string $file The file to parse as php-file
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The content of the file
     * @access protected
     */
    protected function _parse($file)
    {
        return $this->_getFileContentFromBuffer($file);
    }

    /**
     * Appends content to a file under consideration of virtual-fs
     *
     * This method is intend to append content to a file under consideration of virtual-fs.
     * The method is the duplicate to the public method append() but without all the checks needed by public methods.
     *
     * @param string  $file        The file to write to
     * @param string  $data        The content to write
     * @param bool $create      TRUE to create file if it not exists, otherwise FALSE to do not
     * @param int $customFlags Custom flags as integer
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed INTEGER bytes written on success, otherwise FALSE on error
     * @access protected
     */
    protected function _append($file, $data, $create = false, $customFlags = 0)
    {
        return $this->_file_put_contents(
            $file,
            $data,
            self::FILE_APPEND | $customFlags
        );
    }

    /**
     * Writes content to the beginning of a file under consideration of virtual-fs
     *
     * This method is intend to write content to a file under consideration of virtual-fs.
     *
     * @param string  $file        The file to write to
     * @param string  $data        The content to write
     * @param bool $create      TRUE to create file if it not exists, otherwise FALSE to do not
     * @param int $customFlags Custom flags as integer
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed INTEGER bytes written on success, otherwise FALSE on error
     * @access protected
     */
    protected function _rewrite($file, $data, $create = false, $customFlags = 0)
    {
        if ($create) {
            return $this->_file_put_contents(
                $file,
                $data,
                $customFlags
            );
        }

        return false;
    }

    /**
     * Basic processing of a resource
     *
     * This method is intend to retrieve basic information about a file and protects the filesystem
     *
     * @param string $resource The resource to process (can be either folder or file)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The clean and safe resource name
     * @access protected
     */
    protected function _preProcess($resource)
    {
        // clear file / directory stats
        clearstatcache();

        // calculate hash
        $uid = $this->_getUid($resource);

        // already processed in current stack?
        if (!isset($this->resources[$uid])) {
            // remove trailing trash ...
            $resource = trim($resource, "\t\r\n\0\x0B");

            // check for containing BAD-stuff
            $this->_nullByteCheck($resource);

            // store the information
            $this->resources[$uid] = array(
                'resource' => $resource,
                'uid'      => $uid,
                'safe'     => true
            );
        }

        // map data to currentResource
        $this->setCurrentResourceInformation($this->resources[$uid]);

        // return safe resource
        return $resource;
    }

    /**
     * Returns the Uid for a given resource
     *
     * This method is intend to calculate and return the Uid for a given resource.
     *
     * @param string $resource The resource to calculate Uid for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The Uid
     * @access protected
     */
    protected function _getUid($resource)
    {
        return crc32($resource);
    }

    /**
     * checks if a file or folder is writable by this class' write methods
     *
     * checks if a file or folder is writable by this class' write methods
     *
     * @param string $resource The resource to check if writable
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool True if resource is writable
     * @throws Doozr_Base_Exception_Generic
     * @access protected
     */
    protected function _is_resource_writable($resource)
    {
        // if resource does not exist -> we check the level before current level (..)
        // (can be either file or folder) for existence and it it's writable
        if (!$this->_resource_exists($resource)) {
            $realResource = dirname($resource);
        } else {
            $realResource = $resource;
        }

        // now check
        if (!$this->_is_writable($realResource)) {
            return false;
        }

        // file|folder is writable
        return true;
    }

    /**
     * protects the filesystem before NULL-Byte attacks
     *
     * @param string $resource The resource (path or file) to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @throws Doozr_Base_Exception_Generic
     * @access public
     */
    protected function _nullByteCheck($resource)
    {
        if (strpos($resource, self::$nullBytePattern) !== false) {
            throw new Doozr_Base_Exception_Generic(__CLASS__.'() -> NULL-Byte injection caught!');
        }
    }

    /**
     * unlink - virtual-fs supporting wrapper
     *
     * This method is intend to work as unlink - virtual-fs supporting wrapper.
     *
     * @param string $resource Path to the resource to unlink
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool Returns TRUE if the resource could be deleted, otherwise FALSE
     * @access protected
     */
    protected function _unlink($resource)
    {
        if ($this->isVirtual) {
            $resource = $this->_prepareForVfs($resource);
        }

        // return the result of operation (virtual or real)
        return unlink($resource);
    }

    /**
     * is_file - virtual-fs supporting wrapper
     *
     * This method is intend to work as is_file - virtual-fs supporting wrapper.
     *
     * @param string $resource Path to the resource to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool Returns TRUE if the resource is a file, otherwise FALSE
     * @access protected
     */
    protected function _is_file($resource)
    {
        if ($this->isVirtual) {
            $resource = $this->_prepareForVfs($resource);
        }

        // return the result of operation (virtual or real)
        return is_file($resource);
    }

    /**
     * is_dir - virtual-fs supporting wrapper
     *
     * This method is intend to work as is_dir - virtual-fs supporting wrapper.
     *
     * @param string $resource Path to the resource to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool Returns TRUE if the resource is a directory, otherwise FALSE
     * @access protected
     */
    protected function _is_dir($resource)
    {
        if ($this->isVirtual) {
            $resource = $this->_prepareForVfs($resource);
        }

        // return the result of operation (virtual or real)
        return is_dir($resource);
    }

    /**
     * file - virtual-fs supporting wrapper
     *
     * This method is intend to work as file - virtual-fs supporting wrapper.
     *
     * @param string $resource Path to the resource to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool Returns the file in an array. Upon failure, returns FALSE.
     * @access protected
     */
    protected function _file($resource)
    {
        if ($this->isVirtual) {
            $resource = $this->_prepareForVfs($resource);
        }

        // return the result of operation (virtual or real)
        return file($resource);
    }

    /**
     * fopen - virtual-fs supporting wrapper
     *
     * This method is intend to work as fopen - virtual-fs supporting wrapper.
     *
     * @param string $file             The file to open
     * @param int    $mode             The runtimeEnvironment to open file in
     * @param bool   $use_include_path TRUE to lookup in include path, otherwise FALSE to do not
     * @param mixed  $context          The context to open file in
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Handle on file if exists, otherwise FALSE (on failure ...)
     * @access protected
     */
    protected function _fopen($file, $mode = self::FILE_MODE_BINARY, $use_include_path = false, $context = null)
    {
        if ($this->isVirtual) {
            $file = $this->_prepareForVfs($file);
        }

        $context = (!$context) ? stream_context_get_default() : $context;

        return fopen($file, $mode, $use_include_path, $context);
    }

    /**
     * fread - virtual-fs supporting wrapper
     *
     * This method is intend to work as fread - virtual-fs supporting wrapper.
     *
     * @param mixed $handle The handle to read from
     * @param int   $length The number of bytes to read
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Returns the read string or FALSE on failure.
     * @access protected
     */
    protected function _fread($handle, $length = self::PHP_STREAM_COPY_ALL)
    {
        // return the result of operation (virtual or real)
        return fread($handle, $length);
    }

    /**
     * fwrite - virtual-fs supporting wrapper
     *
     * This method is intend to work as fwrite - virtual-fs supporting wrapper.
     *
     * @param mixed  $handle The handle to write to
     * @param string $data   The data to write as string
     * @param int    $length The number of bytes to write
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Returns the read string or FALSE on failure.
     * @access protected
     */
    protected function _fwrite($handle, $data, $length = self::PHP_STREAM_COPY_ALL)
    {
        // return the result of operation (virtual or real)
        return fwrite($handle, $data, $length);
    }

    /**
     * filesize - virtual-fs supporting wrapper
     *
     * This method is intend to work as filesize - virtual-fs supporting wrapper.
     *
     * @param string $file The file to return size of
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Returns the size of the file in bytes, or FALSE (and generates an error of level E_WARNING)
     * @access protected
     */
    protected function _filesize($file)
    {
        if ($this->isVirtual) {
            $file = $this->_prepareForVfs($file);
        }

        return filesize($file);
    }

    /**
     * file_exists - virtual-fs supporting wrapper
     *
     * This method is intend to work as file_exists - virtual-fs supporting wrapper.
     *
     * @param string $resource Path to the file or directory
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool Returns TRUE if the file or directory specified by file exists, otherwise FALSE
     * @access protected
     */
    protected function _resource_exists($resource)
    {
        if ($this->isVirtual) {
            $resource = $this->_prepareForVfs($resource);
        }

        // return the result of operation (virtual or real)
        return file_exists($resource);
    }

    /**
     * is_writable - virtual-fs supporting wrapper
     *
     * This method is intend to work as is_writable - virtual-fs supporting wrapper
     *
     * @param string $resource The resource being checked.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if the resource exists and is writable, otherwise FALSE
     * @access protected
     */
    protected function _is_writable($resource)
    {
        if ($this->isVirtual) {
            $resource = $this->_prepareForVfs($resource);
        }

        // return result
        return is_writable($resource);
    }

    /**
     * readable - virtual-fs supporting wrapper
     *
     * This method is intend to work as is_readale - virtual-fs supporting wrapper
     *
     * @param string $resource The resource being checked.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if the resource exists and is writable, otherwise FALSE
     * @access protected
     */
    protected function _is_readable($resource)
    {
        if ($this->isVirtual) {
            $resource = $this->_prepareForVfs($resource);
        }

        // return result
        return is_readable($resource);
    }

    /**
     * file_get_contents - virtual-fs supporting wrapper
     *
     * This method is intend to work as file_get_contents - virtual-fs supporting wrapper
     *
     * @param string  $file             Name of the file to read.
     * @param bool    $use_include_path TRUE to use include path, FALSE to do not
     * @param mixed   $context          A valid context resource created with stream_context_create().
     *                                  If you don't need to use a custom context, you can skip this parameter by NULL.
     * @param int     $offset           The offset where the reading starts on the original stream.
     *                                  Seeking (offset) is not supported with remote files. Attempting to seek on
     *                                  non-local files may work with small offsets, but this is unpredictable because
     *                                  it works on the buffered stream.
     * @param int     $maxlen           Maximum length of data read. The default is to read until end of file is
     *                                  reached. Note that this parameter is applied to the stream processed by the
     *                                  filters.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The function returns the read data or FALSE on failure.
     * @access protected
     * @link   http://de.php.net/manual/en/function.file-get-contents.php
     */
    protected function _file_get_contents(
        $file,
        $use_include_path = false,
        $context = null,
        $offset = -1,
        $maxlen = self::PHP_STREAM_COPY_ALL
    ) {
        if ($this->isVirtual) {
            $file = $this->_prepareForVfs($file);
        }

        return file_get_contents($file, $use_include_path, $context, $offset, $maxlen);
    }

    /**
     * file_put_contents - virtual-fs supporting wrapper
     *
     * This method is intend to work as file_put_contents - virtual-fs supporting wrapper
     *
     * @param string $file    Path to the file where to write the data.
     * @param mixed  $data    The data to write. Can be either a string, an array or a stream resource
     * @param int    $flags   The value of flags can be any combination joined with the binary OR (|) operator
     * @param mixed  $context A valid context resource created with stream_context_create()
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The function returns the number of bytes that were written to the file, or FALSE on failure
     * @access protected
     * @link   http://de.php.net/manual/en/function.file-put-contents.php
     */
    protected function _file_put_contents($file, $data, $flags = 0, $context = null)
    {
        if ($this->isVirtual) {
            $file = $this->_prepareForVfs($file);
        }

        // return the result of operation (virtual or real)
        return file_put_contents($file, $data, $flags, $context);
    }

    /**
     * reporting for not implemented methods
     *
     * This method is intend to report non implemented methods.
     *
     * @param string $methodSignature The method called
     * @param array $arguments The arguments passed to method call
     *
     * @throws Doozr_Exception
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __call($methodSignature, $arguments)
    {
        throw new Doozr_Exception(
            'Method: '.$methodSignature.' isn\'t implemented yet. '
        );
    }

    /**
     * Returns TRUE if the service is a singleton.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if service is singleton, otherwise FALSE
     * @access public
     */
    public function isSingleton() {
        // TODO: Implement isSingleton() method.
    }

    /**
     * Returns TRUE if the service is a multiple.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if service is multiple, otherwise FALSE
     * @access public
     */
    public function isMultiple() {
        // TODO: Implement isMultiple() method.
    }

    /**
     * Returns name of the service.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The name of the service.
     * @access public
     */
    public function getName() {
        // TODO: Implement getName() method.
    }
}
