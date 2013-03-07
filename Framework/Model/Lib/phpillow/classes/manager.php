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
 * @version $Revision: 94 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */

/**
 * Basic couch DB view and document manager / registry.
 *
 * @package Core
 * @version $Revision: 94 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */
final class phpillowManager
{
    /**
     * Initial mapping of view types to view classes.
     *
     * Mapping values may be added by the method setViewClass(). The array to
     * store the values looks like:
     * <code>
     *  array(
     *      'name' => 'class',
     *      ...
     *  )
     * </code>
     *
     * @var array
     */
    protected static $views = array(
    );

    /**
     * Initial mapping of document types to document classes.
     *
     * Mapping values may be added by the method setDocumentClass(). The array
     * to store the values looks like:
     * <code>
     *  array(
     *      'name' => 'class',
     *      ...
     *  )
     * </code>
     *
     * @var array
     */
    protected static $documents = array(
    );

    /**
     * Empty protected constructor
     *
     * We do not want this registry to be instanciated.
     * 
     * @ignore
     * @return void
     */
    protected function __construct()
    {
    }

    /**
     * Set view class
     *
     * Set a view class for a view type.
     * 
     * @param string $name 
     * @param string $class 
     * @return void
     */
    public static function setViewClass( $name, $class )
    {
        self::$views[$name] = $class;
    }

    /**
     * Return view
     *
     * Get a view object for the given view type. Throws a
     * phpillowNoSuchPropertyException if the view does not exist.
     * 
     * @param string $name 
     * @return phpillowView
     */
    public static function getView( $name )
    {
        // Check if a view with the given name exists.
        if ( !isset( self::$views[$name] ) )
        {
            throw new phpillowNoSuchPropertyException( $name );
        }

        // Instantiate and return view.
        $className = self::$views[$name];
        return new $className;
    }

    /**
     * Set document class
     *
     * Set a document class for a document type.
     * 
     * @param string $name 
     * @param string $class 
     * @return void
     */
    public static function setDocumentClass( $name, $class )
    {
        self::$documents[$name] = $class;
    }

    /**
     * Create new document
     *
     * Create a new document of the given type and return it. Throws a
     * phpillowNoSuchPropertyException if the document does not exist.
     * 
     * @param string $name 
     * @return phpillowDocument
     */
    public static function createDocument( $name )
    {
        // Check if a document with the given name exists.
        if ( !isset( self::$documents[$name] ) )
        {
            throw new phpillowNoSuchPropertyException( $name );
        }

        // Instantiate and return document.
        $className = self::$documents[$name];
        return call_user_func( array( $className, 'createNew' ) );
    }

    /**
     * Fetch document by ID
     *
     * Fetch the document of the given type with the given ID. Throws a
     * phpillowNoSuchPropertyException if the document does not exist.
     * 
     * @param string $name 
     * @param string $id 
     * @return phpillowDocument
     */
    public static function fetchDocument( $name, $id )
    {
        // Check if a document with the given name exists.
        if ( !isset( self::$documents[$name] ) )
        {
            throw new phpillowNoSuchPropertyException( $name );
        }

        // Instantiate and return document.
        $className = self::$documents[$name];
        $document  = new $className();
        return $document->fetchById( $id );
    }

    /**
     * Delete document by ID
     *
     * Delete the document of the given type with the given ID. Throws a
     * phpillowNoSuchPropertyException if the document does not exist.
     *
     * Deletion means, that all revisions, including the current one, are
     * removed.
     * 
     * @param string $name 
     * @param string $id 
     * @return void
     */
    public static function deleteDocument( $name, $id )
    {
        // Check if a document with the given name exists.
        if ( !isset( self::$documents[$name] ) )
        {
            throw new phpillowNoSuchPropertyException( $name );
        }

        $db = phpillowConnection::getInstance();
        $revision = $db->get( $db->getDatabase() . $id );

        // Only delete the current revision. This should delete all revisions
        // in the database except somebody updates the document between the get
        // and the delete request
        $db->delete( $db->getDatabase() . $id . '?rev=' . $revision->_rev );
    }
}

