<?php
$inpage=40;

$by=$_GET["by"];
$filter=$_GET["filter"];

$DATELINK="&amp;filter=".urlencode($filter);

switch($by){
	case "hits":
		$ADMENU.="<a href='index.php?st=".$st."&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("by",$DATELINK)."&amp;by=hosts'>".$LANG["by hosts"]."</a><br>";
		$ADMENU.="<a href='index.php?st=".$st."&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("by",$DATELINK)."&amp;by=users'>".$LANG["by users"]."</a><br>";
		$ADMENU.=$LANG["by hits"]."<br>";
		$az="";
		break;
	case "users":
		$ADMENU.="<a href='index.php?st=".$st."&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("by",$DATELINK)."&amp;by=hosts'>".$LANG["by hosts"]."</a><br>";
		$ADMENU.=$LANG["by users"]."<br>";
		$ADMENU.="<a href='index.php?st=".$st."&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("by",$DATELINK)."&amp;by=hits'>".$LANG["by hits"]."</a><br>";
		$az=" type1=1 AND ";
		break;
	case "hosts":default:
		$ADMENU.=$LANG["by hosts"]."<br>";
		$ADMENU.="<a href='index.php?st=".$st."&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("by",$DATELINK)."&amp;by=users'>".$LANG["by users"]."</a><br>";
		$ADMENU.="<a href='index.php?st=".$st."&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("by",$DATELINK)."&amp;by=hits'>".$LANG["by hits"]."</a><br>";
		$az=" type=1 AND ";
		break;
}

if (!isset($type)) $r=cnstats_sql_query("SELECT d2,d3 FROM cns_data WHERE type=2 ORDER BY id;");
else  $r=cnstats_sql_query("SELECT d2,d3 FROM cns_data WHERE type=2 and d1='".mysql_escape_string($type)."' ORDER BY id;");

while ($a=mysql_fetch_assoc($r)) {
	$rr=mysql_escape_string(trim($a["d3"]));
	$rn=mysql_escape_string(trim($a["d2"]));

	$query=$query."IF(LOCATE('".$rr."',referer)!=0,'".$rn."',\n";
	$count_query++;
	}

$query=$query."'".$LANG["other links"]."'";
for ($i=0;$i<$count_query;$i++) $query=$query.")";

$fltsql=GenerateFilter($filter);
$r=cnstats_sql_query("SELECT ".$query.",count(referer)
          FROM cns_log
          WHERE ".$az." date>'$startdate' AND date<'$enddate' AND referer LIKE 'http%' ".$fltsql."
          GROUP BY ".$query."
          ORDER BY 2 desc;");

$cnt_total=0;
$cnt_others=0;
while($a=mysql_fetch_row($r)){
if($a[0]!=$LANG["other links"]){
	$TABLED[]=$a[0];
	$TABLEC[]=$a[1];
	$cnt_total+=$a[1];
	}
	else $cnt_others=$a[1];
	}
ShowTable(0);
print"<P style='margin:10px;'><B>".$LANG["total"].":</B></P>";
$total=$LANG["total"];
if(isset($LANG["reports"][$st]))$total=$LANG["reports"][$st];
else for($i=0;$i<count($MENU);$i+=3)
if($st==$MENU[$i+1])$total=$MENU[$i+2]."\n";
$TABLED=$TABLEC=Array();
$TABLED[]=$total;
$TABLEC[]=$cnt_total;
$TABLED[]=$LANG["other links"];
$TABLEC[]=$cnt_others;

ShowTable(0);
?>
