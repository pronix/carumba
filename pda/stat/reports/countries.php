<?php
$filter=$_GET["filter"];

$DATELINK="&amp;filter=".urlencode($filter);

$sqlflt=GenerateFilter($filter);
$r=cnstats_sql_query("SELECT country,count(*)
          FROM cns_log
          WHERE type=1 AND date>'".$startdate."' AND date<'".$enddate."' ".$sqlflt."
          GROUP BY country
          ORDER BY 2 desc;");

$count=mysql_num_rows($r);
if ($count!=0) {
        $i=0;
        while ($a=mysql_fetch_array($r,MYSQL_NUM)) {
                $tld="";$code=$a[0];
                if ($a[0]=="0") $a[0]=$LANG["other countries"];
                else {
                        $tld=chr($a[0]>>8).chr($a[0]&0xFF);
                        if (isset($COUNTRY[$tld])) $a[0]=$COUNTRY[$tld];
                        else $a[0]=$tld;

                        $a[0]="<img src=img/countries/".strtolower($tld).".gif width=18 height=12 border=0 align=absmiddle hspace=4><a target=\"_blank\" href=\"index.php?st=lang2&amp;stm=".$stm."&amp;ftm=".$ftm."&amp;tld=".$code."&amp;filter=".urlencode($filter)."\">".$a[0]."</a>";
                        }
                $MAP_TABLED[]=$tld;
                $MAP_TABLEC[]=$a[1];
                $TABLED[]=$a[0];
                $TABLEC[]=$a[1];
                $i++;
                }

        $sum=0;
        for ($i=0;$i<count($MAP_TABLEC);$i++) $sum+=$MAP_TABLEC[$i];
        $LIMIT=Array();
        $LIMIT[1]=1;
        $LIMIT[2]=intval($sum*0.01);
        $LIMIT[3]=intval($sum*0.02);
        $LIMIT[4]=intval($sum*0.1);
        $LIMIT[5]=intval($sum*0.5);

        $gstr="&amp;L1=".$LIMIT[1]."&amp;L2=".$LIMIT[2]."&amp;L3=".$LIMIT[3]."&amp;L4=".$LIMIT[4]."&amp;L5=".$LIMIT[5];
        while (list ($key,$val) = each ($MAP_TABLED)) {
                $gstr.="&amp;".$val."=";
                if ($MAP_TABLEC[$key]>$LIMIT[5]) $gstr.="5";
                elseif ($MAP_TABLEC[$key]>$LIMIT[4]) $gstr.="4";
                elseif ($MAP_TABLEC[$key]>$LIMIT[3]) $gstr.="3";
                elseif ($MAP_TABLEC[$key]>$LIMIT[2]) $gstr.="2";
                else $gstr.="1";
                }
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
<?php
print "<img src='graph/countries.php?&zoom=1".$gstr."' width='770' height='577' border='0'><br>";
?>
<div align=right><img src="img/none.gif" width="1" height="3"></div>
<div align=center>
<table cellspacing="1" cellpadding="0" border="0">
<tr>
<td width=8><img src="img/5.gif" width="9" height="11" border="0"></td><td width='100'>> 50% (<?=$LIMIT[5];?>)</td>
<td width=8><img src="img/4.gif" width="9" height="11" border="0"></td><td width='100'>> 10% (<?=$LIMIT[4];?>)</td>
<td width=8><img src="img/3.gif" width="9" height="11" border="0"></td><td width='90'>> 2% (<?=$LIMIT[3];?>)</td>
<td width=8><img src="img/2.gif" width="9" height="11" border="0"></td><td width='90'>> 1% (<?=$LIMIT[2];?>)</td>
<td width=8><img src="img/1.gif" width="9" height="11" border="0"></td><td width='30'>> 0</td>
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
<table cellspacing="0" cellpadding="0" border="0"><tr><td style="background-image:url('img/world.gif');">
<?php
print "<img src='graph/countries.php?&zoom=0".$gstr."' width='465' height='348' border='0'><br>";
?>
</td></tr></table>

<img src="img/none.gif" width="1" height="3"><br>
<div align=right>
<table cellspacing="1" cellpadding="0" border="0">
<tr>
<td width=8><img src="img/5.gif" width="9" height="11" border="0"></td><td width='100'>> 50% (<?=$LIMIT[5];?>)</td>
<td width=8><img src="img/4.gif" width="9" height="11" border="0"></td><td width='100'>> 10% (<?=$LIMIT[4];?>)</td>
<td width=8><img src="img/3.gif" width="9" height="11" border="0"></td><td width='90'>> 2% (<?=$LIMIT[3];?>)</td>
<td width=8><img src="img/2.gif" width="9" height="11" border="0"></td><td width='90'>> 1% (<?=$LIMIT[2];?>)</td>
<td width=8><img src="img/1.gif" width="9" height="11" border="0"></td><td width='30'>> 0</td>
<td class="tbl1" bgcolor="#D4F3D7"><img src="img/zoom.gif" width="18" height="17" hspace="2" vspace="2" border="0"></td>
<td class="tbl1" bgcolor="#D4F3D7">&nbsp;<a href="javascript:wopen('map','index.php?nowrap=1&amp;zoom=1&amp;st=<?=$st;?>&amp;stm=<?=$stm;?>&amp;ftm=<?=$ftm;?>&amp;filter=<?=$filter;?>',770,600);"><?=$LANG["zoomin"];?></a>&nbsp;</td>
</tr>
</table>
</div>
<img src="img/none.gif" width="1" height="3"><br>
<?php

        ShowTable(0);
        }
else {
        print $TABLE."<tr class=\"tbl1\"><td align=\"center\">\n";
        print $LANG["geoipempty"];
        print "</td></tr></table>\n";
        }
?>
</div>