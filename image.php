<?php

if($_GET['qID']){$qID = $_GET['qID'];} else {$qID = $_POST['qID'];}

$dblink = mysql_connect("localhost","webcarumba","6Fasj6FQ7d");
mysql_select_db("carumba", $dblink);
mysql_query("SET NAMES 'cp1251'");
mysql_query("SET CHARACTER SET cp1251");


$voteResults = getVoteResults($qID);

mysql_close($dblink);

// Зададим значение и подписи
$VALUES=Array();
$LEGEND=Array();

$sum = 0;
foreach($voteResults[0]['answers'] as $key=>$value) {
	$sum += $value;
}
foreach($voteResults[0]['answers'] as $key=>$value) {
	$VALUES[] = $value;
	//$LEGEND[] = $key." - ".round($value/$sum*100)."% ";
}
//$VALUES=Array(100,200,300,400,500,400,300);
//$LEGEND=Array("John","Bob","Alex","Mike","Andrew","Greg");

// Создадим изображения
header("Content-Type: image/png");
$im=ImageCreate(195,119);

// Зададим цвет фона. Немного желтоватый, для того, чтобы было
// видно границы изображения на белом фоне.
$bgcolor=ImageColorAllocate($im,255,255,255);

// Зададим цвета элементов
$COLORS[0] = imagecolorallocate($im, 209, 21, 61); 
$COLORS[1] = imagecolorallocate($im, 209, 21, 171); 
$COLORS[2] = imagecolorallocate($im, 109, 21, 209); 
$COLORS[3] = imagecolorallocate($im, 21, 92, 209); 
$COLORS[4] = imagecolorallocate($im, 21, 185, 209); 
$COLORS[5] = imagecolorallocate($im, 21, 209, 136); 
$COLORS[6] = imagecolorallocate($im, 0, 153, 102); 
$COLORS[7] = imagecolorallocate($im, 209, 207, 21); 
$COLORS[8] = imagecolorallocate($im, 209, 118, 21); 
$COLORS[9] = imagecolorallocate($im, 209, 74, 21); 
$COLORS[10] = imagecolorallocate($im, 137, 91, 74); 
$COLORS[11] = imagecolorallocate($im, 82, 56, 47);


// Зададим цвета теней элементов
$SHADOWS[0] = imagecolorallocate($im, 146, 15, 43); 
$SHADOWS[1] = imagecolorallocate($im, 146, 15, 119); 
$SHADOWS[2] = imagecolorallocate($im, 76, 15, 146); 
$SHADOWS[3] = imagecolorallocate($im, 15, 64, 146); 
$SHADOWS[4] = imagecolorallocate($im, 15, 129, 146); 
$SHADOWS[5] = imagecolorallocate($im, 15, 146, 95); 
$SHADOWS[6] = imagecolorallocate($im, 0, 107, 71); 
$SHADOWS[7] = imagecolorallocate($im, 146, 144, 15); 
$SHADOWS[8] = imagecolorallocate($im, 146, 82, 15); 
$SHADOWS[9] = imagecolorallocate($im, 146, 52, 15); 
$SHADOWS[10] = imagecolorallocate($im, 87, 41, 24); 
$SHADOWS[11] = imagecolorallocate($im, 32, 6, 0);


// Вызов функции рисования диаграммы
Diagramm($im,$VALUES,$LEGEND);

// Генерация изображения
$image = ImagePNG($im);


// $im - идентификатор изображения
// $VALUES - массив со значениями
// $LEGEND - массив с подписями

function getVoteResults($qID)
{
	$voteResults = Array();
	
	$query = "SELECT qID, sID, Question, Ans1, Ans2, Ans3, 
				Ans4, Ans5, Ans6, Ans7, Ans8, Ans9, Ans10 
				FROM pm_vote 
				WHERE 
					isActive = 1 && qID = '".$qID."'
			";
	$result = mysql_query($query);
	if(mysql_num_rows($result)) {
		$j = 0;
		while($vote = mysql_fetch_assoc($result)) {
			for($i = 1; $i < 11; $i++) {
				if($vote["Ans$i"]) {
					$qAns = "SELECT  qID FROM pm_vote_results WHERE qID = '".$vote['qID']."' && aID = '".$i."'";
					$rAns = mysql_query($qAns);
					$voteResults[$j]['answers'][$vote["Ans$i"]] = mysql_num_rows($rAns);
				}
			}
			$voteResults[$j]['question'] = $vote['Question'];
			$voteResults[$j]['qID'] = $vote['qID'];
			$j++;
		}
	}

	return $voteResults;
}

function Diagramm($im,$VALUES,$LEGEND) {
GLOBAL $COLORS,$SHADOWS;

$black=ImageColorAllocate($im,0,0,0);

// Получим размеры изображения
$W=ImageSX($im);                 
$H=ImageSY($im);



// Вывод круговой диаграммы ----------------------------------------

$total=array_sum($VALUES);
$anglesum=$angle=Array(0);
$i=1;

// Расчет углов
while ($i<count($VALUES)) {
	$part=$VALUES[$i-1]/$total;
	$angle[$i]=floor($part*360);
	$anglesum[$i]=array_sum($angle);
	$i++;
	}
$anglesum[]=$anglesum[0];

// Расчет диаметра
$diametr=$W;

// Расчет координат центра эллипса
$circle_x=($diametr/2);
$circle_y=$H/2-10;

// Поправка диаметра, если эллипс не помещается по высоте
//if ($diametr>($H*2)-10) $diametr=($H*2)-40;

// Вывод тени
for ($j=20;$j>0;$j--)
	for ($i=0;$i<count($anglesum)-1;$i++)
		ImageFilledArc($im,$circle_x,$circle_y+$j,
						   $diametr,$diametr/2-10,
						   $anglesum[$i],$anglesum[$i+1],
						   $SHADOWS[$i],IMG_ARC_PIE);

// Вывод круговой диаграммы
for ($i=0;$i<count($anglesum)-1;$i++)
	ImageFilledArc($im,$circle_x,$circle_y,
					   $diametr,$diametr/2-10,
					   $anglesum[$i],$anglesum[$i+1],
					   $COLORS[$i],IMG_ARC_PIE);
}


?>
