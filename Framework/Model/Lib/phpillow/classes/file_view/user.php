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
 * Wrapper for user views
 *
 * @package Core
 * @version $Revision: 159 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */
class phpillowUserFileView extends phpillowFileView
{
    /**
     * Construct view
     *
     * Construct view
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->viewFunctions = array(
            'all' => array(
                'map'    => dirname( __FILE__ ) . '/map/user_all.js',
            ),
            'user' => array(
                'map'    => dirname( __FILE__ ) . '/map/user_user.js',
                'reduce' => dirname( __FILE__ ) . '/reduce/sum.js',
            ),
        );
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
     * @return phpillowUserFileView
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
        return 'users';
    }
}

