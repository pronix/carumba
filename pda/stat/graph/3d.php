<?
session_start();
session_register("DATA");
$DATA=$HTTP_SESSION_VARS["DATA"];

include "../_funct.php";

function imagebar($im,$x,$y,$w,$h,$dx,$dy,$c1,$c2,$c3) {

	imagefilledpolygon($im,
		Array(
			$x, $y-$h,
			$x+$w, $y-$h,
			$x+$w+$dx, $y-$h-$dy,
			$x+$dx, $y-$dy-$h
		), 4, $c1);

	imagefilledpolygon($im,
		Array(
			$x+$w, $y-$h,
			$x+$w, $y,
			$x+$w+$dx, $y-$dy,
			$x+$w+$dx, $y-$dy-$h
		), 4, $c3);

	imagefilledrectangle($im, $x, $y-$h, $x+$w, $y, $c2);
	}

$GDVERSION=gdVersion();
if ($_GET["antialias"]==0) $GDVERSION=1;

// Разрешение
$W=$IMGW;
$H=$IMGH;

// Псевдоглубина
$DX=30;
$DY=20;

// Отступы
$MB=20; // bottom
$ML=8; // left
$M=5; // остальные

// Ширина одного символа
$LW=imagefontwidth(2);

// Если версия GD больше чем 2.0, то все в два раза больше (для сглаживания)
if ($GDVERSION>=2) {
	$W*=2;$H*=2;
	$DX*=2;$DY*=2;
	$LW*=2;$MB*=2;$M*=2;$ML*=2;
	}

// Количество элементов
$count=count($DATA[0]);
if (count($DATA[1])>$count) $count=count($DATA[1]);
if (count($DATA[2])>$count) $count=count($DATA[2]);

if ($count==0) $count=1;

// Сглаживаем графики ##########################################################
if ($_GET["s"]==1) {
	for ($i=2;$i<$count-2;$i++) {
		for ($j=0;$j<$count;$j++) {
			$DATA[$j][$i]=($DATA[$j][$i-1]+$DATA[$j][$i-2]+$DATA[$j][$i]+$DATA[$j][$i+1]+$DATA[$j][$i+2])/5;
			}
		}
	}

// Максимальное значение
$max=0;
for ($i=0;$i<$count;$i++) {
	$max=$max<$DATA[0][$i]?$DATA[0][$i]:$max;
	$max=$max<$DATA[1][$i]?$DATA[1][$i]:$max;
	$max=$max<$DATA[2][$i]?$DATA[2][$i]:$max;
	}

include "shared.php";

$county=$ncounty;
$max=$nmax;

// Подравняем левую границу
$text_width=strlen(cNumber($max))*$LW;
$ML+=$text_width;

// Вывод фона графика
imageline($im, $ML, $M+$DY, $ML, $H-$MB, $c);
imageline($im, $ML, $M+$DY, $ML+$DX, $M, $c);
imageline($im, $ML, $H-$MB, $ML+$DX, $H-$MB-$DY, $c);
imageline($im, $ML, $H-$MB, $W-$M-$DX, $H-$MB, $c);
imageline($im, $W-$M-$DX, $H-$MB, $W-$M, $H-$MB-$DY, $c);

imagefilledrectangle($im, $ML+$DX, $M, $W-$M, $H-$MB-$DY, $bg[1]);
imagerectangle($im, $ML+$DX, $M, $W-$M, $H-$MB-$DY, $c);

imagefill($im, $ML+1, $H/2, $bg[2]);

// Вывод неизменяемой сетки
for ($i=1;$i<3;$i++) {
	imageline($im, $ML+$i*intval($DX/3), $M+$DY-$i*intval($DY/3), $ML+$i*intval($DX/3), $H-$MB-$i*intval($DY/3), $c);
	imageline($im, $ML+$i*intval($DX/3), $H-$MB-$i*intval($DY/3), $W-$M-$DX+$i*intval($DX/3), $H-$MB-$i*intval($DY/3), $c);
	}

// Реальные размеры графика
$RW=$W-$ML-$M-$DX;
$RH=$H-$MB-$M-$DY;

// Координаты нуля
$X0=$ML+$DX;
$Y0=$H-$MB-$DY;

// Вывод изменяемой сетки
for ($i=0;$i<$count;$i++) {
	imageline($im,$X0+$i*($RW/$count),$Y0,$X0+$i*($RW/$count)-$DX,$Y0+$DY,$c);
	imageline($im,$X0+$i*($RW/$count),$Y0,$X0+$i*($RW/$count),$Y0-$RH,$c);
	}

$step=$RH/$county;
for ($i=0;$i<=$county;$i++) {
	imageline($im,$X0,$Y0-$step*$i,$X0+$RW,$Y0-$step*$i,$c);
	imageline($im,$X0,$Y0-$step*$i,$X0-$DX,$Y0-$step*$i+$DY,$c);
	imageline($im,$X0-$DX,$Y0-$step*$i+$DY,$X0-$DX-($ML-$text_width)/4,$Y0-$step*$i+$DY,$text);
	}

// Вывод баров
for ($i=0;$i<$count;$i++) 
	imagebar($im, $X0+$i*($RW/$count)+4-1*intval($DX/3), $Y0+1*intval($DY/3), intval($RW/$count)-4, $RH/$max*$DATA[0][$i], intval($DX/3)-5, intval($DY/3)-3, $bar[0][0], $bar[0][1], $bar[0][2]);

for ($i=0;$i<$count;$i++) 
	imagebar($im, $X0+$i*($RW/$count)+4-2*intval($DX/3), $Y0+2*intval($DY/3), intval($RW/$count)-4, $RH/$max*$DATA[1][$i], intval($DX/3)-5, intval($DY/3)-3, $bar[1][0], $bar[1][1], $bar[1][2]);

for ($i=0;$i<$count;$i++) 
	imagebar($im, $X0+$i*($RW/$count)+4-3*intval($DX/3), $Y0+3*intval($DY/3), intval($RW/$count)-4, $RH/$max*$DATA[2][$i], intval($DX/3)-5, intval($DY/3)-3, $bar[2][0], $bar[2][1], $bar[2][2]);

// Уменьшение и пересчет коррдинат
$ML-=$text_width;
if ($GDVERSION>=2) {                                                                                        
	$im1=imagecreatetruecolor($W/2,$H/2);
	imagecopyresampled($im1,$im,0,0,0,0,$W/2,$H/2,$W,$H);                                                   
	imagedestroy($im);
	$im=$im1;                                                                                               

	$W/=2;$H/=2;
	$DX/=2;$DY/=2;
	$LW/=2;$MB/=2;$M/=2;$ML/=2;
	$X0/=2;$Y0/=2;$step/=2;
	$RW/=2;$RH/=2;
	}

$text=imagecolorallocate($im,136,197,145);

// Вывод подписей по оси Y
for ($i=1;$i<=$county;$i++) {
	$str=cNumber(($max/$county)*$i);
	imagestring($im,2, $X0-$DX-strlen($str)*$LW-$ML/4-2,$Y0+$DY-$step*$i-imagefontheight(2)/2,$str,$text);
	}

// Вывод подписей по оси X
$prev=100000;
$twidth=$LW*strlen($DATA["x"][0])+6;
$i=$X0+$RW-$DX;

while ($i>$X0-$DX) {
	if ($prev-$twidth>$i) {
		$drawx=$i+1-($RW/$count)/2;
		if ($drawx>$X0-$DX) {
			$str=$DATA["x"][intval(($i-$X0+$DX)/($RW/$count))-1];
			imageline($im,$drawx,$Y0+$DY,$i+1-($RW/$count)/2,$Y0+$DY+5,$text);
			imagestring($im,2, $drawx+1-(strlen($str)*$LW)/2 ,$Y0+$DY+7,$str,$text);
			}
		$prev=$i;
		}
	$i-=$RW/$count;
	}

header("Content-Type: image/png");
imagepng($im);
imagedestroy($im);
?>