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
 * @version $Revision: 124 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */

/**
 * This is the bootstrap file, which sets up an autoload mechanism using
 * spl-Autoload-register, which makes all PHPillow classes available in your
 * scripts.
 *
 * To use PHPillow just include this file, like:
 *
 * <code>
 *  include '/path/to/phpillow/bootstrap.php';
 * </code>
 */
function phpillow_autoload( $class )
{
    static $classes;

    if ( $classes === null )
    {
        $classes = include dirname( __FILE__ ) . '/classes/autoload.php';
    }

    if ( !isset( $classes[$class] ) )
    {
        return false;
    }

    include dirname( __FILE__ ) . '/' . $classes[$class];
    return true;
}

spl_autoload_register( 'phpillow_autoload' );