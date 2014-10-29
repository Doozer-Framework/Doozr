<?php
/***********************************************************************************************************************
 *
 * DEMONSTRATION
 * Service: Compact (Demo of compacting resources like CSS, JS + HTML)
 * Example call to this demonstration
 * [e.g. http://127.0.0.1:81/Projekte/DoozR...]/App/compact.php?f=js/vendor/jquery-1.10.1.min.js,js/vendor/less-1.3.3.js
 *
 **********************************************************************************************************************/

/**
 * Bootstrap
 */
require_once '../Framework/DoozR/Bootstrap.php';


/**
 * Instantiate DoozR
 */
$DoozR = DoozR_Core::getInstance();


/**
 * Get registry containing DoozR's base object instances
 */
$registry = DoozR_Registry::getInstance();


/**
 * Get module API from Serviceloader
 */
$compact = DoozR_Loader_Serviceloader::load('compact');


/**
 * Allow use of the Minify URI Builder app. Only set this to true while you need it.
 **/
$min_enableBuilder                        = true;
$min_errorLogger                          = false;
$min_allowDebugFlag                       = false;
$min_cacheFileLocking                     = true;
$min_serveOptions['bubbleCssImports']     = false;
$min_serveOptions['maxAge']               = 1800;
$min_serveOptions['minApp']['groupsOnly'] = false;
$min_symlinks                             = array();
$min_uploaderHoursBehind                  = 0;


$_SERVER['DOCUMENT_ROOT'] = DOOZR_APP_ROOT.'Data/Public/www/view/assets';

DoozR_Compact_Service::$isDocRootSet = true;

DoozR_Compact_Service::setCache(isset($min_cachePath) ? $min_cachePath : '',$min_cacheFileLocking);

DoozR_Compact_Service::serve(new Minify_Controller_MinApp(), $min_serveOptions);
