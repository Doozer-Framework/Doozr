<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Response - State - Body.
 *
 * Body.php - Body stream wrapper.
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
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Class.php';

use Psr\Http\Message\StreamInterface;

/**
 * Doozr - Response - State - Body.
 *
 * Body stream wrapper.
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
class Doozr_Response_Body extends Doozr_Base_Class
    implements
    StreamInterface
{
    /**
     * The resource (PHP) this instance operates on.
     *
     * @var resource
     */
    protected $resource;

    /**
     * The stream resource.
     *
     * @var string|resource
     */
    protected $stream;

    /*------------------------------------------------------------------------------------------------------------------
    | STREAM INTERFACE
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param string $stream The stream (name) we will work on
     * @param string $mode   Mode with which to open stream
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Response_Body
     *
     * @throws InvalidArgumentException
     */
    public function __construct($stream = 'php://memory', $mode = 'r')
    {
        $this->stream = $stream;

        if (is_resource($stream)) {
            $this->resource = $stream;
        } elseif (is_string($stream)) {
            set_error_handler(
                function($errorNumber, $errorMessage) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Invalid file provided for stream; '.
                            'must be a valid path with valid permissions. Error: %s - %s',
                            $errorNumber,
                            $errorMessage
                        )
                    );
                },
                E_WARNING
            );
            $this->resource = fopen($stream, $mode);
            restore_error_handler();
        } else {
            throw new \InvalidArgumentException(
                'Invalid stream provided; must be a string stream identifier or resource'
            );
        }
    }

    /**
     * {@inheritdoc}
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __toString()
    {
        if (!$this->isReadable()) {
            return '';
        }

        try {
            $this->rewind();

            return $this->getContents();
        } catch (RuntimeException $e) {
            return '';
        }
    }

    /**
     * {@inheritdoc}
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function close()
    {
        if (!$this->resource) {
            return;
        }

        $resource = $this->detach();
        fclose($resource);
    }

    /**
     * {@inheritdoc}
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function detach()
    {
        $resource       = $this->resource;
        $this->resource = null;

        return $resource;
    }

    /**
     * Attach a new stream/resource to the instance.
     *
     * @param string|resource $resource
     * @param string          $mode
     *
     * @throws InvalidArgumentException for stream identifier that cannot be cast to a resource
     * @throws InvalidArgumentException for non-resource stream
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function attach($resource, $mode = 'r')
    {
        $error = null;
        if (!is_resource($resource) && is_string($resource)) {
            set_error_handler(function($e) use (&$error) {
                $error = $e;
            },
                E_WARNING);
            $resource = fopen($resource, $mode);
            restore_error_handler();
        }

        if ($error) {
            throw new InvalidArgumentException('Invalid stream reference provided');
        }

        if (!is_resource($resource)) {
            throw new InvalidArgumentException(
                'Invalid stream provided; must be a string stream identifier or resource'
            );
        }

        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function getSize()
    {
        if (null === $this->resource) {
            return;
        }

        $stats = fstat($this->resource);

        return $stats['size'];
    }

    /**
     * {@inheritdoc}
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function tell()
    {
        if (!$this->resource) {
            throw new RuntimeException('No resource available; cannot tell position');
        }

        $result = ftell($this->resource);
        if (!is_int($result)) {
            throw new RuntimeException('Error occurred during tell operation');
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function eof()
    {
        if (!$this->resource) {
            return true;
        }

        return feof($this->resource);
    }

    /**
     * {@inheritdoc}
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function isSeekable()
    {
        if (!$this->resource) {
            return false;
        }

        $meta = stream_get_meta_data($this->resource);

        return $meta['seekable'];
    }

    /**
     * {@inheritdoc}
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (!$this->resource) {
            throw new RuntimeException('No resource available; cannot seek position');
        }

        if (!$this->isSeekable()) {
            throw new RuntimeException('Stream is not seekable');
        }

        $result = fseek($this->resource, $offset, $whence);

        if (0 !== $result) {
            throw new RuntimeException('Error seeking within stream');
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function rewind()
    {
        return $this->seek(0);
    }

    /**
     * {@inheritdoc}
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function isWritable()
    {
        if (!$this->resource) {
            return false;
        }

        $meta = stream_get_meta_data($this->resource);

        return is_writable($meta['uri']);
    }

    /**
     * {@inheritdoc}
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function write($string)
    {
        if (!$this->resource) {
            throw new \RuntimeException('No resource available; cannot write');
        }

        $result = fwrite($this->resource, $string);

        if (false === $result) {
            throw new \RuntimeException('Error writing to stream');
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function isReadable()
    {
        if (!$this->resource) {
            return false;
        }

        $meta = stream_get_meta_data($this->resource);
        $mode = $meta['mode'];

        return strstr($mode, 'r') || strstr($mode, '+');
    }

    /**
     * {@inheritdoc}
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function read($length)
    {
        if (!$this->resource) {
            throw new RuntimeException('No resource available; cannot read');
        }

        if (!$this->isReadable()) {
            throw new RuntimeException('Stream is not readable');
        }

        $result = fread($this->resource, $length);

        if (false === $result) {
            throw new RuntimeException('Error reading stream');
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function getContents()
    {
        if (!$this->isReadable()) {
            return '';
        }

        $result = stream_get_contents($this->resource);
        if (false === $result) {
            throw new RuntimeException('Error reading from stream');
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function getMetadata($key = null)
    {
        if (null === $key) {
            return stream_get_meta_data($this->resource);
        }

        $metadata = stream_get_meta_data($this->resource);
        if (!array_key_exists($key, $metadata)) {
            return;
        }

        return $metadata[$key];
    }
}
