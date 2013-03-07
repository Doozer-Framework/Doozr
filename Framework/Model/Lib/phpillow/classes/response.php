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
 * Response factory to create response objects from JSON results
 *
 * @package Core
 * @version $Revision: 159 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */
class phpillowResponseFactory
{
    /**
     * Parse a server response
     *
     * Parses a server response depending on the response body and the HTTP
     * status code.
     *
     * The method will either return a plain phpillowResponse object, when the
     * server returned a single document. If the server returned a set of
     * documents you will receive a phpillowResultSetResponse object, with a row
     * property to iterate over all documents returned by the server.
     *
     * For put and delete requests the server will just return a status,
     * whether the request was successful, which is represented by a
     * phpillowStatusResponse object.
     *
     * For all other cases most probably some error occurred, which is
     * transformed into a phpillowResponseErrorException, which will be thrown
     * by the parse method.
     *
     * If the third parameter raw is set to true, the body will not expected to
     * be some JSON structure, but just preserved as a raw string.
     *
     * @param array $headers
     * @param string $body
     * @param bool $raw
     * @return phpillowResponse
     */
    public static function parse( array $headers, $body, $raw = false )
    {
        $response = $raw === true ? $body : json_decode( $body, true );

        // To detect the type of the response from the couch DB server we use
        // the response status which indicates the return type.
        switch ( $headers['status'] )
        {
            case 200:
                // The HTTP status code 200 - OK indicates, that we got a document
                // or a set of documents as return value.
                //
                // To check whether we received a set of documents or a single
                // document we can check for the document properties _id or
                // _rev, which are always available for documents and are only
                // available for documents.
                if ( $raw === true )
                {
                    return new phpillowDataResponse( $headers['content-type'], $response );
                }
                elseif ( $body[0] === '[' )
                {
                    return new phpillowArrayResponse( $response );
                }
                elseif ( isset( $response['_id'] ) )
                {
                    return new phpillowResponse( $response );
                }
                elseif ( isset( $response['rows'] ) )
                {
                    return new phpillowResultSetResponse( $response );
                }

                // Otherwise fall back to a plain status response. No break.

            case 201:
            case 202:
                // The following status codes are given for status responses
                // depending on the request type - which does not matter here any
                // more.
                return new phpillowStatusResponse( $response );

            case 404:
                // The 404 and 409 (412) errors are using custom exceptions
                // extending the base error exception, because they are often
                // required to be handled in a special way by the application.
                //
                // Feel free to extend this for other errors as well.
                throw new phpillowResponseNotFoundErrorException( $response );

            case 409: // Conflict
            case 412: // Precondition Failed - we just consider this as a conflict.
                throw new phpillowResponseConflictErrorException( $response );

            default:
                // All other unhandled HTTP codes are for now handled as an error.
                // This may not be true, as lots of other status code may be used
                // for valid responses.
                throw new phpillowResponseErrorException( $headers['status'], $response );
        }
    }
}

