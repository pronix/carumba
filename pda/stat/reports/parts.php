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

$sqlflt=GenerateFilter($filter);
$r=cnstats_sql_query("SELECT IF(LOCATE('%2F',page,1+LOCATE('%2F',page,13))=0,page,LEFT(page,LOCATE('%2F',page,1+LOCATE('%2F',page,13)))),count(page)
              FROM cns_log
              WHERE ".$az." date>'".$startdate."' AND date<'".$enddate."' AND page LIKE 'http%' ".$sqlflt."
              GROUP BY IF(LOCATE('%2F',page,1+LOCATE('%2F',page,13))=0,page,LEFT(page,LOCATE('%2F',page,1+LOCATE('%2F',page,13))))
              ORDER BY 2 desc;");

$PARTS=Array();
$count=mysql_num_rows($r);
if ($start+$inpage>$count) $finish=$count; else $finish=$start+$inpage;
for ($i=$start;$i<$finish;$i++) {
	$data=urldecode(mysql_result($r,$i,0));
	if ($data[strlen($data)-1]=="%") $data[strlen($data)-1]="/";

	$cnt=mysql_result($r,$i,1);
	if (($data=="undefined" || empty($data))) $data="Неизвестно";

	if (strpos($data,"?")) $data=substr($data,0,strpos($data,"?"));
	if (substr($data,0,7)=="http://") $data=substr($data,7);
	if (strpos($data,"/")) $data=substr($data,strpos($data,"/"));

	if (isset($PARTS[$data])) $PARTS[$data]+=$cnt; else $PARTS[$data]=$cnt;
	}	

arsort($PARTS);

$count=0;
while (list ($key, $val) = each ($PARTS)) {
	if ($count>=$start && $count<=$start+$inpage) {
		$TABLED[]=$key;
		$TABLEC[]=$val;
		}
	$count++;
    }

LeftRight($start,$inpage,$num,$count,0);
ShowTable($start);
LeftRight($start,$inpage,$num,$count);
?>
