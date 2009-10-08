<?php
$inpage=40;

$by=$_GET["by"];
$filter=$_GET["filter"];
$domains=intval($_GET["domains"]);
$shorturl=intval($_GET["shorturl"]);

$DATELINK="&amp;by=".$by."&amp;shorturl=".$shorturl."&amp;domains=".$domains."&amp;filter=".urlencode($filter);

if ($domains==1) {
	$ADMENU.="<a href=\"index.php?st=searchpages&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("domains",$DATELINK)."\">".$LANG["without domains"]."</a><br>";
	$ADMENU.=$LANG["with domains"];
	}
else {
	$ADMENU.=$LANG["without domains"]."<br>";
	$ADMENU.="<a href=\"index.php?st=searchpages&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("domains",$DATELINK)."&amp;domains=1\">".$LANG["with domains"]."</a>";
	}

$ADMENU.="<br><img src=\"img/none.gif\" width=1 height=5><br>";

if ($shorturl==1) {
	$ADMENU.="<a href=\"index.php?st=searchpages&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("shorturl",$DATELINK)."\">".$LANG["full url"]."</a><br>";
	$ADMENU.=$LANG["short url"];
	}
else {
	$ADMENU.=$LANG["full url"]."<br>";
	$ADMENU.="<a href=\"index.php?st=searchpages&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("shorturl",$DATELINK)."&amp;shorturl=1\">".$LANG["short url"]."</a>";
	}

$ADMENU.="<br><img src=\"img/none.gif\" width=1 height=5><br>";

$quer=$quer1="";
$cnt=0;
$PRE=$PRU=$PPP=Array();

$r=cnstats_sql_query("SELECT * FROM cns_data WHERE type=1 ORDER BY id");
while ($a=mysql_fetch_assoc($r)) {
	$url=trim($a["d1"]);
	$name=trim($a["d2"]);
	$regexp=trim($a["d3"]);
	$parent=trim($a["d4"]);

	$PRU[$url]=$parent;
	$PRE[$url]=$regexp;
	$PPP[$url]=trim($a["d5"]);

	if (!empty($url)) {
		$quer=$quer."(IF(LOCATE('$url',referer)!=0,referer,\n";
		$quer1=$quer1."(IF(LOCATE('$url',referer)!=0,'$name|||$regexp|||$url',\n";
		$cnt++;
		}
	}
$quer=$quer."'no'";
$quer1=$quer1."'no'";

for ($i=0;$i<$cnt;$i++) {
	$quer=$quer."))";
	$quer1=$quer1."))";
	}

switch($by){
	case "hits":
		$ADMENU.="<a href='index.php?st=".$st."&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("by",$DATELINK)."&amp;by=hosts'>".$LANG["by hosts"]."</a><br>";
		$ADMENU.="<a href='index.php?st=".$st."&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("by",$DATELINK)."&amp;by=users'>".$LANG["by users"]."</a><br>";
		$ADMENU.=$LANG["by hits"]."<br>";
		$az="";
		break;
	case "users":
		$ADMENU.="<a href='index.php?st=".$st."&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("by",$DATELINK)."&amp;by=hosts'>".$LANG["by hosts"]."</a><br>";
		$ADMENU.=$LANG["by users"]."<br>";
		$ADMENU.="<a href='index.php?st=".$st."&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("by",$DATELINK)."&amp;by=hits'>".$LANG["by hits"]."</a><br>";
		$az=" type1=1 AND ";
		break;
	case "hosts":default:
		$ADMENU.=$LANG["by hosts"]."<br>";
		$ADMENU.="<a href='index.php?st=".$st."&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("by",$DATELINK)."&amp;by=users'>".$LANG["by users"]."</a><br>";
		$ADMENU.="<a href='index.php?st=".$st."&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("by",$DATELINK)."&amp;by=hits'>".$LANG["by hits"]."</a><br>";
		$az=" type=1 AND ";
		break;
}

if ($shorturl==1) 
	$pagesql="IF(LOCATE('%3F',page),LEFT(page,LOCATE('%3F',page)-1),page)";
else
	$pagesql="page";

$sqlflt=GenerateFilter($filter);

if ($domains==1) 
	$pagesql="page";
else 
	$pagesql="IF(STRCMP(LEFT(page,13),'http%3A%2F%2F')=0,IF(LOCATE('%2F',page,13),SUBSTRING(".$pagesql.",LOCATE('%2F',".$pagesql.",13)),'/'),".$pagesql.")";

$r=cnstats_sql_query("SELECT ".$quer.",count(referer),".$quer1.",referer, ".$pagesql."
          FROM cns_log
          WHERE ".$az." date>'".$startdate."' AND date<'".$enddate."' AND referer LIKE 'http%' ".$sqlflt."
          GROUP BY ".$pagesql.",referer
          ORDER BY 2 desc;");

$PH=$NM=$CN=$LN=Array();
while ($a=mysql_fetch_array($r,MYSQL_NUM)) {
	list($ssystem,$regexp,$url)=explode("|||",$a[2]);
	$data=GetRegexpPhrase($regexp,$a[0],$url);
	if ($data!="" && $ssystem!="no") {
		if (isset($PH[$a[4]])) $CN[$a[4]]+=$a[1]; else $CN[$a[4]]=$a[1];
		$PH[$a[4]][]=$data;
		$NM[$a[4]][]=$ssystem;
		$LN[$a[4]][]=$a[3];
		$PN[$a[4]][]=$a[1];
		}
	}

arsort($CN);

$count=0;
while (list ($key, $val) = each ($CN)) {
	if ($count>=$start && $count<$start+$inpage) {
		$TABLED[]=$key;
		$TABLEC[]=$val;
		}
	$count++;
    }
?>
<STYLE>
<!--
.vis1 { visibility:visible; display:inline; }
.vis2 { visibility:hidden; display:none; }
//-->
</STYLE>
<SCRIPT Language="JavaScript" type="text/javascript">
<!--
function ptable_exp(idp) {
	var t=document.getElementById("tp"+idp);
	var i=document.getElementById("ip"+idp);
	if (t.className=="vis1") {
		t.className="vis2";
		i.src="img/expand.gif";
		}
	else {
		t.className="vis1";
		i.src="img/colapse.gif";
		}
	}
//-->
</SCRIPT>
<?php
if ($count!=0) {
	LeftRight($start,$inpage,$num,$count,0);
	print $TABLE;
	print "<tr class=tbl1><td align='center' width=75>&nbsp;<b>".$LANG["search system"]."</b></td><td align=center width=251><b>".$LANG["found pages"]."</b></td><td align='center' width=45><b>".$LANG["count"]."</b></td></tr>";
	while (list ($key, $val) = each ($TABLED)) {
		if ($class!="tbl1") $class="tbl1"; else $class="tbl2";
		print ("\n<tr class=".$class.">\n<td></td><td><a href='".urldecode($val)."' target='_blank'>".urldecode($val)."</a></td>\n<td align=right width='10%'>&nbsp;".$TABLEC[$key]."&nbsp;</td></tr>");
		if (count($PH[$val])>1) {
?>
<tr class="tbl0"><td colspan=2 style="height:18"><a href="JavaScript:ptable_exp(<?=$key;?>);"><img id='ip<?=$key;?>' src='img/<?=!$expanded?"expand":"colapse";?>.gif' width=15 height=15 border=0  align='left'></a>&nbsp;<?=$LANG["search phrases"];?></td><td align=right width='10%'>&nbsp;<font color='teal'><?=count($PH[$val]);?></font>&nbsp;</td></tr>
<?php	
			print ("<tr><td colspan=3><table id='tp".$key."' class='vis2' cellspacing=1 cellpadding=3 width='100%'>");
			while (list ($pkey,) = each ($PH[$val])) {
				if ($classp!="tbl1") $classp="tbl1"; else $classp="tbl2";
				print ("\n<tr class=".$classp.">\n<td width=84>&nbsp;<a target='_blank' href='".$LN[$val][$pkey]."'>".$NM[$val][$pkey]."</a></td>\n<td colspan='2'>".phrase_uncode($PH[$val][$pkey])."</td><td align=right width='10%'>&nbsp;<font color='teal'>".$PN[$val][$pkey]."</font>&nbsp;</td>\n</tr>");
		    }
			print "\n</table></td></tr>\n";

		}else{
			if ($class!="tbl1") $class="tbl1"; else $class="tbl2";
			if ($PN[$val][0]>1)
				print ("\n<tr class=".$class.">\n<td width=85>&nbsp;&nbsp;<a target='_blank' href='".$LN[$val][0]."'>".$NM[$val][0]."</a></td>\n<td>".phrase_uncode($PH[$val][0])."</td><td align=right width='10%'>&nbsp;<font color='teal'>".$PN[$val][0]."</font>&nbsp;</td></tr>\n");
			else
				print ("\n<tr class=".$class.">\n<td width=85>&nbsp;&nbsp;<a target='_blank' href='".$LN[$val][0]."'>".$NM[$val][0]."</a></td>\n<td colspan=2>".phrase_uncode($PH[$val][0])."</td></tr>\n");
		}
    }
	print "\n</table>\n";
	LeftRight($start,$inpage,$num,$count);
	}
?>