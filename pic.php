<?php

$dblink = mysql_connect("localhost","webcarumba","6Fasj6FQ7d");
mysql_select_db("carumba", $dblink);
mysql_query("SET NAMES 'cp1251'");
mysql_query("SET CHARACTER SET cp1251");
			
$sID = $_GET["sID"];
$catItem = getItemPicturePath($sID);
mysql_close($dblink);


$tpl = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>%name%</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<link href="/css/pic.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table class="bigpic" cellpadding="0" cellspacing="0">
  <tr> 
    <td class="pod_01"><img src="/images/pix.gif" width="15" height="15"  alt="" /></td>
    <td class="pod_02">&nbsp;</td>
    <td class="pod_03"><img src="/images/pix.gif" width="15" height="15"  alt="" /></td>
  </tr>
  <tr> 
    <td class="pod_04">&nbsp;</td>
    <td class="pic"><img style="cursor:pointer;" onClick="window.close(); return false;" src="%picture%" width="400" height="400"  alt="%name%" /></td>
    <td class="pod_05">&nbsp;</td>
  </tr>
  <tr> 
    <td class="pod_06">&nbsp;</td>
    <td class="pod_07">&nbsp;</td>
    <td class="pod_08">&nbsp;</td>
  </tr>
</table>
</body>
</html>';

$picture = $catItem['PicturePath'] . "/" . $sID . "_3.jpg";

$tpl = str_replace("%picture%", $picture, $tpl);
$tpl = str_replace("%name%", $catItem["Title"], $tpl);
echo $tpl;//http://localhost/catalogue/fluids/antifreeze/1888?viewfull=1

function getItemPicturePath($sID)
{
	$query = "SELECT PicturePath, Title FROM pm_structure, pm_as_categories WHERE pm_structure.sID='".$sID."' && pm_structure.pms_sID = pm_as_categories.sID";
	//echo $query;
	$result= mysql_query($query);
	if(mysql_num_rows($result)) {
		$row = mysql_fetch_assoc($result);
		return $row;
	}
}

?>
