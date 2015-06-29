<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service
 *
 * Session.php - Session based storage.
 *
 * PHP versions 5.5
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
 *
 * Copyright (c) 2005 - 2015, Benjamin Carl - All rights reserved.
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
 * @category   Doozr
 * @package    Doozr_Service
 * @subpackage Doozr_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Service/Doozr/Form/Service/Store/Abstract.php';
require_once DOOZR_DOCUMENT_ROOT . 'Service/Doozr/Form/Service/Store/Interface.php';

/**
 * Doozr - Form - Service
 *
 * Session based storage.
 *
 * @category   Doozr
 * @package    Doozr_Service
 * @subpackage Doozr_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Form_Service_Store_Session extends Doozr_Form_Service_Store_Abstract
    implements
    Doozr_Form_Service_Store_Interface
{
    /**
     * The object we operate on
     *
     * @var Doozr_Session_Service
     * @access protected
     */
    protected $subject;


    /**
     * Constructor.
     *
     * @param Doozr_Session_Service $session The session to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Form_Service_Store_Session $this
     * @access public
     */
    public function __construct(Doozr_Session_Service $session)
    {
        $this->subject = $session;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Creates an entry in store.
     *
     * @param string $key   The key for the data to store
     * @param mixed  $value The value to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success, otherwise FALSE
     * @access public
     */
    public function create($key, $value)
    {
        return $this->subject->create($key, $value);
    }

    /**
     * Reads an entry from store.
     *
     * @param string $key   The key for the data to store
     * @param mixed  $value The value to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed|null The value if set, otherwise NULL
     * @access public
     */
    public function read($key = null)
    {
        return $this->subject->read($key);
    }

    /**
     * Updates an entry in store.
     *
     * @param string $key   The key for the data to store
     * @param mixed  $value The value to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success, otherwise FALSE
     * @access public
     */
    public function update($key, $value)
    {
        return $this->subject->update($key, $value);
    }

    /**
     * Deletes an entry from store.
     *
     * @param string $key The key to delete
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE on success, otherwise FALSE
     * @access public
     */
    public function delete($key)
    {
        return $this->subject->delete($key);
    }
}
