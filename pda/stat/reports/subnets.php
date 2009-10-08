<?php
$op=$_GET["op"];
$filter=$_GET["filter"];

$DATELINK="&amp;filter=".urlencode($filter);

if ($op=="del1") {
	$sql="";
	while (list ($key, $val) = each ($_GET)) {
		if (substr($key,0,2)=="n_" && $val=="on") $sql.=" OR uniqueid='".substr($key,2)."'";
	    }
	if (!empty($sql)) 
		cnstats_sql_query("DELETE FROM cns_subnets WHERE ".substr($sql,3).";");
	header("Location: index.php?st=".$st."&stm=".$stm."&ftm=".$ftm."&op=list&filter=".$filter);
	exit;
	}

if ($op=="add3" || $op=="add2" || $op=="add1") {
	$ip=$_GET["ip"];
	$mask=$_GET["mask"];
	$title=htmlspecialchars($_GET["title"]);
	$filter=$_GET["filter"];

	switch($op) {
		case "add1":
			$mask=ip2long($mask);
			$ip=ip2long($ip);
			$ip1=$ip&$mask;
			$ip2=($ip&$mask)+~$mask;
			break;
		case "add2":
			$c=$mask;
			$mask=0xFFFFFFFF;
			for ($j=0;$j<32-$c;$j++) $mask=$mask<<1;
			$ip=ip2long($ip);
			$ip1=$ip&$mask;
			$ip2=($ip&$mask)+~$mask;
			break;
		case "add3":
			$ip1=ip2long($ip);
			$ip2=ip2long($mask);
		}
	$r=cnstats_sql_query("SELECT id FROM cns_subnets WHERE title='".$title."';");
	if (mysql_num_rows($r)!=0) {
		$id=mysql_result($r,0,0);
		}
	else {
		$r=cnstats_sql_query("SELECT max(id) FROM cns_subnets;");
		$id=intval(mysql_result($r,0,0))+1;
		}
	cnstats_sql_query("INSERT INTO cns_subnets SET id='".$id."', ip1='".$ip1."', ip2='".$ip2."', title='".$title."'");
	header("Location: index.php?st=".$st."&stm=".$stm."&ftm=".$ftm."&op=list&filter=".$filter);
	exit;
	}

if (empty($op)) {
	$ADMENU.=$LANG["report"]."<br>";

	$pnum=0;
	$sql="999999";
	$title[$sql]=$LANG["other nets"];
	$r=cnstats_sql_query("SELECT * FROM cns_subnets ORDER by title;");
	if (mysql_num_rows($r)>0) {
		while ($a=mysql_fetch_array($r,MYSQL_ASSOC)) {
			$num=$a["id"];
			$sql="IF((ip>".$a["ip1"]." AND ip<".$a["ip2"]."),".$num.",".$sql.")";
			$title[$num]=$a["title"];
			}
		}

	$sqlflt=GenerateFilter($filter);
	$r=cnstats_sql_query("select ".$sql.",count(*) from cns_log WHERE date>'".$startdate."' AND date<'".$enddate."' AND type=1 ".$sqlflt." group by 1 order by 2 desc");

$cnt_total=0;
$cnt_others=0;
while($a=mysql_fetch_row($r)){
if($title[$a[0]]!=$LANG["other nets"]){
	$TABLED[]=$title[$a[0]];
	$TABLEC[]=$a[1];
	$cnt_total+=$a[1];
	}
	else $cnt_others=$a[1];
	}
ShowTable(0);
print"<P style='margin:10px;'><B>".$LANG["total"].":</B></P>";
$total=$LANG["total"];
if(isset($LANG["reports"][$st]))$total=$LANG["reports"][$st];
else for($i=0;$i<count($MENU);$i+=3)
if($st==$MENU[$i+1])$total=$MENU[$i+2]."\n";
$TABLED=$TABLEC=Array();
$TABLED[]=$total;
$TABLEC[]=$cnt_total;
$TABLED[]=$LANG["other nets"];
$TABLEC[]=$cnt_others;

ShowTable(0);
	}
else $ADMENU.="<a href='index.php?st=".$st."&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("op",$DATELINK)."'>".$LANG["report"]."</a><br>";

if ($op=="list") {
	$NOFILTER=1;
	$ADMENU.=$LANG["subnets list"]."<br>";
	$r=cnstats_sql_query("SELECT * FROM cns_subnets ORDER by title;");
	if (mysql_num_rows($r)>0) {
		print "<form class='m0' action='index.php' method='get'>";
		print $TABLE;
		while ($a=mysql_fetch_array($r,MYSQL_ASSOC)) {
			if ($class!="tbl1") $class="tbl1"; else $class="tbl2";
			print "<tr class='".$class."'>";
			print "<td width='6%'><input type=checkbox name='n_".$a["uniqueid"]."'></td>";
			print "<td width='20%'>".long2ip($a["ip1"])."</td><td width='2%'>-</td><td width='20%'>".long2ip($a["ip2"])."</td>";
			print "<td width='52%'>".$a["title"]."</td>";
			print "</tr>";
			}
		print "</table>\n";
		print "<input type='hidden' name='filter' value='".urlencode($filter)."'>\n";
		print "<input type='hidden' name='stm' value='".$stm."'>\n";
		print "<input type='hidden' name='ftm' value='".$ftm."'>\n";
		print "<input type='hidden' name='st' value='".$st."'>\n";
		print "<input type='hidden' name='op' value='del1'>\n";
		print "<input type='hidden' name='nowrap' value='1'>\n";
		print "<br><center><input type='submit' value='".$LANG["delete selected"]."'></center>";
		print "</form>\n";
		}
	else {
		print $TABLE."<tr><td align=center>";
		print $LANG["nosubnets"];
		print "</td></tr></table>";
		}

	print "<center><br>\n";
	print "<form class='m0' action='index.php'><table width='250' cellspacing='1' border='0' cellpadding='3' bgcolor='#D4F3D7'>\n";
	print "<tr class='tbl0'><td colspan=2 align='center'><B>".$LANG["longmask"]."</B></td></tr>\n";
	print "<tr class='tbl2'><td width='20%'>IP:&nbsp;</td><td width='80%'><input style='width:100%' type='text' name='ip'><br>".$LANG["example"].": ".$REMOTE_ADDR."</td></tr>\n";
	print "<tr class='tbl2'><td>".$LANG["mask"].":&nbsp;</td><td><input style='width:100%' type='text' name='mask'><br>".$LANG["example"].": 255.255.255.0</td></tr>\n";
	print "<tr class='tbl2'><td>".$LANG["sunbets title"].":&nbsp;</td><td><input style='width:100%' type='text' name='title'><br>".$LANG["example"].": United Kingdom</td></tr>\n";
	print "<tr class='tbl1'><td colspan=2 align='center'><input type=submit value='".$LANG["add"]."'></td></tr>\n";
	print "</table>\n";
	print "<input type='hidden' name='filter' value='".urlencode($filter)."'>\n";
	print "<input type='hidden' name='st' value='".$st."'>\n";
	print "<input type='hidden' name='stm' value='".$stm."'>\n";
	print "<input type='hidden' name='ftm' value='".$ftm."'>\n";
	print "<input type='hidden' name='op' value='add1'>\n";
	print "<input type='hidden' name='nowrap' value='1'>\n";
	print "</form>\n";

	print "<br>";

	print "<form class='m0' action='index.php'><table width='250' cellspacing='1' border='0' cellpadding='3' bgcolor='#D4F3D7'>\n";
	print "<tr class='tbl0'><td colspan=2 align='center'><B>".$LANG["shortmask"]."</B></td></tr>\n";
	print "<tr class='tbl2'><td width='20%'>IP:&nbsp;</td><td width='80%'><input style='width:100%' type='text' name='ip'><br>".$LANG["example"].": ".$REMOTE_ADDR."</td></tr>\n";
	print "<tr class='tbl2'><td>".$LANG["mask"].":&nbsp;</td><td><input style='width:100%' type='text' name='mask'><br>".$LANG["example"].": 24</td></tr>\n";
	print "<tr class='tbl2'><td>".$LANG["sunbets title"].":&nbsp;</td><td><input style='width:100%' type='text' name='title'><br>".$LANG["example"].": United Kingdom</td></tr>\n";
	print "<tr class='tbl1'><td colspan=2 align='center'><input type=submit value='".$LANG["add"]."'></td></tr>\n";
	print "</table>\n";
	print "<input type='hidden' name='filter' value='".urlencode($filter)."'>\n";
	print "<input type='hidden' name='st' value='".$st."'>\n";
	print "<input type='hidden' name='stm' value='".$stm."'>\n";
	print "<input type='hidden' name='ftm' value='".$ftm."'>\n";
	print "<input type='hidden' name='op' value='add2'>\n";
	print "<input type='hidden' name='nowrap' value='1'>\n";
	print "</form>\n";
	print "<br>";

	print "<form class='m0' action='index.php'><table width='250' cellspacing='1' border='0' cellpadding='3' bgcolor='#D4F3D7'>\n";
	print "<tr class='tbl0'><td colspan=2 align='center'><B>".$LANG["fromto"]."</B></td></tr>\n";
	print "<tr class='tbl2'><td width='20%'>".$LANG["startip"].":&nbsp;</td><td width='80%'><input style='width:100%' type='text' name='ip'><br>".$LANG["example"].": 192.168.102.0</td></tr>\n";
	print "<tr class='tbl2'><td>".$LANG["endip"].":&nbsp;</td><td><input style='width:100%' type='text' name='mask'><br>".$LANG["example"].": 192.168.102.255</td></tr>\n";
	print "<tr class='tbl2'><td>".$LANG["sunbets title"].":&nbsp;</td><td><input style='width:100%' type='text' name='title'><br>".$LANG["example"].": United Kingdom</td></tr>\n";
	print "<tr class='tbl1'><td colspan=2 align='center'><input type=submit value='".$LANG["add"]."'></td></tr>\n";
	print "</table>\n";
	print "<input type='hidden' name='filter' value='".urlencode($filter)."'>\n";
	print "<input type='hidden' name='st' value='".$st."'>\n";
	print "<input type='hidden' name='stm' value='".$stm."'>\n";
	print "<input type='hidden' name='ftm' value='".$ftm."'>\n";
	print "<input type='hidden' name='op' value='add3'>\n";
	print "<input type='hidden' name='nowrap' value='1'>\n";
	print "</form></center>\n";

	}
else $ADMENU.="<a href='index.php?dateoff=1&amp;st=".$st."&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("op",$DATELINK)."&amp;op=list'>".$LANG["subnets list"]."</a><br>";
?>
<br>