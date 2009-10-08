<?
	// Скрипту известна переменная-массив с именем $item
	// Формат массива:
	// $item=array (
	//	'url'		=> УРЛ страницы (без домена)
	//	'title'		=> Заголовок страницы
	//	'lastupdate'	=> Время последней индексации
	// );
?>

<div class="result">
 <a href="<?=STARTURL ?><?=$w3s_item['url'] ?>" target="_blank"><?=$w3s_item['title'] ?></a><br />
 <span class="date">Дата индексации: <?=w3s_mdate($w3s_item['lastupdate']) ?></span>
</div>