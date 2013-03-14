<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Base Model
 *
 * Model.php - Base class for model-layers from MV(C|P)
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
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Model
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Model/Observer.php';

/**
 * DoozR Base Model
 *
 * Base class for model-layers from MV(C|P)
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Model
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Base_Model extends DoozR_Base_Model_Observer
{
    /**
     * holds data for CRUD operation(s)
     *
     * @var mixed
     * @access protected
     */
    protected $data;

    /**
     * holds a reference to the Database (DoozR_Core::model)
     *
     * @var object
     * @access protected
     */
    protected $model;

    /**
     * contains the complete request
     *
     * @var array
     * @access protected
     */
    protected $request;

    /**
     * contains the translation for reading request
     *
     * @var array
     * @access protected
     */
    protected $translation;


    /**
     * Constructor of this class
     *
     * This method is the constructor of this class.
     *
     * @param array $request     The whole request as processed by "Route"
     * @param array $translation The translation required to read the request
     *
     * @return void
     * @access public
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __construct(array $request, array $translation)
    {
        // store original request
        $this->request = $request;
        $this->translation = $translation;

        // check for __tearup - Method (it's DoozR's __construct-like magic-method)
        if ($this->hasMethod('__tearup') && is_callable(array($this, '__tearup'))) {
            $this->__tearup($request, $translation);
        }
    }

    /**
     * called on destruction of the class
     *
     * This method is intend to call the teardown method of a model if exist
     *
     * @return  void
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function __destruct()
    {
        // check for __tearup - Method (it's DoozR's __construct-like magic-method)
        if ($this->hasMethod('__teardown') && is_callable(array($this, '__teardown'))) {
            $this->__teardown();
        }
    }

    /**
     * create
     *
     * Create of Crud
     *
     * @param mixed $data The data for create
     *
     * @return  boolean TRUE on success, otherwise FALSE
     * @access  protected
     * @abstract
     */
    protected function create($data = null)
    {
        if ($this->hasMethod('__create') && is_callable(array($this, '__create'))) {
            return $this->__create($data);
        }
    }

    /**
     * read
     *
     * Read of cRud
     *
     * @return  mixed Data on success, otherwise null
     * @access  protected
     * @abstract
     */
    protected function read()
    {
        if ($this->hasMethod('__read') && is_callable(array($this, '__read'))) {
            return $this->__read();
        }
    }

    /**
     * delete
     *
     * Delete of cruD
     *
     * @return  boolean TRUE on success, otherwise FALSE
     * @access  protected
     * @abstract
     */
    protected function delete()
    {
        if ($this->hasMethod('__delete') && is_callable(array($this, '__delete'))) {
            return $this->__delete();
        }
    }

    /**
     * returns the data for requested mode
     *
     * This method (container) is intend to return the data for a requested mode.
     *
     * @return  mixed The data for the mode requested
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * sets the data for requested mode
     *
     * This method (container) is intend to set the data for a requested mode.
     *
     * @param mixed $data The data (array prefered) to set
     *
     * @return  boolean True if everything wents fine, otherwise false
     * @access  public
     * @author  Benjamin Carl <opensource@clickalicious.de>
     */
    public function setData($data)
    {
        return ($this->data = $data);
    }
}

?>