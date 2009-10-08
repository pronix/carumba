<?php

$by=intval($_GET["by"]);
$filter=$_GET["filter"];

$qs=RemoveVar("stm",str_replace("&","&amp;",$_SERVER["QUERY_STRING"]));
$qs=RemoveVar("ftm",$qs);
$qs=RemoveVar("st",$qs);
$qs=RemoveVar("start",$qs);

$DATELINK="&amp;".RemoveVar("start",RemoveVar("sd",RemoveVar("fd",$qs)));

function CustomSelect($name,$add="") {
	GLOBAL $_GET,$LANG;

	print "<SELECT style=\"width:100%\" OnChange=\"javascript:eSelect(this.value,'inp_".$name."')\" name=\"sel_".$name."\" id=\"sel_".$name."\"".$add.">\n";
	print "<OPTION value=\"0\" ".($_GET["sel_".$name]=="0"?"SELECTED":"").">".$LANG["log_any"]."\n";
	print "<OPTION value=\"1\" ".($_GET["sel_".$name]=="1"?"SELECTED":"").">".$LANG["log_like"]."\n";
	print "<OPTION value=\"2\" ".($_GET["sel_".$name]=="2"?"SELECTED":"").">".$LANG["log_notlike"]."\n";
	print "</SELECT>\n";
	}

function CustomInput($name,$add="") {
	GLOBAL $_GET,$LANG;

	if ($_GET["sel_".$name]==0) $ds="disabled "; else $ds="";
	print "<input ".$ds."type=\"text\" style=\"width:100%\" id=\"inp_".$name."\" name=\"inp_".$name."\" value=\"".cnstats_mhtml($_GET["inp_".$name])."\"".$add.">";
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
function eSelect(t,e) {
	var el=document.getElementById(e);
	if (el) {
		if (t==0) el.disabled=true; else el.disabled=false;
		}
	}

function EnaDis(elid,is) {
	var s=document.getElementById(elid);
	if (s) s.disabled=is;
	
	}

function eCountry(t) {
	EnaDis("sel_country",!t);
	if (t) {
		var s=document.getElementById("sel_country");
		EnaDis('inp_country',(s.value==0)?true:false);
		}
	else EnaDis('inp_country',true);

	EnaDis("sel_city",!t);
	if (t) {
		var s=document.getElementById("sel_city");
		EnaDis('inp_city',(s.value==0)?true:false);
		}
	else EnaDis('inp_city',true);
	}

function ptable_exl() {
	var t=document.getElementById("ptable");
	var i=document.getElementById("pimg");
	if (t.className=="vis1") {
		t.className="vis2";
		document.cookie="cnstats_report_log=hidden";
		i.src="img/expand.gif";
		}
	else {
		t.className="vis1";
		document.cookie="cnstats_report_log=visible";
		i.src="img/colapse.gif";
		}
	}

//-->
</SCRIPT>
<?php
print $TABLE;
$expanded=$HTTP_COOKIE_VARS["cnstats_report_log"]=="visible"?true:false;
?>
<tr class="tbl0"><td><a href="JavaScript:ptable_exl();"><img id='pimg' src='img/<?=!$expanded?"expand":"colapse";?>.gif' width=17 height=17 border=0></a></td><td width='95%'><?=$LANG["log_additional"];?>
</td></tr></table>

<table width='<?=$TW;?>' id='ptable' cellspacing='1' border='0' cellpadding='3' bgcolor='#D4F3D7' style='table-layout:fixed;' class="<?=($expanded?"vis1":"vis2");?>">
<form action="index.php" method="get">
<tr class="tbl1"><td width="30%"><?=$LANG["log_page"];?></td><td width="20%"><?=CustomSelect("page");?></td><td width="50%"><?=CustomInput("page");?></td></tr>
<tr class="tbl2"><td nowrap><?=$LANG["log_referer"];?></td><td><?=CustomSelect("referer");?></td><td><?=CustomInput("referer");?></td></tr>
<tr class="tbl1"><td><?=$LANG["log_language"];?></td><td><?=CustomSelect("language");?></td><td><?=CustomInput("language");?></td></tr>
<tr class="tbl2"><td><?=$LANG["log_useragent"];?></td><td><?=CustomSelect("agent");?></td><td><?=CustomInput("agent");?></td></tr>
<tr class="tbl1"><td><?=$LANG["log_ip"];?></td><td>

<SELECT name="sel_ip" style="width:100%" OnClick="EnaDis('inp_ip',this.value!=2?true:false);">
<OPTION <?=$_GET["sel_ip"]=="0"?"SELECTED":"";?> value="0"><?=$LANG["log_any"];?>
<OPTION <?=$_GET["sel_ip"]=="1"?"SELECTED":"";?> value="1"><?=$LANG["log_hidden"];?>
<OPTION <?=$_GET["sel_ip"]=="2"?"SELECTED":"";?> value="2"><?=$LANG["log_calculate"];?>
</SELECT>
                            
</td><td><?=CustomInput("ip",$_GET["sel_ip"]==2?"":"disabled");?></td></tr>
<tr class="tbl2"><td><?=$LANG["log_proxy"];?></td><td>

<SELECT name="sel_proxy" style="width:100%" OnClick="EnaDis('inp_proxy',this.value!=3?true:false);">
<OPTION <?=$_GET["sel_proxy"]=="0"?"SELECTED":"";?> title="<?=$LANG["log_proxy1"];?>" value="0"><?=$LANG["log_any"];?>
<OPTION <?=$_GET["sel_proxy"]=="1"?"SELECTED":"";?> title="<?=$LANG["log_proxy2"];?>" value="1"><?=$LANG["log_without_proxy"];?>
<OPTION <?=$_GET["sel_proxy"]=="2"?"SELECTED":"";?> title="<?=$LANG["log_proxy3"];?>" value="2"><?=$LANG["log_any_proxy"];?>
<OPTION <?=$_GET["sel_proxy"]=="3"?"SELECTED":"";?> title="<?=$LANG["log_proxy4"];?>" value="3"><?=$LANG["log_with_proxy"];?>
</SELECT>

</td><td><?=CustomInput("proxy",$_GET["sel_proxy"]==3?"":"disabled");?></td></tr>


<tr class="tbl1"><td><?=$LANG["log_country"];?></td><td>

<SELECT OnClick="EnaDis('inp_country',this.value==0?true:false);" name="sel_country" style="width:100%" id="sel_country" <?=$_GET["hosts"]=="yes"?"":"disabled";?>>
<OPTION value="0" <?=$_GET["sel_country"]=="0"?"SELECTED":"";?>><?=$LANG["log_any"];?>
<OPTION value="1" <?=$_GET["sel_country"]=="1"?"SELECTED":"";?>><?=$LANG["log_calculate"];?>
</SELECT>

</td><td>

<SELECT name="inp_country" id="inp_country" style="width:100%" <?=($_GET["hosts"]=="yes"&&$_GET["sel_country"]!=0)?"":"disabled";?>>
<?php
while (list ($key, $val) = each ($COUNTRY)) {
	$code=ord($key[0])*256+ord($key[1]);
	print "<OPTION value=\"".$code."\" ".($_GET["inp_country"]==$code?"selected":"").">".$key." / ".$val."\n";
    }
?>
</SELECT>
</td></tr>                                                                                     

<tr class="tbl1"><td width="30%"><?=$LANG["city"];?></td><td width="20%"><?=CustomSelect("city",$_GET["hosts"]=="yes"?"":"disabled");?></td><td width="50%"><?=CustomInput("city");?></td></tr>

<tr class="tbl2"><td colspan="3">
<table><tr><td><input <?=($_GET["hosts"]=="yes"?"checked":"");?> onClick="eCountry(this.checked)" type="checkbox" name="hosts" value="yes"></td><td width="50%"><?=$LANG["log_hosts"];?></td>
<td><input <?=($_GET["users"]=="yes"?"checked":"");?> type="checkbox" name="users" value="yes"></td><td width="50%"><?=$LANG["log_users"];?></td></tr></table>
</td></tr>
<tr class="tbl1"><td colspan="3" align="center">
<input type="hidden" name="st" value="log">
<input type="hidden" name="by" value="<?=$by;?>">
<input type="hidden" name="ftm" value="<?=intval($ftm);?>">
<input type="hidden" name="stm" value="<?=intval($stm);?>">
<input type="hidden" name="filter" value="<?=$filter;?>">
<input type="submit" value="<?=$LANG["log_show"];?>">
</td></tr>
</form>
</table>
<?php
$inpage=40;

if ($by==1) {
	$ADMENU.="<a href=\"index.php?st=log&amp;stm=".$stm."&amp;ftm=".$ftm."&amp;by=0&amp;".RemoveVar("by",$qs)."\">".$LANG["fullreport"]."</a><br>";
	$ADMENU.=$LANG["simplereport"];
	$addfields="";
	}
else {
	$ADMENU.=$LANG["fullreport"]."<br>";
	$ADMENU.="<a href=\"index.php?st=log&amp;stm=".$stm."&amp;ftm=".$ftm."&amp;by=1&amp;".RemoveVar("by",$qs)."\">".$LANG["simplereport"]."</a>";
	$addfields=",proxy,agent,language,country,city";
	}

$where="WHERE 1=1";
if ($_GET["hosts"]=="yes") $where.=" AND type=1";
if ($_GET["users"]=="yes") $where.=" AND type1=1";

if ($_GET["sel_country"]==1) $where.=" AND country='".intval($_GET["inp_country"])."'";

if ($_GET["sel_proxy"]==1) $where.=" AND proxy=-1";
if ($_GET["sel_proxy"]==2) $where.=" AND proxy!=-1";
if ($_GET["sel_proxy"]==3) $where.=" AND proxy='".ip2long($_GET["inp_proxy"])."'";

if ($_GET["sel_ip"]==1) $where.=" AND ip=-1";
if ($_GET["sel_ip"]==2) $where.=" AND ip='".ip2long($_GET["inp_ip"])."'";

if ($_GET["sel_agent"]==1) $where.=" AND agent like '%".cnstats_mhtml($_GET["inp_agent"])."%'";
if ($_GET["sel_agent"]==2) $where.=" AND agent not like '%".cnstats_mhtml($_GET["inp_agent"])."%'";
	
if ($_GET["sel_referer"]==1) $where.=" AND referer like '%".cnstats_mhtml($_GET["inp_referer"])."%'";
if ($_GET["sel_referer"]==2) $where.=" AND referer not like '%".cnstats_mhtml($_GET["inp_referer"])."%'";

if ($_GET["sel_page"]==1) $where.=" AND page like '%".str_replace("%","\%",urlencode(cnstats_mhtml($_GET["inp_page"])))."%'";
if ($_GET["sel_page"]==2) $where.=" AND page not like '%".str_replace("%","\%",urlencode(cnstats_mhtml($_GET["inp_page"])))."%'";

if ($_GET["sel_city"]==1) $where.=" AND city like '%".str_replace("+"," ",str_replace("%","\%",urlencode(cnstats_mhtml($_GET["inp_city"]))))."%'";
if ($_GET["sel_city"]==2) $where.=" AND city not like '%".str_replace("+"," ",str_replace("%","\%",urlencode(cnstats_mhtml($_GET["inp_city"]))))."%'";

$sqlflt=GenerateFilter($filter);
$sql="select date,ip,page,referer,id,type".$addfields." from cns_log ".$where." AND date>'".$startdate."' AND date<'".$enddate."' ".$sqlflt." order by 1 desc LIMIT 2000";

$r=cnstats_sql_query($sql);

$count=mysql_num_rows($r);
if ($start+$inpage>$count) $finish=$count; else $finish=$start+$inpage;
$num=$start;

LeftRight($start,$inpage,$num,$count,10,5,"&amp;".RemoveVar("by",$qs));


if ($by==1) {
	print $TABLE;
	print "<tr class='tbl1'><td width=110 valign='top' align='center'><B>".$LANG["date"]."<br>".$LANG["ip"]."</B></td>";
	print "<td align='center'><B>URL<br>".$LANG["refering page"]."</td>";
	print "</tr>\n";

	for ($i=$start;$i<$finish;$i++) {
		$date=date($CONFIG["datetime_format"],strtotime(mysql_result($r,$i,0)));
		$ip=long2ip(mysql_result($r,$i,1));
		$page=urldecode(mysql_result($r,$i,2));
		$from=urldecode(mysql_result($r,$i,3));
		$rid=mysql_result($r,$i,4);
		$type=mysql_result($r,$i,5);
		$num++;
		$page=urldecode($page);
		$page_dec=phrase_uncode($page);
		$from_dec=phrase_uncode(urldecode($from));

		if (strlen($page_dec)>55) $printpage=substr($page_dec,0,55)."..."; else $printpage=$page_dec;
		if (strlen($from_dec)>55) $printfrom=substr($from_dec,0,55)."..."; else $printfrom=$from_dec;

		if ($type==1) print "<tr class=\"tbl1\">"; else print "<tr class=\"tbl2\">";
		print "<td valign=top>".$date."<br>\n";
		print "<a href=\"index.php?rid=".$rid."&amp;st=ipinfo\">".$ip."</a></td>\n";
		print "<td valign=\"top\"><a href='".$page."' target='_blank'>".$printpage."</a>\n<br>";
		print                    "<a href='".$from."' target='_blank'>".$printfrom."</a>\n</td>\n";
		}
	print "</table>\n";
	}
else {
	for ($i=$start;$i<$finish;$i++) {
		$date=date($CONFIG["datetime_format"],strtotime(mysql_result($r,$i,0)));
	    $ip=long2ip(mysql_result($r,$i,1));
	    $page=mysql_result($r,$i,2);
	    $from=mysql_result($r,$i,3);
	    $rid=mysql_result($r,$i,4);
	    if ($class!="tbl1") $class="tbl1"; else $class="tbl2";
	    $num++;
		$page=urldecode($page);
		$page_dec=phrase_uncode($page);
		$from_dec=phrase_uncode(urldecode($from));

		$proxy=mysql_result($r,$i,6)==-1?$LANG["noproxy"]:long2ip(mysql_result($r,$i,6));

	    if (strlen($page_dec)>70) $printdata1=substr($page_dec,0,70)."..."; else $printdata1=$page_dec;
	    if (strlen($from_dec)>70) $printdata2=substr($from_dec,0,70)."..."; else $printdata2=$from_dec;

		if ($ip=="255.255.255.255") $ip=$LANG["unknownip"];
		else $ip="<a href=\"index.php?rid=".$rid."&amp;st=ipinfo&amp;stm=".$stm."&amp;ftm=".$ftm."\">".$ip."</a>";

		$pfiltua=str_replace("%FLT",urlencode(mysql_result($r,$i,7)),$filtua);
		$pfiltip=str_replace("%FLT",mysql_result($r,$i,1),$filtip);

		
		$language=GetLanguage(substr(mysql_result($r,$i,8),0,2));
		if (empty($language)) $language=mysql_result($r,$i,8);
		else $language.=" (".mysql_result($r,$i,8).")";

		print $TABLE;
	    print "<tr class=\"tbl2\"><td width=\"100\">".$LANG["date"]."</td><td>".$date."</td></tr>\n";
	    print "<tr class=\"tbl1\"><td>".$LANG["url"]."</td><td><a href='".$page."' target='_blank'>".$printdata1."</a></td></tr>\n";
	    print "<tr class=\"tbl1\"><td>".$LANG["referer"]."</td><td><a href='".$from."' target='_blank'>".$printdata2."</a></td></tr>\n";
	    print "<tr class=\"tbl2\"><td>".$LANG["ip"]."</td><td>".$pfiltip.$ip."</td></tr>\n";
	    print "<tr class=\"tbl2\"><td>".$LANG["proxy"]."</td><td>".$proxy."</td></tr>\n";
	    print "<tr class=\"tbl2\"><td>User-Agent</td><td>".$pfiltua.mysql_result($r,$i,7)."</td></tr>\n";
	    print "<tr class=\"tbl2\"><td>".$LANG["language"]."</td><td>".$language."</td></tr>\n";

		
		$country=mysql_result($r,$i,9);
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

		$city=mysql_result($r,$i,10);
		if (!empty($city)) {
			$city=substr($city,0,strpos($city,"|"));
		    print "<tr class=\"tbl2\"><td>".$LANG["city"]."</td><td>".$city."</td></tr>\n";
			}

	    print "</tr>\n";
		print "</table>\n<br>\n";
	    }
	}
?>
