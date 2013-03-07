<?php
/**
 * phpillow multipart parser
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
 * Writes a CouchDB dump into a multipart/mixed MIME file
 *
 * @package Core
 * @version $Revision: 114 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */
class phpillowToolMultipartWriter
{
    /**
     * Stream to read from
     * 
     * @var resource
     */
    protected $stream;

    /**
     * Currently used boundary
     * 
     * @var string
     */
    protected $boundary;

    /**
     * Already used boundaries
     *
     * @param array
     */
    protected static $boundaries = array();

    /**
     * Construct parser from input stream
     * 
     * @param resource $stream 
     * @return void
     */
    public function __construct( $stream )
    {
        $this->stream = $stream;

        // Write document header
        $this->boundary = $this->getBoundary();
        fwrite( $this->stream, "Content-Type: multipart/mixed; boundary=\"" . $this->boundary . "\"\r\n\r\n" );
    }

    /**
     * Write stream end
     * 
     * @return void
     */
    public function __destruct()
    {
        fwrite( $this->stream, "--" . $this->boundary . "--\r\n" );
    }

    /**
     * Get unique boudary
     *
     * Get a unique boundary string, which is not yet used by any wrapping
     * document and most probably not occurs in any of the embedded documents.
     * We cannot be entirely sure about that, but do not want waiting to define
     * the boundary until we loaded all documents into memory.
     * 
     * @return string
     */
    protected function getBoundary()
    {
        do {
            $boundary = '==' . md5( microtime() ) . '==';
        } while ( in_array( $boundary, self::$boundaries ) );

        return self::$boundaries[] = $boundary;
    }

    /**
     * Write a single document to the MIME file
     * 
     * @param array $document 
     * @return void
     */
    protected function writeSimpleDocument( array $document )
    {
        $body = json_encode( $document );

        fwrite( $this->stream, "Content-ID: " . $document['_id'] . "\r\n" );
        fwrite( $this->stream, "Content-Length: " . strlen( $body ) . "\r\n" );
        fwrite( $this->stream, "Content-Type: application/json\r\n" );
        fwrite( $this->stream, "\r\n" );
        fwrite( $this->stream, "$body\r\n" );
    }

    /**
     * Write a single document to the MIME file
     * 
     * @param array $document 
     * @return void
     */
    protected function writeMultipartDocument( array $document )
    {
        $body = json_encode( $document );

        $boundary = $this->getBoundary();
        fwrite( $this->stream, "Content-ID: " . $document['_id'] . "\r\n" );
        fwrite( $this->stream, "Content-Type: multipart/mixed; boundary=\"" . $boundary . "\"\r\n\r\n" );
 
        $attachments = $document['_attachments'];
        unset( $document['_attachments'] );

        // Write document first
        fwrite( $this->stream, "--" . $boundary . "\r\n" );
        $body = json_encode( $document );

        fwrite( $this->stream, "Content-Length: " . strlen( $body ) . "\r\n" );
        fwrite( $this->stream, "Content-Type: application/json\r\n" );
        fwrite( $this->stream, "\r\n" );
        fwrite( $this->stream, "$body\r\n" );

        // Write all attachments
        foreach ( $attachments as $name => $attachment )
        {
            fwrite( $this->stream, "--" . $boundary . "\r\n" );
            $body = $attachment['data'];

            fwrite( $this->stream, "Content-ID: " . $name . "\r\n" );
            fwrite( $this->stream, "Content-Length: " . strlen( $body ) . "\r\n" );
            fwrite( $this->stream, "Content-Type: " . $attachment['content_type'] . "\r\n" );
            fwrite( $this->stream, "\r\n" );
            fwrite( $this->stream, "$body\r\n" );
        }

        // End of multipart/mixed data
        fwrite( $this->stream, "--" . $boundary . "--\r\n" );
    }

    /**
     * Write document to stream
     *
     * Write a single document to the stream. Can create multipart messages, if
     * the document contains attachments.
     * 
     * @param array $document
     * @return void
     */
    public function writeDocument( array $document )
    {
        fwrite( $this->stream, "--" . $this->boundary . "\r\n" );
        if ( isset( $document['_attachments'] ) )
        {
            $this->writeMultipartDocument( $document );
        }
        else
        {
            $this->writeSimpleDocument( $document );
        }
    }
}

