<?php
$inpage=40;

$filter=$_GET["filter"];

$DATELINK="&amp;filter=".urlencode($filter);

$sqlflt=GenerateFilter($filter);
$r=cnstats_sql_query("select language,count(language) from cns_log WHERE type=1 AND date>'".$startdate."' AND date<'".$enddate."' ".$sqlflt." group by language order by 2 desc");

$count=mysql_num_rows($r);
if ($start+$inpage>$count) $finish=$count; else $finish=$start+$inpage;
$num=$start;
for ($i=$start;$i<$finish;$i++) {
	$eng=mysql_result($r,$i,0);
	$cnt=mysql_result($r,$i,1);
	$TABLED[]=$eng;
	$TABLEC[]=$cnt;
	}

LeftRight($start,$inpage,$num,$count,0);
ShowTable($start);
LeftRight($start,$inpage,$num,$count);
?>