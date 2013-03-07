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
 * @version $Revision: 43 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */

/**
 * Validate string inputs against a regular expression
 *
 * @package Core
 * @version $Revision: 43 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */
class phpillowRegexpValidator extends phpillowValidator
{
    /**
     * Regular expression to validate against
     * 
     * @var string
     */
    protected $regexp;

    /**
     * Validator constructor
     *
     * Validator constructor to specify the required PCRE regular expression
     * for the input string validation.
     * 
     * @param string $regexp
     * @return void
     */
    public function __construct( $regexp )
    {
        $this->regexp = $regexp;
    }

    /**
     * Validate input string against a regexp
     *
     * Validates the input string against the configured regular expression.
     * 
     * @param mixed $input 
     * @return string
     */
    public function validate( $input )
    {
        // Check if regular expression matches the input string.
        if ( !preg_match( $this->regexp, $input ) )
        {
            throw new phpillowValidationException(
                'Input %input did not match regular expression %expression.', 
                array(
                    'input' => $input,
                    'expression' => $this->regexp,
                )
            );
        }

        return (string) $input;
    }
}

