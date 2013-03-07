<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doodi - CouchDB - Document - MyBlogDocument
 *
 * MyBlogDocument.class.php - CouchDB - Document - MyBlogDocument
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2013, Benjamin Carl - All rights reserved.
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
 * @category   DoozR
 * @package    DoozR_App
 * @subpackage DoozR_App_Model
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Model/Doodi/Lib/Container/Couchdb/Bootstrap.php';

/**
 * Doodi - CouchDB - Document - MyBlogDocument
 *
 * CouchDB - Document - MyBlogDocument
 *
 * @category   DoozR
 * @package    DoozR_App
 * @subpackage DoozR_App_Model
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class My_Blog_Document extends Doodi_Couchdb_Document
{
    /**
     * the type of this document
     *
     * @var string
     * @access protected
     */
    protected static $type = 'blog_entry';

    /**
     * the required properties of this document
     *
     * @var array
     * @access protected
     */
    protected $requiredProperties = array(
        'title',
        'text'
    );


    /**
     * constructor
     *
     * This method is intend to construct the class.
     *
     * @return  object Instance of this class
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function __construct()
    {
        // defines the available properties
        $this->properties = array(
            'title'    => new Doodi_Couchdb_String_Validator(),
            'text'     => new Doodi_Couchdb_Text_Validator(),
            'comments' => new Doodi_Couchdb_Document_Array_Validator('myBlogComments')
        );

        // call parents constructor
        parent::__construct();
    }


    /**
     * returns the (custom-generated) Id for this document
     *
     * This method is intend to return the (custom-generated) Id for this document.
     *
     * @return  string The Id of this document
     * @access  protected
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    protected function generateId()
    {
        return $this->stringToId($this->storage->title);
    }


    /**
     * returns the type of this document
     *
     * This method is intend to return the type of this document.
     *
     * @return  string The type of this document
     * @access  protected
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    protected function getType()
    {
        return self::$type;
    }
}

?>
