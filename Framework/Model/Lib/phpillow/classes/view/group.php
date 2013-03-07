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
 * Wrapper for group views
 *
 * @package Core
 * @version $Revision: 159 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */
class phpillowGroupView extends phpillowView
{
    /**
     * View functions to be registered on the server
     *
     * @var array
     */
    protected $viewDefinitions = array(
        // Add view for all groups indexed by their name
        'group' => 'function( doc )
{
    if ( doc.type == "group" )
    {
        emit( doc.name, doc._id );
    }
}',
        // Fetch all rights of one user, which is defined by the groups a user
        // belongs to.
        'user_permissions' => 'function( doc )
{
    if ( doc.type == "group" )
    {
        for ( var i = 0; i < doc.users.length; ++i )
        {
            emit( doc.users[i], doc.permissions );
        }
    }
}',
        // Fetch all rights of one user, which is defined by the groups a user
        // belongs to.
        'user_permissions_reduced' => 'function( doc )
{
    if ( doc.type == "group" )
    {
        for ( var i = 0; i < doc.users.length; ++i )
        {
            for ( var j = 0; j < doc.permissions.length; ++j )
            {
                emit( doc.users[i], doc.permissions[j] );
            }
        }
    }
}',
    );

    /**
     * Reduce function for a view function.
     *
     * A reduce function may be used to aggregate / reduce the results
     * calculated by a view function. See the CouchDB documentation for more
     * results: @TODO: Not yet documented.
     *
     * Each view reduce function MUST have a view definition with the same
     * name, otherwise there is nothing to reduce.
     *
     * @var array
     */
    protected $viewReduces = array(
        'user_permissions_reduced' => 'function( keys, values )
{
    var permissions = [];
    for ( var i = 0; i < values.length; ++i )
    {
        if ( permissions.indexOf( values[i] ) == -1 )
        {
            permissions.push( values[i] );
        }
    }
    return permissions;
}',
    );

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
        return parent::createNew( $docType === null ? __CLASS__ : $docType );
    }

    /**
     * Get name of view
     *
     * Get name of view
     *
     * @return string
     */
    protected function getViewName()
    {
        return 'groups';
    }
}

