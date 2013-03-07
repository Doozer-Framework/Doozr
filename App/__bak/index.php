<?php

/**
 * include DoozR bootstrapper
 */
require_once '../Framework/Core/DoozR.bootstrap.php';

/**
 * instanciate DoozR
 */
/*
$DoozR = DoozR_Core::getInstance(
    array(
        'app_root' => 'D:\\Programme\\xampp\\htdocs\\DoozR\\App\\'
    )
);
*/
//pre(DoozR_Datetime::getMicrotimeDiff(DoozR_Core::$starttime));

$DoozR = DoozR_Core::getInstance();


// just a simple "i'm alive message..."
//echo 'index.php called<br />';
/*
for ($intI = 0; $intI < 2; ++$intI) {

    $password = DoozR_Core::module('password');

	pre($password->generate(
	        DoozR_Password::PASSWORD_ALPHANUM_SPECIAL_HARDCORE,
	        128
	));
}
*/

// retrieve model (Doctrine in this case)
$model = DoozR_Core::model();


//pred($_GET->abc());
// test DB operation - create and insert
//$model->export->createTable('test', array('name' => array('type' => 'string')));
//$model->execute('INSERT INTO test (name) VALUES (?)', array('jwage'));

//pred($model);

// test DB operation - fetch data
$sql = $model->prepare('SELECT * FROM user');
$sql->execute();
$results = $sql->fetchAll();
pred($results);

// dummy out the model (object)
pred('DB should be accessed now!');

?>
