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
 * Validate text inputs
 *
 * @package Core
 * @version $Revision: 159 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */
class phpillowDocumentValidator extends phpillowValidator
{
    /**
     * Required class for the aggregated single document.
     *
     * @var string
     */
    protected $documentClass = false;

    /**
     * Validator constructor
     *
     * Validator constructor to specify the required class for the aggregated
     * document.
     *
     * @param mixed $class
     * @return void
     */
    public function __construct( $class = false )
    {
        $this->documentClass = $class;
    }

    /**
     * Validate input as string
     *
     * @param mixed $input
     * @return string
     */
    public function validate( $input )
    {
        // Check if passed input is an object and instance of phpillowDocument
        // at all, otherwise we can exit immediately
        if ( !is_object( $input ) ||
             !( $input instanceof phpillowDocument ) )
        {
            throw new phpillowValidationException( 'Invalid document type provided.', array() );
        }

        // If a specific document class is required, check this type is passed,
        // otherwise throw an exception.
        if ( ( $this->documentClass !== false ) &&
             !( $input instanceof $this->documentClass ) )
        {
            throw new phpillowValidationException( 'Invalid document type provided.', array() );
        }

        // Check if the document already has an ID assigned, otherwise it has
        // not yet been stored in the database and though is invalid.
        if ( $input->_id === null )
        {
            throw new phpillowValidationException( 'Invalid document type provided.', array() );
        }

        // If all above checks has been passed just return the document ID,
        // because that is all we need to store in database.
        return $input->_id;
    }
}

