<?php
$inpage=40;
$filter=$_GET["filter"];

$DATELINK="&amp;filter=".urlencode($filter);

$sqlflt=GenerateFilter($filter);
$r=cnstats_sql_query("select page,count(*) from cns_log WHERE date>'".$startdate."' AND date<'".$enddate."' AND type=1 ".$sqlflt." group by page order by 2 desc");

$count=mysql_num_rows($r);
if ($start+$inpage>$count) $finish=$count; else $finish=$start+$inpage;
for ($i=$start;$i<$finish;$i++) {
	$data=urldecode(mysql_result($r,$i,0));
	$cnt=mysql_result($r,$i,1);
	$TABLED[]="<a target=_blank href='index.php?st=refto&amp;stm=".$stm."&amp;ftm=".$ftm."&amp;filter=".urlencode($filter)."&amp;url=".urlencode($data)."'>[r]</a> <A href='".$data."' target=_blank>".$data."</a>";
	$TABLEU[]="";
	$TABLEC[]=$cnt;
	}

LeftRight($start,$inpage,$num,$count,0);
ShowTable($start);
LeftRight($start,$inpage,$num,$count);
?>
