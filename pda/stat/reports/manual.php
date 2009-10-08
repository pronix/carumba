<?php
if ($CONFIG["language"]=="english") $postfix="";
else $postfix="_".$CONFIG["language"];


$d=file("manual/index".$postfix.".htm");
$d=implode($d,"");
$p1=strpos($d,"<BODY>");
$p2=strpos($d,"</BODY>");
$d=substr($d,$p1,$p2-$p1);

$d=str_replace("../img/","img/",$d);
$d=str_replace("<P>","<P align=\"justify\">",$d);
$d=str_replace("<UL>","<UL style=\"margin:20px;\">",$d);
$d=str_replace("<hr","<div align='right'><A href='javascript:history.back();'>".$LANG["back"]."</a></div><HR",$d);

print "<table cellspacing=10><tr><td>".$d."</td></tr></table>";
$NOFILTER=1;
?>