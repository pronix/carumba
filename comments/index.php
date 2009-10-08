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

$userData = $autMgr->getUserData($userID);
$smarty->assign('userData', $userData);

if (!empty($_POST['action'])) action();

$from = 0;
$per_page = 15;
$cond = '';
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $cond = 'WHERE c.sID='.$_GET['id'];
    $smarty->assign('id', $_GET['id']);
}

$page_line = '';
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}
$db->query("SELECT COUNT(c.sID) as count FROM pm_comments c $cond");
$db->fetch();
$page_line = get_page_line($page, $per_page, $db->data->count, '/comments/'.(isset($_GET['id'])&&is_numeric($_GET['id'])?'?id='.$_GET['id']:''));
$from = ($page-1) * $per_page;

/*
ob_start();
print_r($_POST);
$smarty->assign('message', nl2br(str_replace(' ', '&nbsp;', ob_get_contents()) ) );
ob_end_clean();
*/
$com_list = array();
$sQuery = "SELECT c.*, s.Title FROM pm_comments c LEFT JOIN pm_structure s ON (c.sID = s.sID) $cond ORDER BY date DESC LIMIT $from, $per_page";
$db->query($sQuery);
//print_r($sQuery);
while ($db->fetch()) {
    $db->data->date = date('d-m-Y (H:i)', $db->data->date);
    $db->data->comment = nl2br(htmlspecialchars(substr($db->data->comment, 0, 1024)));
    $com_list[$db->data->cID] = $db->data;
}

// Добавление путей к товарам
$aPathCache = array();
foreach ($com_list as &$v) {
    $id = $v->sID;
    if (isset($aPathCache[$id])) {
        $v->path = $aPathCache[$id];
        continue;
    }
    $aPath = array();
    while ($id) {
        $db->query("SELECT pms_sID, URLName FROM pm_structure WHERE sID=$id LIMIT 1");
        $db->fetch();
        $id = $db->data->pms_sID;
        $aPath[] = $db->data->URLName;
    }
    $aPath = array_reverse($aPath);
    $v->path = 'http://'.$_SERVER['HTTP_HOST'].implode('/', $aPath);
    $aPathCache[$v->sID] = $v->path;
}

$smarty->assign('com_list', $com_list);
$smarty->assign('pages', $page_line);

$smarty->display('main.html');

print '<!-- Generated in '.round(get_microtime() - $stime, 3).' sec -->';
mysql_close();

?>