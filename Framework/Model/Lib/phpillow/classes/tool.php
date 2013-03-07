<?php
/**
 * phpillow tool
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
 * @version $Revision: 185 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */

/**
 * Basic tool handling in- end exports of CouchDB dumps.
 *
 * API and format should be compatible with couchdb-python [1].
 *
 * [1] http://code.google.com/p/couchdb-python/
 *
 * @package Core
 * @version $Revision: 185 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */
class phpillowTool
{
    /**
     * Data source name for the CouchDB connection
     *
     * @var string
     */
    protected $dsn;

    /**
     * CLI tool options
     *
     * @var array
     */
    protected $options;

    /**
     * Parsed connection information
     *
     * @var array
     */
    protected $connectionInfo = array(
        'host' => 'localhost',
        'port' => '5984',
        'user' => null,
        'pass' => null,
        'path' => '/',
    );

    /**
     * Standard output stream
     *
     * @var resource
     */
    protected $stdout = STDOUT;

    /**
     * Standard error stream
     *
     * @var resource
     */
    protected $stderr = STDERR;

    /**
     * Construct tool
     *
     * Construct tool from database DSN (Data-Source-Name, the URL defining the
     * databases location) and an optional set of options.
     *
     * @param mixed $dsn
     * @param array $options
     * @return void
     */
    public function __construct( $dsn, array $options = array() )
    {
        $this->dsn     = $dsn;
        $this->options = $options;

        if ( !array_search( 'string', stream_get_wrappers() ) )
        {
            stream_wrapper_register( 'string', 'phpillowToolStringStream' );
        }
    }

    /**
     * Set output streams
     *
     * Set the output streams to be used by the tool.
     *
     * @param resource $stdout
     * @param resource $stderr
     * @return void
     */
    public function setOutputStreams( $stdout = STDOUT, $stderr = STDERR )
    {
        $this->stdout = $stdout;
        $this->stderr = $stderr;
    }

    /**
     * Echo a message
     *
     * Echo a progress message to STDERR, if the verbose flag is set.
     *
     * @param string $message
     * @return void
     */
    protected function out( $message )
    {
        if ( isset( $this->options['v'] ) ||
             isset( $this->options['verbose'] ) )
        {
            fwrite( $this->stderr, $message );
        }
    }

    /**
     * Print version
     *
     * Print version of the tool, if the version flag has been set.
     *
     * @return bool
     */
    protected function printVersion()
    {
        if ( !isset( $this->options['version'] ) )
        {
            return false;
        }

        $version = '$Revision: 185 $';
        if ( preg_match( '(\\$Revision:\\s+(?P<revision>\\d+)\\s*\\$)', $version, $match ) )
        {
            $version = 'svn-' . $match['revision'];
        }

        fwrite( $this->stdout, "PHPillow backup tool - version: $version\n" );
        return true;
    }

    /**
     * Parse the provided connection information
     *
     * Returns false,if the connection information could not be parser
     * properly.
     *
     * @return bool
     */
    protected function parseConnectionInformation()
    {
        if ( ( $info = @parse_url( $this->dsn ) ) === false )
        {
            fwrite( $this->stderr, "Could not parse provided DSN: {$this->dsn}\n" );
            return false;
        }

        foreach ( $info as $key => $value )
        {
            if ( array_key_exists( $key, $this->connectionInfo ) )
            {
                $this->connectionInfo[$key] = $value;
            }
        }

        if ( isset( $this->options['username'] ) )
        {
            $this->connectionInfo['user'] = $this->options['username'];
        }

        if ( isset( $this->options['password'] ) )
        {
            $this->connectionInfo['pass'] = $this->options['password'];
        }

        return true;
    }

    /**
     * Execute dump command
     *
     * Returns a proper status code indicating successful execution of the
     * command.
     *
     * @return int
     */
    public function dump()
    {
        if ( $this->printVersion() )
        {
            return 0;
        }

        if ( !$this->parseConnectionInformation() )
        {
            return 1;
        }

        $db = new phpillowCustomConnection(
            $this->connectionInfo['host'],
            $this->connectionInfo['port'],
            $this->connectionInfo['user'],
            $this->connectionInfo['pass']
        );

        $writer = new phpillowToolMultipartWriter( $this->stdout );

        // Fetch and dump documents in chunks of 1000 documents, since the
        // memory consumption might be too high otherwise
        // @TODO: Make chunk-size configurable.
        $offset = null;
        $limit  = 1000;
        do {
            $docs = $db->get( $this->connectionInfo['path'] . '/_all_docs?limit=' . $limit .
                ( $offset !== null ? '&startkey="' . $offset . '"' : '' )
            );

            foreach ( $docs->rows as $nr => $doc )
            {
                if ( ( $nr === 0 ) &&
                     ( $offset !== null ) )
                {
                    // The document which equals the startkey and already has
                    // been dumped.
                    continue;
                }

                $offset = $doc['id'];

                $this->out( "Dumping document " . $doc['id'] . "\n" );
                $doc = $db->get( $this->connectionInfo['path'] . '/' . urlencode( $doc['id'] ) );

                // Skip deleted documents
                // @TODO: Make this configurable
                if ( isset( $doc->deleted ) &&
                     ( $doc->deleted === true ) )
                {
                    continue;
                }

                // Fetch attachments explicitly. Including the attachments in
                // the doc sometimes causes errors on CouchDB 0.10
                $doc = $doc->getFullDocument();
                if ( isset( $doc['_attachments'] ) )
                {
                    foreach ( $doc['_attachments'] as $name => $attachment )
                    {
                        $data = $db->get( $this->connectionInfo['path'] . '/' . urlencode( $doc['_id'] ) . '/' . $name, null, true );
                        $doc['_attachments'][$name]['data'] = $data->data;
                    }
                }

                $writer->writeDocument( $doc );
            }
        } while ( count( $docs->rows ) > 1 );

        unset( $writer );
        return 0;
    }

    /**
     * Clean up document definition
     *
     * Returns the cleaned up document body as a result.
     *
     * @param array $document
     * @return string
     */
    protected function getDocumentBody( array $document )
    {
        if ( strpos( $document['Content-Type'], 'application/json' ) === 0 )
        {
            $source = json_decode( $document['body'], true );
            unset( $source['_rev'] );
            return json_encode( $source );
        }

        if ( is_array( $document['body'] ) )
        {
            $main   = array_shift( $document['body'] );
            $source = json_decode( $main['body'], true );
            unset( $source['_rev'] );

            $source['_attachments'] = array();
            foreach ( $document['body'] as $attachment )
            {
                $source['_attachments'][$attachment['Content-ID']] = array(
                    'content_type' => $attachment['Content-Type'],
                    'data'         => base64_encode( $attachment['body'] ),
                );
            }

            return json_encode( $source );
        }

        throw new phpillowMultipartParserException( "Invalid document: " . var_export( $document, true ) );
    }

    /**
     * Execute load command
     *
     * Returns a proper status code indicating successful execution of the
     * command.
     *
     * @return int
     */
    public function load()
    {
        if ( $this->printVersion() )
        {
            return 0;
        }

        if ( !$this->parseConnectionInformation() )
        {
            return 1;
        }

        // Open input stream to read contents from
        $stream = isset( $this->options['input'] ) ? fopen( $this->options['input'], 'r' ) : STDIN;
        $multipartParser = new phpillowToolMultipartParser( $stream );

        $db = new phpillowCustomConnection(
            $this->connectionInfo['host'],
            $this->connectionInfo['port'],
            $this->connectionInfo['user'],
            $this->connectionInfo['pass']
        );

        // Create database if it does not exist yet
        try
        {
            $db->get( $this->connectionInfo['path'] );
        }
        catch ( phpillowResponseNotFoundErrorException $e )
        {
            $db->put( $this->connectionInfo['path'] );
        }

        // Import the documents
        while ( ( $document = $multipartParser->getDocument() ) !== false )
        {
            try
            {
                $this->out( "Loading document " . $document['Content-ID'] . "\n" );
                $path = $this->connectionInfo['path'] . '/' . $document['Content-ID'];
                $db->put( $path, $this->getDocumentBody( $document ) );
            }
            catch ( phpillowException $e )
            {
                fwrite( $this->stderr, $document['Content-ID'] . ': ' . $e->getMessage() . "\n" );
                if ( !isset( $this->options['ignore-errors'] ) )
                {
                    return 1;
                }
            }
        }

        return 0;
    }

    /**
     * Prime caches of all views
     *
     * Returns a proper status code indicating successful execution of the
     * command.
     *
     * @return int
     */
    public function primeCaches()
    {
        if ( $this->printVersion() )
        {
            return 0;
        }

        if ( !$this->parseConnectionInformation() )
        {
            return 1;
        }

        // Open connection
        $db = new phpillowCustomConnection(
            $this->connectionInfo['host'],
            $this->connectionInfo['port'],
            $this->connectionInfo['user'],
            $this->connectionInfo['pass']
        );
        $designDocs = $db->get( $this->connectionInfo['path'] . '/_all_docs?startkey=%22_design%2F%22&endkey=%22_design0%22' );
        foreach ( $designDocs->rows as $doc )
        {
            $views = $db->get( $this->connectionInfo['path'] . '/' . $doc['id'] );
            foreach ( $views->views as $view => $functions )
            {
                $this->out( "Priming view " . $doc['id'] . "/" . $view . "\n" );
                $db->get( $this->connectionInfo['path'] . '/' . $doc['id'] . '/_view/' . $view );
            }
        }
    }
}

