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
 * @version $Revision: 174 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */

/**
 * Validate uuid version 4 inputs
 *
 * @package Core
 * @version $Revision: 174 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */
class phpillowUuidValidator extends phpillowValidator
{
    /**
     * Validate input as uuid version 4
     *
     * @param string $input
     *
     * @throws phpillowValidationException if the given input does not conform 
     * to a uuid version 4
     *
     * @return string
     */
    public function validate( $input )
    {
        $uuidv4Pattern = '(^([0-9a-fA-F]{8})-?([0-9a-fA-F]{4})-?(4[0-9a-fA-F]{3})-?([89abAB][0-9a-fA-F]{3})-?([0-9a-fA-F]{12})$)';
        if ( preg_match( $uuidv4Pattern, $input, $matches ) !== 1 ) 
        {
            throw new phpillowValidationException( 
                'A uuid version 4 is required. Something else has been given.',
                array()
            );
        }

        return strtolower( 
            $matches[1] . '-' . $matches[2] . '-' . $matches[3] . '-' . $matches[4] . '-' . $matches[5]
        );
    }
}
