<?php
$inpage=40;
$filter=$_GET["filter"];

$DATELINK="&amp;filter=".urlencode($filter);

$sqlflt=GenerateFilter($filter);
$r=cnstats_sql_query("select ip,count(ip),proxy,id from cns_log WHERE date>'".$startdate."' AND date<'".$enddate."' ".$sqlflt." group by ip,proxy order by 2 desc");

$count=mysql_num_rows($r);
if ($start+$inpage>$count) $finish=$count; else $finish=$start+$inpage;
$num=$start;
for ($i=$start;$i<$finish;$i++) {
	$data=long2ip(mysql_result($r,$i,0));
	if ($data=="255.255.255.255") $data=$LANG["unknownip"];

	$data1=long2ip(mysql_result($r,$i,2));
	if ($data1!="255.255.255.255") $data.="</a> (".$LANG["proxy"]." ".$data1.")";

	$cnt=mysql_result($r,$i,1);
	$rid=mysql_result($r,$i,3);
	if ($class!="tbl1") $class="tbl1"; else $class="tbl2";
	$num++;
	if (strlen($data)>80) $printdata=substr($data,0,80)."..."; else $printdata=$data;
	$TABLEU[]="index.php?rid=".$rid."&amp;st=ipinfo&amp;stm=".$stm."&amp;ftm=".$ftm."&amp;filter=".urlencode($filter);
	$TABLED[]=$data;
	$TABLEC[]=$cnt;
	}

LeftRight($start,$inpage,$num,$count,0);
ShowTable($start);
LeftRight($start,$inpage,$num,$count);
?>