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
class phpillowDocumentArrayValidator extends phpillowDocumentValidator
{
    /**
     * Validate input as string
     *
     * @param mixed $input
     * @return string
     */
    public function validate( $input )
    {
        // We expect an array of documents, and if this isn't an array, we can
        // bail out immediately.
        if ( !is_array( $input ) )
        {
            throw new phpillowValidationException( 'Invalid document type provided.', array() );
        }

        // Reuse the document validator to validate the single documents in the
        // array.
        foreach ( $input as $key => $value )
        {
            $input[$key] = parent::validate( $value );
        }

        // If no exception has been thrown during the process, return the valid
        // array
        return $input;
    }
}

