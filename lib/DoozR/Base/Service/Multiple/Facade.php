<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Base - Service - Multiple - Facade
*
* Facade.php - Generic facade for multi instance services
*
* PHP versions 5.4
*
* LICENSE:
* DoozR - The lightweight PHP-Framework for high-performance websites
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
* @category   DoozR
* @package    DoozR_Base
* @subpackage DoozR_Base_Service
* @author     Benjamin Carl <opensource@clickalicious.de>
* @copyright  2005 - 2015 Benjamin Carl
* @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
* @version    SVN: $Id$
* @link       http://clickalicious.github.com/DoozR/
*/

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Service/Multiple.php';

/**
 * DoozR - Base - Service - Multiple - Facade
 *
 * Generic facade for multi instance services
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Service
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Base_Service_Multiple_Facade extends DoozR_Base_Service_Multiple
{
    /**
     * Contains an instance of the class/object decorated
     *
     * @var object
     * @access protected
     * @static
     */
    private static $_realObject;


    /**
     * setter for decorated object
     *
     * This method is intend to act as setter for $_realObject.
     *
     * @param object $instance An instance of a class to decorate
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setRealObject($instance)
    {
        self::$_realObject = $instance;
    }

    /**
     * getter for decorated object
     *
     * This method is intend to act as getter for $_realObject.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object An instance of a class
     * @access protected
     */
    protected function getRealObject()
    {
        return self::$_realObject;
    }

    /**
     * generic facade - for all non-implemented methods
     *
     * This method is intend to act as generic facade - for all non-implemented methods
     *
     * @param string $signature The signature (name of the method) originally called
     * @param mixed  $arguments The arguments used for call (can be either an ARRAY of values or NULL)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Result of called method if exists, otherwise NULL
     * @access public
     */
    public function __call($signature, $arguments)
    {
        if ($arguments) {
            $result = call_user_func_array(
                array(self::$_realObject, $signature),
                $arguments
            );
        } else {
            $result = call_user_func(
                array(self::$_realObject, $signature)
            );
        }

        //
        return $result;
    }

    /**
     * generic facade - for all non-implemented static methods
     *
     * This method is intend to act as generic facade - for all non-implemented static methods
     *
     * @param string $signature The signature (name of the method) originally called
     * @param mixed  $arguments The arguments used for call (can be either an ARRAY of values or NULL)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Result of called method if exists, otherwise NULL
     * @access public
     * @static
     */
    public static function __callStatic($signature, $arguments)
    {
        $targetClassname = get_class(self::$_realObject);

        if ($arguments) {
            $result = call_user_func_array(
                $targetClassname.'::'.$signature,
                $arguments
            );
        } else {
            $result = call_user_func(
                array($targetClassname, $signature)
            );
        }

        //
        return $result;
    }

    /**
     * generic getter for dispatching to decorated object
     *
     * This method is intend to act as generic getter for dispatching to decorated object.
     *
     * @param string $property The property to return
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The data from property
     * @access public
     */
    public function __get($property)
    {
        if ($property != '_realObject') {
            return self::$_realObject->{$property};
        }
    }

    /**
     * generic setter for dispatching to decorated object
     *
     * This method is intend to act as generic setter for dispatching to decorated object.
     *
     * @param string $property The property to set
     * @param mixed  $value    The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The data from property
     * @access public
     */
    public function __set($property, $value)
    {
        if ($property != '_realObject') {
            return self::$_realObject->{$property} = $value;
        }
    }
}
