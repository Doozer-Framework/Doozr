<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Demo - Model
 *
 * Demo.php - This is an example model for demonstration purposes
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
 * @version    Git: $Id: 6b6c7dbe66d52ea11fe4e72020b7b05aa129261f $
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

// the Person class
class Person {
    public $name;
    public $phone;

    function Person($name, $phone) {
        $this->name = $name;
        $this->phone = $phone;
    }
}

/**
 * DoozR - Demo - Model
 *
 * This is an example model for demonstration purposes
 *
 * @category   DoozR
 * @package    DoozR_Demo
 * @subpackage DoozR_Demo_Model
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id: 6b6c7dbe66d52ea11fe4e72020b7b05aa129261f $
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
final class Model_Demo extends DoozR_Base_Model implements DoozR_Base_Model_Interface
{
    /**
     * __tearup initializes the class and get automatic called on
     * instanciation. DO NOT USE __construct (in MVC)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function __tearup()
    {
        // let's create an array of objects for test purpose
        $people = array();
        $people[] = new Person("foo", "01-344-121-021");
        $people[] = new Person("bar", "05-999-165-541");
        $people[] = new Person("baz", "01-389-321-024");
        $people[] = new Person("quz", "05-321-378-654");

        $data = array(
            'title'  => 'This is a demonstration headline (e.g. h1)!',
            'people' => $people
        );

        // set data (can e.g. retrieved by controller through ->getData())
        $this->setData($data);
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
        /*...*/
    }
}

?>
