<?
session_start();
session_register("DATA");
$DATA=$_SESSION["DATA"];

function circle($im,$x,$y,$r,$color) {
	for ($i=1;$i<=$r;$i++) {
		imagearc($im,$x,$y,$i,$i,0,360,$color);
		}
	}

$W=465;
$H=348;

function cpixel($im,$x,$y,$r,$color) {
	GLOBAL $zx,$zy;
	if ($x==0 && $y==0) return;

	$x=(223+$x*1.297)*$zx;
	$y=(181-$y*2.007)*$zy;

	if ($r==1) imagesetpixel($im,$x,$y,$color);
	else circle($im,$x,$y,$r,$color);
	}

if ($_GET["zoom"]==1) {
	$im = imagecreatefrompng("../img/cityworldz.png");
	$zx=770/465;
	$zy=577/348;
	$zw=1;
}else{
	$im=imagecreate($W,$H);
	$zx=1;
	$zy=1;
	$zw=0;
}
$white=imagecolorallocate($im,255,255,255);
$red=imagecolorallocate($im,255,0,0);
$blue=imagecolorallocate($im,0,0,255);
imagecolortransparent($im,$white);

while (list ($key, $val) = each ($DATA)) {
	list($cy,$cx)=explode("|",$key);
	$w=1+$zw;
	if ($val>5) $w=3+$zw;
	if ($val>10) $w=5+$zw;
	if ($val>100) $w=7+$zw;
	if ($val>1000) $w=9+$zw;
	if ($val>10000) $w=11+$zw;

	cpixel($im,$cx/10,$cy/10,$w,$red);
	}


header("Content-Type: image/png");
imagepng($im);
imagedestroy($im);
?>
