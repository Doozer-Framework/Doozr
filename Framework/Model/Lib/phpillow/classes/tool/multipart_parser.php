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
 * Parses MIME messages, especially multipart-messages
 *
 * @package Core
 * @version $Revision: 114 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */
class phpillowToolMultipartParser
{
    /**
     * Stream to read from
     *
     * @var resource
     */
    protected $stream;

    /**
     * Document properties
     *
     * @var array
     */
    protected $options;

    /**
     * Construct parser from input stream
     *
     * @param resource $stream
     * @return void
     */
    public function __construct( $stream )
    {
        $this->stream = $stream;

        $this->checkStream();
    }

    /**
     * Check stream
     *
     * Ensure given stream is a "multipart/mixed" mixed document, and read the
     * important document properties, like the boundary string, for further
     * processing.
     *
     * @return void
     */
    protected function checkStream()
    {
        // Find document content type
        while ( !feof( $this->stream ) )
        {
            $line = fgets( $this->stream );

            // Check if we found a new document definition
            if ( !preg_match( '(^Content-Type:\\s+(?P<type>[a-z*][a-z0-9_/*+.-]*)(;\s*(?P<options>.*))?$)', trim( $line ), $match ) )
            {
                continue;
            }

            $type    = $match['type'];
            $options = isset( $match['options'] ) ? preg_split( '(\s*;\s*)', $match['options'] ) : array();
            foreach ( $options as $option )
            {
                if ( preg_match( '(^(?P<key>[a-z-]+)=("?)(?P<value>.*?)\\2$)', $option, $match ) )
                {
                    $this->options[$match['key']] = $match['value'];
                }
            }

            if ( $type !== 'multipart/mixed' )
            {
                throw new phpillowMultipartParserException( 'This handler does only understand "multipart/mixed" documents.' );
            }

            break;
        }

        // Search for first starting boundary
        while ( !feof( $this->stream ) )
        {
            if ( trim( fgets( $this->stream ) ) === '--' . $this->options['boundary'] )
            {
                break;
            }
        }
    }

    /**
     * Parse a single document
     *
     * Parse a single document, and return an array with all headers and the
     * document body. If the parsed document is of the type "multipart/mixed",
     * an array with all parts is returned as the type.
     *
     * @param string $string
     * @return array
     */
    protected function parseDocument( $string )
    {
        $document = array();

        // Read document headers
        while ( preg_match( '(\\A(?P<header>[A-Za-z0-9-]+):\s+(?P<value>.*)$)Sm', $string, $match ) )
        {
            $document[$match['header']] = trim( $match['value'] );
            $string = substr( $string, strlen( $match[0] ) + 1 );
        }

        if ( strpos( $document['Content-Type'], 'multipart/mixed' ) === 0 )
        {
            // Rebuild full document
            $body = fopen( 'string://', 'w' );
            foreach ( $document as $key => $value )
            {
                fwrite( $body, "$key: $value\r\n" );
            }
            fwrite( $body, $string );
            fseek( $body, 0 );

            $document['body'] = array();
            $parser = new phpillowToolMultipartParser( $body );
            while ( ( $part = $parser->getDocument() ) !== false )
            {
                $document['body'][] = $part;
            }
        }
        else
        {
            $document['body'] = trim( $string );
        }

        return $document;
    }

    /**
     * Get document from stream
     *
     * Get the (next) document from the stream. Will return an array with the
     * document properties and their values (like Content-Type), as well as an
     * 'body' index, which contains the actual contents of the document.
     *
     * Returns false, if the stream has ended or no document definition could
     * be found.
     *
     * @return mixed
     */
    public function getDocument()
    {
        if ( feof( $this->stream ) )
        {
            return false;
        }

        $document = '';
        while ( ( ( $line = fgets( $this->stream ) ) !== false ) &&
                ( trim( $line ) !== '--' . $this->options['boundary'] ) &&
                ( trim( $line ) !== '--' . $this->options['boundary'] . '--' ) )
        {
            $document .= $line;
        }

        if ( trim( $document ) === '' )
        {
            return false;
        }

        return $this->parseDocument( ltrim( $document ) );
    }
}

