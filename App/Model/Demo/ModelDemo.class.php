<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Demo - Model
 *
 * ModelDemo.class.php - This is an example model for demonstration purposes
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
 * @package    DoozR_Demo
 * @subpackage DoozR_Demo_Model
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

/**
 * DoozR - Demo - Model
 *
 * This is an example model for demonstration purposes
 *
 * @category   DoozR
 * @package    DoozR_Demo
 * @subpackage DoozR_Demo_Model
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @author     $LastChangedBy$
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
final class Model_Demo extends DoozR_Base_Model implements DoozR_Base_Model_Interface
{
    /**
     * initializes the class
     *
     * __init initializes the class and get automatic called on
     * instanciation. DO NOT USE __construct (in MVC)
     *
     * @return  void
     * @access  protected
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    protected function __init()
    {
        /*
        pre(
            '__init() in '.__CLASS__.' called! :: '.__CLASS__.' does know object: '.$this->object.
            ' and the action '.$this->action
        );
        */
    }


    /**
     * action-method for action = Screen
     *
     * This method is intend to return data for the action show.
     *
     * @return  array data for requested action
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function Screen()
    {
        $data = array(
            'title'     => 'Demo Headline for Template assigned in Demo-MODEL',
            'text_nr_1' => 'Nun ergänze ich die hier im Model enthaltenen Daten ... - time() = '.time()
        );

        // set data (can e.g. retrieved by controller through ->getData())
        $this->setData($data);
    }


    /**
     * magic on __cleanup
     *
     * This method is intend to __cleanup
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     * @since   Method available since Release 1.0.0
     * @version 1.0
     */
    public function __destroy()
    {
        /*
        pre(
            '__destroy() in '.__CLASS__.' called! :: '.__CLASS__.' does know object: '.$this->object.
            ' and the action '.$this->action
        );
        */
    }
}

?>
