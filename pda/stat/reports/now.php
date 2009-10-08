<?php
$inpage=40;

$_GET["dateoff"]=1;
$by=intval($_GET["by"]);
$filter=$_GET["filter"];

$DATELINK="&amp;filter=".urlencode($filter);

if ($by==1) {
	$ADMENU.="<a href='index.php?st=now&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("by",$DATELINK)."&amp;by=0'>".$LANG["fullreport"]."</a><br>";
	$ADMENU.=$LANG["simplereport"];
	}
else {
	$ADMENU.=$LANG["fullreport"]."<br>";
	$ADMENU.="<a href='index.php?st=now&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("by",$DATELINK)."&amp;by=1'>".$LANG["simplereport"]."</a>";
	}


$sdate=date("Y-m-d H:i:s",time()-240+$COUNTER["timeoffset"]);
$edate=date("Y-m-d H:i:s",time()+$COUNTER["timeoffset"]);

$sqlflt=GenerateFilter($filter);
if ($by==1) 
	$r=cnstats_sql_query("select date,page,ip,referer,id from cns_log WHERE date>'".$sdate."' AND date<'".$edate."' ".$sqlflt." group by ip order by 1 desc");
else
	$r=cnstats_sql_query("select date,page,ip,referer,id,proxy,agent,language,country,type from cns_log WHERE date>'".$sdate."' AND date<'".$edate."' ".$sqlflt." group by ip order by 1 desc");

$count=mysql_num_rows($r);

print "<br><div align=right>".$LANG["on-line"].": <B>".$count."</B>&nbsp;&nbsp;</div><br>";

if ($by==1) {
	print $TABLE;
	print "<tr class=tbl0>";
	print "<td width=115>&nbsp;<b>".$LANG["date"]."<br>&nbsp;".$LANG["ip"]."</b></td>";
	print "<td valign=top><b>&nbsp;".$LANG["url"]."<br>&nbsp;".$LANG["referer"]."</b></td>";
	print "</tr>";

	if ($start+$inpage>$count) $finish=$count; else $finish=$start+$inpage;
	$num=$start;
	for ($i=$start;$i<$finish;$i++) {
	    $date=mysql_result($r,$i,0);
	    $page=mysql_result($r,$i,1);
	    $ip=long2ip(mysql_result($r,$i,2));
	    $from=mysql_result($r,$i,3);
	    $rid=mysql_result($r,$i,4);
	    if ($class!="tbl1") $class="tbl1"; else $class="tbl2";
	    $num++;
		$page=urldecode($page);
		$page_dec=phrase_uncode($page);
		$from_dec=phrase_uncode(urldecode($from));

	    if (strlen($page_dec)>55) $printdata1=substr($page_dec,0,55)."..."; else $printdata1=$page_dec;
	    if (strlen($from_dec)>55) $printdata2=substr($from_dec,0,55)."..."; else $printdata2=$from_dec;

	    print "<tr class=".$class.">";
	    print "<td valign=top ".$TDS.">&nbsp;$date<br><a href='index.php?rid=".$rid."&amp;st=ipinfo'>&nbsp;".$ip."</a></td>";
	    print "<td valign=top ".$TDS."><a href='".$page."' target='_blank'>".$printdata1."<br></a><a href='".$from."' target='_blank'>".$printdata2."</a></td>";
	    print "</tr>\n";
	    }
	print "</table>\n";
	}

else {
	if ($start+$inpage>$count) $finish=$count; else $finish=$start+$inpage;
	$num=$start;
	for ($i=$start;$i<$finish;$i++) {
	    $date=mysql_result($r,$i,0);
	    $page=mysql_result($r,$i,1);
	    $ip=long2ip(mysql_result($r,$i,2));
	    $from=mysql_result($r,$i,3);
	    $rid=mysql_result($r,$i,4);
	    if ($class!="tbl1") $class="tbl1"; else $class="tbl2";
	    $num++;
		$page=urldecode($page);
		$page_dec=phrase_uncode($page);
		$from_dec=phrase_uncode(urldecode($from));

		$proxy=mysql_result($r,$i,5)==-1?"":long2ip(mysql_result($r,$i,5));
		if (empty($proxy)) $proxy=$LANG["noproxy"];

	    if (strlen($page_dec)>70) $printdata1=substr($page_dec,0,70)."..."; else $printdata1=$page_dec;
	    if (strlen($from_dec)>70) $printdata2=substr($from_dec,0,70)."..."; else $printdata2=$from_dec;

		if ($ip=="255.255.255.255") $ip=$LANG["unknownip"];
		else $ip="<a target=\"blank\" href=\"index.php?rid=".$rid."&amp;st=ipinfo\">".$ip."</a>";

		$lr=mysql_query("SELECT eng FROM cns_languages WHERE code='".mysql_escape_string(substr(mysql_result($r,$i,7),0,2))."';");
		if (mysql_num_rows($lr)==1) $language=mysql_result($lr,0,0)." (".mysql_result($r,$i,7).")";
		else $language=mysql_result($r,$i,7);

		print $TABLE;
	    print "<tr class=\"tbl2\"><td width=\"100\">".$LANG["date"]."</td><td>".$date."</td></tr>\n";
	    print "<tr class=\"tbl1\"><td>".$LANG["url"]."</td><td><a href='".$page."' target='_blank'>".$printdata1."</a></td></tr>\n";
	    print "<tr class=\"tbl1\"><td>".$LANG["referer"]."</td><td><a href='".$from."' target='_blank'>".$printdata2."</a></td></tr>\n";
	    print "<tr class=\"tbl2\"><td>".$LANG["ip"]."</td><td>".$ip."</td></tr>\n";
	    print "<tr class=\"tbl2\"><td>".$LANG["proxy"]."</td><td>".$proxy."</td></tr>\n";
	    print "<tr class=\"tbl2\"><td>User-Agent</td><td>".mysql_result($r,$i,6)."</td></tr>\n";
	    print "<tr class=\"tbl2\"><td>".$LANG["language"]."</td><td>".$language."</td></tr>\n";

		$country=mysql_result($r,$i,8);
		if ($country!=0) {
			$tld="";
			if ($country=="0") $country=$LANG["other countries"];
			else {
				$tld=chr($country>>8).chr($country&0xFF);
				if (isset($COUNTRY[$tld])) $country=$COUNTRY[$tld];
				else $country=$tld;
	
				$country="<img src=img/countries/".strtolower($tld).".gif width=18 height=12 border=0 align=absmiddle>&nbsp;&nbsp;".$country;
				}
		    print "<tr class=\"tbl2\"><td>".$LANG["country"]."</td><td>".$country."</td></tr>\n";
			}

	    print "</tr>\n";
		print "</table>\n<br>\n";
	    }
	}

if ($count>0) {
	print "<center><img src='img/none.gif' alt='' width=1 height=5><br>\n";
	$prev=$start-$inpage;
	if ($prev>=0) {
    	print "<a href='index.php?st=".$st."&amp;start=".$prev."'>&lt;&lt; ".$LANG["prev"]."</a>\n";
	    }
	print " [ ".$LANG["page"]." ".my_round(0.9999+$num/$inpage)."/".my_round(0.9999+$count/$inpage)." ]\n";
	$next=$start+$inpage;
	if ($next<$count) {
    	print " <a href='index.php?st=$st&amp;start=".$next."'>".$LANG["next"]." &gt;&gt;</a>\n";
	    }
	print "</center><img src='img/none.gif' alt='' width=1 height=5><br>";
	}
?>