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
 * Basic couch DB connection handling class
 *
 * Connection handler using PHPs stream wrappers.
 *
 * @package Core
 * @version $Revision: 159 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */
class phpillowStreamConnection extends phpillowConnection
{
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
    public static function createInstance( $host = '127.0.0.1', $port = 5984, $username = null, $password = null, $called = "phpillowStreamConnection" )
    {
        parent::createInstance( $host, $port, $username, $password, $called );
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
     * @return phpillowResponse
     */
    protected function request( $method, $path, $data, $raw = false )
    {
        $basicAuth = '';
        if ( $this->options['username'] )
        {
            $basicAuth .= "{$this->options['username']}:{$this->options['password']}@";
        }

        $url = 'http://' . $basicAuth . $this->options['host']  . ':' . $this->options['port'] . $path;

        $httpFilePointer = @fopen(
            $url = 'http://' . $this->options['host']  . ':' . $this->options['port'] . $path, 'r', false,
            stream_context_create(
                array(
                    'http' => array(
                        'method'        => $method,
                        'content'       => $data,
                        'ignore_errors' => true,
                        'user_agent'    => 'PHPillow $Revision: 159 $',
                        'timeout'       => $this->options['timeout'],
                        'header'        => 'Content-type: application/json',
                    ),
                )
            )
        );

        // Check if connection has been established successfully
        if ( $httpFilePointer === false )
        {
            $error = error_get_last();
            throw new phpillowConnectionException(
                "Could not connect to server at %ip:%port: %error",
                array(
                    'ip'    => $this->options['ip'],
                    'port'  => $this->options['port'],
                    'error' => $error['message'],
                )
            );
        }

        // Read request body
        $body = '';
        while ( !feof( $httpFilePointer ) )
        {
            $body .= fgets( $httpFilePointer );
        }

        $metaData   = stream_get_meta_data( $httpFilePointer );
        // @todo: This seems to have changed in last CVS versions of PHP 5.3,
        // should be removable, once there is a next release of PHP 5.3
        $rawHeaders = isset( $metaData['wrapper_data']['headers'] ) ? $metaData['wrapper_data']['headers'] : $metaData['wrapper_data'];
        $headers    = array();

        foreach ( $rawHeaders as $lineContent )
        {
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
        }

        // If requested log response information to http log
        if ( $this->options['http-log'] !== false )
        {
            file_put_contents( $this->options['http-log'],
                sprintf( "Requested: %s\n\n%s\n\n%s\n\n",
                    $url,
                    implode( "\n", $rawHeaders ),
                    $body
                )
            );
        }

        // Create response object from couch db response
        return phpillowResponseFactory::parse( $headers, $body, $raw );
    }
}

