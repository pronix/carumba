<?php
    header('Content-Type: text/plain;charset=Windows-1251');
    header('Cache-control: No-Cache');
    header('Pragma: No-Cache');

    require_once('../task/config.inc.php');
    mysql_connect($dbs, $dbu, $dbp);
    mysql_select_db($dbn);

    
    $sID = (isset($_GET['sID']) && is_numeric($_GET['sID'])) ? $_GET['sID'] : 0;
    $val = (isset($_GET['value']) && is_numeric($_GET['value'])) ? $_GET['value'] : 0;
    if ($val < 0 || $val > 5) $val = 0;
    
    if ( $sID && $val) {
        if (empty($_COOKIE["rating"])) {
            $cRating = array( $sID => 1 );
            $oResult = mysql_query("SELECT count FROM pm_rating WHERE sID='$sID' LIMIT 1");
            if ($oResult && mysql_num_rows($oResult)) {
                $oResult = mysql_query("UPDATE pm_rating SET count = count + 1, grade = grade + '$val' WHERE sID='$sID' LIMIT 1");
            } else {
                $oRating = mysql_query("INSERT INTO pm_rating (rID, sID, grade, count) VALUES ('', '$sID', '$val', '1')");
            }
            setcookie("rating", 1, time() + 86400 );
        } else {
            die('-1||<div>Вы уже проголосовали.</div> <div>Попробуйте завтра.</div>');
        }
    } else {
        die('Хуй');
    }

    print mysql_error();
	$oResult = mysql_query("SELECT grade, count FROM pm_rating WHERE sID='$sID' LIMIT 1");
	$fRating = 0;
	if ($oResult && mysql_num_rows($oResult)) {
	    $oRating = mysql_fetch_object($oResult);
	    $fRating = $oRating->grade / $oRating->count;
	}
	$fRating = number_format( $fRating, 1, '.', '' );
    
	print $fRating.'||Ваш голос был учтен';
?>