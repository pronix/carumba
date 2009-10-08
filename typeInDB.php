<p>Заполните следующие поля</p>
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

$sID = (isset($_POST['sID']) && is_numeric($_POST['sID'])) ? $_POST['sID'] : '';
$type = (isset($_POST['type']) && is_numeric($_POST['type'])) ? $_POST['type'] : '';
$typeName = (isset($_POST['typeName'])) ? mysql_escape_string($_POST['typeName']) : '';
$typeValue = (isset($_POST['typeValue'])) ? mysql_escape_string($_POST['typeValue']) : '';

    ?>
	<form action="typeInDB.php" method="post">
	sID<input type="text" name="sID" value="<?=$sID?>"><br>
	typeName<input type="text" name="typeName" value="<?=$typeName?>">(имя)<br>
	typeValue<input type="text" name="typeValue" value="<?=$typeValue?>">(ед. измерения)<br>
	тип раздела<select name="type">
	<option value="1" disabled="true">Товар</option>
	<option value="2" selected="true">Раздел</option>
	</select><hr>
	<input type="submit" name="submit" value="ok"><br>
	</form>
	<?php
print_r($_POST);
print '<br>';

switch ($type) {
    case 1:
        break;
        
    case 2:
        $oResult = mysql_query("SELECT accCatID FROM pm_as_categories WHERE sID=$sID LIMIT 1");
        if (!list($accCatID) = mysql_fetch_array($oResult)) die('Раздел задан некорректно. Проверьте правильность ввода или обратитесь к разработчику.');
        $oResult = mysql_query("SELECT propListID FROM pm_as_prop_list WHERE accCatId='$accCatID' AND propName='$typeName' AND accMeasure='$typeValue' LIMIT 1");
        if (mysql_fetch_array($oResult)) die('такой параметр задан');
        mysql_query("INSERT INTO pm_as_prop_list (accCatID, propName, accMeasure, isHidden, OrderNumber) VALUES ('$accCatID','$typeName','$typeValue','0','1')");
        if ($propListID = mysql_insert_id()) die('Параметр добавлен. propListID='.$propListID);
        die('Ничего небыло добавлено');
        break;    
}
/*
	$query = "SELECT accCatID FROM pm_as_categories WHERE sID = '$sID' LIMIT 1";
	$result = mysql_query($query);
	$row = mysql_fetch_assoc($result);
	$accCatID = $row['accCatID'];
	
	$accIDList = Array();
	
	$query = "SELECT accID FROM pm_as_parts WHERE accCatID = '".$accCatID."'";
	$result = mysql_query($query);
	if(mysql_num_rows($result)) {
		while($row = mysql_fetch_assoc($result)) {
			$accIDList[] = $row['accID'];
		}

		$query = "INSERT INTO pm_as_prop_list (accCatID, propName, accMeasure, isHidden, OrderNumber) VALUES ('".$accCatID."','".$typeName."','','0','1')";
		mysql_query($query);

		$query = "SELECT propListID FROM pm_as_prop_list WHERE accCatID = '".$accCatID."'";
		$propListResult = mysql_query($query);
		$row = mysql_fetch_assoc($propListResult);
		
		$propListID = $row['propListID'];

		foreach($accIDList as $accID) {
			$query = "INSERT INTO pm_as_parts_properties (accID, propListID, propValue) VALUES ('".$accID."', '".$propListID."', '".$typeValue."')";
			mysql_query($query);
		}
		echo 'Усе готово, сир';

		

	} else {
		echo 'No items in this category';
	}
*/

?>
