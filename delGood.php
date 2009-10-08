<?php

require_once('_pm/class.authenticationmgr.php');
require_once('task/config.inc.php');
mysql_connect($dbs, $dbu, $dbp);
mysql_select_db($dbn);

ob_start();

$autMgr = new AuthenticationManager();


$userID = $autMgr->getUserID();
$userGroup = $autMgr->getUserGroup();

if ( ($userID == 1) OR ($userGroup != 5)) {
    $autMgr->endSession();
    header('location: /login');
    exit();
}

if (isset($_POST['sID']) && is_numeric($_POST['sID'])) {
    $sID = $_POST['sID'];
} else {
    $sID = 0;
}

if ($sID) {
    $oResult = mysql_query("SELECT accID FROM pm_as_parts WHERE sID='$sID' LIMIT 1");
    if ($oResult && mysql_num_rows($oResult)) {
        list($accID) = mysql_fetch_array($oResult);
        @mysql_query("DELETE FROM pm_as_parts_properties WHERE accID='$accID'");
        @mysql_query("DELETE FROM pm_as_parts WHERE accID='$accID' LIMIT 1");
        @mysql_query("DELETE FROM pm_structure WHERE sID='$sID' LIMIT 1");
        print 'Товар удален<br />';
    } else {
        print 'Товар с sID='.$sID.' не найден.<br />';
    }
}

$content = ob_get_contents();
ob_end_clean();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="ru">
<head>
<title>Чистильщик товаров</title>
</head>
<body>
<?=$content?>
<p>
<form method="POST" action="delGood.php">
Введите id товара:
<input type="text" name="sID" />
<input type="submit" value="Удалить" />
</form>
</p>
</body>
</html>


<?php
    function _cookie($var) {
        if (isset($_COOKIE[$var])) {
            return $_COOKIE[$var];
        } else {
            return false;
        }
    }

?>