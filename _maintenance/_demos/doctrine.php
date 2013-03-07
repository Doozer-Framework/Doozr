<?php
/*
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
 */

// include DoozR core
require_once 'Controller/Core/Core.php';

// instanciate DoozR core
$DoozR = DoozR_Core::getInstance('');

// check doctrine
//$doctrine = $DoozR->getModuleHandle('doctrine');

// The first time the connection is needed, it is instantiated
// This query triggers the connection to be created

// getting a handle on db woth doctrine differs a littlebit from default driver!
$doctrine_connection = $DoozR->getModuleHandle('db')->getConnection();


/*
$options = array();
$options['setBaseClassPrefix'] = 'Model_';
$options['baseClassesDirectory'] = 'base';
Doctrine::generateModelsFromDb('models', array(), $options);
*/


Doctrine::generateModelsFromDb($DoozR->getPathBase() . 'model/database_model' , array('doctrine'), array('generateTableClasses' => true));




//$doctrine_connection->getTable('category');
/*
// no we can operate on doctrine
$stmt = $doctrine_connection->prepare('SELECT * FROM category');

$stmt->execute();
$results = $stmt->fetchAll();
echo '<pre>';
print_r($results);
echo '</pre>';
*/


?>