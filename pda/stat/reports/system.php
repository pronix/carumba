<?php
$inpage=40;

$filter=$_GET["filter"];

$DATELINK="&amp;filter=".urlencode($filter);

$sqlflt=GenerateFilter($filter);
$r=cnstats_sql_query("select agent,count(agent) from cns_log WHERE date>'".$startdate."' AND date<'".$enddate."' ".$sqlflt." group by agent order by 2 desc");

$count=mysql_num_rows($r);
if ($start+$inpage>$count) $finish=$count; else $finish=$start+$inpage;
for ($i=$start;$i<$finish;$i++) {
	$TABLED[]=mysql_result($r,$i,0);
	$TABLEC[]=mysql_result($r,$i,1);
	}

LeftRight($start,$inpage,$num,$count,0);
ShowTable($start);
LeftRight($start,$inpage,$num,$count);
?>