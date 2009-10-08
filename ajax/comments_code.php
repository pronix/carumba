<?php
header('Cache-control: No-Cache');
header('Pragma: No-Cache');

require_once('../task/config.inc.php');
mysql_connect($dbs, $dbu, $dbp);
mysql_select_db($dbn);

$width = 40;
$height = 17;
if (isset($_GET['num']) && is_numeric($_GET['num'])) {
    $num = $_GET['num'];
} else {
    $num = 0;
}

if ($num) {
    $oResult = mysql_query("SELECT code FROM pm_comments_codes WHERE id='$num' LIMIT 1");
    if ($oResult && mysql_num_rows($oResult)) {
        list($code) = mysql_fetch_array($oResult);
    } else {
        $code = 'Error';
    }
} else {
    $code = 'Error';
}

$img = imagecreatetruecolor( $width, $height );
$background = imagecolorallocate( $img, 255, 255, 255 );
$color = imagecolorallocate( $img, 255, 0 ,0);
imagefilledrectangle($img, 0, 0, $width, $height, $background );
imagettftext( $img, 10, 0, 2, 14, $color, 'arial.ttf', $code);
header('Content-type: image/png');
imagepng($img);
?>