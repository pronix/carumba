<?php
require_once('../_pm/_kernel.config.php');
require_once('../_pm/class.authenticationmgr.php');

// подключаем Smarty
define('SMARTY_DIR', '../smarty/');
require_once(SMARTY_DIR.'Smarty.class.php');
$smarty = new Smarty();

// соединяемся с БД
mysql_connect($CFG['mysql.host'], $CFG['mysql.username'], $CFG['mysql.password']);
mysql_select_db($CFG['mysql.dbname']);
mysql_query("SET NAMES 'cp1251'");
mysql_query("SET CHARACTER SET cp1251");

// Авторизация
$autMgr = new AuthenticationManager();

$userID = $autMgr->getUserID();
$userGroup = $autMgr->getUserGroup();

if ($userGroup != 5 && $userGroup != 4) {
    $autMgr->endSession();
    header('location: /login');
    exit();
}

define('PER_PAGE', 10);
?>