<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr Base Presenter Subject
 *
 * Subject.php - Base subject-template for "Presenter" build (MVP pattern)
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
 * Please feel free to contact us via e-mail: <opensource@clickalicious.de>
 *
 * @category   Doozr
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Presenter
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/State/Container.php';
require_once DOOZR_DOCUMENT_ROOT . 'Doozr/Base/Subject/Interface.php';

/**
 * Doozr Base Presenter Subject
 *
 * Base subject-template for "Presenter" build (MVP pattern)
 *
 * @category   Doozr
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Presenter
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
abstract class Doozr_Base_Presenter_Subject extends Doozr_Base_State_Container
    implements
    Doozr_Base_Subject_Interface
{
    /**
     * Identifier of the observer.
     *
     * @var string
     * @access protected
     */
    protected $identifier = self::IDENTIFIER_PRESENTER;

    /**
     * Contains all attached observers
     *
     * @var Doozr_Base_Observer_Interface[]
     * @access protected
     */
    protected $observer;

    /**
     * Store for data.
     *
     * @var array
     * @access protected
     */
    protected $store = [];

    /*------------------------------------------------------------------------------------------------------------------
    | Init
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor override for SplObjectStorage instantiation.
     *
     * @param Doozr_Base_State_Interface $state The state object
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Doozr_Base_Presenter_Subject Subject
     * @access public
     */
    public function __construct(Doozr_Base_State_Interface $state)
    {
        $this->observer = new SplObjectStorage();
        parent::__construct($state);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | SplSubject
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Notifies all registered observers about an update
     *
     * This method is intend to notify all registered observers about an update.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed|null The result of view or NULL
     * @access public
     */
    public function notify()
    {
        // Iterate the observer within the collection ...
        foreach ($this->observer as $observer) {
            $this->storeData($observer->getIdentifier(), $observer->update($this));
        }

        return $this->getStore(self::IDENTIFIER_VIEW);
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

    /*------------------------------------------------------------------------------------------------------------------
    | Setter & Getter, Isser & Hasser
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for identifier.
     *
     * @param string $identifier Identifier to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * Setter for identifier.
     *
     * @param string $identifier Identifier to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function identifier($identifier)
    {
        $this->setIdentifier($identifier);

        return $this;
    }

    /**
     * Getter for identifier.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }


    /**
     * Setter for store.
     *
     * @param array $store The store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setStore(array $store)
    {
        $this->store = $store;
    }

    /**
     * Fluent: Setter for store.
     *
     * @param array $store The store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function store(array $store)
    {
        $this->setStore($store);

        return $this;
    }

    /**
     * Setter for store.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Store
     * @access protected
     */
    protected function getStore()
    {
        return $this->store;
    }

    /**
     * Stores data by key.
     *
     * @param string $source The key
     * @param mixed  $value  The value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function storeData($source, $value)
    {
        $this->store[$source] = $value;
    }

    /**
     * Returns the data from internal store.
     *
     * @param string|null $source The source to return, or NULL for whole store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The data from store
     * @access public
     * @throws \Doozr_Base_Presenter_Exception
     */
    public function getStoredData($source = null)
    {
        if (null === $source) {
            $result = $this->store;

        } else {
            if (false === isset($this->store[$source])) {
                throw new Doozr_Base_Presenter_Exception(
                    sprintf('No stored data for source (key) "%s"', $source)
                );
            }

            $result = $this->store[$source];
        }

        return $result;
    }
}
