<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Configuration - Hierarchy - Kernel
 *
 * Kernel.php - The "kernel" node representation for providing auto-completion of config values.
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
 * @package    Doozr_Configuration
 * @subpackage Doozr_Configuration_Hierarchy
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

/**
 * Doozr - Configuration - Hierarchy - Kernel
 *
 * The "kernel" node representation for providing auto-completion of config values.
 *
 * @category   Doozr
 * @package    Doozr_Kernel
 * @subpackage Doozr_Kernel_Configuration
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Configuration_Hierarchy_Kernel
{
    /**
     * The caching node of the configuration.
     *
     * @var Doozr_Configuration_Hierarchy_Kernel_Caching
     * @access public
     */
    public $caching;

    /**
     * The debugging node of the configuration.
     *
     * @var Doozr_Configuration_Hierarchy_Kernel_Debugging
     * @access public
     */
    public $debugging;

    /**
     * The localization node of the configuration
     *
     * @var Doozr_Configuration_Hierarchy_Kernel_Localization
     * @access public
     */
    public $localization;

    /**
     * The logging node of the configuration
     *
     * @var Doozr_Configuration_Hierarchy_Kernel_Logging
     * @access public
     */
    public $logging;

    /**
     * The model node of the configuration
     *
     * @var Doozr_Configuration_Hierarchy_Kernel_Model
     * @access public
     */
    public $model;

    /**
     * The path node of the configuration
     *
     * @var Doozr_Configuration_Hierarchy_Kernel_Path
     * @access public
     */
    public $path;

    /**
     * The security node of the configuration
     *
     * @var Doozr_Configuration_Hierarchy_Kernel_Security
     * @access public
     */
    public $security;

    /**
     * The services node of the configuration
     *
     * @var Doozr_Configuration_Hierarchy_Kernel_Service
     * @access public
     */
    public $service;

    /**
     * The system node of the configuration
     *
     * @var Doozr_Configuration_Hierarchy_Kernel_System
     * @access public
     */
    public $system;

    /**
     * The transmission node of the configuration
     *
     * @var Doozr_Configuration_Hierarchy_Kernel_Transmission
     * @access public
     */
    public $transmission;

    /**
     * The view node of the configuration
     *
     * @var Doozr_Configuration_Hierarchy_Kernel_View
     * @access public
     */
    public $view;
}
