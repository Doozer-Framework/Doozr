<?php
/**
 * phpillow string stream
 *
 * This file is part of phpillow.
 *
 * phpillow is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Lesser General Public License as published by the Free
 * Software Foundation; version 3 of the License.
 *
 * phpillow is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for
 * more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with phpillow; if not, write to the Free Software Foundation, Inc., 51
 * Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Core
 * @version $Revision: 114 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */

/**
 * Stream wrapper for plain strings
 *
 * @package Core
 * @version $Revision: 114 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */
class phpillowToolStringStream
{
    /**
     * Current position inside the string
     * 
     * @var int
     */
	protected $position = 0;

    /**
     * String, wrapped by the stream 
     * 
     * @var string
     */
	protected $string;
	
    /**
     * Cached length of the string
     * 
     * @var int
     */
    protected $length;

    /**
     * Open stream
     * 
     * @param string $path 
     * @param string $mode 
     * @param mixed $options 
     * @param mixed $opened_path 
     * @return bool
     */
    public function stream_open( $path, $mode, $options, &$opened_path )
    {
        $this->string   = substr( $path, strpos( $path, '//' ) + 2 );
        $this->position = 0;
        $this->length   = strlen( $this->string );

        return true;
    }

    /**
     * Read from stream
     * 
     * @param int $count 
     * @return string
     */
    public function stream_read( $count )
    {
        $ret = substr( $this->string, $this->position, $count );
        $this->position += strlen( $ret );
        return $ret;
    }

    /**
     * Write to stream
     * 
     * @param string $data 
     * @return int
     */
    public function stream_write( $data )
    {
        $left            = substr( $this->string, 0, $this->position );
        $right           = substr( $this->string, $this->position + strlen( $data ) );
        $this->string    = $left . $data . $right;
        $this->position += $written = strlen( $data );
        $this->length    = strlen( $this->string );
        return $written;
    }

    /**
     * Tell current stream position
     * 
     * @return int
     */
    public function stream_tell()
    {
        return $this->position;
    }

    /**
     * Has the stream reached its end?
     * 
     * @return bool
     */
    public function stream_eof()
    {
        return $this->position >= $this->length;
    }

    /**
     * Seek to a defined position in the string
     * 
     * @param int $offset 
     * @param int $whence 
     * @return bool
     */
    public function stream_seek($offset, $whence)
    {
        switch ( $whence ) {
            case SEEK_SET:
                if ( ( $offset < $this->length ) &&
                     ( $offset >= 0 ) )
                {
                     $this->position = $offset;
                     return true;
                }
                else
                {
                     return false;
                }
                break;

            case SEEK_CUR:
                if ( $offset >= 0 )
                {
                     $this->position += $offset;
                     return true;
                }
                else
                {
                     return false;
                }
                break;

            case SEEK_END:
                if ( ( $this->length + $offset ) >= 0 )
                {
                     $this->position = $this->length + $offset;
                     return true;
                }
                else
                {
                     return false;
                }
                break;

            default:
                return false;
        }
    }

    /**
     * Returns information about the stream
     * 
     * @return void
     */
    public function stream_stat()
    {
        return array();
    }
}

