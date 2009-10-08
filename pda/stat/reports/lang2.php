<?
$inpage=40;
$tld=intval($_GET["tld"]);
$filter=$_GET["filter"];

$DATELINK="&amp;filter=".urlencode($filter);

$sqlflt=GenerateFilter($filter);
$r=cnstats_sql_query("select count(*),cns_languages.eng from cns_log,cns_languages WHERE type=1 AND country='".$tld."' AND date>'".$startdate."' AND date<'".$enddate."' AND LEFT(language,2)=cns_languages.code ".$sqlflt." group by cns_languages.eng order by 1 desc");

$count=mysql_num_rows($r);
if ($start+$inpage>$count) $finish=$count; else $finish=$start+$inpage;
for ($i=$start;$i<$finish;$i++) {
	$eng=mysql_result($r,$i,1);
	$cnt=mysql_result($r,$i,0);
	$TABLED[]=$eng;
	$TABLEC[]=$cnt;
	}

LeftRight($start,$inpage,$num,$count,0);
ShowTable($start);
LeftRight($start,$inpage,$num,$count);
?>