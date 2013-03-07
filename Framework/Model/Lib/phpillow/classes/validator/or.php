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
 * @version $Revision: 177 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */

/**
 * Validate object inputs
 *
 * @package Core
 * @version $Revision: 177 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */
class phpillowOrValidator extends phpillowValidator
{
    /**
     * Array of validators to be checked
     *
     * @var array
     */
    protected $validators;


    /**
     * Validator constructor
     *
     * Validator constructor to specify the validators that should be checked.
     *
     * @param array $validators
     * @return void
     */
    public function __construct( array $validators )
    {
        $this->validators = $validators;
    }

    /**
     * Validate input as object
     *
     * @param stdclass $input
     * @return StdClass
     */
    public function validate( $input )
    {
        $validatorClassNames = array();

        $validatorExceptions = array();

        foreach( $this->validators as $validator )
        {
            try {
                return $validator->validate( $input );
            } catch( phpillowValidationException $e ) {
                $validatorClassName                       = get_class( $validator );
                $validatorClassNames[]                    = $validatorClassName;
                $validatorExceptions[$validatorClassName] = $e;
            }
        }

        $exception = new phpillowValidationException(
            'Could not validate, as none of the given validators (%validators) validated the input.',
            array(
                'validators' => join( ',', $validatorClassNames )
            )
        );

        $exception->validatorExceptions = $validatorExceptions;

        throw $exception;
    }
}
