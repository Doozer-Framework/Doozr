#!/usr/bin/env php
<?php
/**
 * arbit commit message parser
 *
 * You may use the following code as a SVN pre-commit hook, commonly found at
 * /path/to/repository/hooks/pre-commit:
 *
 * <code>
 *  #!/bin/sh
 *
 *  REPOS="$1"
 *  TXN="$2"
 *
 *  # Make sure that the log message contains some text.
 *  MESSAGE=`svnlook log -t "$TXN" "$REPOS"`
 *
 *  ERROR=`/usr/local/svn/bin/validate_commit_message.php "$MESSAGE"`
 *
 *  if [ "$?" = "1" ]; then
 *          echo "" 1>&2
 *          echo "$ERROR" 1>&2
 *          exit 1
 *  fi
 *
 *  # All checks passed, so allow the commit.
 *  exit 0
 * </code>
 *
 * This file is part of arbit.
 *
 * arbit is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * arbit is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with arbit; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Core
 * @version $Revision: 159 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Arbit parser for arbit commit messages defined by the following grammar:
 *
 * <code>
 *  Message       ::= Statement+ | Statement* Comment+
 *  Statement     ::= Reference | Fixed | Implemented | Documented | Tested
 *
 *  Comment       ::= '# ' TextLine | '#\n'
 *
 *  Reference     ::= '- Refs'         BugNr  ': ' TextLine Text?
 *  Fixed         ::= '- ' FixedString BugNr  ': ' TextLine Text?
 *  Implemented   ::= '- Implemented'  BugNr? ': ' TextLine Text?
 *  Documented    ::= '- Documented'   BugNr? ': ' TextLine Text?
 *  Tested        ::= '- Tested: '                 TextLine Text?
 *
 *  FixedString   ::= 'Fixed' | 'Closed'
 *
 *  Text          ::= '  ' TextLine Text?
 *  BugNr         ::= ' #' [1-9]+[0-9]*
 *  TextLine      ::= [\x20-\x7E]+ "\n"
 * </code>
 *
 * With one additional condition not mentioned in the grammar, that no line
 * should ever exceed 79 characters per line.
 *
 * A textual description of the rules above:
 *
 * <code>
 *  All messages should wrap at 79 characters per line. This means, if you are
 *  writing multiple lines after a message starting with a "- " each following
 *  line should be indented by exactly two spaces.
 *
 *  Including descriptive text in your commit messages is generally important to
 *  offer a good overview on the commit when the issue tracker is not available
 *  (commit mails, history).
 *
 *  All messages may include references to existing issues to add status updates
 *  to the issue, which should look like::
 *
 *  	- Refs #<number>: <text>
 *
 *  Where <number> references the ticket and the <text> describes what you did.
 *
 *  Comments
 *  --------
 *
 *  You may always append arbitrary comments in your commit messages, where each
 *  line should start with a number sign (#). Text in these lines won't be
 *  checked.
 *
 *  Bug fix
 *  -------
 *
 *  A bug fix commit message should follow the following scheme::
 *
 *  	- Fixed #<number>: <text>
 *
 *  Where <number> references the closed bug and <text> is a description of the
 *  bug and the fix. Keep in mind that the texts will be used for the changelog,
 *  so please check the spelling before committing.
 *
 *  The bug number is not optional, which means that there should be an open bug
 *  in the issue tracker for *each* bug you fix.
 *
 *  For compatibility with other issue tracker you may also use "Closed" instead
 *  of "Fixed" in your message, but "Fixed" is highly preferred.
 *
 *  New features
 *  ------------
 *
 *  If you implemented a new feature, your commit message should look like::
 *
 *  	- Implemented[ #<number>]: <text>
 *
 *  Where <text> is a short description of the feature you implemented, and
 *  <number> may optionally reference a feature request in the bug tracker. Keep
 *  in mind that the texts will be used for the changelog, so please check the
 *  spelling before committing.
 *
 *  Documentation
 *  -------------
 *
 *  If you extended your documentation, your commit message should look like::
 *
 *  	- Documented[ #<number>]: <text>
 *
 *  Where <number> optionally specifies a documentation request, and the text
 *  describes what you documented.
 *
 *  Additional tests
 *  ----------------
 *
 *  If you added tests for some feature, your commit message should look like::
 *
 *  	- Tested: <text>
 *
 *  Where <text> describes the feature(s) you are testing.
 *
 *  Other commits
 *  -------------
 *
 *  If your commit does not match any of the above rules you should only include a
 *  comment in your commit message or extend this document with your commit
 *  message of desire.
 * </code>
 *
 * Even we have a contextfree grammar for the language, we implement the
 * trivial parser using regular expressions.
 *
 * @package Core
 * @version $Revision: 159 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitCommitMessageParser
{
    /**
     * Construct parser
     *
     * @return void
     */
    public function __construct()
    {
        // ?
    }

    /**
     * Parse a commit message
     *
     * Parses a commit messages defined by the grammar documented in the class
     * header. If a parse error occurs an exception will be thrown containing
     * the error message.
     *
     * On a successful parsing process a struct containing a list with all
     * relevant data will be returned, which looks like:
     *
     * <code>
     *  array(
     *      array(
     *          'type' => <type>,
     *          'bug'  => <number> | null,
     *          'text' => <text>,
     *      ),
     *      ...
     *  )
     * </code>
     *
     * @param string $string
     * @return array
     */
    public function parse( $string )
    {
        $string = $this->normalizeWhitespaces( $string );
        if ( $string === '' )
        {
            throw new arbitCommitParserException(
                'Empty commit message.'
            );
        }

        $string = $this->removeComments( $string );
        if ( $string === '' )
        {
            // Do not enter parsing process if there were only comments in the
            // commit message.
            return array();
        }

        return $this->parseStatements( $string );
    }

    /**
     * Removes comments from a commit message
     *
     * Removes all valid comments from a commit messages, as they are not of
     * interest for the content extraction.
     *
     * @param string $string
     * @return string
     */
    protected function removeComments( $string )
    {
        return preg_replace(
            '(^#(?: [\x20-\x7E]{1,77})?$)m',
            '',
            $string
        );
    }

    /**
     * Normalizes whitespaces in commit message
     *
     * Even not defined in the grammar we do not care about additional newlines
     * or empty lines anywhere.
     *
     * @param string $string
     * @return string
     */
    protected function normalizeWhitespaces( $string )
    {
        return preg_replace(
            '((?:\r\n|\r|\n)(?:[ \t](?:\r\n|\r|\n))*)',
            "\n",
            trim( $string )
        );
    }

    /**
     * Parse all statements
     *
     * Parse the statements like defined in the grammar in the class level
     * docblock. As a result an array is returned with the following structure:
     *
     * <code>
     *  array(
     *      array(
     *          'type' => <type>,
     *          'bug'  => <number> | null,
     *          'text' => <text>,
     *      ),
     *      ...
     *  )
     * </code>
     *
     * If the commit message could not be parsed a arbitCommitParserException
     * will be thrown.
     *
     * @param string $string
     * @return array
     */
    protected function parseStatements( $string )
    {
        $lines = explode( "\n", $string );
        $statements = array();
        $statement = null;

        foreach ( $lines as $line )
        {
            // Skip empty lines
            if ( trim( $line ) === '' )
            {
                continue;
            }

            // Check for line length
            $line = rtrim( $line );
            $echodLine = '"' . substr( $line, 0, 30 ) . '..."';
            if ( strlen( $line ) > 79 )
            {
                throw new arbitCommitParserException( "Too long line: $echodLine" );
            }

            if ( preg_match( '(^-\\x20
              (?# Type of statement )
                (?P<type>Refs|Fixed|Closed|Implemented|Documented|Tested)
              (?# Match optional bug number )
                (?:\\x20\\#(?P<bug>[1-9]+[0-9]*))?
              (?# Match required text line )
                (?::\\x20(?P<text>[\x20-\x7E]+))?
            $)x', $line, $match ) )
            {
                // Check if required text has been included in message
                if ( !isset( $match['text'] ) || empty( $match['text'] ) )
                {
                    throw new arbitCommitParserException(
                        "Textual description missing in line $echodLine"
                    );
                }

                // Check if bug number has been set for statements requiring a
                // bug number
                if ( in_array( $match['type'], array(
                        'Refs',
                        'Fixed',
                        'Closed',
                     ) ) )
                {
                    if ( !isset( $match['bug'] ) || empty( $match['bug'] ) )
                    {
                        throw new arbitCommitParserException(
                            "Missing bug number in line: $echodLine"
                        );
                    }
                }

                // Ensure no bug number has been provided for statements which
                // may not be used together with a bug number
                if ( in_array( $match['type'], array(
                        'Tested',
                     ) ) )
                {
                    if ( isset( $match['bug'] ) && !empty( $match['bug'] ) )
                    {
                        throw new arbitCommitParserException(
                            "Superflous bug number in line: $echodLine"
                        );
                    }

                    // Force bug to null, so we can use this variable later
                    $match['bug'] = null;
                }

                // Append prior statement to statement array
                if ( $statement !== null )
                {
                    $statements[] = $statement;
                }

                // Create new statement from data
                $statement = array(
                    'type' => str_replace( 'Closed', 'Fixed', $match['type'] ),
                    'bug'  => $match['bug'],
                    'text' => $match['text'],
                );
            }
            elseif ( preg_match( '(^  (?P<text>[\x20-\x7E]+)$)', $line, $match ) )
            {
                if ( $statement == null )
                {
                    // Each additional comment line has to be preceeded by a
                    // statement
                    throw new arbitCommitParserException(
                        "No statement precedes text line: $echodLine"
                    );
                }

                $statement['text'] .= ' ' . $match['text'];
            }
            else
            {
                throw new arbitCommitParserException(
                    "Invalid commit message: $echodLine"
                );
            }
        }

        return $statements;
    }
}

/**
 * Very simple exception class, posign for the original exception class
 *
 * @package Core
 * @version $Revision: 159 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitCommitParserException extends Exception
{
}

// Read commit message from CLI
$message = $argv[1];

try
{
    $parser = new arbitCommitMessageParser();
    $parser->parse( $message );
}
catch ( arbitCommitParserException $e )
{
    printf(
        "Erroneous commit message:\n- %s\n\nCheck the commit message guidelines in doc/coding_guidelines.txt.\n",
        $e->getMessage()
    );
    exit( 1 );
}

// Stay silence on success

