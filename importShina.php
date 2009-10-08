<?php
set_time_limit(60);
require_once('_pm/_kernel.config.php');

// Установить обработчик ошибок
error_reporting(E_ALL & ~E_NOTICE);
set_error_handler('userError', E_ALL & ~E_NOTICE);

//trigger_error('', E_ERROR);

$pms_sID = 2002; // Категория летних шин
$prodName = 'GoodYear'; // Название производителя
$accCatID = 86; // Какая-то виртуальная величина. ССылается на Летние шины
// на зимние ссылается $accCatID = 85
$file_name = strtolower($prodName).'.csv';


print 'Загрузка файла<br>';
if (file_exists($file_name)) {
    $goods = file($file_name);
    print 'Файл загружен. Количество строк '.count($goods).'<br>';
} else {
    die('Файл не найден');
}

mysql_connect( $CFG["mysql.host"], $CFG["mysql.username"], $CFG["mysql.password"] ) or die('Плохой коннект');
mysql_select_db($CFG["mysql.dbname"]) or die('База не выбрана');
mysql_query("SET NAMES 'cp1251'");
mysql_query("SET CHARACTER SET cp1251");
print 'Соединение с базой<br>';

// Выбрать из базы производителя или добавить, если не найден
$query = 'SELECT accPlantID, accPlantName, logotype FROM pm_as_producer WHERE accPlantName LIKE "%'.$prodName.'%" LIMIT 1';
$res = mysql_query($query) or die(mysql_error());

if (mysql_num_rows( $res )) {
    list( $accPlantID, $accPlantName, $logotype ) = mysql_fetch_row($res);
    print 'Производитель есть<br>';
} else {
    mysql_query('INSERT INTO pm_as_producer (accPlantID, accPlantName, logotype) VALUES ("", "'.$prodName.'", "")');
    $accPlantID = mysql_insert_id();
    $accPlantName = $prodName;
    print 'Производитель добавлен<br>';
}



ob_start();
$j = 0;
print '<table border=1>';
foreach ($goods as $i => $v) {
    list($title, $producer, $type1, $type2, $type0, $priceb, $prices) = explode(';', $v);
    if (strlen(trim($title))>10) {
        
        // Добавить товар
        mysql_query('INSERT INTO pm_structure ( sID, userID, pms_sID, tplID, CreateDate, URLName, Title, ShortTitle, MetaDesc, MetaKeywords, Content, ModuleName, DataType, OrderNumber, OrderField, SortType, LinkCSSClass, CacheLifetime, ReviseType, CanBeProcessed, CanBeHelpered, IsVersionOfParent, isDeleted, isHidden) 
            VALUES ("", 203, '.$pms_sID.', 5, NOW(), 0, "'.$title.'", "'.$title.'", "","","","Catalogue", 
            "CatItem", 1, "OrderNumber", 0, "", "00:00:00", 0,0,0,0,0,0 )') or die(mysql_error());
        //print 'insert in sturcture<br>';
        $sID = mysql_insert_id();
        mysql_query('UPDATE pm_structure SET URLName = '.$sID.' WHERE sID = '.$sID.' LIMIT 1') or die(mysql_error());
        //print 'update sturcture<br>';
        
        
        // Дополнительные опции для товара
        $query = 'INSERT INTO pm_as_parts ( accID, accCatID, sID, accPlantID, deliveryID, deliveryCode, measure,
            basePrice, salePrice, smallPicture, stdPicture, bigPicture, tplID, ptID, notAvailable )
            VALUES ("", '.$accCatID.', '.$sID.','.$accPlantID.', 0, "", "", '.$priceb.', '.$prices.', "","","",2,1,0 )';
        //print $query;
        mysql_query($query) or die(mysql_error());
        $accID = mysql_insert_id();
        //print 'insert in parts<br>';
        
        // Удалить старые значения
        mysql_query('DELETE FROM pm_as_parts_properties WHERE accID='.$accID);
        
        // Добавить новые значения типов
        mysql_query('INSERT INTO pm_as_parts_properties
            (propID, accID, propListID, propValue) VALUES ("", '.$accID.', 39, "'.$type0.'")') or die(mysql_error()); // Тип
        mysql_query('INSERT INTO pm_as_parts_properties
            (propID, accID, propListID, propValue) VALUES ("", '.$accID.', 41, "'.$type2.'")') or die(mysql_error()); // Размер
        mysql_query('INSERT INTO pm_as_parts_properties
            (propID, accID, propListID, propValue) VALUES ("", '.$accID.', 42, "'.$type1.'")') or die(mysql_error()); // Диаметр
        
        $j++;
        print '<tr>';
        print '<td>'.$sID.'</td><td>'.$accID.'</td><td>'.$title.'</td><td>'.$producer.'</td><td>'.$type1.'</td><td>'.$type2.
            '</td><td>'.$type0.'</td><td>'.$prices.'</td><td>'.$priceb.'</td>';
        print '</tr>';
    }
}
print '</table>';
print 'Всего:'.$j;
ob_end_flush();



/**************************************************************
 *                      Функции
 */

    function get_microtime()
    {
        list($usec, $sec) = explode(' ', microtime());
        return (float)((float)$usec + (float)$sec);
    }

    function userError($errno, $errstr, $errfile, $errline) {
        print '<div style="padding:5px; margin:5px; border:1px solid black; background:white;">';
        print '<b>ERROR</b> '.$errstr.'<br />';
        print $errfile.':'.$errline.'</div>';
    }

?>