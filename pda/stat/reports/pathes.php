<?php
$inpage=40;

$timelimit=intval($STATS_CONF["slow_reports_time_limit"]);
if ($timelimit==0) $timelimit=600;
set_time_limit($timelimit);

if (isset($STATS_CONF["slow_reports_memory_limit"])) {
	@ini_set("memory_limit",$STATS_CONF["slow_reports_memory_limit"]);
	}

$memlimit=ini_get("memory_limit");
if (strtoupper(substr($memlimit,-1)=="M")) $memlimit=substr($memlimit,0,-1)*1024*1024;
if (strtoupper(substr($memlimit,-1)=="K")) $memlimit=substr($memlimit,0,-1)*1024;

$filter=$_GET["filter"];
$DATELINK="&amp;filter=".urlencode($filter);

$page=Array();
$pathes=Array();
$pathes_count=Array();

$quer=GenerateFilter($filter);
$r=cnstats_sql_query("select id,ip,proxy,page from cns_log WHERE date>'".$startdate."' AND date<'".$enddate."' ".$quer." order by ip,proxy,id");

$path=Array();$i=0;
$previp=0;$prevproxy=0;
$processed=0;
$error=0;
while ($a=mysql_fetch_assoc($r)) {
	if (function_exists(memory_get_usage)) {
		if ($memlimit-memory_get_usage()<1024*1024) {
			print "Осталось слишком мало памяти (лимит: $memlimit, занято: ".memory_get_usage().")";
			print "Обработано записей: ".$processed."; Всего: ".mysql_num_rows($r);
			$error=1;
			break;
			}
		}
	if ($prevproxy!=$a["proxy"] || $previp!=$a["ip"]) {

		if ($i!=0) {

			$path["hash"]=array_sum($path);
			$path["id"]=$oldid;

			$cnt=$i+2;
			$found=false;
			for ($i=1;$i<=count($pathes);$i++) {
				if ($pathes[$i]["hash"]==$path["hash"]) {
					if (count($pathes[$i])==$cnt) {
						for ($j=0;$j<$cnt;$j++) {                
							if ($pathes[$i][$j]!=$path[$j]) break;
							$pathes_count[$i]++;
							$found=true;
							}
						}
					}
				}
			if (!$found) {
				$idx=count($pathes)+1;
				$pathes[$idx]=$path;
				$pathes_count[$idx]=1;
				}
			}
		$path=Array();
		$i=0;
		$prevproxy=$a["proxy"];
		$previp=$a["ip"];
		}
	$hash=crc32($a["page"]);
	$path[$i]=$hash;
	$oldid=$a["id"];
	$page[$hash]=urldecode($a["page"]);
	$i++;
	unset($a);
	$processed++;
	}
mysql_free_result($r);


if ($error==0) {

	$D=$C=Array();
	$count=0;
	for ($i=0;$i<count($pathes);$i++) {
		$d="";
		for ($j=0;$j<count($pathes[$i])-2;$j++) $d.="<a href='".$page[$pathes[$i][$j]]."' target='_blank'>".$page[$pathes[$i][$j]]."</a><br>";
		if ($pathes_count[$i]==1) {
			$d=substr($d,0,-4);
			$d.="<br><br><img src='img/log.gif' width=\"12\" height=\"12\" border=\"0\" title=\"".$LANG["pathes_userinfo"]."\" align=\"absmiddle\"><a href=\"index.php?rid=".$pathes[$i]["id"]."&amp;st=ipinfo&amp;stm=".$stm."&amp;ftm=".$ftm."&amp;filter=".urlencode($filter)."\" target='_blank'> ".$LANG["pathes_userinfo"]."</a><br>";
		}
		unset($pathes[$i]);

		$D[]=substr($d,0,-4);
		unset($d);
		$C[]=$pathes_count[$i];
		unset($pathes_count[$i]);
		$count++;
		}
	unset($pathes_count);
	unset($pathes);
	unset($pages);

	array_multisort($C,SORT_DESC, $D);

	if ($start+$inpage>$count) $finish=$count; else $finish=$start+$inpage;
	for ($i=$start;$i<$finish;$i++) {
		$TABLED[]=$D[$i];
		unset($D[$i]);
		$TABLEC[]=$C[$i];
		unset($C[$i]);
		}

	unset($D);
	unset($C);

	LeftRight($start,$inpage,$num,$count,0);
	ShowTable($start);
	LeftRight($start,$inpage,$num,$count);
	}
?>
