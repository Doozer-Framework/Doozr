<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR Base Presenter
 *
 * Presenter.php - Base class for presenter-layers from MV(C|P)
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
 * @subpackage DoozR_Base_Presenter
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id: c3b7e5d84cd534c30d7cc98a6ce6fc9a3fab1921 $
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Presenter/Subject.php';

/**
 * DoozR - Base Presenter
 *
 * Base Presenter of the DoozR Framework.
 *
 * @category   DoozR
 * @package    DoozR_Base
 * @subpackage DoozR_Base_Presenter
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id: c3b7e5d84cd534c30d7cc98a6ce6fc9a3fab1921 $
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */
class DoozR_Base_Presenter extends DoozR_Base_Presenter_Subject
{
    /**
     * holds data for CRUD operation(s)
     *
     * @var mixed
     * @access protected
     */
    protected $data;

    /**
     * Contains the instance of model for communication
     *
     * @var DoozR_Base_Model
     * @access protected
     */
    protected $model;

    /**
     * Contains the instance of view for cummunication
     *
     * @var DoozR_Base_View
     * @access protected
     */
    protected $view;

    /**
     * Contains the instance of config
     *
     * @var DoozR_Config
     * @access protected
     */
    protected $config;

    /**
     * contains the complete request
     *
     * @var array
     * @access protected
     */
    protected $request;

    /**
     * The original unmodified request as array
     *
     * @var array
     * @access protected
     */
    protected $originalRequest;

    /**
     * contains the translation for reading request
     *
     * @var array
     * @access protected
     */
    protected $translation;


    /**
     * This method is the constructor of this class.
     *
     * @param array                  $request         The whole request as processed by "Route"
     * @param array                  $translation     The translation required to read the request
     * @param array                  $originalRequest The original untouched request
     * @param DoozR_Config_Interface $config          The DoozR main config instance
     * @param DoozR_Base_Model       $model           The model to communicate with backend (db)
     * @param DoozR_Base_View        $view            The view to display results
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __construct(
        array $request,
        array $translation,
        array $originalRequest,
        DoozR_Config_Interface $config = null,
        DoozR_Base_Model $model = null,
        DoozR_Base_View $view = null
    ) {
        // store
        $this->request         = $request;
        $this->translation     = $translation;
        $this->originalRequest = $originalRequest;
        $this->config          = $config;
        $this->model           = $model;
        $this->view            = $view;

        // check for __tearup - Method (it's DoozR's __construct-like magic-method)
        if ($this->hasMethod('__tearup') && is_callable(array($this, '__tearup'))) {
            $this->__tearup($this->request, $this->translation);
        }
    }

    /**
     * This method is intend to call the teardown method of a model if exist
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function __destruct()
    {
        // check for __tearup - Method (it's DoozR's __construct-like magic-method)
        if ($this->hasMethod('__teardown') && is_callable(array($this, '__teardown'))) {
            $this->__teardown();
        }
    }

    /**
     * Create of Crud
     *
     * @param mixed $data The data for create
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access protected
     */
    protected function create($data = null)
    {
        if ($this->hasMethod('__create') && is_callable(array($this, '__create'))) {
            return $this->__create();
        }

        // notify observers about new data
        $this->notify();
    }

    /**
     * Read of cRud
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Data on success, otherwise null
     * @access protected
     */
    protected function read()
    {
        if ($this->hasMethod('__read') && is_callable(array($this, '__read'))) {
            return $this->__read();
        }

        // notify observers about new data
        $this->notify();
    }

    /**
     * Delete of cruD
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access protected
     */
    protected function delete()
    {
        if ($this->hasMethod('__delete') && is_callable(array($this, '__delete'))) {
            return $this->__delete();
        }

        // notify observers about new data
        $this->notify();
    }

    /**
     * This method (container) is intend to return the data for a requested mode.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The data for the mode requested
     * @access public
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * This method (container) is intend to set the data for a requested mode.
     *
     * @param mixed $data The data (array prefered) to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean True if everything wents fine, otherwise false
     * @access public
     */
    public function setData($data)
    {
        $this->data = $data;

        // notify observers about new data
        $this->notify();
    }
}

?>
