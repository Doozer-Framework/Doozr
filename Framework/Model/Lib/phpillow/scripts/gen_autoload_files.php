#!/usr/bin/env php
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
 * @package Scripts
 * @version $Revision: 159 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

$basePath = dirname( __FILE__ ) . '/../';

// Add core to search paths
$searchPaths = array(
    $basePath . 'classes/',
);

/**
* CycleDetection
*
 * @package Scripts
 * @version $Revision: 159 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
*/
class arbitTopoLogicalSorting
{
    /**
    * Array with all graph nodes
    *
    * @var array
    */
    protected $nodes;

    /**
    * Array with edges in the graph, which has the structure:
    *
    * <code>
    *  array(
    *      'source_node' = array(
    *          'dest_node_1',
    *          'dest_node_2',
    *          ...
    *      ),
    *      ...
    *  )
    * </code>
    *
    * @var array
    */
    protected $edges;

    /**
    * Array with leaves in the graph
    *
    * @var array
    */
    protected $leaves;

    /**
    * Construct CycleDetection graph
    *
    * @return void
    */
    public function __construct()
    {
        $this->nodes  = array();
        $this->edges  = array();
        $this->leaves = array();
    }

    /**
    * Add node to the graph
    *
    * @param string $className
    * @param array $info
    * @return void
    */
    public function addNode( $className, array $info )
    {
        $this->nodes[$className] = $info;
        $this->leaves[] = $className;
    }

    /**
    * Add directed connection to the graph
    *
    * Add a connection from node $src to node $dst to the graph.
    *
    * @param string $src
    * @param string $dst
    * @return void
    */
    public function addConnection( $src, $dst )
    {
        $this->edges[$src][] = $dst;

        // Remove node with new incoming edge from initial leaves array
        if ( ( $id = array_search( $dst, $this->leaves ) ) !== false )
        {
            unset( $this->leaves[$id] );
        }
    }

    /**
    * Remove cycles from the graph
    *
    * This method uses an algorithm similar to the topological sort algorithm
    * to remove all cycles from the graph. It will not remove all node, which
    * are not in cycles, but leave nodes, which are linked from cycles will
    * stay in the graph.
    *
    * @return void
    */
    public function getSortedNodeList()
    {
        $list = array();

        while ( $leave = array_pop( $this->leaves ) )
        {
            unset( $this->nodes[$leave] );
            $list[] = $leave;

            if ( !isset( $this->edges[$leave] ) )
            {
                continue;
            }

            foreach ( $this->edges[$leave] as $node )
            {
                $count = -1;
                // Count the incoming edges for each node which had an incoming
                // connection from the removed leave.
                foreach ( $this->edges as $src => $dsts )
                {
                    foreach ( $dsts as $nr => $dst )
                    {
                        $count += ( $dst === $node );

                        // Also remove all edges which point to the leave
                        if ( $dst === $leave )
                        {
                            unset( $this->edges[$src][$nr] );
                        }
                    }
                }

                // Add all new leaves to the leave array.
                if ( $count <= 0 )
                {
                    array_push( $this->leaves, $node );
                }
            }

            // Remove all outgoing edges from leave
            unset( $this->edges[$leave] );
        }

        if ( count( $this->edges ) )
        {
            var_dump( $this->edges );
            throw new Exception( 'Cycle found in graph. This should not happen.' );
        }

        return $list;
    }

    /**
     * Render a graph
     *
     * Renders the graph using dot (http://graphviz.org) into the specified PNG
     * file.
     *
     * @param string $file
     * @return void
     */
    public function renderGraph( $file )
    {
        $fp = fopen( $graphFile = '/tmp/arbit_class_graph.dot', 'w' );

        // Open Graph
        fwrite( $fp, "digraph G {\n\n\t
    node [
        fontname=Arial,
        fontcolor=\"#2e3436\",
        fontsize=10,

        style=filled,
        color=\"#2e3436\",
        fillcolor=\"#babdb6\"
    ];

    mindist = 0.4;
    rankdir=RL;
    splines  = true;
    overlap=false;\n\n" );

        // Group nodes by package
        $groups = array();
        foreach ( $this->nodes as $name => $data )
        {
            $package = ( $data['package'] === false ? 'Core' : $data['package'] );
            $groups[$package][] = $name;
        }

        // Add nodes to graph
        foreach ( $groups as $group => $classes )
        {
            fwrite( $fp, sprintf( "\tsubgraph cluster_%s {\n\t\tlabel=\"%s\";\n\n", $group, $group ) );
            foreach ( $classes as $name )
            {
                $data = $this->nodes[$name];

                // Skip exception classes
                if ( strpos( $name, 'Exception' ) !== false )
                {
                    continue;
                }

                fwrite( $fp, sprintf( "\t\tnode [\n\t\t\tlabel = \"%s\",\n\t\t\tshape = \"Mrecord\",\n\t\t\tfillcolor=\"%s\"\n\t\t] \"%s\";\n\n",
                    $name . ' | ' . implode( ' | ', $data['functions'] ),
                    ( $data['type'] === 'class' ? '#eeeeef' : '#729fcf' ),
                    $name
                ) );
            }
            fwrite( $fp, sprintf( "\t}\n\n" ) );
        }

        // Add node connections
        foreach ( $this->edges as $src => $dsts )
        {
            if ( !is_array( $dsts ) ||
                 ( strpos( $src, 'Exception' ) !== false ) )
            {
                // Skip edges without destinations and exception classes
                continue;
            }

            foreach ( $dsts as $dst )
            {
                fwrite( $fp, sprintf( "\t\"%s\" -> \"%s\"\n", $src, $dst ) );
            }
        }

        fwrite( $fp, "\n}\n\n" );
        fclose( $fp );

        // Render graph
        shell_exec( 'dot -Tpng -o ' . escapeshellarg( $file ) . ' ' . escapeshellarg( $graphFile ) );
        unlink( $graphFile );
    }
}

/**
 * Get dependencies for class
 *
 * @param string $file
 * @return array
 */
function getClassDependenciesFromFile( $file )
{
    $classes = array();
    $tokens = token_get_all( file_get_contents( $file ) );

    $lastKeyword = $type = $relation = $class = null;
    $visibility = 'public';
    $package = false;
    foreach( $tokens as $token )
    {
        if ( $lastKeyword === null && is_array( $token ) )
        {
            switch( $token[0] )
            {
                case T_CLASS:
                case T_INTERFACE:
                    $lastKeyword = $token[1];
                    $type = $token[1];
                    break;
                case T_EXTENDS:
                case T_IMPLEMENTS:
                    $lastKeyword = $token[1];
                    $relation = $token[1];
                    break;
                case T_FUNCTION:
                    $lastKeyword = $token[1];
                    break;
                case T_PROTECTED:
                    $visibility = 'protected';
                    break;
                case T_PRIVATE:
                    $visibility = 'private';
                    break;
                case T_DOC_COMMENT:
                    if ( preg_match( '(@subpackage\s+(\w+))', $token[1], $match ) )
                    {
                        $package = $match[1];
                    }
                    break;
            }

        }
        else if ( is_array( $token ) && $token[0] == T_WHITESPACE )
        {
            continue;
        }
        else if ( !is_array( $token ) && $token == ',' )
        {
            continue;
        }
        else if ( is_array( $token ) && $token[0] == T_STRING )
        {
            if ( $lastKeyword === 'extends' )
            {
                // We found a base class, just add to current class
                $classes[$class]['extends'] = $token[1];
                $lastKeyword = null;
            }
            else if ( $lastKeyword === 'implements' )
            {
                // We found an interface, just add to current class
                $classes[$class]['implements'][] = $token[1];
            }
            else if ( $lastKeyword === 'function' )
            {
                if ( $visibility === 'public' )
                {
                    // Only list public methods
                    $classes[$class]['functions'][] = $token[1];
                }

                // Reset visibility
                $visibility = 'public';
            }
            else
            {
                // Else we just found a class name
                $class = $token[1];
                $lastKeyword = null;

                // Initialize class struct
                $classes[$class]['type'] = $type;
                $classes[$class]['extends'] = null;
                $classes[$class]['implements'] = array();
                $classes[$class]['functions'] = array();
                $classes[$class]['package'] = $package;
                $package = false;
            }
        }
        else
        {
            $lastKeyword = null;
        }
    }

    return $classes;
}

foreach ( $searchPaths as $path )
{
    echo "Searching in $path ... ";

    $phpFiles = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            $path,
            RecursiveDirectoryIterator::KEY_AS_FILENAME
        )
    );

    // Collect all files from iterator to sort them.
    $phpFileArray = array();
    foreach ( $phpFiles as $file )
    {
        $phpFileArray[] = (string) $file;
    }
    sort( $phpFileArray );

    $topoSort = new arbitTopoLogicalSorting();
    $mapping = array();
    $maxClassNameLength = 0;

    foreach ( $phpFileArray as $file )
    {
        if ( substr( $file, -4 ) !== '.php' )
        {
            // Skip all non-php files.
            continue;
        }

        $classes = getClassDependenciesFromFile( $file );
        foreach ( $classes as $class => $data )
        {
            $topoSort->addNode( $class, $data );
            $mapping[$class] = str_replace( $path, '', $file );

            // Update max class length for later formatting of the autoload
            // file.
            $maxClassNameLength = max( $maxClassNameLength, strlen( $class ) );

            // Add connections for base class
            if ( $data['extends'] !== null )
            {
                $topoSort->addConnection( $class, $data['extends'] );
            }

            // .. and interfaces
            foreach ( $data['implements'] as $interface )
            {
                $topoSort->addConnection( $class, $interface );
            }
        }
    }

    // Render class dependency graph
    $topoSort->renderGraph( $path . '/classes.png' );

    // Create autoload file from this.
    $sorted = $topoSort->getSortedNodeList();
    $sorted = array_reverse( $sorted );

    $autoload = <<<EOF
<?php
/**
 * phpillow autoload file
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
 * @version \$Revision: 159 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */

/*
 * This array is autogenerated and topologically sorted. Do not change anything
 * in here, but just run the following script in the trunk/ directory.
 *
 * # scripts/gen_autoload_files.php
 */
return array(

EOF;
    foreach ( $sorted as $class )
    {
        if ( !isset( $mapping[$class] ) )
        {
            // File is external
            continue;
        }

        $autoload .= sprintf( "    '%s'%s => '%s',\n",
            $class,
            str_repeat( ' ', $maxClassNameLength - strlen( $class ) ),
            str_replace( $basePath, '', $path ) . $mapping[$class]
        );
    }

    $autoload .= ");\n\n";

    // Write to autoload file
    file_put_contents( $path . '/autoload.php', $autoload );

    echo "Done.\n";
}

