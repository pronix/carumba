<?php
error_reporting(0);
if($_GET['color']){$color = $_GET['color'];} else {$color = $_POST['color'];}
//echo $color.'<br>';
// Создадим изображения
header("Content-Type: image/png");
$smIm=ImageCreate(10,10);

// Зададим цвет фона. Немного желтоватый, для того, чтобы было
// видно границы изображения на белом фоне.
//$bgcolor=ImageColorAllocate($smIm,255,255,255);

// Зададим цвета элементов
$col[0] = imagecolorallocate($smIm, 209, 21, 61); 
$col[1] = imagecolorallocate($smIm, 209, 21, 171); 
$col[2] = imagecolorallocate($smIm, 109, 21, 209); 
$col[3] = imagecolorallocate($smIm, 21, 92, 209); 
$col[4] = imagecolorallocate($smIm, 21, 185, 209); 
$col[5] = imagecolorallocate($smIm, 21, 209, 136); 
$col[6] = imagecolorallocate($smIm, 0, 153, 102); 
$col[7] = imagecolorallocate($smIm, 209, 207, 21); 
$col[8] = imagecolorallocate($smIm, 209, 118, 21); 
$col[9] = imagecolorallocate($smIm, 209, 74, 21); 
$col[10] = imagecolorallocate($smIm, 137, 91, 74); 
$col[11] = imagecolorallocate($smIm, 82, 56, 47);

// Зададим цвета теней элементов
$shad[0] = imagecolorallocate($smIm, 146, 15, 43); 
$shad[1] = imagecolorallocate($smIm, 146, 15, 119); 
$shad[2] = imagecolorallocate($smIm, 76, 15, 146); 
$shad[3] = imagecolorallocate($smIm, 15, 64, 146); 
$shad[4] = imagecolorallocate($smIm, 15, 129, 146); 
$shad[5] = imagecolorallocate($smIm, 15, 146, 95); 
$shad[6] = imagecolorallocate($smIm, 0, 107, 71); 
$shad[7] = imagecolorallocate($smIm, 146, 144, 15); 
$shad[8] = imagecolorallocate($smIm, 146, 82, 15); 
$shad[9] = imagecolorallocate($smIm, 146, 52, 15); 
$shad[10] = imagecolorallocate($smIm, 87, 41, 24); 
$shad[11] = imagecolorallocate($smIm, 32, 6, 0);



// Зададим цвет фона. Немного желтоватый, для того, чтобы было
// видно границы изображения на белом фоне.
$black=ImageColorAllocate($smIm,0,0,0);

ImageFilledRectangle($smIm, 0,0,10,10, $col[$color]);
//ImageRectangle($smIm, 0,0,10,10, $black);

// Генерация изображения
$image = ImagePNG($smIm);

?>