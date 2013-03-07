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
 * Wrapper base for views in the database, where view functions are locally
 * stored as files.
 *
 * @package Core
 * @version $Revision: 159 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */
abstract class phpillowFileView extends phpillowView
{
    /**
     * View functions to be registered on the server.
     *
     * Contains file names for map and reduce functions, indexed by their name.
     * The view files are only read when the view is updated on the server. The
     * format of the array look like:
     *
     * <code>
     *  array(
     *      'name' => array(
     *          'map'    => __DIR__ . '/views/map/all.js',
     *          'reduce' => __DIR__ . '/views/reduce/all.js',
     *      ),
     *      ...
     *  )
     * </code>
     *
     * If you do not want to define a reduce function omit the reduce index or
     * use `null` as a value.
     *
     * @var array
     */
    protected $viewFunctions = array();

    /**
     * Construct new document
     *
     * Construct new document
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->views    = $this->viewFunctions;
    }

    /**
     * Verify stored views
     *
     * Check if the views stored in the database equal the view definitions
     * specified by the vew classes. If the implementation differs update to the
     * view specifications in the class.
     *
     * @return void
     */
    public function verifyView()
    {
        // Fetch view definition from database
        try
        {
            $view = self::fetchById( '_design/' . $this->getViewName() );
        }
        catch ( phpillowResponseNotFoundErrorException $e )
        {
            // If the view does not exist yet, recreate it from current view
            $view = $this;
        }

        // Force setting of view definitions
        $views = array();
        foreach ( $this->viewFunctions as $name => $functions )
        {
            $views[$name]['map'] = file_get_contents( $functions['map'] );

            // Check if there is also a reduce function for the given view
            // function.
            if ( isset( $functions['reduce'] ) )
            {
                $views[$name]['reduce'] = file_get_contents( $functions['reduce'] );
            }
        }

        $view->views = $views;
        $view->save();
    }
}

