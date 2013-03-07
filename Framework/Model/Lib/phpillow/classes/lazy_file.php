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
 * @version $Revision: 182 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */

/**
 * Class representing a file attached to a couchdb document.
 *
 * This class implements a lazy loading mechanism, which does only fetch the
 * binary data of an attachment if it is really accessed.
 *
 * Metadata like content-type and size will accessible, without fetching the
 * data.
 *
 * Once fetched the data will be cached inside the object to provide fast
 * access.
 *
 * @package Core
 * @version $Revision: 182 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 *
 * @property-read $data Binary data of the stored document
 * @property-read $contentType The mimetype of the stored attachment
 * @property-read $size Size in bytes of the stored attachment
 */
class phpillowLazyFile extends phpillowDataResponse
{
    /**
     * Storage for read-only properties
     *
     * @var array
     */
    protected $properties = array(
        'data'        => null,
        'contentType' => null,
        'length'      => null,
    );

    /**
     * Url which provides access to the couchdb attachment associated with this
     * lazy file loader.
     *
     * @var string
     */
    protected $url;

    /**
     * phpillowConnection to be used for retrieving the file data if requested.
     *
     * @var phpillowConnection
     */
    protected $connection;


    /**
     * Construct a new lazy File loader object using a given access url,
     * contentType and length in bytes.
     *
     * @param phpillowConnection $connection
     * @param mixed $url
     * @param mixed $contentType
     * @param mixed $length
     */
    public function __construct( phpillowConnection $connection, $url, $contentType, $length )
    {
        $this->connection = $connection;
        $this->url        = $url;

        $this->properties['contentType'] = $contentType;
        $this->properties['length']      = $length;
    }

    /**
     * Automagic getter for read-only properties
     *
     * @param mixed $key
     * @return mixed
     */
    public function __get( $key )
    {
        // Check if such an property exists at all
        if ( !array_key_exists( $key, $this->properties ) )
        {
            throw new phpillowNoSuchPropertyException( $key );
        }

        switch( $key )
        {
            case 'data':
                if ( $this->properties['data'] === null )
                {
                    $this->properties['data'] = $this->fetchData();
                }
            default:
                return $this->properties[$key];
        }
    }

    /**
     * Fetch the attachment data from the couchdb and return it
     *
     * @return string
     */
    protected function fetchData()
    {
        return $this->connection->get(
            $this->url,
            null,
            true
        )->data;
    }
}
