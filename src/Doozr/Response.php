<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Response.
 *
 * Response.php - Response implementation of Doozr.
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
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Response.php';
require_once DOOZR_DOCUMENT_ROOT.'Doozr/Response/Interface.php';

/**
 * Doozr - Response.
 *
 * Response implementation of Doozr.
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Response extends Doozr_Base_Response
    implements
    Doozr_Response_Interface
{
    /**
     * The Type of the Response
     * Can be one of: Cli, Web.
     *
     * @var string
     */
    protected $type;

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for data.
     *
     * @param string $data The data.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setData($data)
    {
        $this->getStateObject()->setData($data);
    }

    /**
     * Fluent: Setter for data.
     *
     * @param string $data The data.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function data($data)
    {
        $this->setData($data);

        return $this;
    }

    /**
     * Getter for data.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Response_Body The data if set, otherwise NULL
     */
    public function getData()
    {
        return $this->getStateObject()->getData();
    }

    /*------------------------------------------------------------------------------------------------------------------
    | INTERNAL API
    +-----------------------------------------------------------------------------------------------------------------*/
    /*------------------------------------------------------------------------------------------------------------------
    | FULFILL: @see Doozr_Response_Interface
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Generic output method.
     *
     * @param bool $exit TRUE to exit execution directly, otherwise FALSE to continue run after data sent.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function send($exit = true)
    {
        echo $this->getData();

        if (true === $exit) {
            exit;
        }
    }

    /**
     * Setter for type.
     *
     * @param string $type The type.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    protected function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Fluent: Setter for type.
     *
     * @param string $type The type.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function type($type)
    {
        $this->setType($type);

        return $this;
    }

    /**
     * Getter for type.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string Type of the response Cli, Web, ...
     */
    public function getType()
    {
        return $this->type;
    }
}
