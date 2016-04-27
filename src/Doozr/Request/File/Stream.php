<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Request - File - Stream
 *
 * Stream.php - Doozr request file stream implementation.
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

require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Class.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Request/File/Stream/Interface.php';

use Psr\Http\Message\StreamInterface;

/**
 * Doozr - Request - File - Stream
 *
 * Doozr request file stream implementation.
 *
 * @category   Doozr
 * @package    Doozr_Request
 * @subpackage Doozr_Request_File
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 * @final
 */
final class Doozr_Request_File_Stream extends Doozr_Base_Class
    implements
    StreamInterface,
    Doozr_Request_File_Stream_Interface
{
    /**
     * The getMetaComponents og this instance.
     *
     * @var resource
     * @access private
     */
    private $handle;

    /**
     * The name of the filename
     *
     * @var string
     * @access private
     */
    private $filename;

    /**
     * The offset from 0.
     *
     * @var int
     * @access private
     */
    private $offset = 0;

    /**
     * Size of the stream.
     *
     * @var int
     * @access private
     */
    private $size;

    /**
     * Readable state of stream.
     * @var bool
     */
    private $readable = true;

    /**
     * Seekable state of stream.
     *
     * @var bool
     * @access private
     */
    private $seekable = true;

    /**
     * Writable state of stream.
     *
     * @var bool
     * @access private
     */
    private $writable = false;

    /**
     * Constructor.
     *
     * @param string $filename The name of the file.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access public
     */
    public function __construct($filename)
    {
        $this
            ->filename($filename)
            ->handle(
                fopen($filename, 'r')
            )
            ->size(
                filesize($filename)
            );
    }

    /*------------------------------------------------------------------------------------------------------------------
    | SETTER, GETTER, ISSER & HASSER
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for size.
     *
     * @param int $size The size to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * Fluent: Setter for size.
     *
     * @param int $size The size to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access private
     */
    private function size($size)
    {
        $this->setSize($size);

        return $this;
    }

    /**
     * Setter for offset.
     *
     * @param int $offset The offset to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * Fluent: Setter for offset.
     *
     * @param int $offset The offset to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access private
     */
    private function offset($offset)
    {
        $this->setOffset($offset);

        return $this;
    }

    /**
     * Getter for offset.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return int Offset
     * @access private
     */
    private function getOffset()
    {
        return $this->offset;
    }

    /**
     * Setter for filename.
     *
     * @param string $filename The value to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Fluent: Setter for filename.
     *
     * @param string $filename The value to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access private
     */
    private function filename($filename)
    {
        $this->setFilename($filename);

        return $this;
    }

    /**
     * Getter for filename.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The filename
     * @access private
     */
    private function getFilename()
    {
        return $this->filename;
    }

    /**
     * Setter for getMetaComponents.
     *
     * @param int $handle The value to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function setHandle($handle)
    {
        $this->handle = $handle;
    }

    /**
     * Fluent: Setter for getMetaComponents.
     *
     * @param resource $handle The value to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access private
     */
    private function handle($handle)
    {
        $this->setHandle($handle);

        return $this;
    }

    /**
     * Getter for getMetaComponents.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return resource The getMetaComponents of the resource
     * @access public
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * Setter for readable.
     *
     * @param bool $readable The value to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function setReadable($readable)
    {
        $this->readable = $readable;
    }

    /**
     * Fluent: Setter for readable.
     *
     * @param bool $readable The value to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access private
     */
    private function readable($readable)
    {
        $this->setReadable($readable);

        return $this;
    }

    /**
     * Getter for readable.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean Instance for chaining
     * @access private
     */
    private function getReadable()
    {
        return $this->readable;
    }

    /**
     * Setter for seekable.
     *
     * @param bool $seekable The value to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function setSeekable($seekable)
    {
        $this->seekable = $seekable;
    }

    /**
     * Fluent: Setter for seekable.
     *
     * @param bool $seekable The value to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access private
     */
    private function seekable($seekable)
    {
        $this->setSeekable($seekable);

        return $this;
    }

    /**
     * Getter for seekable.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if stream is seekable, otherwise FALSE if not
     * @access private
     */
    private function getSeekable()
    {
        return $this->seekable;
    }

    /**
     * Setter for writable.
     *
     * @param bool $writable The value to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function setWritable($writable)
    {
        $this->writable = $writable;
    }

    /**
     * Fluent: Setter for writable.
     *
     * @param bool $writable The value to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access private
     */
    private function writable($writable)
    {
        $this->setWritable($writable);

        return $this;
    }

    /**
     * Getter for writable.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if writable, otherwise FALSE
     * @access private
     */
    private function getWritable()
    {
        return $this->writable;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | FULFILL StreamInterface
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Closes the stream and any underlying resources.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function close()
    {
        fclose($this->getHandle());
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return resource Underlying PHP stream, if any
     * @access public
     */
    public function detach()
    {
        // Store for return value
        $handle = $this->handle;

        // Remove getMetaComponents internally
        unset(
            $this->handle
        );

        // Reset stored size, and flags
        $this
            ->size(null)
            ->readable(false)
            ->seekable(false)
            ->writable(false);

        // Return as interface requires!
        return $handle;
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return int Position of the file pointer
     * @access public
     * @throws \RuntimeException on error.
     */
    public function tell()
    {
        return $this->getOffset();
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if EOF, otherwise FALSE
     * @access public
     */
    public function eof()
    {
        return ($this->getOffset() === $this->getSize());
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if seekable, otherwise FALSE
     * @access public
     */
    public function isSeekable()
    {
        return $this->getSeekable();
    }

    /**
     * Seek to a position in the stream.
     *
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated based on the seek offset. Valid values
     *                    are identical to the built-in PHP $whence values for `fseek()`.
     *
     *                     SEEK_SET: Set position equal to offset bytes
     *                     SEEK_CUR: Set position to current location plus offset
     *                     SEEK_END: Set position to end-of-stream plus offset.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @throws \RuntimeException on failure.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        // Calculate real offset
        switch ($whence) {
            case SEEK_CUR:
                $offset = $offset + $this->getOffset();
                break;

            case SEEK_END:
                $offset = $this->getSize() + $this->getOffset();
                break;

            default:
            case SEEK_SET:
                // Intentionally left empty
                break;
        }

        fseek($this->getHandle(), $offset);

        // Store new offset for further actions ...
        $this->offset($offset);
    }

    /**
     * Get the size of the stream if known.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer Returns the size in bytes if known, or null if unknown.
     * @access public
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     * @throws \RuntimeException on failure.
     */
    public function rewind()
    {
        if (false === $this->isSeekable()) {
            throw new RuntimeException(
                'Stream is not seekable!'
            );
        }

        // Rewind to 0
        $this->seek(0);
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if writable, otherwise FALSE
     * @access public
     */
    public function isWritable()
    {
        return $this->getWritable();
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return int Returns the number of bytes written to the stream.
     * @access public
     * @throws \RuntimeException on failure.
     */
    public function write($string)
    {
        if (strlen($string) !== $bytesWritten = fwrite($this->getHandle(), $string)) {
            throw new RuntimeException(
                'Buffer could not be written to stream.'
            );
        }

        return $bytesWritten;
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if readable, otherwise FALSE
     * @access public
     */
    public function isReadable()
    {
        return $this->getReadable();
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return them. Fewer than $length bytes may be
     *                    returned if underlying stream call returns fewer bytes.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string Returns the data read from the stream, or an empty string if no bytes are available.
     * @access public
     * @throws \RuntimeException if an error occurs.
     */
    public function read($length)
    {
        return fread($this->getHandle(), $length);
    }

    /**
     * Returns the remaining contents in a string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The content as string
     * @access public
     * @throws \RuntimeException if unable to read or an error occurs while reading.
     */
    public function getContents()
    {
        return fread($this->getHandle(), $this->getSize() - $this->getOffset());
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @param string $key Specific metadata to retrieve.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array|mixed|null Returns an associative array if no key is provided. Returns a specific key value if a
     *                          key is provided and the value is found, or null if the key is not found.
     * @access public
     */
    public function getMetadata($key = null)
    {
        return stream_get_meta_data(
            $this->getHandle()
        );
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The content as string
     * @access public
     */
    public function __toString()
    {
        $this->rewind();
        echo $this->getContents();
    }
}
