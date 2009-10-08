<?php
$filter=$_GET["filter"];

$DATELINK="&amp;filter=".urlencode($filter);

$R=Array();

$sqlflt=GenerateFilter($filter);
$r=cnstats_sql_query("SELECT count(*) as cnt FROM cns_log WHERE date>'".$startdate."' AND date<'".$enddate."' ".$sqlflt." GROUP BY ip,proxy;");
$cnt=0;
$total=0;
while ($a=mysql_fetch_assoc($r)) {
	if (!isset($R[$a["cnt"]])) $R[$a["cnt"]]=1; else $R[$a["cnt"]]++;
	$cnt+=$a["cnt"];
	$total++;
	}
if ($total==0) $total=1;
$avg=sprintf("%2.2f",$cnt/$total);

$N=Array(1=>0,2=>0,3=>0,4=>0,5=>0,9=>0,10=>0,20=>0,50=>0,100=>0);
while (list ($key, $val) = each ($R)) {
	$nkey=$key;
	if ($key>5) $nkey=9;
	if ($key>=10) $nkey=10;
	if ($key>=20) $nkey=20;
	if ($key>=50) $nkey=50;
	if ($key>=100) $nkey=100;
	if (!isset($N[$nkey])) $N[$nkey]=$val; else $N[$nkey]+=$val;
	}

$TABLEC[]=$N[1];  $TABLED[]=$LANG["depth page1"];
$TABLEC[]=$N[2];  $TABLED[]=$LANG["depth page2"];
$TABLEC[]=$N[3];  $TABLED[]=$LANG["depth page3"];
$TABLEC[]=$N[4];  $TABLED[]=$LANG["depth page4"];
$TABLEC[]=$N[5];  $TABLED[]=$LANG["depth page5"];
$TABLEC[]=$N[9];  $TABLED[]=$LANG["depth page9"];
$TABLEC[]=$N[10]; $TABLED[]=$LANG["depth page10"];
$TABLEC[]=$N[20]; $TABLED[]=$LANG["depth page20"];
$TABLEC[]=$N[50]; $TABLED[]=$LANG["depth page50"];
$TABLEC[]=$N[100];$TABLED[]=$LANG["depth page100"];
ShowTable(0);

$CONFIG["showlines"]=0;
print "<br>";
print $TABLE;
print "<tr width='".$TDW."' ".$TDS." class=tbl1><td>".$LANG["depth average"]."</td><td align=right width=45>".$avg."</td></tr>\n";
print "</table>";

?>
<br>