<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr Base-Facade-Singleton-Strict
 *
 * DoozrBaseFacadeSingleton.class.php - Base-Facade-Singleton-Strict for all ...
 *
 * PHP versions 5.5
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
 *
 * Copyright (c) 2005 - 2016, Benjamin Carl - All rights reserved.
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
 *   must display the following acknowledgment: This product includes software
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
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Facade
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Class/Singleton/Strict.php';

/**
 * Doozr Base-Facade-Singleton-Strict
 *
 * Base-Facade-Singleton-Strict for all ...
 *
 * @category   Doozr
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Facade
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Base_Facade_Singleton_Strict extends Doozr_Base_Class_Singleton_Strict
{
    /**
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
        // analyze the basics of current ghost-call and define consts ...
        $this->getChild();

        // get transformer of child (calling) class
        $transformer = call_user_func(array(CHILD, 'getTransformer'));

        // get existing transformations
        $transformations = $transformer->getTransformations();

        if (isset($transformations[$signature])) {
            // transform => call => return result
            return $transformer->transform(null, $signature, $arguments);
        } else {
            trigger_error('Call to undefined function '.$signature.'()', E_USER_ERROR);
        }

        // no success
        return null;
    }

    /**
     * This method is intend to retrieve and store the children (counterpart of parent::) of this class
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function getChild()
    {
        if (false === defined('CHILD')) {
            define('CHILD', get_called_class());
        }
    }
}
