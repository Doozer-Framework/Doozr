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
 * @version $Revision: 161 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */

/**
 * Document representing the groups
 *
 * @package Core
 * @version $Revision: 161 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPL
 */
class phpillowGroupDocument extends phpillowDocument
{
    /**
     * Document type, may be a string matching the regular expression:
     *  (^[a-zA-Z0-9_]+$)
     *
     * @var string
     */
    protected static $type = 'group';

    /**
     * List of required properties. For each required property, which is not
     * set, a validation exception will be thrown on save.
     *
     * @var array
     */
    protected $requiredProperties = array(
        'name',
    );

    /**
     * Indicates whether to keep old revisions of this document or not.
     *
     * @var bool
     */
    protected $versioned = false;

    /**
     * Construct new book document
     *
     * Construct new book document and set its property validators.
     *
     * @return void
     */
    public function __construct()
    {
        $this->properties = array(
            'name'          => new phpillowRegexpValidator( '(^[\x21-\x7e]+$)i' ),
            'description'   => new phpillowTextValidator(),
            'users'         => new phpillowArrayValidator(),
            'userDocs'      => new phpillowDocumentArrayValidator( 'phpillowUserDocument' ),
            'permissions'   => new phpillowArrayValidator(),
        );

        parent::__construct();
    }

    /**
     * Get ID from document
     *
     * The ID normally should be calculated on some meaningful / unique
     * property for the current type of documents. The returned string should
     * not be too long and should not contain multibyte characters.
     *
     * You can return null instead of an ID string, to trigger the ID
     * autogeneration.
     *
     * @return mixed
     */
    protected function generateId()
    {
        return $this->stringToId( $this->storage->name );
    }

    /**
     * Return document type name
     *
     * This method is required to be implemented to return the document type
     * for PHP versions lower then 5.2. When only using PHP 5.3 and higher you
     * might just implement a method which does "return static:$type" in a base
     * class.
     *
     * @return void
     */
    protected function getType()
    {
        return self::$type;
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
     * @return phpillowDocument
     */
    public static function createNew( $docType = null )
    {
        return parent::createNew( $docType === null ? __CLASS__ : $docType );
    }
}

