<?php
$dbs='localhost';
$dbu='webcarumba';
$dbp='6Fasj6FQ7d';
$dbn='carumba';


// параметры из URL
$params = preg_replace( '/^[\/]|[\/]$/', '', $_SERVER['REQUEST_URI'] );
$params = explode( '/', $params );

// директория Smarty
define('SMARTY_DIR', '../smarty/' );

// параметры таюлиц БД
define('DB_PREFIX', 'pm_');
$db_users = DB_PREFIX.'users';
$db_task = DB_PREFIX.'task';


define('PER_PAGE', 10);
define('DATE_FORMAT', 'd.m.Y | <b>H:i</b>');
 

?>