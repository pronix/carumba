<?php
header('Content-Type: text/plain;charset=Windows-1251');
header('Cache-control: No-Cache');
header('Pragma: No-Cache');

require_once('../task/config.inc.php');
mysql_connect($dbs, $dbu, $dbp);
mysql_select_db($dbn);
@mysql_query("SET NAMES cp1251"); 

/**
 * Получить sID товара и текст названия товара
 */
if (isset($_GET['sID']) && is_numeric($_GET['sID'])) {
    $sID = $_GET['sID'];
} else {
    die('bad params');
}




$sError = '';
$id = 0;
$add = 0;


$oResult = mysql_query("SELECT * FROM pm_comments WHERE sID = '$sID' AND public='1' ORDER BY date DESC");
$aComments = array();
$htmlComments = '';


if ($sError != '') {
    $htmlComments .= '<div class="comment">'.$sError.'</div>';
}


if ($oResult && mysql_num_rows($oResult)) {
    while ( $a = mysql_fetch_assoc($oResult) ) {
        $aComments[] = $a;
    }
} else {
    $htmlComments .= '<div class="comment">Здесь пока никто не оставил комментариев.</div>';
}


foreach ($aComments as $k => $v) {
    $htmlComments .= '<div class="comment">'.date('d-m-Y', $v['date']).' | <strong>';
    if ($v['email']) {
        $htmlComments .= '<a href="mailto:'.$v['email'].'">'.strip_tags($v['name']).'</a>';
    } else {
        $htmlComments .= strip_tags( $v['name'] );
    }
    $htmlComments .= '</strong><br />';
    $htmlComments .= nl2br($v['comment']);
    $htmlComments .= '</div>';
}


$iCode = rand(0, 9999);
$sCode = '0000'.$iCode;
$sCode = substr($sCode, strlen($sCode)-4, 4);
mysql_query("INSERT INTO pm_comments_codes (id, date, code) VALUES ('', ".time().", '$sCode')");
$iCode = mysql_insert_id();
if (empty($iCode)) die('bad arguments');

mysql_query("DELETE FROM pm_comments_codes WHERE date < '".(time()-600)."'");

$tpl = file_get_contents('_pm/templates/Catalogue/comment.html');

$tpl = str_replace('{comments}', $htmlComments, $tpl);
$tpl = str_replace('{code}', $iCode, $tpl);
$tpl = str_replace('{sID}', $sID, $tpl);

$tpl = str_replace('{name}', $_POST['txtName'], $tpl);
$tpl = str_replace('{email}', $_POST['txtEmail'], $tpl);
$tpl = str_replace('{message}', $_POST['txtMessage'], $tpl);

print $tpl;
?>