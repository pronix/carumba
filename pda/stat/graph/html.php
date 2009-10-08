<?
$TMPDATA=$DATA;
$DATA=$HTTP_SESSION_VARS["DATA"];

if ($s=="on") $need_s=true; else $need_s=false;

// Количество элементов
$count=count($DATA[0]);
if (count($DATA[1])>$count) $count=count($DATA[1]);
if (count($DATA[2])>$count) $count=count($DATA[2]);

if ($count==0) $count=1;

$dcount=3;
$max=Array();

// Сглаживаем графики ##########################################################
if ($need_s) {
	for ($i=2;$i<$count-2;$i++) {
		for ($j=0;$j<$dcount;$j++) {
			$DATA[$j][$i]=($DATA[$j][$i-1]+$DATA[$j][$i-2]+$DATA[$j][$i]+$DATA[$j][$i+1]+$DATA[$j][$i+2])/5;
			}
		}
	}

// Вычисляем максимум, и строи горизонтальные полосы ###########################
for ($i=0;$i<$count;$i++)
	for ($j=0;$j<$dcount;$j++)
		if ($max[$j]<$DATA[$j][$i]) $max[$j]=$DATA[$j][$i];

$maximum=0;
for ($j=0;$j<$dcount;$j++) if ($maximum<$max[$j]) $maximum=$max[$j];
$k=$h/($maximum+10);
if ($count<=1) $count=2;
$wk=$w/($count-1);

$step=500000;
if ($maximum<5000000) $step=500000;
if ($maximum<1000000) $step=100000;
if ($maximum<100000) $step=10000;
if ($maximum<50000) $step=5000;
if ($maximum<10000) $step=1000;
if ($maximum<5000) $step=500;
if ($maximum<1000) $step=100;
if ($maximum<500) $step=50;
if ($maximum<100) $step=10;
?> 
<table width='<?=$TW;?>' border=0 cellspacing=1 cellpadding=3 bgcolor='#D4F3D7'><tr><td class='tbl1'>
<table width='100%' style='height:258px' border=0 cellspacing=0 cellpadding=0><tr class='tbl1'>
<?

if ($maximum==0) {
	print "<td align=center><br>No Data<br><br></td>";
	}
else {
?>
<td>
<table cellspacing="0" cellpadding="5" border="0" style="height:258px">
<tr><td valign="top" align="right"><?=cNumber($maximum);?></td></tr>
<tr><td align="right"><?=cNumber(intval($maximum/2));?></td></tr>
<tr><td valign="bottom" align="right">0</td></tr>
</table>
</td>
<?
	$w=3;
	if ($type==0) $w=5;
	else if ($maximum>99999) $start=1; else $start=0;
	for	($i=$start;$i<$count;$i++) {
		print "<td valign=\"bottom\" width=\"".intval(100/$count)."%\">";

		if ($dcount>0) print "<img src='img/color1.gif' width='".$w."' height='".(intval($DATA[0][$i]*250/$maximum)+1)."' title='".intval($DATA[0][$i])."'>";
		if ($dcount>1) print "<img src='img/color3.gif' width='".$w."' height='".(intval($DATA[1][$i]*250/$maximum)+1)."' title='".intval($DATA[1][$i])."'>";
		if ($dcount>2) print "<img src='img/color2.gif' width='".$w."' height='".(intval($DATA[2][$i]*250/$maximum)+1)."' title='".intval($DATA[2][$i])."'>";
		print "</td>\n";
		}
	}
?>
</tr></table>
</td></tr></table>
<?
$DATA=$TMPDATA;

?>
