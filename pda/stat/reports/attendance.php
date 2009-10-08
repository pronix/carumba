<?php
$filter=$_GET["filter"];
$type=intval($_GET["type"]);
$day=intval($_GET["day"]);
$month=intval($_GET["month"]);
$year=intval($_GET["year"]);
$prom=intval($_GET["prom"]);
$cb_hits=$_GET["cb_hits"];
$cb_hosts=$_GET["cb_hosts"];
$cb_users=$_GET["cb_users"];
$graph=intval($_GET["graph"]);
$s=$_GET["s"];

$r=mysql_query("SHOW TABLE STATUS");
$size=0;
while ($a=mysql_fetch_array($r,MYSQL_ASSOC)) {
        while (list ($key, $val) = each ($a)) {
                if ($key=="Data_length" && (substr($tname,0,4)=="cns_")) $size+=$val;
                if ($key=="Index_length" && (substr($tname,0,4)=="cns_")) $size+=$val;
                if ($key=="Name") $tname=$val;
                if ($key=="Rows" && $tname=="cns_log") $rows=$val;
            }
        }

$ADMENU=$LANG["basesize"].":</td><td align=right>".cNumber($size/1024)."</td></tr><tr><td>".$LANG["baserows"].":</td><td align=right>".cNumber($rows);

// Входные параметры
if($_GET["second"]!=1){$r=cnstats_sql_query("SELECT show_hits, show_hosts, show_users FROM cns_config;");
		$a=mysql_fetch_array($r,MYSQL_ASSOC);
		mysql_free_result($r);
		$cb_hosts=$a["show_hosts"]==1?"on":"";
		$cb_hits=$a["show_hits"]==1?"on":"";
		$cb_users=$a["show_users"]==1?"on":"";
		$$table="on";
        $type=1;
        }

$dl=RemoveVar("stm",str_replace("&","&amp;",$_SERVER["QUERY_STRING"]));
$dl=RemoveVar("ftm",$dl);
$dl=RemoveVar("st",$dl);
$DATELINK="&amp;".$dl;
$sqlflt=GenerateFilter($filter);
if (mysql_num_rows(cnstats_sql_query("SELECT * FROM cns_counter_total"))==0){
	$tdate=date("Y-m-d H:i:s",mktime(0,0,0,date("m"),date("d"),date("Y"))+$COUNTER["timeoffset"]);
}else{
	$td=mysql_result(cnstats_sql_query("SELECT UNIX_TIMESTAMP(min(date)) FROM cns_counter_total;"),0,0);
	$tdate=date("Y-m-d H:i:s",mktime(0,0,0,date("m",$td),date("d",$td),date("Y",$td)));
}
// Начальные значения
$mini=$minh=$minu=99999999;
$maxi=$maxh=$maxu=0;

// Расчитываем значения для графиков
$DATA["x"]=$DATA[0]=$DATA[1]=$DATA[2]=Array();
if ($td==0) $limit=0; else $limit=44;

// Выводим таблицу
if ($type==0) { /* По часам ############################################# */
        $tm=time()+$COUNTER["timeoffset"];
        $sdate=date("Y-m-d H:i:s",mktime(0,0,0,date("m",$tm) ,date("d",$tm),date("Y",$tm)));
   	    $edate=date("Y-m-d H:i:s",mktime(23,59,59,date("m",$tm) ,date("d",$tm),date("Y",$tm)));
        $html.="<table width=\"".$TW."\" cellspacing=\"1\" cellpadding=\"3\" bgcolor=\"#D4F3D7\">";
        $html.="<tr class=\"tbl2\"><td align=\"center\"><b>".$LANG["date"]."</b></td><td align=\"center\"><b>".$LANG["hours"]."</b></td><td align=\"center\" width=\"100\"><b>".$LANG["hits"]."</b></td><td align=\"center\" width=\"100\"><b>".$LANG["hosts"]."</b></td><td align=\"center\" width=\"100\"><b>".$LANG["visitors"]."</b></td></tr>";

		if(empty($filter)){
	        $r=cnstats_sql_query("select LEFT(date,13),count(page),sum(type),sum(type1) from cns_log WHERE date>='$sdate' AND date<='$edate' GROUP BY LEFT(date,13) ORDER BY date desc");
		}else{	
			$r=cnstats_sql_query("select LEFT(date,13) AS date, count(page) AS hits, count(DISTINCT(concat(ip,concat(':',proxy)))) AS hosts , count(DISTINCT(uid)) AS users FROM cns_log WHERE date>='$sdate' AND date<='$edate' ".$sqlflt." GROUP BY LEFT(date,13) ORDER BY date desc");
		}

        if (mysql_num_rows($r)!=0) $date=substr(mysql_result($r,0,0),0,11);
        else $date=date("Y-m-d H:i:s");
        for ($i=0;$i<24;$i++) {
                $a_hits[$i]="-";
                $a_hosts[$i]="-";
                $a_users[$i]="-";
                } /* of for */

        for ($i=0;$i<mysql_num_rows($r);$i++) {
                $h=intval(substr(mysql_result($r,$i,0),11));
                $a_hits[$h]=mysql_result($r,$i,1);
                $a_hosts[$h]=mysql_result($r,$i,2);
                $a_users[$h]=mysql_result($r,$i,3);

                if ($mini>$a_hits[$h]) $mini=$a_hits[$h];
                if ($maxi<$a_hits[$h]) $maxi=$a_hits[$h];

                if ($minh>$a_hosts[$h]) $minh=$a_hosts[$h];
                if ($maxh<$a_hosts[$h]) $maxh=$a_hosts[$h];

                if ($minu>$a_users[$h]) $minu=$a_users[$h];
                if ($maxu<$a_users[$h]) $maxu=$a_users[$h];
                } /* of for */

        $thi=$tho=$tus=0;
        for ($i=23;$i>=0;$i--) {
				if($i<8||$i>18){$ak1="<font color=gray>";$ak2="</font>";} else $ak1=$ak2="";
	
                if ($class!="tbl1") $class="tbl1"; else $class="tbl2";
                $html.="<tr class=\"".$class."\">\n";

                $html.="\t<td align=\"center\">".$ak1.date($CONFIG["date_format"],strtotime($date)).$ak2."</td>\n";
                $html.="\t<td align=\"center\">".$ak1.$i.$ak2."</td>\n";

                $t1=$ak1;$t2=$ak2;
                if ($a_hits[$i]==$mini) {$t1="<font color=red><B>";$t2="</font></B>";}
                if ($a_hits[$i]==$maxi) {$t1="<font color=blue><B>";$t2="</font></B>";}
                $html.="\t<td align=\"right\">".$t1.$a_hits[$i].$t2."</td>\n";

                $t1=$ak1;$t2=$ak2;
                if ($a_hosts[$i]==$minh) {$t1="<font color=red><B>";$t2="</font></B>";}
                if ($a_hosts[$i]==$maxh) {$t1="<font color=blue><B>";$t2="</font></B>";}
                $html.="\t<td align=\"right\">".$t1.$a_hosts[$i].$t2."</td>\n";

                $t1=$ak1;$t2=$ak2;
                if ($a_users[$i]==$minu) {$t1="<font color=red><B>";$t2="</font></B>";}
                if ($a_users[$i]==$maxu) {$t1="<font color=blue><B>";$t2="</font></B>";}
                $html.="\t<td align=\"right\">".$t1.$a_users[$i].$t2."</td>\n";

                if ($cb_hits=="on") $DATA[0][]=intval($a_hits[$i]);
                if ($cb_users=="on") $DATA[1][]=intval($a_users[$i]);
                if ($cb_hosts=="on") $DATA[2][]=intval($a_hosts[$i]);
                $DATA["x"][]=str_pad($i,2,"0",STR_PAD_LEFT);

                if ($hits!="-") $thi+=$a_hits[$i];
                if ($hosts!="-") $tho+=$a_hosts[$i];
                if ($users!="-") $tus+=$a_users[$i];
                $html.="</tr>\n";
                } /* of for */
        $html.="<tr class=\"tbl2\"><td align=\"center\" colspan=\"2\"><b>".$LANG["total"]."</b></td><td align=\"right\" width=\"100\"><b>".$thi."</b></td><td align=right width=100><b>$tho</b></td><td align=right width=100><b>$tus</b></td></tr>";
        $html.="</table></center>";
        } /* of if ($type==0) */
if ($type==1) { /* По дням ############################################## */
        $html.="<center><br><table width=".$TW." cellspacing=1 cellpadding=3 bgcolor='#D4F3D7'>";
        $html.="<tr class=tbl2><td align=center><b>".$LANG["date"]."</b></td><td align=center width=100><B>".$LANG["hits"]."</b></td><td align=center width=100><b>".$LANG["hosts"]."</b></td><td align=center width=100><b>".$LANG["visitors"]."</b></td></tr>";
		if(empty($filter)){
			$sdate=date("Y-m-d H:i:s",mktime(0,0,0,date("m"),date("d")-$limit,date("Y"))+$COUNTER["timeoffset"]);
			if($tdate>$sdate) {$limit=mysql_num_rows(cnstats_sql_query("SELECT * FROM cns_counter_total"));	$sdate=$tdate;}
			$edate=date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d")-1,date("Y"))+$COUNTER["timeoffset"]);
	        $r=cnstats_sql_query("select LEFT(date,10),hits,hosts,users from cns_counter_total ORDER BY date desc  LIMIT $limit;");
		}else{	
			$sd=mysql_result(cnstats_sql_query("SELECT UNIX_TIMESTAMP(min(date)) FROM cns_log;"),0,0);
			$sdate=date("Y-m-d H:i:s",mktime(0,0,0,date("m",$sd),date("d",$sd),date("Y",$sd)));
			$edate=date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d")-1,date("Y"))+$COUNTER["timeoffset"]);
			$r=cnstats_sql_query("select LEFT(date,10), count(page) AS hits, count(DISTINCT(concat(ip,concat(':',proxy)))) AS hosts , count(DISTINCT(uid)) AS users FROM cns_log WHERE date>='$sdate' AND date<='$edate' ".$sqlflt." GROUP BY LEFT(date,10) ORDER BY date desc");
			$limit=$COUNTER["savelog"];
		}
       	$thi=$tho=$tus=0;
        for ($i=0;$i<mysql_num_rows($r);$i++) {
                $date=mysql_result($r,$i,0);
                $date=substr($date,0,4).substr($date,5,2).substr($date,8,2);
                $a_hits[$date]=mysql_result($r,$i,1);
                $a_hosts[$date]=mysql_result($r,$i,2);
                $a_users[$date]=mysql_result($r,$i,3);

                if ($mini>$a_hits[$date]) $mini=$a_hits[$date];
                if ($maxi<$a_hits[$date]) $maxi=$a_hits[$date];

                if ($minh>$a_hosts[$date]) $minh=$a_hosts[$date];
                if ($maxh<$a_hosts[$date]) $maxh=$a_hosts[$date];

                if ($minu>$a_users[$date]) $minu=$a_users[$date];
                if ($maxu<$a_users[$date]) $maxu=$a_users[$date];
		} /* of for */
        for ($i=0;$i<$limit;$i++) {
                $time=time()-(86400*($i+1));
                $date=date("Ymd",$time);
                $pdate=date("Y-m-d",$time);
				$iw=date("w",$time);
				if($iw==0||$iw==6){$ak1="<font color=gray>";$ak2="</font>";} else $ak1=$ak2="";
                if ($class!="tbl1") $class="tbl1"; else $class="tbl2";
                $html.="<tr class=$class>\n";
                $html.="\t<td align=left>".$ak1.date($CONFIG["date_format"],strtotime($pdate)).", ".$LANG["week_".$iw].$ak2."</td>\n";

                $hits=$a_hits[$date];
                $hosts=$a_hosts[$date];
                $users=$a_users[$date];

                if (empty($hits)) $hits="-"; else $thi+=$hits;
                if (empty($hosts)) $hosts="-"; else $tho+=$hosts;
                if (empty($users)) $users="-"; else $tus+=$users;

                $t1=$ak1;$t2=$ak2;
                if ($hits==$mini) {$t1="<font color=red><B>";$t2="</font></B>";}
                if ($hits==$maxi) {$t1="<font color=blue><B>";$t2="</font></B>";}
                $html.="\t<td align=right>".$t1.$hits.$t2."</td>\n";

                $t1=$ak1;$t2=$ak2;
                if ($hosts==$minh) {$t1="<font color=red><B>";$t2="</font></B>";}
                if ($hosts==$maxh) {$t1="<font color=blue><B>";$t2="</font></B>";}
                $html.="\t<td align=right>".$t1.$hosts.$t2."</td>\n";

                $t1=$ak1;$t2=$ak2;
                if ($users==$minu) {$t1="<font color=red><B>";$t2="</font></B>";}
                if ($users==$maxu) {$t1="<font color=blue><B>";$t2="</font></B>";}
                $html.="\t<td align=right>".$t1.$users.$t2."</td>\n";

                if ($cb_hits=="on") $DATA[0][]=intval($hits);
                if ($cb_users=="on") $DATA[1][]=intval($users);
                if ($cb_hosts=="on") $DATA[2][]=intval($hosts);
                $DATA["x"][]=date($CONFIG["shortdm_format"],$time);

                $html.="</tr>\n";
                } /* of for */
        $html.="<tr class=tbl2><td align=center><b>".$LANG["total"]."</b></td><td align=right width=100><b>$thi</b></td><td align=right width=100><b>$tho</b></td><td align=right width=100><b>$tus</b></td></tr>";
        $html.="</table></center>";
        } /* of if ($type==1) */
if ($type==2) { /* По неделям ########################################### */
        $html.="<center><br><table width=".$TW." cellspacing=1 cellpadding=3 bgcolor='#D4F3D7'>";
        $html.="<tr class=tbl2><td align=center><b>".$LANG["date"]."</b></td><td align=center width=100><b>".$LANG["hits"]."</b></td><td align=center width=100><b>".$LANG["hosts"]."</b></td><td align=center width=100><b>".$LANG["visitors"]."</b></td></tr>";
		$ed=mktime(23,59,59,date("m"),date("d")-date("w"),date("Y"))+$COUNTER["timeoffset"];
		if(empty($filter)){
			$sdate=date("Y-m-d H:i:s",mktime(0,0,0,date("m"),date("d")-7*$limit,date("Y"))+$COUNTER["timeoffset"]);
			if($tdate>$sdate) {
				$id=strtotime($tdate); $iw=date("w",$id); if ($iw==0) $iw=7;
				$maxl=mysql_num_rows(cnstats_sql_query("SELECT * FROM cns_counter_total"));
				$sdate=date("Y-m-d H:i:s",mktime(0,0,0,date("m",$id),date("d",$id)-$iw+1,date("Y",$id)));
			}
			$edate=date("Y-m-d H:i:s",$ed);
	        $r=cnstats_sql_query("select LEFT(date,10),hits,hosts,users from cns_counter_total WHERE date>='$sdate' AND date<='$edate' ORDER BY date desc LIMIT ".(7*$limit).";");
		}else{	
			$sd=mysql_result(cnstats_sql_query("SELECT UNIX_TIMESTAMP(min(date)) FROM cns_log;"),0,0);
			$iw=date("w",$sd);if ($iw==0) $iw=7;
			$sdate=date("Y-m-d H:i:s",mktime(0,0,0,date("m",$sd),date("d",$sd)-$iw+1,date("Y",$sd)));
			$edate=date("Y-m-d H:i:s",$ed);
			$r=cnstats_sql_query("select LEFT(date,10), count(page) AS hits, count(DISTINCT(concat(ip,concat(':',proxy)))) AS hosts , count(DISTINCT(uid)) AS users FROM cns_log WHERE date>='$sdate' AND date<='$edate' ".$sqlflt." GROUP BY LEFT(date,10) ORDER BY date desc");
			$maxl=$COUNTER["savelog"];
		}
		$limit=0;
        for ($i=1;$i<=$maxl;$i++) {
			$time=mktime(0,0,0,date("m"),date("d")-$i,date("Y"));
			$iw=date("w",$time); if ($iw==0) $limit=$limit+1;
		}
        $thi=$tho=$tus=0;
        for ($i=0;$i<mysql_num_rows($r);$i++) {
                $date=mysql_result($r,$i,0);
                $date=substr($date,0,4).substr($date,5,2).substr($date,8,2);
                $a_hits[$date]=mysql_result($r,$i,1);
                $a_hosts[$date]=mysql_result($r,$i,2);
                $a_users[$date]=mysql_result($r,$i,3);
                } /* of for */
        $w=0;
        $day_of_w=1;
        $w_hits=Array();
        $w_hosts=Array();
        $w_users=Array();
        for ($i=0;$i<(7*$limit);$i++) {
                $time=$ed-86400*$i;
                $date=date("Ymd",$time);
                $w_hits[$w]=$w_hits[$w]+$a_hits[$date];
                $w_hosts[$w]=$w_hosts[$w]+$a_hosts[$date];
                $w_users[$w]=$w_users[$w]+$a_users[$date];
                $day_of_w++;
                if ($day_of_w>7) {$day_of_w=1;$w++;}
                } /* of for */

        		for ($i=0;$i<$limit;$i++) {
                if ($mini>$w_hits[$i] && $w_hits[$i]!=0) $mini=$w_hits[$i];
                if ($maxi<$w_hits[$i]) $maxi=$w_hits[$i];

                if ($minh>$w_hosts[$i] && $w_hosts[$i]!=0) $minh=$w_hosts[$i];
                if ($maxh<$w_hosts[$i]) $maxh=$w_hosts[$i];

                if ($minu>$w_users[$i] && $w_users[$i]!=0) $minu=$w_users[$i];
                if ($maxu<$w_users[$i]) $maxu=$w_users[$i];
                }

        for ($i=0;$i<$limit;$i++) {
                if ($class!="tbl1") $class="tbl1"; else $class="tbl2";
                $html.="<tr class=\"".$class."\">\n";
                $date1=date($CONFIG["date_format"],$ed-((7*$i+6)*86400));
                $date2=date($CONFIG["date_format"],$ed-((7*$i)*86400));
                $html.="\t<td align=\"center\">".$date1." - ".$date2."</td>\n";

                if ($cb_hits=="on") $DATA[0][]=intval($w_hits[$i]);
                if ($cb_users=="on") $DATA[1][]=intval($w_users[$i]);
                if ($cb_hosts=="on") $DATA[2][]=intval($w_hosts[$i]);
                $DATA["x"][]=date($CONFIG["shortdm_format"],$ed-((7*$i+6)*86400));

                $hits=$w_hits[$i];
                $hosts=$w_hosts[$i];
                $users=$w_users[$i];

                if (empty($hits)) $hits="-"; else $thi+=$hits;
                if (empty($hosts)) $hosts="-"; else $tho+=$hosts;
                if (empty($users)) $users="-"; else $tus+=$users;

                $t1=$t2="";
                if ($hits==$mini) {$t1="<font color=red><B>";$t2="</font></B>";}
                if ($hits==$maxi) {$t1="<font color=blue><B>";$t2="</font></B>";}
                $html.="\t<td align=right>".$t1.$hits.$t2."</td>\n";

                $t1=$t2="";
                if ($hosts==$minh) {$t1="<font color=red><B>";$t2="</font></B>";}
                if ($hosts==$maxh) {$t1="<font color=blue><B>";$t2="</font></B>";}
                $html.="\t<td align=right>".$t1.$hosts.$t2."</td>\n";

                $t1=$t2="";
                if ($users==$minu) {$t1="<font color=red><B>";$t2="</font></B>";}
                if ($users==$maxu) {$t1="<font color=blue><B>";$t2="</font></B>";}
                $html.="\t<td align=right>".$t1.$users.$t2."</td>\n";

                $html.="</tr>\n";
                } /* of for */
        $html.="<tr class=tbl2><td align=center><b>".$LANG["total"]."</b></td><td align=right width=100><b>$thi</b></td><td align=right width=100><b>$tho</b></td><td align=right width=100><b>$tus</b></td></tr>";
        $html.="</table></center>";
        } /* of if ($type==2) */
if ($type==3) { /* По месяцам ########################################### */
		if(empty($filter)){
			$sdate=date("Y-m-d H:i:s",mktime(0,0,0,date("m")-$limit,1,date("Y"))+$COUNTER["timeoffset"]);
			if($tdate>$sdate) { 
				$sdate=date("Y-m-d H:i:s",mktime(0,0,0,date("m")-$limit,1,date("Y"))+$COUNTER["timeoffset"]);
				$limit=mysql_result(cnstats_sql_query("SELECT count(DISTINCT(LEFT(date,7))) FROM cns_counter_total;"),0,0)-1;
			}
			$edate=date("Y-m-d H:i:s",mktime(23,59,59,date("m"),0,date("Y"))+$COUNTER["timeoffset"]);
	        $r=cnstats_sql_query("select LEFT(date,7),sum(hits),sum(hosts),sum(users) from cns_counter_total WHERE date<='".$edate."' GROUP BY LEFT(date,7) ORDER BY date desc LIMIT $limit;");
		}else{	
			$sd=mysql_result(cnstats_sql_query("SELECT UNIX_TIMESTAMP(min(date)) FROM cns_log;"),0,0);
			$sdate=date("Y-m-d H:i:s",mktime(0,0,0,date("m",$sd),1,date("Y",$sd)));
			$edate=date("Y-m-d H:i:s",mktime(23,59,59,date("m"),0,date("Y"))+$COUNTER["timeoffset"]);
			$r=cnstats_sql_query("select LEFT(date,7), count(page) AS hits, count(DISTINCT(concat(ip,concat(':',proxy)))) AS hosts , count(DISTINCT(uid)) AS users FROM cns_log WHERE date>='$sdate' AND date<='$edate' ".$sqlflt." GROUP BY LEFT(date,7) ORDER BY date desc");
			$limit=1;
		}

        $html.="<center><br><table width=".$TW." cellspacing=1 cellpadding=3 bgcolor='#D4F3D7'>";
        $html.="<tr class=tbl2><td align=center><b>".$LANG["date"]."</b></td><td align=center width=100><b>".$LANG["hits"]."</b></td><td align=center width=100><b>".$LANG["hosts"]."</b></td><td align=center width=100><b>".$LANG["visitors"]."</b></td></tr>";
        $thi=$tho=$tus=0;
        for ($i=0;$i<mysql_num_rows($r);$i++) {
                $date=mysql_result($r,$i,0);
                $date=substr($date,0,4).substr($date,5,2);
                $a_hits[$date]=mysql_result($r,$i,1);
                $a_hosts[$date]=mysql_result($r,$i,2);
                $a_users[$date]=mysql_result($r,$i,3);

                if ($mini>$a_hits[$date]) $mini=$a_hits[$date];
                if ($maxi<$a_hits[$date]) $maxi=$a_hits[$date];
                if ($minh>$a_hosts[$date]) $minh=$a_hosts[$date];
                if ($maxh<$a_hosts[$date]) $maxh=$a_hosts[$date];
                if ($minu>$a_users[$date]) $minu=$a_users[$date];
                if ($maxu<$a_users[$date]) $maxu=$a_users[$date];
                } /* of for */

        for ($i=0;$i<$limit;$i++) {
                $date=date( "Ym", mktime(0,0,0,date("m")-$i,0,date("Y")));
                $pdate=date( $CONFIG["shortdate_format"], mktime(0,0,0,date("m")-$i,0,date("Y")));
                if ($class!="tbl1") $class="tbl1"; else $class="tbl2";
                $html.="<tr class=".$class.">\n";
                $html.="\t<td align=center>".$pdate."</td>\n";

                $hits=$a_hits[$date];
                $hosts=$a_hosts[$date];
                $users=$a_users[$date];

                if ($cb_hits=="on") $DATA[0][]=intval($hits);
                if ($cb_users=="on") $DATA[1][]=intval($users);
                if ($cb_hosts=="on") $DATA[2][]=intval($hosts);
                $DATA["x"][]=$pdate;

                if (empty($hits)) $hits="-"; else $thi+=$hits;
                if (empty($hosts)) $hosts="-"; else $tho+=$hosts;
                if (empty($users)) $users="-"; else $tus+=$users;

                $t1=$t2="";
                if ($hits==$mini) {$t1="<font color=red><B>";$t2="</font></B>";}
                if ($hits==$maxi) {$t1="<font color=blue><B>";$t2="</font></B>";}
                $html.="\t<td align=right>".$t1.$hits.$t2."</td>\n";

                $t1=$t2="";
                if ($hosts==$minh) {$t1="<font color=red><B>";$t2="</font></B>";}
                if ($hosts==$maxh) {$t1="<font color=blue><B>";$t2="</font></B>";}
                $html.="\t<td align=right>".$t1.$hosts.$t2."</td>\n";

                $t1=$t2="";
                if ($users==$minu) {$t1="<font color=red><B>";$t2="</font></B>";}
                if ($users==$maxu) {$t1="<font color=blue><B>";$t2="</font></B>";}
                $html.="\t<td align=right>".$t1.$users.$t2."</td>\n";

                $html.="</tr>\n";
                } /* of for */
        $html.="<tr class=tbl2><td align=center><b>".$LANG["total"]."</b></td><td align=right width=100><b>$thi</b></td><td align=right width=100><b>$tho</b></td><td align=right width=100><b>$tus</b></td></tr>";
        $html.="</table></center>";
        } /* of if ($type==3) */
if ($type==4) { /* По дням недели ########################################### */
        $html.="<center><br><table width=".$TW." cellspacing=1 cellpadding=3 bgcolor='#D4F3D7'>";
        $html.="<tr class=tbl2><td align=center><b>".$LANG["date"]."</b></td><td align=center width=100><b>".$LANG["hits"]."</b></td><td align=center width=100><b>".$LANG["hosts"]."</b></td><td align=center width=100><b>".$LANG["visitors"]."</b></td></tr>";
		$ed=mktime(23,59,59,date("m"),date("d")-date("w"),date("Y"))+$COUNTER["timeoffset"];
		if(empty($filter)){
			$sdate=date("Y-m-d H:i:s",mktime(0,0,0,date("m"),date("d")-7*$limit,date("Y"))+$COUNTER["timeoffset"]);
			if($tdate>$sdate) {
				$id=strtotime($tdate); $iw=date("w",$id); if ($iw==0) $iw=7;
				$sdate=date("Y-m-d H:i:s",mktime(0,0,0,date("m",$id),date("d",$id)-$iw+1,date("Y",$id)));
			}
			$edate=date("Y-m-d H:i:s",$ed);
	        $r=cnstats_sql_query("select LEFT(date,10),hits,hosts,users from cns_counter_total WHERE date>='$sdate' AND date<='$edate' ORDER BY date desc LIMIT ".(7*$limit).";");
		}else{	
			$sd=mysql_result(cnstats_sql_query("SELECT UNIX_TIMESTAMP(min(date)) FROM cns_log;"),0,0);
			$iw=date("w",$sd);if ($iw==0) $iw=7;
			$sdate=date("Y-m-d H:i:s",mktime(0,0,0,date("m",$sd),date("d",$sd)-$iw+1,date("Y",$sd)));
			$edate=date("Y-m-d H:i:s",$ed);
			$r=cnstats_sql_query("select LEFT(date,10), count(page) AS hits, count(DISTINCT(concat(ip,concat(':',proxy)))) AS hosts , count(DISTINCT(uid)) AS users FROM cns_log WHERE date>='$sdate' AND date<='$edate' ".$sqlflt." GROUP BY LEFT(date,10) ORDER BY date desc");
		}
        $thi=$tho=$tus=0;
		$W["0"]="Sun";$W["1"]="Mon";$W["2"]="Tue";$W["3"]="Wed";$W["4"]="Thu";$W["5"]="Fri";$W["6"]="Sat";
        for ($i=0;$i<mysql_num_rows($r);$i++) {
                $date=mysql_result($r,$i,0);
				$iw=date("w",strtotime($date)); if ($iw==0) $iw=7;
                $aw_hits[$iw]=$aw_hits[$iw]+mysql_result($r,$i,1);
                $aw_hosts[$iw]=$aw_hosts[$iw]+mysql_result($r,$i,2);
                $aw_users[$iw]=$aw_users[$iw]+mysql_result($r,$i,3);
                } /* of for */

        		for ($i=1;$i<=7;$i++) {
                if ($mini>$aw_hits[$i] && $aw_hits[$i]!=0) $mini=$aw_hits[$i];
                if ($maxi<$aw_hits[$i]) $maxi=$aw_hits[$i];

                if ($minh>$aw_hosts[$i] && $aw_hosts[$i]!=0) $minh=$aw_hosts[$i];
                if ($maxh<$aw_hosts[$i]) $maxh=$aw_hosts[$i];

                if ($minu>$aw_users[$i] && $aw_users[$i]!=0) $minu=$aw_users[$i];
                if ($maxu<$aw_users[$i]) $maxu=$aw_users[$i];
                }

        for ($i=1;$i<=7;$i++) {
				if($i==6||$i==7){$ak1="<font color=gray>";$ak2="</font>";} else $ak1=$ak2="";
                if ($class!="tbl1") $class="tbl1"; else $class="tbl2";
				$iw=$i; if ($i==7) $iw=0;
                $html.="<tr class=$class>\n";
                $html.="\t<td align=left>".$ak1.$LANG["week_".$iw].$ak2."</td>\n";

                if ($cb_hits=="on") $DATA[0][]=intval($aw_hits[$i]);
                if ($cb_users=="on") $DATA[1][]=intval($aw_users[$i]);
                if ($cb_hosts=="on") $DATA[2][]=intval($aw_hosts[$i]);
                $DATA["x"][]=$W[$iw];

                $hits=$aw_hits[$i];
                $hosts=$aw_hosts[$i];
                $users=$aw_users[$i];

                if (empty($hits)) $hits="-"; else $thi+=$hits;
                if (empty($hosts)) $hosts="-"; else $tho+=$hosts;
                if (empty($users)) $users="-"; else $tus+=$users;

                $t1=$ak1;$t2=$ak2;
                if ($hits==$mini) {$t1="<font color=red><B>";$t2="</font></B>";}
                if ($hits==$maxi) {$t1="<font color=blue><B>";$t2="</font></B>";}
                $html.="\t<td align=right>".$t1.$hits.$t2."</td>\n";

                $t1=$ak1;$t2=$ak2;
                if ($hosts==$minh) {$t1="<font color=red><B>";$t2="</font></B>";}
                if ($hosts==$maxh) {$t1="<font color=blue><B>";$t2="</font></B>";}
                $html.="\t<td align=right>".$t1.$hosts.$t2."</td>\n";

                $t1=$ak1;$t2=$ak2;
                if ($users==$minu) {$t1="<font color=red><B>";$t2="</font></B>";}
                if ($users==$maxu) {$t1="<font color=blue><B>";$t2="</font></B>";}
                $html.="\t<td align=right>".$t1.$users.$t2."</td>\n";

                $html.="</tr>\n";
                } /* of for */
        $html.="<tr class=tbl2><td align=center><b>".$LANG["total"]."</b></td><td align=right width=100><b>$thi</b></td><td align=right width=100><b>$tho</b></td><td align=right width=100><b>$tus</b></td></tr>";
        $html.="</table></center>";
        } /* of if ($type==4) */
print $TABLE."<tr class=tbl2><td>";
if (isset($LANG["reports"][$st])) print $LANG["reports"][$st];
else for ($j=0;$j<count($MENU);$j+=3) 
if ($st==$MENU[$j+1]) print $MENU[$j+2]."\n";

if(!empty($filter)) print $LANG["build_by_log"]; else print $LANG["build_by_common"];
if(!empty($filter))print"<br><font style='color:gray;font-size:10px;'>".$LANG["filter"].": ".$_GET["filter"];
print"<br><font style='color:gray;font-size:10px;'>".$LANG["report for period from"]." ".date($CONFIG["datetime_format"],strtotime($sdate))." ".$LANG["till"]." ".date($CONFIG["datetime_format"],strtotime($edate));
print"</font></td></tr></table>";
print"<img alt='' src=img/none.gif width=1 height=3><br>";

if ($type!=4){
$DATA[0]=array_reverse($DATA[0]);
$DATA[1]=array_reverse($DATA[1]);
$DATA[2]=array_reverse($DATA[2]);
$DATA["x"]=array_reverse($DATA["x"]);
}
$HTTP_SESSION_VARS["DATA"]=$DATA;

// Если выбран график в ручную, то игнорируем настройки
if (isset($_GET["graph"])) $CONFIG["diagram"]=intval($_GET["graph"]);
else $graph=intval($CONFIG["diagram"]);

// Определяем тип графика
$GDVERSION=gdVersion();

// Если GD 2.0, но анти-алиасинг отключен, то делаем вид,
// что GD 1.0 и тогда графики сглаживаться не будут
if ($GDVERSION==2 && $CONFIG["antialias"]==0) $GDVERSION=1;

// Если нет GD, то в любом случае включаем HTML график
if ($GDVERSION==0) $CONFIG["diagram"]=0;

if ($CONFIG["diagram"]>0 && $CONFIG["diagram"]<4) {
        switch ($CONFIG["diagram"]) {
                case  2: $g="lines"; break;
                case  3: $g="bar"; break;
                default: $g="3d";
                }
        $img_smooth="s=".($s=="on"?1:0);
        $img_antialias="antialias=".($GDVERSION==1?0:1);
        print "<img src=\"graph/".$g.".php?".$img_smooth."&".$img_antialias."&rnd=".time()."\" width=\"".$IMGW."\" height=\"".$IMGH."\"><br>\n";
        print "<img src=\"img/none.gif\" width=\"1\" height=\"5\">";
        }
else include "graph/html.php";
?>

<br>
<script language="JavaScript" type="text/javascript">
<!--
function redraw(i) {
        var ge=document.getElementById('ge');ge.value=i;
        var gf=document.getElementById('gf');gf.submit();
        }
//-->
</script>

<center>
<form action="index.php" method="get" class="m0" id="gf">
<table width="<?=$TW;?>" cellspacing="1" cellpadding="0" bgcolor="#D4F3D7"><tr class="tbl2"><td>
<table cellspacing="0" cellpadding="2" border="0" width="100%">
<tr>
<?php
if ($GDVERSION>0) {
        if ($graph==1) print "<td><img src=\"img/graph_1_s.gif\" hspace=\"6\" width=\"18\" height=\"18\" border=\"0\"></td>";
        else print "<td><a href=\"javascript:redraw(1);\"><img src=\"img/graph_1.gif\" hspace=\"6\" width=\"18\" height=\"18\" border=\"0\"></a></td>";
        }
?>
        <td>
        <table cellspacing="0" cellpadding="0" border="0"><tr><td><input type="checkbox" name="cb_hits" <?=($cb_hits=="on"?"checked":"");?>></td><td style="color:red;"><B><?=$LANG["hits"];?></B></td></tr></table>
        </td>
        <td>
        <table cellspacing="0" cellpadding="0" border="0"><tr><td><input type="checkbox" name="s" <?=($s=="on"?"checked":"");?>></td><td><?=$LANG["smooth graphics"];?></td></tr></table>
        </td>
</tr>
<tr>
<?php
if ($GDVERSION>0) {
        if ($graph==2) print "<td><img src=\"img/graph_2_s.gif\" hspace=\"6\" width=\"18\" height=\"18\" border=\"0\"></td>";
        else print "<td><a href=\"javascript:redraw(2);\"><img src=\"img/graph_2.gif\" hspace=\"6\" width=\"18\" height=\"18\" border=\"0\"></a></td>";
        }
?>
        <td>
        <table cellspacing="0" cellpadding="0" border="0"><tr><td><input type="checkbox" name="cb_users" <?=($cb_users=="on"?"checked":"");?>></td><td style="color:green;"><B><?=$LANG["visitors"];?></B></td></tr></table>
        </td>
        <td>&nbsp;</td>
</tr>
<tr>
<?php
if ($GDVERSION>0) {
        if ($graph==3) print "<td><img src=\"img/graph_3_s.gif\" hspace=\"6\" width=\"18\" height=\"18\" border=\"0\"></td>";
        else print "<td><a href=\"javascript:redraw(3);\"><img src=\"img/graph_3.gif\" hspace=\"6\" width=\"18\" height=\"18\" border=\"0\"></a></td>";
        }
?>
        <td>
        <table cellspacing="0" cellpadding="0" border="0"><tr><td><input type="checkbox" name="cb_hosts" <?=($cb_hosts=="on"?"checked":"");?>></td><td style="color:blue;"><B><?=$LANG["hosts"];?></B></td></tr></table>
        </td>
        <td align="right">
        <table width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td>&nbsp;
                <select name="type">
                <option value="0" <?=($type==0?"selected":"");?>><?=$LANG["by hours"];?>
                <option value="1" <?=($type==1?"selected":"");?>><?=$LANG["by days"];?>
                <option value="4" <?=($type==4?"selected":"");?>><?=$LANG["by weekdays"];?>
                <option value="2" <?=($type==2?"selected":"");?>><?=$LANG["by weeks"];?>
                <option value="3" <?=($type==3?"selected":"");?>><?=$LANG["by moths"];?>
                </select>
        </td><td align="right">
                <input type="submit" value="<?=$LANG["update"];?>">
        </td></tr></table>
        </td>
</tr>
</table>
</td></tr></table>
<input type=hidden name="st" value="<?=$st;?>">
<input type=hidden name="stm" value="<?=$stm;?>">
<input type=hidden name="ftm" value="<?=$ftm;?>">
<input type=hidden name="filter" value="<?=$filter;?>">
<input type=hidden name="day" value="<?=$day;?>">
<input type=hidden name="month" value="<?=$month;?>">
<input type=hidden name="year" value="<?=$year;?>">
<input type=hidden name="graph" value="<?=$graph;?>">
<input type=hidden name="prom" value="<?=$prom;?>">
<input type=hidden name="second" value="1">
<input type=hidden name="graph" value="<?=$graph;?>" id="ge">
</form>

<?php
print $html;
$NOFILTER=0;
?>