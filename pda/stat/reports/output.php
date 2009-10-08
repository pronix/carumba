<?
$inpage=40;

$filter=$_GET["filter"];
$out_exclude=intval($_GET["out_exclude"]);

$DATELINK="out_exclude=".$out_exclude.".&amp;filter=".urlencode($filter);

$IP=Array();
$URLS=Array();
$CNTS=Array();

if ($out_exclude==1) {
	$ADMENU.="<a href='index.php?st=output&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("out_exclude",$DATELINK)."'>".$LANG["output all"]."</a><br>";
	$ADMENU.=$LANG["output exclude random"];
	}
else {
	$ADMENU.=$LANG["output all"]."<br>";
	$ADMENU.="<a href='index.php?st=output&amp;stm=".$stm."&amp;ftm=".$ftm.RemoveVar("out_exclude",$DATELINK)."&amp;out_exclude=1'>".$LANG["output exclude random"]."</a>";
	}

$sqlflt=GenerateFilter($filter);
if ($out_exclude==0) {
	$r=cnstats_sql_query("SELECT ip,proxy,page FROM cns_log WHERE date>'".$startdate."' AND date<'".$enddate."' ".$sqlflt." ORDER BY id DESC");
	while ($b=mysql_fetch_array($r,MYSQL_ASSOC)) {
		$key=$b["ip"]."-".$b["proxy"];

		if (!isset($IP[$key])) {
			$url=urldecode($b["page"]);
			$crc=crc32($url);
			$URLS[$crc]=$url;
			if (isset($CNTS[$crc])) $CNTS[$crc]++; else $CNTS[$crc]=1;

			$IP[$key]=1;
			}
		}
	}
else {
	$r=cnstats_sql_query("SELECT ip,proxy,page FROM cns_log WHERE date>'".$startdate."' AND date<'".$enddate."' ".$sqlflt." ORDER BY ip,id DESC");
	while ($b=mysql_fetch_array($r,MYSQL_ASSOC)) {
		$key=$b["ip"]."-".$b["proxy"];

		if ($IP[$key]!=-1)
		if ($IP[$key]>=$out_exclude) {
			$url=urldecode($b["page"]);
			$crc=crc32($url);
			$URLS[$crc]=$url;
			if (isset($CNTS[$crc])) $CNTS[$crc]++; else $CNTS[$crc]=1;

			$IP[$key]=-1;
			}
		else {
			if (!isset($IP[$key])) $IP[$key]=1;
            else $IP[$key]++;
			}
		}
	}


arsort($CNTS);

$count=count($CNTS);
if ($start+$inpage>$count) $finish=$count; else $finish=$start+$inpage;
$num=0;

while (list ($key, $val) = each ($CNTS)) {

	if ($num>=$finish) break;
	if ($num>=$start) {
		$url=$URLS[$key];
		$TABLED[]="<A href='".$url."' target=_blank>".$url."</a>";
		$TABLEU[]="";
		$TABLEC[]=$val;
		}
	$num++;
    }

LeftRight($start,$inpage,$num,$count,0);
ShowTable($start);
LeftRight($start,$inpage,$num,$count);
?>
