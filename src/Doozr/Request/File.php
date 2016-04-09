<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Request - File
 *
 * File.php - Doozr request file implementation for Web (HTTP) request(s).
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
 * @package    Doozr_Request
 * @subpackage Doozr_Request_File
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Class.php';

use Psr\Http\Message\UploadedFileInterface;

/**
 * Doozr - Request - File
 *
 * Doozr request file implementation for Web (HTTP) request(s).
 *
 * @category   Doozr
 * @package    Doozr_Request
 * @subpackage Doozr_Request_File
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
final class Doozr_Request_File extends Doozr_Base_Class implements UploadedFileInterface
{
    /**
     * Moved state of this file.
     *
     * @var bool
     * @access private
     */
    private $moved = false;

    /**
     * File input info.
     *
     * @var array
     * @access private
     */
    private $file;

    /**
     * Always up to date location of the file. Also after move(s).
     *
     * @var string
     * @access private
     */
    private $location;

    /**
     * Stream representation of the file.
     *
     * @var null|Doozr_Request_File_Stream
     * @access private
     */
    private $stream;

    /*------------------------------------------------------------------------------------------------------------------
    | INIT
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param array $file The file input array
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     */
    public function __construct(array $file)
    {
        $this
            ->file($file)
            ->location($file['tmp_name']);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | SETTER, GETTER, ISSER & HASSER
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for location.
     *
     * @param string $location The files current location
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * Fluent: Setter for location.
     *
     * @param string $location The files current location
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access private
     */
    private function location($location)
    {
        $this->setLocation($location);

        return $this;
    }

    /**
     * Getter for location.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Location of the file
     * @access private
     */
    private function getLocation()
    {
        return $this->location;
    }

    /**
     * Setter for file.
     *
     * @param array $file The files array from PHP
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function setFile(array $file)
    {
        $this->file = $file;
    }

    /**
     * Setter for file.
     *
     * @param array $file The files array from PHP
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access private
     */
    private function file(array $file)
    {
        $this->setFile($file);

        return $this;
    }

    /**
     * Getter for file.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array File array from PHP
     * @access private
     */
    private function getFile()
    {
        return $this->file;
    }

    /**
     * Setter for moved.
     *
     * @param boolean $moved The state of moved.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function setMoved($moved)
    {
        $this->moved = $moved;
    }

    /**
     * Fluent: Setter for moved.
     *
     * @param boolean $moved The state of moved.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access private
     */
    private function moved($moved)
    {
        $this->setMoved($moved);

        return $this;
    }

    /**
     * Getter for moved.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if file was moved, otherwise FALSE
     * @access private
     */
    private function getMoved()
    {
        return $this->moved;
    }

    /**
     * Proxy: Getter for moved.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if file was moved, otherwise FALSE
     * @access private
     */
    private function wasMoved()
    {
        return $this->getMoved();
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the files temporary location.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The temporary location
     * @access private
     */
    public function getTemporaryName()
    {
        return $this->getFile()['tmp_name'];
    }

    /*------------------------------------------------------------------------------------------------------------------
    | FULFILL UploadedFileInterface
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Move the uploaded file to a new location.
     *
     * Use this method as an alternative to move_uploaded_file(). This method is
     * guaranteed to work in both SAPI and non-SAPI environments.
     * Implementations must determine which environment they are in, and use the
     * appropriate method (move_uploaded_file(), rename(), or a stream
     * operation) to perform the operation.
     *
     * $targetPath may be an absolute path, or a relative path. If it is a
     * relative path, resolution should be the same as used by PHP's rename()
     * function.
     *
     * The original file or stream MUST be removed on completion.
     *
     * If this method is called more than once, any subsequent calls MUST raise
     * an exception.
     *
     * When used in an SAPI environment where $_FILES is populated, when writing
     * files via moveTo(), is_uploaded_file() and move_uploaded_file() SHOULD be
     * used to ensure permissions and upload status are verified correctly.
     *
     * If you wish to move to a stream, use getStream(), as SAPI operations
     * cannot guarantee writing to stream destinations.
     *
     * @see http://php.net/is_uploaded_file
     * @see http://php.net/move_uploaded_file
     * @param string $targetPath Path to which to move the uploaded file.
     * @throws \InvalidArgumentException if the $path specified is invalid.
     * @throws \RuntimeException on any error during the move operation, or on the second or subsequent call to the method.
     */
    public function moveTo($targetPath)
    {
        if (true === $this->wasMoved()) {
            throw new RuntimeException(
                sprintf('The file has been moved. Move no longer possible.')
            );
        }

        if (false === is_file($this->getTemporaryName()) || true === is_dir($this->getTemporaryName())) {
            throw new RuntimeException(
                sprintf('Invalid data received. Security prevents file operation.')
            );
        }

        if (false === rename($this->getTemporaryName(), $targetPath)) {
            throw new RuntimeException(
                sprintf('The file could not be moved.')
            );
        }

        // Ensure move was successful => then remove source
        if (true === file_exists($targetPath)) {
            unlink($this->getTemporaryName());
        }

        $this
            ->moved(true)
            ->location($targetPath);
    }

    /**
     * Retrieve the file size.
     *
     * Implementations SHOULD return the value stored in the "size" key of
     * the file in the $_FILES array if available, as PHP calculates this based
     * on the actual size transmitted.
     *
     * @return int|null The file size in bytes or null if unknown.
     */
    public function getSize()
    {
        return (true === isset($this->getFile()['size'])) ? $this->getFile()['size'] : filesize($this->getLocation());
    }

    /**
     * Retrieve the error associated with the uploaded file.
     *
     * The return value MUST be one of PHP's UPLOAD_ERR_XXX constants.
     *
     * If the file was uploaded successfully, this method MUST return UPLOAD_ERR_OK.
     *
     * Implementations SHOULD return the value stored in the "error" key of
     * the file in the $_FILES array.
     *
     * @see http://php.net/manual/en/features.file-upload.errors.php
     * @return int One of PHP's UPLOAD_ERR_XXX constants.
     */
    public function getError()
    {
        return $this->getFile()['error'];
    }

    /**
     * Retrieve the filename sent by the client.
     *
     * Do not trust the value returned by this method. A client could send
     * a malicious filename with the intention to corrupt or hack your
     * application.
     *
     * Implementations SHOULD return the value stored in the "name" key of
     * the file in the $_FILES array.
     *
     * @return string|null The filename sent by the client or null if none was provided.
     */
    public function getClientFilename()
    {
        return $this->getFile()['name'];
    }

    /**
     * Retrieve the media type sent by the client.
     *
     * Do not trust the value returned by this method. A client could send
     * a malicious media type with the intention to corrupt or hack your
     * application.
     *
     * Implementations SHOULD return the value stored in the "type" key of
     * the file in the $_FILES array.
     *
     * @return string|null The media type sent by the client or null if none was provided.
     */
    public function getClientMediaType()
    {
        return $this->getFile()['type'];
    }

    /**
     * Retrieve a stream representing the uploaded file.
     *
     * This method MUST return a StreamInterface instance, representing the uploaded file. The purpose of this method
     * is to allow utilizing native PHP stream functionality to manipulate the file upload, such as
     * stream_copy_to_stream() (though the result will need to be decorated in a native PHP stream wrapper to work
     * with such functions).
     *
     * If the moveTo() method has been called previously, this method MUST raise
     * an exception.
     *
     * @return resource Stream representation of the uploaded file.
     * @throws \RuntimeException in cases when no stream is available or can be created.
     */
    public function getStream()
    {
        if (true === $this->wasMoved()) {
            throw new RuntimeException(
                sprintf('The file has been moved. Stream no longer available.')
            );
        }

        if (null === $this->stream) {
            $this->stream = new Doozr_Request_File_Stream(
                $this->getTemporaryName()
            );
        }

        return Doozr_Request_File_Stream_Wrapper::wrap($this->stream);
    }
}
