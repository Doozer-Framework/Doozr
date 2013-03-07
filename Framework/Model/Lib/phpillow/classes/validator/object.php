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
 * @version $Revision: 170 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */

/**
 * Validate object inputs
 *
 * @package Core
 * @version $Revision: 170 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */
class phpillowObjectValidator extends phpillowValidator
{
    /**
     * Required fields of the object.
     *
     * @var array
     */
    protected $requiredFields = null;

    /**
     * Optional fields for the object
     * 
     * @var array
     */
    protected $optionalFields = null;

    /**
     * Validator constructor
     *
     * Validator constructor to specify the required as well as optional fields 
     * of the object, which provide validators on their own.
     *
     * Both arguments are required to be an array of field => validator 
     * mappings or null to indicate there are no fields of the given type:
     *
     * <code>
     *   array( 
     *     'fieldname' => new phpillowNoValidator(),
     *     'another-fieldname' => new phpillowStringValidator(),
     *     'third-fieldname' => new phpillowObjectValidator( … ),
     *     …
     *   )
     * </code>
     *
     * ObjectValidators may contain fields with ObjectValidators themselves.
     *
     * @param array|null $requiredFields
     * @param array|null $optionalFields
     * @return void
     */
    public function __construct( array $requiredFields = null, array $optionalFields = null )
    {
        $this->requiredFields = $requiredFields === null
            ? array()
            : $requiredFields;

        $this->optionalFields = $optionalFields === null
            ? array()
            : $optionalFields;
    }

    /**
     * Validate input as object
     *
     * @param stdclass $input
     * @return StdClass
     */
    public function validate( $input )
    {
        if ( !is_object( $input ) || !( $input instanceof StdClass ) ) 
        {
            throw new phpillowValidationException( 
                'No object provided, where object is required.',
                array() 
            );
        }

        /*
         * Remember which of the required fields have been seen already. 
         * Initially no field has been seen 
         */
        $seenFields = array_map(
            function( $validator ) 
            {
                return false;
            },
            $this->requiredFields
        );

        $fieldValidators = array_merge(
            $this->requiredFields,
            $this->optionalFields
        );

        foreach( $input as $field => $value ) 
        {
            $seenFields[$field] = true;

            if ( !isset( $fieldValidators[$field] ) )
            {
                throw new phpillowValidationException( 
                    "The field '%field' is neither required nor optional and therefore forbidden",
                    array( 'field' => $field )
                );
            }

            // Recursively validate object fields
            $input->$field = $fieldValidators[$field]->validate( $value );
        }

        // Ensure all required fields have been seen
        $allSeen = array_reduce( 
            $seenFields,
            function( $accumulator, $value ) 
            {
                return $accumulator && $value;
            },
            true
        );

        if ( $allSeen !== true ) 
        {
            throw new phpillowValidationException( 
                "Not all required fields have been provided.",
                array()
            );
        }

        // Everything seems fine just return the provided input object
        return $input;
    }
}
