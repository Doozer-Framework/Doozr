<?php
/**
 * retrieve path to user's (App) DoozR - config(s)
 */
require_once 'include/pathfinder.include.php';

// require the bootstrapper for DoozR
require_once '../Framework/Core/DoozR.bootstrap.php';

// get an instance of DoozR
$DoozR = DoozR_Core::getInstance();

$dbHandle = DoozR_Core::model();

/**
 * SELECT
 */
$contaier = $dbHandle->getContainer('TABLE NAME');
$rows = $container->findByUsernameAndHash($username, $hash, true);
$rows = $container->findOOneByUsernameAndHash($username, $hash, true);
$rows = $container->findAll();

/*
 * SELECT MIT JOIN
 */
GormDB_Database::query()
    ->select('user_session')
    ->from('user')
    ->leftJoin('user_role')
    ->on('user.id = user_role.id')
    ->leftJoin('user_role')
    ->on('user.id = user_role.id')
    ->fetch(true);

/**
 * REPLACE
 */
GormDB_Database::query()
    ->replace('user_session')
    ->set('user_id', $result[0]['id'])
    ->set('session_id', 'session_id')
    ->set('remote_addr', '127.0.0.1')
    ->set('login_date', 'now()')
    ->set('last_access_date', 'now()')
    ->store();

/**
 * INSERT
 */
GormDB_Database::query()
    ->insert('user_session')
    ->set('user_id', $result[0]['id'])
    ->set('session_id', 'session_id')
    ->set('remote_addr', '127.0.0.1')
    ->set('login_date', 'now()')
    ->set('last_access_date', 'now()')
    ->store();
    
/**
 * DELETE
 */
GormDB_Database::query()
    ->delete()
    ->where('id = 10')
    ->store();
    
/**
 * UPDATE
 */
GormDB_Database::query()
    ->update('user')
    ->set('hash', 'test')
    ->where('id', '=', 18)
    ->store();
    
    
    
    
    
    
    
    
/**
 * USER ANLEGEN
 */
$result = GormDB_Database::query()
    ->insert('user')
    ->set('user', 'popelpopelpopel')
    ->set('hash', 'md5(Salt(\'popelpopelpopel\'))')
    ->set('contact_id', 30)
    ->store();
    
    pre($result->getInsertId());
die();

//$sql = "
//        SELECT
//        `user`.id,
//        `user`.hash,
//        MD5(Salt('$password')) AS hash2
//        FROM
//        `user`
//        WHERE
//        `user`.user = '$login'
//        AND
//        `user`.hash = MD5(Salt('$password'))
//        HAVING
//        hash = hash2
//        ";

/**
 * LOGIN
 */
pre('INHALT USER:');
$result = GormDB_Database::query()
    ->select('id')
    ->select('hash')
    ->select('MD5(Salt(\'upside\')) AS hash2')
    ->from('user')
    ->where('user', '=', 'robert_barth', 'AND')
    ->where('hash', '=', 'md5(Salt(\'upside\'))')
    ->having('hash = hash2')
    ->fetch(true);

pre($result);

if(empty($result)) {
    pre('NO USER FOUND');
}

//$sql2 = "
//    SELECT
//    user_session.session_id,
//    user_session.remote_addr,
//    user_session.login_time,
//    user_session.last_access
//    FROM
//    user_session
//    WHERE
//    user_session.user_id = $user_id
//    ";
pre('INHALT SESSION:');
$sessionResult = GormDB_Database::query()
    ->select('session_id')
    ->select('remote_addr')
    ->select('login_date')
    ->select('last_access_date')
    ->from('user_session')
    ->where('user_id', '=', $result[0]['id'])
    ->fetch(true);    // true = array

pre($sessionResult);

if(!empty($sessionResult)) {
    pre('USER DIDNT LOGOUT SUCCESSFULL LAST TIME');
}

//$sql_set = "
//    user_id = {$row['id']},
//    session_id = '$session_id',
//    remote_addr = '$remote_addr',
//    login_time = NOW(),
//    last_access = NOW()
//    ";
//
//$sql = "
//    INSERT INTO
//    user_session
//    SET $sql_set
//    ON DUPLICATE KEY
//    UPDATE $sql_set
//    ";

GormDB_Database::query()
    ->replace('user_session')
    ->set('user_id', $result[0]['id'])
    ->set('session_id', 'session_id')
    ->set('remote_addr', '127.0.0.1')
    ->set('login_date', 'now()')
    ->set('last_access_date', 'now()')
    ->store();


