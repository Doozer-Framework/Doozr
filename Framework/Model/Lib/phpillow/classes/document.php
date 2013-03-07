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
 * @version $Revision: 183 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */

/**
 * Basic abstract document
 *
 * @package Core
 * @version $Revision: 183 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */
abstract class phpillowDocument
{
    /**
     * Object storing all the document properties as public attributes. This
     * way it is easy to serialize using json_encode.
     *
     * @var StdClass
     */
    protected $storage;

    /**
     * Properties with they type and value validators
     *
     *  array(
     *      ...,
     *      email => new phpillowMailValidator( ... ),
     *      ...
     *  )
     *
     * @var array
     */
    protected $properties = array();

    /**
     * List of required properties. For each required property, which is not
     * set, a validation exception will be thrown on save.
     *
     * @var array
     */
    protected $requiredProperties = array();

    /**
     * Document type, may be a string matching the regular expression:
     *  (^[a-zA-Z0-9_]+$)
     *
     * @var string
     */
    protected static $type = '_default';

    /**
     * Indicates whether to keep old revisions of this document or not.
     *
     * @var bool
     */
    protected $versioned = true;

    /**
     * Flag, indicating if current document has already been modified
     *
     * @var bool
     */
    protected $modified = false;

    /**
     * Flag, indicating if current document is a new one.
     *
     * @var bool
     */
    protected $newDocument = true;

    /**
     * List of special properties, which are available beside the document
     * specific properties.
     *
     * @var array
     */
    protected static $specialProperties = array(
        '_id',
        '_rev',
        '_attachments',
        'type',
        'revisions',
    );

    /**
     * List of new attachments to the document.
     *
     * @var array
     */
    protected $newAttachments = array();

    /**
     * The phpillowConnection to be used by this document
     *
     * Set to null if you want to use phpillowConnection::getInstance()
     *
     * @var phpillowConnection
     */
    protected $connection = null;

    /**
     * The database to be used by this document
     *
     * Set to null if you want to use phpillowConnection::getDatabase()
     *
     * @var string
     */
    protected $database = null;

    /**
     * Set this before calling static functions.
     *
     * @var string
     */
    public static $docType = null;

    /**
     * Construct new document
     *
     * Construct new document
     *
     * @return void
     */
    public function __construct()
    {
        $this->storage = new StdClass();
        $this->storage->revisions = array();
        $this->storage->_id = null;
        $this->storage->_attachments = array();

        // Set all defined properties to null on construct
        foreach ( $this->properties as $property => $v )
        {
            $this->storage->$property = null;
        }

        // Also store document type in document
        $this->storage->type = $this->getType();
    }

    /**
     * Get document property
     *
     * Get property from document
     *
     * @param string $property
     * @return mixed
     */
    public function __get( $property )
    {
        // Check if property exists as a custom document property
        if ( isset( $this->properties[$property] ) )
        {
            return $this->storage->$property;
        }

        // Check if the requested property is one of the special properties,
        // which are available for all documents
        if ( in_array( $property, self::$specialProperties ) )
        {
            return $this->storage->$property;
        }

        // If none of the above checks passed, the request is invalid.
        throw new phpillowNoSuchPropertyException( $property );
    }

    /**
     * Set a property value
     *
     * Set a property value, which will be validated using the assigned
     * validator. Setting a property will mark the document as modified, so
     * that you know when to store the object.
     *
     * @param string $property
     * @param mixed $value
     * @return void
     */
    public function __set( $property, $value )
    {
        // Check if property exists at all
        if ( !isset( $this->properties[$property] ) )
        {
            throw new phpillowNoSuchPropertyException( $property );
        }

        // Check if the passed value meets the property validation, and perform
        // necessary transformation, like typecasts, or similar.
        //
        // If the value could not be fixed, this may throw an exception.
        $value = $this->properties[$property]->validate( $value );

        // Store value in storage object and mark document modified
        $this->storage->$property = $value;
        $this->modified = true;
    }

    /**
     * Check if document property is set
     *
     * Check if document property is set
     *
     * @param string $property
     * @return boolean
     */
    public function __isset( $property )
    {
        // Check if property exists as a custom document property
        if ( array_key_exists( $property, $this->properties ) ||
             in_array( $property, self::$specialProperties ) )
        {
            return true;
        }

        // If none of the above checks passed, the request is invalid.
        return false;
    }

    /**
     * Set values from a response object
     *
     * Set values of the document from the response object, if they are
     * available in there.
     *
     * @param phpillowResponse $response
     * @return void
     */
    protected function fromResponse( phpillowResponse $response )
    {
        // Set all document property values from response, if available in the
        // response.
        //
        // Also fill a revision object with the set attributes, so that the
        // current revision is also available in history, and it is stored,
        // when the object is modified and stored again.
        $revision = new StdClass();
        $revision->_date = time();
        foreach ( $this->properties as $property => $v )
        {
            if ( isset( $response->$property ) )
            {
                $this->storage->$property = $response->$property;
                $revision->$property = $response->$property;
            }
        }

        // Set special properties from response object
        $this->storage->_rev = $response->_rev;
        $this->storage->_id  = $response->_id;

        // Set attachments array, if the response object contains attachments.
        if ( isset( $response->_attachments ) )
        {
            $this->storage->_attachments = $response->_attachments;
        }

        // Check if the source document already contains a revision history and
        // store it in this case in the document object, if the object should
        // be versioned at all.
        if ( $this->versioned )
        {
            if ( isset( $response->revisions ) )
            {
                $this->storage->revisions = $response->revisions;
            }

            // Add current revision to revision history
            $this->storage->revisions[] = (array) $revision;
        }

        // Document freshly loaded, so it is not modified, and not a new
        // document...
        $this->modified = false;
        $this->newDocument = false;
    }

    /**
     * Get document ID from object ID
     *
     * Composes the document ID out of the document type and the generated ID
     * for the current document.
     *
     * If null is provided as an ID, we keep this value and do not construct
     * something else, to let the server autogenerate some ID.
     *
     * @param string $type
     * @param mixed $id
     * @return mixed
     */
    protected function getDocumentId( $type, $id )
    {
        return ( $id === null ? null : $type . '-' . $id );
    }

    /**
     * Get document by ID
     *
     * Get document by ID and return a document object instance for the fetch
     * document.
     *
     * @param string $id
     * @return phpillowDocument
     */
    public function fetchById( $id )
    {
        // If a fetch is called with an empty ID, we throw an exception, as we
        // would get database statistics otherwise, and the following error may
        // be hard to debug.
        if ( empty( $id ) )
        {
            throw new phpillowResponseNotFoundErrorException( array(
                'error'  => 'not_found',
                'reason' => 'No document ID specified.',
            ) );
        }

        // Fetch object from database
        $db = $this->getConnection();
        $response = $db->get(
            $this->getDatabase() . urlencode( $id )
        );

        // Check if type of response matches type of class
        $this->checkTypeOfResponse( $response );

        // Create document contents from fetched object
        $this->fromResponse( $response );

        return $this;
    }

    /**
     * Verifies that the fetched document is of the given type
     *
     * @param phpillowResponse $response
     * @return void
     */
    public function checkTypeOfResponse( phpillowResponse $response )
    {
        if ( $response->type != $this->getType() )
        {
            throw new phpillowResponseNotFoundErrorException(
                array(
                     'error'  => 'mismatch',
                     'reason' => 'Type does not match: ' . $response->type . ' != ' . $this->getType(),
                )
            );
        }
    }

    /**
     * Create a new instance of the document class
     *
     * Create a new instance of the statically called document class.
     * Implementing this method should only be required when using PHP 5.2 and
     * lower, otherwise the class can be determined using LSB.
     *
     * Do not pass a parameter to this method, this is only used to maintain
     * the called class information for PHP 5.2 and lower.
     *
     * @param mixed $docType
     * @return phpillowDocument
     */
    public static function createNew( $docType = null )
    {
        if ( ( $docType === null ) &&
             function_exists( 'get_called_class' ) )
        {
            $docType = get_called_class();
        }
        elseif ( $docType === null )
        {
            throw new phpillowRuntimeException( 'Invalid docType provided to createNew.' );
        }

        return new $docType();
    }

    /**
     * Return document type name
     *
     * This method is required to be implemented to return the document type
     * for PHP versions lower than 5.3. When only using PHP 5.3 and higher you
     * might just implement a method which does "return static:$type" in a base
     * class.
     *
     * @return string
     */
    abstract protected function getType();

    /**
     * Get ID from document
     *
     * The ID normally should be calculated on some meaningful / unique
     * property for the current type of documents. The returned string should
     * not be too long and should not contain multibyte characters.
     *
     * You can return null instead of an ID string, to trigger the ID
     * autogeneration.
     *
     * @return mixed
     */
    abstract protected function generateId();

    /**
     * Check if all requirements are met
     *
     * Checks if all required properties has been set. Returns an array with
     * the properties, which are required but not set, or true if all
     * requirements are fulfilled.
     *
     * @return mixed
     */
    public function checkRequirements()
    {
        // Iterate over properties and check if they are set and not null
        $errors = array();
        foreach ( $this->requiredProperties as $property )
        {
            if ( !isset( $this->storage->$property ) ||
                 ( $this->storage->$property === null ) )
            {
                $errors[] = $property;
            }
        }

        // If error array is still empty all requirements are met
        if ( $errors === array() )
        {
            return true;
        }

        // Otherwise return the array with errors
        return $errors;
    }

    /**
     * Save the document
     *
     * If thew document has not been modified the method will immediately exit
     * and return false. If the document has been been modified, the modified
     * document will be stored in the database, keeping all the old revision
     * intact and return true on success.
     *
     * On successful creation the (generated) ID will be returned.
     *
     * @return string
     */
    public function save()
    {
        // Get document type
        $type = $this->getType();

        // Ensure all requirements are checked, otherwise bail out with a
        // runtime exception.
        if ( $this->checkRequirements() !== true )
        {
            throw new phpillowRuntimeException(
                'Requirements not checked before storing the document.'
            );
        }

        // Check if we need to store the stuff at all
        if ( ( $this->modified === false ) &&
             ( $this->newDocument !== true ) )
        {
            return false;
        }

        // Generate a new ID, if this is a new document, otherwise reuse the
        // existing document ID.
        if ( $this->newDocument === true )
        {
            $this->storage->_id = $this->getDocumentId( $type, $this->generateId() );
        }

        // Do not send an attachment array, if there aren't any attachments
        if ( !isset( $this->storage->_attachments ) ||
             !count( $this->storage->_attachments ) )
        {
            unset( $this->storage->_attachments );
        }

        // If the document ID is null, the server should autogenerate some ID,
        // but for this we need to use a different request method.
        $db = $this->getConnection();
        if ( $this->storage->_id === null )
        {
            // Store document in database
            unset( $this->storage->_id );
            $response = $db->post(
                $this->getDatabase(),
                json_encode( $this->storage )
            );
        }
        else
        {
            // Store document in database
            $response = $db->put(
                $this->getDatabase() . urlencode( $this->_id ),
                json_encode( $this->storage )
            );
        }

        $this->storage->_rev = $response->rev;

        // Restore the __attachments array if it has been removed before
        if ( !isset( $this->storage->_attachments ) )
        {
            $this->storage->_attachments = array();
        }

        // This document is no longer new
        $this->newDocument = false;

        return $this->storage->_id = $response->id;
    }

    /**
     * Deletes the current document
     *
     * Tries to delete the current document from the database. Might throw a
     * conflict exception in case the document has been modified since the last
     * fetch.
     *
     * @return void
     */
    public function delete()
    {
        $db = $this->getConnection();
        return $db->delete(
            $this->getDatabase() . urlencode( $this->_id ) . '?rev=' . $this->_rev
        );
    }

    /**
     * Get ID string from arbitrary string
     *
     * To calculate an ID string from an phpillowrary string, first iconvs
     * transliteration abilities are used, and after that all, but common ID
     * characters, are replaced by the given replace string, which defaults to
     * _.
     *
     * @param string $string
     * @param string $replace
     * @return string
     */
    protected function stringToId( $string, $replace = '_' )
    {
        // First translit string to ASCII, as this characters are most probably
        // supported everywhere
        $string = iconv( 'UTF-8', 'ASCII//TRANSLIT', $string );

        // And then still replace any obscure characters by _ to ensure nothing
        // "bad" happens with this string.
        $string = preg_replace( '([^A-Za-z0-9.-]+)', $replace, $string );

        // Additionally we convert the string to lowercase, so that we get case
        // insensitive fetching
        return strtolower( $string );
    }

    /**
     * Attach file to document
     *
     * The file passed to the method will be attached to the document and
     * stored in the database. By default the filename of the provided file
     * will be ued as a name, but you may optionally specify a name as the
     * second parameter of the method.
     *
     * You may optionally specify a custom mime type as third parameter. If set
     * it will be used, but not verified, that it matches the actual file
     * contents. If left empty the mime type defaults to
     * 'application/octet-stream'.
     *
     * @param string $fileName
     * @param string $name
     * @param string $mimeType
     * @return void
     */
    public function attachFile( $fileName, $name = false, $mimeType = false )
    {
        $name = ( $name === false ? basename( $fileName ) : $name );

        $this->attachMemoryFile(
            file_get_contents( $fileName ),
            $name,
            $mimeType
        );
    }

    /**
     * Attach file from memory to document
     *
     * The data passed to the method will be attached to the document and
     * stored in the database.
     *
     * You need to specify a name to be used for storing the attachment data.
     *
     * You may optionally specify a custom mime type as third parameter. If set
     * it will be used, but not verified, that it matches the actual file
     * contents. If left empty the mime type defaults to
     * 'application/octet-stream'.
     *
     * @param string $data
     * @param string $name
     * @param string $mimeType
     * @return void
     */
    public function attachMemoryFile( $data, $name, $mimeType = false )
    {
        $this->storage->_attachments[$name] = array(
            'type'         => 'base64',
            'data'         => base64_encode( $data ),
            'content_type' => $mimeType === false ? 'application/octet-stream' : $mimeType,
        );
        $this->modified = true;
    }

    /**
     * Get file contents
     *
     * Get the contents of an attached file as a phpillowDataResponse.
     *
     * @param string $fileName
     * @return phpillowLazyFile
     */
    public function getFile( $fileName )
    {
        if ( !isset( $this->storage->_attachments[$fileName] ) )
        {
            throw new phpillowNoSuchPropertyException( $fileName );
        }

        $attachment = $this->storage->_attachments[$fileName];

        return new phpillowLazyFile(
            $this->getConnection(),
            $this->getDatabase() . urlencode( $this->_id ) . '/' . $fileName,
            $attachment['content_type'],
            $attachment['length']
        );
    }

    /**
     * Return used connection
     *
     * This should always used within a document instead of
     * phpillowConnection::getInstance()
     *
     * @return phpillowConnection
     */
    public function getConnection()
    {
       if ( $this->connection === null ) {
           return phpillowConnection::getInstance();
       }

       return $this->connection;
    }

    /**
     * Reconfigure the connection to be used by this document
     *
     * @param phpillowConnection $connection
     * @return void
     */
    public function setConnection( phpillowConnection $connection )
    {
        $this->connection = $connection;
    }

    /**
     * Return used database
     *
     * This should always used within a document instead of
     * phpillowConnection::getDatabase()
     *
     * @return phpillowConnection
     */
    public function getDatabase()
    {
       if ( $this->database === null ) {
           return phpillowConnection::getDatabase();
       }

       return $this->database;
    }

    /**
     * Reconfigure the database to be used by this document
     *
     * @param string $database
     * @return void
     */
    public function setDatabase( $database )
    {
        $this->database = '/' . $database . '/';
    }
}

