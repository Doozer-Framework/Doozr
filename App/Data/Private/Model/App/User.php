<?php
// @TODO: App App_ app - is my namespace! replace it with a name e.g. of your app

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Document - User
 *
 * User.php - This class is the base for document - User
 * This class extends Doodi_Couchdb_Document (Doodi is the OxM of DoozR
 * and CouchDB is the used driver).
 *
 * PHP versions 5
 *
 * LICENSE:
 * App - The platform that ...
 *
 * Copyright (c) 2013, Benjamin Carl - All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * - All advertising materials mentioning features or use of this software
 *   must display the following acknowledgement: This product includes software
 *   developed by Benjamin Carl and other contributors.
 * - Neither the name Benjamin Carl nor the names of other contributors
 *   may be used to endorse or promote products derived from this
 *   software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * Please feel free to contact us via e-mail: opensource@clickalicious.de
 *
 * @category   App
 * @package    App_Model
 * @subpackage App_Model_User
 * @author     Benjamin Carl <benjamin.carl@clickalicious.de>
 * @copyright  2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://www.app.tld
 */

require_once DOOZR_DOCUMENT_ROOT . 'Model/Doodi/Couchdb/Bootstrap.php';

/**
 * Document - User
 *
 * This class is the base for document - User. This class extends Doodi_Couchdb_Document
 * (Doodi is the OxM of DoozR and CouchDB is the used driver).
 *
 * @category   App
 * @package    App_Model
 * @subpackage App_Model_User
 * @author     Benjamin Carl <benjamin.carl@clickalicious.de>
 * @copyright  2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://www.app.tld
 */
class App_User extends Doodi_Couchdb_Document
{
    /**
     * This is the type of this document
     * We make use of a type so we can differ
     * the documents from each other.
     *
     * @var string
     * @access protected
     */
    protected static $type = 'user';

    /**
     * the required properties of this document
     *
     * @var array
     * @access protected
     */
    protected $requiredProperties = array(
        'salutation',
        'firstname',
        'lastname',
        'email'
    );

    /**
     * Constructor.
     *
     * @param null $id
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \App_User Instance of this class
     * @access public
     */
    public function __construct($id = null)
    {
        // defines the available properties
        $this->properties = array(
            'salutation' => new Doodi_Couchdb_String_Validator(),
            'firstname'  => new Doodi_Couchdb_String_Validator(),
            'lastname'   => new Doodi_Couchdb_String_Validator(),
            'email'      => new Doodi_Couchdb_Email_Validator()
        );

        // call parents constructor
        parent::__construct();

        // check for fetching default view by_id
        if ($id !== null) {
            require_once 'Data/Private/Model/App/User/View.php';
            $result = App_User_View::by_id($id);

            foreach ($result['value'] as $property => $value) {
                $this->properties[$property] = $value;
            }
        }
    }

    /**
     * returns the (custom-generated) Id for this document
     *
     * This method is intend to return the (custom-generated) Id for this document.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The Id of this document
     * @access protected
     */
    protected function generateId()
    {
        return $this->stringToId($this->storage->email);
    }

    /**
     * returns the type of this document
     *
     * This method is intend to return the type of this document.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The type of this document
     * @access protected
     */
    protected function getType()
    {
        return self::$type;
    }
}
