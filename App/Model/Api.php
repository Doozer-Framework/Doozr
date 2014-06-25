<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Api - Model
 *
 * Api.php - This is an example model for demonstration purposes
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2014, Benjamin Carl - All rights reserved.
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
 * @package    DoozR_Api
 * @subpackage DoozR_Api_Model
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

/**
 * DoozR - Api - Model
 *
 * This is an example model for demonstration purposes
 *
 * @category   DoozR
 * @package    DoozR_Api
 * @subpackage DoozR_Api_Model
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
final class Model_Api extends DoozR_Base_Model implements DoozR_Base_Model_Interface
{
    /**
     * __tearup() initializes the class and get automatic called on
     * instanciation. DO NOT USE __construct (in MVC)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function __tearup()
    {
        /* maybe enrich data or something like that */
        $this->data = array(
            'users' => array(
                1234  => array(
                    'id'        => 1234,
                    'user'      => 'jdoe',
                    'firstname' => 'John',
                    'lastname'  => 'Doe',
                    'email'     => 'john.doe@test.com'
                )
            )
        );
    }

    /**
     * __data() is the generic __data proxy and is called on each access via
     * getData().
     *
     * @param DoozR_Request_Api $requestObject The default request object for APIs
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function __data($requestObject = null)
    {
        // @todo: ACL check 1st - default all operations (CRUD) possible - current access-level = 0
        $acl = DoozR_Loader_Serviceloader::load('acl');

        $model = $this;

        // get the resource which we want to use for ...
        $resource = $requestObject->get('/api/{{object}}/{{id}}', function ($object, $id) use ($model) {
            return array($model->escape($object), $model->escape($id));
        });

        //
        if (isset($this->data[$resource[0]])) {
            // get data for resource
            $data = $this->data[$resource[0]];

            // get data for specific arguments filter? ...
            //if (isset($data[$requestObject->arguments->id])) {
            //    $data = $data[$requestObject->arguments->id];
            if (isset($data[$resource[1]])) {

                $data = array(
                    'error'  => null,
                    'result' => $data[$resource[1]]
                );

            } else {
                //$data = json_decode('{"error": {"code": 1, "message": "Invalid ID: '.$requestObject->arguments->id.'"}}');
                $data = json_decode('{"error": {"code": 1, "message": "Invalid ID: '.$resource[1].'"}}');
            }

            // set data (can e.g. retrieved by controller through ->getData())
            $this->setData($data);
        } else {
            $data = json_decode('{"error": {"code": 1, "message": "Invalid resource: '.$resource[0].'"}}');
            $this->setData($data);

        }
    }


    protected function escape($string)
    {
        $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        return htmlentities($string, ENT_QUOTES, 'UTF-8');
    }


    /**
     * Observer notification
     *
     * @param SplSubject $subject The subject which notifies this observer (Presenter)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    protected function __update(SplSubject $subject)
    {
        $this->setData($subject->getData());
    }

    /**
     * magic on __teardown
     *
     * This method is intend to __cleanup
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __teardown()
    {
        /* */
    }
}
