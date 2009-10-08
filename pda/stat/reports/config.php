<?php
$action=$_GET["action"];
$filter=$_GET["filter"];

if ($action==1) {
	$lang=$_GET["lang"];
	$lang=str_replace(";","_",$lang);$lang=str_replace(",","_",$lang);$lang=str_replace(".","_",$lang);
	$lang=cnstats_mhtml($lang);

	$gauge=$_GET["gauge"]=="on"?1:0;
	$percents=$_GET["percents"]=="on"?1:0;
	$hints=$_GET["hints"]=="on"?1:0;
	$antialias=$_GET["antialias"]=="on"?1:0;
	$diagram=intval($_GET["diagram"]);
	$nv=$_GET["show_hits"]=="on"?1:0;
	$nu=$_GET["show_hosts"]=="on"?1:0;
	$nq=$_GET["show_users"]=="on"?1:0;
	$date_format=cnstats_mhtml($_GET["date_format"]);
	$shortdate_format=cnstats_mhtml($_GET["shortdate_format"]);
	$shortdm_format=cnstats_mhtml($_GET["shortdm_format"]);
	$datetime_format=cnstats_mhtml($_GET["datetime_format"]);
	$datetimes_format=cnstats_mhtml($_GET["datetimes_format"]);

	cnstats_sql_query("UPDATE cns_config SET diagram='".$diagram.
		"', antialias='".$antialias.
		"', language='".$lang.
		"', gauge='".$gauge.
		"', hints='".$hints.
		"', percents='".$percents.
		"', date_format='".$date_format.
		"', shortdate_format='".$shortdate_format.
		"', shortdm_format='".$shortdm_format.
		"', datetime_format='".$datetime_format.
		"', datetimes_format='".$datetimes_format.
		"', show_hits='".$nv.
		"', show_hosts='".$nu.
		"', show_users='".$nq.
		"';");
	header("Location: index.php?st=config&stm=".$stm."&ftm=".$ftm."&filter=".$filter);
	exit;
	}

function YesNo($name,$value,$disabled="",$def="") {
	if (!empty($disabled)) $value=$def;

	print "<SELECT name=\"".$name."\" ".$disabled.">\n";
	print "<OPTION value=\"on\"".($value==1?" selected":"").">Yes\n";
	print "<OPTION value=\"off\"".($value==0?" selected":"").">No\n";
	print "</SELECT>\n";
	}

$r=cnstats_sql_query("SELECT * FROM cns_config;");
$a=mysql_fetch_array($r);

if (empty($a["date_format"])) $a["date_format"]=$LANG["date_format"];
if (empty($a["shortdate_format"])) $a["shortdate_format"]=$LANG["shortdate_format"];
if (empty($a["shortdm_format"])) $a["shortdm_format"]=$LANG["shortdm_format"];
if (empty($a["datetime_format"])) $a["datetime_format"]=$LANG["datetime_format"];
if (empty($a["datetimes_format"])) $a["datetimes_format"]=$LANG["datetimes_format"];

if ($a["timeoffset"]==1) $a["timeoffset"]=date("Z")/3600;
?>
<form action='index.php' method='get'>
<?=$TABLE;?>
<tr class="tbl0"><td width="100%"></td><td width="170"></td></tr>
<tr class="tbl0"><td colspan="2" align="center"><b><?=$LANG["configmain"];?></b></td></tr>

<tr class="tbl2"><td width="100%"><?=$LANG["show diagrams"];?></td><td width="1%"><?=YesNo("gauge",$a["gauge"]);?></td></tr>
<tr class="tbl2"><td><?=$LANG["show percents"];?></td><td><?=YesNo("percents",$a["percents"]);?></td></tr>

</table><br><?=$TABLE;?>
<tr class="tbl0"><td width="100%"></td><td width="170"></td></tr>
<tr class="tbl0"><td colspan="2" align="center"><b><?=$LANG["configgraph"];?></b></td></tr>

<tr class="tbl2"><td><?=$LANG["default diagrams"];?></td><td>

<table>
<tr><td><input <?=(gdVersion()==0?"disabled":"");?> type="radio" name="diagram" value="1" <?=($a["diagram"]==1?"checked":"");?>></td><td><img src="img/graph_1_c.gif" vspace="2" width="130" height="75"></td></tr>
<tr><td><input <?=(gdVersion()==0?"disabled":"");?> type="radio" name="diagram" value="2" <?=($a["diagram"]==2?"checked":"");?>></td><td><img src="img/graph_2_c.gif" vspace="2" width="130" height="75"></td></tr>
<tr><td><input <?=(gdVersion()==0?"disabled":"");?> type="radio" name="diagram" value="3" <?=($a["diagram"]==3?"checked":"");?>></td><td><img src="img/graph_3_c.gif" vspace="2" width="130" height="75"></td></tr>
</table>

<tr class="tbl2"><td><?=$LANG["antialias"];?></td><td><?=YesNo("antialias",$a["antialias"],gdVersion()<2?"disabled":"","no");?></td></tr>

<tr class='tbl2'><td><?=$LANG["default_show"];?></td><td>
<input type=checkbox name=show_hits<?=($a["show_hits"]==1?" checked":"");?>> <?=$LANG["hits"];?><br>
<input type=checkbox name=show_users<?=($a["show_users"]==1?" checked":"");?>> <?=$LANG["visitors"];?><br>
<input type=checkbox name=show_hosts<?=($a["show_hosts"]==1?" checked":"");?>> <?=$LANG["hosts"];?><br>
</td></tr>

</table><br><?=$TABLE;?>
<tr class="tbl0"><td width="100%"></td><td width="170"></td></tr>
<tr class="tbl0"><td colspan="2" align="center"><b><?=$LANG["configdate"];?></b></td></tr>

<tr class="tbl2"><td><?=$LANG["text_date_format"];?></td><td><input type="text" name="date_format" value="<?=$a["date_format"];?>" style="width:160px"></td></tr>
<tr class="tbl2"><td><?=$LANG["text_shortdate_format"];?></td><td><input type="text" name="shortdate_format" value="<?=$a["shortdate_format"];?>" style="width:160px"></td></tr>
<tr class="tbl2"><td><?=$LANG["text_shortdm_format"];?></td><td><input type="text" name="shortdm_format" value="<?=$a["shortdm_format"];?>" style="width:160px"></td></tr>
<tr class="tbl2"><td><?=$LANG["text_datetime_format"];?></td><td><input type="text" name="datetime_format" value="<?=$a["datetime_format"];?>" style="width:160px"></td></tr>
<tr class="tbl2"><td><?=$LANG["text_datetimes_format"];?></td><td><input type="text" name="datetimes_format" value="<?=$a["datetimes_format"];?>" style="width:160px"></td></tr>
<tr class="tbl2"><td><?=$LANG["language"];?></td><td><SELECT name="lang" style="width:160px">
<?php
$lng=$a["language"];

$d=dir("lang/");
while ($entry=$d->read()) {
	if (substr($entry,0,4)=="lang") {
		$lang=substr($entry,5,-4);
		if ($lang!=$lng) print "<OPTION>".$lang."\n";
		else print "<OPTION SELECTED>".$lang."\n";
		}
	}
?>
</SELECT></td></tr>
<tr class="tbl1"><td colspan="2" align="center"><input type="submit" value="<?=$LANG["save"];?>"></td></tr>
</table>
<input type="hidden" name="action" value="1">
<input type="hidden" name="st" value="config">
<input type="hidden" name="nowrap" value="1">
<input type="hidden" name="hints" value="off">
<?php
print "<input type='hidden' name='stm' value='".$stm."'>\n";
print "<input type='hidden' name='ftm' value='".$ftm."'>\n";
print "<input type='hidden' name='filter' value='".urlencode($filter)."'>\n";
?>
</form>

<?php
$NOFILTER=1;
?>