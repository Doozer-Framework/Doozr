<?php
/**
 * phpillow CouchDB backend
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
 * @version $Revision: 159 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */

/**
 * Basic couch DB connection handling class.
 *
 * This class uses a custom HTTP client, which may have more bugs then the
 * default PHP HTTP clients, but supports keep alive connections without any
 * extension dependencies.
 *
 * @package Core
 * @version $Revision: 159 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */
class phpillowCustomConnection extends phpillowConnection
{
    /**
     * Connection pointer for connections, once keep alive is working on the
     * CouchDb side.
     *
     * @var resource
     */
    protected $connection;

    /**
     * Create a new couch DB connection instance.
     *
     * Static method to create a new couch DB connection instance. This method
     * should be used to configure the connection for later use.
     *
     * The host and its port default to localhost:5984.
     *
     * @param string $host
     * @param int $port
     * @param string $username
     * @param string $password
     * @param string $called
     * @return void
     */
    public static function createInstance( $host = '127.0.0.1', $port = 5984, $username = null, $password = null, $called = "phpillowCustomConnection" )
    {
        parent::createInstance( $host, $port, $username, $password, $called );
    }

    /**
     * Check for server connection
     *
     * Checks if the connection already has been established, or tries to
     * establish the connection, if not done yet.
     *
     * @return void
     */
    protected function checkConnection()
    {
        // If the connection could not be established, fsockopen sadly does not
        // only return false (as documented), but also always issues a warning.
        if ( ( $this->connection === null ) &&
             ( ( $this->connection = @fsockopen( $this->options['ip'], $this->options['port'], $errno, $errstr ) ) === false ) )
        {
            // This is a bit hackisch...
            $this->connection = null;
            throw new phpillowConnectionException(
                "Could not connect to server at %ip:%port: '%errno: %error'",
                array(
                    'ip'    => $this->options['ip'],
                    'port'  => $this->options['port'],
                    'error' => $errstr,
                    'errno' => $errno,
                )
            );
        }
    }

    /**
     * Build a HTTP 1.1 request
     *
     * Build the HTTP 1.1 request headers from the given input.
     *
     * @param string $method
     * @param string $path
     * @param string $data
     * @return string
     */
    protected function buildRequest( $method, $path, $data )
    {
        // Create basic request headers
        $request = "$method $path HTTP/1.1\r\nHost: {$this->options['host']}\r\n";

        // Add basic auth if set
        if ( $this->options['username'] )
        {
            $request .= sprintf( "Authorization: Basic %s\r\n",
                base64_encode( $this->options['username'] . ':' . $this->options['password'] )
            );
        }

        // Set keep-alive header, which helps to keep to connection
        // initialization costs low, especially when the database server is not
        // available in the locale net.
        $request .= "Connection: " . ( $this->options['keep-alive'] ? 'Keep-Alive' : 'Close' ) . "\r\n";

        // Also add headers and request body if data should be sent to the
        // server. Otherwise just add the closing mark for the header section
        // of the request.
        if ( $data !== null )
        {
            $request .= "Content-type: application/json\r\n";
            $request .= "Content-Length: " . strlen( $data ) . "\r\n\r\n";
            $request .= "$data\r\n";
        }
        else
        {
            $request .= "\r\n";
        }

        return $request;
    }

    /**
     * Perform a request to the server and return the result
     *
     * Perform a request to the server and return the result converted into a
     * phpillowResponse object. If you do not expect a JSON structure, which
     * could be converted in such a response object, set the forth parameter to
     * true, and you get a response object returned, containing the raw body.
     *
     * @param string $method
     * @param string $path
     * @param string $data
     * @param bool $raw
     * @return phpillowResponse
     */
    protected function request( $method, $path, $data, $raw = false )
    {
        // Try establishing the connection to the server
        $this->checkConnection();

        // Send the build request to the server
        if ( fwrite( $this->connection, $request = $this->buildRequest( $method, $path, $data ) ) === false )
        {
            // Reestablish which seems to have been aborted
            //
            // The recursion in this method might be problematic if the
            // connection establishing mechanism does not correctly throw an
            // exception on failure.
            $this->connection = null;
            return $this->request( $method, $path, $data, $raw );
        }

        // If requested log request information to http log
        if ( $this->options['http-log'] !== false )
        {
            $fp = fopen( $this->options['http-log'], 'a' );
            fwrite( $fp, "\n\n" . $request );
        }

        // Read server response headers
        $rawHeaders = '';
        $headers = array(
            'connection' => ( $this->options['keep-alive'] ? 'Keep-Alive' : 'Close' ),
        );

        // Remove leading newlines, should not occur at all, actually.
        while ( ( ( $line = fgets( $this->connection ) ) !== false ) &&
                ( ( $lineContent = rtrim( $line ) ) === '' ) );

        // Throw exception, if connection has been aborted by the server, and
        // leave handling to the user for now.
        if ( $line === false )
        {
            // Reestablish which seems to have been aborted
            //
            // The recursion in this method might be problematic if the
            // connection establishing mechanism does not correctly throw an
            // exception on failure.
            //
            // An aborted connection seems to happen here on long running
            // requests, which cause a connection timeout at server side.
            $this->connection = null;
            return $this->request( $method, $path, $data, $raw );
        }

        do {
            // Also store raw headers for later logging
            $rawHeaders .= $lineContent . "\n";

            // Extract header values
            if ( preg_match( '(^HTTP/(?P<version>\d+\.\d+)\s+(?P<status>\d+))S', $lineContent, $match ) )
            {
                $headers['version'] = $match['version'];
                $headers['status']  = (int) $match['status'];
            }
            else
            {
                list( $key, $value ) = explode( ':', $lineContent, 2 );
                $headers[strtolower( $key )] = ltrim( $value );
            }
        }  while ( ( ( $line = fgets( $this->connection ) ) !== false ) &&
                   ( ( $lineContent = rtrim( $line ) ) !== '' ) );

        // Read response body
        $body = '';
        if ( !isset( $headers['transfer-encoding'] ) ||
             ( $headers['transfer-encoding'] !== 'chunked' ) )
        {
            // HTTP 1.1 supports chunked transfer encoding, if the according
            // header is not set, just read the specified amount of bytes.
            $bytesToRead = (int) ( isset( $headers['content-length'] ) ? $headers['content-length'] : 0 );

            // Read body only as specified by chunk sizes, everything else
            // are just footnotes, which are not relevant for us.
            while ( $bytesToRead > 0 )
            {
                $body .= $read = fgets( $this->connection, $bytesToRead + 1 );
                $bytesToRead -= strlen( $read );
            }
        }
        else
        {
            // When transfer-encoding=chunked has been specified in the
            // response headers, read all chunks and sum them up to the body,
            // until the server has finished. Ignore all additional HTTP
            // options after that.
            do {
                $line = rtrim( fgets( $this->connection ) );

                // Get bytes to read, with option appending comment
                if ( preg_match( '(^([0-9a-f]+)(?:;.*)?$)', $line, $match ) )
                {
                    $bytesToRead = hexdec( $match[1] );

                    // Read body only as specified by chunk sizes, everything else
                    // are just footnotes, which are not relevant for us.
                    $bytesLeft = $bytesToRead;
                    while ( $bytesLeft > 0 )
                    {
                        $body .= $read = fread( $this->connection, $bytesLeft + 2 );
                        $bytesLeft -= strlen( $read );
                    }
                }
            } while ( $bytesToRead > 0 );

            // Chop off \r\n from the end.
            $body = substr( $body, 0, -2 );
        }

        // Reset the connection if the server asks for it.
        if ( $headers['connection'] !== 'Keep-Alive' )
        {
            fclose( $this->connection );
            $this->connection = null;
        }

        // If requested log response information to http log
        if ( $this->options['http-log'] !== false )
        {
            fwrite( $fp, "\n" . $rawHeaders . "\n" . $body . "\n" );
            fclose( $fp );
        }

        // Handle some response state as special cases
        switch ( $headers['status'] )
        {
            case 301:
            case 302:
            case 303:
            case 307:
                $path = parse_url( $headers['location'], PHP_URL_PATH );
                return $this->request( 'GET', $path, $data, $raw );
        }

        // Create response object from couch db response
        return phpillowResponseFactory::parse( $headers, $body, $raw );
    }
}

