<?
$r=cnstats_sql_query("DELETE FROM cns_adminsessions WHERE hash='".mysql_escape_string($_COOKIE["CNSSESSION"])."';");
setcookie("CNSSESSION","");
header("Location: index.php");
?>