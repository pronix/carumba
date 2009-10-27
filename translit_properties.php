<?php
error_reporting(E_ALL);
set_error_handler('userError', E_ERROR | E_WARNING | E_USER_ERROR | E_USER_NOTICE | E_USER_WARNING | E_COMPILE_ERROR);
function userError($errno, $errstr, $errfile, $errline) {
    print '<div style="padding:5px; margin:5px; border:1px solid black; background:white;">';
    print '<b>ERROR</b> '.$errstr.'<br />';
    print $errfile.':'.$errline.'</div>';
}

require_once('_pm/_kernel.config.php');
mysql_connect($CFG["mysql.host"],$CFG["mysql.username"],$CFG["mysql.password"]);
mysql_select_db($CFG["mysql.dbname"]);
mysql_query("SET NAMES 'cp1251'");
mysql_query("SET CHARACTER SET cp1251");

require_once('_pm/class.authenticationmgr.php');
$autMgr = new AuthenticationManager();

$userID = $autMgr->getUserID();
$userGroup = $autMgr->getUserGroup();

if ( ($userID == 1) OR ($userGroup != 5)) {
    $autMgr->endSession();
    header('location: /login');
    exit();
}

$sql = "SELECT propID, propValue FROM pm_as_parts_properties";
$result = mysql_query($sql);
while ($arr = mysql_fetch_array($result)){
	$tr = ruslat($arr[1]);
	$sql = "UPDATE pm_as_parts_properties SET propValueTranslit = '$tr' WHERE propID = '{$arr[0]}'";
	mysql_query($sql);
}

function ruslat($text)
{
	$subs = array ("æ","zh","¸","yo","é","j","þ","yu","÷","ch","ù","sch","ö","tc","ó","u","ê","k","å","e","í","n","ã","g","ø","sh","ç","z","õ","h","ô","f","û","y","â","v","à","a","ï","p","ð","r","î","o","ë","l","ä","d","ý","e","ÿ","ja","ñ","s","ì","m","è","i","ò","t","á","b","ü","","ü","Ú","¨","Yo","É","J","Þ","Yu","×","Cc","Ù","Sch","Ö","Tc","Ó","U","Ê","K","Å","E","Í","N","Ã","G","Ø","Sh","Ç","Z","Õ","H","Ô","F","Û","Y","Â","V","À","A","Ï","P","Ð","R","Î","O","Ë","L","Ä","D","Æ","Zh","Ý","E","ß","Ja","Ñ","S","Ì","M","È","I","Ò","T","Á","B","Ü","","Ú","");
	$len = count ($subs);
	$len = ($len % 2 == 0) ? $len : $len - 1;
	for ($i = 0 ; $i < $len; $i+=2){
		$text = str_replace($subs[$i], $subs[$i+1], $text);
	}
	$text = preg_replace("/[^a-zA-Z0-9]+/","-", $text);
	$text = trim(trim($text),"-");
	return $text;
}
?>