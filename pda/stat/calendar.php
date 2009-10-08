<?
include "_funct.php";

@mysql_connect($STATS_CONF["sqlhost"],$STATS_CONF["sqluser"],$STATS_CONF["sqlpassword"]) or die("Error connectiong to database.\n<hr size=1><b>Host:</b> ".$STATS_CONF["sqlhost"]."\n<br><b>Login:</b> ".$STATS_CONF["sqluser"]."\n<br><b>Using password</b>: ".(empty($STATS_CONF["sqlpassword"])?"no":"yes"));
@mysql_select_db($STATS_CONF["dbname"]) or die("Connecting to MySql...Ok<hr size=1>\nError selecting database<br>\n<B>Database name:</B> ".$STATS_CONF["dbname"]);

$CONFIG=mysql_fetch_array(cnstats_sql_query("SELECT language FROM cns_config"));
include "lang/lang_".$CONFIG["language"].".php";

$el=$_GET["el"]=="sd"?"sd":"fd";

$MONTH=$LANG_MONTH;

?>
<HTML>
<HEAD>
<TITLE><?=$LANG["softname"];?></TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=<?=$LANG["charset"];?>">
<STYLE>
<!--
.tiny {font-size:9px;font-family:tahoma;}
a,a:visited {text-decoration:none;color:blue;}
a:hover {text-decoration:underline}
//-->
</STYLE>
<SCRIPT>
<!--
function ret(str) {
	var nel=window.opener.document.getElementById("<?=$el;?>");
	nel.value=str;
	window.close();
	}
//-->
</SCRIPT>
</HEAD>
<BODY marginwidth=0 marginheight=0 topmargin=0 bottommargin=0 leftmargin=0 rightmargin=0 bgcolor=white background="img/bg.gif">
<?

function ShowCalendar($stamp,$p,$hm) {
	GLOBAL $MONTH,$day,$month,$year,$prom;

	$y=date("Y",$stamp);
	$d=date("d",$stamp);
	$m=date("m",$stamp);

	$time=mktime(0,0,0,$m,$d,$y);
	$stime=mktime(0,0,0,$m,1,$y);
	$seltime1=mktime(0,0,0,$month,$day-$prom,$year);
	$seltime2=mktime(0,0,0,$month,$day,$year);
	$ed=date("t",$time);
	$w=date("w",$stime);
	if ($w==0) $w=7;

	print "<table border=0 bgcolor=#CFEEDE cellspacing=1 cellpadding=2 width=120>";

	print "<tr><td bgcolor=#F3FBF7 colspan=7 align=center class=tiny><B>";
	print $MONTH[intval(date("m",$time))]." ".date("Y",$time)."</B></td></tr>";

	$tr=true;
	print "<tr bgcolor=#F8F8F8>";
	for ($i=1;$i<$w;$i++) print "<td>&nbsp;</td>";
	for ($i=1;$i<=$ed;$i++) {
		$bg="";

		if (!$tr) {print "<tr bgcolor=#F3FBF7>";$tr=true;}
		$ri=$i<10?"0".$i:$i;
		$links="<a href='javascript:ret(\"".$y."-".$m."-".$ri." ".$hm."\");'>";
		$linke="</a>";

		if ($p==0) {if (mktime(0,0,0,$m,$i,$y)<$time) $links=$linke="";}
		else {if (mktime(0,0,0,$m,$i,$y)>$time) $links=$linke="";}

		if (mktime(0,0,0,$m,$i,$y)>=$seltime1 && mktime(0,0,0,$m,$i,$y)<=$seltime2) $bg="bgcolor=#CCEDBF";
		
		print "<td class=tiny align=right $bg>".$links.$i.$linke."</td>";
		$w++;
		if ($w>7) {$w=1;print "</tr>\n";$tr=false;}
		}
	if ($w!=1) for ($i=$w;$i<8;$i++) print "<td>&nbsp;</td>";
	if ($tr) print "</tr>\n";

	print "</table>";	
	}

$hm=$el=="sd"?"00:00":"23:59";

print ShowCalendar(mktime(0,0,0,date("m")-1,date("d"),date("Y")),0,$hm);
print "<img src=img/none.gif width=1 height=3><br>";
print ShowCalendar(time(),1,$hm);
?>
</HTML>
</HEAD>