<?
$CNSTATS_DR=dirname(dirname(__FILE__));
if($CNSTATS_DR[strlen($CNSTATS_DR)-1]!="/") $CNSTATS_DR.="/";
if(!isset($STATS_CONF["dbname"]))include$CNSTATS_DR."stat/config.php";

function islocal($ip) {
	if ($ip=="255.255.255.255") return(true);
	if (substr($ip,0,7)=="192.168") return(true);
	if (substr($ip,0,2)=="10") return(true);
	return(false);
}

function cnstats_sql_query($query,$CONN) {
	GLOBAL $LANG,$STATS_CONF,$COUNTER;
	if ($STATS_CONF["sqlserver"]="MySql") {
		$r=@mysql_db_query($STATS_CONF["dbname"],$query,$CONN);
		if (mysql_errno($CONN)!=0) {
			if ($COUNTER["senderrorsbymail"]=="yes" && !empty($STATS_CONF["cnsoftwarelogin"])) {
				mail($STATS_CONF["cnsoftwarelogin"],"CNStats MySql Error",">A fatal MySQL error occured\n\n".mysql_error()."\nQuery:\n------------\n".$query."\n-----------\nURL: http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."\nDate: ".date($LANG["datetime_format"]));
			}
			die("<font color=red><B>A fatal MySQL error occured:</B></font><br><br>\n\n".mysql_error($CONN)."<br><br>\n\n ".$query);
		}
	}
	return($r);
}

function nmail($CONN) {
	GLOBAL $COUNTER;
	$CONFIG=mysql_fetch_array(cnstats_sql_query("SELECT language FROM cns_config",$CONN));
	include "lang/lang_".$CONFIG["language"].".php";
	$MAIL=mysql_fetch_array(cnstats_sql_query("SELECT mail_day, mail_email, mail_subject, mail_content FROM cns_config",$CONN));
	if (!empty($MAIL["mail_email"])) {
		$need=0;$per=1;
		if ($MAIL["mail_day"]==0) {$need=1;$pper=$LANG["yesterday"];}
		else {
		$wd=date("w",time()+$COUNTER["timeoffset"]);
		if($wd==0) $wd=7;
		if($MAIL["mail_day"]==$wd){$need=1;$per=7;$pper=$LANG["last7dayes"];}}
		$t1=date("Y-m-d H:i:s",mktime(0,0,0,date("m"),date("d")-1,date("Y"))+$COUNTER["timeoffset"]);
		$t2=date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d")-1,date("Y"))+$COUNTER["timeoffset"]);
		$style="style=\"font-family: verdana;font-size: 11px;color:#333333\"";
		$table1="<table cellspacing=0 cellpadding=0 width=\"100%\" border=0>";
		$table2="<table cellspacing=1 cellpadding=2 width=\"100%\" border=0>";
		$bg[1]="#f2fcf4";$bg[2]="#e7f9ea";
		if ($need==1) {
			$mail="";
			$mail.="<html><head><meta http-equiv=content-type content=\"text/html; charset=windows-1251\"></head>\n<body>\n";				
			if (($MAIL["mail_content"]&1)!=0) {
				$mail.=$table1."<tr><td bgcolor=#b8e1bd>\n".$table2."\n<tr ".$style."><td align=left><b>";
				$mail.=$LANG["attendanceper"]." $pper";
				$mail.="</b></td><td align=right><b>CNStats 2.5</b></td></tr>\n<tr ".$style." bgcolor=#f2fcf4><td align=left>".$COUNTER["servername"]."</td><td align=right>";
				$mail.=date("Y-m-d H:i:s",time()+$COUNTER["timeoffset"]);
				$mail.="</td></tr></table>\n</td></tr></table>\n<br>\n";
				$mail.=$table1."<tr><td bgcolor=#b8e1bd>\n".$table2."\n<tr ".$style." align=center><td width=\"25%\"><b>";
				$mail.=$LANG["date"];
				$mail.="</b></td><td width=\"25%\"><b>";
				$mail.=$LANG["sessionss"];
				$mail.="</b></td><td width=\"25%\"><b>";
				$mail.=$LANG["hostss"];
				$mail.="</b></td><td width=\"25%\"><b>";
				$mail.=$LANG["hitss"];
				$mail.="</b></td></tr>\n";
				$r=cnstats_sql_query("select LEFT(date,10) as dt,hits,hosts,users from cns_counter_total ORDER BY date desc LIMIT $per",$CONN);
				while (($a=mysql_fetch_array($r))) {
				$mail.="<tr ".$style." align=center bgcolor=#f2fcf4><td>".$a["dt"]."</td><td>".$a["users"]."</td><td>".$a["hosts"]."</td><td>".$a["hits"]."</td></tr>\n";
				}
				$mail.="</table></td>\n</tr></table>\n<br>\n";
			}
			if (($MAIL["mail_content"]&2)!=0) {
				$mail.=$table1."<tr><td bgcolor=#b8e1bd>\n".$table2."\n<tr ".$style." align=center><td colspan=2><b>";
				$mail.=$LANG["yesterdayreferers"];
				$mail.="</b></td></tr>\n";
				$nreferer="IF(LOCATE('?',referer),LEFT(referer,LOCATE('?',referer)-1),referer)";
				$sql="select ".$nreferer.",count(*) from cns_log WHERE date>'".$t1."' AND date<'".$t2."' AND type1=1 group by 1 order by 2 desc";
				$r=cnstats_sql_query($sql,$CONN);
				$count=mysql_num_rows($r);
				$k=1;
				for ($i=0;$i<=10;$i++) {
					$data=urldecode(mysql_result($r,$i,0));
					$cnt=mysql_result($r,$i,1);
					if (!empty($data)) {
						$mail.="<tr ".$style." align=center bgcolor=".$bg[$k]."><td>";
						$mail.=$cnt;
						$mail.="</td><td align=left>";
						$mail.=$data;
						$mail.="</td></tr>\n";
						if ($k==1) $k=2; else $k=1;
					}
				}
				$mail.="</table></td></tr></table><br>";
			}
			if (($MAIL["mail_content"]&4)!=0) {
				$mail.=$table1."<tr><td bgcolor=#b8e1bd>\n".$table2."\n<tr ".$style." align=center><td colspan=2><b>";
				$mail.=$LANG["Sspages"];
				$mail.="</b></td></tr>\n";
				$npage="IF(LOCATE('%3F',page),LEFT(page,LOCATE('%3F',page)-1),page)";
				$sql="select IF(STRCMP(LEFT(page,13),'http%3A%2F%2F')=0,IF(LOCATE('%2F',page,13),SUBSTRING(".$npage.",LOCATE('%2F',".$npage.",13)),'/'),".$npage."),count(*) from cns_log WHERE date>'".$t1."' AND date<'".$t2."' group by 1 order by 2 desc";
				$r=cnstats_sql_query($sql,$CONN);
				$count=mysql_num_rows($r);
				$k=1;
				for ($i=0;$i<=10;$i++) {
					$data=urldecode(mysql_result($r,$i,0));
					$cnt=mysql_result($r,$i,1);
					if (!empty($data)) {
						$mail.="<tr ".$style." align=center bgcolor=".$bg[$k]."><td>";
						$mail.=$cnt;
						$mail.="</td><td align=left>";
						$mail.=$data;
						$mail.="</td></tr>\n";
						if ($k==1) $k=2; else $k=1;
					}
				}
				$mail.="</table></td></tr></table>\n";
			}
		$mail.="</body></html>";
		$MAIL["mail_subject"]=str_replace("%Y",date("Y",time()+$COUNTER["timeoffset"]),$MAIL["mail_subject"]);
        	$MAIL["mail_subject"]=str_replace("%d",date("d",time()+$COUNTER["timeoffset"]),$MAIL["mail_subject"]);
        	$MAIL["mail_subject"]=str_replace("%m",date("m",time()+$COUNTER["timeoffset"]),$MAIL["mail_subject"]);
        	mail($MAIL["mail_email"],$MAIL["mail_subject"],$mail,"From: \"CNStats\" <".$MAIL["mail_email"].">\nContent-type: text/html; charset=windows-1251","-f".$MAIL["mail_email"]);
		}
	}
}

function midnight_calc() {
	GLOBAL $COUNTER;
	$sdays=intval($COUNTER["savelog"]);if ($sdays<1 || $sdays>30) $sdays=30;
	cnstats_sql_query("DELETE FROM cns_log WHERE date<'".date("Y-m-d H:i:s",mktime(0,0,0,date("m") ,date("d")-$sdays,date("Y"))+$COUNTER["timeoffset"])."'",$COUNTER["CONN"]);
	cnstats_sql_query("OPTIMIZE TABLE cns_log",$COUNTER["CONN"]);
	$r=cnstats_sql_query("SHOW TABLE STATUS",$COUNTER["CONN"]);
	$size=0;
	while ($a=mysql_fetch_array($r,MYSQL_ASSOC)) {
		while (list ($key, $val) = each ($a)) {
			if ($key=="Data_length" && (substr($tname,0,4)=="cns_")) $size+=$val;
			if ($key=="Index_length" && (substr($tname,0,4)=="cns_")) $size+=$val;
			if ($key=="Name") $tname=$val;
		}
	}
	cnstats_sql_query("INSERT INTO cns_size SET date=NOW(), size='".$size."';",$COUNTER["CONN"]);
	nmail($COUNTER["CONN"]);
	mysql_close($COUNTER["CONN"]);
}

function stats_hit($sqlhost,$sqluser,$sqlpassword,$db_name) {
	GLOBAL $_SERVER,$HTTP_COOKIE_VARS,$STATS_CONF,$COUNTER,$_GET,$CNSTATS_DR;
	$noclose=false;
	if (!is_array($COUNTER["excludeip"])) {
		$tmp1=$COUNTER["excludeip"];
		$tmp2=$COUNTER["excludemask"];
		$COUNTER["excludemask"]=$COUNTER["excludeip"]=Array();
		$COUNTER["excludeip"][]=$tmp1;
		$COUNTER["excludemask"][]=$tmp2;
	}
	$exclude=false;
	while (list ($key, $val) = each ($COUNTER["excludeip"])) {
		$eip=ip2long($val);
		$emask=ip2long($COUNTER["excludemask"][$key]);
		if ((ip2long($_SERVER["REMOTE_ADDR"])&$emask)==($eip&$emask)) {$exclude=true;break;}
	}
	if ($exclude) {
		$CONN=mysql_connect($STATS_CONF["sqlhost"],$STATS_CONF["sqluser"],$STATS_CONF["sqlpassword"],TRUE);
		mysql_select_db($STATS_CONF["dbname"],$CONN);
		$r=mysql_query("SELECT t_hits,hits,hosts FROM cns_counter") or die(mysql_error());
		$STATS_CONF=mysql_fetch_array($r,MYSQL_ASSOC);
		mysql_close($CONN);
		return;
	}
	// Connecting to DB
	if (version_compare(phpversion(), "4.2.0", ">="))
		$CONN=@mysql_connect($STATS_CONF["sqlhost"],$STATS_CONF["sqluser"],$STATS_CONF["sqlpassword"],TRUE);
	else
		$CONN=@mysql_connect($STATS_CONF["sqlhost"],$STATS_CONF["sqluser"],$STATS_CONF["sqlpassword"]);
	if (!$CONN) return;
	if (!@mysql_select_db($STATS_CONF["dbname"],$CONN)) return;

	$r=cnstats_sql_query("UPDATE cns_counter SET last='".date("d",time()+$COUNTER["timeoffset"]-$COUNTER["mnoffset"])."';",$CONN);
	if (mysql_affected_rows()==1) {
		ignore_user_abort(1);
		@set_time_limit(0);
		$date=date("Y-m-d H:i:s",mktime(0,0,0,date("m")  ,date("d")-1,date("Y"))+$COUNTER["timeoffset"]);
		$r=cnstats_sql_query("SELECT hits,hosts,users FROM cns_counter",$CONN);
		cnstats_sql_query("UPDATE cns_counter SET hits=0, hosts=0, users=0;",$CONN);
		for ($i=0;$i<mysql_num_rows($r);$i++) {
			$hits=mysql_result($r,$i,0);
			$hosts=mysql_result($r,$i,1);
			$users=mysql_result($r,$i,2);
			cnstats_sql_query("INSERT INTO cns_counter_total set hits='".$hits."',hosts='".$hosts."',date='".$date."', users='".$users."';",$CONN);
		}
		cnstats_sql_query("DELETE FROM cns_today",$CONN);
		cnstats_sql_query("DELETE FROM cns_today_proxy",$CONN);
		$COUNTER["CONN"]=$CONN;
		$noclose=true;
		register_shutdown_function("midnight_calc");
	}
	$agent=mysql_escape_string(htmlspecialchars($_SERVER["HTTP_USER_AGENT"]));
	$ip=$_SERVER["REMOTE_ADDR"];
	$proxy="";
	if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		$proxy=$ip;
		$ip=$_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	$zpos=strrpos($ip,",");
	if ($zpos!=0) $ip=trim(substr($ip,$zpos+1));
	$ip=ip2long($ip);
	$zpos=strrpos($proxy,",");
	if ($zpos!=0) $proxy=trim(substr($proxy,$zpos+1));
	$proxy=ip2long($proxy);

	$c=intval($c);
	$depth=intval($d);
	if ($STATS_CONF["graph"]==1) {
		$page=$STATS_CONF["page"];
		$referer=$STATS_CONF["referer"];
	}else{
		if (isset($STATS_CONF["page"])) $page=urlencode(htmlspecialchars($STATS_CONF["page"]));
		else $page=urlencode(htmlspecialchars("http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]));
		if (isset($STATS_CONF["referer"])) $referer=htmlspecialchars($STATS_CONF["referer"]);
		else $referer=htmlspecialchars($_SERVER["HTTP_REFERER"]);
	}
	$res=htmlspecialchars($rs);
	$date=date("Y-m-d H:i:s",time()+$COUNTER["timeoffset"]);
	$language=htmlspecialchars($_SERVER["HTTP_ACCEPT_LANGUAGE"]);

	$flag=0;
	$r=cnstats_sql_query("SELECT count(*) FROM cns_today WHERE ip='$ip'",$CONN);
	if ($r) $is=mysql_result($r,0,0);
	else $is=0;
	if ($is==0) {
		cnstats_sql_query("INSERT INTO cns_today (ip) VALUES ('$ip')",$CONN);
		$flag=1;
	}

	if(long2ip($proxy)!="255.255.255.255") {
		$r=cnstats_sql_query("SELECT count(*) FROM cns_today_proxy WHERE ip='$proxy'",$CONN);
		if ($r) $is=mysql_result($r,0,0);
		else $is=0;
		if ($is==0) {
			cnstats_sql_query("INSERT INTO cns_today_proxy (ip) VALUES ('$proxy')",$CONN);
			$flag=1;
		}
	}
	// Getting user ID
	if ($STATS_CONF["is_cookie"]==1) {
		$uid=intval($HTTP_COOKIE_VARS["counter"]);
		if ($uid!=0) $type1=0;
		else{
			$type1=1;
			if (substr($page,0,13)=="http%3A%2F%2F") $up=substr($page,13);
			if (strpos($up,"%2F")) $up=substr($up,0,strpos($up,"%2F"));
			if (substr($referer,0,7)=="http://") $ur=substr($referer,7);
			if (strpos($ur,"/")) $ur=substr($ur,0,strpos($ur,"/"));
			if (($up == $ur)&&($flag!=1)) {
				$type1=0; $r=cnstats_sql_query("select max(uid) from cns_log WHERE ip='$ip' AND proxy='$proxy'",$CONN);
				$uid=mysql_result($r,0,0);
			}else{
				if (($referer=="")&&($flag!=1)) {
					$time=mysql_result(cnstats_sql_query("select UNIX_TIMESTAMP(max(date)) from cns_log WHERE ip='$ip' AND proxy='$proxy'",$CONN),0,0);
					if($time-$COUNTER["timeoffset"]+3600<time()){
						$r=cnstats_sql_query("select max(uid) from cns_log",$CONN);
						$uid=mysql_result($r,0,0)+1;
					}else{
						$type1=0; $r=cnstats_sql_query("select max(uid) from cns_log WHERE ip='$ip' AND proxy='$proxy'",$CONN);
						$uid=mysql_result($r,0,0);
					}
				}else{
					$r=cnstats_sql_query("select max(uid) from cns_log",$CONN);
					$uid=mysql_result($r,0,0)+1;
				}
			}
			@setcookie("counter",$uid,mktime(23,59,59,date("m"),date("d"),date("Y")),"/",$STATS_CONF["cookie_host"]);
		}
	}else{
		if ($flag==1){
			$r=cnstats_sql_query("select max(uid) from cns_log",$CONN);
			$uid=mysql_result($r,0,0)+1;
		}else{
			$r=cnstats_sql_query("select max(uid) from cns_log WHERE ip='$ip' AND proxy='$proxy'",$CONN);
			$uid=mysql_result($r,0,0);
		}
		$type1=$flag;
	}
	// Get country for unique hosts
	if ($flag==1) {
		if (islocal(long2ip($ip))) $nip=long2ip($proxy); else $nip=long2ip($ip);
		if (is_file($CNSTATS_DR."stat/geoip/GeoIPCity.dat")) {
			include($CNSTATS_DR."stat/geoip/geoipcity.inc");
			$gi = geoip_open($CNSTATS_DR."stat/geoip/GeoIPCity.dat",GEOIP_STANDARD);
			$gir=geoip_record_by_addr($gi,$nip);
			$country=$gir->country_code;
			$city=mysql_escape_string($gir->city."|".intval($gir->latitude*10)."|".intval($gir->longitude*10));
				if ($country=="" && $city==""){
					$gi = geoip_open($CNSTATS_DR."stat/geoip/GeoIPCity.dat",GEOIP_STANDARD);
					$nip=long2ip($proxy);
					$gir=geoip_record_by_addr($gi,$nip);
					$country=$gir->country_code;
					$city=mysql_escape_string($gir->city."|".intval($gir->latitude*10)."|".intval($gir->longitude*10));
				}
		}elseif (is_file($CNSTATS_DR."stat/geoip/GeoIP.dat")) {
			include($CNSTATS_DR."stat/geoip/geoip.inc");
			$gi = geoip_open($CNSTATS_DR."stat/geoip/GeoIP.dat",GEOIP_STANDARD);
			$country=geoip_country_code_by_addr($gi,$nip);
			if ($country==""){
				$gi = geoip_open($CNSTATS_DR."stat/geoip/GeoIP.dat",GEOIP_STANDARD);
				$nip=long2ip($proxy);
				$country=geoip_country_code_by_addr($gi,$nip);
			}
			geoip_close($gi);
		}else{
			$country="";
			$city="";
		}
		if ($country!="") $country=(ord($country[0])<<8)+ord($country[1]); else $country=0;
	}
	cnstats_sql_query("INSERT DELAYED INTO cns_log (date,ip,type,proxy,page,agent,referer,language,type1,uid,res,depth,cookie,country,city) VALUES ('".$date."','".$ip."',".$flag.",'".$proxy."','".mysql_escape_string($page)."','".$agent."','".mysql_escape_string($referer)."','".$language."','".$type1."','".$uid."','".$res."','".$depth."','".$c."','".$country."','".$city."')",$CONN);
	$r=cnstats_sql_query("SELECT hits,hosts,t_hits,t_hosts,users,t_users FROM cns_counter",$CONN);
	if (mysql_num_rows($r)!=1) {
		cnstats_sql_query("INSERT INTO cns_counter SET hits='1', hosts='1', t_hits='1', t_hosts='1', users='1', t_users='1'",$CONN);
		$hits=1;$hosts=1;$t_hits=1;$t_hosts=1;
	}else{
		$hits=mysql_result($r,0,0)+1;
		$t_hits=mysql_result($r,0,2)+1;
		$hosts=mysql_result($r,0,1);
		$t_hosts=mysql_result($r,0,3);
		$users=mysql_result($r,0,4);
		$t_users=mysql_result($r,0,5);
		if ($flag==1) {
			$hosts++;$t_hosts++;
		}
		if ($type1==1) {
			$users++;$t_users++;
		}
		cnstats_sql_query("UPDATE cns_counter SET hits='$hits', hosts='$hosts', t_hits='$t_hits', t_hosts='$t_hosts', users='$users', t_users='$t_users';",$CONN);
	}
	$STATS_CONF["hits"]=$hits;
	$STATS_CONF["hosts"]=$hosts;
	$STATS_CONF["t_hits"]=$t_hits;
	if (!$noclose) mysql_close($CONN);
}
stats_hit($STATS_CONF["sqlhost"], $STATS_CONF["sqluser"], $STATS_CONF["sqlpassword"], $STATS_CONF["dbname"]);
?>