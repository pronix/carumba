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
$r=cnstats_sql_query("select referer,count(referer) from cns_log WHERE ".$az." date>'".$startdate."' AND date<'".$enddate."' ".$quer." group by referer order by 2 desc");

$count=mysql_num_rows($r);
if ($start+$inpage>$count) $finish=$count; else $finish=$start+$inpage;
for ($i=$start;$i<$finish;$i++) {
	$data=mysql_result($r,$i,0);
	$cnt=mysql_result($r,$i,1);
	$TABLEC[]=$cnt;
	if (!($data=="blockedReferrer" || $data=="undefined" || empty($data))) $TABLED[]=$TABLEU[]=$data;
	else {
		$TABLEU[]="";
		if (!($data=="blockedReferrer")) $TABLED[]=$LANG["noreferer"];
		else $TABLED[]=$LANG[$data];
		}
	}

LeftRight($start,$inpage,$num,$count,0);
ShowTable($start);
LeftRight($start,$inpage,$num,$count);
?>
