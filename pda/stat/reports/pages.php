<?
$inpage=40;

$domains=intval($_GET["domains"]);
$shorturl=intval($_GET["shorturl"]);
$filter=$_GET["filter"];

$DATELINK="&amp;shorturl=".$shorturl."&amp;domains=".$domains."&amp;filter=".urlencode($filter);

if ($domains==1) {
	$ADMENU.="<a href=\"index.php?st=pages&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("domains",$DATELINK)."\">".$LANG["without domains"]."</a><br>";
	$ADMENU.=$LANG["with domains"];
	}
else {
	$ADMENU.=$LANG["without domains"]."<br>";
	$ADMENU.="<a href=\"index.php?st=pages&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("domains",$DATELINK)."&amp;domains=1\">".$LANG["with domains"]."</a>";
	}

$ADMENU.="<br><img src=\"img/none.gif\" width=1 height=5><br>";

if ($shorturl==1) {
	$ADMENU.="<a href=\"index.php?st=pages&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("shorturl",$DATELINK)."\">".$LANG["full url"]."</a><br>";
	$ADMENU.=$LANG["short url"];
	}
else {
	$ADMENU.=$LANG["full url"]."<br>";
	$ADMENU.="<a href=\"index.php?st=pages&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("shorturl",$DATELINK)."&amp;shorturl=1\">".$LANG["short url"]."</a>";
	}

if ($shorturl==1) 
	$pagesql="IF(LOCATE('%3F',page),LEFT(page,LOCATE('%3F',page)-1),page)";
else
	$pagesql="page";

$sqlflt=GenerateFilter($filter);
if ($domains==1) 
	$sql="select ".$pagesql.",count(*) from cns_log WHERE date>'".$startdate."' AND date<'".$enddate."' ".$sqlflt." group by 1 order by 2 desc";
else 
	$sql="select IF(STRCMP(LEFT(page,13),'http%3A%2F%2F')=0,IF(LOCATE('%2F',page,13),SUBSTRING(".$pagesql.",LOCATE('%2F',".$pagesql.",13)),'/'),".$pagesql."),count(*) from cns_log WHERE date>'".$startdate."' AND date<'".$enddate."' ".$sqlflt." group by 1 order by 2 desc";

$r=cnstats_sql_query($sql);
$count=mysql_num_rows($r);

if ($start+$inpage>$count) $finish=$count; else $finish=$start+$inpage;
$num=$start;
for ($i=$start;$i<$finish;$i++) {
	$data=urldecode(mysql_result($r,$i,0));
	$cnt=mysql_result($r,$i,1);
	$num++;
	if (!empty($data)) {
		$TABLEU[]=$TABLED[]=phrase_uncode($data);
		$TABLEC[]=$cnt;
		}
	}

LeftRight($start,$inpage,$num,$count,0,5);
ShowTable($start);
LeftRight($start,$inpage,$num,$count,5,5);
?>