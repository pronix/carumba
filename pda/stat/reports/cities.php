<?
$inpage=40;

$domains=intval($_GET["domains"]);
$shorturl=intval($_GET["shorturl"]);
$filter=$_GET["filter"];

$tld=intval($_GET["tld"]);
$DATELINK="&amp;tld=".$tld."&amp;filter=".urlencode($filter);
$sqlflt=GenerateFilter($filter);

if ($tld!=0) {
	$countrysql=" AND country='".$tld."'";

	$rtld=chr($tld>>8).chr($tld&0xFF);
	if (isset($COUNTRY[$rtld])) $title=$COUNTRY[$rtld]; else $title=$rtld;
		if ($_GET["zoom"]!=1) {
			print "<h1><img src=img/countries/".strtolower($rtld).".gif width=18 height=12 border=0 align=absmiddle hspace=4>".$title."</a> <span style='font-size:11px;font-weight:normal;'>(<a href=\"index.php?st=cities&amp;stm=".$stm."&amp;ftm=".$ftm."&amp;tld=".$code."&amp;filter=".urlencode($filter)."\">".$LANG["all_countries"]."</a>)</span></h1>";
		}
	}

else $countrysql="";

$sql="select city,country,count(*) as cnt from cns_log WHERE type=1 ".$countrysql." AND date>'".$startdate."' AND date<'".$enddate."' ".$sqlflt." group by city,country order by cnt desc";
$r=cnstats_sql_query($sql);

$num=$start;
$DATA=Array();
$i=0;

while ($a=mysql_fetch_assoc($r)) {
	$cnt=$a["cnt"];
	$ar=explode("|",$a["city"]);
	$data=$ar[0];
	$DATA[$ar[1]."|".$ar[2]]=$cnt;
	if ($i>=$start && $i<$start+$inpage) {
		if ($data=="") $data="<font color=#C0C0C0>".$LANG["other countries"]."</font>";
		else $data="<a href=\"index.php?st=log&amp;stm=".$stm."&amp;ftm=".$ftm."&amp;sel_city=1&amp;hosts=yes&amp;inp_city=".urlencode($data)."&amp;filter=".urlencode($filter)."\"><img title=\"".$LANG["log"]."\" src=\"img/log.gif\" width=\"12\" height=\"12\" border=\"0\" align=\"absmiddle\"></a> ".$data;
                            
		$rtld="";$code=$a["country"];
		if ($a["country"]=="0") $a["country"]=$LANG["other countries"];
		else {
			$rtld=chr($a["country"]>>8).chr($a["country"]&0xFF);
			if (isset($COUNTRY[$rtld])) $a["country"]=$COUNTRY[$rtld];
			else $a["country"]=$rtld;
	
			$a["country"]="<img src=img/countries/".strtolower($rtld).".gif width=18 height=12 border=0 align=absmiddle hspace=4><a href=\"index.php?st=cities&amp;stm=".$stm."&amp;ftm=".$ftm."&amp;tld=".$code."&amp;filter=".urlencode($filter)."\">".$a["country"]."</a>";
			}
	
		if (!empty($data)) {
			if ($tld==0) $TABLED[]="<td></a>".$data."</td><td>".$a["country"]."</td>";
			else $TABLED[]="<td>".$data."</td>";
			$TABLEC[]=$cnt;
			}

		}
	$i++;
	$num++;
	}
$count=$i;

$_SESSION["DATA"]=$DATA;

if ($start==0 && gdVersion()!=0) {

                if ($_GET["zoom"]==1) {
?>
<HTML>
<HEAD>
<TITLE><?=$LANG["softname"];?></TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=<?=$LANG["charset"];?>">
<STYLE TYPE="text/css">
<!--
td {font-family:tahoma;font-size: 11px; color: #333}
a,a:visited {font-family:tahoma;font-size:11px;text-decoration:none;color:blue;}
a:hover {text-decoration:underline}
//-->
</STYLE>
</HEAD>
<BODY style='margin:0;'>
<img src="graph/cities.php?zoom=1&r=<?=urlencode(microtime());?>" width='770' height='577' border='0'><br>
<div align=right><img src="img/none.gif" width="1" height="3"></div>
<div align=center>
<table cellspacing="1" cellpadding="0" border="0">
<tr>
<?
if ($tld!=0) {
	print "<td align=right><span style='font-size:11px;font-weight:bold;'><img src=img/countries/".strtolower($rtld).".gif width=18 height=12 border=0 align=absmiddle hspace=4>".$title." </span></td><td width=50>&nbsp;</td>";
}
?>
<td width=40><?=$LANG["sessionss"];?>:&nbsp;&nbsp;</td>
<td width=8><img src="img/c6.gif" width="11" height="11" border="0"></td><td width='80'>&nbsp;> 1 000</td>
<td width=8><img src="img/c5.gif" width="9" height="11" border="0"></td><td width='70'>&nbsp;> 100</td>
<td width=8><img src="img/c4.gif" width="9" height="11" border="0"></td><td width='60'>&nbsp;> 10</td>
<td width=8><img src="img/c3.gif" width="9" height="11" border="0"></td><td width='50'>&nbsp;> 5</td>
<td width=8><img src="img/c2.gif" width="9" height="11" border="0"></td><td width='40'>> 0</td>
</tr>
</table>
</div>
</BODY>
</HTML>
<?php
                exit;
                }
?>

<SCRIPT language="JavaScript" type="text/javascript">
<!--
function wopen(name,url,w,h) {
        if (h>screen.height-100) h=screen.height-100;
        if (w>screen.width-10) w=screen.width-10;

        var x=(screen.width-(w+25))/2;
        var y=(screen.height-(h+25))/2;

        wnd=window.open(url,name,'width='+w+',height='+h+',scrollbars=n,resizable=no,top='+y+',left='+x+',status=no,toolbar=no,menubar=no');
        }
//-->
</SCRIPT>

<div align="center">
<table cellspacing="0" cellpadding="0" border="0"><tr><td style="background-image:url('img/cityworld.gif');">
<img src="graph/cities.php?r=<?=urlencode(microtime());?>" width="465" height="348" border="0"><br>
</td></tr></table></div>
<img src="img/none.gif" width="1" height="3"><div align=right>
<table cellspacing="1" cellpadding="0" border="0">
<tr>
<td width=40><?=$LANG["sessionss"];?>:&nbsp;&nbsp;</td>
<td width=8><img src="img/c5.gif" width="9" height="11" border="0"></td><td width='80'>&nbsp;> 1 000</td>
<td width=8><img src="img/c4.gif" width="9" height="11" border="0"></td><td width='70'>&nbsp;> 100</td>
<td width=8><img src="img/c3.gif" width="9" height="11" border="0"></td><td width='60'>&nbsp;> 10</td>
<td width=8><img src="img/c2.gif" width="9" height="11" border="0"></td><td width='50'>&nbsp;> 5</td>
<td width=8><img src="img/c1.gif" width="9" height="11" border="0"></td><td width='40'>> 0</td>
<td class="tbl1" bgcolor="#D4F3D7"><img src="img/zoom.gif" width="18" height="17" hspace="2" vspace="2" border="0"></td>
<?
if ($tld!=0) $zcode=$code;
?>
<td class="tbl1" bgcolor="#D4F3D7">&nbsp;<a href="javascript:wopen('map','index.php?nowrap=1&amp;zoom=1&amp;st=<?=$st;?>&amp;stm=<?=$stm;?>&amp;ftm=<?=$ftm;?>&amp;tld=<?=$zcode;?>&amp;filter=<?=urlencode(urlencode($filter));?>',770,600);"><?=$LANG["zoomin"];?></a>&nbsp;</td>
</tr>
<tr><td colspan=13><img src="img/none.gif" width="1" height="3"></td></tr>
<tr><td colspan=13 bgcolor="#b8e1bd"><img src="img/none.gif" width="1" height="1"></td></tr>
<tr><td colspan=13><img src="img/none.gif" width="1" height="3"></td></tr></table>
</div>
<?
	}

LeftRight($start,$inpage,$num,$count,0,5);
ShowTable($start,0);
LeftRight($start,$inpage,$num,$count,5,5);
?>