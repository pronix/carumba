<?php

print $TABLE; ?>

<tr class=tbl1><td align=center><b>PHP include</b></td></tr>
<tr class=tbl2><td>
&lt;?<br>
<?php
if ($_SERVER["DOCUMENT_ROOT"][strlen($_SERVER["DOCUMENT_ROOT"])-1]!="/") $_SERVER["DOCUMENT_ROOT"].="/";

print "include &quot;".$_SERVER["DOCUMENT_ROOT"]."cnstats/cnt.php&quot;;<br>\n";
?>
?&gt;
</td></tr>
</table>
<br>
<?php
print $TABLE; ?>

<tr class=tbl1><td align=center><b>SSI include</b></td></tr>
<tr class=tbl2><td>
&lt;!--#include virtual="/stat/cnt.php" --&gt;<br>
</td></tr>
</table>
<br>
<?php 
	print $TABLE;
	if (!empty($_SERVER["HTTP_HOST"])) $_SERVER["HTTP_HOST"]="http://".$_SERVER["HTTP_HOST"];
?>
<tr class=tbl1><td align=center><b>GIF 1x1</b></td></tr>
<tr class=tbl2><td>
&lt;script language="javascript"&gt;<br>
cnsd=document;cnsd.cookie="b=b";cnsc=cnsd.cookie?1:0;<br>
document.write('&lt;img src="<?=$_SERVER["HTTP_HOST"];?>/stat/cntg.php?c='+cnsc+'&r='+escape(cnsd.referrer)+'&p='+escape(cnsd.location)+'" width="1" height="1" border="0"&gt;');<br>
&lt;/script&gt;&lt;noscript&gt;&lt;img src="<?=$_SERVER["HTTP_HOST"];?>/stat/cntg.php?468&c=0" width="1" height="1" border="0"&gt;&lt;/noscript&gt;<br>
</td></tr>
</table>
<?php 

$NOFILTER=1;
?>
