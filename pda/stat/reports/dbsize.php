<?php
$filter=$_GET["filter"];

$r=cnstats_sql_query("SELECT UNIX_TIMESTAMP(date) as date,size FROM cns_size ORDER BY date DESC LIMIT 50");

$sum=0;$min=99999999999;$max=0;
while ($a=mysql_fetch_array($r,MYSQL_NUM)) {
	$sum+=$a[1];
	if ($min>$a[1]) $min=$a[1];
	if ($max<$a[1]) $max=$a[1];
	$TABLED[]=date("Ymd ".$LANG["date_format"],$a[0])."~~~".$a[1];
	}

if (!is_array($TABLED)) {
	print "<br><center>".$LANG["no data"]."</center><br>";
	}
else {
	@sort($TABLED);

	if ($sum!=0) $avg=intval($sum/count($TABLED)); else $agv=0;

	$DATA["x"]=$DATA[0]=$DATA[1]=$DATA[2]=Array();
	$d="";$prev=0;
	while (list ($key, $val) = @each ($TABLED)) {
		
		list($val,$size)=explode("~~~",$TABLED[$key]);
		$val=substr($val,8);
		if ($prev==0) $diff=0; else $diff=$size-$prev;
		$prev=$size;
		if ($class!="tbl1") $class="tbl1"; else $class="tbl2";
		$v="<tr class=\"".$class."\">\n";
		$v.="<td align=\"center\">".$val."</td>\n";
		$v.="<td align=\"right\">".cNumber($size)."</td>\n";
		if ($diff<0) $color="green";
		if ($diff>0) $color="red";
		if ($diff==0) $color="black";
		$v.="<td align=\"right\" style='color:".$color."'>".cNumber($diff)."</td>\n";
		$v.="</tr>\n";
		$d=$v.$d;
		$DATA["x"][]=$val;
		$DATA[0][]=$size;
		$DATA[1][]=$size;
		$DATA[2][]=$size;
		}
	
	$HTTP_SESSION_VARS["DATA"]=$DATA;
	
	$type=1;
	$GDVERSION=gdVersion();
	if ($GDVERSION==2 && $CONFIG["antialias"]==0) $GDVERSION=1;
	if ($GDVERSION==0) $CONFIG["diagram"]=0;
	
	if ($CONFIG["diagram"]>0 && $CONFIG["diagram"]<4) {
		$img_antialias="antialias=".($GDVERSION==1?0:1);
		print "<center><img vspace=5 src=\"graph/lines.php?".$img_antialias."&rnd=".time()."\" width=\"".$IMGW."\" height=\"".$IMGH."\"><br>\n";
		}
	else include "graph/html.php";

	print "<br>".$TABLE;
	print "<tr class=\"tbl1\">";
	print "<td align=\"center\"><B>".$LANG["date"]."</B></td>";
	print "<td align=\"center\"><B>".$LANG["sizeofdb"]."</B></td>";
		print "<td align=\"center\"><B>".$LANG["sizeofdbdiff"]."</B></td>";
	print "</tr>";
	
	print $d."</table></center>\n";
	}

$NOFILTER=1;
?>

