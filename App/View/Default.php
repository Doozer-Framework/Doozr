<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Default - View
 *
 * Default.php - Default View Demonstration
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
 * @package    DoozR_Default
 * @subpackage DoozR_Default_View
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

/**
 * DoozR - Default - View
 *
 * Default View Demonstration
 *
 * @category   DoozR
 * @package    DoozR_Default
 * @subpackage DoozR_Default_View
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
final class View_Default extends DoozR_Base_View implements DoozR_Base_View_Interface
{
    /**
     * called at initialization
     *
     * This method is the replacement for construct. It is called right on construction of
     * the class-instance. It retrieves all arguments 1:1 as passed to constructor.
     *
     * @param array $request     The original request
     * @param array $translation The translation to read the request
     *
     * @return  void
     * @access  protected
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    protected function __tearup(array $request, array $translation)
    {
        pre(
            '__tearup() in '.__CLASS__.' called! :: '.__CLASS__.' start processing of: '.var_export($request, true).
            ' translation: '.var_export($translation, true)
        );

        /******/
        /* @var $model DoozR_Model */
        /*
        $model = DoozR_Model::getInstance();

        // get connection
        $connection = $model->phpillowConnection->___createInstance('localhost', 5984);

        // set database
        $model->phpillowConnection->setDatabase('doozr');

        // get base structure/object for documents
        require_once 'App/myBlogDocument.php';

        // create new doc
        $doc = new myBlogDocument();
        $doc->title = 'New blog post 2';
        $doc->text = 'Hello world.';
        $doc->save();
        */
        /******/
    }

    /**
     * called on destruction
     *
     * This method is the replacement for construct. It is called right on construction of
     * the class-instance. It retrieves all arguments 1:1 as passed to constructor.
     *
     * @return  void
     * @access  protected
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function __teardown()
    {
        pre(
            '__teardown() in '.__CLASS__.' called! :: '.__CLASS__
        );
    }

    /**
     * automatic data presentation demo method
     *
     * This method is intend to demonstrate how data could be automatic
     * be displayed.
     *
     * @return  boolean True if successful, otherwise false
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function screen()
    {
        pred('got it!');

        /*
        // retrieve data for context Screen
        $data = $this->getData();

        // just a simple - we automatic show data from model function
        if (is_array($data)) {
            $data = var_export($data, true);
        }

        // get pre - html
        $text = pre(__CLASS__.' proudly present: '.$data.' directly from Model :) through Response ...', true);

        // deliver the HTML code through response
        return DoozR_Core::front()->getResponse()->sendText($text);
        */
    }
}

?>
