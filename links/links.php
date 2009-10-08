<?php
require_once('config.inc.php');

if (isset($_SERVER['QUERY_STRING'])) {
    $qs = $_SERVER['QUERY_STRING'];
    if (is_numeric($qs)) {
        $cid = $qs;
    } else {
        list($cid) = explode('&', $qs);
        if (!is_numeric($cid)) {
            $cid = 0;
        }
    }
}

$page = (isset($_GET['page'])&&is_numeric($_GET['page'])) ? $_GET['page'] : 1;

$oResult = mysql_query("SELECT sID FROM pm_structure WHERE URLName = 'links' LIMIT 1");
if ($oResult) {
    list($sID) = mysql_fetch_array($oResult);
} else {
    $sID = 0;
}

if (!empty($_POST['url']) && !empty($_POST['title']) && !empty($_POST['cat']) && !empty($_POST['text']) && is_numeric($_POST['cat'])) {
    foreach ($_POST as &$v) {
        $v = mysql_escape_string($v);
    }
    // 	 id, cid,date  	 url  	 title  	 text  	 referer  	 email  	 public
    if (!empty($_POST['action']) && is_numeric($_POST['action'])) {
        mysql_query("UPDATE pm_links SET cid='{$_POST['cat']}',
                                         url='{$_POST['url']}',
                                         title='{$_POST['title']}',
                                         text='{$_POST['text']}',
                                         referer='{$_POST['referer']}',
                                         email='{$_POST['email']}'
                                     WHERE id={$_POST['action']} LIMIT 1");
    } else {
        mysql_query("INSERT INTO `pm_links` ( `id` , `cid` , `date` , `url` , `title` , `text` , `referer` , `email` , `public` )
                            VALUES ('',
                            '{$_POST['cat']}',
                            UNIX_TIMESTAMP() ,
                            '{$_POST['url']}',
                            '{$_POST['title']}',
                            '{$_POST['text']}',
                            '{$_POST['referer']}',
                            '{$_POST['email']}', '0')");
    }
} else {
    $smarty->assign('form', $_POST);
    $smarty->assign('message', 'Заполните все поля формы');
}
print mysql_error();

if (isset($_POST['change']) && is_array($_POST['change'])) {
    switch ($_POST['action']) {
        case 'd':
            foreach ($_POST['change'] as $v) {
                mysql_query('DELETE FROM pm_links WHERE id="'.$v.'" LIMIT 1');
            }
            break;
        case 'h':
            foreach ($_POST['change'] as $v) {
                mysql_query('UPDATE pm_links SET public=0 WHERE id="'.$v.'" LIMIT 1');
            }
            break;
        case 'p':
            foreach ($_POST['change'] as $v) {
                mysql_query('UPDATE pm_links SET public=1 WHERE id="'.$v.'" LIMIT 1');
            }
            break;
        case 'm':
            if (isset($_POST['catid']) && is_numeric($_POST['catid'])) {
                foreach ($_POST['change'] as $v) {
                    mysql_query('UPDATE pm_links SET cid='.$_POST['catid'].' WHERE id="'.$v.'" LIMIT 1');
                }
            }
            break;
    }
}



if ($cid) {

    $oResult = mysql_query('SELECT COUNT(*) FROM pm_links WHERE public=1 AND cid='.$cid);
    if ($oResult) {
        list($count) = mysql_fetch_array($oResult);
    } else {
        $count = 0;
    }
    $per_page = 12;
    $from = ($page-1)*$per_page;
    $pages = ceil($count / $per_page);

    $pageCont = '';
    if ($pages>1) {
        for ($i=1; $i<=$pages; $i++) {
            $pageCont .= ' - ';
            if ($i == $page) {
                $pageCont .= $i;
            } else {
                $pageCont .= '<a href="?'.$cid.'&page='.$i.'">'.$i.'</a>';
            }
        }
    }

    $oResult = mysql_query('SELECT * FROM pm_links WHERE cid='.$cid." ORDER BY date DESC LIMIT $from, $per_page");
    if ($oResult) {
        $aLinks = array();
        while ($aLink = mysql_fetch_assoc($oResult)) {
            foreach ($aLink as &$v) $v = htmlspecialchars(stripcslashes($v));
            $aLink['date'] = date('d-m-Y (H:i)', $aLink['date']);
            $aLinks[] = $aLink;
        }
    }
    $oResult = mysql_query('SELECT Title FROM pm_structure WHERE sID="'.$cid.'" LIMIT 1');
    if ($oResult) {
        list($sCategory) = mysql_fetch_array($oResult);
    }
    $oResult = mysql_query('SELECT sID, Title FROM pm_structure WHERE pms_sID='.$sID.' AND DataType = "Link" ORDER BY Title');
    $aCategories = array();
    if ($oResult)
        while ($aCat = mysql_fetch_assoc($oResult)) {
            $aCat['Title'] = stripslashes($aCat['Title']);
            $aCategories[] = $aCat;
        }
} else {
    $sCategory = 'Категория не определена';
}

$smarty->assign('cid', $cid);
$smarty->assign('pageCont', $pageCont);
$smarty->assign('sCategory', $sCategory);
$smarty->assign('aCategories', $aCategories);
$smarty->assign('links', $aLinks);
$smarty->display('admin_links.html');

?>