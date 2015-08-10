<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Request - File - Stream - Wrapper
 *
 * Wrapper.php - Wrapper for Doozr file upload streams.
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
 * @package    Doozr_Request
 * @subpackage Doozr_Request_File
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Class.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Request/File/Stream/Interface.php';

/**
 * Doozr - Request - File - Stream - Wrapper
 *
 * Wrapper for Doozr file upload streams.
 *
 * @category   Doozr
 * @package    Doozr_Request
 * @subpackage Doozr_Request_File
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 * @final
 */
final class Doozr_Request_File_Stream_Wrapper extends Doozr_Base_Class
{
    /**
     * The resource context.
     *
     * @var resource
     * @access public
     */
    public $context;

    /**
     * The resource stream.
     *
     * @var resource
     * @access private
     */
    private $stream;

    /**
     * The mode of the stream (e.g. r, r+, or w).
     *
     * @var string
     * @access private
     */
    private $mode;

    /**
     * Wraps a passed stream into an stream resource handable by PHP's internal functions.
     *
     * @param Doozr_Request_File_Stream_Interface $stream The stream to get handle from for wrapping
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return resource The wrapped resource
     * @access public
     * @static
     */
    public static function wrap(Doozr_Request_File_Stream_Interface $stream)
    {
        static $cache   = [];
        static $replace = ['_', '\\'];

        $handle      = $stream->getHandle();
        $calledClass = get_called_class();
        $protocol    = str_replace($replace, '-', $calledClass);

        // Register the stream wrapper decorator if needed. Cached - registering + checking wrappers is expensive.
        if (false === isset($cache[$protocol])) {
            self::registerProtocol($protocol, $calledClass);
            $cache[$protocol] = true;
        }

        $wrapper = fopen(
            $protocol.'://',
            stream_get_meta_data($handle)['mode'],
            null,
            stream_context_create(
                [DOOZR_NAMESPACE_FLAT => ['stream' => $handle]]
            )
        );

        if (false === $wrapper) {
            throw new RuntimeException(
                sprintf('Unable to wrap the stream with custom protocol: %s.', $protocol)
            );
        }

        return $wrapper;
    }

    /**
     * Registers the custom protocol so everything is available to PHP's native stream handlers.
     *
     * @param string $protocol The protocol
     * @param $class
     *
     * @access private
     */
    private static function registerProtocol($protocol, $class)
    {
        if (false === in_array($protocol, stream_get_wrappers())) {
            stream_wrapper_register($protocol, $class);
        }
    }

    /*------------------------------------------------------------------------------------------------------------------
    | FULFILL PHP file functionality (required by a custom protocol wrapper)
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * @param $path
     * @param $mode
     * @param $options
     * @param $opened_path
     *
     * @return bool
     */
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $options = stream_context_get_options($this->context);

        if (!isset($options[DOOZR_NAMESPACE_FLAT]['stream'])) {
            return false;
        }

        $this->mode   = $mode;
        $this->stream = $options[DOOZR_NAMESPACE_FLAT]['stream'];

        return true;
    }

    public function stream_close()
    {
        fclose($this->stream);
    }

    public function stream_read($count)
    {
        return fread($this->stream, $count);
    }

    public function stream_write($data)
    {
        return fwrite($this->stream, $data);
    }

    public function stream_tell()
    {
        return ftell($this->stream);
    }

    public function stream_eof()
    {
        return feof($this->stream);
    }

    public function stream_seek($offset, $whence)
    {
        return fseek($this->stream, $offset, $whence);
    }

    public function stream_stat()
    {
        return fstat($this->stream);
    }

    public function stream_flush()
    {
        return fflush($this->stream);
    }

    public function stream_lock($operation)
    {
        return flock($this->stream, $operation);
    }

    public function stream_truncate($new_size)
    {
        return ftruncate($this->stream, $new_size);
    }
}
