<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Base - Presenter - Rest
 *
 * Rest.php - Base class for presenter-layers from MV(C|P) with REST support
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
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Presenter.php';

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
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Base_Presenter_Rest extends DoozR_Base_Presenter
{
    /**
     * The rest service
     *
     * @var DoozR_Rest_Service
     * @access protected
     */
    protected $rest;


    /**
     * Constructor.
     *
     * @param array                  $request         The whole request as processed by "Route"
     * @param array                  $translation     The translation required to read the request
     * @param array                  $originalRequest The original untouched request
     * @param DoozR_Config_Interface $config          The DoozR main config instance
     * @param DoozR_Base_Model       $model           The model to communicate with backend (db)
     * @param DoozR_Base_View        $view            The view to display results
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Base_Presenter_Rest
     * @access public
     */
    public function __construct(
        array                  $request,
        array                  $translation,
        array                  $originalRequest,
        DoozR_Config_Interface $config          = null,
        DoozR_Base_Model       $model           = null,
        DoozR_Base_View        $view            = null
    ) {
        // Init REST layer/service => only difference to DoozR_Base_Presenter
        $this->rest = DoozR_Loader_Serviceloader::load('rest', $originalRequest, count($request));

        // forward call
        parent::__construct($request, $translation, $originalRequest, $config, $model, $view);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Getter for rest
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Rest_Service The rest service instance
     * @access public
     */
    public function getRest()
    {
        return $this->rest;
    }

    /**
     * Setter for rest
     *
     * @param DoozR_Rest_Service $rest A rest service instance to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Rest_Service The rest service instance
     * @access public
     */
    public function setRest(DoozR_Rest_Service $rest)
    {
        return $this->rest = $rest;
    }
}
