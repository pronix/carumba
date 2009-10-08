<?php
$filter=$_GET["filter"];

$DATELINK="&amp;filter=".urlencode($filter);

function islocal($ip) {
	if ($ip=="255.255.255.255") return(true);
	if (substr($ip,0,7)=="192.168") return(true);
	if (substr($ip,0,2)=="10") return(true);
	return(false);
	}

function mygethostbyaddr($ip) {
	GLOBAL $LANG;
	if (substr($ip,0,7)=="192.168") return($LANG["local ip"]."<br>192.168.0.0 - 192.168.255.255");
	if (substr($ip,0,2)=="10") return($LANG["local ip"]."<br>10.0.0.0 - 10.255.255.255");

	return(gethostbyaddr($ip));
	}

function ShowCityAndCountry($ip,$txt="") {
	GLOBAL $LANG,$COUNTRY,$isgeo;

	if (is_file("geoip/GeoIPCity.dat")) {
		if ($isgeo!=1) {include("geoip/geoipcity.inc");$isgeo=1;}
		$gi = geoip_open("geoip/GeoIPCity.dat",GEOIP_STANDARD);
		$gir=geoip_record_by_addr($gi,$ip);
		$country=$gir->country_code;
		$city=$gir->city;
		}
	elseif (is_file("geoip/GeoIP.dat")) {
		if ($isgeo!=1) {include("geoip/geoip.inc");;$isgeo=1;}
		$gi = geoip_open("geoip/GeoIP.dat",GEOIP_STANDARD);
		$country=geoip_country_code_by_addr($gi,$ip);
		geoip_close($gi);
		}
	else {
		$country="";
		$city="";
		}

	if (!empty($country)) {
		$tld=$country;
		if (isset($COUNTRY[$country])) $country=$COUNTRY[$country];
		print "<tr class=\"tbl2\"><td>".$LANG["country"].$txt."</td><td><img src=img/countries/".strtolower($tld).".gif width=18 height=12 border=0 align=absmiddle hspace=4>".$country."</td></tr>\n";
		}
	if (!empty($city)) print "<tr class=\"tbl2\"><td>".$LANG["city"].$txt."</td><td>".$city."</td></tr>\n";
	}

$rid=$_GET["rid"];
if (!empty($rid)) {

	$r=mysql_query("select ip,proxy from cns_log WHERE id='".$rid."'");
	if ($a=mysql_fetch_array($r)) {
		$ip=long2ip($a[0]);
		$proxy=$a[1]==-1?"":long2ip($a[1]);
		if (empty($proxy)) {$proxy=$LANG["not used"];} else $proxy=$proxy." (".mygethostbyaddr($proxy).")";

		print $TABLE;
		if ($ip=="255.255.255.255")	print "<tr class=\"tbl2\"><td>".$LANG["ip"]."</td><td>".$LANG["unknownip"]."</td></tr>\n";
		else {
			print "<tr class=\"tbl2\"><td>".$LANG["ip"]."</td><td>".$ip."</td></tr>\n";
			print "<tr class=\"tbl2\"><td>".$LANG["title"]."</td><td>".mygethostbyaddr($ip)."</td></tr>\n";
			}
		print "<tr class=\"tbl2\"><td>".$LANG["proxy"]."</td><td>".$proxy."</td></tr>\n";

		$proxy=long2ip($a[1]);
		$country=0;
		// Get country for ip
		if (!islocal($ip)) ShowCityAndCountry($ip);
		if (!islocal($proxy)) ShowCityAndCountry($proxy," (".$LANG["proxy"].")");

		if ($ip!="255.255.255.255")	print "<tr class=\"tbl2\"><td colspan=2><a href=\"index.php?st=log&amp;stm=".$stm."&amp;ftm=".$ftm."&amp;sel_ip=2&amp;inp_ip=".urlencode($ip)."&amp;filter=".urlencode($filter)."\"><img src=\"img/log.gif\" width=\"12\" height=\"12\" border=\"0\" title=\"".$LANG["log"]."\" align=\"absmiddle\"> ".$LANG["viewlogforip"]." ".$ip."</a></td></tr>\n";
		                                                                                                                                                             
		print "</table><br>";
		$a=array();
		exec("whois -h whois.ripe.net ".(islocal($ip)?$proxy:$ip),$a);

		if (count($a)!=0) {
			print $TABLE."<tr><td class=\"tbl1\" style=\"font-family:courier new;font-size:12px;\">";
			for ($i=2;$i<count($a);$i++) {
				$do=1;
				if (substr($a[$i],0,4)=="chan")	$do=0;
				if (substr($a[$i],0,1)=="%")	$do=0;
				if ($do==1) print $a[$i]."<br>\n";
				}
			print "</td></tr></table>\n";
			}
		}
	else {
		print $TABLE."<tr><td>";
		print "No info.";
		print "</td></tr></table>\n";
		}

	}
else {
	print $TABLE."<tr><td>";
	print "No info.";
	print "</td></tr></table>\n";
	}
?>
