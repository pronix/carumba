<?php
$by=$_GET["by"];
$op=$_GET["op"];
$filter=$_GET["filter"];

$DATELINK="&amp;filter=".urlencode($filter);

if ($op=="del") {
	$sql="";
	while (list ($key, $val) = each ($_GET)) {
		if (substr($key,0,2)=="n_" && $val=="on") {
			$e=explode("_",substr($key,2));
			$sql.=" OR (url='".base64_decode($e[0])."' AND name='".base64_decode($e[1])."')";
			}
	    }                 

	if (!empty($sql)) 
		cnstats_sql_query("DELETE FROM cns_goodies WHERE ".substr($sql,3).";");
	header("Location: index.php?st=".$st."&stm=".$stm."&ftm=".$ftm."&op=list&filter=".$filter);
	exit;
	}


if ($op=="add1") {
	$url=htmlspecialchars($_GET["url"]);
	$title=htmlspecialchars($_GET["title"]);
	$url=urlencode($url);
	$title=urlencode($title);
	$r=cnstats_sql_query("SELECT count(*) FROM cns_goodies WHERE url='".$url."'");
	if (mysql_result($r,0,0)==0) {
		cnstats_sql_query("INSERT INTO cns_goodies SET url='".$url."',name='".$title."';");
		}
	header("Location: index.php?st=".$st."&stm=".$stm."&ftm=".$ftm."&op=list&filter=".$filter);
	exit;
	}

if (empty($op)) {
	switch($by){
	case "hits":
		$ADMENU_E.="<a href='index.php?st=".$st."&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("by",$DATELINK)."&amp;by=hosts'>".$LANG["by hosts"]."</a><br>";
		$ADMENU_E.="<a href='index.php?st=".$st."&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("by",$DATELINK)."&amp;by=users'>".$LANG["by users"]."</a><br>";
		$ADMENU_E.=$LANG["by hits"]."<br>";
		$az="";
		break;
	case "users":
		$ADMENU_E.="<a href='index.php?st=".$st."&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("by",$DATELINK)."&amp;by=hosts'>".$LANG["by hosts"]."</a><br>";
		$ADMENU_E.=$LANG["by users"]."<br>";
		$ADMENU_E.="<a href='index.php?st=".$st."&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("by",$DATELINK)."&amp;by=hits'>".$LANG["by hits"]."</a><br>";
		$az=" type1=1 AND ";
		break;
	case "hosts":default:
		$ADMENU_E.=$LANG["by hosts"]."<br>";
		$ADMENU_E.="<a href='index.php?st=".$st."&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("by",$DATELINK)."&amp;by=users'>".$LANG["by users"]."</a><br>";
		$ADMENU_E.="<a href='index.php?st=".$st."&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("by",$DATELINK)."&amp;by=hits'>".$LANG["by hits"]."</a><br>";
		$az=" type=1 AND ";
		break;
	}
	$query="";
	$count_query=0;
	$sql_goodies=cnstats_sql_query("SELECT url,name FROM cns_goodies;");
	for ($i=0;$i<mysql_num_rows($sql_goodies);$i++) {
		$s=explode("*",$str);
		$s[0]=urldecode(mysql_result($sql_goodies,$i,1));
		$s[1]=urldecode(mysql_result($sql_goodies,$i,0));
		if (!empty($s[0])) {
			$substring=substr($s[1],1,strlen($s[1]));
			$name=$s[0];
			$query=$query."IF(LOCATE('".$substring."',referer)!=0,'".$name."',";
			$count_query++;
			}
		}
	$query=$query."'".$LANG["other links"]."'";
	for ($i=0;$i<$count_query;$i++) $query=$query.")";

	$fltsql=GenerateFilter($filter);
	$r=cnstats_sql_query("SELECT ".$query.",count(referer)
              FROM cns_log
              WHERE ".$az." date>'".$startdate."' AND date<'".$enddate."' AND referer LIKE 'http%' ".$fltsql."
              GROUP BY ".$query."
              ORDER BY 2 desc;");

	$cnt_total=0;
	$cnt_others=0;
	while($a=mysql_fetch_row($r)){
	if($a[0]!=$LANG["other links"]){
		$TABLED[]=$a[0];
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
	$TABLED[]=$LANG["other links"];
	$TABLEC[]=$cnt_others;

	ShowTable(0);
	print "<br>";
	$ADMENU.=$LANG["report"]."<br>";
	}
else $ADMENU.="<a href='index.php?st=".$st."&amp;stm=".$stm."&amp;ftm=".$ftm.$DATELINK."'>".$LANG["report"]."</a><br>";

if ($op=="list") {
	$NOFILTER=1;
	$ADMENU.=$LANG["partners list"]."<br>";

	print "<form class='m0' action='index.php' method='get'>";
	print $TABLE;
	$r=cnstats_sql_query("SELECT url,name FROM cns_goodies;");
	print "<tr class='tbl1'><td width='6%'>&nbsp;</td><td align='center' width='47%'>&nbsp;<b>".$LANG["site url"]."</b></td><td align='center' width='47%'><b>".$LANG["title"]."</b></td><td align=center>&nbsp;</td></tr>";
	while ($a=mysql_fetch_array($r)) {
		if ($class!="tbl1") $class="tbl1"; else $class="tbl2";
		print ("<tr class='".$class."'><td><input type='checkbox' name='n_".base64_encode($a[0])."_".base64_encode($a[1])."'></td><td>".StripSlashes(urldecode($a[0]))."</td><td>".StripSlashes(urldecode($a[1]))."</td><td></td></tr>");
		}

	print "</table>";
	print "<br><center><input type='submit' value='".$LANG["delete selected"]."'></center>";
	print "<input type='hidden' name='op' value='del'>\n";
	print "<input type='hidden' name='nowrap' value='1'>\n";
	print "<input type='hidden' name='st' value='".$st."'>\n";
	print "<input type='hidden' name='stm' value='".$stm."'>\n";
	print "<input type='hidden' name='ftm' value='".$ftm."'>\n";
	print "<input type='hidden' name='filter' value='".urlencode($filter)."'>\n";
	print "</form>\n";

	print "<br><form action='index.php' method='get' class=m0>\n";
	print "<div align='center'><table width='350' cellspacing='1' border='0' cellpadding='3' bgcolor='#D4F3D7'>\n";
	print "<tr class='tbl2'><td width='30%'>".$LANG["site url"].":&nbsp;</td><td width='70%'><input type='text' style='width:100%' name='url' value='http://'></td></tr>\n";
	print "<tr class='tbl2'><td>".$LANG["title"]."&nbsp;</td><td><input type='text' style='width:100%' name='title' value=''></td></tr>\n";
	print "<tr class='tbl1'><td colspan=2 align='center'><input type='submit' value='".$LANG["add to partners list"]."'></td></tr>\n";
	print "</table></div>\n";

	print "<input type='hidden' name='st' value='".$st."'>\n";
	print "<input type='hidden' name='stm' value='".$stm."'>\n";
	print "<input type='hidden' name='ftm' value='".$ftm."'>\n";
	print "<input type='hidden' name='op' value='add1'>\n";
	print "<input type='hidden' name='nowrap' value='1'>\n";
	print "<input type='hidden' name='filter' value='".urlencode($filter)."'>\n";

	print "</form><br>\n";

	}
else $ADMENU.="<a href='index.php?dateoff=1&amp;st=".$st."&amp;stm=".$stm."&amp;ftm=".$ftm.$DATELINK."&amp;op=list'>".$LANG["partners list"]."</a><br>";
if(empty($op)){$ADMENU.="<img src=\"img/none.gif\" width=1 height=5><br>".$ADMENU_E;}
?>
