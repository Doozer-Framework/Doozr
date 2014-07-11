<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Base View Rest
 *
 * Rest.php - Base View Rest of the DoozR Framework.
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
 * Please feel free to contact us via e-mail: <opensource@clickalicious.de>
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_View
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/View.php';
require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/View/Interface.php';

/**
 * DoozR - Base View Rest
 *
 * Base View Rest of the DoozR Framework.
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_View
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Base_View_Rest extends DoozR_Base_View implements DoozR_Base_View_Interface
{
    /**
     * This method is the magic renderer von View = Main.
     * Upon creating this method gets automagically called when data
     * is set to view via setData(). And we always deliver JSON so
     * its really simple.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __renderMain()
    {
        // Get response
        /* @var $response DoozR_Response_Web */
        $response = $this->front->getResponse();

        // Custom default header configured?
        try {
            $headers = $this->config->transmission->header();
        } catch (Exception $e) {
            $headers = null;
        }

        // Send configured header
        foreach ($headers as $type => $header) {
            $response->sendHeader($header);
        }

        $data = $this->getData();

        if (isset($data->error)) {
            $response->sendHttpStatus(400, null, true, $data->error->message);

        } else {
            // Send our data as HTML through response
            $response->sendJson($this->getData(), $this->getFingerprint(1));
        }
    }

    /**
     * Maybe a bit spooky but a good solution to get data into this part of the
     * MVP structure.
     *
     * @param SplSubject $subject The subject which is automatically dispatched
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function __update(SplSubject $subject)
    {
        // store data internal and call renderer!
        $this->setData( $subject->getData(), true );
    }
}
