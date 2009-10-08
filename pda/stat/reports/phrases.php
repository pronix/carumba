<?php
$inpage=40;

$by=$_GET["by"];
$filter=$_GET["filter"];

$DATELINK="&amp;by=".$by."&amp;filter=".urlencode($filter);

$quer=$quer1="";
$cnt=0;
$PRE=$PRU=$PPP=Array();

$r=cnstats_sql_query("SELECT * FROM cns_data WHERE type=1 ORDER BY id");
while ($a=mysql_fetch_assoc($r)) {
	$url=trim($a["d1"]);
	$name=trim($a["d2"]);
	$regexp=trim($a["d3"]);
	$parent=trim($a["d4"]);

	$PRU[$url]=$parent;
	$PRE[$url]=$regexp;
	$PPP[$url]=trim($a["d5"]);

	if (!empty($url)) {
		$quer=$quer."(IF(LOCATE('$url',referer)!=0,referer,\n";
		$quer1=$quer1."(IF(LOCATE('$url',referer)!=0,'$name|||$regexp|||$url',\n";
		$cnt++;
		}
	}
$quer=$quer."'no'";
$quer1=$quer1."'no'";

for ($i=0;$i<$cnt;$i++) {
	$quer=$quer."))";
	$quer1=$quer1."))";
	}

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
$r=cnstats_sql_query("SELECT ".$quer.",count(referer),".$quer1.",referer
          FROM cns_log
          WHERE ".$az." date>'".$startdate."' AND date<'".$enddate."' AND referer LIKE 'http%' ".$sqlflt."
          GROUP BY ".$quer."
          ORDER BY 2 desc;");

$PH=Array();
while ($a=mysql_fetch_array($r,MYSQL_NUM)) {
	list($ssystem,$regexp,$url)=explode("|||",$a[2]);
	$data=strrtolower(strtolower(GetRegexpPhrase($regexp,$a[0],$url)));
	$cnt=$a[1];

	if ($data!="" && $ssystem!="no") if (isset($PH[$data])) $PH[$data]+=$cnt; else $PH[$data]=$cnt;
	}

arsort($PH);

$count=0;
while (list ($key, $val) = each ($PH)) {
	if ($count>=$start && $count<$start+$inpage) {
		$key=phrase_uncode($key);
		$TABLED[]=$key;
		$TABLEC[]=$val;
		}
	$count++;
    }

LeftRight($start,$inpage,$num,$count,0);
ShowTable($start);
LeftRight($start,$inpage,$num,$count);
?>
