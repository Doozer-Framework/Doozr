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
 * @version $Revision: 169 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */

/**
 * validate boolean inputs
 *
 * @package Core
 * @version $Revision: 169 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */
class phpillowBooleanValidator extends phpillowValidator
{
    /**
     * Validate input as a boolean
     * 
     * @param mixed $input 
     * @return bool
     */
    public function validate( $input )
    {
        return (bool) $input;
    }
}
