<?php
$inpage=40;

$by=$_GET["by"];
$filter=$_GET["filter"];

$DATELINK="&amp;by=".$by."&amp;filter=".urlencode($filter);

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

$quer=GenerateFilter($filter);
$r=cnstats_sql_query("SELECT IF(LOCATE('/',referer,8)=0,CONCAT(referer,'/'),LEFT(referer,LOCATE('/',referer,8))),count(referer)
              FROM cns_log
              WHERE ".$az." date>'".$startdate."' AND date<'".$enddate."' AND referer LIKE 'http%' ".$quer."
              GROUP BY IF(LOCATE('/',referer,8)=0,CONCAT(referer,'/'),LEFT(referer,LOCATE('/',referer,8)))
              ORDER BY 2 desc;");

$TABLED=$TABLEC=Array();

$count=mysql_num_rows($r);
if ($start+$inpage>$count) $finish=$count; else $finish=$start+$inpage;
$num=$start;
for ($i=$start;$i<$finish;$i++) {
	$data=urldecode(mysql_result($r,$i,0));
	$cnt=mysql_result($r,$i,1);
	$num++;
	if (!($data=="undefined" || empty($data))) {
		$TABLEU[]=$TABLED[]=$data;
		$TABLEC[]=$cnt;
		}
	}

LeftRight($start,$inpage,$num,$count,0);
ShowTable($start);
LeftRight($start,$inpage,$num,$count);
?>