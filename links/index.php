<?php
require_once('config.inc.php');

$oResult = mysql_query("SELECT sID FROM pm_structure WHERE URLName = 'links' LIMIT 1");
if ($oResult) {
    list($sID) = mysql_fetch_array($oResult);
} else {
    $sID = 0;
}

if (isset($_POST['inputo'])) {
    if ($_POST['inputo'] != '') {
        $sName = mysql_escape_string($_POST['inputo']);
        if (isset($_POST['editName']) && is_numeric($_POST['editName'])) {
            mysql_query("UPDATE pm_structure SET Title='$sName', ShortTitle='$sName' WHERE sID='{$_POST['editName']}' LIMIT 1");
        } else {
            mysql_query("INSERT INTO `pm_structure` ( `sID` , `userID` , `pms_sID` , `tplID` , `CreateDate` , `URLName` , `Title` , `ShortTitle` , `MetaDesc` , `MetaKeywords` , `Content` , `ModuleName` , `DataType` , `OrderNumber` , `OrderField` , `SortType` , `LinkCSSClass` , `CacheLifetime` , `ReviseType` , `CanBeProcessed` , `CanBeHelpered` , `IsVersionOfParent` , `isDeleted` , `isHidden` )
                        VALUES ('', '2', '$sID', '5', '0000-00-00', '1', '{$sName}', '{$sName}', '', '', '', 'Links', 'Link', '0', 'OrderNumber', '0', '1', '00:00:00', '0', '0', '0', '0', '0', '1'
);");
            $ins = mysql_insert_id();
            mysql_query("UPDATE pm_structure SET URLName = {$ins} WHERE sID={$ins} LIMIT 1");
        }
    } else {
        $smarty->assign('message', 'Введите название категории');
    }
}

//$smarty->assign('message', mysql_error());

if (isset($_POST['change']) && is_array($_POST['change'])) {
    switch ($_POST['action']) {
        case 'd':
            foreach ($_POST['change'] as $v) {
                mysql_query('DELETE FROM pm_structure WHERE sID="'.$v.'" LIMIT 1');
                mysql_query('DELETE FROM pm_links WHERE cid="'.$v.'"');
            }
            break;
        case 'h':
            foreach ($_POST['change'] as $v) {
                mysql_query('UPDATE pm_structure SET isHidden=1 WHERE sID="'.$v.'" LIMIT 1');
            }
            break;
        case 'p':
            foreach ($_POST['change'] as $v) {
                mysql_query('UPDATE pm_structure SET isHidden=0 WHERE sID="'.$v.'" LIMIT 1');
            }
            break;
    }
}

$oResult = mysql_query('SELECT DISTINCT s.sID, s.Title, s.isHidden, COUNT(l.cid) as count, COUNT(l2.cid) as count_new
                        FROM pm_structure s
                        LEFT JOIN pm_links l ON (s.sID = l.cid AND l.public = 1)
                        LEFT JOIN pm_links l2 ON (s.sID = l2.cid AND l2.public = 2)
                        WHERE s.pms_sID='.$sID.' AND s.DataType = "Link"
                        GROUP BY s.sID
                        ORDER BY s.Title');
if ($oResult) {
    $aCategories = array();
    while ($aCat=mysql_fetch_assoc($oResult)) {
        $aCat['Title'] = stripslashes($aCat['Title']);
        $aCategories[] = $aCat;
    }
}

//$smarty->assign('message', mysql_error());
$smarty->assign('categories', $aCategories);
$smarty->display('admin.html');

?>