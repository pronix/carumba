<?
$inpage=40;

$_GET["dateoff"]=1;
$url=urlencode($_GET["url"]);
$filter=$_GET["filter"];

$DATELINK="&amp;filter=".urlencode($filter);

$sqlflt=GenerateFilter($filter);
$r=cnstats_sql_query("select referer,count(referer) from cns_log WHERE date>'".$startdate."' AND date<'".$enddate."' AND type=1 AND page='".$url."' ".$sqlflt." group by referer order by 2 desc");

$count=mysql_num_rows($r);
if ($start+$inpage>$count) $finish=$count; else $finish=$start+$inpage;
for ($i=$start;$i<$finish;$i++) {
	$data=mysql_result($r,$i,0);
	$cnt=mysql_result($r,$i,1);
	
	if ($data=="undefined" || empty($data)) {
		$TABLED[]=$LANG["direct jump"];
		$TABLEU[]="";
		}
	else $TABLEU[]=$TABLED[]=$data;
	
	$TABLEC[]=$cnt;
	}

LeftRight($start,$inpage,$num,$count,0);
ShowTable($start);
LeftRight($start,$inpage,$num,$count);
?>
