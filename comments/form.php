<?php
// Подключение модулей
require_once('../_pm/_kernel.config.php');
require_once('class.sql.php');
require_once('../_pm/class.authenticationmgr.php');
require_once('function.inc.php');

// засекаем время
$stime = get_microtime();

// Установить обработчик ошибок
error_reporting(E_ERROR | E_USER_ERROR | E_USER_NOTICE | E_USER_WARNING | E_COMPILE_ERROR | E_PARSE);
set_error_handler('userError', E_ERROR | E_WARNING | E_USER_ERROR | E_USER_NOTICE | E_USER_WARNING | E_COMPILE_ERROR);


$db = new sql($CFG["mysql.host"], $CFG["mysql.username"], $CFG["mysql.password"], $CFG["mysql.dbname"]);
mysql_connect($CFG["mysql.host"], $CFG["mysql.username"], $CFG["mysql.password"]);
mysql_select_db($CFG["mysql.dbname"]);


// директория Smarty
define('SMARTY_DIR', '../smarty/' );
require_once(SMARTY_DIR.'Smarty.class.php');

$smarty = new Smarty();


// Аутентификация
$autMgr = new AuthenticationManager();

$userID = $autMgr->getUserID();
$userGroup = $autMgr->getUserGroup();

if ( ($userID == 1) OR ($userGroup != 5)) {
    $autMgr->endSession();
    header('location: /login');
    exit();
}

if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $comment = $_POST['comment'];
    $db->query("UPDATE pm_comments SET name='$name', email='$email', comment='$comment' WHERE cID='$id' LIMIT 1");
}


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
} else {
    die('bad aruments');
}

$db->query("SELECT * FROM pm_comments WHERE cID = $id LIMIT 1");
$db->fetch();

$smarty->assign('id',$db->data->cID);
$smarty->assign('name',$db->data->name);
$smarty->assign('email',$db->data->email);
$smarty->assign('comment',$db->data->comment);

$smarty->display('form.html');

print '<!-- Generated in '.round(get_microtime() - $stime, 3).' sec -->';
mysql_close();
?>