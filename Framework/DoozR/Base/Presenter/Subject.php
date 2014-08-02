<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Base Presenter Subject
 *
 * Subject.php - Base subject-template for "Presenter" build (MVP pattern)
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
 * @subpackage DoozR_Base_Presenter
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/State/Container.php';

/**
 * DoozR Base Presenter Subject
 *
 * Base subject-template for "Presenter" build (MVP pattern)
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Presenter
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
abstract class DoozR_Base_Presenter_Subject extends DoozR_Base_State_Container implements SplSubject
{
    /**
     * Contains all attached observers
     *
     * @var SplObjectStorage
     * @access protected
     */
    protected $observer;


    /**
     * Constructor override for SplObjectStorage instantiation.
     *
     * @param DoozR_Base_State_Interface $stateObject The state object
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Base_Presenter_Subject Subject
     * @access public
     */
    public function __construct(DoozR_Base_State_Interface $state)
    {
        $this->observer = new SplObjectStorage();

        parent::__construct($state);
    }

    /**
     * Attaches a new observer instance
     *
     * This method is intend to register a new observer instance.
     *
     * @param SplObserver $observer The observer instance to attach
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function attach(SplObserver $observer)
    {
        $this->observer->attach($observer);
    }

    /**
     * Detaches an observer
     *
     * This method is intend to detach an observer
     *
     * @param SplObserver $observer The observer instance to remove
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function detach(SplObserver $observer)
    {
        $this->observer->detach($observer);
    }

    /**
     * Notifies all registered observers about an update
     *
     * This method is intend to notify all registered observers about an update.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function notify()
    {
        // iterate the observer within the collection ...
        foreach ($this->observer as $observer) {
            // ... and trigger update
            $observer->update($this);
        }
    }
}
