<?php
// засекаем время
$stime = get_microtime();

// Установить обработчик ошибок
error_reporting(E_ERROR | E_USER_ERROR | E_USER_NOTICE | E_USER_WARNING | E_COMPILE_ERROR | E_PARSE);
set_error_handler('userError', E_ERROR | E_WARNING | E_USER_ERROR | E_USER_NOTICE | E_USER_WARNING | E_COMPILE_ERROR);

// Подключение модулей
//require_once('class/class.sql.php');
require_once('../_pm/class.authenticationmgr.php');
require_once('config.inc.php');
require_once('function.inc.php');
require_once(SMARTY_DIR.'Smarty.class.php');

mysql_connect($dbs, $dbu, $dbp);
mysql_select_db($dbn);
mysql_query("SET NAMES 'cp1251'");
mysql_query("SET CHARACTER SET cp1251");
$smarty = new Smarty();


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

/**
 * Обработка URI и выбор входных значений для отображения
 */
$aPar = explode( '/', $_SERVER['REQUEST_URI'] );

$aImportant = array('hot', 'current', 'idea', 'closed');

$aTopic = get_page_info_from_path($aPar);


/**
 * Анализ данных из форм
 *
 */
if (isset($_POST['action']) || isset($_GET['action'])) {
    do_action();
    //header('location: '.$_SERVER['HTTP_REFERER']);
    //exit();
}


//$smarty->assign('message', get_ar($aTopic));

$smarty->assign('topic', $aTopic);




/**
 * Формирование данных для меню
 */
$aMenu = get_menu();


$smarty->assign('menu', $aMenu);


/**
 * Вывод шаблонов
 */
$last_date = time() - 86400*7;
if ($aTopic['id']) {



    $res = @mysql_query("SELECT COUNT(id) FROM task WHERE sid='{$aTopic['id']}'")
    or trigger_error('mysql', E_ERROR);
    list($count) = mysql_fetch_array($res);

    $res = @mysql_query("SELECT t.*, u.Login FROM task t
                             LEFT JOIN pm_users u ON (t.userID = u.userID)
                             WHERE id={$aTopic['id']} LIMIT 1")
    or trigger_error('mysql', E_ERROR);
    $head = mysql_fetch_assoc($res);
    $head['date'] = date('d.m.Y | <b>h:i</b>', $head['date']);
    $head['title'] = stripslashes($head['title']);
    $head['message'] = nl2br( stripslashes($head['message']) );
    $smarty->assign('head', $head);

    $pages = get_page_line($aTopic['page'], PER_PAGE, $count, 'http://'.$_SERVER['HTTP_HOST'].'/task/'.$aTopic['name'].'/'.$aTopic['id'].'/');
    $smarty->assign('pages', $pages);



    $from = ($aTopic['page']-1)*PER_PAGE;
    $res = @mysql_query('SELECT t.id, t.date, t.message, u.Login FROM task t
                            LEFT JOIN pm_users u ON (t.userID = u.userID)
                            WHERE sid='.$aTopic['id'].'
                            ORDER BY date ASC LIMIT '.$from.','.PER_PAGE)
            or trigger_error(mysql_error(), E_USER_ERROR);
    $topic_list = array();
    if ( $res ) {
        while ($ar = mysql_fetch_assoc($res)) {
            if ($ar['date'] > $last_date) {
                $ar['new'] = 1;
            }
            $ar['date'] = date('d.m.Y | <b>h:i</b>', $ar['date']);
            $ar['message'] = nl2br( stripslashes($ar['message']) );
            $topic_list[]=$ar;
        }
    }

    $smarty->assign('topic_list', $topic_list);


    $smarty->display('inside.html');



} else {



    $count = $aMenu[$aTopic['imp']]['count'];

    $pages = get_page_line($aTopic['page'], PER_PAGE, $count, 'http://'.$_SERVER['HTTP_HOST'].'/task/'.$aTopic['name'].'/');
    $smarty->assign('pages', $pages);

    $from = ($aTopic['page']-1)*PER_PAGE;
    $res = mysql_query('SELECT t.id, t.date, t.title, t.important, u.Login
                            FROM task t
                            LEFT JOIN pm_users u ON (t.userID = u.userID)
                            WHERE important="'.$aTopic['imp'].'" AND sid=0
                            ORDER BY date DESC LIMIT '.$from.','.PER_PAGE)
            or trigger_error('SQL errNo='.mysql_errno(), E_USER_ERROR);
    $topic_list = array();
    if ( $res ) {
        while ($ar = mysql_fetch_assoc($res)) {
            if ($ar['date'] > $last_date) {
                $ar['new'] = 1;
            }
            $ar['date'] = date('d.m.Y | <b>h:i</b>', $ar['date']);
            $ar['title'] = stripslashes($ar['title']);
            $res2 = mysql_query('SELECT COUNT(sid) FROM task WHERE sid='.$ar['id']);
            list($ar['count'])=mysql_fetch_array($res2);
            $res2 = mysql_query('SELECT COUNT(sid) FROM task WHERE sid='.$ar['id'].' AND date > '.$last_date);
            list($ar['newcount'])=mysql_fetch_array($res2);
            $topic_list[]=$ar;
        }
    }
    $smarty->assign('topic_list', $topic_list);

    $smarty->display('main.html');




}

print '<!-- Generated in '.round(get_microtime() - $stime, 3).' sec -->';
mysql_close();




/**************************************************************
 *                      Функции
 */

    function get_microtime()
    {
        list($usec, $sec) = explode(' ', microtime());
        return (float)((float)$usec + (float)$sec);
    }

    function userError($errno, $errstr, $errfile, $errline) {
        print '<div style="padding:5px; margin:5px; border:1px solid black; background:white;">';
        print '<b>ERROR</b> '.$errstr.'<br />';
        print $errfile.':'.$errline.'</div>';
    }

?>